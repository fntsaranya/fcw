<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Config;
use FCW\Core\View;
use FCW\Repositories\AppointmentPaymentRepository;
use FCW\Repositories\AppointmentRepository;
use FCW\Services\RazorpayService;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class AppointmentController
{
    public function __construct(
        private readonly AppointmentRepository $appointments = new AppointmentRepository(),
        private readonly AppointmentPaymentRepository $payments = new AppointmentPaymentRepository(),
        private readonly RazorpayService $razorpay = new RazorpayService()
    ) {
    }

    /**
     * @param array<string, string> $oldInput
     */
    public function form(array $oldInput = [], ?string $errorMessage = null): void
    {
        View::render('pages/appointment', [
            'activePage' => 'contact',
            'paymentConfig' => Config::appointmentPayment(),
            'oldInput' => $oldInput,
            'errorMessage' => $errorMessage,
            'bookedSlots' => $this->appointments->getBookedSlots(),
        ]);
    }

    public function create(): void
    {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $concern = trim((string) ($_POST['concern'] ?? ''));
        $preferredDate = trim((string) ($_POST['preferred_date'] ?? ''));
        $preferredTime = trim((string) ($_POST['preferred_time'] ?? ''));

        $oldInput = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'concern' => $concern,
            'preferred_date' => $preferredDate,
            'preferred_time' => $preferredTime,
        ];

        if ($fullName === '' || $email === '' || $phone === '' || $concern === '' || $preferredDate === '') {
            $this->form($oldInput, 'Please fill all required fields to continue with appointment booking.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->form($oldInput, 'Please enter a valid email address.');
            return;
        }

        $normalizedDate = $this->validatePreferredDate($preferredDate);
        if ($normalizedDate === null) {
            $this->form($oldInput, 'Please choose a valid preferred date (today or future).');
            return;
        }

        $paymentConfig = Config::appointmentPayment();
        $amountInr = trim((string) ($paymentConfig['fee_inr'] ?? '0'));
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $amountInr)) {
            $this->form($oldInput, 'Booking amount configuration is invalid. Please contact support.');
            return;
        }

        $currency = trim((string) ($paymentConfig['currency'] ?? 'INR'));

        try {
            $created = $this->appointments->create(
                $fullName,
                $email,
                $phone,
                $concern,
                $normalizedDate,
                $preferredTime === '' ? null : $preferredTime,
                $amountInr,
                $currency,
                $this->clientIp(),
                (string) ($_SERVER['HTTP_USER_AGENT'] ?? '')
            );
        } catch (Throwable $exception) {
            error_log('Appointment create error: ' . $exception->getMessage());
            $this->form($oldInput, $this->createErrorMessage($exception));
            return;
        }

        $redirectUrl = sprintf(
            '/appointment/payment?ref=%s&token=%s',
            rawurlencode((string) $created['booking_reference']),
            rawurlencode((string) $created['ack_token'])
        );

        View::redirect($redirectUrl);
    }

    public function paymentPage(): void
    {
        $reference = trim((string) ($_GET['ref'] ?? ''));
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($reference === '' || $token === '') {
            View::render('pages/not_found', [
                'activePage' => 'contact',
                'title' => 'Booking not found',
                'message' => 'The payment link is invalid or incomplete.',
            ], 404);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $token);
        } catch (Throwable $exception) {
            error_log('Appointment payment page error: ' . $exception->getMessage());
            View::renderDatabaseError('Could not load payment details right now.', 503);
            return;
        }

        if ($booking === null) {
            View::render('pages/not_found', [
                'activePage' => 'contact',
                'title' => 'Booking not found',
                'message' => 'This booking reference is invalid or has expired.',
            ], 404);
            return;
        }

        $paymentConfig = Config::appointmentPayment();
        $latestTransaction = null;
        try {
            $latestTransaction = $this->payments->latestTransactionForAppointment((int) $booking['id']);
        } catch (Throwable $exception) {
            error_log('Appointment transaction lookup warning: ' . $exception->getMessage());
        }

        View::render('pages/appointment_payment', [
            'activePage' => 'contact',
            'booking' => $booking,
            'ackToken' => $token,
            'paymentConfig' => $paymentConfig,
            'paymentConfigured' => $this->razorpay->isConfigured(),
            'latestTransaction' => $latestTransaction,
        ]);
    }

    public function createPaymentOrderApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['booking_reference'] ?? ''));
        $ackToken = trim((string) ($payload['ack_token'] ?? ''));

        if ($reference === '' || $ackToken === '') {
            View::json([
                'status' => 'error',
                'detail' => 'booking_reference and ack_token are required.',
            ], 422);
            return;
        }

        if (!$this->razorpay->isConfigured()) {
            View::json([
                'status' => 'error',
                'detail' => 'Online payment is not configured yet. Please contact support.',
            ], 503);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $ackToken);
        } catch (Throwable $exception) {
            error_log('Payment order booking lookup error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Unable to load this booking right now.'], 503);
            return;
        }

        if ($booking === null) {
            View::json(['status' => 'error', 'detail' => 'Booking not found for given reference/token.'], 404);
            return;
        }

        $currentStatus = (string) ($booking['payment_status'] ?? 'pending_payment');
        if (in_array($currentStatus, ['payment_verified', 'payment_partially_refunded'], true)) {
            View::json([
                'status' => 'error',
                'detail' => 'This booking has already been paid.',
                'booking_reference' => $reference,
                'payment_status' => $currentStatus,
            ], 409);
            return;
        }

        try {
            $localOrder = $this->payments->latestOrderForAppointment((int) $booking['id']);
            if ($localOrder !== null && (string) $localOrder['status'] === 'paid') {
                View::json([
                    'status' => 'error',
                    'detail' => 'This Razorpay order is already paid.',
                    'payment_status' => $currentStatus,
                ], 409);
                return;
            }

            $amountSubunits = $this->amountToSubunits((string) $booking['amount_inr']);
            $currency = strtoupper((string) ($booking['currency'] ?? 'INR'));
            $gatewayOrder = $this->razorpay->createOrder(
                $amountSubunits,
                $currency,
                $this->newOrderReceipt((string) $booking['booking_reference']),
                [
                    'booking_reference' => (string) $booking['booking_reference'],
                    'appointment_id' => (string) $booking['id'],
                ]
            );

            if (
                (int) ($gatewayOrder['amount'] ?? 0) !== $amountSubunits
                || strtoupper((string) ($gatewayOrder['currency'] ?? '')) !== $currency
            ) {
                throw new RuntimeException('Razorpay order amount or currency validation failed.');
            }

            $localOrder = $this->payments->createOrder((int) $booking['id'], $gatewayOrder);
        } catch (Throwable $exception) {
            error_log('Razorpay order create error: ' . $exception->getMessage());
            $message = strtolower($exception->getMessage());
            $detail = str_contains($message, 'authentication failed')
                ? 'Razorpay authentication failed. Please check RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env.'
                : 'Could not initialize secure payment. Please try again.';
            View::json([
                'status' => 'error',
                'detail' => env_bool('APP_DEBUG', false)
                    ? 'Could not initialize payment: ' . $exception->getMessage()
                    : $detail,
            ], 503);
            return;
        }

        $paymentConfig = Config::appointmentPayment();
        View::json([
            'status' => 'ok',
            'checkout' => [
                'key_id' => $this->razorpay->keyId(),
                'order_id' => (string) $localOrder['gateway_order_id'],
                'amount' => (int) $localOrder['amount_subunits'],
                'currency' => (string) $localOrder['currency'],
                'name' => (string) ($paymentConfig['checkout_name'] ?? Config::appName()),
                'description' => (string) ($paymentConfig['checkout_description'] ?? 'Consultation Appointment'),
                'theme_color' => (string) ($paymentConfig['checkout_theme_color'] ?? '#2d6a4f'),
                'prefill' => [
                    'name' => (string) $booking['full_name'],
                    'email' => (string) $booking['email'],
                    'contact' => (string) $booking['phone'],
                ],
            ],
        ]);
    }

    public function verifyPaymentApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['booking_reference'] ?? ''));
        $ackToken = trim((string) ($payload['ack_token'] ?? ''));
        $orderId = trim((string) ($payload['razorpay_order_id'] ?? ''));
        $paymentId = trim((string) ($payload['razorpay_payment_id'] ?? ''));
        $signature = trim((string) ($payload['razorpay_signature'] ?? ''));

        if ($reference === '' || $ackToken === '' || $orderId === '' || $paymentId === '' || $signature === '') {
            View::json(['status' => 'error', 'detail' => 'Incomplete payment verification response.'], 422);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $ackToken);
            $localOrder = $this->payments->findOrderByGatewayId($orderId);
        } catch (Throwable $exception) {
            error_log('Payment verification lookup error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Unable to verify payment right now.'], 503);
            return;
        }

        if (
            $booking === null
            || $localOrder === null
            || (int) $localOrder['appointment_id'] !== (int) $booking['id']
        ) {
            View::json(['status' => 'error', 'detail' => 'Payment does not match this booking.'], 404);
            return;
        }

        if (!$this->razorpay->verifyPaymentSignature($orderId, $paymentId, $signature)) {
            error_log('Razorpay checkout signature mismatch for order ' . $orderId);
            View::json(['status' => 'error', 'detail' => 'Payment signature verification failed.'], 400);
            return;
        }

        try {
            $payment = $this->razorpay->fetchPayment($paymentId);
            if (
                (string) ($payment['id'] ?? '') !== $paymentId
                || (string) ($payment['order_id'] ?? '') !== $orderId
            ) {
                throw new RuntimeException('Razorpay payment identity validation failed.');
            }

            $result = $this->payments->recordPayment($payment, true, 'checkout');
        } catch (Throwable $exception) {
            error_log('Razorpay payment verification error: ' . $exception->getMessage());
            View::json([
                'status' => 'error',
                'detail' => 'Payment was received but confirmation is still processing. Please keep your booking reference.',
            ], 503);
            return;
        }

        View::json([
            'status' => 'ok',
            'booking_reference' => $reference,
            'payment_status' => $result['booking_status'],
            'gateway_status' => $result['gateway_status'],
            'detail' => $this->statusMessage($result['booking_status']),
        ]);
    }

    public function paymentFailureApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['booking_reference'] ?? ''));
        $ackToken = trim((string) ($payload['ack_token'] ?? ''));
        $orderId = trim((string) ($payload['razorpay_order_id'] ?? ''));
        $paymentId = trim((string) ($payload['razorpay_payment_id'] ?? ''));

        if ($reference === '' || $ackToken === '' || $orderId === '' || $paymentId === '') {
            View::json(['status' => 'error', 'detail' => 'Incomplete failed-payment details.'], 422);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $ackToken);
            $localOrder = $this->payments->findOrderByGatewayId($orderId);
            if (
                $booking === null
                || $localOrder === null
                || (int) $localOrder['appointment_id'] !== (int) $booking['id']
            ) {
                View::json(['status' => 'error', 'detail' => 'Payment does not match this booking.'], 404);
                return;
            }

            $payment = $this->razorpay->fetchPayment($paymentId);
            if ((string) ($payment['order_id'] ?? '') !== $orderId) {
                throw new RuntimeException('Razorpay failed-payment order validation failed.');
            }

            $result = $this->payments->recordPayment($payment, false, 'checkout_failure');
        } catch (Throwable $exception) {
            error_log('Razorpay failed-payment reconciliation warning: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Could not reconcile the failed payment yet.'], 503);
            return;
        }

        View::json([
            'status' => 'ok',
            'payment_status' => $result['booking_status'],
            'gateway_status' => $result['gateway_status'],
            'detail' => $this->statusMessage($result['booking_status']),
        ]);
    }

    public function razorpayWebhookApi(): void
    {
        $rawBody = file_get_contents('php://input');
        $rawBody = is_string($rawBody) ? $rawBody : '';
        $signature = trim((string) ($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? ''));

        if ($this->razorpay->webhookSecret() === '') {
            View::json(['status' => 'error', 'detail' => 'Webhook secret is not configured.'], 503);
            return;
        }

        if (!$this->razorpay->verifyWebhookSignature($rawBody, $signature)) {
            View::json(['status' => 'error', 'detail' => 'Invalid webhook signature.'], 400);
            return;
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            View::json(['status' => 'error', 'detail' => 'Invalid webhook payload.'], 400);
            return;
        }

        $eventType = trim((string) ($payload['event'] ?? 'unknown'));
        $payment = $payload['payload']['payment']['entity'] ?? null;
        $payment = is_array($payment) ? $payment : null;
        $gatewayOrderId = $payment === null ? null : trim((string) ($payment['order_id'] ?? ''));
        $gatewayPaymentId = $payment === null ? null : trim((string) ($payment['id'] ?? ''));
        $payloadHash = hash('sha256', $rawBody);
        $eventId = trim((string) ($_SERVER['HTTP_X_RAZORPAY_EVENT_ID'] ?? ''));
        $eventId = substr($eventId !== '' ? $eventId : 'sha256:' . $payloadHash, 0, 255);
        $eventClaimed = false;

        try {
            $eventClaimed = $this->payments->beginWebhookEvent(
                $eventId,
                substr($eventType, 0, 100),
                $gatewayOrderId === '' ? null : $gatewayOrderId,
                $gatewayPaymentId === '' ? null : $gatewayPaymentId,
                $payloadHash
            );

            if (!$eventClaimed) {
                View::json(['status' => 'ok', 'detail' => 'Webhook already processed.']);
                return;
            }

            if ($payment !== null && $gatewayOrderId !== '') {
                $localOrder = $this->payments->findOrderByGatewayId($gatewayOrderId);
                if ($localOrder !== null) {
                    $this->payments->recordPayment($payment, false, 'webhook');
                }
            }

            $this->payments->completeWebhookEvent($eventId);
        } catch (Throwable $exception) {
            if ($eventClaimed) {
                try {
                    $this->payments->failWebhookEvent($eventId, $exception->getMessage());
                } catch (Throwable) {
                }
            }
            error_log('Razorpay webhook processing error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Webhook processing failed.'], 500);
            return;
        }

        View::json(['status' => 'ok']);
    }

    public function paymentStatusApi(string $reference): void
    {
        $reference = trim($reference);
        $ackToken = trim((string) ($_GET['token'] ?? ''));

        if ($reference === '' || $ackToken === '') {
            View::json(['status' => 'error', 'detail' => 'Reference and token are required.'], 422);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $ackToken);
        } catch (Throwable $exception) {
            error_log('Payment status API error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Unable to fetch payment status right now.'], 503);
            return;
        }

        if ($booking === null) {
            View::json(['status' => 'error', 'detail' => 'Booking not found.'], 404);
            return;
        }

        $paymentStatus = (string) ($booking['payment_status'] ?? 'pending_payment');
        $latestTransaction = null;
        try {
            $latestTransaction = $this->payments->latestTransactionForAppointment((int) $booking['id']);
        } catch (Throwable $exception) {
            error_log('Payment status transaction lookup warning: ' . $exception->getMessage());
        }

        View::json([
            'status' => 'ok',
            'booking_reference' => (string) $booking['booking_reference'],
            'payment_status' => $paymentStatus,
            'detail' => $this->statusMessage($paymentStatus),
            'amount_inr' => (string) ($booking['amount_inr'] ?? ''),
            'currency' => (string) ($booking['currency'] ?? 'INR'),
            'payment_acknowledged_at' => $booking['payment_acknowledged_at'] ?? null,
            'payment_verified_at' => $booking['payment_verified_at'] ?? null,
            'verification_note' => $booking['verification_note'] ?? null,
            'gateway_status' => $latestTransaction['status'] ?? null,
            'gateway_payment_id' => $latestTransaction['gateway_payment_id'] ?? null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function requestPayload(): array
    {
        $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
        if (str_contains($contentType, 'application/json')) {
            $rawBody = file_get_contents('php://input');
            if (!is_string($rawBody) || trim($rawBody) === '') {
                return [];
            }

            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return [];
        }

        return $_POST;
    }

    private function validatePreferredDate(string $date): ?string
    {
        $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($parsed === false) {
            return null;
        }

        $normalized = $parsed->format('Y-m-d');
        if ($normalized !== $date) {
            return null;
        }

        $today = new DateTimeImmutable('today');
        if ($parsed < $today) {
            return null;
        }

        return $normalized;
    }

    private function clientIp(): ?string
    {
        $forwarded = trim((string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($forwarded !== '') {
            $parts = explode(',', $forwarded);
            $candidate = trim((string) $parts[0]);
            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }

        $remoteAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
        if ($remoteAddress !== '' && filter_var($remoteAddress, FILTER_VALIDATE_IP)) {
            return $remoteAddress;
        }

        return null;
    }

    private function amountToSubunits(string $amount): int
    {
        $amount = trim($amount);
        if (!preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            throw new RuntimeException('Invalid appointment payment amount.');
        }

        $whole = (int) $matches[1];
        $fraction = str_pad((string) ($matches[2] ?? ''), 2, '0');
        $subunits = ($whole * 100) + (int) $fraction;

        if ($subunits < 1) {
            throw new RuntimeException('Appointment payment amount must be greater than zero.');
        }

        return $subunits;
    }

    private function newOrderReceipt(string $bookingReference): string
    {
        return substr(
            $bookingReference . '-' . date('His') . '-' . strtoupper(bin2hex(random_bytes(2))),
            0,
            40
        );
    }

    private function statusMessage(string $status): string
    {
        return match ($status) {
            'payment_initiated' => 'Payment has been initiated in Razorpay.',
            'payment_authorized' => 'Payment is authorized and awaiting capture confirmation.',
            'payment_verified' => 'Payment captured successfully. Your appointment request is confirmed.',
            'payment_failed' => 'Payment was not completed. You can use Pay to try again.',
            'payment_partially_refunded' => 'This payment has been partially refunded.',
            'payment_refunded' => 'This payment has been refunded.',
            'payment_rejected' => 'Payment was rejected during manual review.',
            default => 'Awaiting payment.',
        };
    }

    private function createErrorMessage(Throwable $exception): string
    {
        $detail = strtolower($exception->getMessage());

        $tableMissing = str_contains($detail, 'appointment_bookings')
            && (
                str_contains($detail, "doesn't exist")
                || str_contains($detail, 'does not exist')
                || str_contains($detail, 'base table or view not found')
                || str_contains($detail, 'undefined table')
            );

        if ($tableMissing) {
            return 'Appointment booking setup is incomplete. Please contact support and try again shortly.';
        }

        $schemaMismatch = str_contains($detail, 'unknown column') || str_contains($detail, 'invalid column');
        if ($schemaMismatch) {
            return 'Appointment booking schema is outdated. Please contact support and retry in a few minutes.';
        }

        if (env_bool('APP_DEBUG', false)) {
            return 'Unable to start appointment booking: ' . $exception->getMessage();
        }

        return 'Unable to start your appointment booking right now. Please try again shortly.';
    }
}

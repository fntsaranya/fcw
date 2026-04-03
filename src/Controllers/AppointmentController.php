<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Config;
use FCW\Core\View;
use FCW\Repositories\AppointmentRepository;
use DateTimeImmutable;
use Throwable;

final class AppointmentController
{
    public function __construct(private readonly AppointmentRepository $appointments = new AppointmentRepository())
    {
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
        $upiId = trim((string) ($paymentConfig['upi_id'] ?? ''));
        $upiIntentUrl = '';

        if ($upiId !== '') {
            $notePrefix = trim((string) ($paymentConfig['upi_note_prefix'] ?? 'FCW Appointment'));
            $note = trim($notePrefix . ' ' . (string) $booking['booking_reference']);
            $upiIntentUrl = sprintf(
                'upi://pay?pa=%s&pn=%s&am=%s&cu=%s&tn=%s',
                rawurlencode($upiId),
                rawurlencode((string) ($paymentConfig['upi_payee_name'] ?? Config::appName())),
                rawurlencode((string) $booking['amount_inr']),
                rawurlencode((string) ($booking['currency'] ?? 'INR')),
                rawurlencode($note)
            );
        }

        $gpayPath = (string) ($paymentConfig['gpay_qr_image'] ?? '');
        $phonepePath = (string) ($paymentConfig['phonepe_qr_image'] ?? '');
        $gpayLogoPath = (string) ($paymentConfig['gpay_logo_image'] ?? '');
        $phonepeLogoPath = (string) ($paymentConfig['phonepe_logo_image'] ?? '');

        View::render('pages/appointment_payment', [
            'activePage' => 'contact',
            'booking' => $booking,
            'ackToken' => $token,
            'paymentConfig' => $paymentConfig,
            'upiIntentUrl' => $upiIntentUrl,
            'gpayLogoPath' => $gpayLogoPath,
            'phonepeLogoPath' => $phonepeLogoPath,
            'gpayQrPath' => $gpayPath,
            'phonepeQrPath' => $phonepePath,
            'gpayLogoExists' => $this->publicAssetExists($gpayLogoPath),
            'phonepeLogoExists' => $this->publicAssetExists($phonepeLogoPath),
            'gpayQrExists' => $this->publicAssetExists($gpayPath),
            'phonepeQrExists' => $this->publicAssetExists($phonepePath),
        ]);
    }

    public function acknowledgePaymentApi(): void
    {
        $payload = $this->requestPayload();

        $reference = trim((string) ($payload['booking_reference'] ?? ''));
        $ackToken = trim((string) ($payload['ack_token'] ?? ''));
        $upiTransactionRef = trim((string) ($payload['upi_transaction_ref'] ?? ''));
        $paymentChannel = trim((string) ($payload['payment_channel'] ?? ''));

        if ($reference === '' || $ackToken === '') {
            View::json([
                'status' => 'error',
                'detail' => 'booking_reference and ack_token are required.',
            ], 422);
            return;
        }

        if ($paymentChannel === '' || !in_array($paymentChannel, ['gpay', 'phonepe'], true)) {
            View::json([
                'status' => 'error',
                'detail' => 'Please select a valid UPI method (Google Pay or PhonePe).',
            ], 422);
            return;
        }

        if ($upiTransactionRef !== '' && (strlen($upiTransactionRef) < 6 || strlen($upiTransactionRef) > 150)) {
            View::json([
                'status' => 'error',
                'detail' => 'UPI transaction reference must be between 6 and 150 characters.',
            ], 422);
            return;
        }

        try {
            $booking = $this->appointments->findByReferenceAndToken($reference, $ackToken);
        } catch (Throwable $exception) {
            error_log('Payment lookup error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Unable to verify booking at the moment.'], 503);
            return;
        }

        if ($booking === null) {
            View::json(['status' => 'error', 'detail' => 'Booking not found for given reference/token.'], 404);
            return;
        }

        $currentStatus = (string) ($booking['payment_status'] ?? 'pending_payment');
        if ($currentStatus === 'payment_verified') {
            View::json([
                'status' => 'ok',
                'detail' => 'This payment is already verified.',
                'booking_reference' => $reference,
                'payment_status' => 'payment_verified',
            ]);
            return;
        }

        try {
            $this->appointments->acknowledgePayment(
                $reference,
                $ackToken,
                $upiTransactionRef === '' ? null : $upiTransactionRef,
                $paymentChannel === '' ? null : $paymentChannel
            );
        } catch (Throwable $exception) {
            error_log('Payment acknowledge error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => 'Could not save payment acknowledgement.'], 503);
            return;
        }

        View::json([
            'status' => 'ok',
            'detail' => 'Payment acknowledgement received. Our team will verify and confirm shortly.',
            'booking_reference' => $reference,
            'payment_status' => 'payment_submitted',
        ]);
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

    private function publicAssetExists(string $publicPath): bool
    {
        if ($publicPath === '' || !str_starts_with($publicPath, '/static/')) {
            return false;
        }

        return is_file(BASE_PATH . $publicPath);
    }

    private function statusMessage(string $status): string
    {
        return match ($status) {
            'payment_submitted' => 'Payment proof submitted. Verification is in progress.',
            'payment_verified' => 'Payment verified. Appointment confirmation will be shared shortly.',
            'payment_rejected' => 'Payment could not be verified. Please re-submit with correct transaction details.',
            default => 'Awaiting payment acknowledgement from your side.',
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

<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Config;
use FCW\Core\View;
use FCW\Repositories\EnquiryRegistrationRepository;
use FCW\Repositories\WebinarRepository;
use FCW\Services\RazorpayService;
use RuntimeException;
use Throwable;

final class EnquiryPaymentController
{
    private RazorpayService $razorpay;

    public function __construct(
        private readonly EnquiryRegistrationRepository $enquiryRegistrations = new EnquiryRegistrationRepository(),
        private readonly WebinarRepository $webinars = new WebinarRepository()
    ) {
        $this->razorpay = new RazorpayService(Config::enquiryPayment());
    }

    public function paymentPage(): void
    {
        $reference = trim((string) ($_GET['ref'] ?? ''));
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($reference === '' || $token === '') {
            View::render('pages/not_found', [
                'activePage' => 'enquiry',
                'title' => 'Registration not found',
                'message' => 'The payment link is invalid or incomplete.',
            ], 404);
            return;
        }

        try {
            $registration = $this->enquiryRegistrations->findByReferenceAndToken($reference, $token);
        } catch (Throwable $exception) {
            error_log('Enquiry payment lookup error: ' . $exception->getMessage());
            View::renderDatabaseError('Could not load payment details right now.', 503);
            return;
        }

        if ($registration === null) {
            View::render('pages/not_found', [
                'activePage' => 'enquiry',
                'title' => 'Registration not found',
                'message' => 'This registration reference is invalid or has expired.',
            ], 404);
            return;
        }

        $webinar = [];
        try {
            $webinar = $this->webinars->getActive();
        } catch (Throwable $e) {
            error_log('Webinar lookup error in payment page: ' . $e->getMessage());
        }

        $paymentConfig = Config::enquiryPayment();
        $whatsappGroupUrl = !empty($webinar['whatsapp_group_link'])
            ? (string) $webinar['whatsapp_group_link']
            : (string) (Config::contact()['WHATSAPP_GROUP'] ?? '#');

        View::render('pages/enquiry_payment', [
            'activePage' => 'enquiry',
            'registration' => $registration,
            'webinar' => $webinar,
            'ackToken' => $token,
            'paymentConfig' => $paymentConfig,
            'paymentConfigured' => $this->razorpay->isConfigured(),
            'whatsappGroupUrl' => $whatsappGroupUrl,
        ]);
    }

    public function createPaymentOrderApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['registration_reference'] ?? ''));
        $token = trim((string) ($payload['ack_token'] ?? ''));

        if ($reference === '' || $token === '') {
            View::json(['status' => 'error', 'detail' => 'Registration reference and token are required.'], 400);
            return;
        }

        try {
            $registration = $this->enquiryRegistrations->findByReferenceAndToken($reference, $token);
            if ($registration === null) {
                View::json(['status' => 'error', 'detail' => 'Registration not found.'], 404);
                return;
            }

            if ((string) ($registration['payment_status'] ?? '') === 'payment_verified') {
                View::json([
                    'status' => 'ok',
                    'already_paid' => true,
                    'payment_status' => 'payment_verified',
                    'detail' => 'This registration has already been paid and confirmed.',
                ]);
                return;
            }

            $amountInr = (float) ($registration['amount_inr'] ?? 0.00);
            if ($amountInr <= 0.00) {
                $amountInr = (float) (Config::enquiryPayment()['fee_inr'] ?? 0.00);
            }
            $amountSubunits = (int) round($amountInr * 100);

            if ($amountSubunits < 100) {
                View::json(['status' => 'error', 'detail' => 'Invalid payment amount.'], 400);
                return;
            }

            $receipt = substr('WEB-' . $registration['registration_reference'], 0, 40);
            $notes = [
                'type' => 'webinar_enquiry_registration',
                'reference' => $registration['registration_reference'],
                'name' => (string) ($registration['name'] ?? ''),
                'email' => (string) ($registration['email'] ?? ''),
                'webinar' => (string) ($registration['webinar_title'] ?? ''),
            ];

            $order = $this->razorpay->createOrder($amountSubunits, 'INR', $receipt, $notes);
            $savedOrder = $this->enquiryRegistrations->createOrder((int) $registration['id'], $order);

            $paymentConfig = Config::enquiryPayment();

            View::json([
                'status' => 'ok',
                'checkout' => [
                    'key_id' => $this->razorpay->keyId(),
                    'order_id' => $savedOrder['gateway_order_id'],
                    'amount' => $savedOrder['amount_subunits'],
                    'currency' => $savedOrder['currency'],
                    'name' => $paymentConfig['checkout_name'],
                    'description' => (string) ($registration['webinar_title'] ?? $paymentConfig['checkout_description']),
                    'prefill' => [
                        'name' => (string) ($registration['name'] ?? ''),
                        'email' => (string) ($registration['email'] ?? ''),
                        'contact' => (string) ($registration['phone'] ?? ''),
                    ],
                    'theme_color' => $paymentConfig['checkout_theme_color'],
                ],
            ]);
        } catch (Throwable $exception) {
            error_log('Enquiry create payment order error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => $exception->getMessage()], 500);
        }
    }

    public function verifyPaymentApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['registration_reference'] ?? ''));
        $token = trim((string) ($payload['ack_token'] ?? ''));
        $orderId = trim((string) ($payload['razorpay_order_id'] ?? ''));
        $paymentId = trim((string) ($payload['razorpay_payment_id'] ?? ''));
        $signature = trim((string) ($payload['razorpay_signature'] ?? ''));

        if ($reference === '' || $token === '' || $orderId === '' || $paymentId === '' || $signature === '') {
            View::json(['status' => 'error', 'detail' => 'Incomplete payment verification payload.'], 400);
            return;
        }

        try {
            $registration = $this->enquiryRegistrations->findByReferenceAndToken($reference, $token);
            if ($registration === null) {
                View::json(['status' => 'error', 'detail' => 'Registration not found.'], 404);
                return;
            }

            $orderRecord = $this->enquiryRegistrations->findOrderByGatewayId($orderId);
            if ($orderRecord === null || (int) $orderRecord['registration_id'] !== (int) $registration['id']) {
                View::json(['status' => 'error', 'detail' => 'Order does not match this registration.'], 400);
                return;
            }

            $signatureValid = $this->razorpay->verifyPaymentSignature($orderId, $paymentId, $signature);
            if (!$signatureValid) {
                View::json(['status' => 'error', 'detail' => 'Payment signature verification failed.'], 400);
                return;
            }

            // Fetch payment details from Razorpay to confirm capture & method
            $paymentDetails = $this->razorpay->fetchPayment($paymentId);
            $paymentStatus = (string) ($paymentDetails['status'] ?? 'captured');
            $method = (string) ($paymentDetails['method'] ?? 'online');
            $amountSubunits = (int) ($paymentDetails['amount'] ?? $orderRecord['amount_subunits']);
            $currency = (string) ($paymentDetails['currency'] ?? 'INR');

            $this->enquiryRegistrations->recordTransaction(
                (int) $orderRecord['id'],
                $paymentId,
                $amountSubunits,
                $currency,
                $paymentStatus,
                $method,
                true
            );

            $this->enquiryRegistrations->markPaymentSuccess(
                (int) $registration['id'],
                $orderId,
                $paymentId,
                $method
            );

            $webinar = [];
            try {
                $webinar = $this->webinars->getActive();
            } catch (Throwable $e) {
                // ignore
            }

            $whatsappGroupUrl = !empty($webinar['whatsapp_group_link'])
                ? (string) $webinar['whatsapp_group_link']
                : (string) (Config::contact()['WHATSAPP_GROUP'] ?? '#');

            $this->enquiryRegistrations->markWhatsAppRedirected((int) $registration['id']);

            View::json([
                'status' => 'ok',
                'payment_status' => 'payment_verified',
                'whatsapp_group_url' => $whatsappGroupUrl,
                'detail' => 'Payment verified successfully! Redirecting you to the WhatsApp Group...',
            ]);
        } catch (Throwable $exception) {
            error_log('Enquiry verify payment error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => $exception->getMessage()], 500);
        }
    }

    public function paymentFailureApi(): void
    {
        $payload = $this->requestPayload();
        $reference = trim((string) ($payload['registration_reference'] ?? ''));
        $token = trim((string) ($payload['ack_token'] ?? ''));
        $orderId = trim((string) ($payload['razorpay_order_id'] ?? ''));
        $paymentId = trim((string) ($payload['razorpay_payment_id'] ?? ''));
        $errorCode = trim((string) ($payload['error_code'] ?? ''));
        $errorDesc = trim((string) ($payload['error_description'] ?? 'Payment cancelled or failed.'));

        if ($reference === '' || $token === '') {
            View::json(['status' => 'error', 'detail' => 'Registration reference and token are required.'], 400);
            return;
        }

        try {
            $registration = $this->enquiryRegistrations->findByReferenceAndToken($reference, $token);
            if ($registration === null) {
                View::json(['status' => 'error', 'detail' => 'Registration not found.'], 404);
                return;
            }

            if ($orderId !== '') {
                $orderRecord = $this->enquiryRegistrations->findOrderByGatewayId($orderId);
                if ($orderRecord !== null && $paymentId !== '') {
                    $this->enquiryRegistrations->recordTransaction(
                        (int) $orderRecord['id'],
                        $paymentId,
                        (int) $orderRecord['amount_subunits'],
                        (string) $orderRecord['currency'],
                        'failed',
                        null,
                        false,
                        $errorCode,
                        $errorDesc
                    );
                }
            }

            $this->enquiryRegistrations->markPaymentFailed(
                (int) $registration['id'],
                $orderId,
                $paymentId !== '' ? $paymentId : null,
                $errorDesc
            );

            View::json([
                'status' => 'ok',
                'payment_status' => 'payment_failed',
                'detail' => $errorDesc,
            ]);
        } catch (Throwable $exception) {
            error_log('Enquiry payment failure recording error: ' . $exception->getMessage());
            View::json(['status' => 'error', 'detail' => $exception->getMessage()], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function requestPayload(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return $_POST;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $_POST;
    }
}

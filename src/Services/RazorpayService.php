<?php
declare(strict_types=1);

namespace FCW\Services;

use FCW\Core\Config;
use RuntimeException;

final class RazorpayService
{
    private const API_BASE_URL = 'https://api.razorpay.com/v1';

    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed>|null $config
     */
    public function __construct(?array $config = null)
    {
        $this->config = $config ?? Config::appointmentPayment();
    }

    public function isConfigured(): bool
    {
        return $this->keyId() !== '' && $this->keySecret() !== '';
    }

    public function keyId(): string
    {
        return trim((string) ($this->config['razorpay_key_id'] ?? ''));
    }

    public function webhookSecret(): string
    {
        return trim((string) ($this->config['razorpay_webhook_secret'] ?? ''));
    }

    /**
     * @param array<string, string> $notes
     * @return array<string, mixed>
     */
    public function createOrder(int $amountSubunits, string $currency, string $receipt, array $notes = []): array
    {
        if ($amountSubunits < 1) {
            throw new RuntimeException('Razorpay order amount must be at least one currency subunit.');
        }

        return $this->request('POST', '/orders', [
            'amount' => $amountSubunits,
            'currency' => strtoupper($currency),
            'receipt' => substr($receipt, 0, 40),
            'notes' => $notes,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchPayment(string $paymentId): array
    {
        if (!preg_match('/^pay_[A-Za-z0-9]+$/', $paymentId)) {
            throw new RuntimeException('Invalid Razorpay payment ID.');
        }

        return $this->request('GET', '/payments/' . rawurlencode($paymentId));
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        if ($orderId === '' || $paymentId === '' || $signature === '' || $this->keySecret() === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret());

        return hash_equals($expected, $signature);
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        $secret = $this->webhookSecret();
        if ($rawBody === '' || $signature === '' || $secret === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, ?array $payload = null): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Razorpay API credentials are not configured.');
        }

        if (!function_exists('curl_init')) {
            throw new RuntimeException('The PHP cURL extension is required for Razorpay payments.');
        }

        $handle = curl_init(self::API_BASE_URL . $path);
        if ($handle === false) {
            throw new RuntimeException('Could not initialize the Razorpay API request.');
        }

        $headers = ['Accept: application/json'];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => $this->keyId() . ':' . $this->keySecret(),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
        ];

        $caInfo = $this->caInfoPath();
        if ($caInfo !== null) {
            $options[CURLOPT_CAINFO] = $caInfo;
        }

        if ($payload !== null) {
            $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES);
            if (!is_string($encoded)) {
                curl_close($handle);
                throw new RuntimeException('Could not encode the Razorpay API request.');
            }

            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = $encoded;
        }

        curl_setopt_array($handle, $options);
        $response = curl_exec($handle);
        $statusCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $curlError = curl_error($handle);
        curl_close($handle);

        if (!is_string($response)) {
            throw new RuntimeException('Razorpay API connection failed: ' . $curlError);
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Razorpay returned an invalid response.');
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $description = trim((string) ($decoded['error']['description'] ?? 'Razorpay API request failed.'));
            throw new RuntimeException($description);
        }

        return $decoded;
    }

    private function keySecret(): string
    {
        return trim((string) ($this->config['razorpay_key_secret'] ?? ''));
    }

    private function caInfoPath(): ?string
    {
        $candidates = [
            (string) ($this->config['razorpay_curl_cainfo'] ?? ''),
            (string) getenv('CURL_CA_BUNDLE'),
            (string) getenv('SSL_CERT_FILE'),
        ];

        if (defined('BASE_PATH')) {
            $candidates[] = BASE_PATH . '/storage/cacert.pem';
        }

        foreach ($candidates as $candidate) {
            $path = trim($candidate);
            if ($path === '') {
                continue;
            }

            if (defined('BASE_PATH') && !preg_match('/^([A-Za-z]:)?[\/\\\\]/', $path)) {
                $path = BASE_PATH . '/' . $path;
            }

            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}

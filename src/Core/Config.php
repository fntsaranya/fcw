<?php
declare(strict_types=1);

namespace FCW\Core;

final class Config
{
    private const RAZORPAY_MODES = ['test', 'live'];

    public static function appName(): string
    {
        return (string) env('APP_NAME', 'Functional Chronic Wellness');
    }

    public static function databaseUrl(): string
    {
        return (string) env(
            'DATABASE_URL',
            'postgresql://neondb_owner:npg_A85YtKZqcuXQ@ep-orange-bush-a9lyf39l-pooler.gwc.azure.neon.tech/neondb?sslmode=require&channel_binding=require'
        );
    }

    public static function autoCreateTables(): bool
    {
        return env_bool('AUTO_CREATE_TABLES', true);
    }

    public static function adminPin(): string
    {
        return (string) env('ADMIN_PIN', '1234');
    }

    public static function contact(): array
    {
        return [
            'EMAIL' => (string) env('CONTACT_EMAIL', 'fntsaranya@gmail.com'),
            'PHONE' => (string) env('CONTACT_PHONE', '+91 9600491703'),
            'PHONE_DISPLAY' => (string) env('CONTACT_PHONE_DISPLAY', '+91 9600491703'),
            'ADDRESS_LINE_1' => (string) env('CONTACT_ADDRESS_LINE_1', 'fntsaranya@gmail.com'),
            'ADDRESS_LINE_2' => (string) env('CONTACT_ADDRESS_LINE_2', ''),
            'INSTAGRAM' => (string) env('CONTACT_INSTAGRAM', 'https://www.instagram.com/functionalchronicwellness?igsh=ZGFjaGs2bzVkc2E3'),
            'LINKEDIN' => (string) env('CONTACT_LINKEDIN', 'https://www.linkedin.com/groups/15857049/'),
            'WHATSAPP_CHANNEL' => (string) env('CONTACT_WHATSAPP_CHANNEL', 'https://whatsapp.com/channel/0029Vb7KXwYEQIarE5JlCI2I'),
            'WHATSAPP_GROUP' => (string) env('CONTACT_WHATSAPP_GROUP', 'https://chat.whatsapp.com/K14PNweOFsPA9vPCRrdjg3?mode=hqrc'),
        ];
    }

    public static function smtp(): array
    {
        return [
            'host' => (string) env('SMTP_SERVER', 'smtp.gmail.com'),
            'port' => env_int('SMTP_PORT', 587),
            'sender_email' => (string) env('SENDER_EMAIL', 'fntsaranya@gmail.com'),
            'sender_password' => (string) env('SENDER_PASSWORD', ''),
            'admin_email' => (string) env('ADMIN_EMAIL', 'fntsaranya@gmail.com'),
        ];
    }

    public static function appointmentPayment(): array
    {
        $mode = self::razorpayMode();
        return [
            'razorpay_mode' => $mode,
            'razorpay_mode_label' => ucfirst($mode) . ' Mode',
            'fee_inr' => (string) env('APPOINTMENT_FEE_INR', '499.00'),
            'currency' => 'INR',
            'razorpay_key_id' => self::razorpayEnvValue($mode, 'KEY_ID', 'RAZORPAY_KEY_ID'),
            'razorpay_key_secret' => self::razorpayEnvValue($mode, 'KEY_SECRET', 'RAZORPAY_KEY_SECRET'),
            'razorpay_webhook_secret' => self::razorpayEnvValue($mode, 'WEBHOOK_SECRET', 'RAZORPAY_WEBHOOK_SECRET'),
            'razorpay_curl_cainfo' => (string) env('RAZORPAY_CURL_CAINFO', ''),
            'checkout_name' => (string) env('RAZORPAY_CHECKOUT_NAME', self::appName()),
            'checkout_description' => (string) env('RAZORPAY_CHECKOUT_DESCRIPTION', 'Consultation Appointment'),
            'checkout_theme_color' => (string) env('RAZORPAY_CHECKOUT_THEME_COLOR', '#2d6a4f'),
        ];
    }

    public static function dbPoolSize(): int
    {
        return env_int('DB_POOL_SIZE', 5);
    }

    public static function dbMaxOverflow(): int
    {
        return env_int('DB_MAX_OVERFLOW', 5);
    }

    public static function dbPoolRecycleSeconds(): int
    {
        return env_int('DB_POOL_RECYCLE_SECONDS', 1800);
    }

    private static function razorpayMode(): string
    {
        $mode = strtolower(trim((string) env('RAZORPAY_MODE', 'test')));
        return in_array($mode, self::RAZORPAY_MODES, true) ? $mode : 'test';
    }

    private static function razorpayEnvValue(string $mode, string $suffix, string $legacyKey): string
    {
        $modeKey = strtoupper($mode);
        $value = trim((string) env('RAZORPAY_' . $modeKey . '_' . $suffix, ''));
        if ($value !== '') {
            return $value;
        }

        return trim((string) env($legacyKey, ''));
    }
}

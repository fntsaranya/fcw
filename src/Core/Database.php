<?php
declare(strict_types=1);

namespace FCW\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;
    private static bool $schemaInitialized = false;
    private static ?string $driver = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $url = self::normalizeDatabaseUrl(Config::databaseUrl());
        $parsed = self::parseDatabaseUrl($url);

        $dsn = self::buildDsn($parsed);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$driver = self::canonicalDriver($parsed['scheme']);

        self::$pdo = new PDO(
            $dsn,
            $parsed['user'],
            $parsed['pass'],
            $options
        );

        return self::$pdo;
    }

    public static function driver(): string
    {
        if (self::$driver !== null) {
            return self::$driver;
        }

        $url = self::normalizeDatabaseUrl(Config::databaseUrl());
        $parsed = self::parseDatabaseUrl($url);
        self::$driver = self::canonicalDriver($parsed['scheme']);

        return self::$driver;
    }

    public static function ensureSchema(): void
    {
        if (self::$schemaInitialized || !Config::autoCreateTables()) {
            return;
        }

        $pdo = self::connection();
        $statements = match (self::driver()) {
            'mysql' => self::mysqlSchemaStatements(),
            'sqlite' => self::sqliteSchemaStatements(),
            default => self::postgresSchemaStatements(),
        };

        foreach ($statements as $sql) {
            $pdo->exec($sql);
        }

        self::$schemaInitialized = true;
    }

    private static function buildDsn(array $parsed): string
    {
        $driver = self::canonicalDriver($parsed['scheme']);

        if ($driver === 'sqlite') {
            return 'sqlite:' . $parsed['path'];
        }

        if ($driver === 'mysql') {
            return sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $parsed['host'],
                $parsed['port'],
                $parsed['dbname'],
                $parsed['charset']
            );
        }

        return sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
            $parsed['host'],
            $parsed['port'],
            $parsed['dbname'],
            $parsed['sslmode']
        );
    }

    /**
     * @return list<string>
     */
    private static function postgresSchemaStatements(): array
    {
        return [
            'CREATE TABLE IF NOT EXISTS contact_submissions (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS blog_posts (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                image_url TEXT NULL,
                created_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS health_assessments (
                id SERIAL PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                score INTEGER NOT NULL,
                interpretation VARCHAR(255) NOT NULL,
                diagnosed_autoimmune BOOLEAN DEFAULT FALSE,
                symptoms_after_puberty BOOLEAN DEFAULT FALSE,
                symptoms_post_pregnancy BOOLEAN DEFAULT FALSE,
                symptoms_after_miscarriage BOOLEAN DEFAULT FALSE,
                symptoms_during_menopause BOOLEAN DEFAULT FALSE,
                family_history_autoimmune BOOLEAN DEFAULT FALSE,
                symptoms_worse_menstrual_cycle BOOLEAN DEFAULT FALSE,
                irregular_cycles BOOLEAN DEFAULT FALSE,
                painful_cycles BOOLEAN DEFAULT FALSE,
                severe_pms BOOLEAN DEFAULT FALSE,
                heavy_bleeding BOOLEAN DEFAULT FALSE,
                missed_periods BOOLEAN DEFAULT FALSE,
                pcos_or_endometriosis BOOLEAN DEFAULT FALSE,
                infertility_history BOOLEAN DEFAULT FALSE,
                unexplained_weight_changes BOOLEAN DEFAULT FALSE,
                temperature_intolerance BOOLEAN DEFAULT FALSE,
                hair_loss BOOLEAN DEFAULT FALSE,
                low_libido BOOLEAN DEFAULT FALSE,
                mood_cycle_changes BOOLEAN DEFAULT FALSE,
                thyroid_history BOOLEAN DEFAULT FALSE,
                chronic_fatigue BOOLEAN DEFAULT FALSE,
                unrefreshed_sleep BOOLEAN DEFAULT FALSE,
                energy_crashes BOOLEAN DEFAULT FALSE,
                sleep_difficulty BOOLEAN DEFAULT FALSE,
                joint_pain BOOLEAN DEFAULT FALSE,
                morning_stiffness BOOLEAN DEFAULT FALSE,
                migratory_pain BOOLEAN DEFAULT FALSE,
                flare_remission BOOLEAN DEFAULT FALSE,
                bloating BOOLEAN DEFAULT FALSE,
                bowel_issues BOOLEAN DEFAULT FALSE,
                food_sensitivity BOOLEAN DEFAULT FALSE,
                gut_disease_history BOOLEAN DEFAULT FALSE,
                frequent_antibiotics BOOLEAN DEFAULT FALSE,
                post_infection_worsening BOOLEAN DEFAULT FALSE,
                dry_skin BOOLEAN DEFAULT FALSE,
                rashes BOOLEAN DEFAULT FALSE,
                eczema BOOLEAN DEFAULT FALSE,
                brittle_nails BOOLEAN DEFAULT FALSE,
                brain_fog BOOLEAN DEFAULT FALSE,
                anxiety_depression BOOLEAN DEFAULT FALSE,
                tingling_numbness BOOLEAN DEFAULT FALSE,
                headaches BOOLEAN DEFAULT FALSE,
                chronic_stress BOOLEAN DEFAULT FALSE,
                major_trauma BOOLEAN DEFAULT FALSE,
                short_sleep BOOLEAN DEFAULT FALSE,
                high_stress BOOLEAN DEFAULT FALSE,
                unexplained_fever BOOLEAN DEFAULT FALSE,
                rapid_weight_loss BOOLEAN DEFAULT FALSE,
                persistent_swelling BOOLEAN DEFAULT FALSE,
                vision_changes BOOLEAN DEFAULT FALSE,
                blood_in_stool_urine BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS appointment_bookings (
                id SERIAL PRIMARY KEY,
                booking_reference VARCHAR(40) UNIQUE NOT NULL,
                ack_token VARCHAR(64) NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                concern TEXT NOT NULL,
                preferred_date DATE NULL,
                preferred_time VARCHAR(50) NULL,
                amount_inr NUMERIC(10,2) NOT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT \'INR\',
                payment_status VARCHAR(40) NOT NULL DEFAULT \'pending_payment\',
                upi_transaction_ref VARCHAR(150) NULL,
                payer_name VARCHAR(255) NULL,
                payer_upi_id VARCHAR(255) NULL,
                payment_channel VARCHAR(40) NULL,
                payment_acknowledged_at TIMESTAMPTZ NULL,
                payment_verified_at TIMESTAMPTZ NULL,
                verification_note TEXT NULL,
                source_ip VARCHAR(64) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMPTZ DEFAULT NOW(),
                updated_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS appointment_payment_orders (
                id BIGSERIAL PRIMARY KEY,
                appointment_id INTEGER NOT NULL REFERENCES appointment_bookings(id) ON DELETE CASCADE,
                gateway VARCHAR(30) NOT NULL DEFAULT \'razorpay\',
                gateway_order_id VARCHAR(100) UNIQUE NOT NULL,
                amount_subunits BIGINT NOT NULL,
                currency VARCHAR(10) NOT NULL,
                status VARCHAR(40) NOT NULL DEFAULT \'created\',
                gateway_created_at TIMESTAMPTZ NULL,
                created_at TIMESTAMPTZ DEFAULT NOW(),
                updated_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS appointment_payment_transactions (
                id BIGSERIAL PRIMARY KEY,
                payment_order_id BIGINT NOT NULL REFERENCES appointment_payment_orders(id) ON DELETE CASCADE,
                gateway_payment_id VARCHAR(100) UNIQUE NOT NULL,
                amount_subunits BIGINT NOT NULL,
                currency VARCHAR(10) NOT NULL,
                status VARCHAR(40) NOT NULL,
                method VARCHAR(40) NULL,
                checkout_signature_verified BOOLEAN NOT NULL DEFAULT FALSE,
                record_source VARCHAR(30) NOT NULL,
                amount_refunded_subunits BIGINT NOT NULL DEFAULT 0,
                refund_status VARCHAR(20) NULL,
                error_code VARCHAR(100) NULL,
                error_description TEXT NULL,
                error_source VARCHAR(100) NULL,
                error_step VARCHAR(100) NULL,
                error_reason VARCHAR(100) NULL,
                gateway_created_at TIMESTAMPTZ NULL,
                paid_at TIMESTAMPTZ NULL,
                failed_at TIMESTAMPTZ NULL,
                created_at TIMESTAMPTZ DEFAULT NOW(),
                updated_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE TABLE IF NOT EXISTS razorpay_webhook_events (
                id BIGSERIAL PRIMARY KEY,
                event_id VARCHAR(255) UNIQUE NOT NULL,
                event_type VARCHAR(100) NOT NULL,
                gateway_order_id VARCHAR(100) NULL,
                gateway_payment_id VARCHAR(100) NULL,
                payload_sha256 VARCHAR(64) NOT NULL,
                processing_status VARCHAR(20) NOT NULL DEFAULT \'processing\',
                last_error TEXT NULL,
                processed_at TIMESTAMPTZ NULL,
                created_at TIMESTAMPTZ DEFAULT NOW(),
                updated_at TIMESTAMPTZ DEFAULT NOW()
            )',
            'CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contact_submissions(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_blog_created_at ON blog_posts(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_assessment_created_at ON health_assessments(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_created_at ON appointment_bookings(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_status ON appointment_bookings(payment_status)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_email ON appointment_bookings(email)',
            'CREATE INDEX IF NOT EXISTS idx_payment_orders_appointment ON appointment_payment_orders(appointment_id)',
            'CREATE INDEX IF NOT EXISTS idx_payment_transactions_order ON appointment_payment_transactions(payment_order_id)',
            'CREATE INDEX IF NOT EXISTS idx_payment_transactions_status ON appointment_payment_transactions(status)',
        ];
    }

    /**
     * @return list<string>
     */
    private static function mysqlSchemaStatements(): array
    {
        return [
            'CREATE TABLE IF NOT EXISTS contact_submissions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_contacts_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS blog_posts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                image_url TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_blog_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS health_assessments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                score INT NOT NULL,
                interpretation VARCHAR(255) NOT NULL,
                diagnosed_autoimmune TINYINT(1) NOT NULL DEFAULT 0,
                symptoms_after_puberty TINYINT(1) NOT NULL DEFAULT 0,
                symptoms_post_pregnancy TINYINT(1) NOT NULL DEFAULT 0,
                symptoms_after_miscarriage TINYINT(1) NOT NULL DEFAULT 0,
                symptoms_during_menopause TINYINT(1) NOT NULL DEFAULT 0,
                family_history_autoimmune TINYINT(1) NOT NULL DEFAULT 0,
                symptoms_worse_menstrual_cycle TINYINT(1) NOT NULL DEFAULT 0,
                irregular_cycles TINYINT(1) NOT NULL DEFAULT 0,
                painful_cycles TINYINT(1) NOT NULL DEFAULT 0,
                severe_pms TINYINT(1) NOT NULL DEFAULT 0,
                heavy_bleeding TINYINT(1) NOT NULL DEFAULT 0,
                missed_periods TINYINT(1) NOT NULL DEFAULT 0,
                pcos_or_endometriosis TINYINT(1) NOT NULL DEFAULT 0,
                infertility_history TINYINT(1) NOT NULL DEFAULT 0,
                unexplained_weight_changes TINYINT(1) NOT NULL DEFAULT 0,
                temperature_intolerance TINYINT(1) NOT NULL DEFAULT 0,
                hair_loss TINYINT(1) NOT NULL DEFAULT 0,
                low_libido TINYINT(1) NOT NULL DEFAULT 0,
                mood_cycle_changes TINYINT(1) NOT NULL DEFAULT 0,
                thyroid_history TINYINT(1) NOT NULL DEFAULT 0,
                chronic_fatigue TINYINT(1) NOT NULL DEFAULT 0,
                unrefreshed_sleep TINYINT(1) NOT NULL DEFAULT 0,
                energy_crashes TINYINT(1) NOT NULL DEFAULT 0,
                sleep_difficulty TINYINT(1) NOT NULL DEFAULT 0,
                joint_pain TINYINT(1) NOT NULL DEFAULT 0,
                morning_stiffness TINYINT(1) NOT NULL DEFAULT 0,
                migratory_pain TINYINT(1) NOT NULL DEFAULT 0,
                flare_remission TINYINT(1) NOT NULL DEFAULT 0,
                bloating TINYINT(1) NOT NULL DEFAULT 0,
                bowel_issues TINYINT(1) NOT NULL DEFAULT 0,
                food_sensitivity TINYINT(1) NOT NULL DEFAULT 0,
                gut_disease_history TINYINT(1) NOT NULL DEFAULT 0,
                frequent_antibiotics TINYINT(1) NOT NULL DEFAULT 0,
                post_infection_worsening TINYINT(1) NOT NULL DEFAULT 0,
                dry_skin TINYINT(1) NOT NULL DEFAULT 0,
                rashes TINYINT(1) NOT NULL DEFAULT 0,
                eczema TINYINT(1) NOT NULL DEFAULT 0,
                brittle_nails TINYINT(1) NOT NULL DEFAULT 0,
                brain_fog TINYINT(1) NOT NULL DEFAULT 0,
                anxiety_depression TINYINT(1) NOT NULL DEFAULT 0,
                tingling_numbness TINYINT(1) NOT NULL DEFAULT 0,
                headaches TINYINT(1) NOT NULL DEFAULT 0,
                chronic_stress TINYINT(1) NOT NULL DEFAULT 0,
                major_trauma TINYINT(1) NOT NULL DEFAULT 0,
                short_sleep TINYINT(1) NOT NULL DEFAULT 0,
                high_stress TINYINT(1) NOT NULL DEFAULT 0,
                unexplained_fever TINYINT(1) NOT NULL DEFAULT 0,
                rapid_weight_loss TINYINT(1) NOT NULL DEFAULT 0,
                persistent_swelling TINYINT(1) NOT NULL DEFAULT 0,
                vision_changes TINYINT(1) NOT NULL DEFAULT 0,
                blood_in_stool_urine TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_assessment_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS appointment_bookings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                booking_reference VARCHAR(40) NOT NULL UNIQUE,
                ack_token VARCHAR(64) NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(100) NOT NULL,
                concern TEXT NOT NULL,
                preferred_date DATE NULL,
                preferred_time VARCHAR(50) NULL,
                amount_inr DECIMAL(10,2) NOT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT \'INR\',
                payment_status VARCHAR(40) NOT NULL DEFAULT \'pending_payment\',
                upi_transaction_ref VARCHAR(150) NULL,
                payer_name VARCHAR(255) NULL,
                payer_upi_id VARCHAR(255) NULL,
                payment_channel VARCHAR(40) NULL,
                payment_acknowledged_at DATETIME NULL,
                payment_verified_at DATETIME NULL,
                verification_note TEXT NULL,
                source_ip VARCHAR(64) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_appointments_created_at (created_at),
                INDEX idx_appointments_status (payment_status),
                INDEX idx_appointments_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS appointment_payment_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                appointment_id INT UNSIGNED NOT NULL,
                gateway VARCHAR(30) NOT NULL DEFAULT \'razorpay\',
                gateway_order_id VARCHAR(100) NOT NULL UNIQUE,
                amount_subunits BIGINT UNSIGNED NOT NULL,
                currency VARCHAR(10) NOT NULL,
                status VARCHAR(40) NOT NULL DEFAULT \'created\',
                gateway_created_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_payment_orders_appointment
                    FOREIGN KEY (appointment_id) REFERENCES appointment_bookings(id) ON DELETE CASCADE,
                INDEX idx_payment_orders_appointment (appointment_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS appointment_payment_transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                payment_order_id BIGINT UNSIGNED NOT NULL,
                gateway_payment_id VARCHAR(100) NOT NULL UNIQUE,
                amount_subunits BIGINT UNSIGNED NOT NULL,
                currency VARCHAR(10) NOT NULL,
                status VARCHAR(40) NOT NULL,
                method VARCHAR(40) NULL,
                checkout_signature_verified TINYINT(1) NOT NULL DEFAULT 0,
                record_source VARCHAR(30) NOT NULL,
                amount_refunded_subunits BIGINT UNSIGNED NOT NULL DEFAULT 0,
                refund_status VARCHAR(20) NULL,
                error_code VARCHAR(100) NULL,
                error_description TEXT NULL,
                error_source VARCHAR(100) NULL,
                error_step VARCHAR(100) NULL,
                error_reason VARCHAR(100) NULL,
                gateway_created_at DATETIME NULL,
                paid_at DATETIME NULL,
                failed_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_payment_transactions_order
                    FOREIGN KEY (payment_order_id) REFERENCES appointment_payment_orders(id) ON DELETE CASCADE,
                INDEX idx_payment_transactions_order (payment_order_id),
                INDEX idx_payment_transactions_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE IF NOT EXISTS razorpay_webhook_events (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                event_id VARCHAR(255) NOT NULL UNIQUE,
                event_type VARCHAR(100) NOT NULL,
                gateway_order_id VARCHAR(100) NULL,
                gateway_payment_id VARCHAR(100) NULL,
                payload_sha256 VARCHAR(64) NOT NULL,
                processing_status VARCHAR(20) NOT NULL DEFAULT \'processing\',
                last_error TEXT NULL,
                processed_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        ];
    }

    /**
     * @return list<string>
     */
    private static function sqliteSchemaStatements(): array
    {
        return [
            'CREATE TABLE IF NOT EXISTS contact_submissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                message TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE TABLE IF NOT EXISTS blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                image_url TEXT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE TABLE IF NOT EXISTS health_assessments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                score INTEGER NOT NULL,
                interpretation TEXT NOT NULL,
                diagnosed_autoimmune INTEGER NOT NULL DEFAULT 0,
                symptoms_after_puberty INTEGER NOT NULL DEFAULT 0,
                symptoms_post_pregnancy INTEGER NOT NULL DEFAULT 0,
                symptoms_after_miscarriage INTEGER NOT NULL DEFAULT 0,
                symptoms_during_menopause INTEGER NOT NULL DEFAULT 0,
                family_history_autoimmune INTEGER NOT NULL DEFAULT 0,
                symptoms_worse_menstrual_cycle INTEGER NOT NULL DEFAULT 0,
                irregular_cycles INTEGER NOT NULL DEFAULT 0,
                painful_cycles INTEGER NOT NULL DEFAULT 0,
                severe_pms INTEGER NOT NULL DEFAULT 0,
                heavy_bleeding INTEGER NOT NULL DEFAULT 0,
                missed_periods INTEGER NOT NULL DEFAULT 0,
                pcos_or_endometriosis INTEGER NOT NULL DEFAULT 0,
                infertility_history INTEGER NOT NULL DEFAULT 0,
                unexplained_weight_changes INTEGER NOT NULL DEFAULT 0,
                temperature_intolerance INTEGER NOT NULL DEFAULT 0,
                hair_loss INTEGER NOT NULL DEFAULT 0,
                low_libido INTEGER NOT NULL DEFAULT 0,
                mood_cycle_changes INTEGER NOT NULL DEFAULT 0,
                thyroid_history INTEGER NOT NULL DEFAULT 0,
                chronic_fatigue INTEGER NOT NULL DEFAULT 0,
                unrefreshed_sleep INTEGER NOT NULL DEFAULT 0,
                energy_crashes INTEGER NOT NULL DEFAULT 0,
                sleep_difficulty INTEGER NOT NULL DEFAULT 0,
                joint_pain INTEGER NOT NULL DEFAULT 0,
                morning_stiffness INTEGER NOT NULL DEFAULT 0,
                migratory_pain INTEGER NOT NULL DEFAULT 0,
                flare_remission INTEGER NOT NULL DEFAULT 0,
                bloating INTEGER NOT NULL DEFAULT 0,
                bowel_issues INTEGER NOT NULL DEFAULT 0,
                food_sensitivity INTEGER NOT NULL DEFAULT 0,
                gut_disease_history INTEGER NOT NULL DEFAULT 0,
                frequent_antibiotics INTEGER NOT NULL DEFAULT 0,
                post_infection_worsening INTEGER NOT NULL DEFAULT 0,
                dry_skin INTEGER NOT NULL DEFAULT 0,
                rashes INTEGER NOT NULL DEFAULT 0,
                eczema INTEGER NOT NULL DEFAULT 0,
                brittle_nails INTEGER NOT NULL DEFAULT 0,
                brain_fog INTEGER NOT NULL DEFAULT 0,
                anxiety_depression INTEGER NOT NULL DEFAULT 0,
                tingling_numbness INTEGER NOT NULL DEFAULT 0,
                headaches INTEGER NOT NULL DEFAULT 0,
                chronic_stress INTEGER NOT NULL DEFAULT 0,
                major_trauma INTEGER NOT NULL DEFAULT 0,
                short_sleep INTEGER NOT NULL DEFAULT 0,
                high_stress INTEGER NOT NULL DEFAULT 0,
                unexplained_fever INTEGER NOT NULL DEFAULT 0,
                rapid_weight_loss INTEGER NOT NULL DEFAULT 0,
                persistent_swelling INTEGER NOT NULL DEFAULT 0,
                vision_changes INTEGER NOT NULL DEFAULT 0,
                blood_in_stool_urine INTEGER NOT NULL DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE TABLE IF NOT EXISTS appointment_bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_reference TEXT NOT NULL UNIQUE,
                ack_token TEXT NOT NULL,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                concern TEXT NOT NULL,
                preferred_date TEXT NULL,
                preferred_time TEXT NULL,
                amount_inr NUMERIC NOT NULL,
                currency TEXT NOT NULL DEFAULT \'INR\',
                payment_status TEXT NOT NULL DEFAULT \'pending_payment\',
                upi_transaction_ref TEXT NULL,
                payer_name TEXT NULL,
                payer_upi_id TEXT NULL,
                payment_channel TEXT NULL,
                payment_acknowledged_at TEXT NULL,
                payment_verified_at TEXT NULL,
                verification_note TEXT NULL,
                source_ip TEXT NULL,
                user_agent TEXT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE TABLE IF NOT EXISTS appointment_payment_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                appointment_id INTEGER NOT NULL,
                gateway TEXT NOT NULL DEFAULT \'razorpay\',
                gateway_order_id TEXT NOT NULL UNIQUE,
                amount_subunits INTEGER NOT NULL,
                currency TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT \'created\',
                gateway_created_at TEXT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (appointment_id) REFERENCES appointment_bookings(id) ON DELETE CASCADE
            )',
            'CREATE TABLE IF NOT EXISTS appointment_payment_transactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                payment_order_id INTEGER NOT NULL,
                gateway_payment_id TEXT NOT NULL UNIQUE,
                amount_subunits INTEGER NOT NULL,
                currency TEXT NOT NULL,
                status TEXT NOT NULL,
                method TEXT NULL,
                checkout_signature_verified INTEGER NOT NULL DEFAULT 0,
                record_source TEXT NOT NULL,
                amount_refunded_subunits INTEGER NOT NULL DEFAULT 0,
                refund_status TEXT NULL,
                error_code TEXT NULL,
                error_description TEXT NULL,
                error_source TEXT NULL,
                error_step TEXT NULL,
                error_reason TEXT NULL,
                gateway_created_at TEXT NULL,
                paid_at TEXT NULL,
                failed_at TEXT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (payment_order_id) REFERENCES appointment_payment_orders(id) ON DELETE CASCADE
            )',
            'CREATE TABLE IF NOT EXISTS razorpay_webhook_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                event_id TEXT NOT NULL UNIQUE,
                event_type TEXT NOT NULL,
                gateway_order_id TEXT NULL,
                gateway_payment_id TEXT NULL,
                payload_sha256 TEXT NOT NULL,
                processing_status TEXT NOT NULL DEFAULT \'processing\',
                last_error TEXT NULL,
                processed_at TEXT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contact_submissions(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_blog_created_at ON blog_posts(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_assessment_created_at ON health_assessments(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_created_at ON appointment_bookings(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_status ON appointment_bookings(payment_status)',
            'CREATE INDEX IF NOT EXISTS idx_appointments_email ON appointment_bookings(email)',
            'CREATE INDEX IF NOT EXISTS idx_payment_orders_appointment ON appointment_payment_orders(appointment_id)',
            'CREATE INDEX IF NOT EXISTS idx_payment_transactions_order ON appointment_payment_transactions(payment_order_id)',
            'CREATE INDEX IF NOT EXISTS idx_payment_transactions_status ON appointment_payment_transactions(status)',
        ];
    }

    private static function normalizeDatabaseUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            throw new PDOException('DATABASE_URL is empty.');
        }

        if (str_starts_with($url, 'postgres://')) {
            $url = 'postgresql://' . substr($url, strlen('postgres://'));
        }

        if (str_starts_with($url, 'mariadb://')) {
            $url = 'mysql://' . substr($url, strlen('mariadb://'));
        }

        if (str_starts_with($url, 'sqlite://') || str_starts_with($url, 'sqlite:')) {
            return $url;
        }

        if (str_starts_with($url, 'https://')) {
            throw new PDOException("DATABASE_URL must start with 'postgresql://', 'mysql://', or 'sqlite://'.");
        }

        if (str_starts_with($url, 'postgresql://') && !str_contains($url, 'sslmode=')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'sslmode=require';
        }

        if (str_starts_with($url, 'mysql://') && !str_contains($url, 'charset=')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'charset=utf8mb4';
        }

        return $url;
    }

    /**
     * @return array{scheme:string,host:string,port:int,dbname:string,user:string,pass:string,sslmode:string,charset:string,path:string}
     */
    private static function parseDatabaseUrl(string $url): array
    {
        if (str_starts_with($url, 'sqlite://') || str_starts_with($url, 'sqlite:')) {
            $path = str_starts_with($url, 'sqlite://')
                ? substr($url, strlen('sqlite://'))
                : substr($url, strlen('sqlite:'));

            if ($path === '') {
                throw new PDOException('SQLite DATABASE_URL path is empty.');
            }

            $path = str_replace('\\', '/', $path);
            if ($path !== ':memory:' && !preg_match('/^([A-Za-z]:)?\//', $path)) {
                $path = BASE_PATH . '/' . ltrim($path, '/');
            }

            if ($path !== ':memory:') {
                $directory = dirname($path);
                if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                    throw new PDOException("Could not create SQLite database directory '{$directory}'.");
                }
            }

            return [
                'scheme' => 'sqlite',
                'host' => '',
                'port' => 0,
                'dbname' => '',
                'user' => '',
                'pass' => '',
                'sslmode' => '',
                'charset' => '',
                'path' => $path,
            ];
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'], $parts['host'], $parts['path'], $parts['user'])) {
            throw new PDOException('Invalid DATABASE_URL format.');
        }

        parse_str($parts['query'] ?? '', $queryParams);
        $scheme = strtolower((string) $parts['scheme']);
        $driver = self::canonicalDriver($scheme);

        return [
            'scheme' => $driver,
            'host' => (string) $parts['host'],
            'port' => isset($parts['port'])
                ? (int) $parts['port']
                : ($driver === 'mysql' ? 3306 : 5432),
            'dbname' => ltrim((string) $parts['path'], '/'),
            'user' => urldecode((string) $parts['user']),
            'pass' => urldecode((string) ($parts['pass'] ?? '')),
            'sslmode' => (string) ($queryParams['sslmode'] ?? 'require'),
            'charset' => (string) ($queryParams['charset'] ?? 'utf8mb4'),
            'path' => '',
        ];
    }

    private static function canonicalDriver(string $scheme): string
    {
        $scheme = strtolower($scheme);

        if (in_array($scheme, ['postgresql', 'pgsql'], true)) {
            return 'pgsql';
        }

        if (in_array($scheme, ['mysql'], true)) {
            return 'mysql';
        }

        if (in_array($scheme, ['sqlite', 'sqlite3'], true)) {
            return 'sqlite';
        }

        throw new PDOException("Unsupported DATABASE_URL scheme '{$scheme}'. Use postgresql://, mysql://, or sqlite://");
    }
}

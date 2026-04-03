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
        $statements = self::driver() === 'mysql'
            ? self::mysqlSchemaStatements()
            : self::postgresSchemaStatements();

        foreach ($statements as $sql) {
            $pdo->exec($sql);
        }

        self::$schemaInitialized = true;
    }

    private static function buildDsn(array $parsed): string
    {
        $driver = self::canonicalDriver($parsed['scheme']);

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
            'CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contact_submissions(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_blog_created_at ON blog_posts(created_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_assessment_created_at ON health_assessments(created_at DESC)',
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

        if (str_starts_with($url, 'https://')) {
            throw new PDOException("DATABASE_URL must start with 'postgresql://' or 'mysql://'.");
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
     * @return array{scheme:string,host:string,port:int,dbname:string,user:string,pass:string,sslmode:string,charset:string}
     */
    private static function parseDatabaseUrl(string $url): array
    {
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

        throw new PDOException("Unsupported DATABASE_URL scheme '{$scheme}'. Use postgresql:// or mysql://");
    }
}

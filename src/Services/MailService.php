<?php
declare(strict_types=1);

namespace FCW\Services;

use FCW\Core\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

final class MailService
{
    public static function sendContactNotification(string $name, string $email, string $phone, string $message): bool
    {
        $subject = sprintf('New Contact Submission from %s', $name);

        $body = sprintf(
            '<h2>New Contact Request</h2><p><strong>Name:</strong> %s</p><p><strong>Email:</strong> %s</p><p><strong>Phone:</strong> %s</p><br><p><strong>Message:</strong></p><p>%s</p>',
            e($name),
            e($email),
            e($phone !== '' ? $phone : 'Not provided'),
            nl2br(e($message))
        );

        return self::sendEmail($subject, $body);
    }

    public static function sendAssessmentNotification(array $assessment): bool
    {
        $subject = sprintf(
            'New Health Assessment: %s (Score: %d)',
            (string) ($assessment['full_name'] ?? 'Unknown'),
            (int) ($assessment['score'] ?? 0)
        );

        $body = [
            '<h2>New Health Assessment Submission</h2>',
            '<p><strong>Name:</strong> ' . e((string) ($assessment['full_name'] ?? '')) . '</p>',
            '<p><strong>Email:</strong> ' . e((string) ($assessment['email'] ?? '')) . '</p>',
            '<p><strong>Phone:</strong> ' . e((string) ($assessment['phone'] ?? '')) . '</p>',
            '<hr>',
            '<h3>Result</h3>',
            '<p><strong>Score:</strong> ' . (int) ($assessment['score'] ?? 0) . '</p>',
            '<p><strong>Interpretation:</strong> ' . e((string) ($assessment['interpretation'] ?? '')) . '</p>',
            '<hr>',
            '<h3>Key Indicators (Yes Answers)</h3>',
            '<ul>',
        ];

        foreach (AssessmentCatalog::booleanFields() as $fieldName) {
            if (!empty($assessment[$fieldName])) {
                $label = AssessmentCatalog::fieldLabels()[$fieldName] ?? $fieldName;
                $body[] = '<li>' . e($label) . '</li>';
            }
        }

        $body[] = '</ul>';
        $body[] = '<p>Login to Admin Dashboard to view full details.</p>';

        return self::sendEmail($subject, implode('', $body));
    }

    private static function sendEmail(string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('PHPMailer is not installed. Run composer install.');
            return false;
        }

        $smtp = Config::smtp();
        if ($smtp['sender_email'] === '' || $smtp['sender_password'] === '') {
            error_log('SMTP credentials are missing.');
            return false;
        }

        $attempts = 3;

        for ($i = 1; $i <= $attempts; $i++) {
            try {
                $mailer = new PHPMailer(true);
                $mailer->isSMTP();
                $mailer->Host = (string) $smtp['host'];
                $mailer->Port = (int) $smtp['port'];
                $mailer->SMTPAuth = true;
                $mailer->Username = (string) $smtp['sender_email'];
                $mailer->Password = (string) $smtp['sender_password'];
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mailer->CharSet = 'UTF-8';

                $mailer->setFrom((string) $smtp['sender_email'], Config::appName());
                $mailer->addAddress((string) $smtp['admin_email']);
                $mailer->Subject = $subject;
                $mailer->isHTML(true);
                $mailer->Body = $htmlBody;

                $mailer->send();
                return true;
            } catch (Throwable $exception) {
                error_log(sprintf('Email send failed (attempt %d/%d): %s', $i, $attempts, $exception->getMessage()));
                if ($i < $attempts) {
                    sleep(2);
                }
            }
        }

        return false;
    }
}

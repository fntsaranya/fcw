<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;

final class WebinarRepository
{
    /**
     * Get the active webinar details. Seeds default content if table is empty.
     *
     * @return array<string, mixed>
     */
    public function getActive(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM webinar_details WHERE is_active = 1 ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row !== false && is_array($row)) {
            $envFee = (float) (\FCW\Core\Config::enquiryPayment()['fee_inr'] ?? 0.00);
            if ((float) ($row['fee_inr'] ?? 0) <= 0 && $envFee > 0) {
                $row['fee_inr'] = $envFee;
            }
            return $row;
        }

        // Seed default webinar details if none exist
        return $this->seedDefault();
    }

    /**
     * Save or update the active webinar record.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function saveOrUpdateActive(array $data): array
    {
        $pdo = Database::connection();
        $existing = $pdo->query('SELECT id FROM webinar_details WHERE is_active = 1 ORDER BY id DESC LIMIT 1')->fetch();

        $title = trim((string) ($data['title'] ?? ''));
        $subtitle = trim((string) ($data['subtitle'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $eventDate = trim((string) ($data['event_date'] ?? ''));
        $eventTime = trim((string) ($data['event_time'] ?? ''));
        $venuePlatform = trim((string) ($data['venue_platform'] ?? 'Live Online'));
        $speaker = trim((string) ($data['speaker'] ?? 'Saranya Mohan, Functional Clinical Nutritionist'));
        $envFee = (float) (\FCW\Core\Config::enquiryPayment()['fee_inr'] ?? 0.00);
        $feeInr = isset($data['fee_inr']) && (string) $data['fee_inr'] !== '' ? (float) $data['fee_inr'] : $envFee;
        $whatsappGroupLink = trim((string) ($data['whatsapp_group_link'] ?? ''));
        $imageUrl = trim((string) ($data['image_url'] ?? ''));

        if ($existing !== false && !empty($existing['id'])) {
            $id = (int) $existing['id'];
            $sql = 'UPDATE webinar_details SET
                        title = :title,
                        subtitle = :subtitle,
                        description = :description,
                        event_date = :event_date,
                        event_time = :event_time,
                        venue_platform = :venue_platform,
                        speaker = :speaker,
                        fee_inr = :fee_inr,
                        whatsapp_group_link = :whatsapp_group_link,
                        image_url = :image_url,
                        updated_at = ' . (Database::driver() === 'pgsql' ? 'NOW()' : (Database::driver() === 'mysql' ? 'NOW()' : 'CURRENT_TIMESTAMP')) . '
                    WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':subtitle' => $subtitle !== '' ? $subtitle : null,
                ':description' => $description,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime,
                ':venue_platform' => $venuePlatform,
                ':speaker' => $speaker,
                ':fee_inr' => $feeInr,
                ':whatsapp_group_link' => $whatsappGroupLink !== '' ? $whatsappGroupLink : null,
                ':image_url' => $imageUrl !== '' ? $imageUrl : null,
                ':id' => $id,
            ]);

            return $this->getActive();
        }

        $sql = 'INSERT INTO webinar_details (
                    title, subtitle, description, event_date, event_time, venue_platform,
                    speaker, fee_inr, whatsapp_group_link, image_url, is_active
                ) VALUES (
                    :title, :subtitle, :description, :event_date, :event_time, :venue_platform,
                    :speaker, :fee_inr, :whatsapp_group_link, :image_url, 1
                )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle !== '' ? $subtitle : null,
            ':description' => $description,
            ':event_date' => $eventDate,
            ':event_time' => $eventTime,
            ':venue_platform' => $venuePlatform,
            ':speaker' => $speaker,
            ':fee_inr' => $feeInr,
            ':whatsapp_group_link' => $whatsappGroupLink !== '' ? $whatsappGroupLink : null,
            ':image_url' => $imageUrl !== '' ? $imageUrl : null,
        ]);

        return $this->getActive();
    }

    /**
     * @return array<string, mixed>
     */
    private function seedDefault(): array
    {
        $pdo = Database::connection();
        $title = 'Is Your Thyroid Condition Autoimmune?';
        $subtitle = '🦋 LIVE WEBINAR';
        $description = "Join our 3-hour live interactive webinar to learn how nutrition, gut health, inflammation, stress, and lifestyle habits can influence thyroid health. You'll gain practical, evidence-informed strategies to better understand and support your thyroid.";
        $eventDate = 'Sunday, 16th August';
        $eventTime = '10:00 AM – 1:00 PM';
        $venuePlatform = 'Live Online';
        $speaker = 'Saranya Mohan, Functional Clinical Nutritionist';
        $envFee = (float) (\FCW\Core\Config::enquiryPayment()['fee_inr'] ?? 0.00);
        $feeInr = $envFee > 0 ? $envFee : 0.00;

        $sql = 'INSERT INTO webinar_details (
                    title, subtitle, description, event_date, event_time, venue_platform,
                    speaker, fee_inr, whatsapp_group_link, image_url, is_active
                ) VALUES (
                    :title, :subtitle, :description, :event_date, :event_time, :venue_platform,
                    :speaker, :fee_inr, NULL, NULL, 1
                )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle,
            ':description' => $description,
            ':event_date' => $eventDate,
            ':event_time' => $eventTime,
            ':venue_platform' => $venuePlatform,
            ':speaker' => $speaker,
            ':fee_inr' => $feeInr,
        ]);

        $stmt = $pdo->prepare('SELECT * FROM webinar_details WHERE is_active = 1 ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();

        return is_array($row) ? $row : [
            'id' => 1,
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'event_date' => $eventDate,
            'event_time' => $eventTime,
            'venue_platform' => $venuePlatform,
            'speaker' => $speaker,
            'fee_inr' => $feeInr,
            'whatsapp_group_link' => null,
            'image_url' => null,
            'is_active' => 1,
        ];
    }
}

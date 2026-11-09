<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;
use Throwable;

final class AppointmentAvailabilityRepository
{
    public const DEFAULT_SLOTS = [
        "10:00 AM - 10:30 AM",
        "10:30 AM - 11:00 AM",
        "11:00 AM - 11:30 AM",
        "11:30 AM - 12:00 PM",
        "12:00 PM - 12:30 PM",
        "12:30 PM - 01:00 PM",
        "01:00 PM - 01:30 PM",
        "01:30 PM - 02:00 PM",
        "02:00 PM - 02:30 PM",
        "02:30 PM - 03:00 PM",
        "03:00 PM - 03:30 PM",
        "03:30 PM - 04:00 PM",
        "04:00 PM - 04:30 PM",
        "04:30 PM - 05:00 PM",
        "05:00 PM - 05:30 PM",
        "05:30 PM - 06:00 PM",
    ];

    private function pdo(): PDO
    {
        return Database::connection();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allUpcoming(): array
    {
        try {
            $sql = "SELECT * FROM appointment_availability_overrides 
                    WHERE override_date >= CURRENT_DATE 
                    ORDER BY override_date ASC, 
                             (CASE WHEN slot_time IS NULL OR slot_time = '' OR slot_time = 'ALL' THEN 0 ELSE 1 END), 
                             slot_time ASC";
            $stmt = $this->pdo()->query($sql);
            return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
        } catch (Throwable $e) {
            error_log('AppointmentAvailabilityRepository allUpcoming error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return array{disabled_dates: list<string>, disabled_slots: array<string, list<string>>}
     */
    public function getDisabledForBooking(): array
    {
        $overrides = $this->allUpcoming();
        $disabledDates = [];
        $disabledSlots = [];

        foreach ($overrides as $row) {
            $date = (string) ($row['override_date'] ?? '');
            if ($date === '') {
                continue;
            }

            $slot = $row['slot_time'] !== null ? trim((string) $row['slot_time']) : '';
            if ($slot === '' || strtoupper($slot) === 'ALL') {
                if (!in_array($date, $disabledDates, true)) {
                    $disabledDates[] = $date;
                }
            } else {
                if (!isset($disabledSlots[$date])) {
                    $disabledSlots[$date] = [];
                }
                if (!in_array($slot, $disabledSlots[$date], true)) {
                    $disabledSlots[$date][] = $slot;
                }
            }
        }

        return [
            'disabled_dates' => $disabledDates,
            'disabled_slots' => $disabledSlots,
        ];
    }

    public function addOverride(string $date, ?string $slotTime = null, ?string $reason = null): bool
    {
        $date = trim($date);
        if ($date === '') {
            return false;
        }

        $normalizedSlot = $slotTime !== null ? trim($slotTime) : null;
        if ($normalizedSlot === '' || strtoupper((string) $normalizedSlot) === 'ALL') {
            $normalizedSlot = null;
        }

        $normalizedReason = $reason !== null ? trim($reason) : null;
        if ($normalizedReason === '') {
            $normalizedReason = null;
        }

        try {
            // Check if duplicate already exists
            if ($normalizedSlot === null) {
                $checkSql = "SELECT COUNT(*) FROM appointment_availability_overrides 
                             WHERE override_date = :date AND (slot_time IS NULL OR slot_time = '' OR slot_time = 'ALL')";
                $checkStmt = $this->pdo()->prepare($checkSql);
                $checkStmt->execute([':date' => $date]);
            } else {
                $checkSql = "SELECT COUNT(*) FROM appointment_availability_overrides 
                             WHERE override_date = :date AND slot_time = :slot_time";
                $checkStmt = $this->pdo()->prepare($checkSql);
                $checkStmt->execute([':date' => $date, ':slot_time' => $normalizedSlot]);
            }

            if ((int) $checkStmt->fetchColumn() > 0) {
                return true; // Already exists
            }

            $sql = "INSERT INTO appointment_availability_overrides (override_date, slot_time, reason) 
                    VALUES (:date, :slot_time, :reason)";
            $stmt = $this->pdo()->prepare($sql);
            return $stmt->execute([
                ':date' => $date,
                ':slot_time' => $normalizedSlot,
                ':reason' => $normalizedReason,
            ]);
        } catch (Throwable $e) {
            error_log('AppointmentAvailabilityRepository addOverride error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteOverride(int $id): bool
    {
        try {
            $stmt = $this->pdo()->prepare("DELETE FROM appointment_availability_overrides WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Throwable $e) {
            error_log('AppointmentAvailabilityRepository deleteOverride error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param array<string, list<string>> $bookedSlots
     */
    public function isSlotAvailable(string $date, ?string $slotTime, array $bookedSlots = []): bool
    {
        $disabledData = $this->getDisabledForBooking();
        $disabledDates = $disabledData['disabled_dates'];
        $disabledSlots = $disabledData['disabled_slots'];

        if (in_array($date, $disabledDates, true)) {
            return false;
        }

        if ($slotTime !== null && $slotTime !== '') {
            $slotsOnDate = $disabledSlots[$date] ?? [];
            if (in_array($slotTime, $slotsOnDate, true)) {
                return false;
            }

            $alreadyBooked = $bookedSlots[$date] ?? [];
            if (in_array($slotTime, $alreadyBooked, true)) {
                return false;
            }
        }

        return true;
    }
}

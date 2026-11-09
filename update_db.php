<?php
require 'config/bootstrap.php';

try {
    \FCW\Core\Database::ensureSchema();

    $webinarRepo = new \FCW\Repositories\WebinarRepository();
    $activeWebinar = $webinarRepo->getActive();

    $pdo = \FCW\Core\Database::connection();

    // Ensure older databases have the intake columns if not already present
    try {
        $pdo->exec("
            ALTER TABLE patient_intake_forms 
            ADD COLUMN hip_circumference VARCHAR(50) DEFAULT NULL AFTER bmi,
            ADD COLUMN waist_circumference VARCHAR(50) DEFAULT NULL AFTER hip_circumference
        ");
    } catch (\Throwable $e) {
        // Ignore if already exists
    }

    // Ensure older databases have the is_active column on webinar_details
    try {
        $pdo->exec("
            ALTER TABLE webinar_details 
            ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1
        ");
    } catch (\Throwable $e) {
        // Ignore if already exists
    }

    echo "<h3>Database updated successfully!</h3>";
    echo "<p>All webinar, enquiry, payment, intake, and appointment availability tables have been checked and synchronized.</p>";
    echo "<p><strong>Active Webinar:</strong> " . htmlspecialchars((string)($activeWebinar['title'] ?? '')) . "</p>";
    echo "<p><a href='/admin/contacts?tab=appointments'>Go to Admin Appointments Manager</a> | <a href='/contact'>Go to Appointment Booking</a></p>";
} catch (\Throwable $e) {
    echo "<h3>Database Update Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

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

    echo "<h3>Database updated successfully!</h3>";
    echo "<p>All webinar, enquiry, payment, and intake tables have been checked and synchronized.</p>";
    echo "<p><strong>Active Webinar:</strong> " . htmlspecialchars((string)($activeWebinar['title'] ?? '')) . "</p>";
    echo "<p><a href='/admin/contacts'>Go to Admin Data Manager</a> | <a href='/enquiry'>Go to Enquiry Page</a></p>";
} catch (\Throwable $e) {
    echo "<h3>Database Update Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

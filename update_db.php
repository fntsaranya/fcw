<?php
require 'config/bootstrap.php';

try {
    $pdo = \FCW\Core\Database::connection();
    $pdo->exec("
        ALTER TABLE patient_intake_forms 
        ADD COLUMN hip_circumference VARCHAR(50) DEFAULT NULL AFTER bmi,
        ADD COLUMN waist_circumference VARCHAR(50) DEFAULT NULL AFTER hip_circumference
    ");
    echo "Database updated successfully! The missing columns have been added.";
} catch (\Throwable $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Database is already up to date! (Columns exist)";
    } else {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

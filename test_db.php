<?php
require 'config/bootstrap.php';
try {
    $repo = new \FCW\Repositories\PatientIntakeRepository();
    $repo->upsert(19, 'Test', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, true, null, null, null, null, null, null, null, null, null, null, null, null, null);
    echo "Success\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

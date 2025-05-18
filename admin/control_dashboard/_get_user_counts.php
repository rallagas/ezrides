<?php
require_once '../../_db.php';
require_once '../../_sql_utility.php';

header('Content-Type: application/json');

try {
    $riders = query("SELECT COUNT(*) as total FROM `user_profile` WHERE rider_plate_no IS NOT NULL");
    $customers = query("SELECT COUNT(*) as total FROM `user_profile` WHERE rider_plate_no IS NULL");

    echo json_encode([
        'riders' => intval($riders[0]['total']),
        'customers' => intval($customers[0]['total'])
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

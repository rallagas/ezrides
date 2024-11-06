<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

// Check if the user is logged in and user_id is set
if (isset($_SESSION['user_id'])) {
    $rider_logged = $_SESSION['user_id'];

    // Delete rider from angkas_rider_queue table
    $unqueue = delete_data(CONN, "angkas_rider_queue", "angkas_rider_id = {$rider_logged} ");
    
    // Check if the deletion was successful
    if ($unqueue) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No rows deleted']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
}

<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

$bookings = query("SELECT * FROM view_angkas_bookings WHERE customer_user_id = ? LIMIT 20", [USER_LOGGED]);

if (!empty($bookings)) {
    // Prepare JSON response
    $response = [
        'hasBooking' => true,
        'bookings' => $bookings
    ];
} else {
    $response = [
        'hasBooking' => false,
        'message' => 'You currently have no Bookings.'
    ];
}

echo json_encode($response);
?>

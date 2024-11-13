<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
$user_logged = $_SESSION['user_id'];    
// Get JSON input from request body
$data = json_decode(file_get_contents("php://input"), true);

// Ensure data is received correctly
if (isset($data['booking_id'])) {
    $bookingStatus = $data['booking_status'];
    $bookingId = $data['booking_id'];

    // Prepare query to update booking status
    $update = update_data("angkas_bookings",array("booking_status" => $bookingStatus), array("angkas_booking_reference" => $bookingId));
    
    // Check if the update was successful
    if($update) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed or no rows affected']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}
?>

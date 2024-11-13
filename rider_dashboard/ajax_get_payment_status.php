<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['booking_id'])) {
    $bookingId = $data['booking_id'];

    // Define the WHERE clause as a string for the select_data function
    $where = "angkas_booking_reference = '$bookingId'";

    // Use select_data function to retrieve the payment status of the booking
    $result = select_data("angkas_bookings", $where);

    if ($result && count($result) > 0) {
        $paymentStatus = $result[0]['payment_status'];
        echo json_encode(['success' => true, 'payment_status' => $paymentStatus]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Booking not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}
?>

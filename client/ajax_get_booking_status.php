<?php
require_once '../_db.php';
require_once '_class_Bookings.php'; // Replace with the actual path to your class file


    $status = isCONN(CONN);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'fetch_booking_status') {
    $bookingId = $_POST['bookingId'];
    $columns = ['booking_status_text','payment_status_text'];
    try {
        $angkasBookings = new AngkasBookings();
        $result = $angkasBookings->getColumnData($columns, USER_LOGGED, $bookingId);

        if (!empty($result)) {
            // Return the booking status as a JSON response
            echo json_encode(['booking' => $result[0] , "Status" => $bookingId]);
        } else {
            echo json_encode(['error' => 'No booking found', 'status' => $bookingId]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage() ,'status' => $bookingId]);
    }
}

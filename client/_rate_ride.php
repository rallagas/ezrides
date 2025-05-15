<?php ob_clean(); 
header('Content-Type: application/json');
require_once('../_db.php');
require_once('../_sql_utility.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingref = $_POST['bookingrefnumber'] ?? null;
    $rating = $_POST['rideRating'] ?? null;

    if ($bookingref && $rating) {
        $update = update_data('angkas_bookings', ['rating' => $rating], ['angkas_booking_reference' => $bookingref]);
       
        if ($update) {
            echo json_encode([
                'success' => true,
                'rating' => $rating,
                'bookingref' => $bookingref
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update rating.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid data.'
        ]);
    }
}

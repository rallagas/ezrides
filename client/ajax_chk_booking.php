<?php
require_once '../_db.php';
require_once '_class_Bookings.php'; // Include the class

// Check if the request is an AJAX request and if it contains the necessary parameter
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Get the user_id from the GET request
    $userId = USER_LOGGED;

    try {
        // Create an instance of the AngkasBookings class
        $angkasBooking = new AngkasBookings();

        // Fetch the booking header details using the userId
        $bookingDetails = $angkasBooking->getBookingHeaderDetails($userId);

        // If no results were found, send a message indicating so
        if (empty($bookingDetails)) {
            echo json_encode([
                'status' => 'error',
                'bookingStatus' => false,
                'message' => 'No booking details found for this user.'
            ]);
        } else {
            // Send the result as JSON
            echo json_encode([
                'status' => 'success',
                'bookingStatus' => true,
                'bookingDetails' => $bookingDetails[0]
            ]);
        }
    } catch (Exception $e) {
        // Catch any exceptions and return a JSON error message
        echo json_encode([
            'status' => 'error',
            'bookingStatus' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    // If the request is not valid, send an error message
    echo json_encode([
        'status' => 'error',
        'bookingStatus' => false,
        'message' => 'Invalid request.'
    ]);
}
?>

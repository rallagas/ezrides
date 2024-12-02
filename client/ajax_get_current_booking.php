<?php
include_once "../_db.php";
include_once "_class_Bookings.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'hasBooking' => false,
        'message' => 'User not logged in.'
    ]);
    exit;
}

$user_logged = $_SESSION['user_id'];

try {
    // Instantiate the AngkasBookings class
    $angkasBookings = new AngkasBookings();

    // Specify the columns we need from the booking table
    $columns = [
        'angkas_booking_reference', 'form_from_dest_name', 'form_to_dest_name',
        'form_Est_Cost', 'date_booked', 'booking_status', 'payment_status_text',
        'rider_firstname', 'rider_lastname', 'booking_status_text'
    ];

    // Retrieve booking data
    $current_booking = $angkasBookings->getColumnData($columns, USER_LOGGED);

    // Check if there is an active booking
    if (!empty($current_booking)) {
        echo json_encode([
            'hasBooking' => true,
            'booking' => $current_booking[0]
        ]);
    } else {
        echo json_encode([
            'hasBooking' => false,
            'message' => 'You currently have no booked rider.'
        ]);
    }
} catch (Exception $e) {
    // Handle exceptions and return an error message
    echo json_encode([
        'hasBooking' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

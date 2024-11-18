<?php
include_once "../_db.php";  // Database connection file
include_once "_class_Bookings.php";
// Assuming the AngkasBookings class is included or autoloaded


if (isset($_POST['booking_reference']) && isset($_POST['rating'])) {
    $angkasBookings = new AngkasBookings();
    
    $bookingReference = $_POST['booking_reference'];
    $rating = $_POST['rating'];

    // Validate inputs
    if (empty($bookingReference) || !in_array($rating, [1, 2, 3, 4, 5])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    // Update the rating in the database using the updateBookingColumns method
    try {
        $data = [
            'rating' => $rating
        ];
        $success = $angkasBookings->updateBookingColumns($bookingReference, $data);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Rating updated']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update rating']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}
else {
    
    echo json_encode(['success' => false, 'message' => 'No Rating Posted']);
}

?>

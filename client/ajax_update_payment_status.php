<?php
// Include the file that contains the AngkasBookings class
require_once '../_db.php';
require_once '_class_Bookings.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingReference = $_POST['bookingReference'] ?? null;
    $newStatus = $_POST['newStatus'] ?? null;

    if ($bookingReference && $newStatus) {
        $angkasBookings = new AngkasBookings();
        
        try {
            $success = $angkasBookings->updatePaymentStatus($bookingReference, $newStatus);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Payment status updated successfully.' : 'Failed to update payment status.'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input data.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}

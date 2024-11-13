<?php 
include_once "../_db.php";
include_once "../_sql_utility.php";

$rider_logged = $_SESSION['user_id'];

// Initialize variables
$current_booking = array();
$queue_list = [];
$current_queue = 0; // Initialize to zero or fetch appropriately
$status = ''; // Initialize status, determine how you want to set it

// Get the list of bookings needed by the rider
$current_booking = query( "SELECT ab.angkas_booking_id
                                  , ab.angkas_booking_reference
                                  , ab.user_id AS customer_user_id
                                  , ab.angkas_rider_user_id
                                  , ab.form_from_dest_name
                                  , ab.user_currentLoc_lat
                                  , ab.user_currentLoc_long
                                  , ab.form_to_dest_name
                                  , ab.formToDest_long
                                  , ab.formToDest_lat
                                  , ab.form_ETA_duration
                                  , ab.form_TotalDistance
                                  , ab.form_Est_Cost
                                  , ab.date_booked
                                  , ab.booking_status
                                  , up.user_firstname
                                  , up.user_lastname
                                  , up.user_mi
                                  , up.user_gender
                                  , up.user_contact_no
                                  , up.user_email_address      
                                  , up.user_profile_image        
                               FROM angkas_bookings AS ab
                               JOIN user_profile AS up ON ab.user_id = up.user_id
                               JOIN users u ON up.user_id = u.user_id
                               WHERE u.t_status = 'A'
                               AND ab.booking_status not in ('C','D')
                               AND ab.angkas_rider_user_id = ?",
                               [$rider_logged]);

if (empty($current_booking)) {
    
     $status = 'No bookings available';
} else {
    foreach ($current_booking as $cx) {
        $queue_list[] = [
            "angkas_booking_id" => $cx['angkas_booking_id'],
            "angkas_booking_reference" => $cx['angkas_booking_reference'],
            "customer_user_id" => $cx['customer_user_id'],
            "angkas_rider_user_id" => $cx['angkas_rider_user_id'],
            "form_from_dest_name" => $cx['form_from_dest_name'],
            "user_currentLoc_lat" => $cx['user_currentLoc_lat'],
            "user_currentLoc_long" => $cx['user_currentLoc_long'],
            "form_to_dest_name" => $cx['form_to_dest_name'],
            "formToDest_long" => $cx['formToDest_long'],
            "formToDest_lat" => $cx['formToDest_lat'],
            "form_ETA_duration" => $cx['form_ETA_duration'],
            "form_TotalDistance" => $cx['form_TotalDistance'],
            "form_Est_Cost" => $cx['form_Est_Cost'],
            "date_booked" => $cx['date_booked'],
            "booking_status" => $cx['booking_status'],
            "user_firstname" => $cx['user_firstname'],
            "user_lastname" => $cx['user_lastname'],
            "user_mi" => $cx['user_mi'],
            "user_gender" => $cx['user_gender'],
            "user_contact_no" => $cx['user_contact_no'],
            "user_email_address" => $cx['user_email_address'],
            "user_profile_image" => $cx['user_profile_image']
        ];
    }
    $status = 'Available';
}

// Set current queue and status based on your logic
$current_queue = count($queue_list); // For example, set current_queue as the number of bookings
//$status = !empty($queue_list) ? 'Available' : 'No bookings available'; // Example status

// Build the final JSON array
$response = [
    "current_queue" => $current_queue,
    "queue_list" => $queue_list,
    "status" => $status,
    "endl" => "yes"
];

// Encode the response to JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

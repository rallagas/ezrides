<?php 
include_once "../_db.php";
include_once "../_sql_utility.php";

//$rider_logged = $_SESSION['user_id'];
$rider_logged = USER_LOGGED;

$current_queue = 0;
$queue_list = [];

// Check if the rider is in the queue
$check_queue_if_im_in = select_data("angkas_rider_queue", "angkas_rider_id = {$rider_logged} AND DATE(queue_date) = CURRENT_DATE"
);

$check_queue_if_im_in_transit = select_data("angkas_rider_queue", 
    "angkas_rider_id = {$rider_logged} AND DATE(queue_date) = CURRENT_DATE AND queue_status = 'I'"
);


if (empty($check_queue_if_im_in) && empty($check_queue_if_im_in_transit)) {
    // Not in the queue, add to the queue
    $tbl = "angkas_rider_queue";
    $dta = ["angkas_rider_id" => $rider_logged];
    insert_data( $tbl, $dta);
    $current_queue = 1; // Assuming new entries start at zero
    $status = "queued";
}
else if (!empty($check_queue_if_im_in_transit)) {
    // Not in the queue, add to the queue
    $status = "in transit";
}

else {
    foreach ($check_queue_if_im_in as $q) {
        $que_id = $q['angkas_rider_queue_id'];
        $qarr = [$que_id];
        $check_queue_num = query( "SELECT COUNT(*) AS queue_num 
                                          FROM angkas_rider_queue 
                                          WHERE DATE(queue_date) = CURRENT_DATE 
                                            AND angkas_rider_queue_id <= ?
                                            AND queue_status = 'A'", 
                                  $qarr);

        if ($check_queue_num !== false && is_array($check_queue_num)) {
            foreach ($check_queue_num as $que_num) {
                $current_queue = $que_num['queue_num'];
            }
        }
    }
    $status = "Already Queued";
}

if ($current_queue) {
    // Get the list of bookings needed by the rider
    $list_of_need_rider =
            query( "SELECT  ab.angkas_booking_id
                              ,  ab.angkas_booking_reference
                              ,  ab.user_id as customer_user_id
                              ,  ab.angkas_rider_user_id
                              ,  ab.form_from_dest_name
                              ,  ab.user_currentLoc_lat
                              ,  ab.user_currentLoc_long
                              ,  ab.form_to_dest_name
                              ,  ab.formToDest_long
                              ,  ab.formToDest_lat
                              ,  ab.form_ETA_duration
                              ,  ab.form_TotalDistance
                              ,  ab.form_Est_Cost
                              ,  ab.date_booked
                              ,  ab.booking_status
                              ,  up.user_firstname
                              ,  up.user_lastname
                              ,  up.user_mi
                              ,  up.user_gender
                              ,  up.user_contact_no
                              ,  up.user_email_address      
                              ,  up.user_profile_image        
                           FROM `angkas_bookings` as ab
                           JOIN `user_profile` as up
                             ON ab.user_id = up.user_id
                           JOIN `users` u
                             ON up.user_id = u.user_id
                          WHERE t_status = 'A'
                            AND t_user_type = 'C'
                            AND ab.user_id NOT in (?)
                            AND ab.angkas_rider_user_id is NULL
            ", [$rider_logged]);
//        select_data( "angkas_bookings", 
//        "angkas_rider_user_id IS NULL AND DATE(date_booked) = CURRENT_DATE AND booking_status = 'P'", 
//        "angkas_booking_id");

    if (empty($list_of_need_rider)) {
        $queue_list = "empty";
    } else {
        foreach ($list_of_need_rider as $cx) {
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
    }
}

// Build the final JSON array
$response = [
    "current_queue" => $current_queue,
    "queue_list" => $queue_list,
    "status" => $status,
    "endl" => "yes"
];

// Encode the response to JSON
$json = json_encode($response);

// Output the JSON
header('Content-Type: application/json');
echo $json;
?>

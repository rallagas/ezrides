<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

$user_logged = $_SESSION['user_id'];
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
                                  , ab.payment_status
                                  , CASE
                                       WHEN ab.payment_status = 'P' THEN 'Pending Payment'
                                       WHEN ab.payment_status = 'D' THEN 'Payment Declined'
                                       WHEN ab.payment_status = 'C' THEN 'Paid'
                                    END payment_status_text
                                  , up.user_firstname
                                  , up.user_lastname
                                  , up.user_mi
                                  , up.user_gender
                                  , up.user_contact_no
                                  , up.user_email_address      
                                  , up.user_profile_image  
                                  , rp.user_firstname AS rider_firstname
                                  , rp.user_lastname AS rider_lastname
                                  , CASE 
                                        WHEN ab.booking_status = 'P' THEN 'Waiting for Driver'
                                        WHEN ab.booking_status = 'A' THEN 'Driver Found'
                                        WHEN ab.booking_status = 'R' THEN 'Driver Arrived in Your Location'
                                        WHEN ab.booking_status = 'I' THEN 'In Transit'
                                        WHEN ab.booking_status = 'C' THEN 'Completed'
                                        WHEN ab.booking_status = 'F' THEN 'Pending Payment'
                                    END AS booking_status_text
                               FROM angkas_bookings AS ab
                               JOIN user_profile AS up 
                                 ON ab.user_id = up.user_id
                               JOIN users u 
                                 ON up.user_id = u.user_id
                               LEFT JOIN user_profile AS rp 
                                 ON ab.angkas_rider_user_id = rp.user_id
                               WHERE ab.user_id = ?
                               AND DATE(date_booked) = CURRENT_DATE
                               AND ab.booking_status <> 'D'
                               ORDER by ab.angkas_booking_id DESC
                               LIMIT 1",
                         [$user_logged]);

if (!empty($current_booking)) {
    // Prepare JSON response
    $response = [
        'hasBooking' => true,
        'booking' => $current_booking[0]
    ];
} else {
    $response = [
        'hasBooking' => false,
        'message' => 'You currently have no booked rider.'
    ];
}

echo json_encode($response);
?>

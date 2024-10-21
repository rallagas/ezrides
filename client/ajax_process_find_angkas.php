<?php
include_once "../_db.php";
include_once "../_sql_utility.php";

if(isset($_POST['form_from_dest'])){
    $from_loc_name=mysqli_real_escape_string(CONN, $_POST['form_from_dest']);
    $from_loc_lat=mysqli_real_escape_string(CONN, $_POST['currentLoc_lat']);
    $from_loc_long=mysqli_real_escape_string(CONN, $_POST['currentLoc_long']);
    $to_loc_name=mysqli_real_escape_string(CONN, $_POST['form_to_dest']);
    $to_loc_lat=mysqli_real_escape_string(CONN, $_POST['formToDest_long']);
    $to_loc_long=mysqli_real_escape_string(CONN, $_POST['formToDest_lat']);
    $eta_mins=mysqli_real_escape_string(CONN, $_POST['form_ETA_duration']);
    $total_dis=mysqli_real_escape_string(CONN, $_POST['form_TotalDistance']);
    $total_cost=mysqli_real_escape_string(CONN, $_POST['form_Est_Cost']);
    $user_logged = $_SESSION['user_id'];
    $ref_num=gen_book_ref_num(8,"ANG");
    
    
    $check_data=select_data(CONN, "angkas_bookings","user_id = {$user_logged} AND DATE(date_booked) = CURRENT_DATE AND booking_status = 'P'");
    
    if(!empty($check_data)){
        echo "There is still a pending booking. see details below:";
        foreach($check_data as $booking){
            echo $booking['angkas_booking_reference'];
        }
    }
    else{
        $table = "angkas_bookings";
        $data = array(
                  "angkas_booking_reference" => $ref_num
                , "user_id" => $user_logged
                , "form_from_dest_name" => $from_loc_name
                , "user_currentLoc_lat" => $from_loc_lat
                , "user_currentLoc_long" => $from_loc_long
                , "form_to_dest_name" => $to_loc_name
                , "formToDest_long" => $to_loc_long
                , "formToDest_lat" => $to_loc_lat
                , "form_ETA_duration" => $eta_mins
                , "form_TotalDistance" => $total_dis
                , "form_Est_Cost" => $total_cost
                );
        
        insert_data(CONN, $table, $data);
        echo 0;
        //echo "Waiting for Rider.";
    }
}
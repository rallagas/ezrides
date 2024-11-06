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
        echo "There is still a pending booking. see details below:"; ?>

<?php foreach($check_data as $booking){ ?>

<div class="btn-group mb-2">
    <a data-bs-toggle="collapse" href="#BookingInfo" role="button" aria-expanded="false" aria-controls="BookingInfo" class="btn btn-primary btn-angkas-booking-ref">
        <?php echo $booking['angkas_booking_reference']; ?>
    </a>
    <a onclick="confirm('do you want to cancel this booking?')" href="?cancelBooking=<?php echo $booking['angkas_booking_reference']; ?>" class='btn btn-danger'>
        <svg xmlns='http://www.w3.org/2000/svg' width='20' height='23' fill='currentColor' class='bi bi-x-circle' viewBox='0 0 16 16'>
            <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16' />
            <path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708' />
        </svg>
    </a>
</div>
<div class="collapse" id="BookingInfo">
    <?php
    foreach($check_data as $cd){
        echo "You booked this ";
    }
    ?>
</div>



<?php } ?>


<?php
    }
    else{
        
        preg_match('/\d+/', $eta_mins, $matches);
        $eta_in_minutes = (float) $matches[0];
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
                , "form_ETA_duration" => $eta_in_minutes
                , "form_TotalDistance" => $total_dis
                , "form_Est_Cost" => $total_cost
                );
        
        insert_data(CONN, $table, $data);
        echo 0;
        //echo "Waiting for Rider.";
    }
}
<?php include_once "../_db.php";
      include_once "../_sql_utility.php";

$rider_logged=$_SESSION['user_id'];
$booking_ref=$_POST['booking_ref'];

$ud = array(
    'angkas_rider_user_id' => $rider_logged
    ,'booking_status' => 'A'
);

$where = array(
    'angkas_booking_reference' => $booking_ref
);

$ab = update_data(CONN, 'angkas_bookings', $ud, $where);

if($ab){
    $ud = array(
    'queue_status' => 'A'
    );

    $where = array(
        'DATE(queue_date)' => date('Y-m-d'),
        'angkas_rider_id' => $rider_logged
    );


    $aq = update_data(CONN, 'angkas_rider_queue', $ud, $where );
    if($aq){
        echo 0;
    }

}
else{
    echo 1;
}

?>



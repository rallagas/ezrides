<?php include_once "../_db.php";
      include_once "../_sql_utility.php";

$rider_logged=$_SESSION['user_id'];

query("DELETE FROM angkas_bookings WHERE date_booked < (NOW() - INTERVAL 1 HOUR) and angkas_rider_user_id is NULL and booking_status = 'P'");
//query("UPDATE angkas_bookings SET booking_status='D' WHERE date_booked < (NOW() - INTERVAL 5 MINUTE) and user_id = ? AND booking_status = 'C'", [USER_LOGGED]);


//$myBooking = select_data( "angkas_bookings", "angkas_rider_user_id = {$rider_logged}");
//if(!empty($myBooking)){
//    header("location: _current_booking_map.php");
//}
?>


<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Document</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
     <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
   <div class="container-fluid px-0">
      <div class="row">
              <div class="col-12">
                  <?php include "nav_rider.php";?>
              </div>
      </div>

       <div class="row px-1" id="availableBookings"></div>

   </div>
    
    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

   <script src="../js/jquery-3.5.1.min.js"></script>
   <script src="rider_process.js"></script>
   <script src="../_multipurpose_ajax.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry&loading=async"></script>

   
</html>
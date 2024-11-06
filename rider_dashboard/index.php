<?php include_once "../_db.php";
      include_once "../_sql_utility.php";

$rider_logged=$_SESSION['user_id'];

//$myBooking = select_data(CONN, "angkas_bookings", "angkas_rider_user_id = {$rider_logged}");
//if(!empty($myBooking)){
//    header("location: _current_booking_map.php");
//}
?>


<html>
<head>
    <meta charset="UTF-8">
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
              <div class="col-12">
                  <h6 class="display-6 fw-bold ms-4">Rider's Dashboard</h6>
                  <h3 class="fs-6 fw-bold ms-5">Welcome <?php $arr= getUserInfo($rider_logged); foreach($arr as $a){ echo $a['user_firstname'] . ", " . $a['user_lastname'];}?></h3>
                  <span id="queueStatus" class="ms-4 badge text-bg-info text-dark"></span>
              </div>
      </div>
       <div class="row px-5" id="availableBookings"></div>
<!--
       <div class="row px-5">
           
          <div id="currentBookingMap" style="height: 500px; width: 100%;" class="col-12 mx-5"></div>

           
       </div>
-->
   </div>
    
    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

   <script src="../js/jquery-3.5.1.min.js"></script>
   <script src="rider_process.js"></script>
   <script src="../_multipurpose_ajax.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry&loading=async"></script>

   
</html>
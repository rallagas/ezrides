<?php include_once "../_db.php";
      include_once "../_sql_utility.php";

$rider_logged=$_SESSION['user_id'];

if(isset($_GET['cancelBooking'])){
    $booking_ref = htmlentities($_GET['cancelBooking']);
    $data = array("angkas_rider_user_id" => NULL);
    $where = array("angkas_booking_reference" =>$booking_ref );
    update_data(CONN, "angkas_bookings",$data, $where);
    ?>
    <div class="alert alert-danger">Booking Cancelled</div>
    <?php 
}

 
    $myBooking = query(CONN, "SELECT ab.angkas_booking_id
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
                                  , ab.form_Est_Cost AmountToPay
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
                               WHERE ab.angkas_rider_user_id = ?
                                AND booking_status <> 'C'",
                               [$rider_logged]);

if(!empty($myBooking)){
  foreach($myBooking as $cb){
      $cusName = $cb['user_firstname'] . " " . $cb['user_lastname'] . " " . $cb['user_mi'];
      $cusContact = $cb['user_contact_no'];
      $cusEmail = $cb['user_email_address'];
      $cusProfile = $cb['user_profile_image'];
      $cusGender = $cb['user_gender'];
      $angkas_book_ref = $cb['angkas_booking_reference'];
      
      $user_from_loc_name = $cb['form_from_dest_name'];
      $user_from_loc_lat = $cb['user_currentLoc_lat'];
      $user_from_loc_long = $cb['user_currentLoc_long'];
      $user_lat_long = $user_from_loc_lat . "," . $user_from_loc_long;
      
      $user_to_loc_name = $cb['form_to_dest_name'];
      $user_to_loc_lat = $cb['formToDest_lat'];
      $user_to_loc_long = $cb['formToDest_long'];
      $user_to_loc_coor = $user_to_loc_long . "," . $user_to_loc_lat;
      
      
      $AmountToPay = $cb['AmountToPay'];
      $user_current_loc_name = $cb['form_from_dest_name'];
  }
}
else{
    header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="_map.css">
</head>
<body>
   
   <div class="container-fluid">
       <div class="row">
          
     <input type="hidden"  id="AmountToPay" value="<?php echo $AmountToPay; ?>">
     <input type="hidden"  id="angkas_booking_ref" value="<?php echo $angkas_book_ref; ?>">
     <input type="hidden" id="form_to_dest" value="<?php echo $user_lat_long; ?>">
      <input type="hidden" id="form_customer_to_dest" value="<?php echo $user_to_loc_coor; ?>">
          
            <div class="col-sm-8 col-lg-8">
                      <div id="customerInfo" class="offcanvas offcanvas-end" tabindex="-1" id="customerInfo" aria-labelledby="customerInfo">
                          <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasExampleLabel"><?php echo $cusName; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                          </div>
                          <div class="offcanvas-body">
                            <div>
                              <table class="table-responsive table-bordered table">
                                  <tr>
                                      <td>Contact No</td>  <td><?php echo $cusContact;?></td> </tr>
                                    <tr>
                                        <td>Email Address</td>  <td><?php echo $cusEmail;?></td>
                                        </tr>
                                        <tr>
                                      <td>Gender</td>  <td><?php echo $cusGender;?></td>
                                      </tr>
                              </table>
                            </div>
<!--
                            <div class="dropdown mt-3">
                              <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Dropdown button
                              </button>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                              </ul>
                            </div>
-->
                          </div>
                          
                      </div>
           </div>
          
          <div class="col-12">
                <div class="card mb-1">
                    <div class="card-header">
                     <h5 class="fw-bold">
                         <?php echo $angkas_book_ref; ?>
                         <button id="viewCusProfile" class="float-end btn btn-sm btn-outline-secondary"
                                 type="button" data-bs-toggle="offcanvas" data-bs-target="#customerInfo" aria-controls="customerInfo">
                             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                         </button>
                     </h5>
                      <small class="fs-6 card-title"> 
                           <span class="fw-bold">From: </span> 
                                <?php echo $user_from_loc_name;?>
                             <span class="fw-bold"> To: </span> 
                                <?php echo $user_to_loc_name;?>
                      </small>
                    </div>
                    <div class="card-body p-0">

                      <div class="input-group border-0 rounded-0 m-0 p-0 route-info">
                            <label class="input-group-text rounded-0">ETA:</label>
                            <input type="text" class="form-control" id="form_ETA_duration" readonly>

                            <label class="input-group-text">Total Distance (km):</label>
                            <input type="text" class="form-control rounded-0" id="form_TotalDistance" readonly>
                     </div>
                    </div>
                    <div class="card-footer">
                        
                <button class="btn btn-sm btn-outline-primary float-start me-3" id="ConfirmArrivalButton">Arrived</button>
                <button class="btn btn-sm btn-outline-success me-3"  id="DropOffCustomer">Drop Off</button>
                 <a href="?cancelBooking=<?php echo $angkas_book_ref;?>" class="btn btn-sm btn-outline-danger" onclick="confirm('Do you really want to cancel this booking?')" id="DropOffCustomer">Cancel</a>
    
                    </div>
                </div>
          </div>
           <div class="col-12">
               
                <div id="map" style="width: 100%; height: 550px;">
                   <div class="container-fluid text-center align-middle">
                   <span class="m-5"> Loading Map ... </span>
                     <div class="spinner-grow" role="status"></div>
                     <div class="spinner-grow" role="status"></div>
                     <div class="spinner-grow" role="status"></div>
                    </div>
                </div>
           </div>
           
         
        
           
       </div>
   </div>



    <script src="_map_config.js"></script>
    <script src="_map_func.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry,marker&callback=initMap&loading=async"></script>

</body>
</html>
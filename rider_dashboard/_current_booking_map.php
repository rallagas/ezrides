<?php include_once "../_db.php";

if(!isset($_SESSION['user_id'])){
  header("location: index.php");
}
else {
  $rider_logged = USER_LOGGED;
}



  

if(isset($_GET['cancelBooking'])){
    $booking_ref = htmlentities($_GET['cancelBooking']);
    $data = array("angkas_rider_user_id" => NULL, );
    $where = array("angkas_booking_reference" => $booking_ref );

    update_data( "angkas_bookings",$data, $where);

    ?>
    <div class="alert alert-danger">Booking Cancelled</div>
    <?php 
    header("location: index.php");
}

 
    $myBooking = query( "SELECT ab.angkas_booking_id
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
                                  , ab.payment_status
                                  , up.user_firstname
                                  , up.user_lastname
                                  , up.user_mi
                                  , up.user_gender
                                  , up.user_contact_no
                                  , up.user_email_address      
                                  , up.user_profile_image      
                                  , sum(uw.wallet_txn_amt)  AmountToClaim
                               FROM angkas_bookings AS ab
                               JOIN user_profile AS up ON ab.user_id = up.user_id
                               JOIN users u ON up.user_id = u.user_id
                               LEFT JOIN user_wallet uw
                                 on (uw.reference_number = ab.angkas_booking_reference
                                   OR uw.reference_number = ab.shop_order_reference_number
                                   )
                                   AND uw.payment_type = 'R'
                                   AND ((uw.user_id is null or uw.user_id = ?)
                                   AND (uw.payTo is null or uw.payTo = ?))

                               WHERE ab.angkas_rider_user_id = ?
                                AND booking_status not in ('C','D')
                              GROUP BY ab.angkas_booking_id
                                  , ab.angkas_booking_reference
                                  , ab.user_id 
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
                                  , up.user_firstname
                                  , up.user_lastname
                                  , up.user_mi
                                  , up.user_gender
                                  , up.user_contact_no
                                  , up.user_email_address      
                                  , up.user_profile_image",
                               [$rider_logged, $rider_logged, $rider_logged]);

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
      $bookingStatus = $cb['booking_status'];
      $user_current_loc_name = $cb['form_from_dest_name'];
      $paymentStatus = $cb['payment_status'];
      $paymentStatusText = null;

      $AmountToClaim = $cb['AmountToClaim'];

      $pstat = ['P' => 'Pending',
                'D' => 'Declined',
                'C' => 'Paid Completed'
      ];

      foreach($pstat as $stat => $val){
        if ($stat == $paymentStatus){
          $paymentStatusText = $stat;
        }
      }
  }
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
                          </div>
                      </div>
           </div>
          
          <div class="col-12">
                <div class="card mb-1">
                    <div class="card-header">
                     <h5 class="fw-bold">
                         <?php echo $angkas_book_ref . "<span class='badge text-bg-success'>(+Php ".$AmountToClaim.")</span>"; ?>
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
              <?php switch($bookingStatus){ 
                       case 'A': ?>
                          <button class="btn btn-sm btn-outline-primary float-start me-3" id="ConfirmArrivalButton">Arrived</button>
                          <a href="?cancelBooking=<?php echo $angkas_book_ref;?>" class="btn btn-sm btn-outline-danger" onclick="confirm('Do you really want to cancel this booking?')" id="CancelBooking">Cancel</a>
                          <button class="btn btn-sm btn-outline-success me-3 d-none"  id="DropOffCustomer">Drop Off</button>
                    <?php break;
                       case 'I': ?>
                      <button class="btn btn-sm btn-outline-success me-3"  id="DropOffCustomer">Drop Off</button>
                   <?php break; 
                       case 'R': ?>
                       <button class="btn btn-sm btn-outline-success me-3"  id="DropOffCustomer">Drop Off</button>
                    <?php break; 
                       default: ?> 
                       <a href="?cancelBooking=<?php echo $angkas_book_ref;?>" class="btn btn-sm btn-outline-danger" onclick="confirm('Do you really want to cancel this booking?')" id="CancelBooking">Cancel</a>  
                       <button class="btn btn-sm btn-outline-success me-3"  id="DropOffCustomer">Drop Off</button>
              <?php } ?>
                
                 
    
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
           <!-- Drop-Off Confirmation Modal -->
<div class="modal fade" id="dropOffModal" tabindex="-1" aria-labelledby="dropOffModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dropOffModalLabel">Drop-Off Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="dropOffMessage">Are you sure you want to drop off the customer?</p>
        <div id="paymentSection" style="display: none;">
          <p>Did the customer pay the amount: <span id="amountToPayText"></span>?</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDropOffBtn" class="btn btn-primary">Confirm Drop-Off</button>
        <button type="button" id="confirmPaymentBtn" class="btn btn-success" style="display: none;">Confirm Payment</button>
      </div>
    </div>
  </div>
</div>

           
         
        
           
       </div>
   </div>
</body>
  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="_map_config.js"></script>
    <script src="_map_func.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry,marker&callback=initMap&loading=async"></script>

</html>
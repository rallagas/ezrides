<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
    $user_logged = $_SESSION['user_id'];    
   // $current_booking=select_data(CONN, "angkas_bookings","user_id = {$user_logged} AND DATE(date_booked) = CURRENT_DATE");
    $current_booking=query(CONN,"SELECT ab.angkas_booking_id
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
                                  , rp.user_firstname rider_firstname
                                  , rp.user_lastname rider_lastname
                                  , case when ab.booking_status = 'P' THEN 'Waiting for Driver'
                                         when ab.booking_status = 'A' THEN 'Driver Found'
                                         when ab.booking_status = 'R' THEN 'Driver Arrived in Your Location'
                                         when ab.booking_status = 'I' THEN 'In Transit'
                                         when ab.booking_status = 'C' THEN 'Completed'
                                         when ab.booking_status = 'F' THEN 'Pending Payment'
                                    end as booking_status
                               FROM angkas_bookings AS ab
                               JOIN user_profile AS up ON ab.user_id = up.user_id
                               JOIN users u ON up.user_id = u.user_id
                               LEFT JOIN user_profile AS rp ON ab.angkas_rider_user_id = u.user_id
                               WHERE ab.user_id = ?
                               and ab.booking_status <> 'C'
                               AND DATE(date_booked) = CURRENT_DATE
                         ", [$user_logged]);
if(!empty($current_booking)){ 
      foreach($current_booking as $cb){
      extract($cb,EXTR_OVERWRITE);
      ?>
        <div class="container">
                <div class="row">
                <div class="col-12">
                 <span class="fw-bold">You have a current booking.</span>
                 <h6 class="fs-3 text-success"><?php echo $angkas_booking_reference;?></h6>
                 <span class="fw-bold fs-5">Origin: </span> <span class="fw-semibold fs-5"><?php echo $form_from_dest_name;?></span>
                 <br>
                 <span class="fw-bold fs-5">Destination: </span> <span class="fw-semibold fs-5"><?php echo $form_to_dest_name;?></span>
                 <br>
                 <span class="fw-bold fs-5">Status: </span> <span class="fw-semibold fs-5 text-danger"><?php echo $booking_status;?></span>
                 <br>
                 <?php if($booking_status != "Waiting for Driver") { ?>
                 <span class="fw-bold fs-5">Driver: </span> <span class="fw-semibold fs-5"><?php echo $rider_firstname . ", " . $rider_lastname ;?></span>
                 <?php } else { ?>
                          <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                          <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                          <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
          
                    <?php  } ?>
                 </div>
                 </div>
       </div>

<?php }

}
else{
    
    echo "You Currently have no booked rider.";
}
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
                                  , ab.shop_order_reference_number
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
      $cus_user_id = $cb['customer_user_id'];
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
      $shopOrderRef = $cb['shop_order_reference_number'];

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


  $shopList = query("SELECT DISTINCT si.item_name
                                                , si.price
                                                , so.quantity
                                                , so.amount_to_pay
                                                , si.item_img
                                                , ab.additionalnotes
                                                , ab.additionalfile
                                             FROM `angkas_bookings` ab
                                             JOIN `shop_orders` so
                                               ON so.shop_order_ref_num = ab.shop_order_reference_number
                                             JOIN `shop_items` si
                                               on so.item_id = si.item_id
                                             WHERE ab.angkas_booking_reference = ? and angkas_rider_user_id = ?"
                          ,[$angkas_book_ref, $rider_logged]);
  $hasShopList = (!empty($shopList));

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EZ Rides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="_map.css">
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <input type="hidden" id="AmountToPay" value="<?php echo $AmountToPay; ?>">
            <input type="hidden" id="angkas_booking_ref" value="<?php echo $angkas_book_ref; ?>">
            <input type="hidden" id="form_to_dest" value="<?php echo $user_lat_long; ?>">
            <input type="hidden" id="form_customer_to_dest" value="<?php echo $user_to_loc_coor; ?>">

            <div class="col-sm-8 col-lg-8">
                <div id="customerInfo" class="offcanvas offcanvas-end" tabindex="-1" id="customerInfo"
                    aria-labelledby="customerInfo">
                    <div class="offcanvas-header" style="background-color:indigo; color: #fff">
                        <h5 class="offcanvas-title" id="offcanvasExampleLabel"><?php echo $cusName; ?></h5>
<!--                        Customer Id for Chat as receipient-->
                        <span class="customer-id d-none" data-customer-userid="<?php echo $cus_user_id;?>"><?php echo $cus_user_id;?></span>
<!--                        Customer Id for Chat as receipient-->
                        <span class="rider-id  d-none" data-rider-userid="<?php echo USER_LOGGED;?>" ><?php echo USER_LOGGED;?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="mb-3">
                            <table class="table-responsive table-striped table">
                                <tr>
                                    <td>Contact No</td>
                                    <td><?php echo $cusContact;?></td>
                                </tr>
                                <tr>
                                    <td>Email Address</td>
                                    <td><?php echo $cusEmail;?></td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><?php echo $cusGender;?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="mb-3" id="conversation">
                                    <div class="modal-content overflow-y-scroll">
                                            <div class="modal-body overflow-y-scroll" id="conversation" style="height: 100%">
                                               <small class="text-center text-body-tertiary">Start a conversation.</small>
                                            </div>
                                   

                                    </div>
                          
                            
                        </div>
                        <div class="mb-3">
                               <form id="formChatCustomer">
                                            <div class="p-0 bg-secondary bg-opacity-50">
                                                   <div class="input-group">
                                                        <input type="hidden" id="rideruserid" name="receiver_id" value="<?php echo $cus_user_id;?>">
                                                       <input type="hidden"  id="senderuserid" name="sender_id" value="<?php echo USER_LOGGED;?>">
                                                       <input type="text" id="messagecontent" class="form-control border-bottom-1 border-top-0 border-start-0 border-end-0 border-dark" name="message">
                                                        <button type="submit" class="btn btn-light">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                                              <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                            </div>
                                        </form>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="col-12">
                <div class="card border-0 shadow mb-1">
                    <div class="card-header pt-2" style="background-color:indigo">
                        <h5 class="fw-bold card-title text-white mb-0">
                            <?php echo $angkas_book_ref . " <span class='badge text-bg-success'>+PHP ".$AmountToClaim."</span>"; 
                            if($hasShopList){?>
                            <a class="btn btn-warning btn-sm" data-bs-toggle="collapse" href="#showList" role="button"
                                aria-expanded="false" aria-controls="showList">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-list-check" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0" />
                                </svg>
                                SHOPPING LIST
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                                </svg>
                            </a>
                            <?php }
                            ?>



                            <button style="width:5vw" id="viewCusProfile" class="float-end btn btn-sm btn-warning open-chat-modal position-relative" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#customerInfo" aria-controls="customerInfo">
                               Chat <img src="../icons/messages.png"  class="img-responsive float-right" style="width: 90%" />
                               
                                <span class="position-absolute msgCounter top-0 start-100 translate-middle badge rounded-pill"></span>
                            </button>

                        </h5>
                    </div>
                    <div class="collapse card-body" id="showList">
                        <?php 
                        
                        if($hasShopList){ ?>
                        <table class="table table-responsive overflow-x-scroll m-0">
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>AMOUNT</th>
                                <th>IMG</th>
                            </tr>
                            <?php
                            $addedNotes=null;
                            $attachmentFile=null;
                            $shopCost=0.00;
                            foreach($shopList as $sl){
                                $addedNotes=$sl['additionalnotes'];
                                $attachmentFile=$sl['additionalfile'];
                                ?>
                            <tr class="py-1">
                                <td><?php echo $sl['item_name'];?></td>
                                <td><?php echo $sl['quantity'] . " pcs";?></td>
                                <td><?php echo $sl['amount_to_pay'];?></td>
                                <td><img src="../client/_shop/item-img/<?php echo $sl['item_img'];?>" alt=""
                                        class="img-fluid" width="50vw"></td>
                            </tr>
                            <?php $shopCost += $sl['amount_to_pay'];
                             } ?>
                             <tr>
                                <td colspan="2" class="text-end">TOTAL (Php)</td>
                                <td colspan="2" class="fw-bold"><?php echo number_format(($shopCost ?? 0.00),2);?></td>
                             </tr>
                            <tr>
                                <td class="fw-bold">NOTE:</td>
                                <td><?php echo $addedNotes; ?></td>
                                <td class="fw-bold">ATTACHMENT:</td>
                                <td>
                                    <?php if($attachmentFile != null){ ?>
                                        <a href="../client/_shop/<?php echo $attachmentFile;?>" class="btn btn-secondary"> Link </a> </td>
                                    <?php }
                                    else {
                                        echo "No File attached.";
                                    } ?>
                            </tr>
                        </table>
                        <?php } ?>


                    </div>
                    <div class="card-footer p-0">
                        <small class="fs-6 card-title">
                            <span class="fw-bold">FROM: </span>
                            <?php echo $user_from_loc_name;?>
                            <span class="fw-bold"> TO: </span>
                            <?php echo $user_to_loc_name;?>
                        </small>
                        <div class="input-group border-0 rounded-0 m-0 p-0 route-info">
                            <?php switch($bookingStatus){ 
                                case 'A': ?>
                            <button class="btn btn-outline-warning bg-yellow  shadow rounded-4" id="ConfirmArrivalButton"> <img src="../icons/taxi-arrival.png" alt="Arrived" style="width: 3vh">Arrived</button>
                            <button class="btn btn-secondary d-none shadow rounded-4" id="DropOffCustomer"> <img src="../icons/taxi-arrival.png" alt="" style="width: 3vh"> Drop Off</button>
                            <?php break;
                                case 'I': ?>
                            <button class="btn btn-secondary shadow rounded-4 " id="DropOffCustomer"> <img src="../icons/taxi-arrival.png" alt="" style="width: 3vh"> Drop Off</button>
                            <?php break; 
                                case 'R': ?>
                            <button class="btn btn-secondary shadow rounded-4" id="DropOffCustomer"> <img src="../icons/taxi-arrival.png" alt="" style="width: 3vh"> Drop Off</button>
                            <?php break; 
                                default: ?>

                            <button class="btn btn-secondary shadow rounded-4" id="DropOffCustomer"><img src="../icons/taxi-arrival.png" alt="" style="width: 3vh"> Drop Off</button>
                            <?php } ?>

                            <span class="input-group-text border-0 fw-bold">ETA:</span>
                            <input type="text" class="form-control  border-0" id="form_ETA_duration" readonly>

                            <span class="input-group-text  border-0 fw-bold">DISTANCE (km):</span>
                            <input type="text" class="form-control rounded-0  border-0" id="form_TotalDistance"
                                readonly>
                            <a href="?cancelBooking=<?php echo $angkas_book_ref;?>" class="btn btn-danger"
                                onclick="confirm('Do you really want to cancel this booking?')"
                                id="CancelBooking">Cancel</a>
                        </div>
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
            <div class="modal fade" id="dropOffModal" tabindex="-1" aria-labelledby="dropOffModalLabel"
                aria-hidden="true">
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
                            <button type="button" id="confirmDropOffBtn" class="btn btn-primary">Confirm
                                Drop-Off</button>
                            <button type="button" id="confirmPaymentBtn" class="btn btn-success"
                                style="display: none;">Confirm Payment</button>
                        </div>
                    </div>
                </div>
            </div>





        </div>
    </div>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="_map_config.js"></script>
<script src="_map_func.js"></script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&libraries=places,geometry,marker&callback=initMap&loading=async">
</script>
<script src="../js/jquery-3.5.1.min.js"></script>
<script src="chat.js"></script>

</html>
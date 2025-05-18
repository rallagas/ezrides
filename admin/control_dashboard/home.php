<body>
    <?php

include_once "../_class_userWallet.php";


$sql = null;
$sql = " SELECT SUM(x.wallet) as income from (
    SELECT case WHEN payment_type = 'A' then wallet_txn_amt *  -1 else wallet_txn_amt end as wallet
  FROM `user_wallet` WHERE wallet_action LIKE '%Admin' OR payment_type = 'A' ) as x ";
$sales_data = query($sql);
$sales = $sales_data[0]['income'] ?? 0;

$sql = "SELECT SUM(wallet_txn_amt) as total_wallet_pool, count(user_id) as userCount from `user_wallet` where payment_type = 'T' AND wallet_txn_status = 'C' ";
$wallet_pool_data = query($sql);
$wallet_pool = $wallet_pool_data[0]['total_wallet_pool'] ?? 0 ;
$userOwnerCount = $wallet_pool_data[0]['userCount'] ?? 0 ;

$activeRidersSQL = "SELECT COUNT(1) countActiveRiders from `angkas_rider_queue` WHERE DATE(queue_date) = CURRENT_DATE";
$activeRider = query($activeRidersSQL);
$ActiveRidersCount = $activeRider[0]['countActiveRiders'];

$sql_booking_total = "SELECT COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` in ('C','D') and `payment_status` in ('C') and DATE(date_booked) > CURRENT_DATE - 60";
$sql_booking_total_data = query($sql_booking_total);
$booking_total = $sql_booking_total_data[0]['total_number_of_bookings'] ?? 0;


$sql_activeBookings = "SELECT COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` NOT in ('C','D') and DATE(date_booked) = CURRENT_DATE ";
$activeBookingsData = query($sql_activeBookings);
$activeBookings = $activeBookingsData[0]['total_number_of_bookings'] ?? 0;

$sql_booking_trend = "SELECT DATE(date_booked) as date_booked, COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` in ('C','D') and `payment_status` in ('C') and DATE(date_booked) > CURRENT_DATE - 60 group by DATE(date_booked) order by  DATE(date_booked)";
$booking_per_day_data = query($sql_booking_trend);

$sql_top_up_approval = "SELECT uw.user_wallet_id
                             , up.user_firstname
                             , up.user_lastname
                             , up.user_mi
                             , up.user_contact_no
                             , uw.wallet_txn_amt
                             , uw.gcash_reference_number
                             , uw.gcash_account_number
                             , uw.gcash_account_name
                             , uw.gcash_attachment
                          FROM `user_profile` up
                          JOIN `user_wallet` uw
                            ON (up.user_id = uw.user_id)
                        WHERE uw.payment_type = 'T'
                          AND uw.wallet_txn_status = 'P'
                          ";
$pendingTopupList = query($sql_top_up_approval);    


?>

    <div class="container-fluid mt-4">
        <div class="row g-2">
            <div class="col-lg-3">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">INCOME <small class="small fs-6 fw-light text-success">PHP</small> </h6>
                        <h1 class="display-4"><?php echo number_format($sales,2);?></h1>
                        <sup class="text-secondary">
                            <?php echo ($wallet_pool > 0) ? number_format(($sales/$wallet_pool)*100,2) : 0.00 ;?>% OF
                            WALLET POOL </sup>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">WALLET POOL <small class="small fs-6 fw-light text-success">PHP</small></h6>
                        <h1 class="display-4"><?php echo number_format($wallet_pool,2);?></h1>
                        <sup class="text-secondary"> <?php echo $userOwnerCount; ?> WALLET USERS </sup>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">

                        <h6 class="card-title fw-bold">ACTIVE RIDERS <span class="text-info fw-light">TODAY</span></h6>
                        <h1 class="display-4"><?php echo $ActiveRidersCount;?></h1>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">ACTIVE BOOKINGS</h6>
                        <h1 class="display-4"><?php echo $activeBookings;?></h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card border-0 shadow">
                    <div class="card-header pb-0">
                        <h6 class="card-title fw-bold mb-0">BOOKING STAT PER DAY <span class="text-secondary">(60
                                DAYS)</span>
                            <br class="m-0 p-0">
                            <sup class="text-secondary mb-0">TOTAL OF <?php echo $booking_total;?></sup>
                        </h6>
                    </div>
                    <div class="card-body overflow-y-scroll" style="height:30vh;">

                        <table class="table table-sm table-responsive table-borderless">
                            <?php foreach($booking_per_day_data as $trend){ ?>
                            <tr>
                                <td style="width:15%" class="small">
                                    <?php echo date('M-d', strtotime($trend['date_booked'])); ?>
                                </td>

                                <td style="width:85%">
                                    <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: <?php echo (($booking_total > 0) ? ($trend['total_number_of_bookings'] / $booking_total) * 100 : 0) ;?>%">
                                        <?php echo $trend['total_number_of_bookings']; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

            </div>

            <div class="col-lg-8 col-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="card-title fw-bold">TOP UP APPROVALS</h6>
                    </div>

                    <div class="card-body overflow-y-scroll" style="height:30vh;">

                        <table class="table table-sm table-responsive table-borderless">
                            <tr>
                                <th></th>
                                <th class=" d-print-none d-lg-print">User</th>
                                <th class="d-print-none d-lg-print">Contact Info</th>
                                <th> Amount </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Number </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Sender </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Reference </th>

                            </tr>
                            <?php 
                            if(empty($pendingTopupList)){ ?>
                            <tr>
                                <td colspan="10" class="text-center">No Request yet.</td>
                            </tr>
                            <?php }
                            else {
                             foreach($pendingTopupList  as $tu){
                             extract($tu); ?>
                            <tr>
                                <td>
                                    <a href="#" class="img-preview btn btn-outline-secondary" data-imgsrc="../../_upload_gcash_receipts/<?php echo $gcash_attachment; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                                            <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                                        </svg>
                                    </a>

                                </td>
                                <td class="d-none d-md-block">
                                    <?php echo $user_lastname . ", " . $user_firstname . "," . $user_mi; ?>
                                </td>
                                <td class="d-none d-md-block"><?php echo $user_contact_no; ?></td>
                                <td><span class="d-none d-md-block d-lg-block small">PHP</span><?php echo number_format($wallet_txn_amt ?? 0.00 ,2); ?>
                                </td>
                                <td><?php echo $gcash_account_number; ?></td>
                                <td><?php echo $gcash_account_name; ?></td>
                                <td><?php echo $gcash_reference_number; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success btn-approve-decline" data-action-id='C' data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                            <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                        </svg>
                                    </a>

                                    <a class="btn btn-sm btn-outline-danger btn-approve-decline" data-action-id='D' data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                            <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856s-.036.586-.113.856c-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a10 10 0 0 1-.443-.05 9.36 9.36 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a9 9 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581s-.027-.414-.075-.581c-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.2 2.2 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.9.9 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1" />
                                        </svg>
                                    </a>
                                </td>

                            </tr>
                            <?php } 
                            }?>
                        </table>
                        <!-- Modal Structure -->
                        <div id="imagePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Image Preview</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="previewImage" src="" alt="Preview" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 collapse">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="card-title fw-bold" id="carrentallist">CAR RENTALS
                            <?php if(!isset($_GET['rentalHist'])){?>
                            <a href="?rentalHist" class="btn btn-link text-decoration-none small">HISTORICAL</a>
                            <?php } 
                            else{ ?>
                            <a href="index.php" class="btn btn-link text-decoration-none small">PENDING APPROVALS</a>
                            <?php } ?>

                            <a href="?CarRentalList" class="btn btn-link text-decoration-none small">CARS FOR RENT</a>
                            <a href="?regCarRental" class="btn btn-link text-decoration-none small">REGISTER CAR FOR
                                RENT +</a>
                            <?php
                                if(isset($_GET['regCarRental'])){ ?>
                            <div class="card">
                                <div class="card-header bg-purple">
                                    <span class="card-title text-light">CAR RENTAL REGISTRATION FORM</span>
                                </div>
                                <div class="card-body">
                                    <form id="newvehicle" action="" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <input type="text" name="ownername" placeholder="Owner Name" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="owneraddress" placeholder="Owner Address" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="carmodel" placeholder="Car Model" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="carcolor" placeholder="Car Color" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <select name="vehicleType" id="" class="form-select">
                                                <option>--Vehicle Type--</option>
                                                <option value="0005">SEDAN (4-5 Seater)</option>
                                                <option value="0006">SUV (6-7 Seater)</option>
                                                <option value="0002">MOTORCYCLE (2 Seater)</option>
                                            </select>

                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="platenumber" placeholder="Plate Number" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="rateperhr" placeholder="Rate Per Hour" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="rateperday" placeholder="Rate Per Day" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="rateperkm" placeholder="Rate Per KM" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <button class="btn btn-outline-secondary attach-file" type="button">
                                                Attach Photo of your Vehicle
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                                                    <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                                                </svg>
                                            </button>
                                            <input type="file" name="carphoto" class="form-file visually-hidden form-control">
                                        </div>

                                        <input type="submit" class="btn btn-primary border-dark border-2 bg-purple text-light">
                                    </form>
                                    <!-- end of form -->
                                </div>
                            </div>
                            <?php }
                            ?>
                        </h6>
                        <?php
                        if(isset($_GET['archiveCar']) && isset($_GET['btnaction'])){
                            $action=$_GET['btnaction'];
                            $vehicleid=$_GET['archiveCar'];
                            $sqlupdatevehicle = "UPDATE vehicle SET vehicle_txn_type = ? WHERE vehicle_id = ?";
                            $update = query($sqlupdatevehicle,[$action,$vehicleid]);
                            ?>
                        <div class="alert alert-success">
                            Vehicle Updated.
                        </div>
                        <?php 
                        }
                        ?>
                    </div>
                    <div class="card-body">

                        <?php

                        if(isset($_POST['vehicleid'])){
                            $vowner=$_POST['vehicleowner'];
                            $vaddress=$_POST['vehicleowneraddress'];
                            $vcolor=$_POST['vehiclecolor'];
                            $vratehr=$_POST['vehiclerateperhour'];
                            $vrateday=$_POST['vehiclerateperday'];
                            $vratekm=$_POST['vehiclerateperkm'];
                            $vid=$_POST['vehicleid'];
                        
                            $queryupdatevehicleinfo="UPDATE vehicle 
                                                        SET vehicle_owner_name = ?
                                                          , vehicle_owner_address = ?
                                                          , vehicle_color = ?
                                                          , vehicle_price_rate_per_hr = ?
                                                          , vehicle_price_rate_per_day = ?
                                                          , vehicle_price_rate_per_km = ?
                                                    WHERE vehicle_id = ?";
                            $updateVehicleInfo = query($queryupdatevehicleinfo,[$vowner,$vaddress,$vcolor,$vratehr,$vrateday,$vratekm,$vid]);
                            ?>
                        <div class="alert alert-success">Vehicle Info Updated.</div>
                        <?php
                        }
          
                        

                        if(isset($_GET['updateCar'])){
                            $sqlgetCarInfo = "SELECT * FROM vehicle WHERE vehicle_id = ?";
                            $getInfo = query($sqlgetCarInfo,[$_GET['updateCar']]);
                            foreach($getInfo as $c){
                            ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Update Car Info</h3>
                            </div>
                            <div class="card-body">
                                <form action="./" method="POST">
                                    <input type="text" hidden name="vehicleid" value="<?php echo $c['vehicle_id'];?>">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Owner</label>
                                        <input type="text" name="vehicleowner" class="form-control" value="<?php echo $c['vehicle_owner_name'];?>" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Owner Address</label>
                                        <input type="text" name="vehicleowneraddress" class="form-control" value="<?php echo $c['vehicle_owner_address'];?>" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Color</label>
                                        <input type="text" name="vehiclecolor" class="form-control" value="<?php echo $c['vehicle_color']?>">
                                    </div>

                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Rate Per Hour</label>
                                        <input type="text" name="vehiclerateperhour" class="form-control" value="<?php echo $c['vehicle_price_rate_per_hr']?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Rate Per Day</label>
                                        <input type="text" name="vehiclerateperday" class="form-control" value="<?php echo $c['vehicle_price_rate_per_day']?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="" class="form-label">Vehicle Rate Per KM</label>
                                        <input type="text" name="vehiclerateperkm" class="form-control" value="<?php echo $c['vehicle_price_rate_per_km']?>">
                                    </div>

                                    <input type="submit" class="btn btn-primary">
                                </form>
                            </div>
                        </div>
                        <?php }
                        }

                        if(isset($_GET['CarRentalList'])){
                            $sql_for_rent="SELECT * FROM `vehicle`";
                            $CarRentData=query($sql_for_rent); ?>
                        <table class="table table-responsive table-striped">
                            <thead>
                                <th>Vehicle Type</th>
                                <th>Plate No.</th>
                                <th colspan="2" class="text-center">Owner</th>
                                <th>Vehicle Model</th>
                                <th>Vehicle Color</th>
                                <th>Vehicle Rate per Hour</th>
                                <th>Vehicle Rate per Day</th>
                                <th>Vehicle Rate per KM</th>
                            </thead>
                            <?php
                                $vt = null;
                                foreach($CarRentData as $car){
                                        switch($car['vehicle_type']){
                                            case '0005': $vt = "Sedan";
                                            break;
                                            case '0007': $vt = "SUV";
                                            break;
                                            case '02': $vt = "Motorcycle";
                                            break;
                                            default: $vt = "Sedan";

                                        }            
                                    ?>
                            <tr>
                                <td><?php echo $vt;?></td>
                                <td><?php echo $car['vehicle_plate_no'];?></td>
                                <td><?php echo $car['vehicle_owner_name'];?></td>
                                <td><?php echo $car['vehicle_owner_address'];?></td>
                                <td><?php echo $car['vehicle_model'];?></td>
                                <td><?php echo $car['vehicle_color'];?></td>
                                <td><?php echo $car['vehicle_price_rate_per_hr'];?></td>
                                <td><?php echo $car['vehicle_price_rate_per_day'];?></td>
                                <td><?php echo $car['vehicle_price_rate_per_km'];?></td>
                                <td> <a href="?CarRentalList&archiveCar=<?php echo $car['vehicle_id'];?>&btnaction=<?php echo ($car['vehicle_txn_type'] == 2) ? 1:2; ?>#carrentallist" class="btn btn-link text-decoration-none"><?php echo ($car['vehicle_txn_type'] == 2) ? 'REACTIVATE':'ARCHIVE'; ?></a>
                                </td>
                                <td> <a href="?updateCar=<?php echo $car['vehicle_id'];?>" class="btn btn-link text-decoration-none">EDIT</a> </td>
                            </tr>
                            <?php } ?>

                        </table>

                        <!-- end of car rental list -->
                        <?php }


                        $sql_rental = "SELECT  a.app_txn_id,
                                            a.amount_to_pay,
                                            a.user_id as user,
                                            up.user_firstname,
                                            up.user_lastname,
                                            up.user_mi,
                                            up.user_contact_no,
                                            ii.item_description,
                                            ii.item_price,
                                            DATE_FORMAT(a.book_start_dte, '%m/%d/%Y') AS book_start_dte, -- Format start date
                                            DATE_FORMAT(a.book_end_dte, '%m/%d/%Y') AS book_end_dte,     -- Format end date
                                            DATEDIFF(a.book_end_dte, a.book_start_dte) AS elapseDay,     -- Use DATEDIFF for clarity
                                            a.book_location_id,
                                            c.cityMunDesc AS mun,
                                            p.provDesc AS prov,
                                            r.regDesc AS reg,
                                            a.payment_status as payment_status,
                                            a.txn_status as app_txn_status
                                        FROM  `app_transactions` AS a
                                        JOIN  `user_profile` AS up
                                            ON a.user_id = up.user_id
                                        JOIN  `items_inventory` AS ii
                                            ON a.book_item_inventory_id = ii.items_inventory_id
                                        JOIN  refcitymun AS c
                                            ON SUBSTR(a.book_location_id, 9, 6) = c.citymunCode
                                        JOIN  refprovince AS p
                                            ON SUBSTR(a.book_location_id, 4, 4) = p.provCode
                                        JOIN  refregion AS r
                                            ON SUBSTR(a.book_location_id, 1, 2) = r.regCode
                                        ";
                        if(!isset($_GET['rentalHist'])){
                            $sql_rental = $sql_rental . " WHERE a.txn_status = 'P'";
                        }
                        $sql_rental_query = query($sql_rental); ?>
                        <span class="fw-bold fs-5">CAR RENTALS CUSTOMER APPROVALS</span>
                        <table class="table table-striped table-responsive overflow-y-scroll" style="height:10vh">
                            <thead>
                                <?php if(!isset($_GET['rentalHist'])){ ?> <th>User Balance <br><sup>PHP</sup></th>
                                <?php } ?>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Plate No.</th>
                                <th class="align-middle d-none d-lg-block d-md-block">Model</th>
                                <th>Payment Status</th>
                                <th>Rental Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </thead>

                            <?php 

                            foreach($sql_rental_query as $r){ 
                                extract($r);
                                $userWalletInstance = new UserWallet($user);
                            $userWallet = $userWalletInstance->getBalance();
                            
                            $car = explode(':',$item_description);
                            ?>
                            <tr>
                                <?php if(!isset($_GET['rentalHist'])){ ?> <td class="align-middle <?php echo ($userWallet < $amount_to_pay) ? "text-danger":"text-success" ; ?>">
                                    <?php echo $userWallet;?></td>
                                <?php } ?>
                                <td class="align-middle">
                                    <?php echo $user_firstname . " " . $user_mi . " " . $user_lastname; ?></td>
                                <td class="align-middle"><?php echo $user_contact_no; ?></td>
                                <td class="d-none d-lg-block d-md-block align-middle"><?php echo $car[2] ; ?></td>
                                <td class="align-middle"><?php echo $car[4] ;?></td>
                                <td class="align-middle"> <span class="small <?php echo ((($userWallet < $amount_to_pay) && $payment_status == 'P') || $payment_status == 'P' || $app_txn_status == 'D') ? "text-danger":"text-success" ; ?>">
                                        <?php  echo ($app_txn_status == 'D') ? "Declined" :  (($payment_status == 'D') ? "Paid" : "Pending $amount_to_pay") ;  ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <?php switch($app_txn_status) {
                                                    case 'D': echo "Declined";
                                                    break;
                                                    case 'C': echo "Approved";
                                                    break;
                                                    default: echo "Pending";
                                            }?>
                                </td>
                                <td class="align-middle"><?php echo $book_start_dte; ?></td>
                                <td class="align-middle"><?php echo $book_end_dte; ?></td>
                                <td class="d-none d-lg-block d-md-block align-middle"><?php echo $elapseDay . " days";?>
                                </td>
                                <td class="align-middle"><span class="d-none d-lg-block d-md-block"><?php echo $reg . ","; ?></span>
                                    <?php echo $prov . ", " . $mun ;?></td>
                                <?php if(!isset($_GET['rentalHist'])){ ?>
                                <td class="align-middle"><a href="#" class="btn btn-success btnApproveRental" data-userid="<?php echo $user;?>" data-amounttopay="<?php echo $amount_to_pay;?>" data-action-id="C" data-apptxnid="<?php echo $app_txn_id;?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                            <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="align-middle"><a href="#" class="btn btn-danger btnApproveRental" data-userid="<?php echo $user;?>" data-amounttopay="<?php echo $amount_to_pay;?>" data-action-id="D" data-apptxnid="<?php echo $app_txn_id;?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                            <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856s-.036.586-.113.856c-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a10 10 0 0 1-.443-.05 9.36 9.36 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a9 9 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581s-.027-.414-.075-.581c-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.2 2.2 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.9.9 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1" />
                                        </svg>
                                    </a>
                                </td>
                                <?php } ?>
                            </tr>

                            <?php }
                        ?>

                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card shadow border-0">
                    <div class="card-header">
                        <h6 class="fs-6 fw-bold">CASH OUT APPROVAL</h6>
                        <sup class="text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z" />
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                            </svg>
                            SEND THEM THE CASHOUT AMOUNT VIA GCASH BEFORE APPROVING</sup>
                    </div>
                    <div class="card-body">
                        <div class="modal fade" id="qrPreviewModal" tabindex="-1" aria-labelledby="qrPreviewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-transparent border-0">
                                    <div class="modal-body text-center p-0">
                                        <img src="" id="qrPreviewImg" class="img-fluid rounded shadow" alt="Full QR Preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table">
                            <?php
                            

                                $sql_CashOutRequest = "SELECT uw.user_wallet_id
                                , up.user_firstname
                                , up.user_lastname
                                , up.user_mi
                                , up.user_contact_no
                                , uw.wallet_txn_amt
                                , uw.gcash_reference_number
                                , uw.gcash_account_number
                                , uw.gcash_account_name
                                , uw.gcash_attachment
                                , uw.gcash_qr
                             FROM `user_profile` up
                             JOIN `user_wallet` uw
                               ON (up.user_id = uw.user_id)
                           WHERE uw.payment_type = 'C'
                             AND uw.wallet_txn_status = 'P' 
                             ";
                                $CashOutRequest = query($sql_CashOutRequest);
                                if(!empty($CashOutRequest)){
                                    foreach($CashOutRequest as $cr){
                                        extract($cr); ?>
                            <tr class="text-center" style="height:100px">
                                <td class="align-middle">
                                    <?php echo strtoupper($user_lastname . ", " . $user_firstname . "," . $user_mi); ?>
                                </td>
                                <td class="align-middle"><span class="d-none d-md-block d-lg-block small">PHP</span><?php echo number_format($wallet_txn_amt * -1 ?? 0.00 ,2); ?>
                                </td>
                                <td class="align-middle"><?php echo $gcash_account_number; ?></td>
                                <td class="align-middle"><?php echo $gcash_account_name; ?></td>
                                <td class="align-middle"><img src="../../<?php echo $gcash_qr;?>" alt="" class="img-thumbnail img-responsive" style="width:100px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#qrPreviewModal"></td>
                                <td class="align-middle"><?php echo $gcash_reference_number; ?></td>
                                <td class="align-middle">
                                    <?php
                                        // Encode wallet ID in Base64
                                        $encoded_wallet_id = base64_encode($user_wallet_id);
                                        ?>
                                    <a href="?approveCashout&walletid=<?php echo $encoded_wallet_id; ?>" class="shadow btn btn-success approveCashOut">Approve</a>

                                </td>
                            </tr>
                            <?php }
                                }
                                else{ ?>
                            <tr>
                                <td colspan='12'>No Pending Cash out Request</td>
                            </tr>
                            <?php }  ?>
                        </table>

                    </div>
                </div>
            </div>
            <!-- Comments -->


            <div class="col-lg-12">
                <div class="card shadow border-0">
                    <div class="card-header">
                        <h6 class="fs-6 fw-bold">COMMENTS AND SUGGESTIONS</h6>
                    </div>
                    <div class="card-body">
                        <?php
                if(isset($_GET['approvecomment'])){
                    $commentid = $_GET['approvecomment'];
                    $sqlupdatecomment = query("UPDATE customerSuggestions SET approved = 1 WHERE cs_id = ?",[$commentid]); ?>
                        <div class="alert alert-success">Comment Approved.</div>
                        <?php }
                if(isset($_GET['disapprovecomment'])){
                    $commentid = $_GET['disapprovecomment'];
                    $sqlupdatecomment = query("UPDATE customerSuggestions SET approved = 0 WHERE cs_id = ?",[$commentid]); ?>
                        <div class="alert alert-warning">Comment Dispproved.</div>
                        <?php
                }
                 ?>
                        <table class="table table-responsive">
                            <tr class="row fw-bold">
                                <th class="col-1">STATUS</th>
                                <th class="col-1">EMAIL ADDRESS</th>
                                <th class="col-5">COMMENT</th>
                                <th class="col-2">RATING</th>
                                <th class="col-2">PHOTO</th>
                                <th class="col-1">ACTION</th>
                            </tr>
                            <?php
                                $rate = 0;
                                $sqlSuggestions = query("SELECT * FROM customerSuggestions");
                                foreach($sqlSuggestions as $com) { ?>
                            <tr class="row" id="comment<?php echo $com['cs_id'];?>">
                                <td class="col-1"><?php echo $com['approved'] == 1 ? "SHOWN" : "HIDDEN"; ?></td>
                                <td class="col-1"><?php echo $com['emailadd'];?></td>
                                <td class="col-5"><?php echo $com['message'];?></td>
                                <td class="col-2"><?php $rate=$com['rate'];
                                                  $maxrate = 5;
                                                    $s = 1;
                                                    while ($s <= $rate){
                                                        ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill text-warning" viewBox="0 0 16 16">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                    </svg>
                                    <?php 
                                                        $s++;
                                                    }
                                                    while ($s <= $maxrate){ ?>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                        <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                                    </svg>
                                    <?php 
                                                    $s++;
                                                    }
                                                        ?>
                                </td>
                                <td class="col-2"><img src="../../images/comments-photo/<?php echo $com['photo'];?>" width="100px" alt=""></td>
                                <td class="col-1">
                                    <?php if($com['approved'] == 0) { ?>
                                    <a href="?approvecomment=<?php echo $com['cs_id'];?>#comment<?php echo $com['cs_id'];?>" class="btn btn-success text-decoration-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                                        </svg>

                                    </a>
                                    <?php } else { ?>
                                    <a href="?disapprovecomment=<?php echo $com['cs_id'];?>#comment<?php echo $com['cs_id'];?>" class="btn btn-danger text-decoration-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.38 1.38 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51q.205.03.443.051c.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.9 1.9 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2 2 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.2 3.2 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.8 4.8 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591" />
                                        </svg>
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php }
                            ?>
                        </table>

                    </div>
                </div>
            </div>
            <!-- Comments -->
        </div>
    </div>

</body>


<script>
    document.querySelectorAll('img[data-bs-target="#qrPreviewModal"]').forEach(img => {
        img.addEventListener('click', function() {
            const fullSrc = this.getAttribute('src');
            document.getElementById('qrPreviewImg').src = fullSrc;
        });
    });
</script>
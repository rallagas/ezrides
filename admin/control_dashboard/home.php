<body>
    <?php

include_once "../_class_userWallet.php";
$sql = null;
$sql = "SELECT SUM(wallet_txn_amt) as income FROM `user_wallet` WHERE wallet_action LIKE '%Admin' ";
$sales_data = query($sql);
$sales = $sales_data[0]['income'] ?? 0;

$sql = "SELECT SUM(wallet_txn_amt) as total_wallet_pool from `user_wallet` ";
$wallet_pool_data = query($sql);
$wallet_pool = $wallet_pool_data[0]['total_wallet_pool'] ?? 0 ;

$activeRidersSQL = "SELECT COUNT(1) countActiveRiders from `angkas_rider_queue` WHERE DATE(queue_date) = CURRENT_DATE";
$activeRider = query($activeRidersSQL);
$ActiveRidersCount = $activeRider[0]['countActiveRiders'];

$sql_booking_total = "SELECT COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` NOT in ('C','D') and DATE(date_booked) = CURRENT_DATE ";
$sql_booking_total_data = query($sql_booking_total);
$booking_total = $sql_booking_total_data[0]['total_number_of_bookings'] ?? 0;

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
                          FROM `user_profile` up
                          JOIN `user_wallet` uw
                            ON (up.user_id = uw.user_id)
                        WHERE uw.payment_type = 'T'
                          AND uw.wallet_txn_status = 'P'
                          ";
$pendingTopupList = query($sql_top_up_approval);                          


?>

    <div class="container mt-4">
        <div class="row g-2">
            <div class="col-lg-3">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">INCOME <small
                                class="small fs-6 fw-light text-success">PHP</small></h6>
                        <h1 class="display-4"><?php echo number_format($sales,2);?></h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">WALLET POOL <small
                                class="small fs-6 fw-light text-success">PHP</small></h6>
                        <h1 class="display-4"><?php echo number_format($wallet_pool,2);?></h1>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                    <h6 class="card-title fw-bold">ACTIVE RIDERS</h6>
                        <h1 class="display-4"><?php echo $ActiveRidersCount;?></h1>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">ACTIVE BOOKINGS</h6>
                        <h1 class="display-4"><?php echo $booking_total;?></h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="card-title fw-bold">BOOKING PER DAY (60 DAYS)</h6>
                    </div>
                    <div class="card-body overflow-y-scroll" style="height:30vh;">

                        <table class="table table-sm table-responsive table-borderless">
                            <?php foreach($booking_per_day_data as $trend){ ?>
                            <tr>
                                <td style="width:10%" class="small"><?php echo $trend['date_booked'];?></td>
                                <td style="width:3%" class="text-center d-none d-lg-block">
                                    <?php echo $trend['total_number_of_bookings']; ?></td>
                                <td style="width:87%">
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar"
                                            style="width: <?php echo (($booking_total > 0) ? ($trend['total_number_of_bookings'] / $booking_total) * 100 : 0) ;?>%">
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
                                <th class="d-none d-md-block">User</th>
                                <th class="d-none d-md-block">Contact Info</th>
                                <th> Amount </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Number </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Name </th>
                                <th> <span class="d-none d-lg-block d-md-block">GCASH Acct</span> Reference </th>
                            </tr>
                            <?php 
                            if(empty($pendingTopupList)){ ?>
                                <tr>
                                    <td colspan="7" class="text-center">No Request yet.</td>
                                </tr>
                            <?php }
                            else {
                             foreach($pendingTopupList  as $tu){
                             extract($tu); ?>
                            <tr>
                                <td class="d-none d-md-block">
                                    <?php echo $user_lastname . ", " . $user_firstname . "," . $user_mi; ?></td>
                                <td class="d-none d-md-block"><?php echo $user_contact_no; ?></td>
                                <td><span
                                        class="d-none d-md-block d-lg-block small">PHP</span><?php echo number_format($wallet_txn_amt ?? 0.00 ,2); ?>
                                </td>
                                <td><?php echo $gcash_account_number; ?></td>
                                <td><?php echo $gcash_account_name; ?></td>
                                <td><?php echo $gcash_reference_number; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success btn-approve-decline"
                                        data-action-id='C'
                                        data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                            <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                        </svg>
                                    </a>

                                    <a class="btn btn-sm btn-outline-danger btn-approve-decline"
                                        data-action-id='D'
                                        data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                            <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856s-.036.586-.113.856c-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a10 10 0 0 1-.443-.05 9.36 9.36 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a9 9 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581s-.027-.414-.075-.581c-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.2 2.2 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.9.9 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1"/>
                                        </svg>
                                    </a>
                                </td>

                            </tr>
                            <?php } 
                            }?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="card-title fw-bold">CAR RENTAL APPROVAL 
                            <?php if(!isset($_GET['rentalHist'])){?>
                            <a href="?rentalHist" class="btn btn-link text-decoration-none small">HISTORICAL</a> 
                            <?php } 
                            else{ ?>
                            <a href="index.php" class="btn btn-link text-decoration-none small">PENDING APPROVALS</a> 
                            <?php } ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        
                        <?php
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

                        <table class="table table-striped table-responsive overflow-y-scroll" style="height:10vh">
                            <thead>
                                <th>User Balance <br><sup>PHP</sup></th>
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
                                    <td class="align-middle <?php echo ($userWallet < $amount_to_pay) ? "text-danger":"text-success" ; ?>"><?php echo $userWallet;?></td>
                                    <td class="align-middle"><?php echo $user_firstname . " " . $user_mi . " " . $user_lastname; ?></td>
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
                                    <td class="d-none d-lg-block d-md-block align-middle"><?php echo $elapseDay . " days";?></td>
                                    <td class="align-middle"><span class="d-none d-lg-block d-md-block"><?php echo $reg . ","; ?></span> <?php echo $prov . ", " . $mun ;?></td>
                                    <?php if(!isset($_GET['rentalHist'])){ ?>
                                    <td class="align-middle"><a href="#" class="btn btn-success btnApproveRental" data-userid="<?php echo $user;?>" data-amounttopay="<?php echo $amount_to_pay;?>" data-action-id="C" data-detailId="<?php echo $item_description;?>" data-apptxnid="<?php echo $app_txn_id;?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                                <path
                                                    d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="align-middle"><a href="#" class="btn btn-danger btnApproveRental" data-userid="<?php echo $user;?>" data-amounttopay="<?php echo $amount_to_pay;?>" data-action-id="D" data-detailId="<?php echo $item_description;?>" data-apptxnid="<?php echo $app_txn_id;?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                                <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856s-.036.586-.113.856c-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a10 10 0 0 1-.443-.05 9.36 9.36 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a9 9 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581s-.027-.414-.075-.581c-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.2 2.2 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.9.9 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1"/>
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

            <div class="col-lg">

            </div>
        </div>
    </div>

</body>


<body class="bg-purple">
    <?php
$sql = null;
$sql = "SELECT SUM(wallet_txn_amt) as income FROM `user_wallet` WHERE wallet_action LIKE '%Admin' ";
$sales_data = query($sql);
$sales = $sales_data[0]['income'] ?? 0;

$sql = "SELECT SUM(wallet_txn_amt) as total_wallet_pool from `user_wallet` ";
$wallet_pool_data = query($sql);
$wallet_pool = $wallet_pool_data[0]['total_wallet_pool'] ?? 0 ;


$sql_booking_total = "SELECT COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` in ('C','D') and `payment_status` in ('C') and DATE(date_booked) > CURRENT_DATE - 60";
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
                             , uw.gcash_amount_sent
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
            <div class="col-lg-4">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">INCOME</h6>
                        <h1 class="display-4"><?php echo number_format($sales,2);?></h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">WALLET POOL</h6>
                        <h1 class="display-4"><?php echo number_format($wallet_pool,2);?></h1>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BOOKING COUNT (60 days)</h6>
                        <h1 class="display-4"><?php echo $booking_total;?></h1>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BOOKING</h6>
                        <table class="table table-sm table-responsive table-borderless">
                            <?php foreach($booking_per_day_data as $trend){ ?>
                            <tr>
                                <td style="width:10%" class="small"><?php echo $trend['date_booked'];?></td>
                                <td style="width:3%" class="text-center d-md-none">
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

            <div class="col-lg-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">TOP UP APPROVALS</h6>
                        <table class="table table-sm table-responsive table-borderless">
                            <?php foreach($pendingTopupList  as $tu){
                             extract($tu); ?>
                            <tr>
                                <td><?php echo $user_lastname . ", " . $user_firstname . "," . $user_mi; ?></td>
                                <td><?php echo $user_contact_no; ?></td>
                                <td><?php echo "Php " . number_format($wallet_txn_amt,2); ?></td>
                                <td><?php echo $gcash_reference_number; ?></td>
                                <td><?php echo $gcash_amount_sent; ?></td>
                                <td><?php echo $gcash_account_name; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-success btn-approve"
                                        data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        Approve
                                    </a>

                                    <a class="btn btn-sm btn-success btn-decline"
                                        data-wallet-id="<?php echo urlencode(base64_encode(openssl_encrypt($user_wallet_id, 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV))); ?>">
                                        Approve
                                    </a>
                                </td>

                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">CAR RENTAL APPROVAL</h6>
                        <?php
                        $sql_rental = "SELECT up.user_firstname
                                            , up.user_lastname
                                            , up.user_mi
                                            , up.user_contact_no
                                            , ii.item_description 
                                            , ii.item_price
                                            , a.book_start_dte
                                            , a.book_end_dte 
                                            , a.book_end_dte - book_start_dte as elapseDay
                                            , a.book_location_id
                                            , c.cityMunDesc mun
                                            , p.provDesc prov
                                            , r.regDesc reg
                                         FROM `app_transactions` as a
                                         JOIN `user_profile` as up
                                           on a.user_id = up.user_id
                                         JOIN `items_inventory` as ii
                                           on a.book_item_inventory_id = ii.items_inventory_id
                                         JOIN  refcitymun as c
                                           on substr(a.book_location_id, 9, 6) = c.citymunCode
                                         JOIN  refprovince as p
                                           on substr(a.book_location_id, 4, 4) = p.provCode
                                        JOIN refregion r
                                          on substr(a.book_location_id, 1, 2) = r.regCode
                                        ";
                        $sql_rental_query = query($sql_rental); ?>

                        <table class="table table-striped">
                            <?php foreach($sql_rental_query as $r){ 
                            extract($r);
                            $car = explode(':',$item_description);
                            ?>
                            <tr>
                                <td><?php echo $user_firstname . " " . $user_mi . " " . $user_lastname; ?></td>
                                <td><?php echo $user_contact_no; ?></td>
                                <td><?php echo $car[2] ; ?></td>
                                <td><?php echo $car[4] ;?></td>
                                <td><?php echo $book_start_dte; ?></td>
                                <td><?php echo $book_end_dte; ?></td>
                                <td><?php echo $elapseDay . " days";?></td>
                                <td><?php echo $reg . "," . $prov . ", " . $mun ;?></td>
                                <td><a href="#" class="btn btn-success btnApproveRental">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16"> <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" /></svg>
                                    </a>
                                </td>
                            </tr>

                            <?php }
                        ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
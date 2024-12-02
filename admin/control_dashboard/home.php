<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Reports</title>
</head>

<body class="bg-purple">
    <?php
$sql = null;
$sql = "SELECT SUM(wallet_txn_amt) as income FROM `user_wallet` WHERE wallet_action LIKE '%Admin' ";
$sales_data = query($sql);
$sales = $sales_data[0]['income'];

$sql = "SELECT SUM(wallet_txn_amt) as total_wallet_pool from `user_wallet` ";
$wallet_pool_data = query($sql);
$wallet_pool = $wallet_pool_data[0]['total_wallet_pool'];


$sql_booking_total = "SELECT COUNT(angkas_booking_reference) total_number_of_bookings from `angkas_bookings` where `booking_status` in ('C','D') and `payment_status` in ('C') and DATE(date_booked) > CURRENT_DATE - 60";
$sql_booking_total_data = query($sql_booking_total);
$booking_total = $sql_booking_total_data[0]['total_number_of_bookings'];

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
            <div class="col-lg-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BOOKING</h6>
                        <table class="table table-sm table-responsive table-borderless">
                            <?php foreach($booking_per_day_data as $trend){ ?>
                            <tr>
                                <td style="width:10%" class="small"><?php echo $trend['date_booked'];?></td>
                                <td style="width:3%" class="text-center">
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
                                </td>

                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
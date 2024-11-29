<?php include_once "../_db.php";
      include_once "../_sql_utility.php";
      require_once "_class_riderWallet.php";
$rider_logged=$_SESSION['user_id'];
query("DELETE FROM angkas_bookings WHERE date_booked < (NOW() - INTERVAL 2 HOUR) and angkas_rider_user_id is NULL and booking_status = 'P'");
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Rider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <?php include_once "nav_rider.php";?>
                <div class="offcanvas offcanvas-start bg-purple vh-100" tabindex="-1" id="appMenu"
                    aria-labelledby="appMenu">
                    <div class="offcanvas-header">
                        <img src="../icons/ezrides.png" alt="" class="img-fluid w-25">
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body container-fluid vh-75">
                        <div class="row g-1 mb-3">
                            <?php  include_once "menu.php"; ?>
                        </div>

                        <div class="row g-1 mb-3 vh-50 border-1" id="BookingHistoryContent">
                            <div class="col-sm-12 col-lg-12 col-md-12">
                                <div id="BookingDetails" class="card shadow"></div>
                                <div class="collapse" id="shopOrderCollapse">
                                    <div id="shopOrderDetails">Loading...</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="MyEarnings row">
            <div class="col-12 px-5">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="wallet-tab" data-bs-toggle="tab"
                            data-bs-target="#wallet-tab-pane" type="button" role="tab" aria-controls="wallet-tab-pane"
                            aria-selected="true">Wallet</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bookings-tab" data-bs-toggle="tab"
                            data-bs-target="#bookings-tab-pane" type="button" role="tab"
                            aria-controls="bookings-tab-pane" aria-selected="false">Bookings</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="claims-tab" data-bs-toggle="tab" data-bs-target="#claims-tab-pane"
                            type="button" role="tab" aria-controls="claims-tab-pane"
                            aria-selected="false">History</button>
                    </li>


                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="wallet-tab-pane" role="tabpanel"
                        aria-labelledby="wallet-tab" tabindex="0">
                        <span class="fs-3">My Earnings</span>
                        <div class="card shadow my-3">
                            <div class="card-body">
                                <span class="card-title text-secondary">Current Wallet Balance</span>
                                <?php 
                                    $userWallet = new UserWallet(USER_LOGGED);
                                    $balance = $userWallet->getBalance(USER_LOGGED);
                                ?>
                                <p class="walletbalance card-text display-4">
                                   Php <?php echo number_format($balance,2)  ;?>
                                </p>

                            </div>
                        </div>
                        <?php $sql_claims = "SELECT uw.user_wallet_id, uw.wallet_txn_amt as wallet_txn_amt, uw.reference_number, DATE(uw.wallet_txn_start_ts) as txn_dte
                                                FROM   `angkas_bookings` ab
                                                    JOIN `user_wallet` uw
                                                        ON ( ab.angkas_booking_reference = uw.reference_number
                                                            OR ab.shop_order_reference_number = uw.reference_number )
                                                WHERE  ab.angkas_rider_user_id = ?
                                                    AND ab.payment_status = 'C'
                                                    AND ab.booking_status = 'C'
                                                    AND uw.payment_type = 'R'
                                                    AND ( uw.payto IS NULL
                                                            AND uw.user_id IS NULL )
                                                union all
                                                SELECT uw.user_wallet_id, uw.wallet_txn_amt * -1 as wallet_txn_amt, uw.reference_number, DATE(uw.wallet_txn_start_ts) as txn_dte
                                                FROM   `angkas_bookings` ab
                                                    JOIN `user_wallet` uw
                                                        ON ( ab.shop_order_reference_number = uw.reference_number )
                                                WHERE  ab.angkas_rider_user_id = ?
                                                    AND ab.payment_status = 'C'
                                                    AND ab.booking_status = 'C'
                                                    AND uw.payment_type = 'S'
                                                    AND ( uw.payto IS NULL) ";
                            $claims = query($sql_claims,[USER_LOGGED,USER_LOGGED]);
                            if(!empty($claims)){

                            foreach($claims as $c){ ?>

                        <a data-claimwallet="<?php echo $c['user_wallet_id']?>" class="text-decoration-none claim-stub">
                            <div class="shadow card rounded-0 bg-success bg-opacity-75 border-5 border-end-0 border-top-0 border-bottom-0 my-1 p-3 text-light clear-fix">
                                <span class="text-secondary"><?php echo $c['txn_dte']; ?></span>
                              <h3 class="fs-3 fw-bold float-end">+ Php <?php echo $c['wallet_txn_amt'];?> </h3> 
                              <small class="small float-end"><?php echo $c['reference_number']; ?></small>

                            </div>
                        </a>
                        <?php } 
                        } ?>

                    </div>

                    <div class="tab-pane fade" id="bookings-tab-pane" role="tabpanel" aria-labelledby="bookings-tab"
                        tabindex="0">.
                        <div id="availableBookings"></div>

                    </div>
                    <div class="tab-pane fade" id="claims-tab-pane" role="tabpanel" aria-labelledby="claims-tab"
                        tabindex="0">
                      
                    </div>

                </div>
            </div>



        </div>



    </div>


</body>

<script src="../js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="rider_process.js"></script>
<script src="../_multipurpose_ajax.js"></script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry&loading=async">
</script>


</html>
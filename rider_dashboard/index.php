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
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane"
                            type="button" role="tab" aria-controls="history-tab-pane"
                            aria-selected="false">History</button>
                    </li>


                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="wallet-tab-pane" role="tabpanel"
                        aria-labelledby="wallet-tab" tabindex="0">
                        
                        <div class="container-fluid">
                            <div class="row gx-3">
                                <div class="col-6">
                                    <div class="card shadow">
                                        <div class="card-body">

                                        <span class="card-title text-secondary">My Wallet Balance 
                                                    <button class="btn btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#topUpModal">CASH IN</button>
                                                    <?php include_once "../top_up_modal.php";?>
                                                </span>
                                            <?php 

                                                    $userWallet = new UserWallet(USER_LOGGED);
                                                    $balance = $userWallet->getbalance(USER_LOGGED);
                                                ?>
                                                <p class="walletbalance card-text display-4">
                                                    Php <?php echo number_format($balance,2)  ;?>
                                                </p>
                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card shadow">
                                            <div class="card-body">
                                                <span class="card-title text-secondary">My Earnings
                                                    <a class="btn btn-link text-decoration-none" data-bs-toggle="collapse"
                                                        href="#cashOut" role="button" aria-expanded="false" aria-controls="cashOut">
                                                        REQUEST CASH OUT
                                                    </a>
                                                </span>
                                                <?php 
                                                    $balance = $userWallet->getEarnings(USER_LOGGED);
                                                ?>
                                                <p class="earnings card-text display-4">
                                                    Php <?php echo number_format($balance,2)  ;?>
                                                </p>

                                                <div class="collapse card border-opacity-25" id="cashOut">
                                                    <div class="card card-body">
                                                        <form id="formCashOut">
                                                            <input name="CashOutAmount" type="number" class="form-control mb-3"
                                                                placeholder="Amount">
                                                            <input name="GCashAccountNumber" type="text" class="form-control mb-3"
                                                                placeholder="GCASH Account Number">
                                                            <input name="GCashAccountName" type="text" class="form-control mb-3"
                                                                placeholder="GCASH Account Name">
                                                            <button class="btn btn-primary">Submit Request
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                    fill="currentColor" class="bi bi-rocket-takeoff"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M9.752 6.193c.599.6 1.73.437 2.528-.362s.96-1.932.362-2.531c-.599-.6-1.73-.438-2.528.361-.798.8-.96 1.933-.362 2.532" />
                                                                    <path
                                                                        d="M15.811 3.312c-.363 1.534-1.334 3.626-3.64 6.218l-.24 2.408a2.56 2.56 0 0 1-.732 1.526L8.817 15.85a.51.51 0 0 1-.867-.434l.27-1.899c.04-.28-.013-.593-.131-.956a9 9 0 0 0-.249-.657l-.082-.202c-.815-.197-1.578-.662-2.191-1.277-.614-.615-1.079-1.379-1.275-2.195l-.203-.083a10 10 0 0 0-.655-.248c-.363-.119-.675-.172-.955-.132l-1.896.27A.51.51 0 0 1 .15 7.17l2.382-2.386c.41-.41.947-.67 1.524-.734h.006l2.4-.238C9.005 1.55 11.087.582 12.623.208c.89-.217 1.59-.232 2.08-.188.244.023.435.06.57.093q.1.026.16.045c.184.06.279.13.351.295l.029.073a3.5 3.5 0 0 1 .157.721c.055.485.051 1.178-.159 2.065m-4.828 7.475.04-.04-.107 1.081a1.54 1.54 0 0 1-.44.913l-1.298 1.3.054-.38c.072-.506-.034-.993-.172-1.418a9 9 0 0 0-.164-.45c.738-.065 1.462-.38 2.087-1.006M5.205 5c-.625.626-.94 1.351-1.004 2.09a9 9 0 0 0-.45-.164c-.424-.138-.91-.244-1.416-.172l-.38.054 1.3-1.3c.245-.246.566-.401.91-.44l1.08-.107zm9.406-3.961c-.38-.034-.967-.027-1.746.163-1.558.38-3.917 1.496-6.937 4.521-.62.62-.799 1.34-.687 2.051.107.676.483 1.362 1.048 1.928.564.565 1.25.941 1.924 1.049.71.112 1.429-.067 2.048-.688 3.079-3.083 4.192-5.444 4.556-6.987.183-.771.18-1.345.138-1.713a3 3 0 0 0-.045-.283 3 3 0 0 0-.3-.041Z" />
                                                                    <path
                                                                        d="M7.009 12.139a7.6 7.6 0 0 1-1.804-1.352A7.6 7.6 0 0 1 3.794 8.86c-1.102.992-1.965 5.054-1.839 5.18.125.126 3.936-.896 5.054-1.902Z" />
                                                                </svg>

                                                            </button>


                                                        </form>
                                                        <div class="txn_status"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="col-12">
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

                                        <a href="#" data-claimwallet="<?php echo $c['user_wallet_id']?>" class="text-decoration-none claim-stub">
                                            <div
                                                class="shadow card rounded-0 bg-success bg-opacity-75 border-5 border-end-0 border-top-0 border-bottom-0 my-1 p-3 text-light clear-fix">
                                                <span class="text-secondary"><?php echo $c['txn_dte']; ?></span>
                                                <h3 class="fs-3 fw-bold float-end">+ Php <?php echo $c['wallet_txn_amt'];?> </h3>
                                                <small class="small float-end"><?php echo $c['reference_number']; ?></small>
                                                <small class="float-end fw-bold">CLAIM EARNINGS STUB</small>
                                            </div>
                                        </a>
                                        <?php } 
                                        } ?>
                                </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="bookings-tab-pane" role="tabpanel" aria-labelledby="bookings-tab"
                        tabindex="0">
                        <div id="availableBookings" >
                                        <div class="card p-3">
                                            <div class="card-body">
                                                
                                            <h3 class="fs-4">No Customers at the moment.</h3>
                                            </div>
                                        </div>

                        </div>

                    </div>
                    <div class="tab-pane fade" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab"
                        tabindex="0">
                        <div id="rideHistory">

                        </div>

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
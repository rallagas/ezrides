<?php include_once "../_db.php";
      include_once "../_sql_utility.php";
      require_once "_class_riderWallet.php";
$userWallet = new UserWallet(USER_LOGGED);
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
    <?php include_once "nav_rider.php";?>
    <?php include_once "../top_up_modal.php";?>

    <div class=" container">
        <div class="row px-2">
            <div class="col-12">
                <div class="txn_status"></div>
            </div>

            <div class="col-6 col-lg-6 mb-3">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <small class="card-title text-secondary">
                            <button class="float-start btn btn-sm btn-warning shadow me-2" data-bs-toggle="modal"
                                data-bs-target="#topUpModal">
                                <span class="d-none d-lg-block"><sup>EZ</sup>CASH IN</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-plus-square" viewBox="0 0 16 16">
                                    <path
                                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                </svg>
                            </button>
                            <span><sup>EZ</sup>WALLET</span>
                        </small>
                        <hr class="m-0 p-0">

                        <span class="walletbalance card-text fs-3 fw-bold float-end">
                            <?php 
                                                $balance = $userWallet->getbalance(USER_LOGGED);
                                                echo "<sup>EZ</sup>COIN " . number_format($balance,2)  ;?>
                        </span>

                    </div>
                </div>
            </div>
            <!-- EARNINGS -->
            <div class="col-6 col-lg-6 mb-3">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <small class="card-title text-secondary">
                            <a class="float-start btn btn-sm btn-dark bg-purple shadow me-2" data-bs-toggle="collapse"
                                href="#convertEarnings" role="button" aria-expanded="false" aria-controls="cashOut">
                                <span class="d-none d-lg-block"><sup>EZ</sup>CONVERT</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-square" viewBox="0 0 16 16">
                                    <path d="M0 6a6 6 0 1 1 12 0A6 6 0 0 1 0 6"/>
                                    <path d="M12.93 5h1.57a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-1.57a7 7 0 0 1-1-.22v1.79A1.5 1.5 0 0 0 5.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 4h-1.79q.145.486.22 1"/>
                                    </svg>
                            </a>

                            
                            <a class="float-start btn btn-sm btn-primary shadow me-2" data-bs-toggle="collapse"
                                href="#cashOut" role="button" aria-expanded="false" aria-controls="cashOut">
                                <span class="d-none d-lg-block"><sup>EZ</sup>CASH OUT</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-down-square" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                                </svg>
                            </a>

                            

                            <span><sup>EZ</sup>EARNINGS</span>
                        </small>
                        <hr class="m-0 p-0">
                        <span class="earnings card-text fs-3 fw-bold float-end">
                            <?php  $earnings = $userWallet->getEarnings();
                                                 echo "PHP " . number_format($earnings,2)  ;?>
                        </span>
                    </div>
                </div>
            </div>
            <!-- END EARNINGS -->
            <div class="col-12">
                <div class="collapse card shadow border-0" id="convertEarnings">
                    <div class="card-header bg-purple text-light">
                        <span class="fs-6 card-title fw-bold">CONVERT TO <sup>EZ</sup>COIN</span>
                        <span class="float-end"><small class="small">EXCHANGE RATE: 1 PHP = 1 EZ COIN</small></span>
                    </div>
                    <div class="card-body">
                        <form id="formConvert">
                            <div class="mb-2 alert alert-warning">
                                <b class="fw-bold">Reminder:</b> You are about to convert your Earnings to EZ Coins. Please be infomed that once converted to EZ Coins, you will not be able to Cash Out, EZ Coins can only be used for transacting within the app.
                            </div>
                            <input name="ConvertAmount" type="number" class="form-control mb-3" placeholder="Amount"
                                max="<?php echo $earnings;?>">
                           
                            <button class="btn btn-dark bg-purple btnCashOut">Convert
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-rocket-takeoff" viewBox="0 0 16 16">
                                    <path
                                        d="M9.752 6.193c.599.6 1.73.437 2.528-.362s.96-1.932.362-2.531c-.599-.6-1.73-.438-2.528.361-.798.8-.96 1.933-.362 2.532" />
                                    <path
                                        d="M15.811 3.312c-.363 1.534-1.334 3.626-3.64 6.218l-.24 2.408a2.56 2.56 0 0 1-.732 1.526L8.817 15.85a.51.51 0 0 1-.867-.434l.27-1.899c.04-.28-.013-.593-.131-.956a9 9 0 0 0-.249-.657l-.082-.202c-.815-.197-1.578-.662-2.191-1.277-.614-.615-1.079-1.379-1.275-2.195l-.203-.083a10 10 0 0 0-.655-.248c-.363-.119-.675-.172-.955-.132l-1.896.27A.51.51 0 0 1 .15 7.17l2.382-2.386c.41-.41.947-.67 1.524-.734h.006l2.4-.238C9.005 1.55 11.087.582 12.623.208c.89-.217 1.59-.232 2.08-.188.244.023.435.06.57.093q.1.026.16.045c.184.06.279.13.351.295l.029.073a3.5 3.5 0 0 1 .157.721c.055.485.051 1.178-.159 2.065m-4.828 7.475.04-.04-.107 1.081a1.54 1.54 0 0 1-.44.913l-1.298 1.3.054-.38c.072-.506-.034-.993-.172-1.418a9 9 0 0 0-.164-.45c.738-.065 1.462-.38 2.087-1.006M5.205 5c-.625.626-.94 1.351-1.004 2.09a9 9 0 0 0-.45-.164c-.424-.138-.91-.244-1.416-.172l-.38.054 1.3-1.3c.245-.246.566-.401.91-.44l1.08-.107zm9.406-3.961c-.38-.034-.967-.027-1.746.163-1.558.38-3.917 1.496-6.937 4.521-.62.62-.799 1.34-.687 2.051.107.676.483 1.362 1.048 1.928.564.565 1.25.941 1.924 1.049.71.112 1.429-.067 2.048-.688 3.079-3.083 4.192-5.444 4.556-6.987.183-.771.18-1.345.138-1.713a3 3 0 0 0-.045-.283 3 3 0 0 0-.3-.041Z" />
                                    <path
                                        d="M7.009 12.139a7.6 7.6 0 0 1-1.804-1.352A7.6 7.6 0 0 1 3.794 8.86c-1.102.992-1.965 5.054-1.839 5.18.125.126 3.936-.896 5.054-1.902Z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="collapse card shadow border-0" id="cashOut">
                    <div class="card-header bg-purple text-light">
                        <span class="fs-6 card-title fw-bold">CASH OUT REQUEST FORM</span>
                    </div>
                    <div class="card-body">
                        <form id="formCashOut">
                            <input name="CashOutAmount" type="number" class="form-control mb-3" placeholder="Amount"
                                max="<?php echo $earnings;?>">
                            <input name="GCashAccountNumber" type="text" class="form-control mb-3"
                                placeholder="GCASH Account Number">
                            <input name="GCashAccountName" type="text" class="form-control mb-3"
                                placeholder="GCASH Account Name">
                            <button class="btn btn-primary btnCashOut">Submit Request
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-rocket-takeoff" viewBox="0 0 16 16">
                                    <path
                                        d="M9.752 6.193c.599.6 1.73.437 2.528-.362s.96-1.932.362-2.531c-.599-.6-1.73-.438-2.528.361-.798.8-.96 1.933-.362 2.532" />
                                    <path
                                        d="M15.811 3.312c-.363 1.534-1.334 3.626-3.64 6.218l-.24 2.408a2.56 2.56 0 0 1-.732 1.526L8.817 15.85a.51.51 0 0 1-.867-.434l.27-1.899c.04-.28-.013-.593-.131-.956a9 9 0 0 0-.249-.657l-.082-.202c-.815-.197-1.578-.662-2.191-1.277-.614-.615-1.079-1.379-1.275-2.195l-.203-.083a10 10 0 0 0-.655-.248c-.363-.119-.675-.172-.955-.132l-1.896.27A.51.51 0 0 1 .15 7.17l2.382-2.386c.41-.41.947-.67 1.524-.734h.006l2.4-.238C9.005 1.55 11.087.582 12.623.208c.89-.217 1.59-.232 2.08-.188.244.023.435.06.57.093q.1.026.16.045c.184.06.279.13.351.295l.029.073a3.5 3.5 0 0 1 .157.721c.055.485.051 1.178-.159 2.065m-4.828 7.475.04-.04-.107 1.081a1.54 1.54 0 0 1-.44.913l-1.298 1.3.054-.38c.072-.506-.034-.993-.172-1.418a9 9 0 0 0-.164-.45c.738-.065 1.462-.38 2.087-1.006M5.205 5c-.625.626-.94 1.351-1.004 2.09a9 9 0 0 0-.45-.164c-.424-.138-.91-.244-1.416-.172l-.38.054 1.3-1.3c.245-.246.566-.401.91-.44l1.08-.107zm9.406-3.961c-.38-.034-.967-.027-1.746.163-1.558.38-3.917 1.496-6.937 4.521-.62.62-.799 1.34-.687 2.051.107.676.483 1.362 1.048 1.928.564.565 1.25.941 1.924 1.049.71.112 1.429-.067 2.048-.688 3.079-3.083 4.192-5.444 4.556-6.987.183-.771.18-1.345.138-1.713a3 3 0 0 0-.045-.283 3 3 0 0 0-.3-.041Z" />
                                    <path
                                        d="M7.009 12.139a7.6 7.6 0 0 1-1.804-1.352A7.6 7.6 0 0 1 3.794 8.86c-1.102.992-1.965 5.054-1.839 5.18.125.126 3.936-.896 5.054-1.902Z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
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

                                                    <a href="#" data-claimwallet="<?php echo $c['user_wallet_id']?>"
                                                        class="text-decoration-none claim-stub">
                                                        <div
                                                            class="shadow card rounded-0 bg-success bg-opacity-75 border-5 border-end-0 border-top-0 border-bottom-0 my-1 p-3 text-light clear-fix">
                                                            <span class="text-secondary"><?php echo $c['txn_dte']; ?></span>
                                                            <h3 class="fs-3 fw-bold float-end">+ Php <?php echo $c['wallet_txn_amt'];?>
                                                            </h3>
                                                            <small class="small float-end"><?php echo $c['reference_number']; ?></small>
                                                            <small class="float-end fw-bold">CLAIM EARNINGS STUB</small>
                                                        </div>
                                                    </a>
                                                    <?php } 
                                            } ?>
            </div>
            <!-- end container-fluid   -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card shadow border-0">
                <div class="card-header bg-purple text-light">
                        <span class="fs-6 card-title fw-bold">CURRENT BOOKING</span>

                    </div>
                    <div class="card-body" id="availableBookings">
                        Checking Available Bookings...
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-purple text-light">
                        <span class="fs-6 card-title fw-bold">RIDE HISTORY</span>
                        <label for="" class="float-end form-label mb-0">
                            <div class="input-group">
                                <span class="input-group-text bg-purple text-light"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                    </svg>
                                </span>
                                <input id="searchRide" type="text"
                                    class="form-control form-control-sm float-end bg-purple border-1 border-light">
                            </div>
                        </label>
                    </div>
                    <div class="card-body" id="rideHistory" style="max-height: 50vh; overflow-y:scroll">
                        No Recent Ride History.
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="card shadow border-0">
                    <div class="card-header bg-purple text-light">
                        <span class="fs-6 card-title fw-bold">TRANSACTION HISTORY</span>

                    </div>
                    <div class="card-body p-0">
                        <table id="transactionHistoryTable" class="table table-hover table-responsive m-0">
                            <thead class="">
                                <tr>
                                    <th>Amount (Php)</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="overflow-scroll">

                                <!-- Transactions will be loaded here -->
                            </tbody>
                        </table>
                        <div id="pagination" class="pagination-container text-center">
                            <!-- Pagination buttons will be inserted here -->
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
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&libraries=places,geometry&loading=async">
</script>


</html>
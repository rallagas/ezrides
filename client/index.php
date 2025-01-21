<?php
require_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
include_once "button-functions.php";
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-xtmKaCh9tfCPtb3MMyjsQVNn3GFjzZsgCVD3LUmAwbLSU3u/7fIZkIVrKyxMAdzs" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    .modal-img {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-img-content {
        margin-top: 15%;
        margin-left: 10%;
        display: block;
        max-width: 80vw;
        max-height: 100%;
        width: 80vh;

    }

    .close {
        position: absolute;
        top: 10%;
        right: 25px;
        color: #fff;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    .quick-links {
        height: 100%;
    }

    .form-range {
        accent-color: #ffcc00;
        /* Changes the primary color of the range control thumb */
    }

    .form-range::-webkit-slider-thumb {
        background-color: #ff9900;
        /* Custom thumb color for WebKit browsers */

    }

    .form-range::-moz-range-thumb {
        background-color: #ff9900;
        /* Custom thumb color for Mozilla browsers */

    }

    .form-range::-ms-thumb {
        background-color: #ff9900;
        /* Custom thumb color for IE/Edge */
    }
    </style>
</head>

<body>
<?php include_once "nav-client.php";?>

    <div class="container-fluid p-1">
        <div class="row px-5" id="queryresult"></div>
        <div class="row px-4">
            <div class="col-12" id="TransactionStatus"></div>
            <?php
                $txn_cat = select_data("txn_category", NULL, "txn_category_id", 15);
                foreach ($txn_cat as $cat) {
                    if (isset($_GET['page']) && isset($_GET['txn_cat'])) {
                        $page = $_GET['page'];
                        $categ = $_GET['txn_cat'];
                        $_SESSION['txn_cat_id'] = $_GET['txn_cat'] ?? 1;
                        if ($cat['page_action'] == $page && $cat['txn_category_id'] == $categ) {
                            $txnlink = $cat['txn_link'];
                            ?>
             <div class="col-12 p-0" id="">
                <?php include_once $txnlink; 
                    if($merchant_id == 13 ) //document processing
                    { ?>
              <!-- Button trigger modal -->
<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#docuTerms" id="openDocuTerms">
  Document Terms
</button>

<!-- Modal -->
<div class="modal fade" style="height:95vh;" id="docuTerms" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="docuTermsLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content overflow-y">
      <div class="modal-header bg-yellow text-light">
        <h1 class="modal-title fs-5 fw-bold" id="docuTermsLabel">EZRides Document Processing Service Terms</h1>
        <button type="button" class="btn btn-close text-light" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<div class="">

<div class="mb-3">
    <h2 class="h6">1. What We Deliver</h2>
    <p>Official, Legal, Business, and personal documents available from this Merchant<br>No cash, valuables, or hazardous items.</p>
</div>

<div class="mb-3">
    <h2 class="h6">2. Service Area</h2>
    <p>Only within our operational region.<br>Covers specific offices and businesses.</p>
</div>

<div class="mb-3">
    <h2 class="h6">3. Handling & Security</h2>
    <p>We respect your privacy; documents stay sealed.<br>Use secure packaging for sensitive items.</p>
</div>

<div class="mb-3">
    <h2 class="h6">4. Limits</h2>
    <p>Max number: 10 Documents.
</div>

<div class="mb-3">
    <h2 class="h6">5. Delivery Time</h2>
    <p>Standard: Within 2-3 Business Days.</p>
</div>

<div class="mb-3">
    <h2 class="h6">6. Identification</h2>
    <p>Show valid ID for pickup and delivery.<br>Signature required on delivery.</p>
</div>

<div class="mb-3">
    <h2 class="h6">7. Changes & Cancellations</h2>
    <p>No Cancellation.</p>
</div>

<div class="mb-3">
    <h2 class="h6">9. Liability</h2>
    <p>Limited coverage for loss or damage.
</div>

</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Understood</button>
        
      </div>
    </div>
  </div>
</div>
                    <?php }
                ?>
            </div>
            <?php }
                    }
                }
                $cat = null;

                if (!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] == 'home')) { ?>

            <div class="col-lg-6">
                <?php include_once "_index_wallet.php"; ?>
            </div>
            <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                        <h1 class="fw-bold display-6 m-5"> Welcome! </h1>
                        <p class="fw-light fs-6">Going Somewhere? Craving for some Burger? Need to Process Documents? Groceries? Pharmacy?</p>
                        <p class="fs-4">EZ Rides</p>
                    </div> -->

            <div class="col-12 col-lg-12">
                <div class="container-fluid">
                    <div class="row gx-1 gy-1">
                        <?php
                                    $txn_cats = select_data("txn_category", "txn_category_status='A'", "txn_category_id", 100);

                                    if ($_SESSION['t_user_type'] == 'C') {
                                        foreach ($txn_cats as $tcat) {
                                            appButton($tcat['icon_class'], $tcat['txn_category_id'], $tcat['page_action'], $tcat['txn_category_name']);
                                        }

                                        appButton('document.png','6','shop','DOCUMENT','&merchant=13');
                                        appButton('groc-delivery.png','6','shop','GROCERY','&merchant=1');
                                        appButton('rx-delivery.png','6','shop','PHARMACY','&merchant=11');
                                        appButton('delivery-guy-icon.png','6','shop','FOOD','&merchant=12');
                                    } ?>
                        <div class="col-4 col-lg-1 col-md-3 col-sm-4 text-center">
                            <a href="./_profile/" class="btn btn-outline-light bg-yellow shadow rounded-4 w-100">
                                <img src="../icons/settings.png" alt="" class="quick-links img-fluid" style="height:7vh;">
                                <br>
                                <span class="small fw-bold" style="font-size:10px">ACCOUNT</span>
                            </a>
                        </div>

                    </div>

                    <div class="row gx-2">
                        <div class="col-12 col-lg-6">
                            <div class="card mt-2 mb-2 shadow-lg">
                                <div class="card-header bg-purple text-light">
                                    <span class="fs-4 fw-bold">TRIPS</span>
                                </div>
                                <div class="card-body">
                                    <?php
                                                $pastTrips = query("SELECT * from `view_angkas_bookings` WHERE customer_user_id = ? AND angkas_booking_reference like 'ANG%' order by date_booked DESC limit 10", [USER_LOGGED]);
                                                foreach($pastTrips as $ps){ ?>

                                    <div
                                        class="alert alert-light mb-1 rounded-0 border-success border-start-5 border-top-1 border-bottom-1 border-end-1">
                                        <span
                                            class="fw-bold fs-5 float-start"><?php echo $ps['angkas_booking_reference'];?></span>
                                        <span class="fw-light float-end"><?php echo $ps['date_booked'];?></span>
                                        <br><br>
                                        You went to <span class="fw-bold"><?php echo $ps['form_to_dest_name'];?></span>
                                        and spent Php <?php echo $ps['form_Est_Cost'];?>
                                    </div>

                                    <?php }
                                                $ps = null; $pastTrips = null;
                                            ?>
                                </div>
                            </div>
                        </div>
                        <!-- modal for the image preview -->
                        <div id="imageModal" class="modal modal-img" style="display: none;">
                            <span class="close">&times;</span>
                            <img class="modal-img-content">
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card mt-2 mb-2">
                                <div class="card-header bg-purple text-light">
                                    <span class="fs-4 fw-bold">DELIVERIES</span>
                                </div>
                                <div class="card-body">
                                    <?php
                                                $pastDelheader = query("SELECT distinct so.shop_order_ref_num as shop_ref
                                                                                      , ab.angkas_booking_reference as rider_ref
                                                                                      , ab.shop_cost shop_fee
                                                                                      , ab.form_Est_Cost rider_fee
                                                                                      , ab.date_booked
                                                                                      , sm.name shop_name
                                                                                      , ab.booking_status
                                                                      from `angkas_bookings`  ab
                                                                      join `shop_orders` so
                                                                        on ab.shop_order_reference_number = so.shop_order_ref_num
                                                                      join `shop_items` si
                                                                        on so.item_id = si.item_id
                                                                      join `shop_merchants` sm
                                                                        on sm.merchant_id = si.merchant_id
                                                                     WHERE ab.user_id = ? 
                                                                       AND angkas_booking_reference NOT like 'ANG%'
                                                                     order by date_booked DESC limit 10", [USER_LOGGED]);
                                                foreach($pastDelheader as $ps){ ?>
                                    <div
                                        class="alert alert-light mb-1 rounded-0 border-success border-start-5 border-top-1 border-bottom-1 border-end-1">
                                        <span class="fw-bold fs-5 float-start"><?php echo $ps['rider_ref'];?> (
                                            <?php echo $ps['shop_ref'];?> )</span>
                                        <span class="fw-light float-end"><?php echo $ps['date_booked'];?></span>
                                        <br><br>
                                        You shopped to <span class="fw-bold"><?php echo $ps['shop_name'];?></span> and
                                        spent Php <?php echo $ps['rider_fee'];?> for the Delivery fee and Php
                                        <?php echo $ps['shop_fee'];?> for the following delivery items:
                                        <br>
                                        <div class="clear-fix">
                                            <?php
                                                                $pastDelDetails = query("SELECT si.item_name, so.quantity, si.price, si.item_img
                                                                    from`shop_orders` so
                                                                    join `shop_items` si
                                                                    on so.item_id = si.item_id
                                                                WHERE so.shop_order_ref_num = ?
                                                                order by so.order_date DESC limit 5", [$ps['shop_ref'] ]);
                                                                foreach($pastDelDetails as $pd ){ ?>
                                            <a href="#" data-bs-toggle="tooltip"
                                                data-bs-title="<?php echo $pd['item_name'] . " x " . $pd['quantity'] . "pcs @ Php " . $pd['price'] ;?>">
                                                <img src="_shop/item-img/<?php echo $pd['item_img'];?>" alt=""
                                                    class="img-fluid object-fit-cover"
                                                    style="width:15%;display:inline-block">
                                            </a>
                                            <?php }
                                            $current_progress=null;
                                            $stat1 = "btn-secondary";
                                            $stat2 = "btn-secondary";
                                            $stat3 = "btn-secondary";
                                            switch($ps['booking_status']){
                                                case 'P': $current_progress= '0%';
                                                            $stat1 = "btn-warning";
                                                            $stat2 = "btn-secondary";
                                                            $stat3 = "btn-secondary"; 
                                                        break;

                                                case 'A': $current_progress= '50%';
                                                $stat1 = "btn-success";
                                                $stat2 = "btn-warning";
                                                $stat3 = "btn-secondary"; 
                                                    break;
                                                
                                                case 'R': $current_progress= '50%';
                                                    $stat1 = "btn-success";
                                                    $stat2 = "btn-success";
                                                    $stat3 = "btn-secondary"; 
                                                        break;

                                                case 'I': $current_progress= '100%';
                                                $stat1 = "btn-success";
                                                $stat2 = "btn-success";
                                                $stat3 = "btn-warning"; 
                                                    break;

                                                case 'C': $current_progress= '100%';
                                                $stat1 = "btn-success";
                                                $stat2 = "btn-success";
                                                $stat3 = "btn-success"; 
                                                    break;
                                                
                                                default: $current_progress= '0%';
                                                $stat1 = "btn-secondary";
                                                $stat2 = "btn-secondary";
                                                $stat3 = "btn-secondary"; 
                                                    
                                            }
                                                            ?>

                                        </div>
                                        <div class="position-relative m-4">
                                            <div class="progress" role="progressbar" aria-label="Progress"
                                                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"
                                                style="height: 1px;">
                                                <div class="progress-bar"
                                                    style="width: <?php echo $current_progress;?>"></div>
                                            </div>
                                            <button type="button"
                                                class="position-absolute top-0 start-0 translate-middle btn btn-sm <?php echo $stat1; ?> rounded-pill"
                                                style="width: 2rem; height:2rem;">1</button>
                                            <button type="button"
                                                class="position-absolute top-0 start-50 translate-middle btn btn-sm <?php echo $stat2; ?> rounded-pill"
                                                style="width: 2rem; height:2rem;">2</button>
                                            <button type="button"
                                                class="position-absolute top-0 start-100 translate-middle btn btn-sm <?php echo $stat3; ?> rounded-pill"
                                                style="width: 2rem; height:2rem;">3</button>
                                        </div>

                                    </div>

                                    <?php }
                                            ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>


       
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/js/coreui.bundle.min.js"
    integrity="sha384-kwU8DU7Bx4h5xZtJ61pZ2SANPo2ukmbAwBd/1e5almQqVbLuRci4qtalMECfu9O6" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>

<script>
$(document).ready(function() {

    // Get the current URL parameters
const urlParams = new URLSearchParams(window.location.search);
const merchant = urlParams.get('merchant');

// Check if merchant parameter equals 13
if (merchant === '13') {
  document.getElementById("openDocuTerms").click();
}

    // When image link is clicked
    $('a[data-bs-toggle="tooltip"]').on('click', function(event) {
        event.preventDefault(); // Prevent default link behavior

        const imageUrl = $(this).find('img').attr('src'); // Get the image URL
        $('.modal-img-content').attr('src', imageUrl); // Set the modal image source

        $('.modal-img').fadeIn(); // Show the modal
    });

    // Close the modal when 'x' is clicked
    $('.close').on('click', function() {
        $('.modal-img').fadeOut();
    });

    // Close the modal when clicking outside the image
    $(window).on('click', function(event) {
        if ($(event.target).is('.modal-img')) {
            $('.modal-img').fadeOut();
        }
    });


    function updateRangeColor(element) {
        let value = $(element).val();
        let color;
        switch (value) {
            case '3':
                color = 'green';
                break;
            case '4':
                color = 'red';
                break;
            default:
                color = '#007bff'; // Default Bootstrap primary color
        }
        $(element).css('accent-color', color); // Change the thumb and track color
    }

    $('.colorRange').on('input change', function() {
        updateRangeColor(this); // Pass the current element to the function
    });

    // Initialize colors on page load
    $('.colorRange').each(function() {
        updateRangeColor(this);
    });

});
</script>

<?php 
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {  
        case 'rent': ?> <script src="./_car_rental.js"></script>
<?php break;  
        case 'angkas': ?>
<?php break; 
        case 'shop': ?>
<script src="_process_ajax.js"></script>
<script src="./_shop.js"></script>
<?php break;
        default: ?>
<script src="_process_ajax.js"></script>
<?php }
} else { ?>
<script src="_process_ajax.js"></script>
<?php } ?>

</html>
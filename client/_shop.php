<?php
include_once "../_db.php";
include_once "./_shop/_class_grocery.php";
include_once "./_shop/func.php";


if(!empty($_SESSION['user_profile'])){
    extract($_SESSION['user_profile']);
    $u_fullname = $user_lastname . ", " . $user_firstname . ", " . $user_mi;
    $u_contact = $user_contact_no;
}

// Fetch all products from the database
$products = Product::fetchAllProducts( CONN);

// Get the total number of products
$totalProducts = count( $products );

// Define the number of items per page
$itemsPerPage = 15;

// Calculate the total number of pages
$totalPages = ceil( $totalProducts / $itemsPerPage );

// Get the current page from the query string, default to page 1
$currentPage = isset( $_GET['pagination'] ) ? ( int )$_GET['pagination'] : 1;

// Ensure the current page is within valid bounds
if ( $currentPage < 1 ) {
    $currentPage = 1;
} elseif ( $currentPage > $totalPages ) {
    $currentPage = $totalPages;
}

// Calculate the starting index for the items to display
$startIndex = ( $currentPage - 1 ) * $itemsPerPage;

// Slice the products array to get the items for the current page
$currentProducts = array_slice( $products, $startIndex, $itemsPerPage );
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Document</title>
    <style>
        #merchantSuggestions {
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        .modal {
            overflow-y: visible !important;
        }

        .autocomplete-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            background-color: white;
            position: absolute;
            width: 100%;
            z-index: 1051;
            /* Ensure it's above other elements */
        }

        .autocomplete-item {
            padding: 8px;
            cursor: pointer;
        }

        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }

    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places&loading=async"></script>
    <!--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&libraries=places"></script>-->
    <script>
        // When the "Get Current Location" button is clicked

    </script>
    <script type="module">
        "use strict";

        let autocomplete;

        function initAutocomplete() {
            const shippingAddressInput = document.getElementById('shippingAddress');
            const pacContainer = document.getElementById('pacContainer');
            const coordinatesInput = document.getElementById('AddressCoordinates');

            // Initialize Autocomplete
            autocomplete = new google.maps.places.Autocomplete(shippingAddressInput, {
                types: ['geocode'],
                fields: ['geometry', 'address_components', 'name']
            });

            // Listen for the "place_changed" event when a place is selected
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    alert('No details available for the selected address');
                    return;
                }

                // Get the coordinates from the place's geometry
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();

                // Update the AddressCoordinates input with the coordinates
                coordinatesInput.value = `${lat}, ${lng}`;
            });

            // Handle the suggestion list when user types
            shippingAddressInput.addEventListener('input', function() {
                const query = shippingAddressInput.value;

                if (query.length < 3) {
                    pacContainer.innerHTML = ''; // Clear the container if the query is too short
                    return;
                }

                // Get place predictions
                const service = new google.maps.places.AutocompleteService();
                service.getPlacePredictions({
                    input: query,
                    types: ['geocode']
                }, function(predictions, status) {
                    if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
                        pacContainer.innerHTML = ''; // Clear the container if no predictions
                        return;
                    }

                    // Create list items from the predictions
                    pacContainer.innerHTML = ''; // Clear previous results
                    predictions.forEach(function(prediction) {
                        const div = document.createElement('span');

                        const classesToAdd = [
                            'badge',
                            'rounded-pill',
                            'text-secondary',
                            'fw-light',
                            'd-block',
                            'text-start',
                            'autocomplete-item'
                        ];

                        classesToAdd.forEach(className => {
                            div.classList.add(className);
                        });
                        div.textContent = prediction.description;

                        // Add click event to each suggestion
                        div.addEventListener('click', function() {
                            shippingAddressInput.value = prediction.description;
                            pacContainer.innerHTML = ''; // Clear suggestions

                            // Trigger the "place_changed" event manually by using PlacesService to get the details
                            const placeService = new google.maps.places.PlacesService(document.createElement('div'));
                            placeService.getDetails({
                                placeId: prediction.place_id
                            }, function(placeDetails, status) {
                                if (status === google.maps.places.PlacesServiceStatus.OK) {
                                    // Extract the coordinates from the place details
                                    const lat = placeDetails.geometry.location.lat();
                                    const lng = placeDetails.geometry.location.lng();

                                    // Update the AddressCoordinates input with the coordinates
                                    coordinatesInput.value = `${lat}, ${lng}`;
                                }
                            });
                        });

                        pacContainer.appendChild(div);
                    });
                });
            });
        }

        // Initialize the Autocomplete only when the modal is shown
        $('#checkoutModal').on('shown.bs.modal', function() {
            initAutocomplete();
        });

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    </script>

</head>

<body>
    <?php include_once "XXnav-client.php";?>

    <div class="container-fluid">
        <div class="row  gx-1">
            <div class="col-12">
                <div id="TransactionStatus" class="mt-1"></div>
            </div>
        </div>
        <div class="row  gx-1">
            <div class="col-12">
                <div class="container-fluid">
                    <div class="row mt-3">
                        <div class="col-lg-12 col-sm-12 clear-fix">

                            <!-- Button to Show Cart Items -->
                            <button id="ShowCartItems" class="btn btn-primary position-relative float-end mx-2 " data-bs-toggle="collapse" data-bs-target="#CartItems" aria-expanded="false" aria-controls="CartItems">
                                <!-- Cart Count Badge -->
                                <span id="cartCountBadge" class="position-absolute z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <!-- Count will be inserted here -->
                                </span>

                                <!-- Cart Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                </svg>
                            </button>

                            <button class="btn btn-warning position-relative mx-2 float-end">

                                <span class="position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    99+
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
                                </svg>
                            </button>

                            <button class="d-none btn btn-success position-relative mx-2 btn-checkout float-end"> <small class="small fw-lighter">Check Out</small>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check-fill" viewBox="0 0 16 16">
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
                                </svg>
                            </button>


                            <a class="btn btn-secondary bg-purple text-white position-relative float-end mx-2" href="../">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
            <div class="col-12">
                <div class="collapse CartItems row gx-1 pt-2" id="CartItems">No Cart Items</div>
            </div>
            <div class="col-12" id="totalAmount"></div>

            <div class="col-12">

                <!-- Modal for Checkout Summary -->
                <div id="checkoutModal" class="modal fade" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="checkoutModalLabel">Order Summary</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formPlaceOrder">
                                <div class="modal-body">
                                    <input type="text" readonly class="form-control text-secondary fw-bold border-0" name="shopReferenceNumber" id="shopReferenceNum" value="<?php echo gen_book_ref_num(8,"SHOP"); ?>">
                                    <input type="hidden" id="userLogged" value="<?php echo $_SESSION['user_id'];?>" class="form-control">
                                    <hr class="p-0 m-0">
                                    <table class="table table-sm" id="CheckOutItems"></table> <!-- Order Summary Items go here -->

                                    <!-- Shipping Details -->
                                    <div class="mt-3">
                                        <div class="card">
                                            <div class="card-header fw-bold">Merchant Info</div>
                                            <div class="card-body" id="MerchantInfo">
                                                <small class="small fw-bold" id="MerchantName"></small> <br>
                                                <small class="small fw-light" id="MerchantAddress"></small>
                                                <small class="small fw-light" id="ContactInfo"></small>
                                                <input type="text" class="form-control fw-light" id="MerchantLocCoor"/>
                                                
                                            </div>
                                        </div>
                                        <h6>Shipping Details</h6>
                                        <input type="text" id="shippingName" name="shipingName" class="form-control mb-2" placeholder="Full Name" value="<?php echo $u_fullname != NULL ? $u_fullname : NULL; ?>" required>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" id="shippingAddress" name="shippingAddress" class="form-control" placeholder="Address" required>
                                            <button id="getCurrentLocation" type="button" data-bs-toggle="tooltip" data-bs-title="Current Location" class="btn btn-outline-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" fill="currentColor" class="bi bi-crosshair" viewBox="0 0 16 16">
                                                    <path d="M8.5.5a.5.5 0 0 0-1 0v.518A7 7 0 0 0 1.018 7.5H.5a.5.5 0 0 0 0 1h.518A7 7 0 0 0 7.5 14.982v.518a.5.5 0 0 0 1 0v-.518A7 7 0 0 0 14.982 8.5h.518a.5.5 0 0 0 0-1h-.518A7 7 0 0 0 8.5 1.018zm-6.48 7A6 6 0 0 1 7.5 2.02v.48a.5.5 0 0 0 1 0v-.48a6 6 0 0 1 5.48 5.48h-.48a.5.5 0 0 0 0 1h.48a6 6 0 0 1-5.48 5.48v-.48a.5.5 0 0 0-1 0v.48A6 6 0 0 1 2.02 8.5h.48a.5.5 0 0 0 0-1zM8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div id="pacContainer">
                                        </div>
                                        <input type="hidden" readonly class="form-control mb-2 form-control-sm text-secondary" id="AddressCoordinates">
                                        <input type="text" id="shippingPhone" class="form-control" value="<?php echo $u_contact;?>" placeholder="Phone Number" required>

                                    </div>

                                    <!-- Payment Details -->
                                    <div class="mt-3">
                                        <h6 class="fw-bolder">Payment Details</h6>
                                        <div id="PaymentDetails">
                                            <table class="table table-responsive">
                                                <tr>
                                                    <th>Shipping Fee</th>
                                                    <td id="ShippingFee">150.00</td>
                                                </tr>
                                                <tr>
                                                    <th>Voucher</th>
                                                    <td> <input id="VoucherCode" type="text" class="form-control form-control-sm" Placeholder="Voucher Code"></td>
                                                </tr>
                                                <tr>
                                                    <td id="voucherInfo" colspan="2"></td>
                                                </tr>
                                                <tr>
                                                    <th>Amount to Pay (Php)</th>
                                                    <td id="FinalAmountToPay"></td>
                                                </tr>
                                            </table>

                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="checkWalletPaymentMode" checked>
                                            <label class="form-check-label" for="checkWalletPaymentMode">

                                                <span class="badge bg-purple">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                                                        <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z" />
                                                    </svg> Wallet Balance:
                                                    <span class="WalletBalance"></span>
                                                </span>
                                            </label>
                                        </div>



                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="placeOrderBtn" class="btn btn-outline-secondary fw-bold text-secondary bg-purple">Book <span class="text-purple fw-bolder">EZ</span>Pabili</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <form>
                    <input type="text" class="rounded-4 form-control my-3" id="SearhItems" placeholder="Search Items">
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Pagination Links -->

            <nav aria-label="Page navigation" class="my-2 shop-navigation">
                <ul class="pagination justify-content-center">
                    <?php if ( $currentPage > 1 ): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage - 1; ?>">Previous</a>
                    </li>
                    <?php endif;
?>

                    <?php for ( $i = 1; $i <= $totalPages; $i++ ): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $i; ?>"><?php echo $i;
?></a>
                    </li>
                    <?php endfor;
?>

                    <?php if ( $currentPage < $totalPages ): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage + 1; ?>">Next</a>
                    </li>
                    <?php endif;
?>
                </ul>
            </nav>
        </div>
        <div class="row g-1" id="searchResults">
            <?php 
                        foreach ( $currentProducts as $product ){
                        
                        $oosBtn = $product->getQuantity() == 0 ? "disabled" : "";
                        ?>
            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-2 col-6 mb-3 mb-sm-1">

                <div class="card">
                    <div class="card-header">
                        <img src="./_shop/item-img/<?php echo ($product->getItemImg() == NULL) ? 'default-groc.png' : $product->getItemImg() ; ?>" alt="" class="card-img-top">
                    </div>
                    <div class="card-body overflow-y-scroll" style="height:25vh">
                        <span class="card-title fw-bold"><?php echo htmlspecialchars( $product->getName() );?></span>
                        <p class="card-text">
                            <span class="badge rounded-pill text-bg-warning">
                                <?php echo htmlspecialchars( $product->getMerchantName() );?>
                            </span>
                            <br>
                            Price: $<?php echo number_format( $product->getPrice(), 2 ); ?>
                            <br>
                            <?php echo $product->getQuantity() == 0 ? "<span class='text-danger'>Out of Stock</span>" : $product->getQuantity() . " in stock" ; ?>
                        </p>

                        <form class="form-add-basket" id="formAddBasket" item-submit-id="<?php echo $product->getId(); ?>">
                            <div class="input-group">
                                <input type="hidden" name="item_id" value="<?php echo $product->getId(); ?>">
                                <input type="text" class="form-control" name="quantity" value="1" <?php echo $oosBtn; ?>>
                                <span class="input-group-text">pcs</span>
                                <button type="submit" class="btn btn-success btn-sm add-basket-btn" <?php echo $oosBtn; ?>>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5" />
                                        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                    </svg>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>



            </div>
            <?php } ?>
        </div>

    </div>


</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_shop.js"></script>

</html>

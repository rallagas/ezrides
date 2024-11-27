<?php
include_once "../_db.php";
include_once "./_shop/_class_grocery.php";
include_once "./_shop/func.php";

if(isset($_SESSION['txn_cat_id'])){
    $txn_cat = $_SESSION['txn_cat_id'];
}

if (!empty($_SESSION['user_profile'])) {
    extract($_SESSION['user_profile']);
    $u_fullname = $user_lastname . ", " . $user_firstname . ", " . $user_mi;
    $u_contact = $user_contact_no;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places&loading=async">
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
                        const placeService = new google.maps.places.PlacesService(
                            document.createElement('div'));
                        placeService.getDetails({
                            placeId: prediction.place_id
                        }, function(placeDetails, status) {
                            if (status === google.maps.places
                                .PlacesServiceStatus.OK) {
                                // Extract the coordinates from the place details
                                const lat = placeDetails.geometry.location
                                    .lat();
                                const lng = placeDetails.geometry.location
                                    .lng();

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

    //  Initialize the Autocomplete only when the modal is shown
    if ($('#checkoutModal').length > 0) {
        $('#checkoutModal').on('shown.bs.modal', function() {
            initAutocomplete();
        });
    }

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>


</head>



<body>
    <?php require_once "nav-client.php"; ?>

    <div class="container-fluid d-none" id="shopHistory">

        <ul class="nav nav-pills nav-justified justify-content-center" id="shopHistoryNav" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="shoplist-tab" data-bs-toggle="tab" data-bs-target="#shop-list-pane"
                    type="button" role="tab" aria-controls="shop-list-pane" aria-selected="true">
                    <span class="small fw-bold">Unpaid Shop List</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pnd-tab" data-bs-toggle="tab" data-bs-target="#paid-no-driver-pane"
                    type="button" role="tab" aria-controls="paid-no-driver-pane" aria-selected="false">
                    <span class="small fw-bold">Paid No Rider</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pwd-tab" data-bs-toggle="tab" data-bs-target="#paid-with-driver-pane"
                    type="button" role="tab" aria-controls="paid-with-driver-pane" aria-selected="false">
                    <span class="small fw-bold">Paid With Rider</span>
                </button>
            </li>
        </ul>
        <div class="tab-content px-2" id="shopHistoryTabs">
            <div class="tab-pane fade show active" id="shop-list-pane" role="tabpanel" aria-labelledby="shoplist-tab"
                tabindex="0">Loading...</div>
            <div class="tab-pane fade" id="paid-no-driver-pane" role="tabpanel" aria-labelledby="pnd-tab" tabindex="0">
                Loading</div>
            <div class="tab-pane fade" id="paid-with-driver-pane" role="tabpanel" aria-labelledby="pwd-tab"
                tabindex="0">.3..</div>
        </div>
    </div>
    <div class="container-fluid mx-1">
        <div class="row g-1">

            <div class="col-12 clear-fix px-2">
                <input type="text" class="rounded-4 form-control form-control-sm d-flex w-100 my-1" id="SearhItems"
                    placeholder="Search Items, Merchants">
            </div>
        </div>
    </div>

    <?php if(!isset($_GET['merchant'])){ ?>
    <div class="container-fluid mx-1">
        <div class="row g-1">
            <?php 
        try {
            // Fetch all merchants using the Merchant class
            $merchants = Merchant::getAllMerchants();
            
            // Check if merchants are available
            if (!empty($merchants)) {
                foreach ($merchants as $merchant) { 
                    // Ensure merchant data is valid
                    if ($merchant instanceof Merchant) { ?>
            <div class="col-6 mb-3">

                <div class="card shadow" style="height:40vh">
                    <div class="card-header p-0">
                        <img src="../images/<?php echo  $merchant->getMerchantImg();?>" alt=""
                            class="card-img-top object-fit-cover" style="height:20vh;">
                    </div>
                    <div class="card-body">
                        <div class="col-12 card-title"
                            style="overflow:hidden;text-overflow:ellipsis;white-space: nowrap;">
                            <span class="fs-6 "><?php echo $merchant->getName(); ?></span>
                        </div>
                        <a class="btn btn-warning rounded-4 border-0"
                            href="?page=<?php echo $_GET['page'] ?? ''; ?>&txn_cat=<?php echo $txn_cat; ?>&merchant=<?php echo $merchant->getId(); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-shop mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zm3 0h-2v3h2z" />
                            </svg>
                        </a>
                    </div>
                    <div class="card-footer badge text-bg-warning p-1 pb-2">
                        <span class="w-100"><?php echo $merchant->getMerchantType();?></span>
                    </div>
                </div>

            </div>
            <?php } else { ?>
            <div class="col-12">
                <p class="text-center text-danger">Invalid merchant data found.</p>
            </div>
            <?php }
                }
            } else { ?>
            <div class="col-12">
                <p class="text-center">No merchants found.</p>
            </div>
            <?php } 
        } catch (Exception $e) { ?>
            <div class="col-12">
                <p class="text-center text-danger">An error occurred while fetching merchants:
                    <?php echo htmlspecialchars($e->getMessage()); ?></p>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>


    <?php
if (isset($_GET['merchant'])) {
    // Sanitize the merchant ID to prevent potential SQL injection or other attacks
    $merchant_id = $_GET['merchant'];
    
    try {
        // Fetch all products for the specified merchant ID
        $products = Product::fetchByMerchantId($merchant_id);

        // Check if products were successfully fetched
        if (is_array($products)) {
            // Get the total number of products
            $totalProducts = count($products);

            // Define the number of items per page
            $itemsPerPage = 15;

            // Calculate the total number of pages
            $totalPages = ceil($totalProducts / $itemsPerPage);

            // Get the current page from the query string, default to page 1
            $currentPage = isset($_GET['pagination']) ? (int) $_GET['pagination'] : 1;

            // Ensure the current page is within valid bounds
            $currentPage = max(1, min($currentPage, $totalPages));

            // Calculate the starting index for the items to display
            $startIndex = ($currentPage - 1) * $itemsPerPage;

            // Slice the products array to get the items for the current page
            $currentProducts = array_slice($products, $startIndex, $itemsPerPage); 
        } else {
            // Handle case where products fetch fails or returns invalid data
            $products = [];
            $currentProducts = [];
            $totalProducts = 0;
            $totalPages = 0;
            $currentPage = 1;
            $startIndex = 0;
        }
    } catch (Exception $e) {
        // Log the error and display a user-friendly message
        error_log($e->getMessage());
        $products = [];
        $currentProducts = [];
        $totalProducts = 0;
        $totalPages = 0;
        $currentPage = 1;
    }

?>

    <div class="bg-dark bg-opacity-75 py-2 position-fixed bottom-0 end-0 d-inline-block z-3">
        <!-- Button to Show Cart Items -->


        <button
            class="d-none shadow btn btn-warning border-2 border-light btn-lg position-relative mx-1 btn-checkout float-end"
            data-bs-toggle="tooltip" data-bs-title="Check Out" data-bs-placement="left">

            <span>Check Out </span>

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-cart-check-fill" viewBox="0 0 16 16">
                <path
                    d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1.646-7.646-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708" />
            </svg>
        </button>
        <button id="ShowCartItems" class="shadow btn btn-warning btn-lg position-relative float-end mx-1"
            data-bs-toggle="collapse" data-bs-target="#CartItems" aria-expanded="false" aria-controls="CartItems">
            <span id="cartCountBadge"
                class="position-absolute z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart"
                viewBox="0 0 16 16">
                <path
                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
            </svg>
        </button>
        <button
            class="HideOrderHistory toggleBtnOrderHist d-none shadow btn btn-lg btn-primary position-relative mx-1 float-end">
            <span class="position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box2"
                viewBox="0 0 16 16">
                <path
                    d="M2.95.4a1 1 0 0 1 .8-.4h8.5a1 1 0 0 1 .8.4l2.85 3.8a.5.5 0 0 1 .1.3V15a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.5a.5.5 0 0 1 .1-.3zM7.5 1H3.75L1.5 4h6zm1 0v3h6l-2.25-3zM15 5H1v10h14z" />
            </svg>
        </button>
        <button
            class="ShowOrderHistory toggleBtnOrderHist shadow btn btn-lg btn-primary position-relative mx-1 float-end">
            <span
                class="MyShopListCountBadge position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam"
                viewBox="0 0 16 16">
                <path
                    d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
            </svg>
        </button>
    </div>
    <!-- Main Shop -->
    <div class="container-fluid" id="MainShop">
        <div class="row  gx-1">
            <div class="col-12 p-0">
 <nav aria-label="breadcrumb small mb-0">
  <ol class="breadcrumb m-0" style="font-size:2vh">
    <li class="breadcrumb-item small"><a class="text-decoration-none" href="index.php">Home</a></li>
    <li class="breadcrumb-item small">
      <a class="text-decoration-none" href="?page=<?php echo htmlspecialchars($_GET['page']); ?>&txn_cat=<?php echo htmlspecialchars($txn_cat); ?>">Merchants</a>
    </li>
    <li class="breadcrumb-item active small" aria-current="page">
      <?php
        // Fetch merchant ID from query string or other sources
        $merchantId = isset($_GET['merchant']) ? intval($_GET['merchant']) : null;
        if ($merchantId) {
            try {
                $merchantName = Merchant::getMerchantNamesById($merchantId);
                echo !empty($merchantName) ? $merchantName[0] : "Unknown Merchant";
            } catch (Exception $e) {
                echo "Error fetching merchant name";
            }
        } else {
            echo "Merchant not specified";
        }
      ?>
    </li>
  </ol>
</nav>

                <div id="TransactionStatus"></div>
            </div>
        </div>
        <div class="row gx-1">
            <div class="col-12">
                <div class="collapse CartItems row gx-1 py-1 ps-2 bg-success small border border-5 border-start-0 border-end-0 border-bottom-0 border-success bg-opacity-75"
                    id="CartItems">
                    No Cart Items
                </div>
            </div>
            <div class="col-12">
                <div class="modal fade" id="checkoutModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
                    tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-purple text-light">
                                <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Order Summary</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="formPlaceOrder">
                                <div class="modal-body overflow-y-scroll" style="height:70vh">
                                    <input type="text" readonly class="form-control text-secondary fw-bold border-0"
                                        name="shopReferenceNumber" id="shopReferenceNum">
                                    <input type="hidden" id="userLogged" value="<?php echo $_SESSION['user_id']; ?>"
                                        class="form-control">
                                    <hr class="p-0 m-0">
                                    <table class="table table-sm" id="CheckOutItems"></table>
                                    <!-- Order Summary Items go here -->
                                    <!-- Shipping Details -->
                                    <div class="mt-3">
                                        <div
                                            class="card rounded-0 border-info border-5 border-top-0 border-bottom-0 border-end-0 border-start-5">
                                            <div class="card-body bg-info opacity-75 text-tertiary-body"
                                                id="MerchantInfo">
                                                <small class="small fw-bold">Shop in </small>
                                                <small class="small fw-bold" id="MerchantName"></small> <br>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                                                    <path
                                                        d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                                                </svg>
                                                <small class="small fw-light" id="MerchantAddress"></small>
                                                <small class="small fw-light" id="ContactInfo"></small>
                                                <input type="hidden" id="MerchantLocCoor" name="MerchantLocCoor"
                                                    class="form-control fw-light" id="MerchantLocCoor" />
                                            </div>
                                        </div>
                                        <div class="input-group input-group-sm my-2">
                                            <input type="text" id="shippingAddress" name="shippingAddress"
                                                class="form-control form-control-sm border-warning"
                                                placeholder="Shipping Address" required>
                                            <button id="getCurrentLocation" type="button" data-bs-toggle="tooltip"
                                                data-bs-title="Current Location" class="btn btn-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10"
                                                    fill="currentColor" class="bi bi-crosshair" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.5.5a.5.5 0 0 0-1 0v.518A7 7 0 0 0 1.018 7.5H.5a.5.5 0 0 0 0 1h.518A7 7 0 0 0 7.5 14.982v.518a.5.5 0 0 0 1 0v-.518A7 7 0 0 0 14.982 8.5h.518a.5.5 0 0 0 0-1h-.518A7 7 0 0 0 8.5 1.018zm-6.48 7A6 6 0 0 1 7.5 2.02v.48a.5.5 0 0 0 1 0v-.48a6 6 0 0 1 5.48 5.48h-.48a.5.5 0 0 0 0 1h.48a6 6 0 0 1-5.48 5.48v-.48a.5.5 0 0 0-1 0v.48A6 6 0 0 1 2.02 8.5h.48a.5.5 0 0 0 0-1zM8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div id="pacContainer"></div>
                                        <input type="text" id="shippingName" name="shipingName"
                                            class="form-control form-control-sm mb-2 border border-secondary border-opacity-50"
                                            placeholder="Full Name"
                                            value="<?php echo $u_fullname != NULL ? $u_fullname : NULL; ?>" required>
                                        <input type="text" id="shippingPhone"
                                            class="form-control form-control-sm mb-2 border border-secondary border-opacity-50"
                                            value="<?php echo $u_contact; ?>" placeholder="Phone Number" required>
                                        <input type="hidden" readonly
                                            class="form-control mb-2 form-control-sm text-secondary"
                                            id="AddressCoordinates">
                                        <div
                                            class="input-group input-group-sm border rounded border-secondary border-opacity-50 mb-2">
                                            <span class="input-group-text small border-0">Distance</span>
                                            <input type="text" class=" border-0 form-control form-control-sm"
                                                id="formDistanceKM" required>
                                            <span class=" border-0 small input-group-text">KM</span>
                                        </div>
                                        <div
                                            class="input-group input-group-sm border rounded border-secondary border-opacity-50  mb-2">
                                            <span class="input-group-text small border-0">Est. Time to Complete
                                                Route</span>
                                            <input type="text" class="form-control form-control-sm  border-0"
                                                id="formETA" required>
                                            <span class="input-group-text small  border-0">Mins</span>
                                        </div>
                                        <div
                                            class="input-group input-group-sm border rounded border-secondary border-opacity-50  mb-2">
                                            <span class="input-group-text border-0">Delivery Cost (Php)</span>
                                            <Input type="text" class="border-0 form-control form-control-sm"
                                                id="formEstimatedCost" required />
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="d-inline-block AlertStatus"></div>
                                    <!--                                    <button id="placeOrderBtn" class="btn btn-outline-secondary fw-bold text-secondary bg-purple">Book <span class="text-purple fw-bolder">EZ</span>Pabili</button>-->
                                    <button id="placeOrderBtn" type="submit" class="btn btn-primary"
                                        data-bs-target="#PaymentModal" data-bs-toggle="modal">Place Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="PaymentModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
                    tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-purple text-light">
                                <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Payment</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Payment Details -->
                                <div class="mt-1">
                                    <div class="order-status mb-1"></div>
                                    <div class="order-details mb-1"></div>
                                    <div id="PaymentDetails">
                                        <table class="table table-responsive">
                                            <tr>
                                                <th>Voucher</th>
                                                <td> <input id="VoucherCode" type="text"
                                                        class="form-control form-control-sm" Placeholder="Voucher Code">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="voucherInfo" colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <th>Amount to Pay (Php)</th>
                                                <td id="FinalAmountToPay" class="FinalAmountToPay"></td>
                                            </tr>
                                        </table>

                                    </div>


                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="checkWalletPaymentMode" checked>
                                        <label class="form-check-label" for="checkWalletPaymentMode">

                                            <span class="shadow badge bg-purple">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z" />
                                                </svg> <span class="small">Pay in Advance thru Wallet Balance: </span>
                                                <span class="walletbalance"></span>
                                            </span>
                                        </label>
                                    </div>


                                </div>
                            </div>
                            <div class="modal-footer">

                                <button class="btn btn-outline-success FinalAmountToPay PayNowBtn" id="PayNowBtn"
                                    data-payshopcost="" data-paydeliveryfee="" data-orderrefnum=""
                                    data-bookingrefnum="">Pay Now</button>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="row">
            <nav aria-label="Page navigation" class="shop-navigation p-0">
                <ul class="pagination pagination-sm justify-content-center m-1">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $currPage; ?>&pagination=<?php echo $currentPage - 1; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                                <path
                                    d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
                            </svg>
                        </a>
                    </li>
                    <?php endif;
                    ?>


                    <?php for ($i = 1; $i < $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link border-0 p-0 pt-0 border-0 bg-light text-secondary"
                            href="?page=<?php echo $currPage; ?>&pagination=<?php echo $i; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-circle-fill" viewBox="0 0 16 16">
                                <circle cx="8" cy="8" r="8" />
                            </svg>
                        </a>
                    </li>
                    <?php endfor;
                    ?>

                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $currPage; ?>&pagination=<?php echo $currentPage + 1; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                <path
                                    d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z" />
                            </svg>
                        </a>
                    </li>
                    <?php endif;
                    ?>
                </ul>
            </nav>
        </div>
        <div class="row g-1" style="height:80vh" id="searchResults">
            <?php
            foreach ($currentProducts as $product) {
                $oosBtn = $product->getQuantity() == 0 ? "disabled" : "";
                ?>
            <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2 col-6 mb-sm-1">
                <div class="card position-relative">
                    <img src="./_shop/item-img/<?php echo ($product->getItemImg() == NULL) ? 'default-groc.png' : $product->getItemImg(); ?>"
                        alt="" class="card-img-top m-0">

                    <div class="card-body border-0 p-0">
                        <div class="container-fluid m-0 p-1">
                            <div class="row">
                                <div class="col-12 text-center"><span
                                        class="card-title fw-bold small"><?php echo htmlspecialchars($product->getName()); ?></span>
                                </div>
                                <div class="col-6">
                                    <p class="card-text">
                                        <span class="badge rounded-pill text-bg-warning">
                                            <?php echo htmlspecialchars($product->getMerchantName()); ?>
                                        </span>
                                        <br> <span class="small fw-bold">Php
                                            <?php echo number_format($product->getPrice(), 2); ?></span>

                                        <br>
                                        <?php echo $product->getQuantity() == 0 ? "<span class='position-absolute top-50 start-50 translate-middle badge p-3 rounded-pill bg-danger opacity-75'>Out of Stock</span>" : null; ?>

                                    </p>
                                </div>
                                <div class="col-6">
                                    <form class="form-add-basket" id="formAddBasket"
                                        item-submit-id="<?php echo $product->getId(); ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $product->getId(); ?>">
                                        <input type="hidden" name="quantity" value="1" <?php echo $oosBtn; ?>>
                                        <button type="submit" class="btn btn-danger btn-lg add-basket-btn float-end"
                                            <?php echo $oosBtn; ?>>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-bag-plus mb-1" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5" />
                                                <path
                                                    d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>



            </div>
            <?php } ?>
        </div>
    </div>
<?php
} else {
    $products = [];
    $currentProducts = [];
    $totalProducts = 0;
    $totalPages = 0;
    $currentPage = 1;
    $startIndex = 0;
}
?>

</body>

</html>
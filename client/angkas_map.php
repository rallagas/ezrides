<?php
require_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Angkas</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/css/coreui.min.css" rel="stylesheet" integrity="sha384-xtmKaCh9tfCPtb3MMyjsQVNn3GFjzZsgCVD3LUmAwbLSU3u/7fIZkIVrKyxMAdzs" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="angkas_map.css">
    <link rel="stylesheet" href="../css/style.css">


</head>

<body>
    <!-- Defined commutes SVGs -->
    <?php include_once "angkas_map_svg.html";?>
    <!-- End commutes SVGs -->

    <?php include_once "nav-client.php";?>
    <div class="offcanvas offcanvas-start bg-purple vh-100" tabindex="-1" id="appMenu" aria-labelledby="appMenu">
        <div class="offcanvas-header">
            <img src="../icons/ezrides.png" alt="" class="img-fluid w-25">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body container-fluid vh-75">
            <div class="row g-1 mb-3">
                <?php  include_once "menu.php"; ?>
            </div>

            <div class="row g-1 mb-3 vh-50 border-1" id="MyBookings">
                <div class="col-sm-12 col-lg-12 col-md-12">
                    <div id="BookingDetails" class="card shadow"></div>
                </div>
            </div>

        </div>
    </div>

    <main class="commutes container-fluid clear-fix">

        <div class="commutes-info row bg-warning position-fixed bottom-0 z-1 mb-3 rounded-3 shadow">

            <div class="commutes-initial-state border-0">
                <svg aria-label="Directions Icon" width="53" height="53" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#commutes-initial-icon" />
                </svg>
                <div class="description fw-bold">
                    EZ Wallet Balance: <span class="walletbalance fw-bold"></span>
                    <h1 class="heading fs-2">BOOK YOUR EZ RIDE</h1>
                    <p>See your Travel Time in Real Time</p>
                </div>
                <button class="add-button btn text-light shadow" autofocus>
                    <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                        <use href="#commutes-add-icon" />
                    </svg>
                    <span class="label"><span class="fw-bold">Where to? </span>
                </button>
            </div>

            <div class="commutes-destinations">
                <div class="destinations-container">


                    <div class="destination-list vw-100"></div>
                    <button class="add-button d-none">
                        <div class="label">Add destination</div>
                    </button>
                </div>
                <button class="left-control hide" data-direction="-1" aria-label="Scroll left">
                    <svg width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" data-direction="-1">
                        <use href="#commutes-chevron-left-icon" data-direction="-1" />
                    </svg>
                </button>
                <button class="right-control hide" data-direction="1" aria-label="Scroll right">
                    <svg width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" data-direction="1">
                        <use href="#commutes-chevron-right-icon" data-direction="1" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="commutes-map row" aria-label="Map">
            <div id="map" class="map-view col-12"></div>
        </div>
    </main>

    <div class="commutes-modal-container shadow">
        <div class="commutes-modal" role="dialog" aria-modal="true" aria-labelledby="add-edit-heading">
            <div class="content bg-purple">
                <h2 id="add-edit-heading" class="heading text-light">Where to Go?</h2>
                <form id="destination-form">
                    <input type="text" id="destination-address-input" class="shadow form-control" name="destination-address" placeholder="Enter a place or address" autocomplete="off" required>
                    <div class="error-message" role="alert"></div>
                    <div class="travel-modes d-none">
                        <input type="radio" name="travel-mode" id="driving-mode" value="DRIVING" aria-label="Driving travel mode">
                        <label for="driving-mode" class="left-label" title="Driving travel mode">
                            <svg aria-label="Driving icon" mlns="http://www.w3.org/2000/svg">
                                <use href="#commutes-driving-icon" />
                            </svg>
                        </label>
                    </div>
                </form>
                <div class="modal-action-bar">
                    <button class="delete-destination-button me-1 hide" type="reset">
                        Delete
                    </button>
                    <button class="cancel-button btn btn-outline-secondary me-1" type="reset">
                        Cancel
                    </button>
                    <button class="add-destination-button get-curr-loc btn btn-primary me-1" type="button">
                        Add
                    </button>
                    <button class="edit-destination-button hide me-1" type="button">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once "../top_up_modal.php" ;?>


    <nav class="navbar fixed-bottom bg-purple rideInfoContainer d-none" style="height:10vh">
        <div class="container h-100">
            <button id="btnRideInfo" class="btn btn-outline-light w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" style="height:100%">
                <span class="fw-bold">Angkas Information</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-caret-up-square mb-1 ms-2" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path d="M3.544 10.705A.5.5 0 0 0 4 11h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5a.5.5 0 0 0-.082.537" />
                </svg>
            </button>


            <div class="offcanvas offcanvas-sm offcanvas-bottom bg-light currentBookingCanvas" style="height: 70vh" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
                <div class="offcanvas-head clear-fix bg-purple p-2 text-center">
                    <span class="input-group input-group-sm">
                        <span class="input-group-text small">EZ WALLET</span>
                        <span class="input-group-text small walletbalance fw-bold"></span>
                        <button class="btn btn-outline-light bg-purple fw-bold" data-bs-toggle="modal" data-bs-target="#topUpModal">
                            + CASH-IN</button>
                    </span>

                </div>
                <div class="offcanvas-body small overflow-y-scroll">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12" id="destinationInfoFormContainer"></div>
                            <div class="col-lg-12 col-sm-12 col-md-12" id="currentBookingInfo">
                                <div id="BookingInfoTable" class="card shadow-sm p-3 mb-3">
                                    <div class="mb-2">
                                        <small class="text-muted"><i id="BookedElaseTime"></i></small><br>
                                        <span id="BookingReferenceNumber" class="text-success fw-bold"></span>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">Fare</small><br>
                                        <span id="RideEstCost" class="text-secondary fw-bold"></span>
                                        <span id="paymentStatus" class="text-secondary fw-bold"></span><br>
                                        <button id="btnPayRider" data-payment-app="" class="btn-pay btn btn-success fw-bold mt-2 d-none">PAY NOW</button>
                                    </div>

                                    <div class="mb-2">
                                       <div class="card shadow border-0">
                                           <div class="card-body">
                                                <small class="text-muted"> <img src="../icons/originlocation.png" style="width:30px;" alt="" class="img-responsive"> Origin</small>
                                                <span class="fw-semibold" id="CustomerOrigin"></span>
                                                <br>
                                                <small class="text-muted">Destination</small>
                                                <span class="fw-semibold" id="CustomerDestination"></span>
                                           </div>
                                       </div>
                                       
                                    </div>

                                    <div class="mb-3 row">
                                        <div class="col-lg-2 col-12 p-2">
                                            <span class="badge text-bg-secondary fs-5" id="riderInfoBookingStatus"></span>
                                        </div>
                                        <div class="col-lg-10 col-12 p-2">
                                            <div class="badge d-flex shadow text-bg-light rounded-3 p-2" id="ratingcontainer"></div>
                                        </div>
                                        
                                        <div class="col-12" id="riderInfoPI"></div>
                                    </div>
                                </div>



                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="angkas_map.js"></script>
    <script src="_process_ajax.js"></script>
    <script src="chat.js"></script>

    <script>
        // Configuration object
        const CONFIGURATION = {
            defaultTravelMode: "DRIVING",
            distanceMeasurementType: "METRIC",
            mapOptions: {
                fullscreenControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                zoom: 15,
                zoomControl: true,
                maxZoom: 20,
                mapId: "b3394d825c3c2f44",
                center: null, // Center will be dynamically updated
                restriction: {
                    latLngBounds: {
                        // Adjusted boundaries for Albay and Sorsogon with a 1km offset
                        north: 13.618, // Northernmost latitude with offset
                        south: 12.580, // Southernmost latitude with offset
                        east: 124.348, // Easternmost longitude with offset
                        west: 123.400, // Westernmost longitude with offset
                    },
                    strictBounds: true, // Enforce strict boundary limits
                },
            },
            mapsApiKey: "AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs",
            currentAddressTxt: null, // Current address will be dynamically updated
            curLocCoor: null, // Current coordinates will be dynamically updated
        };


        async function getCurrentLocation() {
            if (!navigator.geolocation) {
                throw new Error("Geolocation is not supported by this browser.");
            }
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => resolve({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    }),
                    (error) => {
                        let message = "Geolocation failed.";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                message = "User denied the request for Geolocation.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                message = "The request to get user location timed out.";
                                break;
                            case error.UNKNOWN_ERROR:
                                message = "An unknown error occurred.";
                                break;
                        }
                        reject(new Error(message));
                    }, {
                        timeout: 10000,
                        maximumAge: 0,
                        enableHighAccuracy: true
                    }
                );
            });
        }

        async function getReadableAddress(location) {
            try {
                const geocoder = new google.maps.Geocoder();
                const latLng = {
                    lat: location.lat,
                    lng: location.lng
                };

                return new Promise((resolve, reject) => {
                    geocoder.geocode({
                        location: latLng
                    }, (results, status) => {
                        if (status === "OK" && results[0]) {
                            resolve({
                                address: results[0].formatted_address
                            });
                        } else {
                            reject(new Error(`Geocoder failed: ${status}`));
                        }
                    });
                });
            } catch (error) {
                console.error("Error fetching readable address:", error);
                return {
                    address: "Unknown Address"
                };
            }
        }
        /**
         * Updates the values of input fields with the user's current address and coordinates.
         */
        // function setInputValues() {
        //     const addressInput = $('input[name=form_from_dest]');
        //     const coordinatesInput = $('input[name=curLocCoor]');

        //     if (addressInput.length > 0 && coordinatesInput.length > 0) {
        //         addressInput.val(CONFIGURATION.currentAddressTxt || 'Unknown Address');
        //         coordinatesInput.val(CONFIGURATION.curLocCoor || 'Unknown');
        //         console.log('Input fields updated:', {
        //             curLocAddressTxt: CONFIGURATION.currentAddressTxt,
        //             curLocCoor: CONFIGURATION.curLocCoor,
        //         });
        //     }
        // }

        /**
         * Initializes the map, fetches the current location and address, and updates the configuration.
         */
        async function initMap() {

            try {
                // Fetch current location
                const location = await getCurrentLocation();
                CONFIGURATION.curLocCoor = `${location.lat},${location.lng}`;

                console.log("User's Current Location:", CONFIGURATION.curLocCoor);
                $("span.currloc").text(CONFIGURATION.curLocCoor);

                // Fetch readable address
                const addressData = await getReadableAddress(location);
                CONFIGURATION.currentAddressTxt = addressData.address;
                console.log("User's Current Address:", CONFIGURATION.currentAddressTxt);
                $("span.currAddress").text(CONFIGURATION.currentAddressTxt);

                // Update input values
                //ensureInputsExistAndSetValues();

                // Validate location against Region V bounds
                const {
                    lat,
                    lng
                } = location;
                const bounds = CONFIGURATION.mapOptions.restriction.latLngBounds;
                if (
                    lat >= bounds.north ||
                    lat <= bounds.south ||
                    lng >= bounds.east ||
                    lng <= bounds.west
                ) {
                    showModal("EZ Rides Services is not available in your current location.");
                    throw new Error("Location is outside the allowed boundaries.");
                }

                // Update map options
                CONFIGURATION.mapOptions.center = location;

                // Initialize the map with the updated configuration
                new Commutes(CONFIGURATION);




            } catch (error) {
                console.error('Error initializing map or fetching location data:', error);
                CONFIGURATION.curLocCoor = "";
                CONFIGURATION.currentAddressTxt = "";
                //ensureInputsExistAndSetValues();
            }
        }


        // Function to show a modal
        function showModal(message) {
            // Create modal elements
            const modal = document.createElement("div");
            modal.style.position = "fixed";
            modal.style.top = "0";
            modal.style.left = "0";
            modal.style.width = "100%";
            modal.style.height = "100%";
            modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
            modal.style.display = "flex";
            modal.style.justifyContent = "center";
            modal.style.alignItems = "center";
            modal.style.zIndex = "1000";

            const modalContent = document.createElement("div");
            modalContent.style.backgroundColor = "#fff";
            modalContent.style.padding = "20px";
            modalContent.style.borderRadius = "8px";
            modalContent.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
            modalContent.style.textAlign = "center";

            const modalText = document.createElement("p");
            modalText.textContent = message;
            modalText.style.margin = "0 0 20px";

            modalContent.appendChild(modalText);
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
        }

        // function ensureInputsExistAndSetValues() {
        //     const addressInput = $('input[name=form_from_dest]');
        //     const coordinatesInput = $('input[name=curLocCoor]');

        //     if (addressInput.length > 0 && coordinatesInput.length > 0) {
        //         if (!addressInput.val() || !coordinatesInput.val()) {
        //             setInputValues();
        //         }
        //     } else {
        //         console.log("Waiting for input fields to render...");
        //         setTimeout(ensureInputsExistAndSetValues, 500); // Retry every 500ms
        //     }
        // }


        async function initializeApp() {
            await initMap(); // Ensure the map is initialized and CONFIGURATION is populated

            chkBooking(); // Assuming this function needs to be called here
            // ensureInputsExistAndSetValues(); // This will handle the input updates when ready
            const wallet = '.walletbalance';
            fetchAndAssignWalletBalance(wallet);
        }


        $(document).on("click", ".get-curr-loc", function() {
            setInterval(() => {
                if ($('input[name=form_from_dest]').length > 0 && $('input[name=curLocCoor]').length > 0) {

                    $('input[name=form_from_dest]').val($("span.currAddress").text());
                    $('input[name=curLocCoor]').val($("span.currloc").text());
                }
            }, 500);


        });

        $(document).ready(function() {
            setTimeout(() => {
                $("#curlocationinfo").addClass("visually-hidden");
            }, 15000);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&callback=initializeApp&libraries=places,geometry&solution_channel=GMP_QB_commutes_v3_c&loading=async" async defer></script>

</body>

</html>
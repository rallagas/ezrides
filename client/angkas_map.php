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
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-xtmKaCh9tfCPtb3MMyjsQVNn3GFjzZsgCVD3LUmAwbLSU3u/7fIZkIVrKyxMAdzs" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="angkas_map.css">

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

        <div class="commutes-info row bg-light bg-opacity-75 position-fixed bottom-0 z-1 mb-1">

            <div class="commutes-initial-state border-0">
                <svg aria-label="Directions Icon" width="53" height="53" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#commutes-initial-icon" />
                </svg>
                <div class="description">
                    Wallet Balance: <span class="walletbalance badge text-bg-warning"></span>
                    <h1 class="heading">Book your EZ Rides</h1>
                    <p>See your Travel Time in Real Time</p>
                </div>
                <button class="add-button btn btn-primary shadow" autofocus>
                    <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                        <use href="#commutes-add-icon" />
                    </svg>
                    <span class="label"><span class="fw-bold">Where to? </span>
                </button>
            </div>

            <div class="commutes-destinations">
                <div class="destinations-container">


                    <div class="destination-list overflow-y-scroll vw-100"></div>
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
            <div class="map-view col-12"></div>
        </div>
    </main>

    <div class="commutes-modal-container shadow">
        <div class="commutes-modal" role="dialog" aria-modal="true" aria-labelledby="add-edit-heading">
            <div class="content bg-purple">
                <h2 id="add-edit-heading" class="heading text-light">Where to Go?</h2>
                <form id="destination-form">
                    <input type="text" id="destination-address-input" class="shadow form-control"
                        name="destination-address" placeholder="Enter a place or address" autocomplete="off" required>
                    <div class="error-message" role="alert"></div>
                    <div class="travel-modes d-none">
                        <input type="radio" name="travel-mode" id="driving-mode" value="DRIVING"
                            aria-label="Driving travel mode">
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

    <nav class="navbar fixed-bottom bg-purple rideInfoContainer d-none">
        <div class="container py-3">
            <button id="btnRideInfo" class="btn btn-outline-light my-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                <span class="my-3">Ride Info</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-caret-up-square mb-1 ms-2" viewBox="0 0 16 16">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path
                        d="M3.544 10.705A.5.5 0 0 0 4 11h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5a.5.5 0 0 0-.082.537" />
                </svg>
            </button>


            <div class="offcanvas offcanvas-sm offcanvas-bottom vh-25 bg-light currentBookingCanvas"  style="height: 60vh" tabindex="-1"
                id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
                <div class="offcanvas-head clear-fix bg-purple p-2 text-center">
                    <span class="input-group input-group-sm">
                        <button id="LoadBookingHistory" class="btn btn-outline-light bg-purple btn-sm"
                            data-bs-toggle="modal" data-bs-target="#BookingHistory">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-book-half" viewBox="0 0 16 16">
                                <path
                                    d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
                            </svg>
                        </button>
                        <span class="input-group-text small">Wallet</span>
                        <span class="input-group-text small walletbalance"></span>
                        <button class="btn btn-outline-light bg-purple btn-sm" data-bs-toggle="modal"
                            data-bs-target="#topUpModal">Top-Up</button>
                    </span>

                </div>
                <div class="offcanvas-body small">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12" id="destinationInfoFormContainer"></div>
                            <div class="col-lg-12 col-sm-12 col-md-12" id="currentBookingInfo">
                                <table class="table table-bordered" id="BookingInfoTable">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Booking #</th>
                                            <td id="BookingReferenceNumber" class="text-success fw-bold"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Booked</th>
                                            <td class="text-success fw-bold" id="BookedElaseTime">
                                                ${elapsedTimeInMinutes} min ago.</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Fare </th>
                                            <td class="text-secondary fw-bold">
                                                <span id="RideEstCost"></span>
                                                <span id="paymentStatus">( Checking... ) </span> <br>

                                                <button id="btnPayRider" data-payment-app="" class="btn-pay btn btn-outline-success d-none">Pay Now</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Origin</th>
                                            <td class="fw-semibold" id="CustomerOrigin">{form_from_dest_name}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Destination</th>
                                            <td class="fw-semibold" id="CustomerDestination">{form_to_dest_name}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Booking Status</th>
                                            <td id="riderInfoBookingStatus" class="fw-semibold">{booking_status_text}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Driver</th>
                                            <td class="fw-semibold" id="riderInfoPI">
                                                <div class="spinner-grow text-danger spinner-grow-sm" role="status">
                                                </div>
                                                <div class="spinner-grow text-danger spinner-grow-sm" role="status">
                                                </div>
                                                <div class="spinner-grow text-danger spinner-grow-sm" role="status">
                                                </div>
                                                {booking.rider_firstname}, {booking.rider_lastname}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th scope="row">Rate</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div id="myRatingCustomFeedbackStart"
                                                        class="text-body-secondary me-3"></div>
                                                    <div id="myRatingCustomFeedback"></div>
                                                    <div id="myRatingCustomFeedbackEnd" class="badge text-bg-dark ms-3">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="angkas_map.js"></script>
    <script src="_process_ajax.js"></script>
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
            mapId: "",
            center: null, // Center will be dynamically updated
        },
        mapsApiKey: "AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A",
        currentAddressTxt: null, // Current address will be dynamically updated
        curLocCoor: null, // Current coordinates will be dynamically updated
    };

    /**
     * Retrieves the user's current location coordinates using the Geolocation API.
     * @returns {Promise<{lat: number, lng: number}>} - A Promise resolving to an object with latitude and longitude.
     */
    // async function getCurrentLocation() {
    //     if (!navigator.geolocation) {
    //         throw new Error("Geolocation is not supported by this browser.");
    //     }
    //     return new Promise((resolve, reject) => {
    //         navigator.geolocation.getCurrentPosition(
    //             (position) => {
    //                 const {
    //                     latitude,
    //                     longitude
    //                 } = position.coords;
    //                 resolve({
    //                     lat: latitude,
    //                     lng: longitude
    //                 });
    //             },
    //             (error) => {
    //                 reject(new Error("Geolocation failed. Ensure location services are enabled."));
    //             }
    //         );
    //     });
    // }
    async function getCurrentLocation() {
    if (!navigator.geolocation) {
        throw new Error("Geolocation is not supported by this browser.");
    }
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(
            (position) => resolve({ lat: position.coords.latitude, lng: position.coords.longitude }),
            (error) => reject(new Error("Geolocation failed. Enable location services."))
        );
    });
}
    /**
     * Converts geographic coordinates into a readable address using the Google Maps Geocoder.
     * @param {{lat: number, lng: number}} coordinates - The latitude and longitude to geocode.
     * @returns {Promise<{status: string, address: string | null}>} - A Promise resolving to an address object.
     */
    // async function getReadableAddress(coordinates) {
    //     const geocoder = new google.maps.Geocoder();
    //     const latLng = {
    //         lat: coordinates.lat,
    //         lng: coordinates.lng
    //     };

    //     return new Promise((resolve, reject) => {
    //         geocoder.geocode({
    //             location: latLng
    //         }, (results, status) => {
    //             if (status === google.maps.GeocoderStatus.OK) {
    //                 const address = results[0]?.formatted_address || null;
    //                 resolve({
    //                     status: "OK",
    //                     address
    //                 });
    //             } else {
    //                 reject(new Error(`Geocoder failed due to: ${status}`));
    //             }
    //         });
    //     });
    // }
    async function getReadableAddress(location) {
    try {
        const geocoder = new google.maps.Geocoder();
        const latLng = { lat: location.lat, lng: location.lng };

        return new Promise((resolve, reject) => {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    resolve({ address: results[0].formatted_address });
                } else {
                    reject(new Error(`Geocoder failed: ${status}`));
                }
            });
        });
    } catch (error) {
        console.error("Error fetching readable address:", error);
        return { address: "Unknown Address" };
    }
}
    /**
     * Updates the values of input fields with the user's current address and coordinates.
     */
function setInputValues() {
    const addressInput = $('input[name=form_from_dest]');
    const coordinatesInput = $('input[name=curLocCoor]');

    if (addressInput.length > 0 && coordinatesInput.length > 0) {
        addressInput.val(CONFIGURATION.currentAddressTxt || 'Unknown Address');
        coordinatesInput.val(CONFIGURATION.curLocCoor || 'Unknown');
        console.log('Input fields updated:', {
            curLocAddressTxt: CONFIGURATION.currentAddressTxt,
            curLocCoor: CONFIGURATION.curLocCoor,
        });
    }
}

    /**
     * Initializes the map, fetches the current location and address, and updates the configuration.
     */
    async function initMap() {
    try {
        // Fetch current location
        const location = await getCurrentLocation();
        CONFIGURATION.curLocCoor = `${location.lat},${location.lng}`;
        console.log("User's Current Location:", CONFIGURATION.curLocCoor);

        // Fetch readable address
        const addressData = await getReadableAddress(location);
        CONFIGURATION.currentAddressTxt = addressData.address;
        console.log("User's Current Address:", CONFIGURATION.currentAddressTxt);

        // Update map options
        CONFIGURATION.mapOptions.center = location;

        // Initialize the map with the updated configuration
        new Commutes(CONFIGURATION);

        // Update input values
        ensureInputsExistAndSetValues();

    } catch (error) {
        console.error('Error initializing map or fetching location data:', error);
        CONFIGURATION.curLocCoor = "Unknown";
        CONFIGURATION.currentAddressTxt = "Unknown Address";
        ensureInputsExistAndSetValues();
    }
}

function ensureInputsExistAndSetValues() {
    const addressInput = $('input[name=form_from_dest]');
    const coordinatesInput = $('input[name=curLocCoor]');

    if (addressInput.length > 0 && coordinatesInput.length > 0) {
        setInputValues();
    } else {
        console.log("Waiting for input fields to render...");
        setTimeout(ensureInputsExistAndSetValues, 500); // Retry every 500ms
    }
}

    async function initializeApp() {
        await initMap(); // Ensure the map is initialized and CONFIGURATION is populated
        
        chkBooking();
        
        setInputValues(); // Safely call after CONFIGURATION is updated
        
        
        const wallet = '.walletbalance';
        fetchAndAssignWalletBalance(wallet);

    }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&callback=initializeApp&libraries=places,geometry&solution_channel=GMP_QB_commutes_v3_c&loading=async" async defer></script>

</body>

</html>
<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
//$user_logged = $_SESSION['user_id'];
//remove those past booking that have not been picked by any rider more than 1 hour ago
query("DELETE FROM angkas_bookings WHERE date_booked < (NOW() - INTERVAL 1 HOUR) and user_id = ? and angkas_rider_user_id is NULL and booking_status = 'P'", [USER_LOGGED]);
query("UPDATE angkas_bookings SET booking_status='D' WHERE date_booked < (NOW() - INTERVAL 5 MINUTE) and user_id = ? AND booking_status = 'C'", [USER_LOGGED]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Angkas</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="_map.css">
</head>
<body>
    <?php   
   // $current_booking=select_data(CONN, "angkas_bookings","user_id = {$user_logged} AND DATE(date_booked) = CURRENT_DATE");
    $current_booking=query("SELECT ab.angkas_booking_id
                                  , ab.angkas_booking_reference
                                  , ab.user_id AS customer_user_id
                                  , ab.angkas_rider_user_id
                                  , ab.form_from_dest_name
                                  , ab.user_currentLoc_lat
                                  , ab.user_currentLoc_long
                                  , ab.form_to_dest_name
                                  , ab.formToDest_long
                                  , ab.formToDest_lat
                                  , ab.form_ETA_duration
                                  , ab.form_TotalDistance
                                  , ab.form_Est_Cost
                                  , ab.date_booked
                                  , ab.booking_status
                                  , up.user_firstname
                                  , up.user_lastname
                                  , up.user_mi
                                  , up.user_gender
                                  , up.user_contact_no
                                  , up.user_email_address      
                                  , up.user_profile_image  
                                  , rp.user_firstname rider_firstname
                                  , rp.user_lastname rider_lastname
                                  , case when ab.booking_status = 'P' THEN 'Waiting for Driver'
                                         when ab.booking_status = 'A' THEN 'Driver Found'
                                         when ab.booking_status = 'R' THEN 'Driver Arrived in Your Location'
                                         when ab.booking_status = 'I' THEN 'In Transit'
                                         when ab.booking_status = 'C' THEN 'Completed'
                                         when ab.booking_status = 'F' THEN 'Pending Payment'
                                   end as booking_status
                               FROM angkas_bookings AS ab
                               JOIN user_profile AS up ON ab.user_id = up.user_id
                               JOIN users u ON up.user_id = u.user_id
                               LEFT JOIN user_profile AS rp ON ab.angkas_rider_user_id = u.user_id
                               WHERE ab.user_id = ?
                               AND DATE(date_booked) = CURRENT_DATE
                               AND ab.booking_status <> 'D' 
                         ", [USER_LOGGED]);
 require_once "__commutes_svg.php";
?>
    <div class="container-fluid">
        <div class="row p-0">

            <!-- Top-Up Modal -->
            <div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="topUpForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="topUpModalLabel">Top-Up Wallet</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="topUpAmount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="topUpAmount" name="amount" min="1" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Top-Up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Top-Up Modal -->
            <div class="modal fade" id="BookingHistory" tabindex="-1" aria-labelledby="BookingHistory" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-purple text-light">
                            <h5 class="modal-title">Booking History</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body overflow-y-scroll" style="height:90vh" id="BookingHistoryContent">

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row p-0">
            <nav class="navbar fixed-bottom bg-purple">
                <div class="container py-3">
                    <button id="btnRideInfo" class="btn btn-outline-light my-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                        <span class="my-3">Ride Info</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-caret-up-square mb-1 ms-2" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                            <path d="M3.544 10.705A.5.5 0 0 0 4 11h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5a.5.5 0 0 0-.082.537" />
                        </svg>
                    </button>


                    <div class="offcanvas offcanvas-sm offcanvas-bottom vh-25 bg-light" style="height: 60vh" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
                        <div class="offcanvas-head clear-fix bg-purple p-2 text-center">
                            <span class="input-group input-group-sm">
                                <button id="LoadBookingHistory" class="btn btn-outline-light bg-purple btn-sm" data-bs-toggle="modal" data-bs-target="#BookingHistory">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16">
                                        <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
                                    </svg>
                                </button>
                                <span class="input-group-text small">Wallet</span>
                                <span class="input-group-text small walletbalance"></span>
                                <button class="btn btn-outline-light bg-purple btn-sm" data-bs-toggle="modal" data-bs-target="#topUpModal">Top-Up</button>
                            </span>

                        </div>
                        <div class="offcanvas-body small">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 col-md-12" id="currentBookingInfo">

                                       <div id="BookingInfoTable" class="d-none card shadow-sm p-3 mb-3">
    <div class="mb-2">
        <small class="text-muted">Booking #</small><br>
        <span id="BookingReferenceNumber" class="text-success fw-bold"></span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Booked</small><br>
        <span class="text-success fw-bold" id="BookedElaseTime">${elapsedTimeInMinutes} min ago.</span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Fare</small><br>
        <span id="RideEstCost" class="text-secondary fw-bold"></span>
        <span id="paymentStatus" class="text-secondary fw-bold">( Checking... )</span><br>
        <button id="btnPayRider" data-payment-app="" class="btn-pay btn btn-outline-success btn-sm mt-2 d-none">Pay Now</button>
    </div>

    <div class="mb-2">
        <small class="text-muted">Origin</small><br>
        <span class="fw-semibold" id="CustomerOrigin">{form_from_dest_name}</span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Destination</small><br>
        <span class="fw-semibold" id="CustomerDestination">{form_to_dest_name}</span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Booking Status</small><br>
        <span class="fw-semibold" id="riderInfoBookingStatus">{booking_status_text}</span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Driver</small><br>
        <span class="fw-semibold" id="riderInfoPI">
            <div class="spinner-grow text-danger spinner-grow-sm me-1" role="status"></div>
            <div class="spinner-grow text-danger spinner-grow-sm me-1" role="status"></div>
            <div class="spinner-grow text-danger spinner-grow-sm me-1" role="status"></div>
            {booking.rider_firstname}, {booking.rider_lastname}
        </span>
    </div>

    <div class="mb-2">
        <small class="text-muted">Rate</small>
        <div class="d-flex align-items-center">
            <div id="myRatingCustomFeedbackStart" class="text-body-secondary me-3"></div>
            <div id="myRatingCustomFeedback"></div>
            <div id="myRatingCustomFeedbackEnd" class="badge text-bg-dark ms-3"></div>
        </div>
    </div>
</div>



                                    </div>
                                </div>

                            </div>

                            <form id="formFindAngkas" class="row g-2">

                                <div id="infoAlert" class="col-12"></div>
                                <div class="col-12">
                                    <button type="submit" id="findMeARiderBTN" class="btn d-flex btn-primary d-none">Find me a Rider</button>
                                </div>
                                <div class="col-3">
                                    <label for="form_from_dest" class="form-label align-middle">Pickup Location </label>
                                </div>
                                <div class="col-9">
                                    <input readonly id="form_from_dest" name="form_from_dest" type="text" class="form-control" placeholder="Checking your current location">
                                </div>
                                <div class="col-3">
                                    <label for="form_from_dest" class="form-label">Destination</label>
                                </div>
                                <div class="col-9">
                                    <input readonly required id="form_to_dest" name="form_to_dest" type="text" class="form-control" placeholder="Click 'Add Destination' on the Map">
                                </div>
                                <input type="text" id="currentLocCoor">
                                <input hidden id="currentLoc_lat" name="currentLoc_lat" type="text" class="form-control">
                                <input hidden id="currentLoc_long" name="currentLoc_long" type="text" class="form-control">

                                <input hidden type="text" class="form-control" id="formToDest_long" name="formToDest_long">
                                <input hidden type="text" class="form-control" id="formToDest_lat" name="formToDest_lat">

                                <div class="col-12 input-group">
                                    <span class="input-group-text">Php</span>
                                    <input readonly required type="text" class="form-control" id="form_Est_Cost" name="form_Est_Cost">
                                </div>
                                <div class="col-12 input-group">
                                    <input readonly type="text" class="form-control" id="form_ETA_duration" name="form_ETA_duration">
                                    <span class="input-group-text">minutes </span>
                                    <input readonly type="text" class="form-control" id="form_TotalDistance" name="form_TotalDistance">
                                    <span class="input-group-text">km/s</span>
                                </div>



                            </form>


                        </div>

                    </div>


                </div>

            </nav>
            <div class="col-lg-12 col-sm-12 vh-100 p-0">
                <main class="commutes clear-fix">
                    <div class="commutes-info">
                        <?php if(empty($current_booking)){ ?>
                        <div class="commutes-initial-state pb-3">
                            <svg aria-label="Directions Icon" width="53" height="53" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#commutes-initial-icon" />
                            </svg>
                            <div class="description ms-3 mb-3">
                                <h1 class="heading">Where to?</h1>
                                <p>Tap "Add Destination".</p>
                            </div>
                            <button class="add-button btn btn-primary mb-3 text-light" autofocus>
                                <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#commutes-add-icon" />
                                </svg>
                                <span class="label">Add destination</span>
                            </button>
                        </div>
                        <?php } ?>

                        <!-- <div class="commutes-destinations">
                            <div class="destinations-container">
                                <div class="destination-list"></div>

                                <button class="add-button">
                                    <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                                        <use href="#commutes-add-icon" />
                                    </svg>
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
                        </div> -->
                    </div>

                    <div class="commutes-map vh-100 m-0 p-0" aria-label="Map">
                        <div class="map-view vh-100"></div>
                    </div>


                </main>
            </div>
        </div>
    </div>



    <div class="commutes-modal-container modal">
        <div class="commutes-modal modal-dialog" role="dialog" aria-modal="true" aria-labelledby="add-edit-heading">
            <div class="content modal-content">
                <h2 id="add-edit-heading" class="heading modal-title">Add destination</h2>
                <form id="destination-form">
                    <input type="text" id="destination-address-input" name="destination-address" placeholder="Enter a place or address" autocomplete="off" required>
                    <div class="error-message" role="alert"></div>

                    <h6 class="heading">Choose your Angkas Vehicle</h6>
                    <div class="fade travel-modes">
                        <input type="radio" name="travel-mode" id="driving-mode" value="DRIVING" aria-label="Driving travel mode">
                        <label for="driving-mode" class="left-label" title="Driving travel mode">
                            <svg aria-label="Driving icon" mlns="http://www.w3.org/2000/svg">
                                <use href="#commutes-driving-icon" />
                            </svg>
                        </label>


                    </div>

                    <div class="modal-action-bar">
                        <button class="delete-destination-button" type="reset">
                            Delete
                        </button>
                        <button class="cancel-button" type="reset">
                            Cancel
                        </button>
                        <button class="add-destination-button btn btn-primary" type="button">
                            Add
                        </button>
                        <button class="edit-destination-button btn btn-secondary" type="button">
                            Done
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
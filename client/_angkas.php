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
            <nav class="navbar fixed-bottom bg-purple">
                <div class="container">

                    <button id="btnRideInfo" class="btn btn-outline-light my-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                        <span class="my-3">Ride Info</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-caret-up-square mb-1 ms-2" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                            <path d="M3.544 10.705A.5.5 0 0 0 4 11h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5a.5.5 0 0 0-.082.537" />
                        </svg>
                    </button>


                    <div class="offcanvas offcanvas-sm offcanvas-bottom h-50 bg-light p-2" tabindex="2" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
                        <div class="offcanvas-head">
                          <h3 class="fw-bold">Ride Information</h3>
                        </div>
                        <div class="offcanvas-body small">

                         

                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12 col-md-12" id="currentBookingInfo"></div>
                                    <div class="col-lg-8"></div>
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
            <div class="col-lg-12 col-sm-12 px-0">
                <main class="commutes">
                    <div class="commutes-info">
                        <?php if(empty($current_booking)){ ?>
                        <div class="commutes-initial-state">
                            <svg aria-label="Directions Icon" width="53" height="53" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <use href="#commutes-initial-icon" />
                            </svg>
                            <div class="description">
                                <h1 class="heading">Where to?</h1>
                                <p>Tap "Add Destination".</p>
                            </div>
                            <button class="add-button btn btn-primary m-3 text-light" autofocus>
                                <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#commutes-add-icon" />
                                </svg>
                                <span class="label">Add destination</span>
                            </button>
                        </div>
                        <?php } ?>

                        <div class="commutes-destinations">
                            <div class="destinations-container">
                                <div class="destination-list"></div>
                                
                            <button class="add-button">
                              <svg aria-label="Add Icon" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg">
                                <use href="#commutes-add-icon"/>
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
                        </div>
                    </div>

                    <div class="commutes-map h-100" aria-label="Map">
                        <div class="map-view"></div>
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
                    <div class="travel-modes">
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




    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="_map_config.js"></script>
    <script src="_map_func.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=places,geometry,marker&callback=initMap&loading=async"></script>
    <script src="_map_jquery.js"></script>
</body>

</html>



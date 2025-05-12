<nav class="navbar sticky-top">
    <div class="container-fluid clear-fix">
        

        <div class="position-fixed top-0 start-0 m-2 d-inline-block" style="z-index:1000">
            <span class="fs-3 float-start">
                <span class="fw-bold" style="color:indigo">@<?php echo isset($_SESSION['t_username']) ? $_SESSION['t_username'] : "No User Found";?></span>
            </span>
        </div>
        <div class="position-fixed top-0 end-0 m-2 d-inline-block" style="z-index:1000">


            <a href="index.php" class="btn btn-outline-warning bg-yellow  shadow rounded-4">
                <img src="../icons/house.png" alt="" class="quick-links img-fluid" style="width:3vw;">
            </a>

                <?php
                if(isset($_SESSION['t_rider_status'])){
                        if($_SESSION['t_rider_status'] == 1){
                        ?>
                            <a href="../rider_dashboard/index.php" class="btn btn-outline-warning bg-yellow  shadow rounded-4">
                                <img src="../icons/rider-page.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                            </a>
                <?php }
                }
                ?>

            <button id="appMenuBtn" class="btn btn-outline-warning bg-yellow  shadow rounded-4" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#appMenu" aria-controls="appMenu">
                <img src="../icons/menu.png" alt="" class="quick-links img-fluid" style="width:3vw;">
            </button>
                        <?php
            $current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
            if ($current_page === 'angkas_map.php') {
            ?>
                <div class="alert alert-warning float-start py-2 rounded-4 mx-2" id="curlocationinfo">
                    <span class="fw-bold currloc visually-hidden">Loading...</span>
                    Current Address: <span class="fw-bold currAddress">Loading...</span>
                </div>
            <?php
            }
            ?>

            <?php
            if(isset($_GET['page'])){
                if($_GET['page'] === 'shop' && isset($_GET['merchant'])){ ?>
                            <button
                                class="d-none shadow btn bg-danger mx-1 rounded-4 btn-checkout"
                                data-bs-toggle="tooltip" data-bs-title="Check Out" data-bs-placement="left">
                                <img src="../icons/checkout.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                              
                            </button>
                            <button id="ShowCartItems" class="shadow btn btn-warning rounded-4 position-relative float-end"
                                data-bs-toggle="collapse" data-bs-target="#CartItems" aria-expanded="false" aria-controls="CartItems">
                                <span id="cartCountBadge"
                                    class="position-absolute z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                                    <img src="../icons/trolley.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                            </button>
                            <button
                                class="HideOrderHistory toggleBtnOrderHist d-none shadow btn rounded-4 btn-outline-secondary bg-purple position-relative mx-1 float-end">
                                <span class="position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                                <img src="../icons/hidehist.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                            </button>
                            <button
                                class="ShowOrderHistory toggleBtnOrderHist shadow btn rounded-4 btn-primary position-relative mx-1 float-end">
                                <span
                                    class="MyShopListCountBadge position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                                    <img src="../icons/cycle.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                            </button>
                <?php }
            }
            ?>

        </div>
</nav>

<div class="offcanvas offcanvas-end bg-purple vh-100" tabindex="-1" id="appMenu" aria-labelledby="appMenu">
    <div class="offcanvas-header">
        <img src="../icons/ezrides.png" alt="" class="img-fluid w-25">
        <button type="button" class="btn-close btn-secondary" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body container-fluid vh-75">
        <div class="row">
            <div class="col-12">
                <span class="fw-bold text-white">
                    Hello, @<?php echo isset($_SESSION['t_username']) ? $_SESSION['t_username'] : "No User Found";?>
                </span>

                <a id="userLogOut" href="#" class="float-end btn btn-sm btn-danger">
                    <img src="../icons/logout.png" alt="" class="quick-links img-fluid" style="width:3vw;">
                </a>
            </div>
        </div>
        <div class="row g-1 my-3">
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


<div class="modal open-chat-modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content overflow-y-scroll">
            <div class="modal-header bg-purple text-light">
                <span class="modal-title fw-bold" id="chatModalLabel">EZ Chat @<span id="riderName"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
                <div class="modal-body overflow-y-scroll" id="conversation" style="height: 80vh">
                  
                   <small class="text-center text-body-tertiary">Start a conversation.</small>
                   
                  
                </div>
          <form id="formChatRider">
                <div class="modal-footer p-0 bg-secondary bg-opacity-50">
                       <div class="input-group rounded-5 border-9">
                            <input type="hidden" id="rideruserid" name="receiver_id" value="">
                           <input type="hidden"  id="senderuserid" name="sender_id" value="<?php echo USER_LOGGED;?>">
                           <input type="text" id="messagecontent" class="form-control border-0" name="message">
                            <button type="submit" class="btn btn-secondary border-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                  <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                </svg>
                            </button>
                        </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
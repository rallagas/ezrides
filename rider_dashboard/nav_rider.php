<nav class="navbar sticky-top  mb-5">
    <div class="container-fluid clear-fix">
        <div class="position-fixed top-0 start-0 m-2 d-inline-block" style="z-index:1000">
            <span class="fs-3 float-start">
 <img src="../profile/<?php echo getUserProfile(USER_LOGGED)[0]['user_profile_image'];?>"  
                     alt="Profile Picture" 
                     class="rounded-circle mb-3 profile-upload-label " 
                     style="width: 45px; height: 45px; object-fit: cover;">               

                  <span class="fw-bold" style="color:indigo">@<?php echo isset($_SESSION['t_username']) ? $_SESSION['t_username'] : "No User Found";?></span>
            </span>
        </div>

        <div class="position-fixed top-0 end-0 m-2 d-inline-block" style="z-index:1000">


            <a href="index.php" class="btn btn-outline-warning bg-warning shadow rounded-4">
                <img src="../icons/house.png" alt="" class="quick-links img-fluid" style="width:4vh;">
            </a>
            <a href="../client/index.php" class="btn btn-outline-warning bg-warning shadow rounded-4">
                <img src="../icons/mobile-shopping.png" alt="" class="quick-links img-fluid" style="width:4vh;">
            </a>

            <button id="appMenuBtn" class="btn btn-outline-warning bg-warning shadow rounded-4" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#appMenu" aria-controls="appMenu">
                <img src="../icons/menu.png" alt="" class="quick-links img-fluid" style="width:4vh;">
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
            <button class="d-none shadow btn bg-danger mx-1 rounded-4 btn-checkout" data-bs-toggle="tooltip"
                data-bs-title="Check Out" data-bs-placement="left">
                <img src="../icons/checkout.png" alt="" class="quick-links img-fluid" style="width:4vh;">

            </button>
            <button id="ShowCartItems" class="shadow btn btn-warning rounded-4 position-relative float-end"
                data-bs-toggle="collapse" data-bs-target="#CartItems" aria-expanded="false" aria-controls="CartItems">
                <span id="cartCountBadge"
                    class="position-absolute z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                <img src="../icons/trolley.png" alt="" class="quick-links img-fluid" style="width:4vh;">
            </button>
            <button
                class="HideOrderHistory toggleBtnOrderHist d-none shadow btn rounded-4 btn-outline-secondary bg-purple position-relative mx-1 float-end">
                <span
                    class="position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                <img src="../icons/hidehist.png" alt="" class="quick-links img-fluid" style="width:4vh;">
            </button>
            <button
                class="ShowOrderHistory toggleBtnOrderHist shadow btn rounded-4 btn-primary position-relative mx-1 float-end">
                <span
                    class="MyShopListCountBadge position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>
                <img src="../icons/cycle.png" alt="" class="quick-links img-fluid" style="width:4vh;">
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
                    <img src="../icons/logout.png" alt="" class="quick-links img-fluid" style="width:3vh;">
                </a>
            </div>
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
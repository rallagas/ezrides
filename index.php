<?php
include_once "_db.php";
include_once "_functions.php";
?>


<html>

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>EZ Rides</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--    <link rel="stylesheet" href="css/style.css">-->
    <style>
    .bg-purple {
        background-color: mediumpurple;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-purple">
        <div class="container">
            <a class="navbar-brand align-middle" href="index.php">
                <img src="icons/ezrides-icon.png" alt="" width="70" height="60"
                    class="border border-0 rounded-3 d-inline-block align-text-top shadow">
                <span class="">EZ<span class="text-secondary">RIDES</span></span>
            </a>
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> -->
      <!-- <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
    </div> -->
            <div class="d-block d-lg-none">

            <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseLogin" aria-expanded="false" aria-controls="collapseLogin">
                    Log In
                </button>
                <a class="btn btn-light me-3" href="index.php?page=registration" >Sign
                    Up
                </a>

                <a class="btn btn-warning me-3" href="index.php?page=registration&regRider" >
                <img src="icons/delivery-icon-2.png" alt="" height="25vh">
                </a>

            </div>
            <div class="d-none d-lg-block">

            <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseLogin" aria-expanded="false" aria-controls="collapseLogin">
                    Log In
                </button>
                <a class="btn btn-light me-3" href="index.php?page=registration" >Sign
                    Up
                </a>

                <a class="btn btn-warning me-3" href="index.php?page=registration&regRider" >
                    Become a Rider <img src="icons/delivery-icon-2.png" alt="" height="25vh">
                </a>

            </div>



        </div>
    </nav>

    <div class="container-fluid">
        <div class="row gx-2">
            <div id="header" class="mt-2 col-lg-12 offset-lg-1 col-md-6 offset-md-3 col-sm-6 offset-sm-3">


            </div>
        </div>
        <div class="row px-5">
            <div class="col-lg-8 py-5" >
                <h1 class="fw-bold display-3"> Book your Ride with EZ <span class="text-tertiary">Rides</span></h1>
                <p class="fw-light fs-4">Fast, reliable door-to-door service in Albay when you need it most.
                </p>
            </div>
            <div class="col-lg-4">
                <img src="icons/delivery-icon-map.png" class="img-fluid" style="height:25vh">
            </div>
        </div>

       
        <div class="row px-5 g-2">
            <div class="col-8 offset-2">
                <div id="logstatus"></div>
            </div>
            <div class="col-lg-6 offset-3 col-md-6 col-sm-12 pt-3">
                <div class="multi-collapse collapse mt-0" id="collapseLogin">
                    <form id="formUserLog">
                        <div class="card shadow">
                            <div class="card-header bg-purple text-light">
                                <h3 class="fs-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        class="bi bi-person-circle mb-1" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                    </svg>
                                    Login
                                </h3>

                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="log_username" class="form-label"> Username or Email </label>
                                    <input type="text" id="log_username" name="log_username" class="form-control w-100">
                                </div>
                                <div class="mb-3">
                                    <label for="log_password" class="form-label"> Password</label>
                                    <input type="password" id="log_password" name="log_password" class="form-control">

                                </div>
                            </div>
                            <div class="card-footer">
                            <button id="loginButton" class="btn btn-outline-light bg-purple mb-0 float-end"
                                    type="submit">Login
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            


        </div>

        <?php 
        if(isset($_GET['page'])){
            if ($_GET['page'] == 'registration'){ ?>
            <div class="row">
                <div class="col-8 offset-2">
                    <?php include_once "_registration.php";?>
                </div>
            </div>
        <?php }
        }
        ?>

<div class="row">
<div class="col-sm-12 col-lg-12 bg-purple text-light">
                <h3 class="display-3 ms-5 py-5">
                    Our Services
                </h3>
            </div>
</div>
        <div class="row gx-5 px-5">
     
            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/car-rental-icon.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold card-title">EZ RENT</h5>
                    <p class="card-caption">Rent and Drive your own vehicle for an affordable rate.</p>
                </div>
            </div>

            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/ride-hailing-icon.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ RIDE</h5>
                    <p class="card-caption">Book your ride to your destination, wait for the rider at an affordable and convenient rate.</p>
                </div>
            </div>
            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/delivery-guy-icon.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ FOOD DELIVERY</h5> 
                    <p class="card-caption">Hungry? EZ Delivery is our solution, "ipa EZ Delivery mo na yan!"</p>

                </div>
            </div>
            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/rx-delivery.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ PHARMACY DELIVERY</h5> 
                    <p class="card-caption">Can't Get up and buy Needed Medicine? Book your rider now!"</p>

                </div>
            </div>
            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/document.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ DOCUMENT DELIVERY</h5> 
                    <p class="card-caption">You need to get your PSA Document? Book a rider now and let us do it for you.</p>

                </div>
            </div>
            <div class="col-12 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/wallet.png" alt="" class="card-img-top">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ WALLET</h5> 
                    <p class="card-caption">Pay with ease, cash-in for easy payment thru our EZ Wallet system.</p>

                </div>
            </div>
        </div>

    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<!--<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>-->
<script src="js/jquery-3.5.1.min.js"></script>
<script src="_multipurpose_ajax.js"></script>

<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>

</html>
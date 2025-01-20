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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <style>
    .bg-purple {
        background-color: #2f007c;
    }

    .text-purple {
        color: indigo;
    }

    .bg-yellow {
        background-color: #feaa12;
    }

    @media only screen and (max-width: 768px) {
        .service-icon {
            width: 30vw;
        }
    }

    .banner-animation {
        position: relative;
        /* Ensure positioning context */
        height: 25vh;
    }

    .img-rider,
    .img-truck,
    .img-citybg,
    .img-truck2 {
        position: absolute;
        /* Position relative to the parent div */
        bottom: 0;
        /* Keep the images at the bottom */
        height: 10vh;
        /* Maintain the specified height */
    }

    .img-rider {
        left: 10px;
        animation: riderLoop 7s linear forwards;
        /* Added 'forwards' to preserve the final state */
    }

    .img-truck {
        right: 10px;
        animation: truckLoop 7s linear forwards;
        /* Added 'forwards' */
    }

    .img-truck2 {
        right: 10px;
        height: 7vh;
        animation: truckLoop2 4s linear forwards;
        /* Added 'forwards' */
    }

    .banner-animation {
        background-image: url('icons/city.png');
        background-repeat: repeat-x;
        background-size: 100% auto;
        background-position: bottom;
    }

    /* Keyframes for animations remain the same */


    @keyframes slideInLeft {
        0% {
            transform: translateX(-80vw);
            opacity: 0;
        }

        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideInRight {
        0% {
            transform: translateX(50vw);
            opacity: 0;
        }

        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes riderLoop {
        0% {
            transform: translateX(-50vw);
        }

        100% {
            transform: translateX(20vw);
        }
    }

    @keyframes truckLoop {
        0% {
            transform: translateX(50vw);
        }

        100% {
            transform: translateX(-10vw);
        }
    }

    @keyframes truckLoop2 {
        0% {
            transform: translateX(50vw);
        }

        100% {
            transform: translateX(-50vw);
        }
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top bg-dark bg-opacity-75">
        <div class="container">
            <a class="navbar-brand align-middle" href="index.php">
                <img src="icons/ezrides-full.png" alt="" style="height:5vw" class="">
            </a>
            <div class="clear-fix">
                <a class="btn bg-yellow btn-warning border-2 border-light me-3 rounded-4 float-end" href="?page=login">
                    <span class="d-none d-lg-block d-print-inline fw-bold text-light">
                        <img class="float-start" src="icons/key.png" alt="" height="25vh">&nbsp;LOGIN</span>
                    <span class="d-lg-none">
                        <img src="icons/key.png" alt="" height="25vh">
                    </span>
                </a>
                <a class="btn bg-yellow btn-warning border-2 border-light me-3 rounded-4 float-end"
                    href="?page=register">
                    <span class="d-none d-lg-block d-print-inline fw-bold text-light">
                        <img class="float-start" src="icons/travel-car-icon.png" alt="" height="25vh">&nbsp;SIGN
                        UP</span>
                    <span class="d-lg-none">
                        <img src="icons/travel-car-icon.png" alt="" height="25vh">
                    </span>
                </a>
                <a class="btn bg-yellow btn-warning border-2 border-light me-3 rounded-4 float-end"
                    href="?page=register&regRider">
                    <span class="d-none d-lg-block d-print-inline fw-bold text-light">
                        <img class="float-start" src="icons/ezrides-icon.png" alt="" height="25vh">&nbsp;BE A
                        RIDER</span>
                    <span class="d-lg-none">
                        <img src="icons/ezrides-icon.png" alt="" height="25vh">
                    </span>
                </a>

            </div>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row bg-yellow">
            <div class="col-12">
                <div id="logstatus"></div>
            </div>
        </div>
        <?php 
             if(isset($_GET['page'])){
                $pageNow = $_GET['page'];
        ?>
        <div class="row bg-yellow">
            <div class="col-lg-3 col-2"></div>
            <div class="col-lg-6 col-8 pt-5">
                <div class="mt-5">
                    <?php if($pageNow == 'login') { ?>
                    <br><br>
                    <form id="formUserLog" class="mt-5">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z" />
                                        <path fill-rule="evenodd"
                                            d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php }
                     else if($pageNow == 'register'){ 
                            include_once "_registration.php";
                    }else{
                       echo "pagenotFound";
                    } ?>


                </div>
            </div>
            <div class="col-lg-3 col-2"></div>
        </div>
        <?php } 
        else if(!isset($_GET['page'])) { ?>
        <div class="row" id="sliders">
            <div id="carouselExampleAutoplaying" class="carousel slide px-0" data-bs-ride="carousel">
                <div class="carousel-inner px-0">
                    <?php
                    $slides=5;
                    $currSlide=1;
                    while($currSlide <= $slides){
                    ?>
                    <div class="carousel-item px-0 <?php echo $currSlide == 1 ? "active" : "" ; ?>">
                        <img src="icons/sliders/<?php echo $currSlide . ".png"; ?>"
                            class="mx-0 img-fluid vw-100 d-none d-md-block" alt="...">
                        <img src="icons/sliders-vertical/<?php echo $currSlide . ".png"; ?>"
                            class="mx-0 img-fluid vw-100 d-lg-none" alt="...">
                    </div>
                    <?php 
                         $currSlide++;
                    } 
                    ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>

                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>

                </button>
            </div>
        </div>
        <?php }
else{
    echo "default";
} ?>
    </div>
    <div class="container-fluid">
        <div class="row gx-3 bg-yellow text-light">
            <div class=" col-12 col-lg-4 text-center pt-3">
            <div class="card shadow border-0">
                <div class="card-header bg-purple">
                    <h5 class="fs-5 mb-0 text-light card-title fw-bold">GOAL</h5>
                </div>
                <div class="card-body">
                
                    <h6 class="fs-6">
                        We are determined to provide high-quality Delivery and D2D services that exceed expectations.. Let's
                        take a look at what we have been working on and what our future plans are.
                    </h6>
                </div>
             </div>
            </div>
            <div class=" px-3 col-12 col-lg-4 text-center pt-3">
                <div class="card shadow border-0">
                    <div class="card-header bg-purple">
                        <h5 class="fs-5 mb-0 text-light card-title fw-bold">
                            MISSION
                        </h5> 
                    </div>
                <div class=" card-body">
                    
                    <h6 class="fs-6">
                        We empower riders to deliver top-notch online services with integrity and speed. We're committed to
                        constant improvement, striving to make every delivery a seamless experience that earns trust and
                        satisfaction to Customer."
                        To provide exceptional online services through dedicated riders who prioritize customer satisfaction
                        above all else
                        And also to support and empower service providers, including riders, to learn and grow through their
                        experiences
                    </h6>
                </div>
                </div>
            </div>
            <div class=" px-3 col-12 col-lg-4 text-center pt-3">
                <div class="card shadow border-0">
                    <div class="card-header bg-purple">
                        <h5 class="fs-5 mb-0 text-light card-title fw-bold">
                            VISION
                        </h5> 
                    </div>
                <div class=" card-body">
                    
                    <h6 class="fs-6">
                    Delivering Seamless Excellence: Empowering Service Providers to Provide Quality Online Services.
                    </h6>
                </div>
                </div>
            </div>


        </div>
        <div class="row bg-yellow">
            <div class="col-lg-12 mb-0 banner-animation overflow-hidden">
                <img src="icons/truck.gif" class="img-fluid img-truck" alt="">
                <img src="icons/rider.gif" class="img-fluid img-rider" alt="">
                <img src="icons/orange-truck.gif" class="img-fluid img-truck2">
            </div>
        </div>
    </div>

    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12 bg-purple text-light">
                <h3 class="fw-bold fs-2 ms-5 py-3">
                    SERVICES
                </h3>
            </div>
        </div>
    </div>
    <div class="container">

        <div class="row">

            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/car-rental-icon.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0 text-center">
                    <h5 class="fs-4 fw-bold card-title">EZ RENT</h5>
                    <p class="card-caption">Rent and Drive your own vehicle for an affordable rate.</p>
                </div>
            </div>

            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/ride-hailing-icon.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0   text-center">
                    <h5 class="fs-4 fw-bold">EZ RIDE</h5>
                    <p class="card-caption">Book your ride to your destination, wait for the rider at an affordable and
                        convenient rate.</p>
                </div>
            </div>
            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/delivery-guy-icon.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0 text-center">
                    <h5 class="fs-4 fw-bold">EZ FOOD DELIVERY</h5>
                    <p class="card-caption">Hungry? EZ Delivery is our solution, "ipa EZ Delivery mo na yan!"</p>

                </div>
            </div>
            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/rx-delivery.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0  text-center">
                    <h5 class="fs-4 fw-bold">EZ PHARMACY DELIVERY</h5>
                    <p class="card-caption">Can't Get up and buy Needed Medicine? Book your rider now!"</p>

                </div>
            </div>
            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/document.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0 text-center">
                    <h5 class="fs-5 fw-bold  text-center">EZ DOCUMENT DELIVERY</h5>
                    <p class="card-caption">You need to get your PSA Document? Book a rider now and let us do it for
                        you.</p>

                </div>
            </div>
            <div class="col-6 col-lg-4 card border-0">
                <div class="card-header border-0 p-5">
                    <img src="icons/wallet.png" alt="" class="card-img-top service-icon">
                </div>
                <div class="card-body border-0">
                    <h5 class="fs-5 fw-bold  text-center">EZ WALLET</h5>
                    <p class="card-caption">Pay with ease, cash-in for easy payment thru our EZ Wallet system.</p>

                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12 bg-purple text-light">
                <h3 class="fw-bold fs-2 ms-5 py-3">
                    TESTIMONIALS
                </h3>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-yellow px-3">
        <div class="row">
            <?php
                $commentdata = query("SELECT * FROM `customerSuggestions` WHERE `approved`=1 LIMIT 6");
                if(!empty($commentdata)){
                    foreach($commentdata as $cd){ ?>
            <div class="col-3">
                <div class="my-3 card shadow">
                    <?php
                    if($cd['photo'] != null){?>
                    <img src="images/comments-photo/<?php echo $cd['photo'];?>" alt="" class="card-img-top">
                    <?php }
                            ?>
                    <div class="card-body text-center">
                        <span class="fs-5 fw-bold">
                            "<?php echo $cd['message'];?>"
                        </span>
                        <br>
                        <div class="container-fluid">
                            <?php
                                        $starcount=1;
                                        while($starcount <= $cd['rate']){ ?>
                            <img class="img-fluid" style="width:30px;" src="icons/star.png" alt="1">
                            <?php $starcount++; }
                                    ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                    }
                }
                
            ?>

        </div>
    </div>


    <div class="container-fluid bg-purple py-4 py-md-5 px-4 px-md-3 text-light">
        <div class="row px-4">
            <div class="col-lg-3 mb-3">
                <a class="d-inline-flex align-items-center mb-2 text-body-emphasis text-decoration-none" href="/"
                    aria-label="Bootstrap">

                    <img src="icons/ezrides-full.png" alt="" class="img-fluid w-25">
                </a>
                <ul class="list-unstyled small">
                    <li class="mb-2 fs-4">A multipurpose delivery platform for the people of Albay</a>.</li>

                </ul>
            </div>

            <div class="col-12 col-lg-7 mb-3">
                <h5 class="fs-5">We would like to hear from you.</h5>

                <form action="_process_comments.php" id="suggestionBox" enctype="multipart/form-data">
                    <div class="mb-2">
                        <input type="email" class="form-control bg-yellow border-2 border-dark" name="comment_email"
                            placeholder="Your Email" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="comment_msg" class="form-control bg-yellow border-2 border-dark"
                            placeholder="Your Comment"></textarea>
                    </div>
                    <button class="btn bg-yellow border-2 border-dark trigger-attach" type="button"
                        aria-label="Attach file">
                        <img src="icons/attachment.png" alt="" height="60px">
                    </button>
                    <input type="file" class="attach-file form-control mb-2 visually-hidden" name="comment_pic">

                    <!-- Rating with clickable stars -->
                    <div class="rating-stars mb-3">
                        <span data-value="1" class="star"><img class="img-fluid" style="width:60px;"
                                src="icons/empty_star.png" alt="1"></span>
                        <span data-value="2" class="star"><img class="img-fluid" style="width:60px;"
                                src="icons/empty_star.png" alt="2"></span>
                        <span data-value="3" class="star"><img class="img-fluid" style="width:60px;"
                                src="icons/empty_star.png" alt="3"></span>
                        <span data-value="4" class="star"><img class="img-fluid" style="width:60px;"
                                src="icons/empty_star.png" alt="4"></span>
                        <span data-value="5" class="star"><img class="img-fluid" style="width:60px;"
                                src="icons/empty_star.png" alt=" 5"></span>
                        <input type="hidden" name="comment_rating" id="commentRating" value="3">
                    </div>

                    <button class="btn btn-outline-dark bg-yellow fw-bold">
                        <span class="fs-5">SUBMIT</span> <img src="icons/airplane.png" alt="" height="30px">
                    </button>
                </form>

                <div id="responseMessage"></div>


            </div>

            <div class="col-lg-2">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4 text-justified">
                            <a title="ezridesligaocity2022@gmail.com" data-bs-toggle="collapse" href="#emailAdd"
                                role="button" aria-expanded="false" aria-controls="emailAdd" class="track"
                                data-track-arguments="ga, event, subhome, icons, logo-google">
                                <img class="lzy track lazyload--done"
                                    src="https://media.flaticon.com/dist/min/img/home-icons/logos/google.svg"
                                    data-src="https://media.flaticon.com/dist/min/img/home-icons/logos/google.svg"
                                    alt="Google logo" width="50" height="50"
                                    srcset="https://media.flaticon.com/dist/min/img/home-icons/logos/google.svg 4x">

                            </a>
                            <span class="collapse" id="emailAdd">ezridesligaocity2022@gmail.com</span>
                        </div>
                        <div class="col-4 text-center">
                            <a href="https://www.facebook.com/profile.php?id=100087211785973&mibextid=ZbWKwL">
                                <img class="lzy track lazyload--done"
                                    src="https://media.flaticon.com/dist/min/img/home-icons/logos/facebook.svg"
                                    data-src="https://media.flaticon.com/dist/min/img/home-icons/logos/facebook.svg"
                                    alt="Facebook logo" width="50" height="50"
                                    srcset="https://media.flaticon.com/dist/min/img/home-icons/logos/facebook.svg 4x">

                            </a>
                        </div>
                        <div class="col-4 text-center">
                            <a href="https://www.flaticon.com/free-icons/instagram" class="track"
                                data-track-arguments="ga, event, subhome, icons, logo-instagram">
                                <img class="lzy track lazyload--done"
                                    src="https://media.flaticon.com/dist/min/img/home-icons/logos/instagram.svg"
                                    data-src="https://media.flaticon.com/dist/min/img/home-icons/logos/instagram.svg"
                                    alt="Instagram logo" width="50" height="50"
                                    srcset="https://media.flaticon.com/dist/min/img/home-icons/logos/instagram.svg 4x">

                            </a>
                        </div>

                    </div>
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

<script>
$(document).ready(function() {

    $('.trigger-attach').on('click', function() {
        $('.attach-file').trigger('click'); // Simulate a click on the hidden file input
    });

    const starIcons = {
        empty: 'icons/empty_star.png',
        filled: 'icons/star.png'
    };

    $('.star').on('click', function() {
        const rating = $(this).data('value');
        $('#commentRating').val(rating); // Set hidden input value
        updateStars(rating); // Update star visuals
    });

    function updateStars(rating) {
        $('.star').each(function() {
            const starValue = $(this).data('value');
            const starImg = starValue <= rating ? starIcons.filled : starIcons.empty;
            $(this).find('img').attr('src', starImg);
        });
    }

    // Initialize with no stars selected
    updateStars(3);
    $('#suggestionBox').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this); // Create a FormData object from the form

        $.ajax({
            url: '_process_comments.php', // The PHP script to process the form
            type: 'POST',
            data: formData,
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            success: function(response) {
                try {
                    const jsonData = JSON.parse(response); // Parse the JSON response
                    if (jsonData.status === 'success') {
                        alert(jsonData.message); // Display success message
                        $('#suggestionBox')[0].reset(); // Reset the form
                    } else {
                        alert('Error: ' + jsonData.message); // Display error message
                    }
                } catch (error) {
                    alert('Invalid response from server.'); // Handle invalid JSON response
                }
            },
            error: function() {
                alert('An error occurred. Please try again later.'); // Handle AJAX error
            }
        });
    });

});
</script>

</html>
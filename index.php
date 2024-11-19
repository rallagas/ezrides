<?php
include_once "_db.php";
include_once "_functions.php";
?>


<html>

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--    <link rel="stylesheet" href="css/style.css">-->
    <style>
        .bg-purple {
            background-color: mediumpurple;
        }

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row gx-2">
            <div id="header" class="mt-2 col-lg-3 offset-lg-1 col-md-6 offset-md-3 col-sm-6 offset-sm-3">
                <img src="icons/ezrides.png" alt="" class="img-fluid">
            </div>
        </div>

        <div class="row px-5 g-2">
           <div class="col-8 offset-2">
               <div id="logstatus"></div>
           </div>
            <div class="col-lg-6 col-md-6 col-sm-12 pt-3">
                <button class="text-center shadow btn btn-lg btn-outline-secondary my-1 d-flex w-100 p-3" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="collapseLogin">Sign In</button>
                <div class="multi-collapse collapse show" id="collapseLogin">
                    <form id="formUserLog">
                        <div class="card">
                            <div class="card-header bg-secondary text-light">
                                
                                <h6 class="card-title fw-bold fs-5 mt-2">Sign In as Customer</h6>

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

                                <button id="loginButton" class="btn btn-outline-light bg-purple mb-0" type="submit">Login</button>


                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 pt-3">
               <button class="text-center shadow bg-purple btn btn-lg btn-outline-secondary text-light my-1 d-flex w-100 p-3" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="collapseLogin collapseRegister">
                <span  data-bs-toggle="tooltip" data-bs-title="Create Account" data-bs-placement="bottom">Join Us</span>   
                </button>
                <div class="multi-collapse collapse" id="collapseRegister">
                    <?php include "_registration.php";?>
                    <div id="RegStatus"></div>
                </div>
            </div>


        </div>
        <div class="row px-5">
            <div class="col-lg-12">
                <h1 class="fw-bold display-3 mt-5"> Receive orders, dispatch drivers, and track deliveries easily </h1>
                <p class="fw-light">Manage your courier business and provide better customer service without the hassle.</p>
            </div>
        </div>

        <div class="row bg-purple mt-5 text-white">
            <div class="col-sm-12 col-lg-8 offset-lg-2">
                <div class="p-5">
                    <h3 class="text-center m-5">
                        Offer an easy way for customers to order and track their deliveries
                    </h3>


                </div>
            </div>
            <div class="col-4 offset-4">
                <a href="" class="btn rounded text-white">
                    <img src="icons/take-away.png" alt="" class="rounded-circle img-fluid w-25">
                </a>
            </div>
        </div>

    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!--<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>-->
<script src="js/jquery-3.5.1.min.js"></script>
<script src="_multipurpose_ajax.js"></script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>

</html>

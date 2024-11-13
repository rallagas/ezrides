<?php
include_once "_db.php";
include_once "_functions.php";
?>


<html>

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row gx-2">
            <div id="header" class="col-lg-3 offset-lg-1 col-md-6 offset-md-3 col-sm-6 offset-sm-3">
                <img src="icons/ezrides.png" alt="" class="img-fluid">
            </div>

            <div class="col-lg-2 offset-lg-4 col-md-4 offset-md-2 col-sm-4 offset-sm-2">
                <a class="btn btn-lg btn-outline-secondary mt-3 d-flex p-3" role="button" data-bs-toggle="collapse" href="#collapseRegister" aria-expanded="false" aria-controls="collapseRegister">Join Us</a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4">
                <a class="btn btn-lg btn-outline-light bg-purple mt-3 d-flex p-3" role="button" data-bs-toggle="collapse" href="#collapseLogin" aria-expanded="false" aria-controls="collapseLogin">Sign In</a>
            </div>
        </div>

        <div class="row px-5">
            <div class="col-lg-5 col-sm-12 col-md-12 offset-lg-1 offset-sm-0">
               <div class="collapse" id="collapseRegister">
                    <?php include "_registration.php";?>
               </div>
               
              
            </div>

            <div class="col-lg-4 col-md-6 offset-md-3 col-sm-12 offset-lg-2 mt-0 collapse px-0 " id="collapseLogin">

                <form id="formUserLog">
                    <div class="card">
                        <div class="card-header bg-purple text-white">
                            <div id="logstatus"></div>
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

                            <a class="btn btn-link" role="button" data-bs-toggle="collapse" href="#collapseRegister" aria-expanded="false" aria-controls="collapseRegister">Create Account</a>

                        </div>
                    </div>
                </form>
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

</html>
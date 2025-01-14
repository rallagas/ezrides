<?php
include_once "_db.php";
include_once "_functions.php";
include_once "url_checker.php";


?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ez Rides</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <?php include_once "_nav.php";?>
            </div>
        </div>
        
        <?php
            ifPageis('register','_registration.php');
        ?>
        
        <div class="row">
            <div class="col-lg-4 col-md-2 col-sm-2 col-xs-2"></div>
            <div class="col-lg-4 col-md-8 col-sm-8 col-xs-8">
               <div id="alertLogUser" class="alert"></div>
                <?php
                   ifPageis('loguser','_loguser.php');
                ?>
            </div>
            <div class="col-lg-4 col-md-2 col-sm-2 col-xs-2"></div>
        </div>
    </div>
    
    
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="js/bootstrap.js"></script>
<script src="process_ajax.js"></script>
</html>
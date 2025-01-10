<?php
require_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/css/coreui.min.css" rel="stylesheet" integrity="sha384-xtmKaCh9tfCPtb3MMyjsQVNn3GFjzZsgCVD3LUmAwbLSU3u/7fIZkIVrKyxMAdzs" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css"> -->
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <?php include_once "nav-client.php";?>

            
    <hr class="m-0 p-0 text-dark">
    <div class="container-fluid p-1">
        <div class="row px-5" id="queryresult"></div>
        <div class="row px-2">
            <div class="col-12" id="TransactionStatus"></div>
                <?php

                $txn_cat = select_data( "txn_category", NULL, "txn_category_id", 15 );
                foreach ( $txn_cat as $cat ) { 
                            if(isset($_GET['page']) && isset($_GET['txn_cat'])){
                                $page = $_GET['page']; 
                                $categ = $_GET['txn_cat']; 
                                $_SESSION['txn_cat_id'] = $_GET['txn_cat'] ?? 1;
                                ?>
                                <?php 
                                if($cat['page_action'] == $page && $cat['txn_category_id'] == $categ){ 
                                   $txnlink = $cat['txn_link'];
                                ?>          
                                <div class="col-12 p-0" id=""></div>              
                                    <?php include_once $txnlink; 
                                    ?>
                                 </div>
                                <?php 
                                }
                            }
                        
                } $cat=null;
                if ( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'home' ) ) { ?>
                
                            <div class="col-lg-12">
                                <?php include_once "_index_wallet.php"; ?>
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12">
            
                                <h1 class="fw-bold display-6 mt-5"> Welcome! </h1>
                                <p class="fw-light">Going Somewhere? Craving for some Burger?</p>

                                <div class="card border-0 p-0">
                                    <div class="card-body">
                                        <div class="container-fluid">
                                            <div class="row gx-1">
                                                <?php
                                                $txn_cats = select_data("txn_category","txn_category_status='A'","txn_category_id",100);

                                if ($_SESSION['t_user_type'] == 'C') {
                                    foreach ($txn_cats as $tcat) {
                                        appButton($tcat['icon_class'], $tcat['txn_category_id'], $tcat['page_action'], $tcat['txn_category_name']);
                                    }
                                } ?>
                                <div class="col-lg-3 col-md-2 col-sm-2 col-3 p-0 text-center">
                                    <a href="index.php?page=shop&txn_cat=6&merchant=13" class="btn btn-outline-light bg-purple shadow rounded-4 w-100">
                                        <img src="../icons/document.png" alt="" class="img-fluid" width="80%">
                                        <span class="fw-bold" style="font-size:10px">DOCUMENT</span>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-2 col-sm-2 col-3 p-0 text-center">
                                    <a href="index.php?page=shop&txn_cat=6&merchant=11" class="btn btn-outline-light bg-purple shadow rounded-4 w-100">
                                        <img src="../icons/rx-delivery.png" alt="" class="img-fluid" width="80%">
                                        <span class="fw-bold" style="font-size:10px">PHARMACY</span>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-2 col-sm-2 col-3 p-0 text-center">
                                    <a href="index.php?page=shop&txn_cat=6&merchant=1" class="btn btn-outline-light bg-purple shadow rounded-4 w-100">
                                        <img src="../icons/groc-delivery.png" alt="" class="img-fluid" width="80%">
                                        <span class=" small fw-bold" style="font-size:10px">GROCERY</span>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-2 col-sm-2 col-3 p-0 text-center">
                                    <a href="index.php?page=shop&txn_cat=6&merchant=12" class="btn btn-outline-light bg-purple shadow rounded-4 w-100">
                                        <img src="../icons/delivery-guy-icon.png" alt="" class="img-fluid" width="80%">
                                        <span class=" small fw-bold" style="font-size:10px">FOOD</span>
                                    </a>
                                </div>
                            
                            </div>
                            <div class="col-lg-8 col-sm-12 col-sm-12 vh-100">
                                <?php include_once "_restaurant_finder.php";?>
                            </div>
                       
                <?php } ?>
      

        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.7.0/dist/js/coreui.bundle.min.js" integrity="sha384-kwU8DU7Bx4h5xZtJ61pZ2SANPo2ukmbAwBd/1e5almQqVbLuRci4qtalMECfu9O6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


<?php 
if(isset($_GET['page'])){
$page = $_GET['page'];
switch($page){  
        case 'rent': ?> <script src="./_car_rental.js"></script> 
 <?php break;  
        case 'angkas': ?> 

 <?php break; 
       case 'shop': ?> 
                <script src="_process_ajax.js"></script>
                <script src="./_shop.js"></script>             
      <?php 
      break;
      default:  ?> <script src="_process_ajax.js"></script>
      <?php 
      }
}
else{ ?>
    <script src="_process_ajax.js"></script>
    <script src="__animations.js"></script>
<?php }
 ?>

</html>
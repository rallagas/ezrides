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
                                ?>
                                <?php 
                                if($cat['page_action'] == $page && $cat['txn_category_id'] == $categ){ 
                                   $txnlink = $cat['txn_link'];
                                ?>          
                                <div class="col-12 p-0" id=""></div>              
                                    <?php include_once $txnlink; 
                                        $_SESSION['txn_cat_id'] = $_GET['txn_cat'];
                                    ?>
                                 </div>
                                <?php 
                                }
                            }
                        
                } $cat=null;
                if ( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'home' ) ) { ?>
                
                            <div class="col-lg-8 offset-lg-4 col-sm-12 col-sm-12 vh-100">
                                

                                <?php include_once "_index_wallet.php"; ?>
                                <h6 class="display-6">Discover Places</h6>
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
                <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A&libraries=maps,places,geometry,marker&callback=initializeApp&loading=async"></script> -->
                <!-- <script src="./_map_config.js"></script>  -->
                <!-- <script src="./_map_func.js"></script>  -->
                <!-- <script src="angkas_map.js"></script> -->
                <!-- <script src="_process_ajax.js"></script> -->
    
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
<?php }
 ?>

</html>
<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
$user_logged = $_SESSION['user_id'];

//if(isset($_GET['page'])){
//    $page=$_GET['page'];
//}
//
//if(isset($_GET['page_action'])){
//    $page_action = $_GET['page_action'];
//}
//if(isset($_GET['page_include_form'])){
//    $page_include_form = $_GET['page_include_form'];
//}
//if(isset($_GET['page_txn_link'])){
//    $page_txn_link = $_GET['page_txn_link'];
//}
//
///*SET SESSION*/
if(isset($_GET['txn_cat'])){
    $_SESSION['txn_category'] = $_GET['txn_cat'];
    $txn_cat = $_SESSION['txn_category'];
}
/*------------*/
/*Include the _car_rental_form.php*/
//if(isset($_GET['car_value'])){
//    ifActionis($page_action,$page_include_form);
//}
//    
//remove booking that are more than 1 hour ago.

query( "DELETE FROM `angkas_bookings` WHERE `date_booked` < (NOW() - INTERVAL 1 HOUR) and user_id = ?", [$user_logged]);

include_once "_map.php";
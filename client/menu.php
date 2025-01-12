<?php
include_once "button-functions.php";

//get all transaction_category
$txn_cats = select_data("txn_category","txn_category_status='A'","txn_category_id",100);

if ($_SESSION['t_user_type'] == 'C') {
    foreach ($txn_cats as $tcat) {
        appButtonIcon($tcat['icon_class'], $tcat['txn_category_id'], $tcat['page_action'], $tcat['txn_category_name']);
    }
    appButtonIcon('document.png','6','shop','DOCUMENT','&merchant=13');
    appButtonIcon('groc-delivery.png','6','shop','GROCERY','&merchant=1');
    appButtonIcon('rx-delivery.png','6','shop','PHARMACY','&merchant=11');
    appButtonIcon('delivery-guy-icon.png','6','shop','FOOD','&merchant=12');
} ?>
<div class="col-4 col-lg-4 col-md-3 col-sm-4 text-center">
    <a href="./_profile/" class="btn btn-outline-warning bg-yellow shadow rounded-4 w-100">
        <img src="../icons/settings.png" alt="" class="quick-links img-fluid" style="height:7vh;">
    </a>
</div>

<?php 
if ($_SESSION['t_rider_status'] == '1'){
    $tcatR = select_data("txn_category","txn_category_status='R'","txn_category_id",100);
    foreach($tcatR as $r){
        appButtonIcon($r['icon_class'], $r['txn_category_id'], $r['page_action'], $r['txn_category_name']);
    }
}
?>
<?php
include_once "button-functions.php";

//get all transaction_category
$txn_cats = select_data("txn_category","txn_category_status='A'","txn_category_id",100);

if($_SESSION['t_user_type'] == 'C'){
    foreach($txn_cats as $tcat){
        appButton($tcat['icon_class']
                    , $tcat['txn_category_id']
                    , $tcat['page_action']
                   );
    }
}

if ($_SESSION['t_rider_status'] == '1'){
   $tcatR = select_data("txn_category","txn_category_status='R'","txn_category_id",100); 
        foreach($tcatR as $r){
        appButton($r['icon_class']
                    , $r['txn_category_id']
                    , $r['page_action']
                   );
    }
}
?>
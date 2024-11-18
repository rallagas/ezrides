<?php
include_once "button-functions.php";

//get all transaction_category
$txn_cat = select_data("txn_category","txn_category_status='A'","txn_category_id",100);

if($_SESSION['t_user_type'] == 'C'){
    foreach($txn_cat as $tcat){
        appButton($tcat['icon_class']
                    , $tcat['txn_category_id']
                    , $tcat['page_action']
                   );
    }
}

if ($_SESSION['t_rider_status'] == 'R'){
   $tcatR = select_data("txn_category","txn_category_status='R'","txn_category_id",100); 
        foreach($tcatR as $r){
        appButton($tcat['icon_class']
                    , $tcat['txn_category_id']
                    , $tcat['page_action']
                   );
    }
}
?>
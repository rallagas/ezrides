<?php
include_once "button-functions.php";

//get all transaction_category
$txn_cat = select_data(CONN,"txn_category","txn_category_status='A'","txn_category_id",100);


if($_SESSION['t_user_type'] == 'C'){
    foreach($txn_cat as $tcat){
        appButton($tcat['icon_class']
                    , $tcat['txn_title']
                    , $tcat['txn_link']
                    , $tcat['txn_category_id']
                    , $tcat['page_action']
                    , $tcat['page_include_form']
                   );
    }
}

if ($_SESSION['t_rider_status'] == 'R'){
   $tcatR = select_data(CONN,"txn_category","txn_category_status='R'","txn_category_id",100); 
        foreach($tcatR as $r){
        appButton($r['icon_class']
                    , $r['txn_title']
                    , $r['txn_link']
                    , $r['txn_category_id']
                    , $r['page_action']
                    , $r['page_include_form']
                    , "bg-primary"
                   );
    }
}
?>
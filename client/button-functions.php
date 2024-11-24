<?php
function getTxnCatInfo ($txn_cat_id){
    $response = [];
    $txn_cat = select_data("txn_category","txn_category_id='{$txn_cat_id}'");
    foreach($txn_cat as $tc){
        extract($tc);
        array_push($tc, $response);
    }
    return $response;
}


function appButton( $icon_class 
                     , $txn_cat=NULL
                     , $page_action=NULL
                     , $bgcolor="bg-purple"
                     ){ ?>
     <div class="col-lg-3 col-md-2 col-sm-2 col-3 p-0 mb-4 text-center">
            
              <a href="index.php?page=<?php echo $page_action; ?>&txn_cat=<?php echo $txn_cat?>" 
                 class="btn btn-outline-light m-0 <?php echo $bgcolor; ?>">
                   <i  class="fs-1 fi fi-rr-<?php echo $icon_class;?>" ></i>
              </a>
               
              
           </div>
<?php } 



?>
          
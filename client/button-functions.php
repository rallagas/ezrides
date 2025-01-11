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
                     , $page_name=NULL
                     , $bgcolor="bg-purple"
                     ){ ?>

<div class="col-lg-1 col-md-2 col-sm-3 col-3 mb-4 text-center d-none d-md-block">
    <a href="index.php?page=<?php echo $page_action; ?>&txn_cat=<?php echo $txn_cat;?>" 
        class="btn btn-outline-light bg-yellow shadow rounded-4 w-100">
        <img src="../icons/<?php echo $icon_class;?>" alt="" class="quick-links img-fluid" width="80%">
        <span class="fw-bold" style="font-size:10px"><?php echo strtoupper($page_name ?? ''); ?></span>
    </a>
</div>

<?php } 


function appButtonIcon( $icon_class 
      , $txn_cat=NULL
      , $page_action=NULL
      , $bgcolor="bg-purple"
      ){ ?>
<div class="col mb-4 text-center">

    <a href="index.php?page=<?php echo $page_action; ?>&txn_cat=<?php echo $txn_cat?>"
        class="btn btn-outline-warning bg-yellow shadow rounded-4 w-75">
        <img src="../icons/<?php echo $icon_class;?>" alt="" class="img-fluid" style="width: 25vw;">

    </a>


</div>
<?php } 
?>
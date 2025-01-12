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
                     , $otherparam=null
                     , $bgcolor="bg-purple"
                     ){ ?>

<div class="col-4 col-lg-1 col-md-3 col-sm-4 text-center">
    <a href="index.php?page=<?php echo $page_action; ?>&txn_cat=<?php echo $txn_cat . $otherparam;?>" 
        class="btn btn-outline-light bg-yellow shadow rounded-4 w-100">
        <img src="../icons/<?php echo $icon_class;?>" alt="" class="quick-links img-fluid" style="height:7vh;">
        <br>
        <span class="fw-bold" style="font-size:10px"><?php echo strtoupper($page_name ?? ''); ?></span>
    </a>
</div>

<?php } 


function appButtonIcon( $icon_class 
      , $txn_cat=NULL
      , $page_action=NULL
      , $page_name=NULL
      , $otherparam=NULL
      , $bgcolor="bg-yellow"
      ){ ?>
<div class="col-lg-4 col-4 col-md-3 col-sm-3 text-center">
    <a href="index.php?page=<?php echo $page_action; ?>&txn_cat=<?php echo $txn_cat . $otherparam; ?>"
        class="btn btn-outline-warning <?php echo $bgcolor;?> shadow rounded-4 w-100">
        <img src="../icons/<?php echo $icon_class;?>" alt="" class="img-fluid" style="height:7vh;">
    </a>
</div>
<?php } 
?>
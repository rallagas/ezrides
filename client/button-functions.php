<?php
function appButton( $icon_class 
                     , $txn_title=NULL
                     , $txn_link=NULL
                     , $txn_cat=NULL
                     , $page_action=NULL
                     , $page_include_form=NULL
                     , $bgcolor="bg-purple"
                     ){ ?>
<!--     <div class="col-lg-1 col-md-3 col-sm-2 col-3 text-center my-3">-->
              <a title="<?php echo $txn_title;?>" 
                 href="?page=<?php echo $txn_title;?>&page_action=<?php echo $page_action;?>&page_txn_link=<?php echo $txn_link;?>&page_include_form=<?php echo $page_include_form;?>&txn_cat=<?php echo $txn_cat;?>" 
                 class="btn btn-outline-light btn-md me-1 <?php echo $bgcolor; ?> rounded-circle p-3">
                   <i class="fi fi-rr-<?php echo $icon_class;?>"></i>
              </a>
                
              
<!--           </div>-->
<?php } 
function navButton( $icon_class 
                     , $txn_title=NULL
                     , $txn_link=NULL
                     , $txn_cat=NULL
                     , $page_action=NULL
                     , $page_include_form=NULL
                     , $bgcolor="bg-purple"
                     ){ ?>
<div class="d-flex">
              <a title="<?php echo $txn_title;?>" 
                 href="?page=<?php echo $txn_title;?>&page_action=<?php echo $page_action;?>&page_txn_link=<?php echo $txn_link;?>&page_include_form=<?php echo $page_include_form;?>&txn_cat=<?php echo $txn_cat;?>" 
                 class="btn btn-outline-light btn-sm mx-1 <?php echo $bgcolor; ?> rounded-circle p-3">
                   <i class="fi fi-rr-<?php echo $icon_class;?>"></i>
              </a>
</div>
<?php } 



?>
          
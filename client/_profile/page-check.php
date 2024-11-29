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
<?php 
include_once "../_db.php";
include_once "../_functions.php";
include_once "../_sql_utility.php";
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>EZ Rides</title>
     <link rel="stylesheet" href="../css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="../bootstrap-icons-1.11.3/font/bootstrap-icons.css">-->
     <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
     <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  
   
   <div class="container-fluid px-0">
      <div class="row">
          <div class="col-lg-12">  
              <?php include_once "nav-client.php";?>
          </div>
      </div>
      <div class="row p-5">
              <?php include_once "menu.php"; 
          
                $txn_cat = select_data(CONN,"txn_category",NULL,"txn_category_id",15); ?>
          
          <div class="col-12">
         <?php  foreach($txn_cat as $cat){
              ifPageis($cat['page_action'],$cat['txn_link']);
          } ?>
         </div>
                  
      </div>
      <div class="row px-5" id="queryresult"></div>
   </div>
    
</body>
<script src="../js/bootstrap.js"></script>
<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../process_ajax.js"></script>

<script>
    $(document).ready(function(){
        
        
        var spinner="<div class='mt-5 spinner-border spinner-border-sm'></div>";
        var grower="<div class='mt-5 spinner-grow spinner-grow-sm'></div>";
         
        
        $("#userLogOut").click(function(){
            $("body").html("<center>"+grower+grower+grower+"</center>");
            setTimeout(function(){
                window.location.assign("../index.php?logout");
            }, 2500);

        });
        
    });
    </script>

</html>
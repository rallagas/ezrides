<?php 
include_once "../../_db.php";
include_once "../../_sql_utility.php";
include_once "../_class_grocery.php";
include_once "func.php";

if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    header("location: ../../index.php?logoutSuccessful");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .bg-purple{
            background-color:mediumpurple;
        }
    </style>
</head>
<body>
   
   <div class="container-fluid">
       <div class="row"  style="height:100vh">
           <div class="col-3 px-0 bg-light" id="sidePanel">
               <?php include_once "sidepanel.php";?>
           </div>
           <div class="col-9" id="controlDashboard">
<!--               load page functions here -->
                      <?php
                            loadPage();
                        ?>
                      
           </div>
       </div>
         
       
   </div>
   
   
    
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_loader.js"></script>
</html>
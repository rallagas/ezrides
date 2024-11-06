<?php 
include_once "../_class_grocery.php";
include_once "func.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .bg-purple{
            background-color:blueviolet;
        }
    </style>
</head>
<body>
   
   <div class="container-fluid">
       <div class="row">
           <div class="col-12" id="controlDashboard">
<!--               load page functions here -->
                      <?php
                            include_once "_buy.php";
                        ?>
           </div>
       </div>
         
       
   </div>
   
   
    
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_loader.js"></script>
</html>
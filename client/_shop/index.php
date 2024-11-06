<?php
include_once "../../_db.php";
include_once "../../_sql_utility.php";
$user_logged = $_SESSION['user_id'];

if(isset($_GET['page'])){
    $page=$_GET['page'];
}

if(isset($_GET['page_action'])){
    $page_action = $_GET['page_action'];
}
if(isset($_GET['page_include_form'])){
    $page_include_form = $_GET['page_include_form'];
}
if(isset($_GET['page_txn_link'])){
    $page_txn_link = $_GET['page_txn_link'];
}

/*SET SESSION*/
if(isset($_GET['txn_cat'])){
    //1 = Car Rental
    //2 = Angkas
    $_SESSION['txn_category'] = $_GET['txn_cat'];
    $txn_cat = $_SESSION['txn_category'];
}



 include "_class_grocery.php";
$db = new Database();
$db->dbConnection();


?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
   
   <div class="container">
       <div class="row">
         
          <?php
           
           $products = Product::fetchAllProducts($db);
           if(!empty($products)){
           foreach($products as $p){
           ?>
               <div class="col-lg-4 col-sm-12">
                  
                   <?php echo $p->getName(); ?>
               
               </div>
           <?php }
           } else {
               
               echo "No Items";
           }?>
           
       </div>
       
   </div>
   
   
    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</html>
<?php
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
/*------------*/


/*Include the _car_rental_form.php*/
if(isset($_GET['car_value'])){
 
ifActionis($page_action,$page_include_form);   
}

/*Show car selection*/

$vehicles = select_data( "lu_cars");

foreach($vehicles as $car) {
    ?>
   <div class="card col-3 px-0">
  <img src="../car_image/<?php echo $car['car_img'];?>" class="card-img-top m-0" alt="...">
  <div class="card-body">
    <h5 class="card-title"><?php echo $car['car_year_model'] . " " . $car['car_brand'];?></h5>
    <p class="card-text"><?php echo "Php ". $car['car_rent_price'];?></p>
    <a href="?page=<?php echo $page?>&page_action=<?php echo $page_action;?>&page_txn_link=<?php echo $page_txn_link;?>&page_include_form=<?php echo $page_include_form; ?>&txn_cat=<?php echo $txn_cat;?>&car_value=<?php echo $car['car_id']; ?>" class="btn btn-primary"><?php echo $page_action;?></a>
  </div>
</div>
   
<?php 
} //end of foreach
?>
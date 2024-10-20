<?php
include_once "../_db.php";
include_once "../_sql_utility.php";


$userLogged = $_SESSION['user_id'];
$region = $_POST['f_region'];
$province = $_POST['f_province'];
$municipality = $_POST['f_municipality'];
$location_id = $region . "-" . $province . "-" . $municipality;
$txn_cat = $_SESSION['txn_category'];
$item_reference_id = $_POST['f_car_id'];

//check if item is already logged into inventory
$item_inv_data = select_data(CONN, "items_inventory","txn_category_id=$txn_cat AND item_reference_id=$item_reference_id");

if(empty($item_inv_data)){
    //insert a new record for the item
    $car_info = select_data(CONN, "lu_cars", "car_id=$item_reference_id");
    foreach($car_info as $ci){
        $item_description = "RENTAL:" . $ci['car_id'] . ":" . $ci['car_brand'] . ":" . $ci['car_rent_price'] . ":" . $ci['car_plate_no'];
        $car_owner = $ci['car_owner_id'];
        $item_price = $ci['car_rent_price'];
    }
    
    $inv_table = "items_inventory"; 
    $inv_data = array(
        'item_reference_id' => $item_reference_id,
        'item_description' => $item_description,
        'vendor_id' => $car_owner,
        'txn_category_id' => $txn_cat,
        'item_price' => $item_price
    );
    
    insert_data(CONN,$inv_table,$inv_data);
    
    $items_inventory_id = getLastInsertedId(CONN, $inv_table);
    
}
else{
    foreach($item_inv_data as $iid){
        $items_inventory_id = $iid['items_inventory_id'];
    }
}

//insert into app_transactions

$from_dte = $_POST['f_rent_from_date'];
$to_dte = $_POST['f_rent_to_date'];

$table = "app_transactions";
$data = array(
    'user_id' => $userLogged,
    'txn_category_id' =>$txn_cat,
    'book_start_dte' => $from_dte,
    'book_end_dte' => $to_dte,
    'book_location_id' => $location_id,
    'book_item_inventory_id' => $items_inventory_id
);

insert_data(CONN, $table, $data);

echo "Car has been booked.";
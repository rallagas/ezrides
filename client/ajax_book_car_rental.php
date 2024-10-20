<?php
include_once "../_db.php";
include_once "../_sql_utility.php";




if(isset($_POST['rent_this_vehicle'])){
    $vehicle_id = $_POST['rent_this_vehicle']; 
    $book_start_dte = $_POST['bookStartDte'];
    $book_end_dte = $_POST['bookEndDte'];
    $location_id = $_POST['toDestination'];
    $userLogged = $_SESSION['user_id'];
    $txn_cat= 1;

    $chk_item_inv = select_data(CONN,"items_inventory","item_reference_id=$vehicle_id AND txn_category_id=1");
    
      $car_info = select_data(CONN, "vehicle", "vehicle_id=$vehicle_id and vehicle_txn_type=1");
        foreach($car_info as $ci){
            $item_description = "RENTAL:" . $ci['vehicle_id'] . ":" . $ci['vehicle_model'] . ":" . $ci['vehicle_price_rate_per_day'] . ":" . $ci['vehicle_plate_no'];
            $vehicle_model = $ci['vehicle_model'] ;
            $car_owner = $ci['vehicle_owner_id'];
            $item_price = $ci['vehicle_price_rate_per_day'];
            $item_reference_id = $ci['vehicle_id'];
        }
    
    
    if(empty($chk_item_inv)){
      
    
        $inv_table = "items_inventory"; 
        $inv_data = array(
            'item_reference_id' => $item_reference_id,
            'item_description' => $item_description,
            'vendor_id' => $car_owner,
            'txn_category_id' => 1,
            'item_price' => $item_price
        );

        insert_data(CONN,$inv_table,$inv_data);

        $items_inventory_id = getLastInsertedId(CONN, $inv_table);
        $rate_per_day = $item_price;
    }
    else{
        foreach($chk_item_inv as $iid){
            //get the inventory_id of the vehicle if its already in the inventory
            $items_inventory_id = $iid['items_inventory_id'];
            $rate_per_day = $iid['item_price'];
        }
    }
    
    //insert into app_transactions
$number_of_days = calculateDaysDifference($book_start_dte,$book_end_dte);
$amount_to_pay = ($rate_per_day * $number_of_days);
$table = "app_transactions";
$data = array(
    'user_id' => $userLogged,
    'txn_category_id' =>$txn_cat,
    'book_start_dte' => $book_start_dte,
    'book_end_dte' => $book_end_dte,
    'book_location_id' => $location_id,
    'book_item_inventory_id' => $items_inventory_id,
    'amount_to_pay' => $amount_to_pay
);

insert_data(CONN, $table, $data);

echo "Car Booked, Pending for confirmation.<br>";
echo "Summary:<br>";
echo "You booked to Rent " . $vehicle_model . " between " .  $book_start_dte . " and " . $book_end_dte . " for " . $number_of_days .  " days going to " . getLocationAddress($location_id) . ", Total Amount to pay is Php " . number_format($amount_to_pay,2) ;
    
}

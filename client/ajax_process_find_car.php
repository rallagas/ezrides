<?php
include_once "../_db.php";
include_once "../_sql_utility.php";


$bookStartDate = $_POST['f_rent_from_date'];
$bookEndDate = $_POST['f_rent_to_date'];
$region = $_POST['f_region'];
$province = $_POST['f_province'];
$municipality = $_POST['f_municipality'];
$location = $region . "-" . $province . "-" . $municipality;

$params = array($bookSta<?php
// Enable CORS for the response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Validate the API key and parameters
if (!isset($_GET['origins']) || !isset($_GET['destinations']) || !isset($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters.']);
    exit;
}

// Extract parameters from the request
$origins = urlencode($_GET['origins']);
$destinations = urlencode($_GET['destinations']);
$apiKey = urlencode($_GET['key']);

// Construct the Google Maps Distance Matrix API URL
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$origins&destinations=$destinations&key=$apiKey";

// Make the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching data from Google API.', 'details' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Close the cURL session
curl_close($ch);

// Forward the API response to the client
header('Content-Type: application/json');
echo $response;
?>
rtDate,$bookEndDate);

$sql_get_list_of_available = 
       "SELECT distinct v.vehicle_id, 
              v.vehicle_type, 
              v.vehicle_plate_no, 
              v.vehicle_color, 
              v.vehicle_model, 
              v.vehicle_img, 
              v.vehicle_owner_id, 
              v.vehicle_price_rate_per_hr, 
              v.vehicle_price_rate_per_day, 
              v.vehicle_price_rate_per_km, 
              v.vehicle_txn_type, 
              at.book_start_dte AS book_start_dte, 
              at.book_end_dte AS book_end_dte 
            FROM  vehicle v 
              LEFT JOIN items_inventory ii ON (
                ii.item_reference_id = v.vehicle_id
              ) 
              LEFT JOIN app_transactions as at ON (
                at.book_item_inventory_id = ii.items_inventory_id 
                AND at.txn_category_id = ii.txn_category_id
              ) 
            WHERE v.vehicle_txn_type = 1
              AND (
                  (at.book_start_dte is null and at.book_end_dte is null)
                  OR
                  (( STR_TO_DATE(?,'%Y %M %d') NOT BETWEEN at.book_start_dte AND at.book_end_dte)
                  AND
                  ( STR_TO_DATE(?,'%Y %M %d') NOT BETWEEN at.book_start_dte AND at.book_end_dte))
                  );
                  ";

$list = query( $sql_get_list_of_available, $params);
if(empty($list)){
    echo "No Available Vehicle at the moment.";
}
else { ?>
    
<h3 class="fw-bold">Select Car</h3>
<?php foreach($list as $car){ ?>
        <div class="card col-lg-3 col-md-4 col-sm-6 col-xs-12 px-2 mb-5 mx-0 border-0">
          <img src="../car_image/<?php echo $car['vehicle_img'];?>" class="rounded card-img-top mb-3 object-fit-cover h-75"  alt="...">
          <div class="card-body">
            <h5 class="card-title"><?php echo $car['vehicle_model'] ;?> </h5>
           
            <p class="card-text"><?php echo "Php ". $car['vehicle_price_rate_per_day'];?> per day</p>
           <div class="card-footer" id="alertid<?php echo $car['vehicle_id'];?>">
                <button type="submit" data="<?php echo $car['vehicle_id'];?>" class="btn-rent-car btn btn-sm btn-primary">Book This</button> 
               <div class="input-group">
                   <input hidden id="book_start_dte_vehicle<?php echo $car['vehicle_id'];?>" value="<?php echo  $bookStartDate; ?>" type="text" class="form-control">
                   <input hidden  id="book_end_dte_vehicle<?php echo $car['vehicle_id'];?>" value="<?php echo  $bookEndDate; ?>" type="text" class="form-control">
                   <input hidden type="text" id="toDestination<?php echo $car['vehicle_id'];?>" value="<?php echo $location;?> " class="form-control">
               </div>
               
           </div>
          </div>
        </div>
<?php }
}
?>

<script>
$(document).ready(function(){
    $('button.btn-rent-car').click(function(e){
    var vehicle_id = $(this).attr('data');
    var book_start_dt = $("input#book_start_dte_vehicle" + vehicle_id).val();
    var book_end_dt = $("input#book_end_dte_vehicle" + vehicle_id).val();
    var toDestination = $("input#toDestination" + vehicle_id).val();
   
  $.post("ajax_book_car_rental.php",
  {
    rent_this_vehicle: vehicle_id,
    bookStartDte : book_start_dt,
    bookEndDte : book_end_dt,
    toDestination : toDestination
  },
  function(data){
       
    $(".card-footer#alertid" + vehicle_id).html("<div class='alert alert-success'>" + data + "</div>");

						
  });
    
    e.preventDefault();
});
    
});
</script>





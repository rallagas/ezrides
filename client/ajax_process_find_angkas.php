<?php
include_once "../_db.php";
include_once "../_sql_utility.php";
include_once "_class_Bookings.php";

$response = []; // Initialize response array

if (isset($_SESSION['txn_cat_id'])) {
    $txn_cat_id = $_SESSION['txn_cat_id'];
} else {
    // Handle the case where the session variable is not set
    $txn_cat_id = 2; // Or any default value you want to assign
    throw new Exception("Transaction category ID is not set in the session."); 
}
$prefix = TxnCategory::getTxnPrefix($txn_cat_id);

// Define required POST parameters
$requiredParams = [
    'form_from_dest',
    'curLocCoor',
    'form_to_dest',
    'formToDest_long',
    'formToDest_lat',
    'form_ETA_duration',
    'form_TotalDistance',
    'form_Est_Cost'
];

// Check for missing parameters
$missingParams = [];
foreach ($requiredParams as $param) {
    if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
        $missingParams[] = $param;
    }
}

if (!empty($missingParams)) {
    // Respond with missing parameters
    $response['error'] = true;
    $response['message'] = "Invalid request. Missing required parameters.";
    $response['missingParameters'] = $missingParams;

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Sanitize and extract POST parameters
$from_loc_name = mysqli_real_escape_string(CONN, $_POST['form_from_dest']);
$curLocCoor = mysqli_real_escape_string(CONN, $_POST['curLocCoor']);
list($from_loc_lat, $from_loc_long) = explode(',', $curLocCoor);
$to_loc_name = mysqli_real_escape_string(CONN, $_POST['form_to_dest']);
$to_loc_lat = mysqli_real_escape_string(CONN, $_POST['formToDest_long']);
$to_loc_long = mysqli_real_escape_string(CONN, $_POST['formToDest_lat']);
$eta_mins = mysqli_real_escape_string(CONN, $_POST['form_ETA_duration']);
$total_dis = mysqli_real_escape_string(CONN, $_POST['form_TotalDistance']);
$total_cost = mysqli_real_escape_string(CONN, $_POST['form_Est_Cost']);
$user_logged = $_SESSION['user_id'];
$ref_num = gen_book_ref_num(8, $prefix);

// Check for existing pending booking
$check_data = select_data("angkas_bookings", "user_id = {$user_logged} AND DATE(date_booked) = CURRENT_DATE AND booking_status = 'P' and transaction_category_id = {$txn_cat_id}");

if (!empty($check_data)) {
    $response['hasPendingBooking'] = true;
    $response['message'] = "There is still a pending booking. See details below:";
    $response['pendingBookings'] = [];

    foreach ($check_data as $booking) {
        $response['pendingBookings'][] = [
            'angkas_booking_reference' => $booking['angkas_booking_reference'],
        ];
    }
} else {
    // Extract ETA in minutes
    preg_match('/\d+/', $eta_mins, $matches);
    $eta_in_minutes = (float)$matches[0];

    // Prepare data for insertion
    $data = [
        "angkas_booking_reference" => $ref_num,
        "user_id" => $user_logged,
        "form_from_dest_name" => $from_loc_name,
        "user_currentLoc_lat" => $from_loc_lat,
        "user_currentLoc_long" => $from_loc_long,
        "form_to_dest_name" => $to_loc_name,
        "formToDest_long" => $to_loc_long,
        "formToDest_lat" => $to_loc_lat,
        "form_ETA_duration" => $eta_in_minutes,
        "form_TotalDistance" => trim(str_replace('km','',$total_dis)),
        "form_Est_Cost" => $total_cost,
        "transaction_category_id" => $txn_cat_id
    ];

    // Use the AngkasBookings class to insert the booking
    $bookings = new AngkasBookings();
    if ($bookings->insertBooking($data)) {
        $response['hasPendingBooking'] = false;
        $response['message'] = "Booking successfully created. Waiting for Rider.";
        $response['bookingReference'] = $ref_num;
        $response['prefix'] = $prefix;
    } else {
        $response['hasPendingBooking'] = false;
        $response['message'] = "Failed to create booking. Please try again later.";
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

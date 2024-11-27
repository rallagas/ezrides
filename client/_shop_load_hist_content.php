<?php
header('Content-Type: application/json'); // Set response to JSON
include_once "../_db.php";
include_once "_class_Bookings.php";
include_once "../_sql_utility.php";

try {
    // Retrieve and sanitize `view_type` from the request
    $view_type = isset($_POST['view_type']) ? (int)$_POST['view_type'] : null;
    $user_id = USER_LOGGED; // Assuming USER_LOGGED is defined as the logged-in user ID

    if (!$view_type || !$user_id) {
        throw new Exception("Missing required parameters: view_type or user_id.");
    }

    $columns = '*';
    $distinct = null;

    // Base SQL
    $sql_shop_order_ref = "SELECT $distinct $columns FROM `shop_booking_header_view` WHERE `customer_user_id` = ? AND order_state_ind <> 'C'";
    $append_sql = "";

    // Determine the SQL condition based on view_type
    switch ($view_type) {
        case 1:
            $append_sql = " AND `angkas_booking_reference` IS NULL";
            break;
        case 2:
            $append_sql = " AND `angkas_booking_reference` IS NOT NULL and `shop_payment_status` = 'C' and `booking_payment_status` = 'C' AND `rider_user_id` IS NULL";
            break;
        case 3:
            $append_sql = " AND `shop_payment_status` = 'C' AND `booking_payment_status` = 'C' AND `rider_user_id` IS NOT NULL";
            break;
        case 4:
            $append_sql = " AND (`shop_payment_status` = 'P' or `shop_payment_status` IS NULL or `shop_payment_status` = '') AND (`booking_payment_status` = 'P' or `booking_payment_status` IS NULL)";
            break;
    }

    $final_sql = $sql_shop_order_ref . $append_sql;

    // Execute query and fetch results
    $resultArr = query($final_sql, [$user_id]);

    // Return success response
    echo json_encode([
        "success" => true,
        "data" => $resultArr,
        "SQL" => $final_sql
    ]);
} catch (Exception $e) {
    // Handle errors and return as JSON response
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "exception" => $e,
    ]);
}
?>

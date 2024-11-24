<?php
header('Content-Type: application/json'); // Set response to JSON
include_once "../_db.php";
include_once "_class_Bookings.php";
include_once "../_sql_utility.php";

try {
    // Retrieve and sanitize `view_type` from the request
    $ref_num = isset($_POST['ref_num']);
    $user_id = USER_LOGGED; // Assuming USER_LOGGED is defined as the logged-in user ID

    $columns = '*';
    $distinct = null;

    // Base SQL
    $sql_shop_order_ref = "SELECT $distinct $columns FROM `shop_item_merchant_view` WHERE `customer_user_id` = ? AND `shop_order_ref_num` = ?";
    $final_sql = $sql_shop_order_ref;

    // Execute query and fetch results
    $resultArr = query($final_sql, [$user_id, $ref_num]);

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

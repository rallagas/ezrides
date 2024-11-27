<?php
header('Content-Type: application/json'); // Set response to JSON
include_once "../_db.php";
include_once "_class_Bookings.php";
include_once "../_sql_utility.php";

try {
    // Retrieve and sanitize `ref_num` from the request
    $ref_num = isset($_POST['ref_num']) ? trim($_POST['ref_num']) : null;
    if (!$ref_num) {
        throw new Exception("Invalid reference number provided.");
    }

    $user_id = USER_LOGGED; // Assuming USER_LOGGED is defined as the logged-in user ID

    // Use prepared statements to avoid SQL injection
    $sql_shop_order_ref = "SELECT * FROM `shop_item_merchant_view` WHERE `customer_user_id` = ? AND `shop_order_ref_num` = ?";
    $stmt = CONN->prepare($sql_shop_order_ref);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("is", $user_id, $ref_num); // Bind parameters
    $stmt->execute();
    $resultArr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Return success response
    echo json_encode([
        "success" => true,
        "data" => $resultArr,
        "SQL" => $sql_shop_order_ref
    ]);
} catch (Exception $e) {
    // Handle errors and return as JSON response
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "exception" => $e->getTraceAsString(),
    ]);
}
?>

<?php
include_once "../../../_db.php"; // Include your database connection

$response = ["success" => false, "count" => 0];

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Query to get the total item count in the cart for the user
    $query = "SELECT count(*) AS cart_count FROM shop_orders WHERE user_id = ? and order_state_ind = 'C'";
    $stmt = CONN->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($cart_count);
    $stmt->fetch();

    $response["success"] = true;
    $response["count"] = $cart_count ?? 0; // Default to 0 if no items
}

echo json_encode($response);
?>

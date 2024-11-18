<?php
include_once "../../_db.php"; // Include your database connection
include_once "_class_grocery.php"; // Include the Merchant, Product, and Cart classes

$userId = USER_LOGGED;

// Check if the user ID is valid
if (NULL === $userId) {
    echo json_encode(["success" => false, "message" => "Invalid user session"]);
    exit;
}

// Fetch cart items for the user directly from the shop_orders table
$query = "
    SELECT ci.item_id, ci.quantity
    FROM shop_orders ci
    WHERE ci.user_id = ? AND ci.order_state_ind = 'C'
";
$stmt = CONN->prepare($query);
$stmt->bind_param("s", $userId);

$response = ["success" => true, "cartItems" => []];

if ($stmt->execute()) {
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Fetch the product details for each item in the cart
        $product = Product::fetchById($row['item_id']);
        if ($product) {
            // Prepare the response data with product details and quantity
            $response["cartItems"][] = [
                "item_id" => $product->getId(),
                "item_name" => $product->getName(),
                "price" => $product->getPrice(),
                "quantity" => $row['quantity'],
                "item_img" => $product->getItemImg()
            ];
        }
    }
} else {
    $response["success"] = false;
    $response["message"] = "Failed to fetch cart items.";
}

// Return the response as JSON
header("Content-Type: application/json");
echo json_encode($response);

<?php
include_once "../../_db.php"; // Include your database connection
include_once "_class_grocery.php"; // Include the Merchant, Product, and Cart classes

// Initialize user ID
$userId = USER_LOGGED;

// Check if the user ID is valid
if (NULL === $userId) {
    echo json_encode(["success" => false, "message" => "Invalid user session"]);
    exit;
}

try {
    // Create a Cart instance
    $cart = new Cart($userId);

    // Fetch cart details
    $cartItems = $cart->getCartDetails();

    // Prepare the response
    if (empty($cartItems)) {
        echo json_encode(["success" => false, "message" => "Your cart is empty."]);
    } else {
        echo json_encode(["success" => true, "cartItems" => $cartItems]);
    }
} catch (Exception $e) {
    // Handle errors gracefully
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>

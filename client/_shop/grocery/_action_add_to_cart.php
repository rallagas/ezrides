<?php
include_once "../../../_db.php"; // Include your database connection
include_once "../_class_grocery.php"; // Include the Product class

$response = ["success" => false, "message" => ""];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response["message"] = "User not logged in.";
    echo json_encode($response);
    exit;
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Validate input data
if (isset($_POST['item_id'], $_POST['quantity'])) {
    $itemId = $_POST['item_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product price
    $product = Product::fetchById($itemId); // Assuming a static method fetchById to get a product instance by item_id
    if (!$product) {
        $response["message"] = "Item not found.";
        echo json_encode($response);
        exit;
    }

    $price = $product->getPrice();
    $amountToPay = $price * $quantity;

    // Define data for insert or update in cart
    $data = [
        'user_id' => $userId,
        'item_id' => $itemId,
        'quantity' => $quantity,
        'amount_to_pay' => $amountToPay
    ];

    // Use sql::query to insert or update with ON DUPLICATE KEY UPDATE
    $query = "
        INSERT INTO shop_orders (user_id, item_id, quantity, amount_to_pay) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            quantity = quantity + VALUES(quantity),
            amount_to_pay = amount_to_pay + VALUES(amount_to_pay)";
    
    $result = query($query, array_values($data));

    // Check result and respond
    if ($result) {
        $response["success"] = true;
        $response["message"] = "Item added to cart.";
    } else {
        $response["message"] = "Error adding item to cart.";
    }
} else {
    $response["message"] = "Invalid input.";
}

echo json_encode($response);
?>

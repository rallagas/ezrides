
<?php
include_once "../../_db.php"; // Include database connection
include_once "_class_grocery.php"; // Include the Product class

$response = ["success" => false, "message" => ""];

// Get the user ID from the session
$userId = USER_LOGGED;

// Create the Cart object for the user
$items = array("item_id" => $_POST['item_id']);
$cart = new Cart($userId, $items);

// Validate input data
if (isset($_POST['item_id'], $_POST['quantity'])) {
    $itemId = $_POST['item_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch the product
    $product = Product::fetchById($itemId); // Assuming a static method fetchById to get a product instance
    if (!$product) {
        $response["message"] = "Item not found.";
        echo json_encode($response);
        exit;
    }

    // Add to cart
    $cart->addToCart($product, $quantity);

    $response["success"] = true;
    $response["message"] = "Item added to cart successfully.";
} else {
    $response["message"] = "Invalid input.";
}

echo json_encode($response);

?>

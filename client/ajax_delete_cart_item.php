<?php
include_once "../_db.php";
include_once "./_shop/_class_grocery.php";

header("Content-Type: application/json");

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

// Get the Order IDs from the request
$orderIds = $_POST['orderIds'] ?? [];

if (empty($orderIds) || !is_array($orderIds)) {
    echo json_encode(["success" => false, "message" => "No valid Order IDs provided."]);
    exit;
}

$userId = USER_LOGGED;

if (NULL === $userId) {
    echo json_encode(["success" => false, "message" => "Invalid user session."]);
    exit;
}

try {
    // Initialize the Cart instance
    $cart = new Cart($userId);

    // Attempt to delete the items
    $result = $cart->deleteItems($orderIds);

    // Return the response
    echo json_encode(["success" => $result]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>

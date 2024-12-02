<?php
include_once "../../_db.php"; // Ensure database connection
include_once "../_class_grocery.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $productId = intval(trim($_POST['itemId'] ?? 0));
        $itemName = trim($_POST['ItemName'] ?? '');
        $submittedMerchantId = intval(trim($_POST['merchantId'] ?? 0));
        $merchantName = trim($_POST['MerchantName'] ?? '');
        $itemPrice = floatval(trim($_POST['itemPrice'] ?? ''));
        $errors = [];

        // Validate required fields
        if (!$productId) $errors[] = 'Product ID is required.';
        if (!$itemName) $errors[] = 'Item name is required.';
        if (!$itemPrice) $errors[] = 'Price is required.';

        if ($errors) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid request parameters', 'details' => $errors]);
            exit;
        }

        // Fetch the product by ID
        $product = Product::fetchById($productId);
        if (!$product) {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Product not found.']);
            exit;
        }

        // Default item image to current if not changed
        $itemImg = $product->getItemImg();

        // Handle file upload if provided
        if (!empty($_FILES['itemNewImg']['name'])) {
            $uploadDir = '../../client/_shop/item-img/';
            $newImgName = basename($_FILES['itemNewImg']['name']);
            $uploadPath = $uploadDir . $newImgName;

            if (move_uploaded_file($_FILES['itemNewImg']['tmp_name'], $uploadPath)) {
                $itemImg = $newImgName; // Update to new image name
            } else {
                throw new Exception("Failed to upload image.");
            }
        }

        // Fetch merchant ID based on the merchant name
        $merchantId = Merchant::fetchMerchantIdByName($merchantName);
        if (!$merchantId) {
            // Use the submitted merchant ID if the name doesn't match any merchant
            $merchantId = $submittedMerchantId;
        }

        // Update the product
        $success = $product->updateItem($itemName, $itemPrice, $merchantId, $itemImg);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to update the item.']);
        }
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}

<?php
include_once '../../_db.php';
$response = ['success' => false];

try {
    // Validate required inputs
    $itemName = isset($_POST['ItemName']) ? trim($_POST['ItemName']) : '';
    $itemPrice = isset($_POST['ItemPrice']) ? (float)$_POST['ItemPrice'] : 0.00;
    $merchantName = isset($_POST['MerchantName']) ? trim($_POST['MerchantName']) : '';

    if (empty($itemName) || $itemPrice <= 0 || empty($merchantName)) {
        throw new Exception('All fields except the image are required.');
    }

    // Fetch the merchant ID based on the name
    $stmt = CONN->prepare("SELECT merchant_id FROM shop_merchants WHERE name = ?");
    $stmt->bind_param('s', $merchantName);
    $stmt->execute();
    $merchantResult = $stmt->get_result();

    if ($merchantResult->num_rows === 0) {
        throw new Exception('Merchant not found.');
    }

    $merchant = $merchantResult->fetch_assoc();
    $merchantId = $merchant['merchant_id'];

    // Handle file upload
    $itemImg = null; // Default if no file is uploaded
    if (isset($_FILES['itemImg']) && $_FILES['itemImg']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../client/_shop/item-img/";
        $fileName = uniqid() . "_" . basename($_FILES['itemImg']['name']);
        $targetFile = $targetDir . $fileName;

        // Validate the file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['itemImg']['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }

        if (!move_uploaded_file($_FILES['itemImg']['tmp_name'], $targetFile)) {
            throw new Exception('Failed to upload image.');
        }

        $itemImg = $fileName; // Save the file name to store in the database
    }

    // Insert the new item into the database
    $stmt = CONN->prepare(
        "INSERT INTO shop_items (item_name, price, merchant_id, item_img) 
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('sdss', $itemName, $itemPrice, $merchantId, $itemImg);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        throw new Exception('Failed to save item. Please try again.');
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

<?php
include_once "../../_db.php";
include_once "_class_grocery.php";
include_once "../_class_Bookings.php";

// Initialize the response array with default values
$response = [
    "CartPlaceOrder" => false,
    "AngkasBookings" => false,
    "order_items" => [],
    "success" => false,
    "message" => '',
    "OrderRefNum" => '',
    "totalAmountToPay" => 0,
    "AngkasBookingInfo" => [],
    "statusCode" => 500 // Default status code for error
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Handle FormData or raw JSON input
        $isMultipart = strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false;

        if ($isMultipart) {
            // Handle FormData input
            $data = [
                'order_items' => isset($_POST['order_items']) ? json_decode($_POST['order_items'], true) : [],
                'order_ref_num' => $_POST['order_ref_num'] ?? '',
                'shipping_name' => $_POST['shipping_name'] ?? '',
                'shipping_address' => $_POST['shipping_address'] ?? '',
                'shipping_phone' => $_POST['shipping_phone'] ?? '',
                'shipping_coordinates' => $_POST['shipping_coordinates'] ?? '',
                'payment_mode' => $_POST['payment_mode'] ?? '',
                'merchant_address' => $_POST['merchant_address'] ?? '',
                'merchant_loc_coor' => $_POST['merchant_loc_coor'] ?? '',
                'estCost' => floatval($_POST['estCost'] ?? 0),
                'etaTime' => $_POST['etaTime'] ?? '',
                'etaDistanceKm' => floatval($_POST['etaDistanceKm'] ?? 0),
                'additionalNotes' => $_POST['additionalNotes'] ?? '',
                'additionalFile' => NULL
            ];

            // Handle file attachments (if any)
            if (isset($_FILES['additionalFile']) && $_FILES['additionalFile']['error'] === UPLOAD_ERR_OK) {
                $uploadedFilePath = 'additional-attachments/' . basename($_FILES['additionalFile']['name']);
                if (!move_uploaded_file($_FILES['additionalFile']['tmp_name'], $uploadedFilePath)) {
                    throw new Exception("Failed to upload file.");
                }
                $data['additionalFile'] = $uploadedFilePath;
            }
        } else {
            // Handle raw JSON input
            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, true); // Decode JSON into an associative array
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON input.");
            }
        }

        // Validate user login
        $userId = USER_LOGGED; // Assuming USER_LOGGED is predefined
        if (!$userId) {
            throw new Exception("User is not logged in.");
        }

        // Extract the values from the decoded data
        $orderItems = $data['order_items'] ?? [];
        $orderRefNum = $data['order_ref_num'] ?? '';
        $shippingName = $data['shipping_name'] ?? '';
        $shipToAddress = $data['shipping_address'] ?? '';
        $shippingPhone = $data['shipping_phone'] ?? '';
        $shipToCoordinates = $data['shipping_coordinates'] ?? '';
        $paymentMode = $data['payment_mode'] ?? '';
        $shipFromAddress = $data['merchant_address'] ?? '';
        $shipFromCoordinates = $data['merchant_loc_coor'] ?? '';
        $estCost = floatval($data['estCost'] ?? 0);
        $ETA = $data['etaTime'] ?? '';
        $etaDistanceKm = floatval($data['etaDistanceKm'] ?? 0);
        $additionalNote = $data['additionalNotes'];
        $additionalFile = $data['additionalFile'];

        // Calculate total amount and collect item/order IDs
        $totalAmount = 0.00;
        $order_ids = [];

        foreach ($orderItems as $item) {
            $totalAmount += $item['amount'];
            $order_ids[] = $item['orderId'];
        }

        // Instantiate the Cart class and place order
        $cart = new Cart($userId, $orderItems);
        $shippingDetails = [
            'name' => $shippingName,
            'address' => $shipToAddress,
            'phone' => $shippingPhone,
            'coordinates' => $shipToCoordinates,
        ];

        if (!$cart->placeOrder($orderRefNum, $shippingDetails, $order_ids)) {
            $response['CartPlaceOrder'] = false;
            throw new Exception("Failed to place the order.");
        } else {
            $response['CartPlaceOrder'] = true;
        }

        // Split coordinates for Angkas booking
        list($userLat, $userLong) = explode(',', $shipFromCoordinates);
        list($destLat, $destLong) = explode(',', $shipToCoordinates);

        $angkasData = [
            'angkas_booking_reference' => gen_book_ref_num(8, TxnCategory::getTxnPrefix($_SESSION['txn_cat_id'])),
            'shop_order_reference_number' => $orderRefNum,
            'shop_cost' => $totalAmount,
            'user_id' => $userId,
            'form_from_dest_name' => $shipFromAddress,
            'user_currentLoc_lat' => (float) trim($userLat),
            'user_currentLoc_long' => (float) trim($userLong),
            'form_to_dest_name' => $shipToAddress,
            'formToDest_lat' => (float) trim($destLat),
            'formToDest_long' => (float) trim($destLong),
            'form_ETA_duration' => $ETA,
            'form_TotalDistance' => $etaDistanceKm,
            'form_Est_Cost' => $estCost,
            'booking_status' => 'P',
            'payment_status' => 'P',
            'transaction_category_id' => $_SESSION['txn_cat_id'],
            'additionalnotes' => $additionalNote,
            'additionalfile' => $additionalFile
        ];

        // Create Angkas booking
        $angkasBookings = new AngkasBookings();
        if (!$angkasBookings->insertBooking($angkasData)) {
            $response['AngkasBookings'] = false;
            throw new Exception("Failed to create Angkas booking.");
        } else {
            $response['AngkasBookings'] = true;
        }

        // Respond with success
        $response = [
            'order_items' => $orderItems,
            'success' => true,
            'message' => 'Order and Angkas booking placed successfully!',
            'OrderRefNum' => $orderRefNum,
            'totalAmountToPay' => $totalAmount + $estCost,
            'AngkasBookingInfo' => $angkasData,
            'statusCode' => 200
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        // Error response
        error_log($e->getMessage()); // Log the error for debugging
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        $response['statusCode'] = 500;
        echo json_encode($response);
    }

} else {
    // Invalid request method
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
    $response['statusCode'] = 405;
    echo json_encode($response);
}
?>

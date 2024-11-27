<?php
// Include necessary files and initialize the database connection
include_once "../_db.php"; // Adjust the path as necessary
include_once "../_sql_utility.php";
include_once "./_shop/_class_grocery.php";

// Set the content type to JSON
header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

try {
    // Get the JSON data from the AJAX request
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data['refNum']) || empty($data['paymentStatus'])) {
        throw new Exception("Missing required parameters: refNum or paymentStatus.");
    }

    // Extract parameters from the request
    $referenceNum = $data['refNum'];
    $paymentStatus = $data['paymentStatus'];

    // Validate the payment status
    $validStatuses = ['P', 'F', 'C']; // Paid, Failed, Completed
    if (!in_array($paymentStatus, $validStatuses)) {
        throw new Exception("Invalid payment status provided.");
    }

    // Initialize the ShopOrders object
    $userId = USER_LOGGED; // Use the session user ID or replace with appropriate logic
    $shopOrders = new ShopOrders($userId, []);

    // Update the payment status column
    $shopOrders->UpdateOrderColumn('payment_status', $paymentStatus, $referenceNum, $userId);
    $shopOrders->UpdateOrderColumn('order_state_ind', 'P', $referenceNum, $userId);

    // Return a success response
    $response["success"] = true;
    $response["message"] = "Payment status updated successfully.";

} catch (Exception $e) {
    // Handle exceptions and return error messages
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

// Output the response as JSON
echo json_encode($response);
?>

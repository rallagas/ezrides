<?php
header('Content-Type: application/json');

include_once "../_db.php";
include_once "./_shop/_class_grocery.php";

try {
    // Get and validate input
    $shopOrderRefNum = $_POST['shop_order_ref_num'] ?? null;

    if (empty($shopOrderRefNum)) {
        throw new Exception("Shop Order Reference Number is required.");
    }

    $grocery = new ShopOrders(null,array(),$shopOrderRefNum); // Assuming the class instance name is Grocery

    // Check in shop_orders table
    $shopOrdersExists = $grocery->ValidateOrderRefNum($shopOrderRefNum);

    // Check in angkas_bookings table
    $query = "SELECT 1 FROM angkas_bookings WHERE shop_order_reference_number = ? OR angkas_booking_reference = ? LIMIT 1";
    $stmt = CONN->prepare($query);

    if (!$stmt) {
        throw new Exception("Failed to prepare query: " . CONN->error);
    }

    $stmt->bind_param("ss", $shopOrderRefNum,$shopOrderRefNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $angkasBookingsExists = $result->num_rows > 0;

    $stmt->close();

    // Determine the table(s) where the reference exists
    $tables = [];
    if ($shopOrdersExists) {
        $tables[] = "shop_orders";
    }
    if ($angkasBookingsExists) {
        $tables[] = "angkas_bookings";
    }

    // Build response
    $response = [
        "success" => true,
        "exist" => !empty($tables), // true if exists in any table
        "table" => $tables ? implode(", ", $tables) : null,
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
    ]);
}

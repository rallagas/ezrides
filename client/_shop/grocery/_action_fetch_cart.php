<?php
include_once "../../../_db.php"; // Include your database connection

$userId = $_SESSION['user_id'];

$response = ["cartItems" => []];

$query = "SELECT ci.item_id, ci.quantity, si.price, si.item_name, si.item_img
          FROM shop_orders ci
          JOIN shop_items si ON ci.item_id = si.item_id
          WHERE ci.user_id = ?"; // Adjust as per your logic
$stmt = CONN->prepare($query);
$stmt->bind_param("i", $userId); // Set user ID

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response["cartItems"][] = [
            "item_id" => $row["item_id"],
            "item_name" => $row["item_name"],
            "price" => $row["price"],
            "quantity" => $row["quantity"],
            "item_img" => $row["item_img"]
        ];
    }
}

echo json_encode($response);
?>
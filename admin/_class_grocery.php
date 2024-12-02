<?php
class Merchant {
    private $id;
    private $name;
    private $merchant_loc_coor;
    private $contactInfo;
    private $address;
    private $merchantImg; // Renamed from $img to $merchantImg
    private $merchantType; // Renamed from $type to $merchantType

    public function __construct( $id, $name, $merchant_loc_coor, $merchantImg = null, $contactInfo = null, $address = null, $merchantType = null ) {
        $this->id = $id;
        $this->name = $name;
        $this->merchant_loc_coor = $merchant_loc_coor;
        $this->contactInfo = $contactInfo;
        $this->address = $address;
        $this->merchantImg = $merchantImg;  // Set to $merchantImg
        $this->merchantType = $merchantType; // Set to $merchantType
    }

    // Getter methods
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getContactInfo() {
        return $this->contactInfo;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getMerchantLocCoor() {
        return $this->merchant_loc_coor;
    }

    public function getMerchantImg() {  // Renamed to match the property
        return $this->merchantImg;
    }

    public function getMerchantType() { // Renamed to match the property
        return $this->merchantType;
    }


    public static function fetchMerchantIdByName($merchantName) {
        $query = "SELECT merchant_id FROM shop_merchants WHERE name = ?";
        $stmt = CONN->prepare($query);
        $stmt->bind_param("s", $merchantName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['merchant_id']; // Return the merchant_id
        }
        return null; // Return null if no merchant found
    }

    public static function getAllMerchantNames() {

        $query = "SELECT name FROM shop_merchants";
        $stmt = CONN->prepare( $query );
        $stmt->execute();
        $result = $stmt->get_result();

        $names = [];
        while ( $row = $result->fetch_assoc() ) {
            $names[] = $row['name'];
        }
        return $names;
    }
    public static function getMerchantNamesById($merchantId) {

        $query = "SELECT name FROM shop_merchants WHERE merchant_id = ?";
        $stmt = CONN->prepare( $query );
        $stmt->bind_param('i', $merchantId);
        $stmt->execute();
        
        $result = $stmt->get_result();

        $names = [];
        while ( $row = $result->fetch_assoc() ) {
            $names[] = $row['name'];
        }
        return $names;
    }

    public static function getAllMerchants() {

        $query = "SELECT DISTINCT * FROM shop_merchants";
        $stmt = CONN->prepare( $query );
        $stmt->execute();
        $result = $stmt->get_result();

        $merchants = [];
        while ( $row = $result->fetch_assoc() ) {
            $merchants[] = new Merchant(
                $row['merchant_id'],
                $row['name'],
                $row['merchant_loc_coor'],
                $row['merchant_img'] ?? null,
                $row['contact_info'] ?? null,
                $row['address'] ?? null,
                $row['merchant_type'] ?? null
            );
        }
        return $merchants;
    }

public static function fetchMerchantInfoByItem($itemIds = []) 
    {
    // Check if itemIds is an array and not empty
    if (empty($itemIds)) {
        return null; // No items to query
    }

    // Join item IDs into a string for the SQL query
    $placeholders = implode(',', array_fill(0, count($itemIds), '?'));

    // Prepare the SQL query
    $query = "SELECT DISTINCT sm.*
                FROM shop_items si
                JOIN shop_merchants sm 
                  ON si.merchant_id = sm.merchant_id
                WHERE si.item_id IN ($placeholders) ";

    // Prepare the statement
    $stmt = CONN->prepare($query);
    if ($stmt === false) {
        return null; // SQL error, return null
    }

    // Bind the parameters
    $types = str_repeat('i', count($itemIds)); // 'i' for integers
    $stmt->bind_param($types, ...$itemIds); // Dynamically bind parameters

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any result exists and return the merchant info
    $merchants = [];
    while ($row = $result->fetch_assoc()) {
        // Store merchants indexed by their merchant_id
        $merchants[$row['merchant_id']] = new Merchant(
            $row['merchant_id'],
            $row['name'],
            $row['merchant_loc_coor'],
            $row['merchant_img'] ?? null,
            $row['contact_info'] ?? null,
            $row['address'] ?? null,
            $row['merchant_type'] ?? null
        );
    }

    // If merchants are found, return them, otherwise return null
    return !empty($merchants) ? $merchants : null;
}


    public static function fetchCommonMerchantById( $orderRefNum, $itemIds = [] ) {
        // Join item IDs into a string for the SQL query
        $placeholders = implode( ',', array_fill( 0, count( $itemIds ), '?' ) );

        $query = "SELECT DISTINCT sm.* 
                    FROM shop_orders so
                   JOIN shop_items si
                     ON si.item_id = so.item_id
                   JOIN shop_merchants sm 
                     ON si.merchant_id = sm.merchant_id
                   WHERE si.item_id IN ($placeholders)
                     AND so.shop_order_ref_num = ? ";

        // Dynamically bind parameters
        $stmt = CONN->prepare( $query );

        $bindParams = array_merge( $itemIds, [$orderRefNum] );
        $types = str_repeat( 'i', count( $itemIds ) ) . 's';
        $stmt->bind_param( $types, ...$bindParams );

        $stmt->execute();
        $result = $stmt->get_result();

        if ( $row = $result->fetch_assoc() ) {
            return new Merchant(
                $row['merchant_id'],
                $row['name'],
                $row['merchant_loc_coor'],
                $row['merchant_img'] ?? null,
                $row['contact_info'] ?? null,
                $row['address'] ?? null,
                $row['merchant_type'] ?? null
            );
        }
        return null;
    }

}

class Product {
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $merchantId;
    private $merchantName;
    private $itemImg;

    public function __construct( $id, $name, $price, $quantity, $merchantId, $merchantName, $itemImg = null ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->merchantId = $merchantId;
        $this->merchantName = $merchantName;
        $this->itemImg = $itemImg;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getMerchantId() {
        return $this->merchantId;
    }

    public function getMerchantName() {
        return $this->merchantName;
    }

    public function getItemImg() {
        return $this->itemImg;
    }

    public function updateItem($name, $price, $merchantId, $itemImg = null) {
        $query = "UPDATE shop_items 
                  SET item_name = ?, price = ?, merchant_id = ?, item_img = ? 
                  WHERE item_id = ?";
        $stmt = CONN->prepare($query);
        
        // Handle null item image
        $stmt->bind_param(
            "sdisi", 
            $name, 
            $price, 
            $merchantId, 
            $itemImg, 
            $this->id
        );
        
        return $stmt->execute(); // Return whether the update was successful
    }

    public static function fetchItemIdByName($itemName) {
        $query = "SELECT item_id FROM shop_items WHERE item_name = ?";
        $stmt = CONN->prepare($query);
        $stmt->bind_param("s", $itemName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['item_id']; // Return the item_id
        }
        return null; // Return null if no item found
    }

    public static function fetchAllProducts() {

        $query = "SELECT si.item_id, si.item_name, si.price, si.quantity, si.merchant_id, sm.name AS merchant_name, si.item_img
            FROM shop_items si
            JOIN shop_merchants sm ON si.merchant_id = sm.merchant_id
        ";
        $stmt = CONN->prepare( $query );
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ( $row = $result->fetch_assoc() ) {
            $products[] = new Product(
                $row['item_id'],
                $row['item_name'],
                $row['price'],
                $row['quantity'],
                $row['merchant_id'],
                $row['merchant_name'],
                $row['item_img']
            );
        }
        return $products;
    }

    public static function fetchById( $itemId ) {

        $query = "
            SELECT si.item_id, si.item_name, si.price, si.quantity, si.merchant_id, sm.name AS merchant_name, si.item_img, sm.merchant_loc_coor
            FROM shop_items si
            JOIN shop_merchants sm ON si.merchant_id = sm.merchant_id
            WHERE si.item_id = ?
        ";
        $stmt = CONN->prepare( $query );
        $stmt->bind_param( "i", $itemId );
        $stmt->execute();
        $result = $stmt->get_result();

        if ( $row = $result->fetch_assoc() ) {
            return new Product(
                $row['item_id'],
                $row['item_name'],
                $row['price'],
                $row['quantity'],
                $row['merchant_id'],
                $row['merchant_name'],
                $row['item_img']
            );
        }
        return null;
    }
    public static function fetchByMerchantId($merchantId) {
        $query = "SELECT si.item_id, si.item_name, si.price, si.quantity, si.merchant_id, sm.name AS merchant_name, si.item_img, sm.merchant_loc_coor
            FROM shop_items si
            JOIN shop_merchants sm ON si.merchant_id = sm.merchant_id
            WHERE si.merchant_id = ?";
        $stmt = CONN->prepare($query);
        $stmt->bind_param("i", $merchantId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $products = []; // Initialize an array to store products
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['item_id'],
                $row['item_name'],
                $row['price'],
                $row['quantity'],
                $row['merchant_id'],
                $row['merchant_name'],
                $row['item_img']
            );
        }
    
        return $products; // Return the array of products
    }
    

}

class Cart {
    private $items = [];
    private $userId;

public function __construct( $userId, $items = []) {
        $this->userId = $userId;

        // Ensure $items is an array and assign to the private property
        if ( is_array( $items ) ) {
            $this->items = $items;
        } else {
            throw new Exception( "Items must be an array." );
        }
        }

public function getItems() {
        return $this->items;
        }
public function addToCart( Product $product, $quantity ) {
        // Check if the item is already in the cart
        if ( isset( $this->items[$product->getId()] ) ) {
            $this->items[$product->getId()]['quantity'];
            // Increase quantity if already in cart
        } else {
            // Add the product to the cart if not already added
            $this->items[$product->getId()] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        // Add or update item in shop_orders table
        $this->updateCartInDatabase( $product, $quantity );
        }
public function getTotal() {
        $total = 0;
        foreach ( $this->items as $item ) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }
public function getCartDetails() {
    $query = "  SELECT ci.order_id
             , ci.item_id
             , ci.quantity
             , p.item_name AS item_name
             , p.price
             , p.item_img AS item_img
        FROM shop_orders ci
        JOIN shop_items p ON ci.item_id = p.item_id
        WHERE ci.user_id = ? AND ci.order_state_ind = 'C' ";

    $stmt = CONN->prepare($query);
    $stmt->bind_param("s", $this->userId);

    $cartItems = [];
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = [
                "order_id" => $row["order_id"],
                "item_id" => $row["item_id"],
                "item_name" => $row["item_name"],
                "price" => $row["price"],
                "quantity" => $row["quantity"],
                "item_img" => $row["item_img"]
            ];
        }
    }

    return $cartItems;
    }
public function placeOrder($orderRefNum, $shippingDetails = [], $Order_ids = []) {
    // Validate inputs
    if (empty($orderRefNum)) {
        throw new Exception("Order reference number is required.");
    }
    if (empty($shippingDetails['name']) || empty($shippingDetails['address']) || 
        empty($shippingDetails['phone']) || empty($shippingDetails['coordinates'])) {
        throw new Exception("Shipping details are incomplete.");
    }
    if (empty($Order_ids)) {
        throw new Exception("No Order IDs found.");
    }

    // Sanitize and format order IDs
    $orderIdsPlaceholder = implode(',', array_fill(0, count($Order_ids), '?'));

    $shippingName = $shippingDetails['name'];
    $shippingAddress = $shippingDetails['address'];
    $shippingPhone = $shippingDetails['phone'];
    $addressCoordinates = $shippingDetails['coordinates'];

    // Prepare the query
    $query = "UPDATE shop_orders 
        SET order_state_ind = 'O',
            shop_order_ref_num = ?,
            shipping_name = ?,
            shipping_address = ?,
            shipping_phone = ?,
            shipping_address_coor = ?
        WHERE order_id IN ($orderIdsPlaceholder) ";

    $stmt = CONN->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare query: " . CONN->error);
    }

    // Bind parameters dynamically
    $types = str_repeat('s', 5) . str_repeat('i', count($Order_ids)); // 5 strings + n integers
    $params = array_merge([$orderRefNum, $shippingName, $shippingAddress, $shippingPhone, $addressCoordinates], $Order_ids);

    $stmt->bind_param($types, ...$params);

    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception("Failed to update orders: " . $stmt->error);
    }

    return true;
    }
public function deleteItems(array $orderIds) {
    if (empty($orderIds)) {
        return false; // No order IDs provided, return false
    }

    // Sanitize and format order IDs
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

    // SQL to delete items where order_state_ind = 'C' for the given order IDs
    $query = "  DELETE FROM shop_orders
        WHERE order_state_ind = 'C' 
        AND user_id = ? 
        AND order_id IN ($placeholders) ";

    // Prepare the statement
    $stmt = CONN->prepare($query);
    if (!$stmt) {
        return false; // Failed to prepare query, return false
    }

    // Bind parameters dynamically
    $types = str_repeat('i', count($orderIds) + 1); // 1 integer for user_id + n integers for order_ids
    $params = array_merge([$this->userId], $orderIds);

    $stmt->bind_param($types, ...$params);

    // Execute the query
    if (!$stmt->execute()) {
        return false; // Failed to execute query, return false
    }

    // Return true if at least one row was deleted
    return $stmt->affected_rows > 0;
    }

private function updateCartInDatabase( Product $product, $quantity ) {
        // Define data for insert or update in cart
        $amountToPay = $product->getPrice() * $quantity;
        $data = [
            'user_id' => $this->userId,
            'item_id' => $product->getId(),
            'quantity' => $quantity,
            'amount_to_pay' => $amountToPay
        ];
        
       


        // Query to insert or update the cart in the shop_orders table
        $query = "
            INSERT INTO shop_orders (user_id, item_id, quantity, amount_to_pay) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                quantity = quantity + VALUES(quantity),
                amount_to_pay = amount_to_pay + VALUES(amount_to_pay)
        ";

        $stmt = CONN->prepare( $query );
        $stmt->bind_param( "siid", $data['user_id'], $data['item_id'], $data['quantity'], $data['amount_to_pay'] );
        $stmt->execute();

    }

}

class ShopOrders extends Cart {
    private $orderRefNum;

    public function __construct($userId, $items, $orderRefNum = null) {
        // Call parent constructor to initialize user ID and items
        parent::__construct($userId, $items);

        // Set the order reference number
        $this->orderRefNum = $orderRefNum;
    }

    // Method to fetch the amount to pay for a specific order
    public function GetAmountToPay($orderRefNum) {
        $status = ['total_amount' => 0.00, 'message' => null, 'bookingStatus' => false];
        // Validate the order reference number
        if (empty($orderRefNum)) {
            throw new Exception("Order reference number is required.");
        }

        // Query to retrieve the total amount to pay for the given order reference number
        $query = "
            SELECT SUM(ab.shop_cost + ab.form_Est_cost)  AS total_amount 
            FROM shop_orders so
            JOIN angkas_booking ab
             on  so.shop_order_ref_num = ab.shop_order_reference_number
            WHERE so.shop_order_ref_num = ?
             AND ab.payment_status <> 'P'
        ";

        $stmt = CONN->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . CONN->error);
        }

        $stmt->bind_param("s", $orderRefNum);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $status = ['total_amount' => 0.00, 'message' => "No Booking Found"];
        }

        $row = $result->fetch_assoc();


        return $row['total_amount'];
    }

    // Method to update an order's status to "completed" after payment
    public function CompleteOrder($orderRefNum) {
        if (empty($orderRefNum)) {
            throw new Exception("Order reference number is required.");
        }

        // Query to mark the order as completed
        $query = "
            UPDATE shop_orders 
            SET order_state_ind = 'C' 
            WHERE shop_order_ref_num = ?
        ";

        $stmt = CONN->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . CONN->error);
        }

        $stmt->bind_param("s", $orderRefNum);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No orders updated. Please check the order reference number.");
        }

        return true;
    }

    // Method to fetch all items associated with a specific order
    public function GetOrderItems($orderRefNum) {
        if (empty($orderRefNum)) {
            throw new Exception("Order reference number is required.");
        }

        // Query to retrieve all items for a specific order reference number
        $query = "
            SELECT item_id, quantity, amount_to_pay 
            FROM shop_orders 
            WHERE shop_order_ref_num = ?
        ";

        $stmt = CONN->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . CONN->error);
        }

        $stmt->bind_param("s", $orderRefNum);
        $stmt->execute();

        $result = $stmt->get_result();
        $items = [];

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    }

    public function UpdateOrderColumn($column, $new_value, $shop_order_ref_num, $user_id = null) {

        $userId = $user_id ?? USER_LOGGED;

        // Validate inputs
        if (empty($column)) {
            throw new Exception("Column name is required.");
        }
        if (empty($shop_order_ref_num)) {
            throw new Exception("Order reference number is required.");
        }
    
        // Build the query dynamically
        $query = "UPDATE shop_orders SET `$column` = ? WHERE shop_order_ref_num = ?";
        $params = [$new_value, $shop_order_ref_num];
        $types = "ss";
    
        // Add user_id conditionally if provided
        if (!empty($user_id)) {
            $query .= " AND user_id = ?";
            $params[] = $userId;
            $types .= "i"; // Add integer type for user_id
        }
    
        // Prepare the statement
        $stmt = CONN->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . CONN->error);
        }
    
        // Dynamically bind parameters
        $stmt->bind_param($types, ...$params);
    
        // Execute the query
        $stmt->execute();
    
        // Check if rows were affected
        if ($stmt->affected_rows === 0) {
            throw new Exception("No rows updated. Check the reference number or conditions.");
        }
    
        return true;
    }

    public function ValidateOrderRefNum($orderRefNum) {
        // Validate the input
        if (empty($orderRefNum)) {
            throw new Exception("Order reference number is required.");
        }
    
        // Query to check if the order reference number exists
        $query = "SELECT 1 FROM shop_orders WHERE shop_order_ref_num = ? LIMIT 1";
    
        // Prepare the statement
        $stmt = CONN->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . CONN->error);
        }
    
        // Bind the parameter
        $stmt->bind_param("s", $orderRefNum);
    
        // Execute the query
        $stmt->execute();
    
        // Get the result
        $result = $stmt->get_result();
    
        // Return true if a record exists, false otherwise
        return $result->num_rows > 0;
    }
    
    
}
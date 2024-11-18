<?php

class Merchant {
    private $id;
    private $name;
    private $merchant_loc_coor;
    private $contactInfo;
    private $address;

    public function __construct( $id, $name, $merchant_loc_coor, $contactInfo = null, $address = null ) {
        $this->id = $id;
        $this->name = $name;
        $this->merchant_loc_coor = $merchant_loc_coor;
        $this->contactInfo = $contactInfo;
        $this->address = $address;
    }

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

    public static function getAllMerchants() {

        $query = "SELECT * FROM shop_merchants";
        $stmt = CONN->prepare( $query );
        $stmt->execute();
        $result = $stmt->get_result();

        $merchants = [];
        while ( $row = $result->fetch_assoc() ) {
            $merchants[] = new Merchant(
                $row['merchant_id'],
                $row['name'],
                $row['contact_info'] ?? null,
                $row['address'] ?? null
            );
        }
        return $merchants;
    }

    public static function fetchMerchantInfoByItem( $itemIds = [] ) {
        // Join item IDs into a string for the SQL query
        $placeholders = implode( ',', array_fill( 0, count( $itemIds ), '?' ) );

        $query = "
        SELECT DISTINCT sm.*
        FROM shop_items si
        JOIN shop_merchants sm 
          ON si.merchant_id = sm.merchant_id
        WHERE si.item_id IN ($placeholders)
        ";

        // Dynamically bind parameters
        $stmt = CONN->prepare( $query );

        $types = str_repeat( 'i', count( $itemIds ) );
        $stmt->bind_param( $types, ...$itemIds );

        $stmt->execute();
        $result = $stmt->get_result();

        if ( $row = $result->fetch_assoc() ) {
            return new Merchant(
                $row['merchant_id'],
                $row['name'],
                $row['merchant_loc_coor'],
                $row['phone'] ?? null,
                $row['address'] ?? null
            );
        }
        return null;
    }
    public static function fetchCommonMerchantById( $orderRefNum, $itemIds = [] ) {
        // Join item IDs into a string for the SQL query
        $placeholders = implode( ',', array_fill( 0, count( $itemIds ), '?' ) );

        $query = "
        SELECT DISTINCT sm.*
        FROM shop_orders so
        JOIN shop_items si
          ON si.item_id = so.item_id
        JOIN shop_merchants sm 
          ON si.merchant_id = sm.merchant_id
        WHERE si.item_id IN ($placeholders)
          AND so.shop_order_ref_num = ?
    ";

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
                $row['phone'] ?? null,
                $row['address'] ?? null
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

    public static function fetchAllProducts() {

        $query = "
            SELECT si.item_id, si.item_name, si.price, si.quantity, si.merchant_id, sm.name AS merchant_name, si.item_img
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

}

class Cart {
    private $items = [];
    private $userId;

    public function __construct( $userId, $items ) {
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

    // Add or update the product in the cart

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

    // Method to calculate total amount of items in the cart

    public function getTotal() {
        $total = 0;
        foreach ( $this->items as $item ) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    // Display cart items

    public function displayCart() {
        foreach ( $this->items as $item ) {
            echo $item['product']->getName() . " - Quantity: " . $item['quantity'] . " - Price: $" . $item['product']->getPrice() . "\n";
        }
        echo "Total: $" . $this->getTotal() . "\n";
    }

    // Place the order

    public function placeOrder( $orderRefNum, $shippingDetails = [] ) {
        // Validate inputs
        if ( empty( $orderRefNum ) ) {
            throw new Exception( "Order reference number is required." );
        }
        if ( empty( $shippingDetails['name'] ) || empty( $shippingDetails['address'] ) || empty( $shippingDetails['phone'] ) || empty( $shippingDetails['coordinates'] ) ) {
            throw new Exception( "Shipping details are incomplete." );
        }

        $shippingName = $shippingDetails['name'];
        $shippingAddress = $shippingDetails['address'];
        $shippingPhone = $shippingDetails['phone'];
        $addressCoordinates = $shippingDetails['coordinates'];

        // Process each selected item from $this->items
        foreach ( $this->items as $item ) {
            $itemId = $item['itemId'];
            $quantity = $item['quantity'];

            // Update order status and reference number in shop_orders
            $query = "
                UPDATE shop_orders 
                SET order_state_ind = 'O',
                    shop_order_ref_num = ?,
                    shipping_name = ?,
                    shipping_address = ?,
                    shipping_phone = ?,
                    shipping_address_coor = ?
                WHERE user_id = ? 
                  AND item_id = ? 
                  AND order_state_ind = 'C'
            ";

            $stmt = CONN->prepare( $query );
            if ( !$stmt ) {
                throw new Exception( "Failed to prepare query: " . CONN->error );
                return false;
            }

            $stmt->bind_param(
                "ssssssi",
                $orderRefNum,
                $shippingName,
                $shippingAddress,
                $shippingPhone,
                $addressCoordinates,
                $this->userId,
                $itemId
            );

            if ( !$stmt->execute() ) {
                throw new Exception( "Failed to update item $itemId: " . $stmt->error );
                return false;
            };
        }
        
     return true;
    }

    // Update the cart in the database with the new or updated item

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

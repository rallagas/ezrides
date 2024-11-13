<?php
include_once "../../_db.php";
// Database connection settings
//class Database {
//     private $pdo;
//
//    public function dbConnection() {
//        
//            $host = 'localhost';
//            $dbname = 'ezride';
//            $password = '';
//            $username='root';
//            
//         try {
//            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
//            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        } catch (PDOException $e) {
//            die("Connection failed: " . $e->getMessage()); // Use die to stop execution on failure
//        }
//    }
//
//    public function getConnection() {
//        return $this->pdo;
//    }
//
//    // Example method to perform a query
//    public function select($query) {
//        if ($this->pdo === null) {
//            die("Database connection not established."); // Check for connection
//        }
//        
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute();
//        return $stmt->fetchAll(PDO::FETCH_ASSOC);
//    }
//}

class Merchant {
    private $id;
    private $name;
    private $contactInfo;
    private $address;

    public function __construct($id, $name, $contactInfo = null, $address = null) {
        $this->id = $id;
        $this->name = $name;
        $this->contactInfo = $contactInfo;
        $this->address = $address;
    }

    // Getters for Merchant properties
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

    // Static method to fetch all merchants' names
    public static function getAllMerchantNames(Database $database) {
        $query = "SELECT name FROM shop_merchants";
        $stmt = $database->getConnection()->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Static method to fetch all merchants' details
    public static function getAllMerchants(Database $database) {
        $query = "SELECT * FROM shop_merchants";
        $stmt = $database->getConnection()->prepare($query);
        $stmt->execute();
        
        $merchants = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $merchants[] = new Merchant(
                $row['merchant_id'],
                $row['name'],
                $row['contact_info'] ?? null,
                $row['address'] ?? null
            );
        }

        return $merchants;
    }
}


// Class to represent a Product
class Product {
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $merchantId;
    private $merchantName; // New property for merchant name

    public function __construct($id, $name, $price, $quantity, $merchantId, $merchantName) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->merchantId = $merchantId;
        $this->merchantName = $merchantName; // Initialize merchant name
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
        return $this->quantity; // Getter for quantity
    }

    public function getMerchantId() {
        return $this->merchantId; // Getter for merchant ID
    }

    public function getMerchantName() {
        return $this->merchantName; // Getter for merchant name
    }

    // Static method to fetch products along with merchant information from the database
    public static function fetchAllProducts(Database $database) {
        $query = "
            SELECT gi.item_id, gi.item_name, gi.price, gi.quantity, gi.merchant_id, gm.name AS merchant_name
            FROM shop_items gi
            JOIN shop_merchants gm 
              ON gi.merchant_id = gm.merchant_id
        ";
        $results = $database->select($query);
        
        $products = [];
        foreach ($results as $row) {
            $products[] = new Product(
                $row['item_id'], 
                $row['item_name'], 
                $row['price'], 
                $row['quantity'], 
                $row['merchant_id'], 
                $row['merchant_name'] // Include merchant name
            );
        }
        return $products;
    }
}

// Class to represent a Cart
class Cart {
    private $items = [];
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function addProduct(Product $product, $quantity) {
        if (isset($this->items[$product->getId()])) {
            $this->items[$product->getId()]['quantity'] += $quantity;
        } else {
            $this->items[$product->getId()] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function displayCart() {
        foreach ($this->items as $item) {
            echo $item['product']->getName() . " - Quantity: " . $item['quantity'] . " - Price: $" . $item['product']->getPrice() . "\n";
        }
        echo "Total: $" . $this->getTotal() . "\n";
    }

    // Method to place an order
    public function placeOrder() {
        $query = "INSERT INTO orders (item_id, quantity) VALUES (:item_id, :quantity)";
        $stmt = $this->database->conn->prepare($query);

        foreach ($this->items as $item) {
            $stmt->bindParam(':item_id', $item['product']->getId());
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->execute();
        }

        echo "Order placed successfully!\n";
    }
}
<?php
include_once "../../../_db.php";
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

    public static function getAllMerchantNames() {
        $data = select_data('shop_merchants', null, null, null);
        return array_column($data, 'name');
    }

    public static function getAllMerchants() {
        $results = select_data('shop_merchants');
        $merchants = [];

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

class Product {
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $merchantId;
    private $merchantName;
    private $itemImg;

    public function __construct($id, $name, $price, $quantity, $merchantId, $merchantName, $itemImg = null) {
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
            SELECT gi.item_id, gi.item_name, gi.price, gi.quantity, gi.merchant_id, gm.name AS merchant_name, gi.item_img
            FROM shop_items gi
            JOIN shop_merchants gm 
              ON gi.merchant_id = gm.merchant_id
        ";
        $results = query($query);
        
        $products = [];
        foreach ($results as $row) {
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

    public static function fetchById($itemId) {
        $query = "
            SELECT si.item_id, si.item_name, si.price, si.quantity, si.merchant_id, sm.name AS merchant_name, si.item_img
            FROM shop_items si
            JOIN shop_merchants sm ON si.merchant_id = sm.merchant_id
            WHERE si.item_id = ?
        ";
        $results = query($query, [$itemId]);
        
        if ($results) {
            $result = $results[0];
            return new Product(
                $result['item_id'], 
                $result['item_name'], 
                $result['price'], 
                $result['quantity'], 
                $result['merchant_id'], 
                $result['merchant_name'], 
                $result['item_img']
            );
        }
        return null;
    }
}

class Cart {
    private $items = [];
    private $database;
    private $userId;

    public function __construct(Database $database, $userId) {
        $this->database = $database;
        $this->userId = $userId;
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

    public function placeOrder() {
        $orderRefNum = uniqid("ORD-");

        foreach ($this->items as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            $amountToPay = $product->getPrice() * $quantity;

            $data = [
                'shop_order_ref_num' => $orderRefNum,
                'user_id' => $this->userId,
                'item_id' => $product->getId(),
                'quantity' => $quantity,
                'amount_to_pay' => $amountToPay
            ];

            insert_data('shop_orders', $data);
        }

        echo "Order placed successfully! Reference Number: " . $orderRefNum . "\n";
    }
}



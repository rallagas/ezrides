<?php
// Include your database and Product class
include_once '../_class_grocery.php'; // Update the path as necessary
// Create a database instance
$db = new Database();
$db->dbConnection();
// Get the PDO connection
$pdo = $db->getConnection();

// Check if the search query is set
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];

    // Sanitize the input
    $searchQuery = htmlspecialchars($searchQuery);
    
    // Prepare the SQL statement to fetch matching products
    $query = "
        SELECT gi.item_id, gi.item_name, gi.price, gi.quantity, gi.merchant_id, gm.name AS merchant_name
        FROM grocery_items gi
        JOIN grocery_merchants gm ON gi.merchant_id = gm.merchant_id
        WHERE gi.item_name LIKE :search OR gm.name LIKE :search
    ";
    
    // Prepare and execute the statement
    $stmt = $pdo->prepare($query); // Use the PDO connection to prepare the statement
    $stmt->execute(['search' => '%' . $searchQuery . '%']);
    
    // Fetch results
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the HTML output
    $output = '';
    foreach ($products as $row) {
        $product = new Product(
            $row['item_id'], 
            $row['item_name'], 
            $row['price'], 
            $row['quantity'], 
            $row['merchant_id'], 
            $row['merchant_name']
        );

        $output .= '
            <div class="col-sm-4 mb-5 mb-sm-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">' . htmlspecialchars($product->getName()) . '</h5>
                        <p class="card-text">
                            <span class="badge rounded-pill text-bg-warning">' . htmlspecialchars($product->getMerchantName()) . '</span>
                            Price: $' . number_format($product->getPrice(), 2) . ' 
                            (' . $product->getQuantity() . ' in stock)
                        </p>
                          <div class="btn-group-sm">
                            <a href="#" class="btn btn-danger btn-sm">Deactivate</a>
                            <a href="#" class="btn btn-warning btn-sm">Update</a>
                        </div>
                    </div>
                </div>
            </div>';
    }

    // Return the HTML output
    echo $output;
}
?>
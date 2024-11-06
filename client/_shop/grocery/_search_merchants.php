<?php
require_once "../_class_grocery.php";

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $database = new Database();
    $database->dbConnection();
    
    // Get matching merchants
    $stmt = $database->getConnection()->prepare("SELECT name FROM grocery_merchants WHERE name LIKE :query LIMIT 10");
    $stmt->execute([':query' => '%' . $query . '%']);
    $merchants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($merchants);
}
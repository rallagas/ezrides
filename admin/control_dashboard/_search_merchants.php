<?php
include_once "../../_db.php"; // Ensure $mysqli or database connection `CONN` is initialized properly

header('Content-Type: application/json'); // Always set JSON response header

$response = ['message' => null, 'error' => null, 'merchants' => []];

try {
    // Validate and sanitize the query parameter
    if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
        $response['message'] = 'Query parameter is required.';
        echo json_encode($response);
        exit;
    }

    $query = trim($_GET['query']);

    // Prepare the SQL statement
    $stmt = CONN->prepare("SELECT name FROM shop_merchants WHERE name LIKE ? LIMIT 10");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . CONN->error);
    }

    // Bind the parameter
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param("s", $searchTerm);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results as an associative array
    if(empty($result)){
        $response['message'] = "Merchant Not found.";
    }   
    else{
        $response['merchants'] = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Close the statement
    $stmt->close();
} catch (Exception $e) {
    // Handle exceptions
    http_response_code(500); // Internal Server Error
    $response['error'] = $e->getMessage();
} 

// Return JSON response
echo json_encode($response);

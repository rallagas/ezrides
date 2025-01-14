<?php
include_once "_db.php";
include_once "_sql_utility.php";
header('Content-Type: application/json'); // Set the response content type to JSON

try {
    // Check if 'search' parameter is set
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        
        // Define the search query with a parameter placeholder
        $searchCondition = ['vehicle_model' => "%$search%"];
        
        // Execute the query using the utility function
        $results = select('angkas_vehicle_model', $searchCondition, ['vehicle_model' => 'ASC']);
        
        if(!empty($results)) {
            echo json_encode($results);
        } else {
            http_response_code(200); // 200 OK status
            echo json_encode([]); // Empty JSON array if no results
        }
     
    } else {
        // If 'search' parameter is missing
        http_response_code(400); // 400 Bad Request
        echo json_encode(['error' => 'Missing search parameter.']);
    }
} catch (Exception $e) {
    // Handle potential errors
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>

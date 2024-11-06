<?php
include_once "_db.php";


try {
    // Check if 'search' parameter is set
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        
        // Define SQL query with a parameter placeholder
        $sql = "SELECT vehicle_model FROM angkas_vehicle_model WHERE vehicle_model LIKE ? LIMIT 10";
        
        // Execute the query using the utility function
        $results = query(CONN, $sql, ['%' . $search . '%']);
        
        // Check if results are fetched successfully
        if ($results !== false) {
            // Return results as JSON
            echo json_encode($results);
        } else {
            // Handle query failure
            http_response_code(500);
            echo json_encode(['error' => 'Query failed.']);
        }
    }
} catch (Exception $e) {
    // Handle potential errors
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>

<?php
include_once "../../_db.php"; // Ensure $mysqli is initialized properly

header('Content-Type: application/json'); // Always set JSON response header

try {
    // Validate the query parameter
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = trim($_GET['query']);
        
        // Prepare the SQL statement
        $stmt = CONN->prepare("SELECT name FROM shop_merchants WHERE name LIKE ? LIMIT 10");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $mysqli->error);
        }

        // Bind the parameter
        $searchTerm = '%' . $query . '%';
        $stmt->bind_param("s", $searchTerm);

        // Execute the statement
        $stmt->execute();

        // Fetch results
        $result = $stmt->get_result();
        $merchants = $result->fetch_all(MYSQLI_ASSOC);

        // Return JSON response
        echo json_encode($merchants);

        // Close the statement
        $stmt->close();
    } else {
        // Return an empty array if query is invalid or too short
        echo json_encode([]);
    }
} catch (Exception $e) {
    // Return error message as JSON
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Ensure the database connection is closed
    if (isset($mysqli) && $mysqli->ping()) {
        $mysqli->close();
    }
}

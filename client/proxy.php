<?php
// Enable CORS for the response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Validate the API key and parameters
if (!isset($_GET['origins']) || !isset($_GET['destinations']) || !isset($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters.']);
    exit;
}

// Extract parameters from the request
$origins = urlencode($_GET['origins']);
$destinations = urlencode($_GET['destinations']);
$apiKey = urlencode($_GET['key']);

// Construct the Google Maps Distance Matrix API URL
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$origins&destinations=$destinations&key=$apiKey";

// Make the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching data from Google API.', 'details' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Close the cURL session
curl_close($ch);

// Forward the API response to the client
header('Content-Type: application/json');
echo $response;
?>

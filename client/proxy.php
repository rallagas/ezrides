<?php
// Enable CORS for the response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Define geographic bounds for Albay and Sorsogon
$albayBounds = [
    'lat_min' => 13.080, // Southernmost latitude
    'lat_max' => 13.544, // Northernmost latitude
    'lng_min' => 123.333, // Westernmost longitude
    'lng_max' => 124.184 // Easternmost longitude
];
$sorsogonBounds = [
    'lat_min' => 12.583, // Southernmost latitude
    'lat_max' => 13.120, // Northernmost latitude
    'lng_min' => 123.570, // Westernmost longitude
    'lng_max' => 124.200 // Easternmost longitude
];

// Validate the API key and parameters
if (!isset($_GET['origins']) || !isset($_GET['destinations']) || !isset($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters.']);
    exit;
}

// Function to check if coordinates are within bounds
function isWithinBounds($lat, $lng, $bounds) {
    return $lat >= $bounds['lat_min'] && $lat <= $bounds['lat_max'] &&
           $lng >= $bounds['lng_min'] && $lng <= $bounds['lng_max'];
}

// Extract and validate origins and destinations
function validateLocations($locations, $albayBounds, $sorsogonBounds) {
    foreach ($locations as $location) {
        // Split into latitude and longitude
        $coords = explode(',', $location);
        if (count($coords) !== 2) {
            return false; // Invalid format
        }

        $lat = (float) $coords[0];
        $lng = (float) $coords[1];

        // Check if the coordinates fall within either Albay or Sorsogon
        if (!isWithinBounds($lat, $lng, $albayBounds) && !isWithinBounds($lat, $lng, $sorsogonBounds)) {
            return false; // Out of bounds
        }
    }
    return true;
}

$origins = explode('|', urldecode($_GET['origins']));
$destinations = explode('|', urldecode($_GET['destinations']));

// Validate origins and destinations
if (!validateLocations($origins, $albayBounds, $sorsogonBounds) || 
    !validateLocations($destinations, $albayBounds, $sorsogonBounds)) {
    http_response_code(400);
    echo json_encode(['error' => 'Locations must be within Albay or Sorsogon.']);
    exit;
}

// Construct the Google Maps Distance Matrix API URL
$encodedOrigins = urlencode(implode('|', $origins));
$encodedDestinations = urlencode(implode('|', $destinations));
$apiKey = urlencode($_GET['key']);
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$encodedOrigins&destinations=$encodedDestinations&key=$apiKey";

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

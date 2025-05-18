<?php
// Enable error reporting for development purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the configuration class
class Config {
    
    const HOST = 'localhost';
    const DBNAME = 'ezride';
    const USERNAME = 'root';
    const PASSWORD = '';
    const GOOGLE_MAPS_API_KEY = 'AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs';

    public static function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        return $protocol . $domain;
    }
}

// Session management class
class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->isUserLoggedIn()) {
            if (!defined('USER_LOGGED')) {
                define('USER_LOGGED', $_SESSION['user_id']);
            }
        }
    }

    public function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}



// Function to get distance and ETA from Google Distance Matrix API
function getDistanceAndETA($fromLat, $fromLng, $toLat, $toLng, $APIKey = Config::GOOGLE_MAPS_API_KEY) {
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric"
        . "&origins={$fromLat},{$fromLng}"
        . "&destinations={$toLat},{$toLng}"
        . "&key={$APIKey}";

    try {
        $context = stream_context_create(['http' => ['timeout' => 10]]);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception("Failed to fetch data from Google Distance Matrix API.");
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from API.");
        }

        if ($data['status'] !== 'OK') {
            throw new Exception("API Error: " . ($data['error_message'] ?? 'Unknown error'));
        }

        $element = $data['rows'][0]['elements'][0] ?? null;

        if ($element['status'] !== 'OK') {
            throw new Exception("Unable to calculate distance and ETA: " . ($element['status'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'distance_km' => $element['distance']['value'] / 1000, // Convert meters to km
            'eta_minutes' => ceil($element['duration']['value'] / 60), // Convert seconds to minutes
        ];
    } catch (Exception $e) {
        error_log("Error in getDistanceAndETA: " . $e->getMessage());
        return [
            'success' => false,
            'distance_km' => 3, // Default fallback
            'eta_minutes' => 15, // Default fallback
            'error' => true,
            'message' => $e->getMessage(),
        ];
    }
}

// Database connection class
class Database {
    private $connection;

    public function __construct() {
        $this->connection = mysqli_connect(Config::HOST, Config::USERNAME, Config::PASSWORD, Config::DBNAME);

        if (!$this->connection) {
            die(json_encode([
                'success' => false,
                'message' => "Database connection failed: " . mysqli_connect_error(),
            ]));
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __destruct() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}

function isCONN($conn) {
    return $conn ? true : false;
}

// Initialize classes
$db = new Database();
define('BASE_PATH', __DIR__);
define('CONN', $db->getConnection());
define('SECRET_KEY', 'ezrides'); // Use a secure, random key
define('SECRET_IV', 'ezrides123456789');   // Use a secure, random IV
define('GCASH_ADMIN_ACCOUNT',"");
define('GCASH_ADMIN_NAME', NULL);
include_once "_sql_utility.php";


$baseClientUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/ezrides/client/";
$baseRiderUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/ezrides/rider_dashboard/";




$sessionManager = new SessionManager();

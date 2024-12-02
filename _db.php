<?php

// Enable error reporting for development purposes
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

// Define the configuration class

class Config {
    const HOST = 'localhost';
    const DBNAME = 'ezride';
    const USERNAME = 'root';
    const PASSWORD = '';
    const GOOGLE_MAPS_API_KEY = 'AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI';

    public static function getBaseUrl() {
        $protocol = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 )
        ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        return $protocol . $domain;
    }

    public static function getIndexPaths() {
        return [
            'main' => '/ezrides/index.php',
            'client' => '/ezrides/clients/index.php',
            'admin' => '/ezrides/admin/index.php',
            'rider' => '/ezrides/rider_dashboard/index.php'
        ];
    }
}

// Database connection class

class Database {
    private $connection;

    public function __construct() {
        $this->connection = mysqli_connect( Config::HOST, Config::USERNAME, Config::PASSWORD, Config::DBNAME );

        if ( !$this->connection ) {
            die( json_encode( [
                'success' => false,
                'message' => "Database connection failed: " . mysqli_connect_error(),
            ] ) );
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function __destruct() {
        if ( $this->connection ) {
            mysqli_close( $this->connection );
        }
    }
}

// Session management class

class SessionManager {
    public function __construct() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        if ( $this->isUserLoggedIn() ) {
            if ( !defined( 'USER_LOGGED' ) ) {
                define( 'USER_LOGGED', $_SESSION['user_id'] );
            }
        }
    }

    public function isUserLoggedIn() {
        return isset( $_SESSION['user_id'] );
    }
}

// Redirection class

class Redirect {
    private $sessionManager;
    private $baseUrl;
    private $indexPaths;

    public function __construct( SessionManager $sessionManager ) {
        $this->sessionManager = $sessionManager;
        $this->baseUrl = Config::getBaseUrl();
        $this->indexPaths = Config::getIndexPaths();
    }

    public function checkAndRedirect() {
        $currentPage = $_SERVER['REQUEST_URI'];
        $mainIndexUrl = $this->baseUrl . $this->indexPaths['main'];

        if ( $this->isSpecialPage( $currentPage ) ) {
            return;
            // Do not redirect special pages
        }

        if ( !$this->sessionManager->isUserLoggedIn() && $currentPage !== $this->indexPaths['main'] && !in_array( $currentPage, $this->getFullIndexUrls() ) ) {
            header( "Location: " . $mainIndexUrl );
            exit();
        }
    }

    private function isSpecialPage( $currentPage ) {
        return strpos( basename( $currentPage ), '_' ) === 0;
    }

    private function getFullIndexUrls() {
        return array_map( fn( $path ) => $this->baseUrl . $path, $this->indexPaths );
    }
}

// Function to get distance and ETA from Google Distance Matrix API

function getDistanceAndETA( $fromLat, $fromLng, $toLat, $toLng, $APIKey = Config::GOOGLE_MAPS_API_KEY ) {
    $apiKey = $APIKey;
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric"
    . "&origins={$fromLat},{$fromLng}"
    . "&destinations={$toLat},{$toLng}"
    . "&key={$apiKey}";

    try {
        $context = stream_context_create( ['http' => ['timeout' => 10]] );
        $response = @file_get_contents( $url, false, $context );

        if ( $response === false ) {
            throw new Exception( "Failed to fetch data from Google Distance Matrix API." );
        }

        $data = json_decode( $response, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            throw new Exception( "Invalid JSON response from API." );
        }

        if ( $data['status'] !== 'OK' ) {
            throw new Exception( "API Error: " . ( $data['error_message'] ?? 'Unknown error' ) );
        }

        $element = $data['rows'][0]['elements'][0] ?? null;

        if ( $element['status'] !== 'OK' ) {
            throw new Exception( "Unable to calculate distance and ETA: " . ( $element['status'] ?? 'Unknown error' ) );
        }

        return [
            'success' => true,
            'distance_km' => $element['distance']['value'] / 1000, // Convert meters to km
            'eta_minutes' => ceil( $element['duration']['value'] / 60 ), // Convert seconds to minutes
        ];
    } catch ( Exception $e ) {
        error_log( "Error in getDistanceAndETA: " . $e->getMessage() );
        return [
            'success' => false,
            'distance_km' => 3, // Default fallback
            'eta_minutes' => 15, // Default fallback
            'error' => true,
            'message' => $e->getMessage(),
        ];
    }
}

function isCONN( $conn ) {
    return $conn ? true : false;
}

// Initialize classes
$db = new Database();
define( 'CONN', $db->getConnection() );
define('SECRET_KEY', 'ezrides'); // Use a secure, random key
define('SECRET_IV', 'ezrides123456789');   // Use a secure, random IV
define('GCASH_ADMIN_ACCOUNT',"09985518206");
define('GCASH_ADMIN_NAME',NULL);

$sessionManager = new SessionManager();
$redirect = new Redirect( $sessionManager );
$redirect->checkAndRedirect();
include_once "_sql_utility.php";

?>

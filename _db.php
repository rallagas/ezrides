<?php
// Enable error reporting for development purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration class for constants and reusable methods
class Config {
    const HOST = 'localhost';
    const DBNAME = 'ezride';
    const USERNAME = 'root';
    const PASSWORD = '';
    const GOOGLE_MAPS_API_KEY = 'AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs';
    const SECRET_KEY = 'ezrides';
    const SECRET_IV = 'ezrides123456789';
    const GCASH_ADMIN_ACCOUNT = "09985518206";
    const GCASH_ADMIN_NAME = NULL;

    public static function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'];
    }
}

// Session manager to handle session initialization and login status
class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($this->isUserLoggedIn()) {
            define('USER_LOGGED', $_SESSION['user_id'] ?? null);
        }
    }

    public function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

// Redirect class with user type-based access control
class Redirect {
    private $sessionManager;
    private $baseUrl;

    public function __construct(SessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
        $this->baseUrl = Config::getBaseUrl();
    }

    public function checkAndRedirect() {
        if (!$this->sessionManager->isUserLoggedIn()) {
            $this->redirectToHome();
        }

        $userType = $_SESSION['t_user_type'] ?? null;
        $currentPage = $_SERVER['REQUEST_URI'];

        if ($userType === 'A' || $this->isAuthorizedPage($userType, $currentPage)) {
            return; // Allow access
        }

        $this->redirectToUnauthorized();
    }

    private function isAuthorizedPage($userType, $page) {
        return ($userType === 'C' && !$this->isRestrictedPage($page, ['/admin/', '/rider_dashboard/'])) ||
               ($userType === 'R' && strpos($page, '/rider_dashboard/') !== false);
    }

    private function isRestrictedPage($page, $restrictedPaths) {
        foreach ($restrictedPaths as $path) {
            if (strpos($page, $path) !== false) {
                return true;
            }
        }
        return false;
    }

    private function redirectToHome() {
        header("Location: " . $this->baseUrl . "/index.php");
        exit();
    }

    private function redirectToUnauthorized() {
        header("Location: " . $this->baseUrl . "/unauthorized.php");
        exit();
    }
}

// Google Distance Matrix API helper function
function getDistanceAndETA($fromLat, $fromLng, $toLat, $toLng) {
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric"
         . "&origins={$fromLat},{$fromLng}"
         . "&destinations={$toLat},{$toLng}"
         . "&key=" . Config::GOOGLE_MAPS_API_KEY;

    try {
        $response = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 10]]));
        if ($response === false) throw new Exception("Failed to fetch data from Google API.");
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || $data['status'] !== 'OK') {
            throw new Exception("API Error: " . ($data['error_message'] ?? 'Unknown error'));
        }

        $element = $data['rows'][0]['elements'][0] ?? null;
        if ($element['status'] !== 'OK') throw new Exception("Unable to calculate distance and ETA.");

        return [
            'success' => true,
            'distance_km' => $element['distance']['value'] / 1000,
            'eta_minutes' => ceil($element['duration']['value'] / 60)
        ];
    } catch (Exception $e) {
        error_log("Error in getDistanceAndETA: " . $e->getMessage());
        return ['success' => false, 'distance_km' => 3, 'eta_minutes' => 15, 'error' => true, 'message' => $e->getMessage()];
    }
}

// Database connection class
class Database {
    private $connection;

    public function __construct() {
        $this->connection = mysqli_connect(Config::HOST, Config::USERNAME, Config::PASSWORD, Config::DBNAME);
        if (!$this->connection) {
            die(json_encode(['success' => false, 'message' => "Database connection failed: " . mysqli_connect_error()]));
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

// Initialize key classes
$db = new Database();
define('CONN', $db->getConnection());
include_once "_sql_utility.php";

define('SECRET_KEY', Config::SECRET_KEY); // Use a secure, random key
define('SECRET_IV', Config::SECRET_IV);   // Use a secure, random IV
define('GCASH_ADMIN_ACCOUNT',Config::GCASH_ADMIN_ACCOUNT);
define('GCASH_ADMIN_NAME', Config::GCASH_ADMIN_NAME);

$sessionManager = new SessionManager();
$redirect = new Redirect($sessionManager);
$redirect->checkAndRedirect();
?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Config
{
    const HOST = 'localhost';
    const DBNAME = 'ezride';
    const USERNAME = 'root';
    const PASSWORD = '';
    const GOOGLE_MAPS_API_KEY = 'AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI';

    public static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        return $protocol . $domain;
    }

    public static function getIndexPaths()
    {
        return [
            'main' => '/ezrides/index.php',
            'client' => '/ezrides/clients/index.php',
            'admin' => '/ezrides/admin/index.php',
            'rider' => '/ezrides/rider_dashboard/index.php'
        ];
    }
}

class Database
{
    private $connection;

    public function __construct()
    {
        $this->connection = mysqli_connect(Config::HOST, Config::USERNAME, Config::PASSWORD, Config::DBNAME);

        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

class SessionManager
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in and set the global constant
        if ($this->isUserLoggedIn()) {
            // Define the constant USER_LOGGED globally, based on the user_id from the session
            if (!defined('USER_LOGGED')) {
                define('USER_LOGGED', $_SESSION['user_id']);
            }
        }
    }

    public function isUserLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}


class Redirect
{
    private $sessionManager;
    private $baseUrl;
    private $indexPaths;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
        $this->baseUrl = Config::getBaseUrl();
        $this->indexPaths = Config::getIndexPaths();
    }

    public function checkAndRedirect()
    {
        $current_page = $_SERVER['REQUEST_URI'];
        $main_index_url = $this->baseUrl . $this->indexPaths['main'];

        // Check if the current page is a special page (starts with an underscore)
        if ($this->isSpecialPage($current_page)) {
            return; // Don't redirect, just return
        }

        // Redirect only if the user is not logged in and the current page is not the main index URL
        if (!$this->sessionManager->isUserLoggedIn() && $current_page !== $this->indexPaths['main'] && !in_array($current_page, $this->getFullIndexUrls())) {
            header("Location: " . $main_index_url);
            exit();
        }
    }

    private function isSpecialPage($current_page)
    {
        // Check if the current page filename starts with an underscore
        return strpos(basename($current_page), '_') === 0;
    }

    private function getFullIndexUrls()
    {
        return [
            $this->baseUrl . $this->indexPaths['main'],
            $this->baseUrl . $this->indexPaths['client'],
            $this->baseUrl . $this->indexPaths['admin'],
            $this->baseUrl . $this->indexPaths['rider']
        ];
    }
}



// Initialize classes
$db = new Database();
define('CONN', $db->getConnection());

$sessionManager = new SessionManager();
$redirect = new Redirect($sessionManager);

// Perform redirection if needed
$redirect->checkAndRedirect();


include_once "_sql_utility.php";
?>

<?php
include_once "_db.php";
include_once "_functions.php";

header('Content-Type: application/json'); // Ensure JSON output for all responses

// User class to handle authentication and session management
class User {
    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    // Function to authenticate user
    public function login() {
        if (empty($this->username) || empty($this->password)) {
            return $this->response("error", "Please fill in all required fields.");
        }

        // Check if user exists using select_data utility function
        $user = select_data('users', "t_username = '{$this->username}'");

        if (count($user) > 0) {
            $user = $user[0]; // Get first matching user record
            if (password_verify($this->password, $user['t_password'])) {
                if ($user['t_status'] === 'A') {
                    $this->initializeSession($user);

                    // Determine redirection based on user type and rider status
                    $redirectUrl = $this->determineRedirect($user);

                    // Set user online status using utility function
                    $setOnline = setOnlineStatus($user['user_id'], 1);

                    return $this->response("success", "Login successful.", [
                        "redirect" => $redirectUrl,
                        "onlinestatus" => $setOnline
                    ]);
                } else {
                    return $this->response("error", "User account is inactive.");
                }
            } else {
                return $this->response("error", "Incorrect password.");
            }
        } else {
            return $this->response("error", "Username not found.");
        }
    }

    // Determine redirection based on user type and rider status
    private function determineRedirect($user) {
        if ($user['t_user_type'] === 'A') {
            return "admin/";
        } elseif ($user['t_user_type'] === 'C') {
            return $user['t_rider_status'] == 1
                ? "rider_dashboard/"
                : "client/index.php?page=home";
        } else {
            return "client/index.php?page=home"; // Default redirect for other types
        }
    }

    // Fetch user profile data
    private function getUserProfile($user_id) {
        $profile = select_data("user_profile", "user_id = $user_id");
        return $profile ? $profile[0] : null;
    }

    // Initialize session variables
    private function initializeSession($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['t_username'] = $user['t_username'];
        $_SESSION['t_user_type'] = $user['t_user_type'];
        $_SESSION['t_rider_status'] = $user['t_rider_status'];
        

        // Load profile data
        $profile = $this->getUserProfile($user['user_id']);
        if ($profile) {
            $_SESSION['user_profile'] = [
                'user_firstname' => $profile['user_firstname'],
                'user_lastname' => $profile['user_lastname'],
                'user_mi' => $profile['user_mi'],
                'user_contact_no' => $profile['user_contact_no'],
                'user_email_address' => $profile['user_email_address'],
                'user_profile_image' => $profile['user_profile_image'],
            ];
        }
    }

    // Format response
    private function response($status, $message, $additionalData = []) {
        $response = [
            "status" => $status,
            "message" => $message
        ];
        return json_encode(array_merge($response, $additionalData));
    }
}

// Handle the login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_username'])) {
    $username = $_POST['log_username'];
    $password = $_POST['log_password'];
    $user = new User($username, $password);
    echo $user->login();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}
?>

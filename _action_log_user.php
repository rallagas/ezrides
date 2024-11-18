<?php
include_once "_db.php";
include_once "_functions.php";
header( 'Content-Type: application/json' );
// Ensure JSON output for all responses

// User class to handle authentication and session management

class User {
    private $username;
    private $password;

    public function __construct( $username, $password ) {
        $this->username = $username;
        $this->password = $password;
    }

    // Function to authenticate user

    public function login() {
        if ( empty( $this->username ) || empty( $this->password ) ) {
            return $this->response( "error", "Please fill in all required fields." );
        }

        // Check if user exists using select_data utility function
        $user = select_data( 'users', "t_username = '{$this->username}'" );

        if ( count( $user ) > 0 ) {
            $user = $user[0];
            // Get first matching user record
            if ( password_verify( $this->password, $user['t_password'] ) ) {
                if ( $user['t_status'] === 'A' ) {
                    $this->initializeSession( $user );

                    $profile = $this->getUserProfile( $user['user_id'] );
                    if ( $profile ) {

                        $_SESSION['user_profile'] = [
                            'user_firstname' => $profile['user_firstname'],
                            'user_lastname' => $profile['user_lastname'],
                            'user_mi' => $profile['user_mi'],
                            'user_contact_no' => $profile['user_contact_no'],
                            'user_email_address' => $profile['user_email_address']
                        ];

                        $_SESSION['user_firstname'] = $profile['user_firstname'];
                        $_SESSION['user_lastname'] = $profile['user_lastname'];
                        $_SESSION['user_mi'] = $profile['user_mi'];
                        $_SESSION['user_contact_no'] = $profile['user_contact_no'];
                        $_SESSION['user_email_address'] = $profile['user_email_address'];

                        // Set user online status using the updated utility function
                        $setOnline = setOnlineStatus( $user['user_id'], 1 );

                        return $this->response( "success", "Login successful.", [
                            "redirect" => "client/index.php?page=home", // Replace with the desired redirect URL
                            "onlinestatus" => $setOnline
                        ] );
                    } else {
                        return $this->response( "error", "Error fetching user profile data." );
                    }
                } else {
                    return $this->response( "error", "User account is inactive." );
                }
            } else {
                return $this->response( "error", "Incorrect password." );
            }
        } else {
            return $this->response( "error", "Username not found." );
        }
    }

    // Fetch user profile data using the select_data function

    private function getUserProfile( $user_id ) {
        $profile = select_data( "user_profile", "user_id = $user_id" );
        return $profile ? $profile[0] : null;
    }

    // Initialize session variables

    private function initializeSession( $user ) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['t_username'] = $user['t_username'];
        $_SESSION['t_user_type'] = $user['t_user_type'];
        $_SESSION['t_rider_status'] = $user['t_rider_status'];
    }

    // Format response

    private function response( $status, $message, $additionalData = [] ) {
        $response = [
            "status" => $status,
            "message" => $message
        ];
        return json_encode( array_merge( $response, $additionalData ) );
    }
}

// Handle the login request
if ( isset( $_POST['log_username'] ) ) {
    $username = $_POST['log_username'];
    $password = $_POST['log_password'];
    $user = new User( $username, $password );
    echo $user->login();
} else {
    echo json_encode( [
        "status" => "error",
        "message" => "Invalid request."
    ] );
}
?>

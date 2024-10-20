<?php
include_once "_db.php";
if (isset($_POST['log_username'])) {
    $log_username = $_POST['log_username'];
    $log_password = $_POST['log_password'];

    // Validate input
    if (empty($log_username) || empty($log_password)) {
        echo "Please fill in all required fields.";
    } else {
        // Check if user exists
        $check_user_query = "SELECT * FROM users WHERE t_username = ?";
        $stmt = $conn->prepare($check_user_query);
        $stmt->bind_param("s", $log_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = password_hash($row['t_password'],PASSWORD_DEFAULT);

            // Verify password
            if (password_verify($log_password, $hashed_password)) {
                // Check user status
                if ($row['t_status'] == 'A') {
                    // Set session variables
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['t_username'] = $row['t_username'];
                    $_SESSION['t_user_type'] = $row['t_user_type'];
                    $_SESSION['t_rider_status'] = $row['t_rider_status'];

                    // Fetch user profile data
                    $get_profile_query = "SELECT * FROM user_profile WHERE user_id = ?";
                    $stmt = $conn->prepare($get_profile_query);
                    $stmt->bind_param("i", $row['user_id']);
                    $stmt->execute();
                    $profile_result = $stmt->get_result();

                    if ($profile_result->num_rows > 0) {
                        $profile_row = $profile_result->fetch_assoc();
                        $_SESSION['user_firstname'] = $profile_row['user_firstname'];
                        $_SESSION['user_lastname'] = $profile_row['user_lastname'];
                        $_SESSION['user_mi'] = $profile_row['user_mi'];
                        $_SESSION['user_contact_no'] = $profile_row['user_contact_no'];
                        $_SESSION['user_email_address'] = $profile_row['user_email_address'];

                        // Redirect to the desired page after successful login
                        echo 1; // Replace with your desired redirect URL
                        exit();
                    } else {
                        echo "Error fetching user profile data.";
                    }
                } else {
                    echo "User account is inactive.";
                }
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Username not found.";
        }
    }
}

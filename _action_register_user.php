<?php
include_once "_db.php";


if (isset($_POST['f_emailadd'])) {
    $f_emailadd = $_POST['f_emailadd'];
    $f_username = $_POST['f_username'];
    $f_password = $_POST['f_password'];
    $f_cpassword = $_POST['f_cpassword'];

    // Validate input
    if (empty($f_emailadd) || empty($f_username) || empty($f_password) || empty($f_cpassword)) {
        echo "Please fill in all required fields.";
    } elseif ($f_password !== $f_cpassword) {
        echo "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE t_username = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $f_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Username already exists.";
        } else {
            // Insert user data into the database
            $insert_user_query = "INSERT INTO users (t_username, t_password) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_user_query);
            $stmt->bind_param("ss", $f_username, $f_password);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Get the newly inserted user ID
                $user_id = $conn->insert_id;

                // Insert user profile data
                $insert_profile_query = "INSERT INTO user_profile (user_id, user_email_address) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_profile_query);
                $stmt->bind_param("is", $user_id, $f_emailadd);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "Registration successful!";
                } else {
                    echo "Error inserting user profile data.";
                }
            } else {
                echo "Error inserting user data.";
            }
        }
    }
}
?>
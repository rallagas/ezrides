<?php
include_once "_db.php"; // Include database connection and utility functions
//include_once "_sql_utility.php";
include_once "_functions.php";

header('Content-Type: application/json'); // Set header to return JSON

$errors = []; // Initialize an array to store validation errors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Required fields
    $requiredFields = ['f_emailadd', 'f_username', 'f_password', 'f_cpassword', 'agreement_Checkbox'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = "This field is required.";
        }
    }

    // Email validation
    if (!filter_var($_POST['f_emailadd'], FILTER_VALIDATE_EMAIL)) {
        $errors['f_emailadd'] = "Invalid email format.";
    }

    // Username validation (alphanumeric, 4-20 characters)
    if (!empty($_POST['f_username']) && !preg_match('/^[a-zA-Z0-9]{4,20}$/', $_POST['f_username'])) {
        $errors['f_username'] = "Username should be 4-20 alphanumeric characters.";
        
        if (usernameExists(CONN, $_POST['f_username'])) {
            $errors['f_username'] = "Username already exists.";
        }
    }

    // Password validation (minimum length 6)
    if (!empty($_POST['f_password']) && strlen($_POST['f_password']) < 6) {
        $errors['f_password'] = "Password must be at least 6 characters long.";
    }

    // Password confirmation
    if ($_POST['f_password'] !== $_POST['f_cpassword']) {
        $errors['f_cpassword'] = "Passwords do not match.";
    }

    // Contact number validation (Philippines format: 09XXXXXXXXX)
    if (!empty($_POST['f_contact']) && !preg_match('/^09\d{9}$/', $_POST['f_contact'])) {
        $errors['f_contact'] = "Invalid contact number. Use format 09XXXXXXXXX.";
    }

    // Gender validation (optional field, only if value is provided)
    if (!empty($_POST['f_gender']) && !in_array($_POST['f_gender'], ['M', 'F', '1', '2', '3'])) {
        $errors['f_gender'] = "Invalid gender selection.";
    }

    // Rider information validation (only if registering as a rider)
    $isRider = !empty($_POST['f_rider_status']);
    if ($isRider) {
        if (empty($_POST['f_r_car_brand'])) {
            $errors['f_r_car_brand'] = "Car brand/model is required for riders.";
        }
        if (!empty($_POST['f_r_plate_no']) && !preg_match('/^[A-Z]{3}-\d{4}$/', $_POST['f_r_plate_no'])) {
            $errors['f_r_plate_no'] = "Plate number must be in the format XXX-XXXX.";
        }
//        if (!empty($_POST['f_r_license_no']) ) {
//            $errors['f_r_license_no'] = "License Invalid.";
//        }
    }

    // Agreement checkbox validation
    if (empty($_POST['agreement_Checkbox'])) {
        $errors['agreement_Checkbox'] = "You must accept the terms and conditions.";
    }

    // If there are validation errors, return them in JSON format
    if (!empty($errors)) {
        echo json_encode([
            "status" => "error",
            "message" => "Validation failed.",
            "errors" => $errors // Provide detailed errors for each field
        ]);
        exit;
    }

    // No validation errors, proceed with database insertion

    // Insert into `users` table
    $userData = [
        't_username' => $_POST['f_username'],
        't_password' => password_hash($_POST['f_password'], PASSWORD_BCRYPT), // Hash password
        't_rider_status' => $isRider ? 1 : 0
    ];
    insert_data('users', $userData);
    $userId = getLastInsertedId('users'); // Get the last inserted user ID using the new function

    // Insert into `user_profile` table
    $profileData = [
        'user_id' => $userId,
        'user_firstname' => $_POST['f_fname'] ?? null,
        'user_lastname' => $_POST['f_lname'] ?? null,
        'user_mi' => $_POST['f_mname'] ?? null,
        'user_contact_no' => $_POST['f_contact'] ?? null,
        'user_gender' => $_POST['f_gender'] ?? null,
        'user_email_address' => $_POST['f_emailadd'],
        'rider_plate_no' => $isRider ? ($_POST['f_r_plate_no'] ?? null) : null,
        'rider_license_no' => $isRider ? ($_POST['f_r_license_no'] ?? null) : null
    ];
    insert_data('user_profile', $profileData);

    // Return success response
    echo json_encode([
        "status" => "success",
        "message" => "Registration completed successfully!"
    ]);
} else {
    // If request method is not POST, return an error
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>

<?php
// Include SQL utility functions
//include_once "_sql_utility.php";

// Check if the current page is the specified page
function ifPageis($page, $include, $default="") {
    if (isset($_GET['page']) && $_GET['page'] == $page && isset($_GET['txn_cat'])) {

         $_SESSION['txn_cat_id'] = $_GET['txn_cat'];

        include_once "$include";
        return true;
        exit;
    }
    else if($default != ""){
        include_once $default;
        return true;
        exit;
    }
    else {
        return false;
        exit;
    }

}

// Check if the current action is the specified action
function ifActionis($page, $include) {
    if (isset($_GET['page_action']) && $_GET['page_action'] == $page) {
        include_once "$include";
        return true;
    }
    return false;
}

// Set the user's online status
// Example for setting the online status
function setOnlineStatus($user_id, $online_status = 0) {
    $data = [
        't_online_status' => $online_status,
        't_last_online_ts' => ($online_status == 1) ? date("Y-m-d H:i:s") : null // Only set timestamp if online
    ];

    $where = [
        'user_id' => $user_id
    ];

    return update_data('users', $data, $where); // Update the 'users' table with data and condition
}

// Check if the username already exists
function usernameExists($username) {
    $table = "users";
    $where = ["t_username" => $username];

    // Use the select_data function to query the username
    $result = select_data($table, "t_username = '$username'");
    return !empty($result);
}

// Check if the email already exists
function emailExists($email) {
    $table = "user_profile";
    $where = ["user_email_address" => $email];

    // Use the select_data function to query the email
    $result = select_data($table, "user_email_address = '$email'");
    return !empty($result);
}

// Check the user's online status
function checkOnlineStatus($user_id) {
    $table = "users";
    $result = select_data($table, "user_id = $user_id");

    if (!empty($result)) {
        $row = $result[0];
        $online_status = ($row['t_online_status'] == 1) ? "Online" : "Offline";
        return [
            'online_status' => $online_status,
            'last_online_ts' => $row['t_last_online_ts']
        ];
    }

    return null; // User not found
}

function isRider($user_id) {
    // Table name and condition
    $table = "users";
    $where = "user_id = $user_id AND t_rider_status = 1";
    
    // Use the select_data utility function
    $result = select_data($table, $where, null, 1); // Limit to 1 result for efficiency
    
    // Check if we got a result
    return !empty($result); // If result is not empty, the user is a rider
}
?>

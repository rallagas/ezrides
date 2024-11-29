<?php
include_once "../../_db.php";
include_once "../../_sql_utility.php";


$data = [];

if(isset($_POST['firstname'])){
    $data = [
        /* COLUMN NAMES   =>   VALUE */
        'user_firstname' => $_POST['firstname'],
        'user_lastname' => $_POST['lastname'],
        'user_mi' => $_POST['mi'],
        'user_email_address' => $_POST['email'],
        'user_contact_no' => $_POST['phone']
    ];

    $where = ['user_id' => USER_LOGGED];

    $table = "user_profile";

    if(update_data($table, $data, $where)) {
        header("location: index.php?msg=profileUpdated");
    }
    else{
        header("location: index.php?msg=somethingWentWrong");
    }


}

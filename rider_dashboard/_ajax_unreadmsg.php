<?php
require_once '../_db.php';
require_once '../_sql_utility.php';

$receiver_id = USER_LOGGED; // assuming user is logged in

$where = "receiver_id = $receiver_id AND status = 1 AND DATE(date_received) = CURRENT_DATE";
$unreadMessages = select_data("chatbox", $where);
$count = count($unreadMessages);

echo json_encode([
    'success' => true,
    'unread_count' => $count
]);

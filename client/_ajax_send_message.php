<?php
require '../_db.php'; // Include your database connection


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = intval($_POST['sender_id']);
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if (!empty($sender_id) && !empty($receiver_id) && !empty($message)) {
        $data = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message
        ];

        if (insert_data('chatbox', $data)) {
            echo json_encode([
                'status' => 'success',
                'message' => $message
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'error' => 'Failed to insert the message into the database.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'error' => 'Invalid input data.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'error' => 'Invalid request method.'
    ]);
}
?>

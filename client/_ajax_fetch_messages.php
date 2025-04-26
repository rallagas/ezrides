<?php
require __DIR__ . "../_db.php"; // Include the database connection and query function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = intval($_POST['sender_id']);
    $receiver_id = intval($_POST['receiver_id']);

    if (!empty($sender_id) && !empty($receiver_id)) {
        $sql = "
            SELECT * 
            FROM chatbox 
            WHERE 
                (sender_id = ? AND receiver_id = ?) 
                OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY date_received ASC
            LIMIT 10
        ";

        // Execute the query using the query() function
        $messages = query($sql, [$sender_id, $receiver_id, $receiver_id, $sender_id]);

        echo json_encode([
            'status' => 'success',
            'messages' => $messages
        ]);
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

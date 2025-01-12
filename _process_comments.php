<?php
require_once '_db.php';

$response = ['status' => 'error', 'message' => 'Something went wrong, please try again.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'comment_email', FILTER_SANITIZE_EMAIL); // Sanitize email input
    $message = filter_input(INPUT_POST, 'comment_msg', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Modern and safer alternative to FILTER_SANITIZE_STRING
    $rating = filter_input(INPUT_POST, 'comment_rating', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $photo = $_FILES['comment_pic'] ?? null;
    
    if ($email && $message && $rating !== false) {
        $photoPath = null;

        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            $uploadDir = './images/comments-photo/';
            $photoName = uniqid('comment_', true) . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
            $photoPath = $uploadDir . $photoName;

            if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
                $response['message'] = 'Failed to upload the photo.';
                echo json_encode($response);
                exit;
            }
        }

        $stmt = CONN->prepare('INSERT INTO customerSuggestions (emailadd, message, rate, photo) VALUES (NULL, ?, ?, ?)');
        $stmt->bind_param('ssis',$email, $message, $rating, $photoPath);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Thank you for your suggestion!'];
        } else {
            $response['message'] = 'Failed to save your suggestion. Please try again later.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid input. Please fill all fields correctly.';
    }
}

echo json_encode($response);
?>

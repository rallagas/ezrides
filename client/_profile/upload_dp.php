<?php
//session_start();
require_once '../../_db.php'; // Ensure CONN is defined for DB connection

if (!isset($_SESSION['user_id']) || !isset($_POST['submit_profile_upload'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../../profile/$user_id/";
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $dateTime = date('Ymd_His');
    $filename = $dateTime . '.jpg';
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        //$relativePath = $targetFile;
        $relativePath = $user_id . "/" . $filename; //filename

        $stmt = mysqli_prepare(CONN, "UPDATE user_profile SET user_profile_image = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $relativePath, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

      header("Location: index.php?uploadSuccessful"); // Replace with your profile page
        exit;
    } else {
        echo "Failed to upload the file.";
    }
} else {
    echo "No file uploaded or upload error.";
}

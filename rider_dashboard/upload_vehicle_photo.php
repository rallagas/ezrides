<?php
include_once "../_db.php"; // where USER_LOGGED is defined

$user_id = USER_LOGGED;
$photo_slot = isset($_POST['photo_slot']) ? intval($_POST['photo_slot']) : 0;

if (!isset($_FILES['vehicle_photo']) || $photo_slot < 1 || $photo_slot > 2) {
    die("Invalid upload.");
}
$upload_dir = "../profile/" . $user_id . "/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$file = $_FILES['vehicle_photo'];
$filename = basename($file['name']);
$ext = pathinfo($filename, PATHINFO_EXTENSION);
$new_filename = "vehicle_photo_$photo_slot." . strtolower($ext);
$target_path = $upload_dir . $new_filename;

if (!in_array($file['type'], $allowed_types)) {
    die("Only JPG and PNG images are allowed.");
}

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    $db_path = $user_id . "/" . $new_filename;
    $column = $photo_slot == 1 ? 'vehicle_photo_1' : 'vehicle_photo_2';

   
    $stmt = CONN->prepare("UPDATE user_profile SET $column = ? WHERE user_id = ?");
    $stmt->bind_param("si", $db_path, $user_id);
    $stmt->execute();

    header("Location: profile.php?upload=success"); // redirect to your profile page
    exit;
} else {
    die("Upload failed.");
}
?>

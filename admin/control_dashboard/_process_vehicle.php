<?php
require_once '../../_db.php';  // Include the database connection

$response = ['success' => false, 'message' => ''];

try {
    $vehicle_type = $_POST['vehicleType'];
    $vehicle_plate_no = $_POST['platenumber'];
    $vehicle_color = $_POST['carcolor'];
    $vehicle_model = $_POST['carmodel'];
    $vehicle_owner_name = $_POST['ownername'];
    $vehicle_owner_address = $_POST['owneraddress'];
    $vehicle_price_rate_per_hr = $_POST['rateperhr'];
    $vehicle_price_rate_per_day = $_POST['rateperday'];
    $vehicle_price_rate_per_km = $_POST['rateperkm'];

    // File upload handling
    $vehicle_img = '';
    if (isset($_FILES['carphoto']) && $_FILES['carphoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../car_image/';
        $fileName = basename($_FILES['carphoto']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['carphoto']['tmp_name'], $targetFile)) {
            $vehicle_img = $fileName;
        } else {
            throw new Exception('Failed to upload photo.');
        }
    }

    // Prepare SQL
    $stmt = mysqli_prepare(CONN, "INSERT INTO vehicle (
            vehicle_type, vehicle_plate_no, vehicle_color, vehicle_model,
            vehicle_img, vehicle_owner_name, vehicle_owner_address,
            vehicle_price_rate_per_hr, vehicle_price_rate_per_day, vehicle_price_rate_per_km
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param(
        $stmt, 'ssssssssss', 
        $vehicle_type, $vehicle_plate_no, $vehicle_color, $vehicle_model, 
        $vehicle_img, $vehicle_owner_name, $vehicle_owner_address, 
        $vehicle_price_rate_per_hr, $vehicle_price_rate_per_day, $vehicle_price_rate_per_km
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Vehicle registered successfully.';
    } else {
        throw new Exception('Database insertion failed: ' . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>

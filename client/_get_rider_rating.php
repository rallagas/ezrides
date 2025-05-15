<?php
include_once '../_db.php';
header('Content-Type: application/json');

// Validate request
if (!isset($_POST['rideruserid']) || empty($_POST['rideruserid'])) {
    echo json_encode(['success' => false, 'message' => 'Rider user ID is required.']);
    exit;
}

$riderId = $_POST['rideruserid'];

// Get total number of ratings (n), average rating for this rider (R)
$query = "
    SELECT COUNT(rating) as count, AVG(rating) as avg_rating
    FROM angkas_bookings
    WHERE angkas_rider_user_id = ? AND rating IS NOT NULL
";

$stmt = mysqli_prepare(CONN, $query);
mysqli_stmt_bind_param($stmt, 's', $riderId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$riderData = mysqli_fetch_assoc($result);

// Get global average (C) and minimum number of votes to balance (m)
$query2 = "
    SELECT COUNT(rating) as total_ratings, AVG(rating) as global_avg
    FROM angkas_bookings
    WHERE rating IS NOT NULL
";

$res2 = mysqli_query(CONN, $query2);
$globalData = mysqli_fetch_assoc($res2);

// Default to 0 if no ratings globally
$total_ratings = (int) $riderData['count'];
$R = floatval($riderData['avg_rating']);
$C = floatval($globalData['global_avg']);
$m = 5; // Threshold; can adjust based on system size

if ($total_ratings === 0) {
    echo json_encode(['success' => true, 'rating' => null, 'message' => 'No Rating yet']);
    exit;
}

// Bayesian Rating Formula: (v / (v + m)) * R + (m / (v + m)) * C
$bayesian_rating = round((($total_ratings / ($total_ratings + $m)) * $R) + (($m / ($total_ratings + $m)) * $C), 2);

echo json_encode(['success' => true, 'rating' => $bayesian_rating]);

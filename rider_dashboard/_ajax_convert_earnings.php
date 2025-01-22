<?php
require_once '../_db.php';
require_once '_class_riderWallet.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ConvertAmount'])) {
        $amount = floatval($_POST['ConvertAmount']);

        if (!defined('USER_LOGGED')) {
            throw new Exception("User not logged in.");
        }

        $wallet = new UserWallet(USER_LOGGED);
        $result = $wallet->convertEarnings($amount);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Earnings converted successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to convert earnings. Please try again.'
            ]);
        }
    } else {
        throw new Exception("Invalid request.");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

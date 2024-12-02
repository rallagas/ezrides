<?php
include_once "../../_db.php"; // Include database connection
include_once "_class_voucher.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $voucherCode = $_POST['voucher_code'];
        $voucherInfo = DiscountVoucher::getVoucherInfo($voucherCode);

        echo json_encode([
            'success' => true,
            'data' => $voucherInfo,
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}
?>

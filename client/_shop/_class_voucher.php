<?php
class DiscountVoucher {
    // Method to get specific voucher info by voucher code
    public static function getVoucherInfo($voucherCode) {

        $query = "SELECT * FROM vouchers WHERE voucher_code = ?";
        $stmt = CONN->prepare($query);
        $stmt->bind_param("s", $voucherCode);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                throw new Exception("Voucher not found.");
            }
        } else {
            throw new Exception("Failed to fetch voucher: " . $stmt->error);
        }
    }

    // Method to add a new voucher
    public static function addVoucher($voucherCode, $voucherAmt, $voucherDesc, $voucherValidUntil, $voucherAvailCount) {

        $query = "
            INSERT INTO vouchers (voucher_code, voucher_amt, voucher_desc, voucher_valid_until, voucher_avail_count)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = CONN->prepare($query);
        $stmt->bind_param("sdssi", $voucherCode, $voucherAmt, $voucherDesc, $voucherValidUntil, $voucherAvailCount);

        if ($stmt->execute()) {
            return CONN->insert_id; // Return the newly inserted voucher ID
        } else {
            throw new Exception("Failed to add voucher: " . $stmt->error);
        }
    }
}
?>

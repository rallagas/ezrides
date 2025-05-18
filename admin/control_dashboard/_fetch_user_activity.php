<?php
require_once('../../_db.php');
require_once('../../_sql_utility.php');

$user_id = intval($_POST['user_id']);
$output = "";

// Get trips and deliveries
$sql = "
    SELECT ab.*, 
           CASE 
               WHEN ab.angkas_booking_reference LIKE 'ANG%' THEN 'Angkas Ride' 
               WHEN ab.angkas_booking_reference LIKE 'GRX%' THEN 'Delivery Ride' 
               ELSE 'Unknown' 
           END AS ride_type,
           so.amount_to_pay AS shop_item_cost
    FROM angkas_bookings ab
    LEFT JOIN shop_orders so 
        ON ab.shop_order_reference_number = so.shop_order_ref_num
    WHERE ab.user_id = $user_id
    ORDER BY ab.date_booked DESC
";

$logs = query($sql);

$grouped = [];
foreach ($logs as $log) {
    $date = date("Y-m-d", strtotime($log['date_booked']));
    $grouped[$date][] = $log;
}

// Fetch payment history
$wallet_sql = "
    SELECT * FROM user_wallet
    WHERE payFrom = $user_id OR payTo = $user_id
    ORDER BY wallet_txn_start_ts DESC
";
$wallets = query($wallet_sql);
$wallet_by_day = [];
foreach ($wallets as $w) {
    $date = date("Y-m-d", strtotime($w['wallet_txn_start_ts']));
    $wallet_by_day[$date][] = $w;
}

// Merge dates
$all_dates = array_unique(array_merge(array_keys($grouped), array_keys($wallet_by_day)));
rsort($all_dates);

// Build HTML output
foreach ($all_dates as $date) {
    $output .= "<h5 class='mt-4'>" . date("F j, Y", strtotime($date)) . "</h5><div class='list-group mb-3'>";

    if (!empty($grouped[$date])) {
        foreach ($grouped[$date] as $log) {
            $output .= "<div class='list-group-item'>";
            $output .= "<strong>{$log['ride_type']}</strong><br>";
            $output .= "From: {$log['form_from_dest_name']}<br>";
            $output .= "To: {$log['form_to_dest_name']}<br>";
            $output .= "Fare: ₱" . number_format($log['form_Est_Cost'], 2) . "<br>";
            if ($log['ride_type'] == 'Delivery Ride') {
                $output .= "Shop Cost: ₱" . number_format($log['shop_cost'], 2) . "<br>";
                if ($log['shop_order_reference_number']) {
                    $output .= "<small>Shop Ref#: {$log['shop_order_reference_number']}</small><br>";
                }
            }
            $output .= "<small class='text-muted'>Booked: {$log['date_booked']}</small>";
            $output .= "</div>";
        }
    }

    if (!empty($wallet_by_day[$date])) {
        foreach ($wallet_by_day[$date] as $txn) {
            $fromTo = ($txn['payTo'] == -1) ? "Paid to Site" : "Transfer";
            $output .= "<div class='list-group-item bg-light'>";
            $output .= "<strong>{$txn['wallet_action']}</strong> – ₱" . number_format($txn['wallet_txn_amt'], 2) . "<br>";
            $output .= "<small class='text-muted'>Txn: {$fromTo}, Ref: {$txn['reference_number']}</small><br>";
            $output .= "<small class='text-muted'>Timestamp: {$txn['wallet_txn_start_ts']}</small>";
            $output .= "</div>";
        }
    }

    $output .= "</div>";
}

if (empty($output)) {
    $output = "<p class='text-muted'>No activity found for this user.</p>";
}

echo $output;

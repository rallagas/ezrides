<div class="overflow-y-scroll m-0 p-0" style="height:100vh">
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
    FROM `angkas_bookings` ab
    LEFT JOIN `shop_orders` so 
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

//fetch_user_profile
$user_sql = "select up.rider_plate_no 
                    ,up.user_profile_image 
                    ,up.user_firstname
                    , up.user_lastname
                    , up.user_mi
                    , up.user_contact_no
                    , up.user_id
                    , up.user_email_address
                    , up.vehicle_model_id
                    , up.vehicle_photo_1
                    , DATE(u.date_joined) as date_joined
                    , u.t_status
              from user_profile up
              JOIN users u 
              on u.user_id = up.user_id
              where u.user_id = ?";
$userdata = query($user_sql, [$user_id]);
foreach($userdata as $ud){
?>

<div class="card mb-3">
  <div class="card-header p-0">
  <div id="vehicleCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner" style="height: 200px;">

      <?php
      $vehiclePhotos = [
        $ud['vehicle_photo_1'] ?? 'default.jpg',
        $ud['vehicle_photo_2'] ?? 'default.jpg'
      ];

      $shown = [];
      foreach ($vehiclePhotos as $index => $photo):
        $image = !empty($photo) && strtolower($photo) !== 'null' ? $photo : 'default.jpg';

        // prevent duplicate images
        if (in_array($image, $shown)) continue;
        $shown[] = $image;
      ?>
        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
          <img src="../../profile/<?php echo $image; ?>" class="d-block w-100" style="height: 200px; object-fit: cover;" alt="Vehicle Image">
        </div>
      <?php endforeach; ?>

    </div>

    <?php if (count($shown) > 1): ?>
      <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    <?php endif; ?>
  </div>
</div>

  <div class="card-body position-relative">
    <div class="position-absolute top-0 start-50 translate-middle" >
      <img src="../../profile/<?php echo $ud['user_profile_image']; ?>" 
              alt="Profile" class="rounded-circle border border-white border-5 shadow" style="width: 100px; height: 100px; object-fit: cover;">
    </div>

    <div class="text-center mt-5 pt-3">
      <h4 class="fw-bold mb-0"><?php echo $ud['user_firstname'] . ' ' . $ud['user_lastname']; ?></h4>
      <span class="small text-muted"><?php echo ($ud['rider_plate_no'] ? 'Rider' : 'client');?></span>
 
    </div>

    <div class="d-flex justify-content-center gap-4 mb-3 text-center">
      <div>
        <div class="fw-bold"><?php echo $ud['user_email_address'] ?? ''; ?></div>
      </div>
      <div>
      <div class="fw-bold">
  Joined Since <?php echo isset($ud['date_joined']) ? date('F d, Y', strtotime($ud['date_joined'])) : 'N/A'; ?>
</div>

      </div>
    </div>

    <div class="text-center mt-3">
      <button class="btn btn-outline-primary me-2"><i class="bi bi-chat-dots"></i> <?php echo $ud['t_status'] == 'A' ? 'Block' : 'Unblock'; ?></button>
    </div>
  </div>
</div>

<?php
}
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
?>
</div>
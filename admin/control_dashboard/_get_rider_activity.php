<?php
require_once '../../_db.php';
require_once '../../_sql_utility.php';

header('Content-Type: application/json');


try {
    $sql = "
        SELECT DATE(us.date_joined) AS reg_date, COUNT(*) AS total
        FROM users us
        JOIN user_profile up ON us.user_id = up.user_id
        WHERE up.rider_plate_no IS NOT NULL
          AND DATE(us.date_joined) >= CURDATE() - INTERVAL 9 DAY
        GROUP BY DATE(us.date_joined)
        ORDER BY reg_date ASC
    ";

    $results = query($sql);

    // Build a full 10-day range
    $data = [];
    for ($i = 9; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $data[$date] = 0;
    }

    // Populate with actual results
    foreach ($results as $row) {
        $data[$row['reg_date']] = intval($row['total']);
    }

    echo json_encode([
        'labels' => array_keys($data),
        'counts' => array_values($data)
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
<?php
require_once '../../_db.php';
require_once '../../_sql_utility.php';

$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';

if ($keyword === '') {
    echo "<div class='text-muted'>Enter a name to search.</div>";
    exit;
}

$keyword = "%" . $keyword . "%";

// Search users by name (first or last)
$sql = "
    SELECT us.user_id, up.user_firstname, up.user_lastname, up.rider_plate_no
    FROM users us
    JOIN user_profile up ON us.user_id = up.user_id
    WHERE CONCAT(up.user_firstname, ' ', up.user_lastname) LIKE ?
       OR CONCAT(up.user_lastname, ' ', up.user_firstname) LIKE ?
    ORDER BY up.user_lastname ASC
";

$results = query($sql, [$keyword, $keyword]);

if (count($results) === 0) {
    echo "<div class='text-danger'>No users found.</div>";
    exit;
}

foreach ($results as $u) {
    $btnClass = $u['rider_plate_no'] == NULL ? 'btn-primary' : 'btn-warning';
    $fullName = strtoupper($u['user_lastname'] . ", " . $u['user_firstname']);
    $user_id = $u['user_id'];
    $userclass=($u['rider_plate_no'] == NULL ? 'btn-primary' : 'btn-warning');
    echo "<a href='#'  id='$user_id' 
            class='user-log-trigger btn w-100 $btnClass text-start p-2 mb-2 user-log-trigger'>
                $fullName
          </a>";
}

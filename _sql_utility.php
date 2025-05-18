<?php
// db.php - Define and initialize the global database connection constant
//require_once 'db.php';

/*******************************************/
/* SQL UTILITY 2.1 */
/*******************************************/

function gen_book_ref_num($len, $prefix=""){
    $alpha_num = array_merge(range('A', 'Z'), range(0, 9));
    $key = "";
    for ($i = 0; $i < $len; $i++){
        $key .= $i % 2 == 0 ? $alpha_num[rand(0, 25)] : $alpha_num[rand(26, 35)];
    }
    return $prefix . $key;
}



function insert_data($table, $data) {
    // Database connection, assuming CONN is a valid connection
    
    $keys = array_keys($data);
    $placeholders = implode(',', array_fill(0, count($keys), '?'));
    $query = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES ($placeholders)";

    $stmt = mysqli_prepare(CONN, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error(CONN));  // Log the prepare error
        return false;
    }

    // Dynamically bind parameters
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($data)), ...array_values($data));

    // Execute and check for errors
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));  // Log the execution error
        return false;
    }

    mysqli_stmt_close($stmt);
    return true;
}

/********************
$data = [
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
    'password' => 'mypassword'
];
insert_data('users', $data);
**********************/



function select_data($table, $where = null, $order_by = null, $limit = null) {
    $query = "SELECT * FROM $table";
    if ($where) $query .= " WHERE $where";
    if ($order_by) $query .= " ORDER BY $order_by";
    if ($limit) $query .= " LIMIT $limit";
    
    $stmt = mysqli_prepare(CONN, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}


function select($table, $where = null, $order_by = null, $limit = null) {
    $query = "SELECT * FROM $table";

        // Handle WHERE clause
    if ($where && is_array($where)) {
        $conditions = [];
        $values = [];
        foreach ($where as $column => $value) {
            if (is_null($value)) {
                $conditions[] = "$column IS NULL";
            } elseif (strpos($value, '%') !== false) {
                $conditions[] = "$column LIKE ?";
                $values[] = $value;
            } else {
                $conditions[] = "$column = ?";
                $values[] = $value;
            }
        }
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Handle ORDER BY clause
    if ($order_by && is_array($order_by)) {
        $orders = [];
        foreach ($order_by as $column => $direction) {
            $orders[] = "$column $direction";
        }
        $query .= " ORDER BY " . implode(", ", $orders);
    }

    // Handle LIMIT clause
    if ($limit) {
        $query .= " LIMIT $limit";
    }

    // Prepare the statement
    $stmt = mysqli_prepare(CONN, $query);

    // Bind parameters for WHERE clause
    if ($where && is_array($where)) {
        $types = str_repeat('s', count($where));
        $values = array_values($where);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
    }

    // Execute the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the results
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}


function select_join($tables = [], $joins = [], $where = null, $order_by = null, $limit = null) {
    // Base query
    $query = "SELECT * FROM $tables[0]";

    // Handle JOIN clauses
    if (!empty($joins) && is_array($joins)) {
        foreach ($joins as $join) {
            // Each join must specify ['table' => 'table_name', 'on' => 'condition', 'type' => 'INNER|LEFT|RIGHT|FULL']
            $type = strtoupper($join['type'] ?? 'INNER');
            $table = $join['table'];
            $on = $join['on'];
            $query .= " $type JOIN $table ON $on";
        }
    }

    // Handle WHERE clause
    if ($where && is_array($where)) {
        $conditions = [];
        $values = [];
        foreach ($where as $column => $value) {
            if (is_null($value)) {
                $conditions[] = "$column IS NULL";
            } elseif (strpos($value, '%') !== false) {
                $conditions[] = "$column LIKE ?";
                $values[] = $value;
            } else {
                $conditions[] = "$column = ?";
                $values[] = $value;
            }
        }
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Handle ORDER BY clause
    if ($order_by && is_array($order_by)) {
        $orders = [];
        foreach ($order_by as $column => $direction) {
            $orders[] = "$column $direction";
        }
        $query .= " ORDER BY " . implode(", ", $orders);
    }

    // Handle LIMIT clause
    if ($limit) {
        $query .= " LIMIT $limit";
    }

    // Prepare the statement
    $stmt = mysqli_prepare(CONN, $query);

    // Bind parameters for WHERE clause
    if (!empty($values)) {
        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($stmt, $types, ...$values);
    }

    // Execute the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the results
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

/*
simple joins
$tables = ['users'];
$joins = [
    ['table' => 'orders', 'on' => 'users.id = orders.user_id', 'type' => 'INNER']
];
$where = ['users.status' => 'active', 'orders.total' => '%100%'];
$data = select_data($tables, $joins, $where);
// Generates: 
// SELECT * FROM users 
// INNER JOIN orders ON users.id = orders.user_id 
// WHERE users.status = 'active' AND orders.total LIKE '%100%'

multiplejoins
$tables = ['users'];
$joins = [
    ['table' => 'orders', 'on' => 'users.id = orders.user_id', 'type' => 'LEFT'],
    ['table' => 'products', 'on' => 'orders.product_id = products.id', 'type' => 'INNER']
];
$where = ['users.status' => 'active', 'products.category' => 'electronics'];
$order_by = ['users.name' => 'ASC', 'products.price' => 'DESC'];
$limit = 10;

$data = select_data($tables, $joins, $where, $order_by, $limit);
// Generates:
// SELECT * FROM users 
// LEFT JOIN orders ON users.id = orders.user_id 
// INNER JOIN products ON orders.product_id = products.id 
// WHERE users.status = 'active' AND products.category = 'electronics' 
// ORDER BY users.name ASC, products.price DESC 
// LIMIT 10

*/



/*
$data = select_data('users', 'id = 1');
foreach ($data as $row) {
    echo $row['name'] . '<br>';
    echo $row['email'] . '<br>';
}

*/

function delete_data($table, $where) {
    $query = "DELETE FROM $table WHERE $where";
    $stmt = mysqli_prepare(CONN, $query);
    mysqli_stmt_execute($stmt);
}
/*
delete_data('users', 'id = 1');
*/

function update_data($table, $data, $where) {
    // Ensure $data and $where are both associative arrays
    if (!is_array($data) || !is_array($where)) {
        throw new InvalidArgumentException('Both $data and $where must be associative arrays.');
    }

    // Prepare SET clause (column = value pairs for update)
    $set_clause = [];
    foreach ($data as $column => $value) {
        $set_clause[] = "$column = ?";
    }

    // Prepare WHERE clause (column = value pairs for condition)
    $where_clause = [];
    foreach ($where as $column => $value) {
        $where_clause[] = "$column = ?";
    }

    // Combine SET and WHERE clauses
    $query = "UPDATE $table SET " . implode(', ', $set_clause) . " WHERE " . implode(' AND ', $where_clause);

    // Prepare the statement
    $stmt = mysqli_prepare(CONN, $query);
    
    // Combine the values from both $data and $where for binding
    $params = array_merge(array_values($data), array_values($where));

    // Determine the types for binding
    $types = str_repeat('s', count($params));  // Assuming all values are strings. Modify as needed.

    // Bind parameters
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    // Execute the statement
    return mysqli_stmt_execute($stmt);
}

/*
$data = [
    'name' => 'Jane Doe',
    'email' => 'janedoe@example.com'
];
$where = ['id' => 1];
update_data('users', $data, $where);
*/

function query($sql, $params = []) {
    $stmt = mysqli_prepare(CONN, $sql);
    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
    }
    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);

    if ($resultData) {
        $data = [];
        while ($row = mysqli_fetch_assoc($resultData)) {
            $data[] = $row;
        }
        return $data;
    }
}



function getUserFullName($user_id) {

    $user_id = intval($user_id); // Sanitize
    $sql = "SELECT user_firstname, user_lastname FROM user_profile WHERE user_id = $user_id LIMIT 1";
    $result = query($sql);

    if ($result && count($result) > 0) {
        $user = $result[0];
        return $user['user_firstname'] . ' ' . $user['user_lastname'];
    } else {
        return 'Unknown User';
    }
}

function getLastInsertedId($tableName) {
    $result = mysqli_query(CONN, "SELECT LAST_INSERT_ID() AS last_id FROM $tableName");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['last_id'];
    }
    return false;
}

function calculateDaysDifference($date1, $date2) {
    return abs((new DateTime($date1))->diff(new DateTime($date2))->days);
}

function calculateRiderRate($distanceInKm, $durationInHours, $baseRatePerHour = 0.00, $baseRatePerKm = 0.00, $flagDownRate = 0.00) {
    return ($distanceInKm * $baseRatePerKm) + ($durationInHours * $baseRatePerHour) + $flagDownRate;
}

function getLocationAddress($location_id) {
    $loc_arr = explode('-', $location_id);
    $region = $loc_arr[0];
    $province = $loc_arr[1];
    $municity = $loc_arr[2];
    
    $address = "";
    $citymun_desc = select_data("refcitymun", "citymunCode = $municity");
    foreach ($citymun_desc as $cm) {
        $address .= " ," . $cm['citymunDesc'];
    }

    $prov_desc = select_data("refprovince", "provCode = $province");
    foreach ($prov_desc as $prov) {
        $address .= " ," . $prov['provDesc'];
    }

    $reg_desc = select_data("refregion", "regCode = $region");
    foreach ($reg_desc as $reg) {
        $address .= " ," . $reg['regDesc'];
    }
    return trim($address, ", ");
}

function getUserInfo($user_id) {
    return select_data("user_profile", "user_id = $user_id");
}

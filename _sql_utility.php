<?php
/*******************************************/
/*SQL UTILITY 2.0
/*******************************************/


function gen_book_ref_num($len, $prefix=""){
    $alpha_num=array('A','B','C','D','E','F','G','H','I','J'
                     ,'K','L','M','N','O','P','Q','R','S','T'
                     ,'U','V','W','X','Y','Z','0','1','2','3'
                     ,'4','5','6','7','8','9','0');
    $key="";
    for ($i = 0; $i <= $len; $i++){
        if($i%2 == 0 && $i > 0){
           $key .= $alpha_num[rand(0,25)];
        }
        else{
             $key .= $alpha_num[rand(26,sizeof($alpha_num)-1)];
        }
     }
    return $prefix . $key;
}

function insert_data($conn, $table, $data) {
  $keys = array_keys($data);
  $values = array_values($data);
  $keys_str = implode(',', $keys);
  $placeholders = implode(',', array_fill(0, count($values), '?'));
  $query = "INSERT INTO $table ($keys_str) VALUES ($placeholders)";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, str_repeat('s', count($values)), ...$values);
  mysqli_stmt_execute($stmt);
}
/* SAMPLE USAGE */


//// Set up database connection
////$conn = mysqli_connect('localhost', 'username', 'password', 'mydatabase');
//// Define data to insert into the 'users' table
//$data = array(
//  'name' => 'John Doe',
//  'email' => 'johndoe@example.com',
//  'password' => 'mypassword'
//);
//// Call the insert_data() function to insert the data into the 'users' table
//insert_data($conn, 'users', $data);
//
//// Close the database connection
//mysqli_close($conn);


function select_data($conn, $table, $where = null, $order_by = null, $limit = null) {
  $query = "SELECT * FROM $table";
  if($where) {
    $query .= " WHERE $where";
  }
  if($order_by) {
    $query .= " ORDER BY $order_by";
  }
  if($limit) {
    $query .= " LIMIT $limit";
  }
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $data = array();
  while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }
  return $data;
}

/*SAMPLE USAGE*/
/*
// Set up database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'mydatabase');

// Call the select_data() function to select data from the 'users' table
$data = select_data($conn, 'users', 'id = 1');

// Loop through the selected data and output it to the page
foreach($data as $row) {
  echo $row['name'] . '<br>';
  echo $row['email'] . '<br>';
}

// Close the database connection
mysqli_close($conn);
*/


function delete_data($conn, $table, $where) {
  $query = "DELETE FROM $table WHERE $where";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_execute($stmt);
}

/*
SAMPLE USAGE
// Set up database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'mydatabase');

// Call the delete_data() function to delete data from the 'users' table
delete_data($conn, 'users', 'id = 1');

// Close the database connection
mysqli_close($conn);

*/

/**
 * Update data in a MySQL table using prepared statements.
 *
 * @param mysqli $conn The MySQLi database connection object.
 * @param string $table The name of the table to update data in.
 * @param array $data An associative array of column-value pairs to update.
 * @param array $where An associative array of column-value pairs to filter the update by.
 *
 * @return bool True if the update was successful, false otherwise.
 */
function update_data($conn, $table, $data, $where) {
  // Build the query string
  $query = "UPDATE $table SET ";

  // Add column-value pairs to the query string
  foreach($data as $column => $value) {
    $query .= "$column = ?, ";
  }

  // Remove the final comma and space from the query string
  $query = rtrim($query, ', ');

  // Add the WHERE clause to the query string
  $query .= " WHERE ";

  // Add column-value pairs to the WHERE clause
  foreach($where as $column => $value) {
    $query .= "$column = ? AND ";
  }

  // Remove the final "AND " from the WHERE clause
  $query = rtrim($query, 'AND ');

  // Prepare the query
  $stmt = mysqli_prepare($conn, $query);

  // Bind the data values to the prepared statement
  $param_types = str_repeat('s', count($data) + count($where));
  $params = array_merge(array_values($data), array_values($where));
  mysqli_stmt_bind_param($stmt, $param_types, ...$params);

  // Execute the prepared statement and return true if successful, false otherwise
  return mysqli_stmt_execute($stmt);
}

/*
SAMPLE USAGE
// Set up database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'mydatabase');

// Define data to update in the 'users' table
$data = array(
  'name' => 'Jane Doe',
  'email' => 'janedoe@example.com',
  'password' => 'newpassword'
);

// Call the update_data() function to update the data in the 'users' table
update_data($conn, 'users', $data, 'id = 1');

// Close the database connection
mysqli_close($conn);

*/

function query($conn, $sql, $params = array()){
        $errmsg = false;
        $stmt=mysqli_stmt_init($conn);
        if(mysqli_stmt_prepare($stmt, $sql)){
            $str = NULL;
            $cnt_p = count($params);
            
            if($cnt_p > 0){
                foreach($params as $param){
                    $str .= "s";
                }
                 mysqli_stmt_bind_param($stmt, "{$str}" , ...$params );
            }
            //echo $sql;
            if(mysqli_stmt_execute($stmt)){
               
                 $resultData = mysqli_stmt_get_result($stmt);
                 if(!empty($resultData)){
                      $arr=array();
                      while($row = mysqli_fetch_assoc($resultData)){
                          array_push($arr,$row);
                      }
                      return $arr;
                 }
                  else{
                     return true;
                 }
            }
            else{
               return false;
            }
        }
        return false;
            
    }


function getLastInsertedId($conn, $tableName) {
    $sql = "SELECT LAST_INSERT_ID() AS last_id FROM $tableName";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['last_id'];
    } else {
        return false;
    }
}


function calculateDaysDifference($date1, $date2) {
    // Create DateTime objects from the string dates
    $date1 = new DateTime($date1);
    $date2 = new DateTime($date2);

    // Calculate the difference between the two  dates
    $interval = $date2->diff($date1);

    // Extract the number of days from the interval
    $daysDifference = $interval->days;

    return $daysDifference;
}


function calculateRiderRate($distanceInKm, $durationInHours, $baseRatePerHour = 0.00, $baseRatePerKm = 0.00, $flagDownRate = 0.00) {
    // Define base rate per kilometer and per hour
    // Calculate total rate based on distance and duration
    $totalRate = ($distanceInKm * $baseRatePerKm) + ($durationInHours * $baseRatePerHour) + $flagDownRate;
    
    return $totalRate;
}

//// Example usage:
//$distanceTraveled = 15; // Replace with actual distance
//$duration = 2.5; // Replace with actual duration
//
//$riderRate = calculateRiderRate($distanceTraveled, $duration);
//echo "Rider's rate: " . $riderRate;



function getLocationAddress ($location_id){
    //04-0434-043406 
    $loc_arr = array();
    $loc_arr = explode('-',$location_id);
    $region = $loc_arr[0];
    $province = $loc_arr[1];
    $municity = $loc_arr[2];
    $address = "";
    
    $citymun_desc = select_data(CONN, "refcitymun", "citymunCode = $municity");
    foreach($citymun_desc as $cm)
    $address = $address . " ," . $cm['citymunDesc'];
    
    $prov_desc = select_data(CONN, "refprovince", "provCode = $province");
    foreach($prov_desc as $prov)
    $address = $address . " ," . $prov['provDesc'];
    
    $reg_desc = select_data(CONN, "refregion", "regCode = $region");
    foreach($reg_desc as $reg)
    $address = $address . " ," . $reg['regDesc'];
    
    return $address;
    
}


function getUserInfo ($user_id){
    $user_profile = select_data(CONN,"user_profile","user_id = $user_id");
    return $user_profile;
}







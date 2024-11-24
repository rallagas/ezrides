<?php

class TxnCategory {
    private $txn_category_id;
    private $page_action;
    private $txn_prefix;
    private $txn_link;
    private $page_include_form;
    private $txn_category_name;
    private $txn_category_status;
    private $icon_class;
    private $txn_title;
    private $load_js_file;

    // Constructor to initialize the class

    public function __construct( $txn_category_id = null ) {
        if ( $txn_category_id ) {
            $this->loadTxnCategory( $txn_category_id );
        }
    }

    /**
    * Load transaction category data into the object
    * @param int $txn_category_id
    */

    private function loadTxnCategory( $txn_category_id ) {
        $query = "SELECT * FROM txn_category WHERE txn_category_id = ?";
        $stmt = CONN->prepare( $query );
        $stmt->bind_param( "i", $txn_category_id );

        if ( $stmt->execute() ) {
            $result = $stmt->get_result();
            if ( $row = $result->fetch_assoc() ) {
                $this->txn_category_id = $row['txn_category_id'];
                $this->page_action = $row['page_action'];
                $this->txn_prefix = $row['txn_prefix'];
                $this->txn_link = $row['txn_link'];
                $this->page_include_form = $row['page_include_form'];
                $this->txn_category_name = $row['txn_category_name'];
                $this->txn_category_status = $row['txn_category_status'];
                $this->icon_class = $row['icon_class'];
                $this->txn_title = $row['txn_title'];
                $this->load_js_file = $row['load_js_file'];
            }
        }

        $stmt->close();
    }

    /**
    * Get the transaction prefix by ID
    * @param int $txn_category_id
    * @return string|null
    */
    public static function getTxnPrefix( $txn_category_id ) {
        $query = "SELECT txn_prefix FROM txn_category WHERE txn_category_id = ?";
        $stmt = CONN->prepare( $query );
        $stmt->bind_param( "i", $txn_category_id );
        $prefix = null;

        if ( $stmt->execute() ) {
            $result = $stmt->get_result();
            if ( $row = $result->fetch_assoc() ) {
                $prefix = $row['txn_prefix'];
            }
        }

        $stmt->close();
        return $prefix;
    }

    // Additional getters

    public function getTxnCategoryId() {
        return $this->txn_category_id;
    }

    public function getPageAction() {
        return $this->page_action;
    }

    public function getTxnPrefixInstance() {
        return $this->txn_prefix;
    }

    public function getTxnLink() {
        return $this->txn_link;
    }

    public function getPageIncludeForm() {
        return $this->page_include_form;
    }

    public function getTxnCategoryName() {
        return $this->txn_category_name;
    }

    public function getTxnCategoryStatus() {
        return $this->txn_category_status;
    }

    public function getIconClass() {
        return $this->icon_class;
    }

    public function getTxnTitle() {
        return $this->txn_title;
    }

    public function getLoadJsFile() {
        return $this->load_js_file;
    }
}

class AngkasBookings {
    private $conn;

    public function __construct() {
        // Use the defined constant CONN for the database connection
        $this->conn = CONN;
    }

    /**
    * Retrieves data from a specified column in the angkas_bookings table.
    *
    * @param string $column - The column to retrieve data from.
    * @param int|null $bookingId - Optional booking ID to filter results.
    * @return array - An array of results or an empty array if no data is found.
    */
    // Public method to get data from the angkas_booking_header_view
    public function getBookingHeaderDetails($userId = null, $op="<>" ,$bookingStatus = 'D', $limit = 1) {
        try {
            // Build the SQL query to fetch the required columns
            $sql = "
                SELECT 
                    angkas_booking_reference, 
                    shop_order_reference_number, 
                    customer_user_id, 
                    rider_user_id, 
                    total_amount_to_pay, 
                    angkas_booking_eta_duration, 
                    angkas_booking_total_distance, 
                    angkas_booking_estimated_cost, 
                    angkas_booking_avg_rating, 
                    customer_username, 
                    customer_user_type, 
                    customer_firstname, 
                    customer_lastname, 
                    booking_status, 
                    payment_status
                FROM `booking_shop_header_view`
            ";

            // If userId is provided, add a WHERE clause to filter by user_id
            if ($userId !== null) {
                $sql .= " WHERE customer_user_id = ? AND booking_status " . $op ." ? ";
            }
            
            $sql .= " ORDER BY `angkas_booking_id` DESC";
            
            if ($limit > 0){
                $sql .= " LIMIT $limit ";
            }
            
            

            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            // Bind the parameter if userId is provided
            if ($userId !== null) {
                $stmt->bind_param("is", $userId, $bookingStatus); // 'i' for integer
            }

            // Execute the statement
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            // Fetch all results into an array
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            // Close the statement
            $stmt->close();

            // Return the result as an associative array
            return $data;

        } catch (Exception $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return false;  // Return false in case of an error
        }
    }
    

    public function getColumnData( $columns, $userId = null, $bookingReference = null, $numRows = 0 ) {
        
        $txn_cat = $_SESSION['txn_cat_id'] != 0  ?  $_SESSION['txn_cat_id'] : 2 ;
        // Ensure $columns is treated as an array if a single column is passed
        if ( is_string( $columns ) ) {
            $columns = [$columns];
        }

        // List of allowed columns for validation
        $allowedColumns = [ 'transaction_category_id', 'txn_prefix', 'angkas_booking_id', 'angkas_booking_reference', 'customer_user_id'
        , 'rider_user_id', 'form_from_dest_name', 'user_currentLoc_lat'
        , 'user_currentLoc_long', 'form_to_dest_name', 'formToDest_long'
        , 'formToDest_lat', 'form_ETA_duration', 'form_TotalDistance', 'form_Est_Cost'
        , 'date_booked', 'booking_status', 'payment_status', 'rating'
        , 'payment_status_text', 'customer_firstname', 'customer_lastname'
        , 'customer_mi', 'customer_gender', 'customer_contact_no', 'customer_email_address'
        , 'customer_profile', 'rider_firstname', 'rider_lastname', 'booking_status_text'
    ];

    // Validate columns
    foreach ( $columns as $column ) {
        if ( !in_array( $column, $allowedColumns ) ) {
            throw new InvalidArgumentException( "Invalid column name: " . $column );
        }
    }

    // Join columns for the SELECT statement
    $selectedColumns = implode( ", ", $columns );

    // Base SQL query
    $sql = "SELECT $selectedColumns FROM view_angkas_bookings WHERE transaction_category_id = '$txn_cat' ";

    // Parameters array for filters
    $params = [];
    $paramTypes = "";

    // Add user ID filter if provided
    if ( $userId !== null ) {
        $sql .= " AND customer_user_id = ?";
        $params[] = $userId;
        $paramTypes .= "i";
        // assuming user_id is an integer
    }

    // Add booking reference filter if provided
    if ( $bookingReference !== null ) {
        $sql .= $userId !== null ? " AND" : " WHERE";
        $sql .= " angkas_booking_reference = ?";
        $params[] = $bookingReference;
        $paramTypes .= "s";
        // assuming angkas_booking_reference is a string
    }

    // Add sorting
    $sql .= " ORDER BY angkas_booking_id DESC";

    if ( $numRows > 0 ) {
        $sql .= " LIMIT $numRows";
    }

    // Prepare the statement
    $stmt = $this->conn->prepare( $sql );
    if ( $stmt === false ) {
        throw new Exception( "Failed to prepare statement: " . $this->conn->error );
    }

    // Bind parameters dynamically
    if ( !empty( $params ) ) {
        $stmt->bind_param( $paramTypes, ...$params );
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all results into an array
    $data = [];
    while ( $row = $result->fetch_assoc() ) {
        if ( count( $columns ) === 1 ) {
            // If a single column was requested, return just the column's value
            $data[] = $row[$columns[0]];
        } else {
            // For multiple columns, return the full row
            $data[] = $row;
        }
    }

    // Close the statement
    $stmt->close();

    return $data;
}



public function getBookingShopCombined($columns, $shopOrderUserId = null, $referenceNumbers = null, $numRows = 0) {
    // Ensure $columns is treated as an array if a single column is passed
    if (is_string($columns)) {
        $columns = [$columns];
    }

    // List of allowed columns for validation
    $allowedColumns = [
        'shop_order_id', 'shop_order_reference_number', 'shop_order_voucher_code', 'shop_order_shipping_fee', 'shop_order_shipping_name',
        'shop_order_shipping_address', 'shop_order_shipping_address_coordinates', 'shop_order_shipping_phone', 'shop_order_user_id',
        'shop_order_rider_id', 'shop_order_item_id', 'shop_order_quantity', 'shop_order_amount_to_pay', 'shop_order_date', 
        'shop_order_delivery_status', 'shop_order_payment_status', 'shop_order_state_indicator', 'shop_order_special_instructions', 
        'angkas_booking_id', 'angkas_booking_reference', 'angkas_booking_transaction_category_id', 'angkas_booking_rider_user_id', 
        'angkas_booking_from_destination_name', 'angkas_booking_user_current_location_latitude', 'angkas_booking_user_current_location_longitude', 
        'angkas_booking_to_destination_name', 'angkas_booking_to_destination_longitude', 'angkas_booking_to_destination_latitude', 
        'angkas_booking_eta_duration', 'angkas_booking_total_distance', 'angkas_booking_estimated_cost', 'angkas_booking_date_booked', 
        'angkas_booking_status', 'angkas_booking_payment_status', 'angkas_booking_rating', 'user_profile_id', 'user_firstname', 
        'user_lastname', 'user_middle_initial', 'user_contact_number', 'user_gender', 'user_email_address', 'user_profile_image', 
        'user_rider_plate_number', 'user_rider_license_number'
    ];

    // Validate columns
    foreach ($columns as $column) {
        if (!in_array($column, $allowedColumns)) {
            throw new InvalidArgumentException("Invalid column name: " . $column);
        }
    }

    // Join columns for the SELECT statement
    $selectedColumns = implode(", ", $columns);

    // Base SQL query
    $sql = "SELECT $selectedColumns FROM booking_shop_combined";

    // Parameters array for filters
    $params = [];
    $paramTypes = "";

    // Add user ID filter if provided
    if ($shopOrderUserId !== null) {
        $sql .= " WHERE shop_order_user_id = ?";
        $params[] = $shopOrderUserId;
        $paramTypes .= "i"; // assuming shop_order_user_id is an integer
    }

    // Add reference number filter if provided
    if ($referenceNumbers !== null) {
        if (is_array($referenceNumbers)) {
            $placeholders = implode(", ", array_fill(0, count($referenceNumbers), "?"));
            $sql .= ($shopOrderUserId !== null) ? " AND (" : " WHERE (";
            $sql .= "shop_order_reference_number IN ($placeholders) OR angkas_booking_reference IN ($placeholders))";
            $params = array_merge($params, $referenceNumbers, $referenceNumbers); // Merge both sets of references
            $paramTypes .= str_repeat("s", count($referenceNumbers) * 2); // Assuming reference numbers are strings
        } else {
            $sql .= ($shopOrderUserId !== null) ? " AND (" : " WHERE (";
            $sql .= "shop_order_reference_number = ? OR angkas_booking_reference = ?)";
            $params[] = $referenceNumbers;
            $params[] = $referenceNumbers;
            $paramTypes .= "ss"; // assuming reference numbers are strings
        }
    }

    // Add sorting
    $sql .= " ORDER BY angkas_booking_id DESC";

    // Apply limit if required
    if ($numRows > 0) {
        $sql .= " LIMIT $numRows";
    }

    // Prepare the statement
    $stmt = $this->conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Failed to prepare statement: " . $this->conn->error);
    }

    // Bind parameters dynamically
    if (!empty($params)) {
        $stmt->bind_param($paramTypes, ...$params);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all results into an array
    $data = [];
    while ($row = $result->fetch_assoc()) {
        if (count($columns) === 1) {
            // If a single column was requested, return just the column's value
            $data[] = $row[$columns[0]];
        } else {
            // For multiple columns, return the full row
            $data[] = $row;
        }
    }

    // Close the statement
    $stmt->close();

    return $data;
}


    
    
public function updatePaymentStatus($bookingReference, $newStatus)
    {
        // Validate that the new status is valid
        $allowedStatuses = ['P', 'D', 'C']; // 'P' = Pending, 'D' = Declined, 'C' = Completed
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new InvalidArgumentException("Invalid payment status: " . $newStatus);
        }

        // SQL query to update the payment status
        $sql = "UPDATE angkas_bookings SET payment_status = ? WHERE angkas_booking_reference = ?";

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        // Bind parameters and execute the query
        $stmt->bind_param("ss", $newStatus, $bookingReference);
        $success = $stmt->execute();

        // Close the statement
        $stmt->close();

        return $success;
    }
    
    public function insertBooking(array $data)
    {
        // List of allowed columns for validation
        $allowedColumns = [
            'angkas_booking_reference','shop_order_reference_number','shop_cost', 'user_id', 'form_from_dest_name', 'user_currentLoc_lat', 
            'user_currentLoc_long', 'form_to_dest_name', 'formToDest_long', 'formToDest_lat', 
            'form_ETA_duration', 'form_TotalDistance', 'form_Est_Cost', 'date_booked', 
            'booking_status', 'payment_status', 'rating', 'transaction_category_id'
            ];

            // Validate the input data
            foreach ( $data as $column => $value ) {
                if ( !in_array( $column, $allowedColumns ) ) {
                    throw new InvalidArgumentException( "Invalid column name: " . $column );
                }
            }

            // Build the SQL query dynamically
            $columns = implode( ", ", array_keys( $data ) );
            $placeholders = implode( ", ", array_fill( 0, count( $data ), "?" ) );

            $sql = "INSERT INTO angkas_bookings ($columns) VALUES ($placeholders)";

            // Prepare the statement
            $stmt = $this->conn->prepare( $sql );
            if ( $stmt === false ) {
                throw new Exception( "Failed to prepare statement: " . $this->conn->error );
            }

            // Determine the types of the parameters
            $paramTypes = "";
            $paramValues = [];
            foreach ( $data as $value ) {
                if ( is_int( $value ) ) {
                    $paramTypes .= "i";
                } elseif ( is_float( $value ) ) {
                    $paramTypes .= "d";
                    $value = number_format($value,2);
                } else {
                    $paramTypes .= "s";
                }
                $paramValues[] = $value;
            }

            // Bind the parameters and execute the query
            $stmt->bind_param( $paramTypes, ...$paramValues );
            $success = $stmt->execute();

            // Close the statement
            $stmt->close();

            return $success . ":". $sql;
        }
    
    public function updateBookingColumns($bookingReference, $data)
{
    // List of allowed columns for validation
    $allowedColumns = [
        'angkas_booking_reference', 'customer_user_id', 'rider_user_id', 'form_from_dest_name',
        'user_currentLoc_lat', 'user_currentLoc_long', 'form_to_dest_name', 'formToDest_long',
        'formToDest_lat', 'form_ETA_duration', 'form_TotalDistance', 'form_Est_Cost', 'date_booked',
        'booking_status', 'payment_status', 'payment_status_text', 'customer_firstname',
        'customer_lastname', 'customer_mi', 'customer_gender', 'customer_contact_no', 'customer_email_address',
        'customer_profile', 'rider_firstname', 'rider_lastname', 'booking_status_text', 'rating'
    ];

    // Validate the input data columns
    foreach ($data as $column => $value) {
        if (!in_array($column, $allowedColumns)) {
            throw new InvalidArgumentException("Invalid column name: " . $column);
        }
    }

    // Prepare the SET part of the SQL query dynamically
    $setClauses = [];
    $paramValues = [];
    $paramTypes = '';

            foreach ( $data as $column => $value ) {
                $setClauses[] = "$column = ?";
                $paramValues[] = $value;

                // Determine the type of the parameter
                if ( is_int( $value ) ) {
                    $paramTypes .= "i";
                } elseif ( is_float( $value ) ) {
                    $paramTypes .= "d";
                } else {
                    $paramTypes .= "s";
                }
            }

            $setClauseStr = implode( ", ", $setClauses );

            // SQL query to update the specified columns
            $sql = "UPDATE angkas_bookings SET $setClauseStr WHERE angkas_booking_reference = ?";

            // Prepare the statement
            $stmt = $this->conn->prepare( $sql );
            if ( $stmt === false ) {
                throw new Exception( "Failed to prepare statement: " . $this->conn->error );
            }

            // Bind parameters for the SET clauses
            $paramValues[] = $bookingReference;
            $paramTypes .= "s";
            // Always add the string type for booking reference

            // Bind the parameters dynamically
            $stmt->bind_param( $paramTypes, ...$paramValues );

            // Execute the query
            $success = $stmt->execute();

            // Close the statement
            $stmt->close();

            return $success;
        }

    }

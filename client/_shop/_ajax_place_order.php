<?php
include_once "../../_db.php";
// Include database connection
include_once "_class_grocery.php";
include_once "../_class_Bookings.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        
        // Extract data from POST request
        $userId = USER_LOGGED;
        $orderItems = $_POST['order_items'];
        $orderRefNum = $_POST['order_ref_num'];
        $shippingName = $_POST['shipping_name'];
        $shippingAddress = $_POST['shipping_address'];
        $shippingPhone = $_POST['shipping_phone'];
        $addressCoordinates = $_POST['address_coordinates'];

        // Debug: Check the structure of orderItems
        error_log('Order items: ' . print_r($orderItems, true));

        // Validate required fields
        if (
            empty($userId) ||
            empty($orderItems) ||
            empty($orderRefNum) ||
            empty($shippingName) ||
            empty($shippingAddress) ||
            empty($shippingPhone)
        ) {
            throw new Exception("All fields are required.");
        }
        $bookingSuccess = false;
        $placeOrderStat = null;
        $angkasData = [];
        $orderedItemIds = [];
        $totalAmount = 0.00;
        foreach ($orderItems as $items) {
            $totalAmount += $items['amount'];
            array_push($orderedItemIds ,$items['itemId']); // Get list of item IDs
        }
        $ShopCost = $totalAmount; //Shop Cost

        // Instantiate the Cart class, passing both userId and orderItems
        $cart = new Cart($userId, $orderItems);

        // Prepare shipping details array
        $shippingDetails = [
            'name' => $shippingName,
            'address' => $shippingAddress,
            'phone' => $shippingPhone,
            'coordinates' => $addressCoordinates,
        ];

        // Call the placeOrder method with the type and shipping details
        $placeOrderStat = $cart->placeOrder($orderRefNum, $shippingDetails);
        

        if(!$placeOrderStat){
            throw new Exception("Place Order Failed");
        }
        else {
            // Fetch merchant info
            $merchant_info = Merchant::fetchCommonMerchantById($orderRefNum, $orderedItemIds);
            if (!$merchant_info) {
                throw new Exception("Merchant information could not be retrieved for: " . $orderRefNum . ":" . implode(',',$orderedItemIds));
            }
            // Build needed data for the AngkasBookings
            $form_from_dest = $merchant_info->getAddress();
            $merchant_loc_coor = $merchant_info->getMerchantLocCoor();
            list($currentLoc_lat, $currentLoc_long) = explode(',', $merchant_loc_coor);

            $form_to_dest = $shippingAddress;
            list($formToDest_lat, $formToDest_long) = explode(',', $addressCoordinates);

            // Get distance, ETA, and cost
            $estimates = getDistanceAndETA($currentLoc_lat, $currentLoc_long, $formToDest_lat, $formToDest_long);
            $estCost = 0.00;
            $cost = computeCostByDistance($estimates['distance_km']);
            $cost = floatval(str_replace(',', '', $cost)); // Ensure the value is a valid float
            $estCost = $cost; //estimated cost in Angkas
            
            $totalAmountToPay = $ShopCost + $estCost ; //add the Booking Estimated Cost
            $totalAmountToPay = floatval(str_replace(',', '', $totalAmountToPay)); 
        
            $txn_cat_id = $_SESSION['txn_cat_id'] ;
            $prefix = TxnCategory::getTxnPrefix($txn_cat_id);
            $ref_num = gen_book_ref_num(8, $prefix);


            // Insert new Angkas booking
            $angkasData = [
                'angkas_booking_reference' => $ref_num, // Generate unique reference
                'shop_order_reference_number' => $orderRefNum,
                'user_id' => $userId,
                'form_from_dest_name' => $form_from_dest,
                'user_currentLoc_lat' => (float) trim($currentLoc_lat),
                'user_currentLoc_long' => (float) trim($currentLoc_long),
                'form_to_dest_name' => $form_to_dest,
                'formToDest_long' => (float) trim($formToDest_long),
                'formToDest_lat' => (float) trim($formToDest_lat),
                'form_ETA_duration' => $estimates['eta_minutes'] ?? null,
                'form_TotalDistance' => $estimates['distance_km'] ?? null,
                'form_Est_Cost' => $estCost, //travel angkas Cost
                'date_booked' => date('Y-m-d H:i:s'),
                'booking_status' => 'P',
                'payment_status' => 'P',
                'transaction_category_id' => $_SESSION['txn_cat_id'] // Add relevant category ID if available
            ];

            $angkasBookings = new AngkasBookings();
            $bookingSuccess = $angkasBookings->insertBooking($angkasData);

//            if (!$bookingSuccess) {
//                throw new Exception("Failed to create Angkas booking.");
//            }
            
            // Respond with success
            echo json_encode([
                'OrderMsg' => "Order Sucessfully Placed.",
                'success' => true,
                'OrderInfo' => $orderItems,
                'OrderRefNum' => $orderRefNum,
                'MerchantInfo' => [
                    'id' => $merchant_info->getId(),
                    'name' => $merchant_info->getName(),
                    'contact' => $merchant_info->getContactInfo(),
                    'address' => $merchant_info->getAddress(),
                    'merchantLocCoor' => $merchant_info->getMerchantLocCoor()
                ],
                'AngkasBookingInfo' => $angkasData,
                'message' => 'Order and Angkas booking placed successfully!',
                'totalAmountToPay' => $totalAmountToPay,
                'bookingStatus' => $bookingSuccess
            ]);
        }
    } catch (Exception $e) {
        // Handle errors
        echo json_encode([
            'success' => false,
            'AngkasBookingInfo' => $angkasData,
            'message' => $e->getMessage(),
            'bookingStatus' => $bookingSuccess
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}

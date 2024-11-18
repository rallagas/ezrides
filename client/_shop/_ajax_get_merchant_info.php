<?php
include_once "../../_db.php";
include_once "_class_grocery.php";
// Assuming the PHP file is receiving the item_id via POST
    
if(isset($_POST['item_id'])) {
    
    $itemId = $_POST['item_id'];
    $response = ['success'=>false, 'item' => $itemId];
    $response['item'] = $itemId;
    if ($itemId !== null) {
        try {
            // Call the Merchant class to fetch merchant information based on item_id
            $merchant = Merchant::fetchMerchantInfoByItem([$itemId]);

            if ($merchant) {
                // Prepare the response data
                $response = [
                    'success' => true,
                    'merchant_info' => [
                        'id' => $merchant->getId(),
                        'name' => $merchant->getName(),
                        'contact_info' => $merchant->getContactInfo(),
                        'address' => $merchant->getAddress(),
                        'merchant_loc_coor' => $merchant->getMerchantLocCoor(),
                    ]
                ];
            } else {
                // No merchant found
                $response = [
                    'success' => false,
                    'message' => 'Merchant not found for the given item ID.'
                ];
            }
        } catch (Exception $e) {
            // Handle any errors during the database query or processing
            $response = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    } else {
        // Handle missing or invalid item_id
        $response = [
            'success' => false,
            'message' => 'No item_id provided.'
        ];
    }    
    
}


// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

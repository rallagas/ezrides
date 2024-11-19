<?php
include_once "../../_db.php";
include_once "_class_grocery.php";

// Initialize response
$response = ['success' => false, 'message' => 'No item_ids provided.'];

// Check if item_ids are provided in the POST request
if (isset($_POST['item_ids']) && is_array($_POST['item_ids']) && count($_POST['item_ids']) > 0) {
    // Retrieve item_ids from the POST request
    $itemIds = $_POST['item_ids'];

    try {
        // Fetch merchant information based on the provided item IDs
        $merchants = Merchant::fetchMerchantInfoByItem($itemIds);

        // Check if merchants are found
        if ($merchants) {
            
            $response['message'] = "Items Provided";
            $response['success'] = true;
            $response['merchant_info'] = [];

            // Prepare merchant data to return
            foreach ($merchants as $merchant) {
                $response['merchant_info'][$merchant->getId()] = [
                    'id' => $merchant->getId(),
                    'name' => $merchant->getName(),
                    'contact_info' => $merchant->getContactInfo(),
                    'address' => $merchant->getAddress(),
                    'merchant_loc_coor' => $merchant->getMerchantLocCoor(),
                ];
            }
        } else {
            $response['message'] = 'No merchants found for the provided item IDs.';
        }
    } catch (Exception $e) {
        // Handle any errors during the database query or processing
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

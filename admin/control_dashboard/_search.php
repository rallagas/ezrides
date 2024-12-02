<?php
include_once "../../_db.php"; // Ensure the database connection is included
include_once '../_class_grocery.php';

// Check if the search query is set
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];

    // Sanitize the input to prevent XSS
    $searchQuery = htmlspecialchars($searchQuery);

    // Prepare the SQL statement to fetch matching products
    $query = "
        SELECT gi.item_id, gi.item_name, gi.price, gi.quantity, gi.merchant_id, gm.name AS merchant_name, gi.item_img
        FROM shop_items gi
        JOIN shop_merchants gm ON gi.merchant_id = gm.merchant_id
        WHERE gi.item_name LIKE ? OR gm.name LIKE ?
    ";

    // Prepare the statement using mysqli
    if ($stmt = CONN->prepare($query)) {
        // Bind the parameters to the query
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->bind_param('ss', $searchTerm, $searchTerm); // 'ss' means two string parameters

        // Execute the statement
        $stmt->execute();

        // Get the result of the query
        $result = $stmt->get_result();

        // Prepare the HTML output
        $output = '';
        while ($row = $result->fetch_assoc()) {
            $product = new Product(
                $row['item_id'],
                $row['item_name'],
                $row['price'],
                $row['quantity'],
                $row['merchant_id'],
                $row['merchant_name'],
                $row['item_img'] // Assuming the image path is stored in item_img
            );

            // Set default image if none is provided
            $itemImg = $product->getItemImg() ?: 'default-groc.png';

            // Append the product information to the output
            $output .= '
                <div class="col-sm-4 mb-5 mb-sm-2">
                    <form action="_update_item.php" method="POST" class="formUpdateItem" enctype="multipart/form-data">
                        <div class="card">
                            <img src="../../client/_shop/item-img/' . htmlspecialchars($itemImg) . '" alt="" class="card-img-top">
                            <input type="file" class="form-control form-control-sm" name="itemNewImg">
                            <div class="card-body">
                                <input type="text" readonly class="form-control fw-bold border-0 form-editable" name="ItemName"
                                    value="' . htmlspecialchars($product->getName()) . '">

                                <p class="card-text">
                                    <input type="hidden" class="form-control" name="itemId" value="' . htmlspecialchars($product->getId()) . '">
                                    <input type="hidden" class="form-control" name="merchantId" value="' . htmlspecialchars($product->getMerchantId()) . '">
                                    <input type="text" readonly id="InputMerchant" name="MerchantName" class="form-editable form-control form-control-sm border-0 text-bg-warning" value="' . htmlspecialchars($product->getMerchantName()) . '">
                                    <div class="input-group border-0">
                                        <span class="input-group-text border-0">Php</span>
                                        <input type="text" name="itemPrice" readonly class="form-control form-editable form-control-sm border-0 ps-0" value="' . number_format($product->getPrice(), 2) . '">
                                    </div>
                                </p>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">Update</button>
                            </div>
                        </div>
                    </form>
                </div>';
        }

        // Close the statement
        $stmt->close();

        // Return the HTML output
        echo $output;
    } else {
        echo 'Error preparing the query: ' . CONN->error;
    }
}
?>

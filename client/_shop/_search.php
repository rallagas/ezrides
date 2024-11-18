<?php

include_once "../../_db.php";
include_once "_class_grocery.php"; // Update the path as necessary

$response = ["success" => false, "message" => ""];

// Check if the search query is set
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];

    // Sanitize the input
    $searchQuery = htmlspecialchars($searchQuery);

    // Prepare and execute the query using the SQL utility
    $query = "
        SELECT gi.item_id,
               gi.item_name,
               gi.price,
               gi.quantity,
               gi.merchant_id,
               gm.name AS merchant_name,
               gi.item_img
          FROM shop_items gi
          JOIN shop_merchants gm ON gi.merchant_id = gm.merchant_id
          JOIN shop_category sc ON gi.category = sc.sc_id
         WHERE (gi.item_name LIKE ? OR gm.name LIKE ?)
           AND sc.shop_category_name = 'grocery'
    ";

    // Fetch results using the `sql::query` utility function
    $params = ['%' . $searchQuery . '%', '%' . $searchQuery . '%'];
    $products = query($query, $params);

    // Prepare the HTML output
    if ($products) {
        foreach ($products as $row) {
            $product = new Product(
                $row['item_id'],
                $row['item_name'],
                $row['price'],
                $row['quantity'],
                $row['merchant_id'],
                $row['merchant_name'],
                $row['item_img']
            );
            ?>
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3 mb-sm-1">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4">
                            <img src="./_shop/item-img/<?php echo ($product->getItemImg() == NULL) ? 'default-groc.png' : $product->getItemImg(); ?>" alt="" class="img-fluid">
                        </div>
                        <div class="col-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product->getName()); ?></h5>
                                    <p class="card-text">
                                        <span class="badge rounded-pill text-bg-warning">
                                            <?php echo htmlspecialchars($product->getMerchantName()); ?>
                                        </span>
                                        Price: $<?php echo number_format($product->getPrice(), 2); ?>
                                        (<?php echo $product->getQuantity(); ?> in stock)
                                    </p>
                                    <form class="form-add-basket" id="formAddBasket" item-submit-id="<?php echo $product->getId(); ?>">
                                        <div class="input-group">
                                            <input type="hidden" name="item_id" value="<?php echo $product->getId(); ?>">
                                            <input type="text" class="form-control" name="quantity" value="1">
                                            <span class="input-group-text">pcs</span>
                                            <button type="submit" class="btn btn-success btn-sm add-basket-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5" />
                                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No products found.</p>";
    }
}
?>

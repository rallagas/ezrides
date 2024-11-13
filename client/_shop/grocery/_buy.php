<style>
    #merchantSuggestions {
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
    }

</style>



<?php
// Include your database class and the Product class

include '../_class_grocery.php';   // Ensure this points to your Product class file

// Create a new Database instance
//$db = new Database();
//$db->dbConnection(); // Assuming this method initializes the connection

$currPage="_buy";
// Fetch all products from the database
$products = Product::fetchAllProducts($db);

// Get the total number of products
$totalProducts = count($products);

// Define the number of items per page
$itemsPerPage = 15;

// Calculate the total number of pages
$totalPages = ceil($totalProducts / $itemsPerPage);

// Get the current page from the query string, default to page 1
$currentPage = isset($_GET['pagination']) ? (int)$_GET['pagination'] : 1;

// Ensure the current page is within valid bounds
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

// Calculate the starting index for the items to display
$startIndex = ($currentPage - 1) * $itemsPerPage;

// Slice the products array to get the items for the current page
$currentProducts = array_slice($products, $startIndex, $itemsPerPage);
?>


<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row mt-5">
                    <div class="col-10">
                    </div>
                    <div class="col-lg-2 col-sm-4">
                        <a class="me-3 btn btn-secondary bg-purple text-white position-relative" href="../../">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                            </svg>
                        </a>

                        <!-- Button to Show Cart Items -->
                        <button id="ShowCartItems" class="btn btn-primary position-relative" data-bs-toggle="collapse" data-bs-target="#CartItems" aria-expanded="false" aria-controls="CartItems">
                            <!-- Cart Count Badge -->
                            <span id="cartCountBadge" class="position-absolute z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <!-- Count will be inserted here -->
                            </span>

                            <!-- Cart Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg>
                        </button>

                        <button class="btn btn-warning position-relative ms-4">

                            <span class="position-absolute  z-3 top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                99+
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-12">
            <div class="collapse CartItems row gx-1 pt-4" id="CartItems">
               
                   Loading Cart Items
                    <!-- Cart items will be loaded here dynamically -->
              
            </div>

        </div>
        <div class="col-12" id="totalAmount"></div>
        <div class="col-12">
            <button class="me-3 mt-3 btn rounded-3 btn-warning btn-checkout">Checkout</button>
        </div>


        <div class="col-12">

            <!-- Modal for Checkout Summary -->
            <div id="checkoutModal" class="modal fade" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="checkoutModalLabel">Order Summary</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="userLogged" value="<?php session_start(); echo $_SESSION['user_id'];?>" class="form-control">
                            <div class="row" id="CheckOutItems"></div> <!-- Order Summary Items go here -->

                            <!-- Shipping Details -->
                            <div class="mt-3">
                                <h6>Shipping Details</h6>
                                <input type="text" id="shippingName" class="form-control mb-2" placeholder="Full Name" required>
                                <input type="text" id="shippingAddress" class="form-control mb-2" placeholder="Address" required>
                                <input type="text" id="shippingPhone" class="form-control" placeholder="Phone Number" required>
                            </div>

                            <!-- Payment Details -->
                            <div class="mt-3">
                                <h6>Payment Details</h6>
                                <input type="text" id="paymentCard" class="form-control mb-2" placeholder="Card Number" required>
                                <input type="text" id="paymentExpiry" class="form-control mb-2" placeholder="Expiry Date (MM/YY)" required>
                                <input type="text" id="paymentCVC" class="form-control" placeholder="CVC" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="placeOrderBtn" class="btn btn-success">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <form>
                <input type="text" class="rounded-4 form-control my-3" id="SearhItems" placeholder="Search Items">
            </form>
        </div>
    </div>


    <div class="row">
        <!-- Pagination Links -->

        <nav aria-label="Page navigation" class="my-2 shop-navigation">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage - 1; ?>">Previous</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage + 1; ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="row" id="searchResults">
        <?php foreach ($currentProducts as $product): ?>
        <div class="col-sm-6 col-md-6 col-lg-4 mb-3 mb-sm-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-4">
                        <img src="item-img/<?php echo ($product->getItemImg() == NULL) ? 'default-groc.png' : $product->getItemImg() ; ?>" alt="" class="img-fluid">

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
        <?php endforeach; ?>
    </div>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_buy.js"></script>

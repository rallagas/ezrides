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
// include '../_class_grocery.php';   // Ensure this points to your Product class file

// Create a new Database instance
$db = CONN;

$currPage="_items_control";
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


<div class="container" style="height:85vh">
    <div class="row">
        <div class="col-12">
            <form action="">
                <input type="text" class="rounded-4 form-control my-3" id="SearhItems" placeholder="Search Items">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12 my-1 alert txn_status">
            
        </div>
    </div>
    <div class="row">
        <!-- Pagination Links -->

        <div class="col-lg-12">

            <a class="btn btn-primary" data-bs-toggle="collapse" href="#DivNewItem" role="button" aria-expanded="false"
                aria-controls="DivNewItem"> New Item + </a>
            <div id="DivNewItem" class="collapse shadow">
                <form id="FormNewItem" enctype="multipart/form-data">
                    <div class="border border-1 p-2 my-3">
                        <h6 class="fw-bold">New Item</h6>

                            <input type="text" class="form-control mb-2" name="ItemName" Placeholder="Item Name" />
                            <input type="text" class="form-control mb-2" name="ItemPrice" Placeholder="Item Price" />
                            <input type="text" class="form-control mb-2 inputMerchant" name="MerchantName" placeholder="Search Merchant">
                            <input type="file" class="form-control mb-2" name="itemImg">
                            <button type="submit" class="btn btn-primary">Save</button>
                        <div id="merchantSuggestions" class="my-2"></div>
                    </div>
                </form>
            </div>

        </div>
        <nav aria-label="Page navigation" class="my-2">



            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage - 1; ?>">Previous</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $currPage;?>&pagination=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?php echo $currPage;?>&pagination=<?php echo $currentPage + 1; ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="row g-1 h-100 overflow-y-scroll" id="searchResults">
        <?php foreach ($currentProducts as $product): ?>

        <div class="col-sm-4 col-md-6 col-lg-3 mb-3 mb-sm-1">
            <form action="_update_item.php" method="POST" class="formUpdateItem" enctype="multipart/form-data">
                <div class="card">
                    <img src="../../client/_shop/item-img/<?php echo ($product->getItemImg() ?? "default-groc.png") ;?>" alt="" class="card-img-top">
                    <input type="file" class="form-control form-control-sm" name="itemNewImg">
                    <div class="card-body">
                        <input type="text" readonly class="form-control fw-bold border-0 form-editable" name="ItemName"
                            value="<?php echo htmlspecialchars($product->getName()); ?>">

                        <p class="card-text">
                            <input type="hidden" class="form-control" name="itemId" value="<?php echo htmlspecialchars($product->getId()); ?>">
                            <input type="hidden" class="form-control" name="merchantId" value=" <?php echo htmlspecialchars($product->getMerchantId()); ?>">
                            <input type="text" readonly id="InputMerchant" name="MerchantName" class="form-editable form-control form-control-sm border-0 text-bg-warning" value=" <?php echo htmlspecialchars($product->getMerchantName()); ?>">
                            <div class="input-group border-0">
                            <span class="input-group-text border-0">Php</span>
                            <input type="text" name="itemPrice" readonly class="form-control form-editable form-control-sm border-0 ps-0" value="<?php echo number_format($product->getPrice(), 2); ?>">
                            </div>
                        </p>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script>
$(document).ready(function() {
    // Event listener for the search input
    $('#SearhItems').on('input', function() {
        var query = $(this).val(); // Get the current value of the input

        // Send the search query to the server
        $.post('_search.php', {
            search: query
        }, function(response) {
            // Update the results container with the response
            $('#searchResults').html(response);
        });
    });
   

    

   

});


function displaySuggestions(suggestions) {
    const suggestionBox = $('#merchantSuggestions');
    suggestionBox.empty(); // Clear previous suggestions

    if (suggestions.length > 0) {
        suggestions.forEach(function (merchant) {
            const item = $('<a></a>')
                .addClass('btn btn-sm m-1 btn-outline-secondary rounded-4 small')
                .text(merchant.name)
                .on('click', function () {
                    $('.inputMerchant').val(merchant.name); // Set the selected merchant name
                    suggestionBox.hide(); // Hide the suggestion box
                });
            suggestionBox.append(item);
        });
        suggestionBox.show(); // Display the suggestions box
    } else {
        suggestionBox.hide(); // Hide if no suggestions found
    }
}

$(document).on('input', '.inputMerchant', function () {
    const query = $(this).val().trim();

    if (query.length > 1) { // Fetch suggestions after 2 characters
        $.ajax({
            url: '_search_merchants.php', // Your backend endpoint
            method: 'GET',
            data: { query: query },
            dataType: 'json', // Expect JSON response
            success: function (data) {
                if (data.error || data.message) {
                    console.error('Error:', data.error);
                    console.warn("Warning:", data.message);
                    $('#merchantSuggestions').html(data.message);
                } else if (data.merchants && data.merchants.length > 0) {
                    displaySuggestions(data.merchants);
                } else {
                    $('#merchantSuggestions').hide(); // Hide if no merchants found
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching merchant suggestions:', status, error);
                $('#merchantSuggestions').hide();
            }
        });
    } else {
        $('#merchantSuggestions').hide(); // Hide if input is too short
    }
});


$(document).on('submit', '.formUpdateItem', function (e) {
    e.preventDefault(); // Prevent default form submission

    let form = $(this)[0];
    let formData = new FormData(form); // Gather form data

    $.ajax({
        url: '_update_item.php', // Backend endpoint
        type: 'POST',
        data: formData,
        processData: false, // Important: Prevent jQuery from processing data
        contentType: false, // Important: Prevent jQuery from setting content type
        success: function (response) {
            if (response.success) {
                $(".txn_status").html('Item updated successfully!').removeClass("alert-danger").addClass("alert-success");
                setTimeout(()=>{
                    $(".txn_status").fadeOut();
                },2000);
            } else {
                $(".txn_status").html('Failed to update item: ' + (response.error || 'Unknown error')).removeClass("alert-success").addClass("alert-danger");
                setTimeout(()=>{
                    $(".txn_status").fadeOut();
                },2000);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            $(".txn_status").html('Failed to update item: ' + (error || 'Unknown error')).removeClass("alert-success").addClass("alert-danger");
            setTimeout(()=>{
                $(".txn_status").fadeOut();
                },2000);
        }
    });
});

$(document).on("dblclick", ".form-editable", function (e) {
    e.preventDefault();
    $(this).prop("readonly", false); // Enable editing for the specific element that was double-clicked
});

</script>
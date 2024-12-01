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
//include '../_class_grocery.php';   // Ensure this points to your Product class file

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


<div class="container">
  <div class="row">
    <div class="col-12">
        <form action="">
            <input type="text" class="rounded-4 form-control my-3" id="SearhItems" placeholder="Search Items">
        </form>
    </div>
  </div>
   <div class="row">
           <!-- Pagination Links -->
           
           <div class="col-12">
               
 <a class="btn btn-primary" data-bs-toggle="collapse" href="#FormNewItem" role="button" aria-expanded="false" aria-controls="FormNewItem"> New Item + </a>
                <a href="" class="btn btn-outline-primary">Filter </a>
             <div id="FormNewItem" class="collapse fade">
              <form action="">
                <div class="border border-1 p-2 my-3">
                 <h6 class="fw-bold">New Item</h6>
                  <div class="mb-1 input-group">
                      <input type="text" class="form-control" Placeholder="item name" />
<!--
                      <select name="merchant" id="selectMerchant" class="form-select">
                          <option value="">--Merchant--</option>
                      </select>
-->
                      
                      <input type="text" class="form-control" id="inputMerchant" placeholder="Search Merchant">
                      <input type="text" class="form-control" Placeholder="Initial Qty">
                      <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                  
               <div id="merchantSuggestions" class="my-2"></div>
                  </div>
              </form>
              </div>
               
           </div>
    <nav aria-label="Page navigation" class="my-2">
      
      
       
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
            <div class="col-sm-4 col-md-6 col-lg-4 mb-3 mb-sm-1">
                <div class="card">
                   <img src="" alt="" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product->getName()); ?></h5>
                        <p class="card-text">
                            <span class="badge rounded-pill text-bg-warning">
                                <?php echo htmlspecialchars($product->getMerchantName()); ?>
                            </span>
                            Price: $<?php echo number_format($product->getPrice(), 2); ?> 
                            (<?php echo $product->getQuantity(); ?> in stock)
                        </p>
                        <div class="btn-group ">
                            <a href="#" class="btn btn-danger btn-sm">Deactivate</a>
                            <a href="#" class="btn btn-warning btn-sm">Update</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    // Event listener for the search input
    $('#SearhItems').on('input', function() {
        var query = $(this).val(); // Get the current value of the input

        // Send the search query to the server
        $.post('_search.php', { search: query }, function(response) {
            // Update the results container with the response
            $('#searchResults').html(response);
        });
    });

    $('#inputMerchant').on('keydown', function () {
        
        let query = $(this).val().trim();
        if (query.length > 1) { // Start suggesting after 2 characters
            $.ajax({
                url: '_search_merchants.php', // Your backend endpoint
                method: 'GET',
                data: { query: query },
                success: function (data) {
                    let suggestions = JSON.parse(data);
                    displaySuggestions(suggestions);
                },
                error: function () {
                    $('#merchantSuggestions').hide(); // Hide suggestions if there's an error
                }
            });
        } else {
            $('#merchantSuggestions').hide(); // Hide if input is too short
        }
   
    });

    function displaySuggestions(suggestions) {
        let suggestionBox = $('div#merchantSuggestions');
        suggestionBox.empty(); // Clear previous suggestions

        if (suggestions.length > 0) {
            suggestions.forEach(function (merchant) {
                let item = $('<a></a>')
                    .addClass('btn')
                    .addClass('btn-sm')
                    .addClass('mx-1')
                    .addClass('btn-outline-secondary')
                    .text(merchant.name) // Assumes response contains 'name'
                    .on('click', function () {
                        $('#inputMerchant').val(merchant.name);
                        suggestionBox.hide();
                    });
                suggestionBox.append(item);
            });
            suggestionBox.show(); // Display the suggestions dropdown
        } else {
            suggestionBox.hide(); // Hide if no suggestions found
        }
    }
});

</script>
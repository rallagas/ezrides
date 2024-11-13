$(document).ready(function () {
    // Event listener for the search input
    $('#SearhItems').on('input', function () {
        var query = $(this).val(); // Get the current value of the input

        // Send the search query to the server
        $.post('_search.php', {
            search: query
        }, function (response) {
            // Update the results container with the response
            if (query != "") {
                $(".shop-navigation").addClass("collapse");
            } else {
                $(".shop-navigation").removeClass("collapse");
            }
            $('#searchResults').html(response);
        });
    });

    $('#inputMerchant').on('keydown', function () {

        let query = $(this).val().trim();
        if (query.length > 1) { // Start suggesting after 2 characters
            $.ajax({
                url: '_search_merchants.php', // Your backend endpoint
                method: 'GET',
                data: {
                    query: query
                },
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


    // Function to fetch and display cart items
    function loadCartItems() {
        $.ajax({
            type: "GET",
            url: "_action_fetch_cart.php", // URL to your script that fetches cart items
            dataType: "json",
            success: function (response) {
                let cartContent = "";
                let total = 0;

                // Loop through each cart item and build HTML structure
                response.cartItems.forEach(function (item) {
                    let itemImg = item.item_img == null ? "default-groc.png" : item.item_img;
                    cartContent += `
                        <div class="col-lg-3 col-sm-6 col-md-6 cart-item">
                            <div class="container-fluid p-0" cart-item-id data-id="${item.item_id}">
                                <span class="collapse">${item.item_id}</span>
                                <div class="row gx-2">
                                   <div class="col-1">
                                        <input type="checkbox" class="cart-item-checkbox form-check-input mt-3" data-price="${item.price}" data-quantity="${item.quantity}" />
                                    </div>
                                    <div class="col-4">
                                        <img class="img-fluid" src="item-img/${itemImg}" alt="${item.item_name}" />    
                                    </div>
                                    <div class="col-7">
                                        <span class="item-name">${item.item_name}</span> <br>
                                        <span class="item-price">Price: $${item.price}</span>
                                        <span class="item-qty">x ${item.quantity} pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Insert cart items into #CartItems container
                $(".CartItems").html(cartContent);

                // Attach event handler to checkboxes to update total
                $(".cart-item-checkbox").change(function () {
                    calculateTotal();
                });
            },
            error: function () {
                $(".CartItems").html("<p>Error loading cart items.</p>");
            }
        });
    }
    
    $(".CartItems").empty();
    loadCartItems();
    



    // Function to calculate the total amount based on checked items
    function calculateTotal() {
        let total = 0;
        $(".cart-item-checkbox:checked").each(function () {
            let price = $(this).data("price");
            let quantity = $(this).data("quantity");
            total += price * quantity;
        });
        if (total > 0.00) {
            $(".btn-checkout").html("Checkout (Php " + total.toFixed(2) + ")");

        }
    }

    // Handle form submission to add item to cart
    $('form#formAddBasket').submit(function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "_action_add_to_cart.php", // URL to your script that handles adding to cart
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    console.log("Item added to cart successfully!" + response.success);
                    updateCartCount(); //refresh cartCount
                    loadCartItems(); // Refresh cart items
                } else {
                    alert("Failed to add item to cart: " + response.message);
                }
            },
            error: function () {
                alert("Error adding item to cart.");
            }
        });
    });


    function updateCartCount() {
        $.ajax({
            url: "_get_cart_count.php", // Replace with the correct path to the PHP script
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let count = response.count;
                    // Limit display to "99+" if count exceeds 99
                    let displayCount = count > 99 ? "99+" : count;
                    $("#cartCountBadge").text(displayCount);
                }
            },
            error: function () {
                console.log("Error fetching cart count");
            }
        });
    }

    // Display checkout modal with cart items
    $('.btn-checkout').click(function () {
        let checkedItems = [];
        let totalAmount = 0;

        const reference_num = 'ORD-' + Date.now();

        // Gather checked cart items
        $('.cart-item-checkbox:checked').each(function () {
            let itemContainer = $(this).closest('.cart-item');

            // Retrieve individual data from the HTML structure
            let itemId = itemContainer.find('[cart-item-id]').data('id'); // Item ID
            let itemName = itemContainer.find('.item-name').text().trim(); // Item Name
            let itemPrice = parseFloat($(this).data('price')); // Price from checkbox data-price
            let itemQuantity = parseInt($(this).data('quantity')); // Quantity from checkbox data-quantity
            let itemImageSrc = itemContainer.find('img').attr('src'); // Image source

            // Calculate the total amount for this item
            let amountToPay = itemPrice * itemQuantity;

            // Output each variable to verify
            console.log("Item ID:", itemId);
            console.log("Item Name:", itemName);
            console.log("Price per Item:", itemPrice);
            console.log("Quantity:", itemQuantity);
            console.log("Image Source:", itemImageSrc);
            console.log("Amount to Pay:", amountToPay);

            totalAmount += amountToPay;

            checkedItems.push({
                item_id: itemId,
                name: itemName,
                quantity: itemQuantity,
                price: itemPrice,
                amount: amountToPay
            });
        });

        // Update total amount and item list in modal
        $('#checkout-total').text(totalAmount.toFixed(2));
        $('#CheckOutItems').empty();
        checkedItems.forEach(function (item) {
            $('#CheckOutItems').append(`
                <div class="col-12 mb-2">
                    <i>${item.name}</i> x ${item.quantity} pcs = Php ${item.amount.toFixed(2)}
                </div>
            `);
        });
        $('#CheckOutItems').append("<hr>");
        $('#CheckOutItems').append(`
                <div class="col-12 mt-2">
                    Total Amount to Pay is Php ${totalAmount.toFixed(2)}
                    <hr>
                </div>
            `);

        // Show modal
        $('#checkoutModal').modal('show');
    });

    // Place Order
    //    $('#placeOrderBtn').click(function () {
    //        let orderData = {
    //            user_id: 1,
    //            items: [],
    //            shipping: {
    //                name: $('#shippingName').val(),
    //                address: $('#shippingAddress').val(),
    //                phone: $('#shippingPhone').val()
    //            },
    //            payment: {
    //                card: $('#paymentCard').val(),
    //                expiry: $('#paymentExpiry').val(),
    //                cvc: $('#paymentCVC').val()
    //            },
    //            total_amount: $('#checkout-total').text(),
    //            reference_num: 'ORD-' + Date.now() // Generate unique order reference
    //        };
    //
    //        // Validate form inputs
    //        if (!orderData.shipping.name || !orderData.shipping.address || !orderData.shipping.phone ||
    //            !orderData.payment.card || !orderData.payment.expiry || !orderData.payment.cvc) {
    //            alert('Please fill all required fields.');
    //            return;
    //        }
    //
    //        // Gather checked cart items
    //        $('#CartItems input[type="checkbox"]:checked').each(function () {
    //            let item = $(this).closest('.cart-item');
    //            orderData.items.push({
    //                item_id: item.data('id'),
    //                quantity: item.find('.item-quantity').val()
    //            });
    //        });
    //
    //        // Send AJAX request to place the order
    //        $.ajax({
    //            url: 'place_order.php',
    //            type: 'POST',
    //            data: JSON.stringify(orderData),
    //            contentType: 'application/json',
    //            success: function (response) {
    //                if (response.success) {
    //                    alert('Order placed successfully!');
    //                    location.reload();
    //                } else {
    //                    alert('Error placing order: ' + response.message);
    //                }
    //            },
    //            error: function () {
    //                alert('An error occurred while placing the order.');
    //            }
    //        });
    //    });

    // Update cart count every 1 second (1000 ms)
    updateCartCount();

    // Load cart items initially
    //  loadCartItems();
});

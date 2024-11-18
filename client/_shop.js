function getWalletBalance() {

    $.ajax({
        url: '_shop/ajax_get_balance.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#WalletBalance').text(response.balance ? `Php ${response.balance}` : 'Error fetching balance');
        },
        error: function (xhr, status, error) {
            console.error('Error fetching balance:', error);
            $('#WalletBalance').text('An error occurred. Please try again.');
        }
    });
}


function displaySuggestions(suggestions) {
    let suggestionBox = $('div#merchantSuggestions');
    suggestionBox.empty(); // Clear previous suggestions

    if (suggestions.length > 0) {
        suggestions.forEach(function (merchant) {
            let item = $('<a></a>')
                .addClass('btn btn-sm mx-1 btn-outline-secondary')
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
        url: "_shop/_action_fetch_cart.php", // URL to your script that fetches cart items
        dataType: "json",
        success: function (response) {
            let cartContent = "";
            let total = 0;

            if (response.success && response.cartItems.length > 0) {
                $(".btn-checkout").removeClass("d-none");
                // Loop through each cart item and build HTML structure
                response.cartItems.forEach(function (item) {
                    let itemImg = item.item_img == null ? "default-groc.png" : item.item_img;
                    cartContent += `
                        <div class="col-lg-3 col-sm-6 col-md-6 cart-item">
                            <div class="container-fluid p-0" cart-item-id="${item.item_id}">
                                <span class="collapse">${item.item_id}</span>
                                <div class="row gx-2 mb-1">
                                   <div class="col-1">
                                        <input checked type="checkbox" class="cart-item-checkbox form-check-input mt-3" data-price="${item.price}" data-quantity="${item.quantity}" />
                                    </div>
                                    <div class="col-2">
                                        <img class="img-fluid" src="./_shop/item-img/${itemImg}" alt="${item.item_name}" />    
                                    </div>
                                    <div class="col-9">
                                        <span class="item-name">${item.item_name}</span> <br>
                                        <span class="item-price">Price: Php ${item.price}</span>
                                        <span class="item-qty">x ${item.quantity} pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Insert cart items into #CartItems container
                $(".CartItems").removeClass("alert alert-warning").html(cartContent);
            } else {
                //hide checkout button
                $(".btn-checkout").removeClass("d-none").addClass("d-none");

                $(".CartItems").addClass("alert alert-warning mt-2").html("<span>No Cart Items.</span>");

            }

            // Attach event handler to checkboxes to update total

        },
        error: function () {
            $(".CartItems").html("<p>Error loading cart items.</p>");
        }
    });
}


// Function to calculate the total amount based on checked items
function calculateTotal() {
    let total = 0;
    $(".cart-item-checkbox:checked").each(() => {
        let price = $(this).data("price");
        let quantity = $(this).data("quantity");
        total += price * quantity;

        if (total > 0.00) {
            $(".btn-checkout").html("Checkout (Php " + total.toFixed(2) + ")");
        }
        console.log("Total Checked: " + total);
    });
}



function updateCartCount() {
    $.ajax({
        url: "_shop/_get_cart_count.php", // Replace with the correct path to the PHP script
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


function genShopRefNum(len, prefix = "") {
    // Create an array of alphanumeric characters (A-Z, 0-9)
    const alphaNum = [...Array(26)].map((_, i) => String.fromCharCode(65 + i)) // A-Z
        .concat([...Array(10)].map((_, i) => i.toString())); // 0-9

    let key = "";

    // Loop to generate the reference number
    for (let i = 0; i < len; i++) {
        // Randomly pick from the first 26 characters (A-Z) or the last 10 characters (0-9)
        if (i % 2 === 0) {
            key += alphaNum[Math.floor(Math.random() * 26)]; // A-Z
        } else {
            key += alphaNum[Math.floor(Math.random() * 10) + 26]; // 0-9
        }
    }

    // Return the final reference number with the prefix
    return prefix + key;
}


//functions for Voucher:
// Function to fetch voucher info
function fetchVoucherInfo(voucherCode) {
    if (voucherCode.length > 2) { // Only fetch data if voucher code has at least 3 characters
        $.ajax({
            url: './_shop/_ajax_fetch_voucher_info.php', // PHP endpoint for fetching voucher info
            type: 'POST',
            data: {
                voucher_code: voucherCode
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    displayVoucherInfo(data);
                } else {
                    displayVoucherError(response.message);
                }
            },
            error: function () {
                displayVoucherError('Failed to fetch voucher information.');
            },
        });
    } else {
        clearVoucherInfo(); // Clear info when input length is less than 3
    }
}

// Function to display voucher info
// Function to display voucher info in table format
function displayVoucherInfo(data) {
    $('#voucherInfo').html(`
        <div class="table-responsive table-sm">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Code</th>
                        <td>${data.voucher_code}</td>
                    </tr>
                    <tr>
                        <th>Discount Amount</th>
                        <td id="VoucherAmount">${data.voucher_amt}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>${data.voucher_desc}</td>
                    </tr>
                    <tr>
                        <th>Valid Until</th>
                        <td>${data.voucher_valid_until}</td>
                    </tr>
                    <tr>
                        <th>Available Count</th>
                        <td>${data.voucher_avail_count}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `);
}


// Function to display error message
function displayVoucherError(message) {
    $('#voucherInfo').html(`<p>${message}</p>`);
}

// Function to clear voucher info
function clearVoucherInfo() {
    $('#voucherInfo').empty();
}

async function getDistanceAndETA(fromLat, fromLng, toLat, toLng, apiKey = 'AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI') {
    const url = `https://maps.googleapis.com/maps/api/distancematrix/json?units=metric` +
                `&origins=${fromLat},${fromLng}` +
                `&destinations=${toLat},${toLng}` +
                `&key=${apiKey}`;
    
    try {
        const response = await fetch(url, { timeout: 10000 }); // Set a 10-second timeout

        if (!response.ok) {
            throw new Error("Failed to fetch data from Google Distance Matrix API.");
        }

        const data = await response.json();

        if (!data || data.status !== 'OK') {
            throw new Error(`API Error: ${data.error_message || 'Unknown error'}`);
        }

        const element = data.rows[0]?.elements[0];
        
        if (!element || element.status !== 'OK') {
            throw new Error("Unable to calculate distance and ETA: " + element );
        }

        // Prepare the JSON encoded result with distance (float) and ETA (int)
        const result = {
            success: true,
            distanceKm: parseFloat((element.distance.value / 1000).toFixed(2)), // Convert meters to km as float
            etaMinutes: Math.round(element.duration.value / 60) // Convert seconds to minutes as int
        };

        // Return the result as a JSON string
        return JSON.stringify(result);

    } catch (error) {
        console.error("Error in getDistanceAndETA: ", error.message);

        // Return the error result as a JSON string
        const result = {
            success: false,
            distanceKm: 3, // Default fallback
            etaMinutes: 15, // Default fallback
            error: true,
            message: error.message,
        };

        return JSON.stringify(result);
    }
}



function calculateTotalAmount(subtotal, shippingFee, voucherAmt) {
    // Ensure inputs are numbers
    subtotal = parseFloat(subtotal) || 0;
    shippingFee = parseFloat(shippingFee) || 0;
    voucherAmt = parseFloat(voucherAmt) || 0;

    // Calculate the final total amount
    const totalAmountToPay = subtotal + shippingFee - voucherAmt;

    // Return the calculated total
    return totalAmountToPay;
}

function computeCostbyDistance(distanceText) {
    const distanceValue = parseFloat(distanceText); // Extract numeric value
    const currentDate = new Date();
    const currentHour = currentDate.getHours();
    let flagDownRate = 0.00; // Changed from const to let for reassignment

    // Set flag down rate based on time (6PM to 5AM)
    if (currentHour >= 18 || currentHour < 5) {
        flagDownRate = 100;
    } else {
        flagDownRate = 60;
    }

    const rateAfter3KMs = 10.00;
    const MIN_DISTANCE = 3.00;

    // Convert distance if in miles and compute the cost
    if (distanceText.includes('mi')) {
        if (distanceValue * 1.60934 > 3) {
            return ((((distanceValue * 1.60934) - MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);
        } else {
            return (((MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);
        }
    }
    // If already in kilometers, subtract minimum distance and calculate cost
    return (((distanceValue - MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);
}


function placeOrder(data, callback) {
    if (!data || typeof data !== "object") {
        console.error("Invalid data passed to placeOrder.");
        return;
    }

    $.ajax({
        url: '_shop/_ajax_place_order.php',
        method: 'POST',
        dataType: 'json',
        data: data,
        success: function (response) {
            if (callback && typeof callback === "function") {
                callback(null, response); // Pass the response to the callback
            }
        },
        error: function (xhr) {
            if (callback && typeof callback === "function") {
                callback(xhr.responseText, null); // Pass the error to the callback
            }
        }
    });
}

function fetchMerchantInfo(itemId) {
return $.ajax({
        url: './_shop/_ajax_get_merchant_info.php', // Replace with the actual server-side script URL
        method: 'POST',
        dataType: 'json',
        data: { item_id: itemId }
    })
    .then(response => {
        if (response.success) {
            return response.merchant_info; // Return the merchant info on success
            
        } else {
            throw new Error(response.message || 'Failed to fetch merchant info.');
        }
    })
    .catch(error => {
        console.error('Error fetching merchant info:', error);
        throw error; // Re-throw the error for further handling
    });
}


const LoadingIcon = `<div class="d-flex align-items-center">
                          <strong role="status">Loading...</strong>
                          <div class="spinner-border ms-auto" aria-hidden="true"></div>
                        </div>`;


$(document).ready(function () {


$('#VoucherCode').on('input', function () {
        const voucherCode = $(this).val();
        fetchVoucherInfo(voucherCode);
    });    
$('#getCurrentLocation').on('click', function () {
    try {
        // Check if the browser supports geolocation
        if (!navigator.geolocation) {
            throw new Error("Geolocation is not supported by this browser.");
        }

        // Use the geolocation API with high accuracy enabled
        navigator.geolocation.getCurrentPosition(function (position) {
            try {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                // Fill the hidden input with coordinates
                $('#AddressCoordinates').val(latitude + "," + longitude);

                // Initialize Google Maps Geocoder
                var geocoder = new google.maps.Geocoder();

                // Get the readable address using reverse geocoding
                var latlng = new google.maps.LatLng(latitude, longitude);
                geocoder.geocode({
                    'location': latlng
                }, function (results, status) {
                    try {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                // Put the readable address into the shipping address field
                                $('#shippingAddress').val(results[0].formatted_address);
                            } else {
                                throw new Error("No address found.");
                            }
                        } else {
                            throw new Error("Geocoder failed due to: " + status);
                        }
                    } catch (geocoderError) {
                        console.error("Error in Geocoding: " + geocoderError.message);
                    }
                });
            } catch (positionError) {
                console.error("Error getting position: " + positionError.message);
            }
        }, function (error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
                default:
                    alert("Geolocation error: " + error.message);
            }
        }, {
            enableHighAccuracy: true, // Prefer accurate location
            timeout: 5000, // 5 seconds timeout
            maximumAge: 0 // No cached location
        });
    } catch (browserError) {
        throw new Error(browserError.message);
    }
});

    $(".CartItems").empty();
    loadCartItems();
    updateCartCount();

    calculateTotal();
    $(".cart-item-checkbox").on("change", () => {
        calculateTotal();
    });

    // Event listener for the search input
    $('#SearhItems').on('input', function () {
        var query = $(this).val(); // Get the current value of the input

        // Send the search query to the server
        $.post('_shop/_search.php', {
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
                url: '_shop/_search_merchants.php', // Your backend endpoint
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
    // Handle form submission to add item to cart
    $('form#formAddBasket').submit(function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "_shop/_action_add_to_cart.php", // URL to your script that handles adding to cart
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

    $('.btn-checkout').click(function () {
        let checkedItems = [];
        let subtotal = 0;

        // Get payment details
        let shippingFee = parseFloat($("#ShippingFee").text().trim());
        let voucherAmt = parseFloat($("#VoucherAmount").text().trim() || 0); // Default to 0 if no voucher amount is present
        let voucherCode = $("#VoucherCode").val().trim();

        // Gather checked cart items
        $('.cart-item-checkbox:checked').each(function () {
            let itemContainer = $(this).closest('.cart-item');

            // Retrieve individual data from the HTML structure
            let itemId = itemContainer.find('[cart-item-id]').attr('cart-item-id'); // Item ID
            let merchantInfo = fetchMerchantInfo(itemId);
            
            
            let itemName = itemContainer.find('.item-name').text().trim(); // Item Name
            let itemPrice = parseFloat($(this).data('price')); // Price from checkbox data-price
            let itemQuantity = parseInt($(this).data('quantity')); // Quantity from checkbox data-quantity

            // Calculate the amount for this item
            let amountToPay = itemPrice * itemQuantity;

            // Add to subtotal
            subtotal += amountToPay;
            
            

            // Add the item to the checked items array
            checkedItems.push({
                item_id: itemId,
                name: itemName,
                quantity: itemQuantity,
                price: itemPrice,
                amount: amountToPay,
                merchantInfo : merchantInfo
            });
        });

        // Calculate the final amount
        let finalAmountToPay = calculateTotalAmount(subtotal, shippingFee, voucherAmt);

        // Update modal content
        
        $('#checkout-total').text(finalAmountToPay.toFixed(2));
        $('table#CheckOutItems').empty();
        checkedItems.forEach(function (item) {
            console.log("Creating row for " + item.item_id);
            $('table#CheckOutItems').append(`<tr class="check-out-item" checkout-item-id="${item.item_id}">
                                            <td class="ps-3">
                                                <i>${item.name}</i>
                                            </td>
                                            <td> x </td>
                                            <td> ${item.quantity} pcs </td>
                                            <td> Php ${item.amount.toFixed(2)} </td>
                                        </tr> 
                                       `);
        });
        
        
        $('table#CheckOutItems').append(`<tr class="border-0 border-top">
                                            <td colspan="3">Sub Total:</td> <td>Php ${subtotal.toFixed(2)}</td>
                                         </tr>`);

        // Update payment details in the modal
        $("#PaymentDetails #ShippingFee").text(shippingFee.toFixed(2));
        $("#PaymentDetails #VoucherAmount").text(voucherAmt.toFixed(2));
        $("#PaymentDetails #FinalAmountToPay").text(finalAmountToPay.toFixed(2));

        // Show the modal
        $('#checkoutModal').modal('show');
    });

    
    

//    $('#placeOrderBtn').on('click', function (e) {
//        $(this).html(LoadingIcon).prop("disabled");
//        e.preventDefault(); // Prevent default form submission
//        // Get user id from hidden input field
//        const userId = $('#userLogged').val();
//
//        // Gather the checkout items
//        const orderItems = [];
//        $('#CheckOutItems .check-out-item').each(function () {
//            const itemId = $(this).attr('checkout-item-id');
//            const itemName = $(this).find('i').text();
//            const quantity = parseInt($(this).find('td:nth-child(3)').text().split(' pcs')[0].trim());
//            const amount = parseFloat($(this).find('td:nth-child(4)').text().replace('Php ', '').trim());
//
//            orderItems.push({
//                itemId,
//                itemName,
//                quantity,
//                amount
//            });
//        });
//        // Submit the order via AJAX
//        $.ajax({
//            url: '_shop/_ajax_place_order.php', // PHP file to handle order placement
//            method: 'POST',
//            dataType: 'json',
//            data: {
//                user_id: userId,
//                order_items: orderItems,
//                order_ref_num: $("#shopReferenceNum").val(),
//                shipping_name: $('#shippingName').val(),
//                shipping_address: $('#shippingAddress').val(),
//                shipping_phone: $('#shippingPhone').val(),
//                address_coordinates: $('#AddressCoordinates').val(),
//                payment_mode: $('#checkWalletPaymentMode').prop('checked') ? 'wallet' : 'other',
//            },
//            success: async function (response) {
//
//                if (response.success) {
//                    console.log(response.OrderMsg);
//
//                    // If payment is using wallet
//                    if ($('#checkWalletPaymentMode').prop('checked')) {
//                        makePayment(response.totalAmountToPay, 'grocery');
//                    } else {
//                        console.log("Pending Payment: Php " + response.totalAmountToPay);
//                    }
//
//                    // Reset the form and update UI
//                    $('#formPlaceOrder')[0].reset();
//                    $('#CheckOutItems').addClass('alert alert-success mx-2').html(response.message).append(LoadingIcon); // Clear the order summary
//
//                    loadCartItems();
//                    updateCartCount();
//
//                    setTimeout(() => {
//                        $(".modal").hide();
//                        $(".modal-backdrop").hide();
//                    }, 3000);
//                } else {
//                    console.error('Failed to place the order. Please try again.', response.message + ",Booking Info:" + response.AngkasBookingInfo.form_Est_Cost + ", Booking Status: " + response.bookingStatus);
//                }
//            },
//
//            error: function (xhr, status, error) {
//                console.error('Order submission failed: ', error);
//                alert('An error occurred while placing the order.');
//            }
//        });
//    });

});

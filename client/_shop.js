'use strict';
const walletbalance = $(".walletbalance");


function displaySuggestions(suggestions) {
    var suggestionBox = $('div#merchantSuggestions');
    suggestionBox.empty(); // Clear previous suggestions

    if (suggestions.length > 0) {
        suggestions.forEach(function (merchant) {
            var item = $('<a></a>')
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
            var cartContent = "";
            var total = 0;

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
                                        <input checked type="checkbox" class="cart-item-checkbox form-check-input mt-3" data-orderid="${item.order_id}" data-price="${item.price}" data-quantity="${item.quantity}" />
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
                
<div class="card border-0 border-start border-warning shadow">
                    <div class="card-header bg-danger p-1">
                        <h5 class="fs-5 fw-bold text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gift" viewBox="0 0 16 16">
                                <path d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A3 3 0 0 1 3 2.506zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43zM9 3h2.932l.023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0zM1 4v2h6V4zm8 0v2h6V4zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5z" />
                            </svg>
                            ${data.voucher_code}
                        </h5>
                    </div>
                    <div class="card-body">
                        <h4 class="fw-bolder mb-4 fs-4">${data.voucher_desc}</h4>
  <span class="small text-end">
                        <figcaption class="blockquote-footer">
                            Valid Until <cite title="${data.voucher_avail_count}">${data.voucher_valid_until}</cite>
                        </figcaption>
                        </span>
                    </div>
                    <div class="card-footer text-end">
                        <button data-id="" class="btn btn-sm btn-danger shadow">Claim</button>
                    </div>
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

async function getDistanceAndETAProxy(fromLat, fromLng, toLat, toLng) {
    const proxyUrl = 'proxy.php'; // Update this with the actual path to your PHP script
    const apiKey = 'AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI';
    const url = `${proxyUrl}?origins=${fromLat},${fromLng}&destinations=${toLat},${toLng}&key=${apiKey}`;

    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error("Failed to fetch data from proxy.");
        }

        const data = await response.json();

        if (!data || data.status !== 'OK') {
            throw new Error(`API Error: ${data.error_message || 'Unknown error'}`);
        }

        const element = data.rows[0]?.elements[0];

        if (!element || element.status !== 'OK') {
            throw new Error("Unable to calculate distance and ETA.");
        }

        return JSON.stringify({
            success: true,
            distanceKm: parseFloat((element.distance.value / 1000).toFixed(2)), // Convert meters to km
            etaMinutes: Math.round(element.duration.value / 60) // Convert seconds to minutes
        });
    } catch (error) {
        console.error("Error in getDistanceAndETA: ", error.message);

        return JSON.stringify({
            success: false,
            distanceKm: 3, // Default fallback
            etaMinutes: 15, // Default fallback
            error: true,
            message: error.message
        });
    }
}


async function getDistanceAndETA(fromLat, fromLng, toLat, toLng, apiKey = 'AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI') {
    const url = `https://maps.googleapis.com/maps/api/distancematrix/json?units=metric` +
        `&origins=${fromLat},${fromLng}` +
        `&destinations=${toLat},${toLng}` +
        `&key=${apiKey}`;

    try {
        const response = await fetch(url, {
            timeout: 10000
        }); // Set a 10-second timeout

        if (!response.ok) {
            throw new Error("Failed to fetch data from Google Distance Matrix API.");
        }

        const data = await response.json();

        if (!data || data.status !== 'OK') {
            throw new Error(`API Error: ${data.error_message || 'Unknown error'}`);
        }

        const element = data.rows[0]?.elements[0];

        if (!element || element.status !== 'OK') {
            throw new Error("Unable to calculate distance and ETA: " + element);
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

function PlaceOrder(data) {
    return new Promise((resolve, reject) => {
        // Validate the data structure before making the request
        if (!data || typeof data !== "object") {
            reject(new Error("Invalid data passed to PlaceOrder. Ensure it is an object."));
            return;
        }

        // Log the data being sent for debugging
        console.log("Sending data to server:", data);

        // Perform the AJAX request
        $.ajax({
            url: '_shop/_ajax_place_order.php',
            type: 'POST',
            contentType: 'application/json', // Set the content type for JSON
            data: JSON.stringify(data), // Convert the data to a JSON string
            success: function (response) {
                try {
                    const result = JSON.parse(response); // Parse the JSON response
                    console.log("Response from server:", result);
                    resolve(result); // Resolve the promise with the parsed result
                } catch (err) {
                    reject(new Error("Failed to parse server response as JSON."));
                }
            },
            error: function (xhr, status, error) {
                console.error("Error in PlaceOrder:", error);
                reject(new Error(`AJAX error: ${status} - ${error}`));
            },
        });
    });
}

async function handleOrder(data) {
    try {
        const response = await PlaceOrder(data);
        console.log("Order placed successfully:", response);

        let ShopCost = parseFloat(response.AngkasBookingInfo.shop_cost, 2);
        let RideCost = parseFloat(response.AngkasBookingInfo.form_Est_Cost, 2);
        let FinalAmountToPay = ShopCost + RideCost;
        $(".order-status").addClass("alert alert-success").text(response.message);
        $(".order-details").addClass("card").html(`
                  <div class="card-header">
                    <h6 class="fw-bold">Summary</h6>
                </div>
                <div class="card-body p-1">
                    <span class="fw-bolder">Order Reference Number: </span> <br>
                    <span class="fw-light">${response.OrderRefNum}</span>
                    <br>
                    <span class="fw-bolder">Booking Reference:</span> <br>
                    <span class="fw-light">${response.AngkasBookingInfo.angkas_booking_reference}</span>
                    <br>
                    <span class="fw-bolder">Shop Cost:</span> <br>
                    <span class="fw-light">Php ${ShopCost.toFixed(2)}</span>
                    <br>
                    <span class="fw-bolder">Delivery Cost:</span> <br>
                    <span class="fw-light">Php ${RideCost.toFixed(2)}</span>

                </div>
        `);
        $('#FinalAmountToPay').text(FinalAmountToPay.toFixed(2));
        // Handle success, maybe show a confirmation message to the user
    } catch (error) {
        console.error("Error placing order:", error.message);
        // Handle error, show an error message to the user
    }
}


async function fetchMerchantInfo(itemIds) {
    console.log("Fetching merchant info for item IDs:", itemIds);

    try {
        const response = await $.ajax({
            url: './_shop/_ajax_get_merchant_info.php',
            method: 'POST',
            dataType: 'json',
            data: {
                item_ids: itemIds
            } // Sending item_ids as an array
        });

        if (response.success) {
            console.log("Merchant info fetched successfully:", response.merchant_info);
            return response; // Return the full response including merchant info
        } else {
            console.error("Failed to fetch merchant info:", response.message);
            throw new Error(response.message || 'Failed to fetch merchant info.');
        }
    } catch (error) {
        console.error("Error during fetchMerchantInfo AJAX call:", error);
        throw error; // Re-throw the error for further handling
    }
}


//async function UpdateOrderIds(OrderIds,) {
//    console.log("Fetching merchant info for item IDs:", itemIds);
//
//    try {
//        const response = await $.ajax({
//            url: './_shop/_ajax_get_merchant_info.php',
//            method: 'POST',
//            dataType: 'json',
//            data: {
//                item_ids: itemIds
//            } // Sending item_ids as an array
//        });
//
//        if (response.success) {
//            console.log("Merchant info fetched successfully:", response.merchant_info);
//            return response; // Return the full response including merchant info
//        } else {
//            console.error("Failed to fetch merchant info:", response.message);
//            throw new Error(response.message || 'Failed to fetch merchant info.');
//        }
//    } catch (error) {
//        console.error("Error during fetchMerchantInfo AJAX call:", error);
//        throw error; // Re-throw the error for further handling
//    }
//}
// Fetch wallet balance
// Refactored function to fetch wallet balance
function getWalletBalance() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'ajax_get_balance.php',
            type: 'GET',
            dataType: 'json',  // Expect JSON response
            contentType: 'application/json',
            success: function(response) {
                resolve(response);  // Resolve with the response data
            },
            error: function(xhr, status, error) {
                console.error('Error fetching wallet balance:', error);
                reject({
                    error: 'An error occurred. Please try again.'
                });
            }
        });
    });
}


// Function to fetch the balance and assign it to elements
async function fetchAndAssignWalletBalance(elements) {
    try {
        const data = await getWalletBalance(); // Get the data from getWalletBalance()

        // Check if the response contains the balance and assign it
        if (data.balance) {
            elements.html(`Php ${data.balance}`);
        } else {
            elements.text('Error fetching balance');
        }
        
        return data; // Return the full response data for further usage if needed
    } catch (error) {
        console.error('Error:', error);
        elements.text('Error fetching balance');
        return { error: error.message }; // Return an error message in case of failure
    }
}


const LoadingIcon = `<span class="spinner-border spinner-border-sm ms-auto" aria-hidden="true"></span>`;



$(document).ready(function () {


    $('#VoucherCode').on('input', function () {
        const voucherCode = $(this).val();
        fetchVoucherInfo(voucherCode);
    });
    $('#getCurrentLocation').on('click', async function () {
        const originalContent = $(this).html(); // Save the original button content
        const $button = $(this); // Cache the button for later use

        $button.prop('disabled', true); // Disable the button to prevent spam clicks
        $button.html(LoadingIcon); // Set loading icon while fetching location

        try {
            // Fetch the user's current location and update shipping address/coordinates
            await GetCurrentLocation('#shippingAddress', '#AddressCoordinates');

            // Retrieve the merchant's coordinates from the DOM
            const merchantCoordinates = $('#MerchantLocCoor').val();
            if (!merchantCoordinates) {
                throw new Error("Merchant's location coordinates are not available.");
            }
            const [merchantLat, merchantLng] = merchantCoordinates.split(',').map(coord => parseFloat(coord.trim()));

            // Retrieve the user's coordinates from the updated input
            const userCoordinates = $('#AddressCoordinates').val();
            const [userLat, userLng] = userCoordinates.split(',').map(coord => parseFloat(coord.trim()));

            // Calculate the distance and ETA between the user and merchant
            const resultJSON = await getDistanceAndETAProxy(userLat, userLng, merchantLat, merchantLng);
            const result = JSON.parse(resultJSON);

            if (!result.success) {
                throw new Error(result.message || "Unable to calculate distance and ETA.");
            }

            // Update the input fields with distance, ETA, and estimated cost
            $('#formDistanceKM').val(result.distanceKm);
            console.log("DistanceKM:", result.distanceKm);
            $('#formETA').val(result.etaMinutes);
            console.log("DistanceKM:", result.distanceKm);

            // Compute the cost based on distance
            const estimatedCost = computeCostbyDistance(`${result.distanceKm} km`);
            $('#formEstimatedCost').val(estimatedCost);
        } catch (error) {
            console.error("Error handling location and distance calculation:", error.message);
            alert('Failed to retrieve location or calculate distance.');
        } finally {
            $button.prop('disabled', false); // Re-enable the button
            $button.html(originalContent); // Restore the original button content
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

    $('.btn-checkout').click(async function () {
        $('#checkoutModal').modal('show');
        console.log("Checkout button clicked.");

        fetchAndAssignWalletBalance(walletbalance);

        let checkedItems = [];
        let itemIds = [];
        let subtotal = 0;

        // Get payment details
        let shippingFee = parseFloat($("#ShippingFee").text().trim()) || 0;
        let voucherAmt = parseFloat($("#VoucherAmount").text().trim()) || 0; // Default to 0 if invalid
        let voucherCode = $("#VoucherCode").val().trim();

        console.log("Payment details fetched: ", {
            shippingFee,
            voucherAmt,
            voucherCode,
        });

        // Gather checked cart items
        $('.cart-item-checkbox:checked').each(function () {
            console.log("Processing a checked item...");

            let itemContainer = $(this).closest('.cart-item');
            let itemId = itemContainer.find('[cart-item-id]').attr('cart-item-id'); // Item ID
            let itemName = itemContainer.find('.item-name').text().trim(); // Item Name
            let itemPrice = parseFloat($(this).data('price')) || 0; // Price from checkbox data-price
            let itemQuantity = parseInt($(this).data('quantity')) || 0; // Quantity from checkbox data-quantity
            let order_id = parseInt($(this).data('orderid')) || 0; // Quantity from checkbox data-quantity

            console.log("Item details: ", {
                itemId,
                itemName,
                itemPrice,
                itemQuantity,
                order_id
            });

            // Calculate the amount for this item
            let amountToPay = itemPrice * itemQuantity;

            // Add to subtotal
            subtotal += amountToPay;

            // Add the item to the checked items array
            checkedItems.push({
                order_id: order_id,
                item_id: itemId,
                name: itemName,
                quantity: itemQuantity,
                price: itemPrice,
                amount: amountToPay,
            });
            itemIds.push(itemId);
        });
        console.log("Subtotal calculated: ", subtotal);
        console.log("Item IDs collected: ", itemIds);

        // Fetch merchant info
        let merchantInfo = await fetchMerchantInfo(itemIds).catch((error) => {
            console.error("Error fetching merchant info: ", error);
        });

        if (merchantInfo && merchantInfo.merchant_info) {
            // Iterate over each merchant and populate the UI with their details
            Object.values(merchantInfo.merchant_info).forEach((merchant) => {
                $('#MerchantName').text(merchant.name || 'N/A');
                $('#MerchantAddress').text(merchant.address || 'N/A');
                $('#ContactInfo').text(merchant.contact_info || 'N/A');
                $('#MerchantLocCoor').val(merchant.merchant_loc_coor || 'N/A');
            });
        } else {
            console.log("No merchant info found.");
        }

        console.log("Merchant info fetched: ", merchantInfo);


        // Calculate final amount
        let finalAmountToPay = calculateTotalAmount(subtotal, shippingFee, voucherAmt);

        console.log("Final amount to pay: ", finalAmountToPay);

        // Update modal content
        $('#FinalAmountToPay').text(finalAmountToPay.toFixed(2));
        $('table#CheckOutItems').empty();

        checkedItems.forEach(function (item) {
            console.log("Creating row for item: ", item);

            let row = `<tr class="check-out-item" order-id="${item.order_id}" checkout-item-id="${item.item_id}">
                <td><i>${item.name}</i></td>
                <td> x </td>
                <td> ${item.quantity} pcs </td>
                <td> Php ${item.amount.toFixed(2)} </td>
            </tr>`;

            $('table#CheckOutItems').append(row);
        });

        console.log("Checkout modal updated with items.");
    });





    // Place Order button handler
    $('#placeOrderBtn').on('click', async function (e) {
        e.preventDefault(); // Prevent default form submission
        const $button = $(this);
        $button.html(LoadingIcon).prop("disabled", true); // Show loading state

        try {
            // Gather the user data and checkout items
            const userId = $('#userLogged').val();

            const orderItems = [];
            $('#CheckOutItems .check-out-item').each(function () {
                const itemId = $(this).attr('checkout-item-id');
                const itemName = $(this).find('i').text();
                const quantity = parseInt($(this).find('td:nth-child(3)').text().split(' pcs')[0].trim());
                const amount = parseFloat($(this).find('td:nth-child(4)').text().replace('Php ', '').trim());
                const orderId = $(this).attr('order-id');

                orderItems.push({
                    itemId: parseInt(itemId),
                    itemName: itemName,
                    quantity: quantity,
                    amount: amount,
                    orderId: parseInt(orderId),
                });
            });
            console.log("order_items", orderItems);

            const data = {
                order_items: orderItems,
                order_ref_num: $("#shopReferenceNum").val(),
                shipping_name: $('#shippingName').val(),
                shipping_address: $('#shippingAddress').val(),
                shipping_phone: $('#shippingPhone').val(),
                shipping_coordinates: $('#AddressCoordinates').val(),
                payment_mode: $('#checkWalletPaymentMode').prop('checked') ? 'wallet' : 'other',
                merchant_address: $("#MerchantAddress").text().trim(),
                merchant_loc_coor: $("#MerchantLocCoor").val(),
                estCost: $("#formEstimatedCost").val(),
                etaTime: $("#formETA").val(),
                etaDistanceKm: $("#formDistanceKM").val(),
            };

            // Call PlaceOrder and handle the response
            handleOrder(data);
            fetchAndAssignWalletBalance(walletbalance);



        } catch (error) {
            console.error("Error placing order:", error.message);
            alert(`Error: ${error.message}`);
        } finally {
            $button.html('Place Order').prop("disabled", false); // Reset the button after completion
        }
    });


});

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
async function loadCartItems() {
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
                        <div class="col-4 col-lg-2 col-sm-4 border border-0 col-md-6 cart-item position-relative">
                            <div class="container-fluid p-0" cart-item-id="${item.item_id}">
                                <div class="row gx-2 mb-1 text-light">
                                        <input checked type="checkbox" class="position-absolute top-25 start-50 translate-middle cart-item-checkbox form-check-input mt-3" data-orderid="${item.order_id}" data-price="${item.price}" data-quantity="${item.quantity}" />
                                        <button data-orderid="${item.order_id}" class="deleteCartItem text-center p-0 z-3 btn btn-sm w-25 position-absolute bottom-0 start-0"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                            </svg>
                                        </button>
                                    
                                    <div class="col-sm-12">
                                        <img class="img-fluid object-fit-cover shadow" style="height:15vh;width:100%" src="./_shop/item-img/${itemImg}" alt="${item.item_name}"/>    
                                    </div>
                                    <div class="col-sm-12 text-center">
                                        <span class="small item-name">${item.item_name}</span> 
                                        <br class="m-0 p-0">
                                        <span class="small item-price mt-0 pt-0">Php ${item.price}</span><br>
                                        <span class="small item-qty float-end">${item.quantity} pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Insert cart items into #CartItems container
                $(".CartItems").html(cartContent);
            } else {
                //hide checkout button
                $(".btn-checkout").removeClass("d-none").addClass("d-none");
            }
        },
        error: function () {
            console.error("Error loading Cart");
        }
    });
}

function deleteCartItems(orderIds) {
    if (!Array.isArray(orderIds) || orderIds.length === 0) {
        console.error("Invalid Order IDs provided.");
        return;
    }
    $.ajax({
        url: "ajax_delete_cart_item.php",
        method: "POST",
        data: { orderIds: orderIds },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                // Remove the corresponding cart items from the display
                orderIds.forEach(function (orderId) {
                    $(`.cart-item button[data-Orderid='${orderId}']`).closest(".cart-item").remove();
                });
                console.log("Order Id to delete: ",orderId);
            } else {
                console.error("Failed to remove items from the cart: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error in AJAX request:", status, error);
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
    const apiKey = 'AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs';
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


// async function getDistanceAndETA(fromLat, fromLng, toLat, toLng, apiKey = 'AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs') {
//     const url = `https://maps.googleapis.com/maps/api/distancematrix/json?units=metric` +
//         `&origins=${fromLat},${fromLng}` +
//         `&destinations=${toLat},${toLng}` +
//         `&key=${apiKey}`;

//     try {
//         const response = await fetch(url, {
//             timeout: 10000
//         }); // Set a 10-second timeout

//         if (!response.ok) {
//             throw new Error("Failed to fetch data from Google Distance Matrix API.");
//         }

//         const data = await response.json();

//         if (!data || data.status !== 'OK') {
//             throw new Error(`API Error: ${data.error_message || 'Unknown error'}`);
//         }

//         const element = data.rows[0]?.elements[0];

//         if (!element || element.status !== 'OK') {
//             throw new Error("Unable to calculate distance and ETA: " + element);
//         }

//         // Prepare the JSON encoded result with distance (float) and ETA (int)
//         const result = {
//             success: true,
//             distanceKm: parseFloat((element.distance.value / 1000).toFixed(2)), // Convert meters to km as float
//             etaMinutes: Math.round(element.duration.value / 60) // Convert seconds to minutes as int
//         };

//         // Return the result as a JSON string
//         return JSON.stringify(result);

//     } catch (error) {
//         console.error("Error in getDistanceAndETA: ", error.message);

//         // Return the error result as a JSON string
//         const result = {
//             success: false,
//             distanceKm: 3, // Default fallback
//             etaMinutes: 15, // Default fallback
//             error: true,
//             message: error.message,
//         };

//         return JSON.stringify(result);
//     }
// }



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
        } 
        else {
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
        const response = await PlaceOrder(data); // Place the orderor
        const wallet = await getWalletBalance(); // Get wallet balance
        console.log("Order placed successfully:", response);

        // Extract costs and calculate the final amount to pay
        let OrderRefNum = response.OrderRefNum;
        let BookingRefNum = response.AngkasBookingInfo.angkas_booking_reference;
        let ShopCost = parseFloat(response.AngkasBookingInfo.shop_cost, 2);
        let RideCost = parseFloat(response.AngkasBookingInfo.form_Est_Cost, 2);
        let FinalAmountToPay = ShopCost + RideCost;
        

        // Update the order status UI
        $(".order-status").addClass("alert alert-success border-start-4 border-success p-3")
            .html(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill me-3" viewBox="0 0 16 16">
                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                </svg><small class='small ms-2'>${response.message}</small>`);

        // Update the order details UI
        $(".order-details").addClass("card").html(`
                <div class="card-body p-2">
                    <span class="small fw-bolder">Order Reference Number: </span>
                    <span id="FinalOrderRefNum" class="small fw-light">${OrderRefNum}</span>
                    <br>
                    <span class="small fw-bolder">Booking Reference:</span> 
                    <span class="small fw-light">${BookingRefNum}</span>
                    <br>
                    <span class="small fw-bolder">Shop Cost: Php</span> 
                    <span id="FinalShopCost" class="small fw-light"> ${ShopCost.toFixed(2)}</span>
                    <br>
                    <span class="small fw-bolder">Delivery Cost: Php</span> 
                    <span  id="FinalDeliveryFee" class="small fw-bold">${RideCost.toFixed(2)}</span>
                </div>
        `);

        $(".PayNowBtn").attr("data-payshopcost",ShopCost);
        $(".PayNowBtn").attr("data-paydeliveryfee",RideCost);
        $(".PayNowBtn").attr("data-orderrefnum",OrderRefNum);
        $(".PayNowBtn").attr("data-bookingrefnum",BookingRefNum);

        // Check if wallet balance is sufficient
        const walletBalance = wallet.balance;
        
        if (walletBalance < FinalAmountToPay) {
        console.log( "Compare : Wallet Balance" ,walletBalance, "Final Amount To Pay;", FinalAmountToPay );
            // If insufficient, uncheck the wallet payment checkbox
            $("#checkWalletPaymentMode").prop('checked', false).prop('disabled', true);
            $("label[for=checkWalletPaymentMode]>span")
                .removeClass("bg-purple")
                .addClass("text-bg-danger")  
                .text("Insufficient Balance. Top-Up and Pay Later.");
            $(".PayNowBtn").prop('disabled',true);
        } else {
            // Otherwise, ensure it's checked if the balance is sufficient
            $("#checkWalletPaymentMode").prop('checked', true);
        }

        // Update the Final Amount to Pay display
        $('#FinalAmountToPay').text(FinalAmountToPay.toFixed(2));
        $('button.FinalAmountToPay').text("Pay ");

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
async function fetchShopList(view_type) {
    try {
        const response = await $.ajax({
            url: "_shop_load_hist_content.php",
            type: "POST",
            data: { view_type },
            dataType: "json"
        });

        if (response.success) {
            return response.data; // Return the data array on success
        } else {
            throw new Error(response.error || "Unknown error occurred");
        }
    } catch (error) {
        console.error("Error fetching shop list:", error.message);
        throw error; // Re-throw the error for the caller to handle
    }
}

async function renderShopList(shopList, containerElement) {
    const container = document.getElementById(containerElement);
    container.innerHTML = ""; // Clear existing content
    
    if (shopList.length === 0) {
        container.innerHTML = `<div class="alert alert-info">No shop orders found.</div>`;
        return;
    }

    for (const shop of shopList) {
        // Create card structure
        let payButton=`
                <div class="card-footer">
                    <a data-payshopcost="${shop.shop_cost}"
                    data-paydeliveryfee="${shop.angkas_booking_estimated_cost}"
                    data-orderrefnum="${shop.shop_order_reference_number}"
                    data-bookingrefnum="${shop.angkas_booking_reference}"
                   class="btn btn-danger PayNowBtn">pay</a>
                </div>`;
        
        let RiderInfo = `
                <div class="card-footer">
                   <span class="badge text-bg-secondary">With Rider</span>
                   <span class="badge text-bg-secondary">${shop.rider_name}</span>
                </div>`;
        let ControlFooter = "";
        let ShopPaymentStatus = null;
        switch(shop.shop_payment_status){
            case 'C': ShopPaymentStatus = "Paid";
               break;
            case 'P': ShopPaymentStatus = "Pending Payment";
            ControlFooter = payButton;
               break;
            case 'D': ShopPaymentStatus = "Declined";
            ControlFooter = payButton;
               break;
            default: ShopPaymentStatus = "Pending Payment";
            ControlFooter = payButton;
        }

        if(shop.rider_user_id !== null){
            ControlFooter = RiderInfo;
        }
        

        let card = `
        <div class="card bg-light mb-1">
            <div class="card-header">
                <h5 class="card-title float-start">${shop.shop_order_reference_number}</h5>
                <small class="small float-end">${shop.order_date} (${shop.elapsed_time})</small>
            </div>
             <div class="card-body">
                <div class="container-fluid">
                    <div class="row gx-0">
                        <div class="col-12">
                            <p class="card-text">
                                <strong>Shop Payment Status:</strong> ${ShopPaymentStatus || "N/A"}<br>
                                <strong>Total Amount:</strong> Php ${shop.shop_cost || "0.00"}<br>
                                <strong>Booking Reference:</strong> ${shop.angkas_booking_reference || "Not yet Booked"}
                            </p>
                        </div>
                        <div class="col-12 shop-items-content" id="${shop.shop_order_reference_number}"></div>
                    </div>
                </div>
            </div>` +
            ControlFooter +
            `</div>
        `;

        // Insert card into container
        container.insertAdjacentHTML('beforeend', card);

        // Fetch items for this shop order reference number and render the table
        const itemData = await loadItemFromReference(shop.shop_order_reference_number);
        if (itemData) {
            const tableHTML = generateItemTable(itemData);
            // Insert table into the corresponding shop order content
            document.getElementById(shop.shop_order_reference_number).innerHTML = tableHTML;
        }
    }
}

// Generates a table of items for a given shop order reference
function generateItemTable(items) {
    let tableHTML = `
        <table class="table table-hover table-responsive">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
    `;

    // Loop through each item and generate table rows
    items.forEach(item => {
        const amount = (parseFloat(item.price) * parseInt(item.quantity)).toFixed(2); // Calculate amount
        tableHTML += `
            <tr>
                <td>${item.item_name}</td>
                <td>${parseFloat(item.price).toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>${amount}</td>
            </tr>
        `;
    });

    tableHTML += `</tbody></table>`;
    return tableHTML;
}

// Refactor loadItemFromReference to return a promise
async function loadItemFromReference(refNum) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '_shop_load_item_from_reference.php',
            type: 'POST',
            data: { ref_num: refNum },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    resolve(response.data); // Resolve the promise with the data
                } else {
                    reject('Error loading data: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                reject("Request failed: " + error);
            }
        });
    });
}
async function validateRefNum(shopOrderRefNum) {
    return new Promise((resolve, reject) => {
        if (!shopOrderRefNum) {
            reject("Shop Order Reference Number is required.");
            return;
        }

        $.ajax({
            url: "ajax_validate_ref.php", // Adjust PHP script path
            type: "POST",
            data: { shop_order_ref_num: shopOrderRefNum },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    resolve(response); // Response includes 'exist' and 'table'
                } else {
                    reject(response.error || "Validation failed.");
                }
            },
            error: function (xhr, status, error) {
                reject(`AJAX request failed: ${error}`);
            }
        });
    });
}


// async function shopItemsBaseFromReference(refNum) {
//     // Ensure that data is an array
//     const data = await loadItemFromReference(refNum);
//     if (!Array.isArray(data) || data.length === 0) {
//         alert('No items found.');
//         return;
//     }

//     // Create table HTML structure
//     let tableHTML = `
//         <table class="table table-hover">
//             <thead>
//                 <tr>
//                     <th>Item Name</th>
//                     <th>Price</th>
//                     <th>Quantity</th>
//                     <th>Amount</th>
//                 </tr>
//             </thead>
//             <tbody>
//     `;

//     // Loop through each item in the data array to populate the rows
//     data.forEach(item => {
//         // Calculate the amount (price * quantity)
//         const amount = (item.price * item.quantity).toFixed(2);

//         // Add row to the table
//         tableHTML += `
//             <tr>
//                 <td>${item.item_name}</td>
//                 <td>${item.price.toFixed(2)}</td>
//                 <td>${item.quantity}</td>
//                 <td>${amount}</td>
//             </tr>
//         `;
//     });

//     // Close the table body and table tags
//     tableHTML += `</tbody></table>`;

//     // Insert the table HTML into the DOM
//     const tableContainer = document.getElementById('tableContainer'); // Assuming an element with id "tableContainer"
//     // Update the DOM with the table
// }


//----------------



$(document).on("click", "button#ShowCartItems", function () {
   let data = null;
   updateCartCount(); //refresh cartCount
   loadCartItems(); // Refresh cart items

});
$(document).on("click", "button.deleteCartItem", function () {
    const orderId = $(this).data("orderid"); // Get the order ID from the button's data attribute
    deleteCartItems([orderId]); // Call the function with the order ID
    updateCartCount();
});

$(document).ready(function() {
        // Event listener for 'ShowOrderHistory' button
        $(".ShowOrderHistory").on("click", async function (e) {
            e.preventDefault(); // Prevent default action
            
            try {
                let viewType = 4; // Example: Get all shop orders with no booking reference
                let shopList = await fetchShopList(viewType);
                console.log("Shop list fetched successfully:", shopList);
                renderShopList(shopList,'shop-list-pane');


                 viewType = 2; // Example: Get all shop orders with no booking reference
                 shopList = await fetchShopList(viewType);
                console.log("Shop list fetched successfully:", shopList);
                renderShopList(shopList,'paid-no-driver-pane');


                viewType = 3; // Example: Get all shop orders with no booking reference
                shopList = await fetchShopList(viewType);
               console.log("Shop list fetched successfully:", shopList);
               renderShopList(shopList,'paid-with-driver-pane');
    
                // Toggle visibility of buttons and views
                $(this).addClass("d-none"); // Hide "ShowOrderHistory" button
                $(".HideOrderHistory").removeClass("d-none"); // Show "HideOrderHistory" button
                $("#MainShop").addClass("d-none"); // Hide "MainShop"
                $("#shopHistory").removeClass("d-none"); // Show "shopHistory"
            } catch (error) {
                console.error("Failed to load shop list:", error.message);
            }
        });
    
        // Event listener for 'HideOrderHistory' button
        $(".HideOrderHistory").on("click", function () {
            $(this).addClass("d-none"); // Hide "HideOrderHistory" button
            $(".ShowOrderHistory").removeClass("d-none"); // Show "ShowOrderHistory" button
            $("#shopHistory").addClass("d-none"); // Hide "shopHistory"
            $("#MainShop").removeClass("d-none"); // Show "MainShop"
        });
    
    

    // Usage example with async/await


    
    const walletbalance = $(".walletbalance");

 
    updateCartCount();
    calculateTotal();
    $(".cart-item-checkbox").on("change", () => {
        calculateTotal();
    });
    $('#VoucherCode').on('input', function () {
        const voucherCode = $(this).val();
        fetchVoucherInfo(voucherCode);
    });

    $('#getCurrentLocation').on('click', async function () {
        const originalContent = $(this).html(); // Save the original button content
        const checkIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                          <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                        </svg>`;
        const $button = $(this); // Cache the button for later use
        const $shippingAddress = $("#shippingAddress");

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
            console.log("ETA:", result.etaMinutes);

            // Compute the cost based on distance
            const estimatedCost = computeCostbyDistance(`${result.distanceKm} km`);
            $('#formEstimatedCost').val(estimatedCost);
        } catch (error) {
            console.error("Error handling location and distance calculation:", error.message);
            alert('Failed to retrieve location or calculate distance.');
        } finally {
            //$button.prop('disabled', false); // Re-enable the button
            $button.html(checkIcon).removeClass("border-warning btn-warning").addClass("border-success btn-success text-light"); // Restore the original button content
            $shippingAddress.removeClass("border-warning").addClass("border-success");
        }
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
    // $('#inputMerchant').on('keydown', function () {

    //     let query = $(this).val().trim();
    //     if (query.length > 1) { // Start suggesting after 2 characters
    //         $.ajax({
    //             url: '_shop/_search_merchants.php', // Your backend endpoint
    //             method: 'GET',
    //             data: {
    //                 query: query
    //             },
    //             success: function (data) {
    //                 let suggestions = JSON.parse(data);
    //                 displaySuggestions(suggestions);
    //             },
    //             error: function () {
    //                 $('#merchantSuggestions').hide(); // Hide suggestions if there's an error
    //             }
    //         });
    //     } else {
    //         $('#merchantSuggestions').hide(); // Hide if input is too short
    //     }

    // });
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
// Function to generate a unique ShopOrderRefNum
async function generateUniqueShopOrderRefNum() {
    let shopOrderRefNum = null;
    do {
        shopOrderRefNum = genBookRefNum(8, "SHOP");
        const validationResult = await validateRefNum(shopOrderRefNum);
        if (!validationResult.exist) break;
    } while (true);
    return shopOrderRefNum;
}

async function reassignShopOrderNum () {
    console.log("Checkout modal shown.");
    // Generate and assign a unique ShopOrderRefNum
    let shopOrderRefNum = await generateUniqueShopOrderRefNum();
    $("#shopReferenceNum").val(shopOrderRefNum);
    // Set the generated reference number in the modal input field
}
// Event listener for the checkout button
$('.btn-checkout').click(async function () {
    $('#checkoutModal').modal('show');
    // Fetch and assign wallet balance
    fetchAndAssignWalletBalance(walletbalance);
    reassignShopOrderNum();

    // Initialize variables
    let checkedItems = [];
    let itemIds = [];
    let subtotal = 0;

    // Get payment details
    let shippingFee = parseFloat($("#ShippingFee").text().trim()) || 0;
    let voucherAmt = parseFloat($("#VoucherAmount").text().trim()) || 0;
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
        let itemId = itemContainer.find('[cart-item-id]').attr('cart-item-id');
        let itemName = itemContainer.find('.item-name').text().trim();
        let itemPrice = parseFloat($(this).data('price')) || 0;
        let itemQuantity = parseInt($(this).data('quantity')) || 0;
        let order_id = parseInt($(this).data('orderid')) || 0;

        console.log("Item details: ", {
            itemId,
            itemName,
            itemPrice,
            itemQuantity,
            order_id,
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
        isAddressNull = ($('#shippingAddress').val() == "");
        if(isAddressNull){
            $('#getCurrentLocation').trigger('click');
        }
        $(".PayNowBtn").prop("disabled",true).html(LoadingIcon + " Placing the Order.");
        setTimeout(()=>{
            try {
                // Gather the user data and checkout items
                const userId = $('#userLogged').val();
               
                if (!userId) {
                    $(this).text("User not logged in.").addClass("btn-danger");
                    throw new Error("User is not logged in.");
                }
        
                const orderItems = [];
                $('#CheckOutItems .check-out-item').each(function () {
                    const itemId = $(this).attr('checkout-item-id');
                    const itemName = $(this).find('i').text();
                    const quantity = parseInt($(this).find('td:nth-child(3)').text().split(' pcs')[0].trim());
                    const amount = parseFloat($(this).find('td:nth-child(4)').text().replace('Php ', '').trim());
                    const orderId = $(this).attr('order-id');
        
                    if (!itemId || !quantity || !amount || !orderId) {
                        throw new Error("Some item details are missing.");
                    }
        
                    orderItems.push({
                        itemId: parseInt(itemId),
                        itemName: itemName,
                        quantity: quantity,
                        amount: amount,
                        orderId: parseInt(orderId),
                    });
                });
        
                if (orderItems.length === 0) throw new Error("No items in the order.");
        
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
                    handleOrder(data); //places the 
                    fetchAndAssignWalletBalance(walletbalance);
                    updateCartCount();
                    $(".PayNowBtn").prop("disabled",false).html("Pay Now");
            } catch (error) {
                console.error("Error placing order:", error.message);
                alert(`${error.message}`);
            } finally {
                $button.html('Place Order').prop("disabled", false); // Reset the button after completion
            }
        }
        , (isAddressNull ? 4000 : 1000) );
 
    });
    
});


$(document).on("click","#PayNowBtn, .PayNowBtn", function (e) {
    e.preventDefault();
    
    // Get values from attributes or fallback to element text
    let FShopCost = $(this).attr('data-payshopcost') || $("#FinalShopCost").text().trim();
    let FDeliveryFee = $(this).attr('data-paydeliveryfee') || parseFloat($("#FinalDeliveryFee").text().trim());
    let FOrderRefNum = $(this).attr('data-orderrefnum') || $("#FinalOrderRefNum").text().trim();
    let FBookingRefNum = $(this).attr('data-bookingrefnum');

    // Ensure numeric values for FShopCost and FDeliveryFee
    FShopCost = parseFloat(FShopCost) || 0;
    FDeliveryFee = parseFloat(FDeliveryFee) || 0;

    // Perform payments
    if(FShopCost !== null && FOrderRefNum !== null){
        makePayment(FShopCost, null, FOrderRefNum, FOrderRefNum, 'S'); // Pay shop cost
        updatePaymentStatus(FOrderRefNum, 'C','S');
    }
    
    if(FDeliveryFee != null && FBookingRefNum !== null){
        makePayment(FDeliveryFee, null, null, FBookingRefNum, 'R');      // Pay delivery fee
        updatePaymentStatus(FBookingRefNum, 'C','A');
    }
    $(this).html(LoadingIcon);
    setTimeout(()=>{
        $(this).prop("disabled", true);
        
        if("form".length > 0){
            $("form").trigger("reset");
        }
    },1000);

    
    $("button.btn-success").removeClass("btn-success").addClass("btn-warning").prop("disabled",false);
    $("input.border-success").removeClass("border-success").addClass("border-warning");

    // Close modal after 10 seconds
    setTimeout(() => {
        $(".btn-close").trigger("click");
    }, 10000);
});
// Global variables
let currentPage = 1;
const pageSize = 5;
let transactions = [];
let lastBookingStatus = null; // Store the last known value
let debounceTimeout = null;
const LoadingIcon = `<span class="spinner-border spinner-border-sm ms-auto" aria-hidden="true"></span>`;
const CreateHtml = {
    loadingGrower : `<div class="spinner-grow text-danger spinner-grow-sm" role="status"></div><div class="spinner-grow text-danger spinner-grow-sm" role="status"></div><div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>`
}
const chkIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg>`;

const elements = {
    orderStatus : $(".order-status"),
    rideInfoContainer : $(".rideInfoContainer"),
    btnRideInfo : $(".btnRideInfo"),
    commutesInfo : $('.commutes-info'),
    formFindAngkas: $('#formFindAngkas'),
    transactionHistoryTable: $('#transactionHistoryTable'),
    walletBalance: $('.walletbalance'),
    bookingReferenceNumber: $('#BookingReferenceNumber'),
    myRatingCustomFeedback: $('#myRatingCustomFeedback'),
    myRatingCustomFeedbackEnd: $('#myRatingCustomFeedbackEnd'),
    btnPayRider: $('#btnPayRider'),
    findMeARiderBTN: $('#findMeARiderBTN'),
    bookingHistoryContent: $('#BookingHistoryContent'),
    bookingInfoTable : $('#bookingInfoTable'),
    rentalAlert: $('#RentalAlert'),
    topUpForm: $('#topUpForm'),
    topUpAmount: $('#topUpAmount'),
    topUpModal: $('#topUpModal'),
//    userLogOut: $('#userLogOut'),
    pagination: $('#pagination'),
    transactionStatus: $('#TransactionStatus'),
    appMenuBtn: $('#appMenuBtn'),
    loadBookingHistory: $('#LoadBookingHistory'),
    bookingDetails: $('#BookingDetails'),
    bookedElapseTime: $('#BookedElaseTime'),
    rideEstCost: $('#RideEstCost'),
    paymentStatus: $('#paymentStatus'),
    customerOrigin: $('#CustomerOrigin'),
    customerDestination: $('#CustomerDestination'),
    riderInfoBookingStatus: $('#riderInfoBookingStatus'),
    riderInfoPI: $('#riderInfoPI'),
    addDestinationButton : $(".add-destination-button"),
};

$(document).on('click','#userLogOut',function(e){
    e.preventDefault;
    console.log("Logout button clicked");
    $.ajax({
        url: '../_action_logout_user.php',
        type: 'POST',
        success: function (response) {
            console.log('Session ended successfully:', response);

            $('body').html(LoadingIcon + " Heading Out so Soon?").addClass('text-center mt-5');
            setTimeout(() => {
                    window.location.href = '../index.php?page=login';
            }, 3000);
            
        },
        error: function (xhr, status, error) {
            console.error('Failed to end session:', error);
            console.error('An error occurred while trying to log you out. Please try again.');
        }
    });
});

function clog(logMsg) {
    console.log(logMsg);
}
function updateRatingInDatabase(bookingRefNum, ratingValue) {
    $.ajax({
        url: 'ajax_update_rating.php', // Backend URL for updating the rating
        type: 'POST',
        data: {
            booking_reference: bookingRefNum,
            rating: ratingValue
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                console.log("Rating updated successfully");
            } else {
                console.warn("Warning on updating rating:", response.message);
            }
        },
        error: function (xhr, status, error) {
            console.warn("AJAX error: " + error);
        }
    });
}
// Checks if multiple elements are loaded
function isElementLoaded(...selectors) {
    return selectors.every(selector => {
        const exists = document.querySelector(selector) !== null;
        //        console.log(`${selector} ${exists ? 'has' : 'has not'} been loaded`);
        return exists;
    });
}
// Load transaction history
function loadTransactionHistory() {
    $.ajax({
        url: 'ajax_fetch_wallet_transactions.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            transactions = response;
            renderTransactions();
            renderPagination();
        },
        error: function (xhr, status, error) {
            console.error('Failed to load transaction history:', error);
        }
    });
}
// Render transactions for the current page
function renderTransactions() {
    if (isElementLoaded('#transactionHistoryTable')) {
        const tbody = $('#transactionHistoryTable tbody');
        tbody.empty();

        const pageTransactions = transactions.slice((currentPage - 1) * pageSize, currentPage * pageSize);
        pageTransactions.forEach(transaction => {
            tbody.append(`
                <tr>
                    <td>${transaction.amount}</td>
                    <td>${transaction.status}</td>
                    <td>${transaction.date}</td>
                    <td>${transaction.wallet_txn_status}</td>
                </tr>
            `);
        });
    }
}
// Render pagination controls
function renderPagination() {
    const totalPages = Math.ceil(transactions.length / pageSize);
    const paginationContainer = $('#pagination');
    paginationContainer.empty();

    if (currentPage > 1) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage - 1})">Previous</button>`);
    }

    for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        paginationContainer.append(`<button class="btn btn-secondary ${activeClass}" onclick="changePage(${i})">${i}</button>`);
    }

    if (currentPage < totalPages) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage + 1})">Next</button>`);
    }
}
// Change page and re-render data for wallet transactions
function changePage(page) {
    if (page >= 1 && page <= Math.ceil(transactions.length / pageSize)) {
        currentPage = page;
        renderTransactions();
        renderPagination();
    }
}
// Make a payment
function makePayment(estimatedCost, payFrom = null, payTo = null, referenceNum = null, paymentType = null, action = null) {
    const walletBalanceElement = elements.walletBalance;
    const triggerElement = $(this); // Capture the element that triggers this function

    $.ajax({
        url: 'ajax_make_payment.php',
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            amount: estimatedCost,
            payToUser: payTo,
            payFromUser: payFrom,
            refNum: referenceNum,
            paymentType: paymentType,
            wallet_action: action
        }),
        contentType: 'application/json',
        success: function (response) {
            if (response.success) {
                console.log("Payment Successful.");
                elements.orderStatus.append("<br>Paid Php " + parseFloat(response.amount).toFixed(2) + " for " + referenceNum + "<br>");
                triggerElement.prop('disabled', true); // Disable the triggering element
                
                fetchAndAssignWalletBalance(walletBalanceElement);
            } else {
                elements.orderStatus
                    .removeClass("alert-success")
                    .addClass("alert-danger")
                    .text("Payment Failed.");
            }
        },
        error: function (xhr, status, error) {
            console.error('Payment error:', error);
        }
    });
}

// Refactored function to fetch wallet balance
function getWalletBalance() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'ajax_get_balance.php',
            type: 'GET',
            dataType: 'json',  // Expect JSON response
            contentType: 'application/json',
            success: function (response) {
                resolve(response);  // Resolve with the response data
            },
            error: function (xhr, status, error) {
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
        const data = await getWalletBalance(); // Get wallet balance data
        if (data && data.balance) {
            const formattedBalance = `Php ${data.balance}`;

            // Ensure `elements` is a jQuery object
            $(elements).each(function () {
                const element = $(this);

                // Check if the element is an input or textarea (elements that use val())
                if (element.is("input, textarea")) {
                    element.val(formattedBalance);
                } 
                // Otherwise, use text() for other elements
                else {
                    element.text(formattedBalance);
                }
            });

            console.log("Wallet balance assigned successfully:", formattedBalance);
        } else {
            console.warn("Warning: Cannot fetch wallet balance or balance is missing.");
        }

        return data; // Return the full response data for further usage if needed
    } catch (error) {
        console.error("Error:", error);
        return { error: error.message }; // Return an error message in case of failure
    }
}




function updatePaymentStatus(bookingReference, newStatus, txn_type) { //for Rider Angkas Transactions
    $.ajax({
        type: "POST",
        url: "ajax_update_payment_status.php", // Replace with the actual URL of your PHP endpoint
        data: {
            bookingReference: bookingReference,
            newStatus: newStatus,
            txnType: txn_type
        },
        success: function (response) {
            // Assuming the PHP endpoint returns a JSON object with success and message
            if (response.success) {
                console.log("Payment status updated successfully.");
                // alert("Payment status updated successfully.");
                // Optionally, you could update the UI here to reflect the new status
            } else {
                console.error("Failed to update payment status: " + response.message);
                //alert("Failed to update payment status: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("An error occurred: " + error);
            //alert("An error occurred: " + error);
        }
    });
}

function updateShopPaymentStatus(shopReferenceNum, status) {
    $.ajax({
        url: 'ajax_update_shop_payment_status.php',
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            refNum: shopReferenceNum,
            paymentStatus: status
        }),
        contentType: 'application/json',
        success: function (response) {
            if (response.success) {
                console.log("Payment status updated successfully.");
            } else {
                console.error("Failed to update payment status:", response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error updating payment status:", error);
        }
    });
}

// Check booking status
function chkBookingStatus() {
    // Check if the necessary elements are loaded and have valid text content
    if (isElementLoaded('#riderInfoBookingStatus') && isElementLoaded('#BookingReferenceNumber')) {
        const bookingRef = $('#BookingReferenceNumber').text().trim();
        const walletBalance = parseFloat(elements.walletBalance.text().replace('Php ', '').replace(',', '').trim());
        // Ensure booking reference number is valid
        if (!bookingRef) {
            console.warn("Booking reference number is not loaded or invalid (empty text). Skipping status check.");
            return;
        }

        $.ajax({
            url: 'ajax_get_booking_status.php',
            type: 'POST',
            data: {
                action: 'fetch_booking_status',
                bookingId: bookingRef
            },
            dataType: 'json',
            success: function (response) {
                // Check if the response contains the booking data
                if (response.booking && typeof response.booking === 'object') {
                    const bookingStatusText = response.booking.booking_status_text;
                    const paymentStatusText = response.booking.payment_status_text;
                    const EstCost = response.booking.form_Est_Cost;
                    // Update booking status
                    $('#riderInfoBookingStatus').html(
                        bookingStatusText === 'Waiting for Driver' ? CreateHtml.loadingGrower :
                            bookingStatusText
                    );
                    console.log("Start count down to update booking info.");
                    setTimeout(() => {
                        chkBooking(elements)
                    }, 5000);
                    // Show payment button if not paid
                    if (paymentStatusText !== 'Paid' && bookingStatusText !== 'Waiting for Driver') {
                        $('#btnPayRider').removeClass('d-none');
                        console.log("Show Payment Button.");
                    }
                    //disable if wallet balance is below cost
                    if (EstCost > walletBalance) {
                        $('#btnPayRider').prop('disabled',true);
                    
                    }
                    else{
                        $('#btnPayRider').prop('disabled',false);
                    }


                } else {
                    // Handle the case where the response doesn't contain booking data
                    console.warn('Invalid response: Booking data not found.');
                    $('#riderInfoBookingStatus').text('Error: Booking data not found.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching booking status:', error);
                $('#riderInfoBookingStatus').text('Error fetching booking status');
            }

        });
    } else {
        console.warn("Necessary elements are not fully loaded or don't have valid text content. Skipping status check.");
    }
}
function chkBooking() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax_get_current_booking.php",
            dataType: "json",
            success: function (data) {
                if (data.hasBooking) {
                    const booking = data.booking;
                    const currentDate = new Date();
                    const dateBooked = new Date(booking.date_booked);
                    const elapsedTimeInMinutes = Math.floor((currentDate - dateBooked) / (1000 * 60));

                    // Check for 'Completed' or 'Done' status with 'Paid' payment status
                    if ((booking.booking_status_text === "Completed" || booking.booking_status_text === "Done") && booking.payment_status_text === "Paid") {
                        // Hide BookingInfoTable and show formFindAngkas
                        elements.bookingInfoTable.addClass("d-none").hide();
                        //elements.formFindAngkas.removeClass("d-none").show();
                        resolve(0); // Indicate no active booking
                        return;
                    }
                    else{
                        // Update BookingInfoTable with current booking details
                        elements.commutesInfo.addClass("d-none");
                        elements.rideInfoContainer.removeClass("d-none");

                        // Populate table elements
                        elements.bookingReferenceNumber.text(booking.angkas_booking_reference || "N/A");
                        elements.bookedElapseTime.text(`${elapsedTimeInMinutes} min ago.`);
                        elements.rideEstCost.text(`Php ${booking.form_Est_Cost || "0.00"}`);
                        elements.paymentStatus.text(`( ${booking.payment_status_text || "Checking..."} )`);
                        elements.customerOrigin.text(booking.form_from_dest_name || "N/A");
                        elements.customerDestination.text(booking.form_to_dest_name || "N/A");
                        elements.riderInfoBookingStatus.text(booking.booking_status_text || "N/A");

                        elements.btnPayRider.attr("data-payment-app", booking.angkas_booking_reference);
                        // Handle payment button visibility
                        if (booking.payment_status_text === "Paid") {
                            elements.btnPayRider.addClass("d-none").hide();
                        } else {
                            elements.btnPayRider.removeClass("d-none").show();
                        }

                        // Handle driver info
                         /* ${booking.rider_firstname || "N/A"}, ${booking.rider_lastname } */
                        if (booking.booking_status_text !== "Waiting for Driver") {
                            elements.riderInfoPI.html(
                                `<div class="card border-0 shadow-sm rounded-4 px-3 py-3 mb-3">
  <div class="d-flex align-items-center justify-content-between">
    
    <!-- Left: Profile Picture and Name -->
    <div class="d-flex align-items-center">
      <img src="../profile/${booking.rider_profile || 'default.jpg'}"
           alt="Profile Picture"
           class="rounded-circle me-3"
           style="width: 60px; height: 60px; object-fit: cover;">
      <div>
        <div class="d-flex align-items-center">
          <div class="me-2 text-warning">
            ★★★★★
          </div>
          <span class="fw-semibold small text-dark">4.8</span>
        </div>
        <h6 class="fw-bold m-0">${booking.rider_firstname || "N/A"} ${booking.rider_lastname || ""}</h6>
      </div>
    </div>

    <!-- Right: Plate Info -->
    <div class="text-center">
      <img src="../icons/scooter.png" alt="scooter icon" style="width: 30px;">
   <div class="bg-light text-dark px-2 py-1 rounded fw-semibold small mt-1" style="font-size: 0.75rem;">
        ${booking.vehicle_model || "N/A"}
      </div>
      <div class="bg-light text-dark px-2 py-1 rounded fw-semibold small mt-1" style="font-size: 0.75rem;">
        ${booking.plate_no || "N/A"}
      </div>
    </div>
  </div>

  <!-- Bottom Message -->
  <div class="mt-2 ps-1">
    <p class="text-muted mb-1 small">${booking.rider_firstname || "The rider"} ${booking.rider_lastname || ""} accepted your request</p>
  </div>

  <!-- Chat Button -->
  <div class="mt-2 d-flex justify-content-end">
    <a class="btn bg-yellow shadow open-chat-modal fw-bold"
       data-bs-toggle="modal"
       data-bs-target="#chatModal"
       data-rider-username="${booking.rider_username}"
       data-rider-userid="${booking.rider_user_id}">
       Chat <img src="../icons/messages.png" style="width: 18px; margin-left: 6px;" />
    </a>
  </div>
</div>

`
                            );
                        } else {
                            elements.riderInfoPI.html(CreateHtml.loadingGrower);
                        }
                    }
                    
                    

                    resolve(1); // Indicate an active booking exists
                } else {
                    // No active booking
                    elements.bookingInfoTable.addClass("d-none").hide();
                    // elements.formFindAngkas.trigger("reset");
                    // elements.formFindAngkas.removeClass("d-none").show();
                    resolve(0);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading booking:", error);
                reject(error);
            }
        });
    });
}

function checkPendingBooking() {
    return $.ajax({
        url: 'ajax_chk_booking.php', // Update with the correct path
        method: 'GET', // Or 'POST' depending on your server setup
        dataType: 'json'
    });
}
async function fetchBookingDetails() {
    try {
        const response = await checkPendingBooking();

        // If the value has changed, set up a debounce to delay DOM update
        if (response.bookingDetails.booking_status !== lastBookingStatus) {
            if (debounceTimeout) {
                clearTimeout(debounceTimeout); // Clear previous debounce if new data comes in
            }

            debounceTimeout = setTimeout(() => {
                updateBookingStatus(response.bookingDetails.booking_status);
                lastBookingStatus = response.bookingDetails.booking_status;
            }, 1500); // Debounce time in milliseconds (300ms here)
        } else {
            console.log("No change in booking status, skipping update.");
        }
    } catch (error) {
        console.error("Error fetching booking data:", error);
    }
}
async function handleCheckPendingBooking() { //in the app button
    try {
        const response = await checkPendingBooking();
        if (response.bookingStatus) {
            console.log("Pending booking found:", response.bookingDetails);
            let BookingStatus = response.bookingDetails.booking_status;
            let bookingStatusText = "";

            switch (BookingStatus) {
                case 'P':
                    bookingStatusText = 'Waiting for Driver';
                    textColor = 'text-bg-light';
                    break;
                case 'A':
                    bookingStatusText = 'We Found you a Driver';
                    textColor = 'text-bg-success';
                    break;
                case 'R':
                    bookingStatusText = 'Driver Arrived in Your Location';
                    textColor = 'text-bg-success';
                    break;
                case 'I':
                    bookingStatusText = 'In Transit';
                    textColor = 'text-bg-info';
                    break;
                case 'C':
                    bookingStatusText = 'Completed';
                    textColor = 'text-bg-success';
                    break;
                default:
                    bookingStatusText = 'Unknown Status';
                    break;
            }

            let payment_status = response.bookingDetails.payment_status;
            let paymentStatusText = "";

            switch (payment_status) {
                case 'D':
                    paymentStatusText = 'We Found you a Driver';
                    textColor = 'text-bg-success';
                    break;
                case 'C':
                    paymentStatusText = 'Completed';
                    textColor = 'text-bg-success';
                    break;
                default:
                    paymentStatusText = `<button id="PayNowBtn" data-payment-app="${response.bookingDetails.shop_order_reference_number}" class="btn btn-warning PayNowBtn" data-orderrefnum="${response.bookingDetails.shop_order_reference_number}" data-payshopcost="${response.bookingDetails.total_amount_to_pay}" data-paydeliveryfee="${response.bookingDetails.angkas_booking_estimated_cost}" >Pay Now</btn>`;
                    break;
            }

            let finalAmountToPay = response.bookingDetails.angkas_booking_estimated_cost;
            let $shopItemsReference = response.bookingDetails.shop_order_reference_number;

            if ($shopItemsReference != null) {
                finalAmountToPay += response.bookingDetails.total_amount_to_pay; //shop amount
            }

           

            var bookingCardView = `
            <div class="card-header bg-warning clear-fix">
                <span class="fw-bolder" id="BookingReferenceNum">${response.bookingDetails.angkas_booking_reference}</span>
                <span class="badge ${textColor} fw-bold text-danger float-end" id="bookingStatus" style="height:100px">${bookingStatusText}</span>
            </div>
            <div class="card-body" style="--bs-bg-opacity: .2;" id="bookingDetails">
                <div class="container-fluid">
                    <div class="row g-1">
                    
                        <div class="col-6">
                            <span class="fw-bold">Shop Cost</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="totalAmountToPay">Php ${response.bookingDetails.total_amount_to_pay}</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-bold">Delivery Cost</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="estimatedCost">Php ${response.bookingDetails.angkas_booking_estimated_cost}</span>
                        </div>
                   
                        <div class="col-6">
                            <span class="fw-bold">Payment Status</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="paymentStatus">${paymentStatusText}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                      ${response.bookingDetails.shop_order_reference_number
                    ? `<a data-bs-toggle="collapse" href="#${response.bookingDetails.shop_order_reference_number}" role="button" aria-expanded="false" aria-controls="${response.bookingDetails.shop_order_reference_number}" class="btn btn-sm btn-primary d-flex mx-2 my-1" id="${response.bookingDetails.shop_order_reference_number}">${response.bookingDetails.shop_order_reference_number}</a>` 
                    : ''}
                    </div>
                         ${response.bookingDetails.shop_order_reference_number
                        ? `<div class="col-12 shopDetails" id="${response.bookingDetails.shop_order_reference_number}"></div>`
                         : '' }

                        
                    </div>

                </div>
               </div>
            </div>
            `;

            

            // Ensure the element with id 'bookingDetails' exists in the DOM
            $('#BookingDetails').html(bookingCardView); // Set the content to the container
             // Construct the booking card view dynamically
             const itemData = await loadItemFromReference(response.bookingDetails.shop_order_reference_number);
             if (itemData) {
                 const tableHTML = generateItemTable(itemData);
                 // Insert table into the corresponding shop order content
                 $("div#" + response.bookingDetails.shop_order_reference_number).html(tableHTML);
             }
            return true;
        } else {
            console.log("No pending bookings.");
            return false;
        }
    } catch (error) {
        console.error("Error checking booking status:", error);
        return false;
    }

}
// Function to update the DOM element
function updateBookingStatus(newStatus) {
    const statusElement = $('#bookingStatus');
    switch (newStatus) {
        case 'P':
            bookingStatusText = 'Waiting for Driver';
            textColor = 'text-bg-light';
            break;
        case 'A':
            bookingStatusText = 'We Found you a Driver';
            textColor = 'text-bg-success';
            break;
        case 'R':
            bookingStatusText = 'Driver Arrived in Your Location';
            textColor = 'text-bg-success';
            break;
        case 'I':
            bookingStatusText = 'In Transit';
            textColor = 'text-bg-info';
            break;
        case 'C':
            bookingStatusText = 'Completed';
            textColor = 'text-bg-success';
            break;
        default:
            bookingStatusText = 'Unknown Status';
            break;
    }
    statusElement.text(bookingStatusText);
    statusElement.removeClass().addClass(textColor + " badge fw-bold float-end");
    //  console.log("Booking status updated:", newStatus);
}
function loadBookingInfo() {
    $.ajax({
        type: "GET",
        url: "ajax_get_all_booking.php",
        dataType: "json",
        success: function (response) {
            const container = elements.BookingHistoryContent;
            container.empty(); // Clear previous content

            if (response.hasBooking && response.bookings && response.bookings.length > 0) {
                response.bookings.forEach((booking) => {
                    // Dynamically create a card for each booking
                    const defaultImage =
                        booking.customer_gender === 'M' ? '../icons/male_person1.jpg' :
                            booking.customer_gender === 'F' ? '../icons/female_person1.jpg' :
                                '../icons/male_person2.jpg';

                    const bookingCard = ` 
                          <div class="card mb-3 w-100 mx-1">
                                <div class="row g-0">
                                    <div class="col-4 d-sm-none d-lg-block">
                                        <img src="${booking.rider_profile || defaultImage}" class="img-fluid rounded-start" alt="Rider Image">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
                                            <img src="${booking.rider_profile || defaultImage}" class="img-fluid card-img-top rounded-start d-lg-none" alt="Rider Image">
                                            <span class="card-title fw-bold">${booking.angkas_booking_reference}</span>
                                            <p class="card-text">
                                                <strong>From:</strong> ${booking.form_from_dest_name}<br>
                                                <strong>To:</strong> ${booking.form_to_dest_name}<br>
                                                <strong>Estimated Cost:</strong>Php ${booking.form_Est_Cost}
                                            </p>
                                            <p class="card-text"><small class="text-body-secondary">Date Booked: ${booking.date_booked}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    // Append each card to the modal content
                    container.append(bookingCard);
                });
            } else {
                // No booking data available
                container.html(`<p>${response.message || "No bookings found."}</p>`);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            container.html(`<p>Failed to fetch booking information. Please try again later.</p>`);
        }
    });
}
function processAngkasBooking(data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "ajax_process_find_angkas.php",
            data: data,
            dataType: "json", // Expect JSON response
            success: function (response) {
                if (response.hasPendingBooking) {
                    console.log("Pending booking exists:", response.pendingBookings);
                    resolve({ status: "pending", data: response });
                } else if (response.bookingReference) {
                    console.log("New booking created with reference:", response.bookingReference);
                    resolve({ status: "new", data: response });
                } else if (response.error) {
                    console.error("Error:", response.message);
                    reject(new Error(response.message || "An error occurred during booking."));
                } else {
                    console.warn("Unexpected response format:", response);
                    reject(new Error("Unexpected response format"));
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(new Error(error));
            }
        });
    });
}

function onSuccess(response, status, bookingNum) {
    if (status === "pending") {
        chkBooking(elements); // Handle pending bookings
    } else if (status === "new") {
        chkBooking(elements); // Update interface with booking details
        chkBookingStatus(bookingNum); // Pass booking reference
    }
}
function onError(errorMessage) {
    console.error("Error occurred:", errorMessage);
}

async function GetCurrentLocation(addressText, addressCoor, CurrLatElement = null, CurrLongElement = null) {
    const addressTxt = $(addressText);
    const addressCoorElement = $(addressCoor);

    if (!navigator.geolocation) {
        throw new Error("Geolocation is not supported by this browser.");
    }

    return new Promise((resolve, reject) => {
        // Request high-accuracy location
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Update the hidden inputs
                addressCoorElement.val(`${latitude},${longitude}`);
                if (CurrLatElement !== null) $(CurrLatElement).val(`${latitude}`);
                if (CurrLongElement !== null) $(CurrLongElement).val(`${longitude}`);

                // Perform reverse geocoding using Google Maps Geocoder
                const geocoder = new google.maps.Geocoder();
                const latlng = new google.maps.LatLng(latitude, longitude);

                geocoder.geocode(
                    {
                        location: latlng,
                        bounds: new google.maps.LatLngBounds(
                            new google.maps.LatLng(12.0, 123.0), // Example Southwest bound for Albay/Sorsogon
                            new google.maps.LatLng(13.5, 124.0) // Example Northeast bound
                        ),
                    },
                    (results, status) => {
                        if (status === google.maps.GeocoderStatus.OK && results[0]) {
                            const address = results[0].formatted_address;

                            // Update the address field
                            addressTxt.val(address);

                            // Resolve with the fetched data
                            resolve({
                                address: address,
                                coordinates: { lat: latitude, lng: longitude },
                            });
                        } else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                            reject(new Error("Quota exceeded for Google Maps API."));
                        } else if (status === google.maps.GeocoderStatus.REQUEST_DENIED) {
                            reject(new Error("Request denied. Check your API key or permissions."));
                        } else if (status === google.maps.GeocoderStatus.INVALID_REQUEST) {
                            reject(new Error("Invalid request. Ensure location parameters are correct."));
                        } else {
                            reject(new Error(`Geocoder failed with status: ${status}`));
                        }
                    }
                );
            },
            (error) => {
                let errorMessage = "";
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "User denied the request for Geolocation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Location information is unavailable.";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "The request to get user location timed out.";
                        break;
                    default:
                        errorMessage = "An unknown error occurred.";
                }

                reject(new Error(errorMessage));
            },
            {
                enableHighAccuracy: true,
                timeout: 10000, // Increased timeout for better accuracy
                maximumAge: 0,
            }
        );
    }).catch((error) => {
        console.error("Error in GetCurrentLocation:", error.message);
        $("#locationStatus").addClass("alert alert-danger").text("Error in GetCurrentLocation:" + error.message);;
        $("#shippingAddress").val(NULL);
        throw error; // Ensure errors bubble up to the caller
    });
}


function handleLocationError(isGeolocationError) {
    const errorMessage = isGeolocationError
        ? "Geolocation service failed. Please enable location services in your browser."
        : "Your browser does not support geolocation.";
    console.error(errorMessage);
}



function genBookRefNum(len, prefix = "") {
    const alphaNum = [..."ABCDEFGHIJKLMNOPQRSTUVWXYZ", ..."0123456789"];
    let key = "";

    for (let i = 0; i < len; i++) {
        // Choose a random index from the appropriate subset of alphaNum
        key += i % 2 === 0 
            ? alphaNum[Math.floor(Math.random() * 26)] // Letters (A-Z)
            : alphaNum[Math.floor(Math.random() * 10) + 26]; // Numbers (0-9)
    }

    return (prefix + key);
}
/****Triggers and Calling Functions****/

$(document).on("click", ".btnRideInfo", () => {
    const walletBalanceElement = $(".walletbalance");
    fetchAndAssignWalletBalance(walletBalanceElement);
});
$(document).on("submit", "#formFindAngkas", async function (e) {
    e.preventDefault();

    const form = $(this);
    const serializedData = form.serialize();
    console.log("Serialized Data:", serializedData);

    try {
        const response = await processAngkasBooking(serializedData);
        
        console.log("Booking response:", response);
        chkBooking();

        elements.commutesInfo.addClass("d-none");

        elements.rideInfoContainer.removeClass("d-none");

        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasBottom');
        bsOffcanvas.show();
        
    } catch (error) {
        console.error("Error processing booking:", error);
    }
});

let AppPendingBookingInterval = -1;
$(document).on('click','#appMenuBtn', (e)=>{
    
    e.preventDefault;
     if(handleCheckPendingBooking()){
         clearInterval(AppPendingBookingInterval);
     }
     else{
        AppPendingBookingInterval = setInterval(() => {
            handleCheckPendingBooking();
        }, 5000); // Check every 5 seconds
     }

});

// Document ready event
$(document).ready(function () {
    
    chkBooking(elements);
    setInterval(() => {
        if (isElementLoaded('#BookingReferenceNumber')) {
            const bookingNum = elements.bookingReferenceNumber.text().trim();
            if (bookingNum.length > 0) {
                chkBookingStatus(bookingNum);
            }
        }
    }, 5000);
    
    if (elements.transactionHistoryTable.length) {
        console.log("Transaction history table loaded. Initializing data load...");
        loadTransactionHistory();
    }

    elements.loadBookingHistory.on("click", loadBookingInfo);

    $('#chatModal').on('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = $(event.relatedTarget);

        // Extract info from data-* attributes
        var riderUsername = button.data('rider-username');
        var riderUserId = button.data('rider-userid');

        // Update modal content
        $('#riderName').text(riderUsername);
        $('#riderNameModal').text(riderUsername);
        $('#rideruserid').val(riderUserId);
    });

//
//    $('#formChatRider').on('submit', async function (e) {
//        e.preventDefault(); // Prevent default form submission behavior
//
//        // Collect form data
//        const formData = $(this).serialize();
//
//        try {
//            // Perform AJAX request to send the message
//            const response = await $.ajax({
//                url: '_ajax_send_message.php', // Backend script to handle message sending
//                type: 'POST',
//                data: formData
//            });
//
//            const result = JSON.parse(response);
//
//            if (result.status === 'success') {
//                // Clear the message input field
//                $('#formChatRider #messagecontent').val('');
//
//                // Re-fetch and update the chat messages
//                const senderId = $('input[name="sender_id"]').val();
//                const receiverId = $('input[name="receiver_id"]').val();
//
//                const messages = await fetchChatMessages(senderId, receiverId);
//
//                // Clear the chat container
//                $('#conversation').empty();
//
//                // Format and append messages to the chat container
//                messages.forEach((message) => {
//                    const messageClass = message.sender_id === senderId ? 'btn-secondary' : 'btn-primary';
//                    // timeAlignment = message.sender_id === senderId ? 'text-end' : 'text-start';
//                    const     alignment = message.sender_id === senderId ? 'ms-auto' : 'me-auto';
//                   const     timeAlignment = message.sender_id === senderId ? 'ms-auto' : 'me-auto';
//                    $('#conversation').append(`
//                         <div class="d-flex mb-1">
//                        <div class="p-2 ${alignment}">
//                            <p class="btn ${messageClass}"> ${message.message} </p>
//                        </div>
//                        <div class="p-2 ${timeAlignment}">
//                            <small class="small">${message.date_received}</small>
//                        </div>
//                    </div>
//                    `);
//                });
//            } else {
//                console.log('Failed to send the message. Please try again.');
//            }
//        } catch (error) {
//            console.error('An error occurred while sending the message. Please try again.',error);
//        }
//    });
//
//    
//
});
//
//
//async function fetchChatMessages(senderId, receiverId) {
//    return new Promise((resolve, reject) => {
//        $.ajax({
//            url: '_ajax_fetch_messages.php', // Backend script to fetch messages
//            method: 'POST',
//            data: {
//                sender_id: senderId,
//                receiver_id: receiverId
//            },
//            dataType: 'json',
//            success: function (response) {
//                if (response.status === 'success') {
//                    resolve(response.messages); // Return messages on success
//                } else {
//                    reject(response.error || 'Failed to fetch messages.');
//                }
//            },
//            error: function (xhr, status, error) {
//                reject(error || 'An error occurred while fetching messages.');
//            }
//        });
//    });
//}
//
//$(document).on('click', '.open-chat-modal', async function () {
//    const senderId = $('#senderuserid').val();
//    const receiverId = $(this).data('rider-userid');
//    
//  e.preventDefault(); // Prevent default form submission behavior
//
//    // Collect form data
//    const formData = $(this).serialize();
//    const conversationDiv = $('#conversation');
//
//    try {
//        // Perform AJAX request to send the message
//        const response = await $.ajax({
//            url: '_ajax_send_message.php', // Backend script to handle message sending
//            type: 'POST',
//            data: formData
//        });
//
//        const result = JSON.parse(response);
//
//        if (result.status === 'success') {
//            // Clear the message input field
//            $('#messagecontent').val('');
//
//            // Re-fetch and update the chat messages
//            // const senderId = $('input[name="sender_id"]').val();
//            //    const receiverId = $('input[name="receiver_id"]').val();
//
////            const senderId = $(".rider-id").data('rider-userid');
////            const receiverId = $(".customer-id").data('customer-userid');
//
//            const messages = await fetchChatMessages(senderId, receiverId);
//
//            // Clear the chat container
//            $('#conversation').empty();
//
//            // Format and append messages to the chat container
//            if (messages.length > 0) {
//                messages.forEach(msg => {
//                    const isSender = msg.sender_id === senderId;
//
//                    const alignmentClass = isSender ? 'justify-content-end' : 'justify-content-start';
//                    const bubbleClass = isSender ? 'bg-primary text-white' : 'bg-light text-dark';
//
//                    const messageHtml = `
//            <div class="d-flex ${alignmentClass} mb-1">
//                <div class="p-2 rounded-3 ${bubbleClass}">
//                    ${msg.message}
//                </div>
//            </div>
//        `;
//
//                    conversationDiv.append(messageHtml);
//                });
//            } else {
//                conversationDiv.html('<small class="text-center text-body-tertiary">No messages yet for today. Start the conversation!</small>');
//            }
//
//
//        } else {
//            console.log('Failed to send the message. Please try again.');
//        }
//    } catch (error) {
//        console.error('An error occurred while sending the message. Please try again.', error);
//    }
//});





$(document).on("click", "#PayNowBtn, .PayNowBtn, .btn-pay", function (e) {
    e.preventDefault();

    let FShopCost, FDeliveryFee, FOrderRefNum, FBookingRefNum, walletBalance, estimatedCost, dataPaymentApp, row;

    if ($(this).attr('id') === 'btnPayRider') {
        // For Ride Payment
        walletBalance = parseFloat(elements.walletBalance.text().replace('Php ', '').replace(',', '').trim());
        dataPaymentApp = elements.btnPayRider.attr('data-payment-app');
        row = elements.btnPayRider.closest('tr');
        estimatedCost = parseFloat(row.find('.text-secondary').text().replace('Php ', '').replace(',', '').trim());

        if (!isNaN(estimatedCost) && estimatedCost > 0 && walletBalance > estimatedCost) {
            console.log("::Payment:", estimatedCost, dataPaymentApp, 'R', 'Ride Payment');
            makePayment(estimatedCost, null, null, dataPaymentApp, 'R', 'Ride Payment');
            updatePaymentStatus(dataPaymentApp, 'C', 'A'); //for booking
            $(this).html(LoadingIcon + " Please Wait");
            setTimeout(() => {
                $(this).html(chkIcon + " Done");
            }, 1000);
        }
    } else {
        // For Shop and Delivery Payment
        FShopCost = $(this).attr('data-payshopcost') || $("#FinalShopCost").text().trim();
        FDeliveryFee = $(this).attr('data-paydeliveryfee') || parseFloat($("#FinalDeliveryFee").text().trim());
        FOrderRefNum = $(this).attr('data-orderrefnum') || $("#FinalOrderRefNum").text().trim();
        FBookingRefNum = $(this).attr('data-bookingrefnum');

        // Ensure numeric values for FShopCost and FDeliveryFee
        FShopCost = parseFloat(FShopCost) || 0;
        FDeliveryFee = parseFloat(FDeliveryFee) || 0;

        // Perform payments for shop cost and delivery fee
        if (FShopCost !== null && FOrderRefNum !== null) {
            makePayment(FShopCost, null, FOrderRefNum, FOrderRefNum, 'S'); // Pay shop cost
            updatePaymentStatus(FOrderRefNum, 'C', 'S');
        }

        if (FDeliveryFee != null && FBookingRefNum !== null) {
            makePayment(FDeliveryFee, null, null, FBookingRefNum, 'R'); // Pay delivery fee
            updatePaymentStatus(FBookingRefNum, 'C', 'A');
        }

        $(this).html(LoadingIcon);
        setTimeout(() => {
            $(this).prop("disabled", true);

            if ("form".length > 0) {
                $("form").trigger("reset");
            }
        }, 1000);

        $("button.btn-success").removeClass("btn-success").addClass("btn-warning").prop("disabled", false);
        $("input.border-success").removeClass("border-success").addClass("border-warning");

        // Close modal after 10 seconds
        setTimeout(() => {
            $(".btn-close").trigger("click");
        }, 10000);

        $(this).removeClass("btn-warning").removeClass("btn-danger").addClass("btn-success").html(chkIcon + " Done");
    }
});


$(document).on('submit', '#topUpForm', function (event) {
    event.preventDefault();

    const form = $(this)[0];
    const formData = new FormData(form); // Use FormData for handling file uploads
    const submitButton = $('button[type=submit]');
    submitButton.html(LoadingIcon);
    submitButton.prop('disabled', true);

    $.ajax({
        url: 'ajax_top_up_wallet.php',
        type: 'POST',
        data: formData, // Use FormData
        processData: false, // Prevent jQuery from automatically processing the data
        contentType: false, // Prevent jQuery from setting the content type
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Disable the button and show the loading icon
                // Process data (e.g., refresh transaction history and update wallet balance)
                loadTransactionHistory();
                fetchAndAssignWalletBalance(elements.walletBalance);
        
                // Simulate processing delay
                setTimeout(() => {
                    // Replace the button icon with the confirmation icon
                    submitButton.html(chkIcon);
        
                    // Re-enable the button and hide the modal after a short delay
                    setTimeout(() => {
                        $('#topUpModal').modal('hide');
                        submitButton.prop('disabled', false).html('Top-Up'); // Reset button text for future use
                    }, 2000); // Delay before closing the modal
                }, 2000); // Delay for showing the loading icon
            }  else {
                console.error(response.error || 'Top-up failed. Please try again.');
            }
        },
        error: function () {
        
            console.log("An error occurred. Please try again later.");
        }
    });
});





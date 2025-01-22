let isBookingAccepted = false; // Tracks if a booking has been accepted
loadingIcon = "<span class='spinner-border spinner-border-sm'></span>";
let currentPage = 1;
const pageSize = 5;
let transactions = [];
let lastBookingStatus = null; // Store the last known value

const chkIcon = `<span class="text-light"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
<path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
</svg></span>`;

$(document).on("submit", ".booking_form", function (e) {
    e.preventDefault();
    const booking_id = $(this).find('input[name="booking_ref"]').val();
    console.log("Booking ID submitted:", booking_id);

    $.ajax({
        type: "POST",
        url: "ajax_rider_accept_booking.php",
        data: $(this).serialize(),
        success: function (data) {
            console.log("Booking accepted response:", data);
            isBookingAccepted = true; // Set the flag
            fetchCurrentBookings(); // Display the accepted booking
            clearInterval(queueInterval); // Stop the interval check
            
        },
        error: function (xhr, status, error) {
            console.error("Error accepting booking:", error);
            fetchQueueData(); // Retry fetching the queue
        }
    });
});

$(document).on("click",".confirmDropOffBtn", async function(){

});
function claimStubWallet(userWalletId) {
    // Make AJAX request
    $.ajax({
        url: 'ajax_claim_stub.php', // Replace with your backend endpoint
        type: 'POST',
        data: {
            user_wallet_id: userWalletId,
        },
        success: function(response) {
            // Handle success response
            if (response.success) {
                console.log('Wallet record updated successfully!');
                fetchAndAssignWalletBalance('.walletbalance',".earnings");
            } else {
                console.warn('Failed to update wallet record: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error('Error updating wallet record:', error);
        }
    });
}

    
async function updateQueue() {
    try {
        if (isBookingAccepted) {
            console.log("Booking already accepted. Stopping queue updates.");
            return; // Stop further updates
        }

        const queueData = await fetchQueueData(); // Fetch queue data

        if (queueData.queue_list.length > 0 && queueData.status !== "in transit") {
            handleQueueData(queueData); // Process and render the data
        } else if (queueData.status === "in transit") {
            console.log("Booking accepted. Fetching current bookings.");
            isBookingAccepted = true; // Set flag to true
            fetchCurrentBookings(); // Fetch the accepted booking
        }else if(queueData.queue_list.length === 0){
            console.log("No Queue listed.");
        }
         else {
            console.log("Looking for customers.");
        }
    } catch (error) {
        console.error("Error updating queue:", error);
    }
}


function fetchQueueData() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax_rider_queue.php",
            dataType: "json",
            success: function (data) {
                resolve(data); // Resolve the promise with the data
            },
            error: function (xhr, status, error) {
                console.error("Error fetching queue data:", error);
                reject(error); // Reject the promise with the error
            }
        });
    });
}

function handleQueueData(data) {
    if (!data) {
        console.warn("No data received for queue processing.");
        return;
    }

    const currentQueue = data.current_queue;
    const queueList = data.queue_list;
    const status = data.status;

    //$("#availableBookings").html(`<span class="badge text-bg-info">Current Queue: ${currentQueue}, Status: ${status} </span>`);
    $("#bookingCards").remove(); // Remove previous cards
    $("#availableBookings").append('<div id="bookingCards" class="col-12"></div>');

    if (status !== "in transit" && Array.isArray(queueList) && queueList.length > 0) {
        queueList.forEach((booking) => {
            const card = createBookingCard(booking);
            $("#bookingCards").append(card);
        });
    }
}



function fetchCurrentBookings() {
    $.ajax({
        url: "ajax_get_booking.php",
        dataType: "json",
        success: function (data) {
            const availableBookings = $("#availableBookings");
            const bookingCards = $("#bookingCards");

            availableBookings.empty();

            if (data && Array.isArray(data.queue_list) && data.queue_list.length > 0) {
                bookingCards.remove();
                availableBookings.append('<div id="bookingCards" class="col-12"></div>');
                data.queue_list.forEach((booking) => {
                    const card = ViewBookingCard(booking, false); // Use the accepted booking card view
                    $("#bookingCards").append(card);
                });
            } else {
                availableBookings.html('<div class="alert alert-warning">No Current Bookings available. Checking for new Bookings ' + loadingIcon + '</div>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching current bookings:", error);
        }
    });
}


function fetchHistBookings(searchkey = null) {
    const requestData = {};
    
    if (searchkey !== null) {
        requestData.searchkey = searchkey;
    }

    $.ajax({
        url: "ajax_fetch_ride_history.php",
        dataType: "json",
        data: requestData,
        success: function (data) {
            const bookingHistory = $("#rideHistory");
            const bookingCards = $("#bookingCardsHist");

            bookingHistory.empty();

            if (data && Array.isArray(data.queue_list) && data.queue_list.length > 0) {
                bookingCards.remove();
                bookingHistory.append('<div id="bookingCardsHist" class="col-12"></div>');
                data.queue_list.forEach((booking) => {
                    const card = ViewBookingCard(booking, null); // Use the accepted booking card view
                    $("#bookingCardsHist").append(card);
                });
            } else {
                bookingHistory.html('<div class="alert alert-secondary">No Booking found.</div>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching current bookings:", error);
        }
    });
}

$(document).on('input', '#searchRide', function () {
    const searchKey = $(this).val().trim();
    fetchHistBookings(searchKey);
});



// Declare Functions

function createBookingCard(booking) {
    // Get the current date and time
const currentDate = new Date();
const dateBooked = new Date(booking.date_booked);
const elapsedTimeInMinutes = Math.floor((currentDate - dateBooked) / (1000 * 60));

let colorText = "";
if (elapsedTimeInMinutes > 30) {
   colorText = "text-danger";
}
else if (elapsedTimeInMinutes > 15) {
    colorText = "text-warning";
}
else if (elapsedTimeInMinutes > 10) {
    colorText = "text-info";
}
else if (elapsedTimeInMinutes <= 10) {
    colorText = "text-success";
}
   return `
      <div class="row d-flex align-items-start mb-4 p-3 rounded bg-white">
        <div class="col-md-3 d-none d-md-block position-relative">
            <img src="../icons/${booking.user_profile_image}"  class="img-fluid rounded-start"  alt="${booking.user_firstname} ${booking.user_lastname}"  style="object-fit: cover; height: 200px; width: 100%;">
        </div>
        <div class="col-md-6">
            <div class="card-body">
                <h5 class="text-primary">${booking.angkas_booking_reference}</h5>
                <p class="small fw-light mb-0 ${colorText}">Booked ${elapsedTimeInMinutes} min ago</p>
                <hr class="my-0">
                <p class="mb-1"><strong>From:</strong> ${booking.form_from_dest_name}</p>
                <p class="mb-1"><strong>To:</strong> ${booking.form_to_dest_name}</p>
                <p class="mb-1">
                    <strong>ETA Duration:</strong> ${booking.form_ETA_duration} mins 
                    <strong>Total Distance:</strong> ${booking.form_TotalDistance} km
                </p>
                <p class="mb-0">
                    <strong>Contact:</strong> ${booking.user_contact_no} 
                    <span class="text-muted">(${booking.user_email_address})</span>
                </p>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-center">
                <form id="map${booking.angkas_booking_reference}" class="booking_form w-100" method="POST">
                    <input type="hidden" value="${booking.angkas_booking_reference}" name="booking_ref" />
                    <button type="submit" class="shadow btn btn-success w-100 text-center py-3">
                        Accept Booking
                        (<strong>Php ${booking.form_Est_Cost}</strong>)
                    </button>
                </form>
        </div>
</div>

   `;
}


    

    // Function to create booking card
    function ViewBookingCard(booking, hist = true) {
        const isHist = (hist == null ? false : true);
        // Get the current date and time
        const currentDate = new Date();
        // Parse booking.date_booked as a Date object
        const dateBooked = new Date(booking.date_booked);
        const elapsedTimeInMinutes = Math.floor((currentDate - dateBooked) / (1000 * 60));
    
        // Determine the color class based on elapsed time
        let colorText = "";
        if (elapsedTimeInMinutes > 30) {
            colorText = "text-danger";
        } else if (elapsedTimeInMinutes > 15) {
            colorText = "text-warning";
        } else if (elapsedTimeInMinutes > 10) {
            colorText = "text-info";
        } else {
            colorText = "text-success";
        }
    
        // Create the action button only if hist is true
        const actionButton = (isHist === false)
            ? "" : `<div class="col-md-3 d-flex align-items-center justify-content-center">
                    <a href="_current_booking_map.php" 
                       class="btn shadow btn-success w-75">
                       Show Location
                    </a>
               </div>`
            ;
    
        // Return the booking card HTML
        return `
          <div class="row d-flex align-items-start mb-4 p-3 shadow-sm border-0 rounded bg-white">
            <!-- Image Section -->
            <div class="col-md-3 col-sm-12">
                <img src="../icons/${booking.user_profile_image}" 
                     class="img-fluid rounded-start" 
                     alt="${booking.user_firstname} ${booking.user_lastname}" 
                     style="object-fit: cover; height: 200px; width: 100%;">
            </div>
    
            <!-- Info Section -->
            <div class="col-md-6">
                <div class="row mb-2">
                    <h5 class="text-primary">${booking.angkas_booking_reference}</h5>
                    <p class="small text-muted">Booked ${elapsedTimeInMinutes} minutes ago</p>
                </div>
                <hr class="my-2">
                <div class="row">
                    <p class="mb-1"><strong>From:</strong> ${booking.form_from_dest_name}</p>
                    <p class="mb-1"><strong>To:</strong> ${booking.form_to_dest_name}</p>
                    <p class="mb-1">
                        <strong>ETA Duration:</strong> ${booking.form_ETA_duration} mins 
                        <strong>Total Distance:</strong> ${booking.form_TotalDistance} km
                    </p>
                    <p class="mb-0">
                        <strong>Contact:</strong> ${booking.user_contact_no} 
                        <span class="text-muted">(${booking.user_email_address})</span>
                    </p>
                </div>
            </div>
            ${actionButton}
          </div>`;
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
async function fetchAndAssignWalletBalance(balance = null, earnings  = null) {
    try {
        const data = await getWalletBalance(); // Get wallet balance data
        if (data && data.balance && data.earnings ) {
            const formattedBalance = `Php ${data.balance}`;
            const formattedEarnings = `Php ${data.earnings}`

            // Ensure `elements` is a jQuery object
        if(balance !== null){
            $(balance).each(function () {
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
        }
        if(earnings !== null){
            $(earnings).each(function () {
                const element = $(this);

                // Check if the element is an input or textarea (elements that use val())
                if (element.is("input, textarea")) {
                    element.val(formattedEarnings);
                } 
                // Otherwise, use text() for other elements
                else {
                    element.text(formattedEarnings);
                }
            });
        }
          

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


/*Call functions upon document ready*/
let queueInterval; // Declare the interval variable globally

// Function to start the queue interval
function startQueueInterval() {
    queueInterval = setInterval(() => {
        updateQueue();
    }, 5000); // Run every 10 seconds
}

// Function to stop the queue interval
function stopQueueInterval() {
    if (queueInterval) {
        clearInterval(queueInterval);
        queueInterval = null; // Ensure it's cleared
    }
}

$(document).ready(function() {

    loadTransactionHistory();
    fetchHistBookings();
    fetchCurrentBookings();
    
    
    
    // Initial data fetch
    // Start the queue interval when the page loads
    startQueueInterval();




});


$(document).on("submit","#formConvert", function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const formData = $(this).serialize(); // Serialize form data

    $.ajax({
        url: "_ajax_convert_earnings.php", // Backend script
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (response) {
            if (response.success) {
                alert("Conversion successful: " + response.message);
                fetchAndAssignWalletBalance('.walletbalance',".earnings");
                loadTransactionHistory();
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error: ", error);
            alert("An error occurred while processing your request. Please try again.");
        }
    });
});


    // Event listener for booking-tab click
    // $(document).on("click", "#bookings-tab", async function () {
    //     fetchCurrentBookings();
    //     startQueueInterval(); // Restart the queue updates
    // });


$(document).on("click",".claim-stub", function(e){
    e.preventDefault;
    const userWalletId = $(this).data("claimwallet");
    console.log("Claiming",userWalletId);
    claimStubWallet(userWalletId);
    fetchAndAssignWalletBalance('.walletbalance',".earnings");
    console.log("Claimed",userWalletId);
    $(this).prop("disabled",true);
    $(this).html("<h3 class='fw-bold text-success'>Claimed!</h3>");
    //$(this).addClass("d-none");
});

$(document).on('submit','#formCashOut', function (event) {
    event.preventDefault(); // Prevent default form submission behavior
    const $button = $('.btnCashOut');
    $button.prop("disabled",true);
    $button.html(loadingIcon);
    // Extract form data
    const amount = parseFloat($('input[name="CashOutAmount"]').val().trim());
    const gcashAccountNumber = $('input[name="GCashAccountNumber"]').val().trim();
    const gcashAccountName = $('input[name="GCashAccountName"]').val().trim();

    // Validate form inputs
    if (!amount || amount <= 0) {
        alert('Please enter a valid amount.');
        return;
    }
    if (!gcashAccountNumber) {
        alert('Please provide a valid GCash Account Number.');
        return;
    }
    if (!gcashAccountName) {
        alert('Please provide a valid GCash Account Name.');
        return;
    }

    // Call the cashOut function
    cashOut({
        amount: amount,
        payFrom: -1, // Assuming 99 is a default value for "payFrom"
        referenceNum : null, // Generate a unique reference number
        paymentType: 'C', // Default payment type is 'C'
        action: 'Cash Out', // Wallet action
        gcashAccountNumber: gcashAccountNumber,
        gcashAccountName: gcashAccountName
    });
    $button.html(chkIcon);
});

async function cashOut({ 
    amount, 
    payFrom = -1, 
    payTo = null, 
    referenceNum = null, 
    paymentType = 'C', 
    action = 'Cash Out', 
    gcashAccountNumber = null, 
    gcashAccountName = null 
}) {
    const walletBalanceElement = $(".earnings");
    const triggerElement = $(this);
    const currentBalanceText = parseFloat(walletBalanceElement.text().trim());
    let currentBalance = 0.00;

    
    const data = await getWalletBalance(); // Get wallet balance data
    if (data && data.balance) {
        currentBalance = parseFloat(data.balance.replace(/[^0-9.-]+/g, ""));
    }
    else{
        currentBalance = parseFloat(
            currentBalanceText.replace(/[^0-9.-]+/g, "") // Remove "Php" and commas
        );
    }


    console.log(amount, currentBalance);
    if(amount > currentBalance){
        $('.txn_status')
        .addClass("alert pt-2")
        .removeClass("alert-success")
        .addClass("alert-danger")
        .html(`
            <br>Requested cash out of Php ${parseFloat(amount).toFixed(2)} 
            to GCash Account: ${gcashAccountNumber} is not possible. You cannot request more than your balance<br>
        `);
        console.error("Cannot Cash Out more than your balance", amount,currentBalance );
        return;
    }
    else{

    // Disable the button during the request
    triggerElement.prop('disabled', true);


    $.ajax({
        url: 'ajax_cash_out.php',
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            amount,
            payToUser: payTo,
            payFromUser: payFrom,
            refNum: referenceNum,
            paymentType,
            wallet_action: action,
            gcashAccountNumber,
            gcashAccountName
        }),
        contentType: 'application/json',
        success: function (response) {
            if (response.success) {
                console.log("Cash-out request submitted successfully.");
                $('.txn_status')
                    .removeClass("alert-danger")
                    .addClass("alert-success")
                    .html(`
                        <br>Requested cash out of Php ${parseFloat(response.amount).toFixed(2)} 
                        to GCash Account: ${gcashAccountNumber}<br>
                    `);

                fetchAndAssignWalletBalance('.walletbalance','.earnings');
            } else {
                $('.txn_status')
                    .removeClass("alert-success")
                    .addClass("alert-danger")
                    .text(response.message || "Cash-out request failed.");
            }
        },
        error: function (xhr, status, error) {
            console.error('Cash-out request error:', error);
            $('.txn_status')
                .removeClass("alert-success")
                .addClass("alert-danger")
                .text("An error occurred while processing the cash-out request.");
        },
        complete: function () {
            triggerElement.prop('disabled', false);
        }
    });
    }

}

function loadTransactionHistory() {
    $.ajax({
        url: '../client/ajax_fetch_wallet_transactions.php',
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
function isElementLoaded(...selectors) {
    return selectors.every(selector => {
        const exists = document.querySelector(selector) !== null;
        //        console.log(`${selector} ${exists ? 'has' : 'has not'} been loaded`);
        return exists;
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
        paginationContainer.append(`<button class="btn btn-warning rounded-5 btn-sm m-1" onclick="changePage(${currentPage - 1})"> < </button>`);
    }

    for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        paginationContainer.append(`<button class="btn btn-warning  rounded-5 btn-sm m-1 ${activeClass}" onclick="changePage(${i})">${i}</button>`);
    }

    if (currentPage < totalPages) {
        paginationContainer.append(`<button class="btn btn-warning  rounded-5 btn-sm ms-1" onclick="changePage(${currentPage + 1})"> > </button>`);
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

$(document).on('submit', '#topUpForm', function (event) {
    event.preventDefault();

    const form = $(this)[0]; // Get the raw DOM element
    const formData = new FormData(form); // Create FormData object with form content

    $.ajax({
        url: 'ajax_top_up_wallet.php',
        type: 'POST',
        data: formData, // Use FormData instead of serialized data
        dataType: 'json',
        processData: false, // Prevent jQuery from automatically transforming the data into a query string
        contentType: false, // Prevent jQuery from overriding the Content-Type header
        success: function (response) {
            if (response.success) {
                $('#topUpModal').modal('hide'); // Close the modal
                //form.closest('tr').addClass('d-none'); // Hide the parent row
                loadTransactionHistory(); // Refresh transaction history
                fetchAndAssignWalletBalance('.walletbalance',".earnings"); // Update wallet balance
                $(".txn_status").addClass("alert alert-success").text("Top Up Request sent.");
            } else {
                alert(response.error || 'Top-up failed. Please try again.');
            }
        },
        error: function () {
            alert('An error occurred. Please try again later.');
        }
    });
});

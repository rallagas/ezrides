// Function to load transaction history
let currentPage = 1; // Start on page 1
const pageSize = 5; // Number of transactions per page
let transactions = []; // Store the full list of transactions

function waitForElement(selector, callback, timeout = 5000) {
    const startTime = Date.now();

    // Check if the element already exists
    if (document.querySelector(selector)) {
        callback(document.querySelector(selector));
        return;
    }

    // Set up a MutationObserver to watch for changes in the DOM
    const observer = new MutationObserver((mutations, observerInstance) => {
        const element = document.querySelector(selector);
        if (element) {
            observerInstance.disconnect(); // Stop observing
            callback(element); // Execute the callback
        } else if (Date.now() - startTime > timeout) {
            observerInstance.disconnect();
            console.error(`Timeout: Element ${selector} not found within ${timeout}ms`);
        }
    });

    // Start observing the document for DOM changes
    observer.observe(document.body, { childList: true, subtree: true });
}



function isElementLoaded(...selectors) {
    for (let selector of selectors) {
        if (document.querySelector(selector) !== null) {
            console.log(selector + " has been loaded");
        } else {
            console.log(selector + " has not been loaded");
            return false; // Return false if any element is not found
        }
    }
    return true; // All elements found
}

// Function to load transaction history
function loadTransactionHistory() {
    $.ajax({
        url: 'ajax_fetch_wallet_transactions.php', // Endpoint to fetch transaction history
        type: 'GET',
        dataType: 'json', // Expecting JSON response
        success: function (response) {
            transactions = response; // Store the transactions
            renderTransactions(); // Render the current page of transactions
            renderPagination(); // Render the pagination controls
        },
        error: function (xhr, status, error) {
            console.error('Failed to load transaction history:', error);
        }
    });
}

// Function to render transactions for the current page
function renderTransactions() {
    if (isElementLoaded('#transactionHistoryTable')) {
        const tbody = $('#transactionHistoryTable tbody');
        tbody.empty(); // Clear any existing rows

        // Get the transactions for the current page
        const pageTransactions = transactions.slice((currentPage - 1) * pageSize, currentPage * pageSize);

        // Loop through each transaction in the current page and create rows
        pageTransactions.forEach(transaction => {
            const row = `
            <tr>
                <td>$${transaction.amount}</td>
                <td>${transaction.type}</td>
                <td>${transaction.status}</td>
                <td>${transaction.date}</td>
            </tr>
        `;
            tbody.append(row);
        });
    }

}


// Function to render pagination controls
function renderPagination() {
    const totalPages = Math.ceil(transactions.length / pageSize); // Total number of pages
    const paginationContainer = $('#pagination');

    paginationContainer.empty(); // Clear any existing pagination buttons

    // Add "Previous" button
    if (currentPage > 1) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage - 1})">Previous</button>`);
    }

    // Add page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        paginationContainer.append(`<button class="btn btn-secondary ${activeClass}" onclick="changePage(${i})">${i}</button>`);
    }

    // Add "Next" button
    if (currentPage < totalPages) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage + 1})">Next</button>`);
    }
}


// Function to change the page and re-render the data
function changePage(page) {
    if (page >= 1 && page <= Math.ceil(transactions.length / pageSize)) {
        currentPage = page;
        renderTransactions();
        renderPagination();
    }
}



function makePayment(estimatedCost, action = null, row = null) {

    var row = row;
    $.ajax({
        url: 'ajax_make_payment.php', // URL to make payment
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            amount: estimatedCost,
            wallet_action: action
        }),
        contentType: 'application/json',
        success: function (paymentResponse) {
            if (paymentResponse.success) {

                console.log("Payment Successful.");
                // Optionally update the UI (e.g., mark as paid)
                row.find('#paymentStatus').text('Paid');
                row.find('.btn-pay').prop('disabled', true); // Disable the pay button
                getWalletBalance;
            } else {
                console.log('Payment failed. Please try again.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Payment error:', error);
            console.log('An error occurred while processing your payment.');
        }
    });
}

function getWalletBalance() {
    // Show loading state (optional)
    $('#WalletBalance').text('Loading...');

    // AJAX request to fetch the balance
    $.ajax({
        url: 'ajax_get_balance.php', // URL to the PHP endpoint to get balance
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            // Check if the response contains a balance field
            if (response.balance) {
                // Update the UI with the user's wallet balance
                $('#WalletBalance').text('Php ' + response.balance);
            } else {
                // Handle any error response (e.g., balance not found)
                $('#WalletBalance').text('Error fetching balance');
            }
        },
        error: function (xhr, status, error) {
            // Handle the error if the AJAX request fails
            console.error('Error fetching balance:', error);
            $('#WalletBalance').text('An error occurred. Please try again.');
        }
    });
}


function chkBooking() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax_get_current_booking.php",
            dataType: "json",
            success: function (data) {
                let returnVal = 0;

                if (data.hasBooking) {
                    const booking = data.booking;
                    const currentDate = new Date();
                    const dateBooked = new Date(booking.date_booked);
                    const elapsedTimeInMinutes = Math.floor((currentDate - dateBooked) / (1000 * 60));

                    $("#formFindAngkas").hide();
                    const bookingHtml = ` 
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row">Booking #</th>
                                    <td id="BookingReferenceNumber" class="text-success fw-bold">${booking.angkas_booking_reference}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Booked #</th>
                                    <td class="text-success fw-bold">${elapsedTimeInMinutes} min ago.</td>
                                </tr>
                                <tr>
                                    <th scope="row">Fare
                                           
                                    </th>
                                    <td class="text-secondary fw-bold" >Php ${booking.form_Est_Cost} 
                                        <span id="paymentStatus">( ${booking.payment_status_text} ) </span> <br>
                                       <button class="btn-pay btn btn-outline-success">Pay</button>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Origin</th>
                                    <td class="fw-semibold">${booking.form_from_dest_name}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Destination</th>
                                    <td class="fw-semibold">${booking.form_to_dest_name}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td id="riderInfoBookingStatus" class="fw-semibold text-danger">${booking.booking_status_text}</td>
                                </tr>
                                ${booking.booking_status_text !== "Waiting for Driver" 
                                    ? `<tr id="riderInfoPI">
                                           <th scope="row">Driver</th>
                                           <td class="fw-semibold">${booking.rider_firstname}, ${booking.rider_lastname}</td>
                                       </tr>`
                                    : `<tr>
                                           <th scope="row">Driver</th>
                                           <td>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                               <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                                           </td>
                                       </tr>`
                                }
                            </tbody>
                        </table>
                    `;
                    $("div#currentBookingInfo").html(bookingHtml);
                    $("#infoAlert").html(bookingHtml);

                    returnVal = 1; // Set returnVal to 1 if there's a booking
                } else {
                    $("#formFindAngkas").show();
                    const noBookingHtml = `<div class="alert alert-info">${data.message}</div>`;
                    $("div#currentBookingInfo").html(noBookingHtml);
                    returnVal = 0; // Set returnVal to 0 if no booking
                }

                resolve(returnVal);
            },
            error: function (xhr, status, error) {
                console.error("Error loading booking:", error);
                reject(error);
            }
        });
    });
}

//function chkBookingStatus() {
//    console.log("Check Booking Status.");
//    if (isElementLoaded('#riderInfoBookingStatus')) {
//        var bookingNum = $('#BookingReferenceNumber').text();
//
//        $.ajax({
//            url: 'ajax_get_booking_status.php', // URL to the PHP endpoint to get balance
//            method: 'GET',
//            dataType: 'json',
//            success: function (response) {
//                // Check if the response contains a balance field
//                if (response.balance) {
//                    // Update the UI with the user's wallet balance
//                    $('#WalletBalance').text('Php ' + response.balance);
//                } else {
//                    // Handle any error response (e.g., balance not found)
//                    $('#WalletBalance').text('Error fetching balance');
//                }
//            },
//            error: function (xhr, status, error) {
//                // Handle the error if the AJAX request fails
//                console.error('Error fetching balance:', error);
//                $('#WalletBalance').text('An error occurred. Please try again.');
//            }
//        });
//    }
//}

function chkBookingStatus(bookingId) {
    if (isElementLoaded('#riderInfoBookingStatus', '#BookingReferenceNumber')) {
        const bookingNum = $('#BookingReferenceNumber').text();
        $.ajax({
            url: 'ajax_get_booking_status.php', // Replace with the actual path to your PHP file
            type: 'POST',
            data: {
                action: 'fetch_booking_status',
                bookingId: bookingNum
            },
            dataType: 'json',
            success: function (response) {
                let statusText = '';
                let statusHtml = '';

                // Determine the status text and HTML based on the booking_status
                switch (response.booking_status) {
                    case 'P':
                        statusText = 'Waiting for Driver';
                        statusHtml = `
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                        `;
                        break;
                    case 'A':
                        statusText = 'Driver Found';
                        break;
                    case 'R':
                        statusText = 'Driver Arrived in Your Location';
                        break;
                    case 'I':
                        statusText = 'In Transit';
                        break;
                    case 'C':
                        statusText = 'Completed';
                        break;
                    case 'F':
                        statusText = 'Pending Payment';
                        break;
                    default:
                        statusText = 'Unknown Status';
                }

                // Update the booking status text
                $('#riderInfoBookingStatus').html(statusHtml || statusText);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching booking status:', error);
                $('#riderInfoBookingStatus').text('Error fetching booking status');
            }
        });
    }


}



$(document).ready(function () {


    chkBooking();
    getWalletBalance();


    // Check if the #transactionHistoryTable element exists on the page
    if (isElementLoaded('#transactionHistoryTable')) {
        // Call loadTransactionHistory() to fetch and display transaction history
        loadTransactionHistory();
    }

    // Event listener for the Pay button
    setTimeout(function () {
        if (isElementLoaded('.btn-pay')) {
            $('.btn-pay').click(function (event) {
                console.log("btn pay was clicked.");
                event.preventDefault(); // Prevent the default form submit behavior

                // Get the row containing the fare information

                var row = $(this).closest('tr');
                var costText = row.find('.text-secondary').text();

                // Extract the estimated cost from the string
                var estimatedCost = parseFloat(costText.replace('Php ', '').replace(',', '').trim());

                if (isNaN(estimatedCost) || estimatedCost <= 0) {
                    console.log('Invalid amount to pay. Please check the booking.');
                    return;
                }
                console.log(estimatedCost);
                getWalletBalance;
                makePayment(estimatedCost, null, row);

            });
        }

    }, 3000);


    if (isElementLoaded(".add-destination-button", "#btnRideInfo", '#findMeARiderBTN')) {
        $(".add-destination-button").click(function () {
            $("#findMeARiderBTN").removeClass("d-none");
            $("#btnRideInfo").click();
            chkBooking();
        });

    }
    setTimeout(function () {
        if (isElementLoaded("#findMeARiderBTN", '#riderInfoBookingStatus', '#BookingReferenceNumber')) {
            
            const bookingNum = $('#BookingReferenceNumber').text();
            console.log("checking Booking Status for " + bookingNum);

            chkBooking();
            console.log("Starting Booking Status, starting watch");
            setInterval(chkBookingStatus(bookingNum), 2000);
            $("#findMeARiderBTN").on("click", function () {
                let chkBookingIntervalId = setInterval(chkBookingStatus(bookingNum), 2000);
                setInterval(chkBookingStatus(bookingNum), 2000);
            });

        }
    }, 3000);





    $('form#formRegistration').submit(function (e) {

        $.ajax({
            type: "POST",
            url: "_action_register_user.php",
            data: $("form#formRegistration").serialize(),
            success: function (data) {
                //alert(data);//return false;
                if (data) {
                    $("button.reset-button").click();
                    $("div.status").addClass("alert alert-success").html(data);
                }

            }
        });
        e.preventDefault();
    });


    $('#formCarRental').submit(function (e) {

        $.ajax({
            type: "POST",
            url: "ajax_process_car_rental.php",
            data: $("#formCarRental").serialize(),
            success: function (data) {
                //alert(data);//return false;

                $("div#RentalAlert").addClass("alert-success mt-3").html("<div class='spinner'></div> Processing...");
                $("div.spinner").addClass("spinner-border");

                setTimeout(function () {
                    $("div#RentalAlert").html(data);;
                }, 1000);

            }
        });
        e.preventDefault();
    });


    $('#formFindAngkas').submit(function (e) {

        $.ajax({
            type: "POST",
            url: "ajax_process_find_angkas.php",
            data: $("#formFindAngkas").serialize(),
            success: function (data) {
                if (data == '0') {
                    $("#infoAlert").removeClass("alert-warning").addClass("alert alert-success").html("Looking for a rider.").append("<div class='spinner-grow spinner-grow-sm'><span class='visually-hidden'>Thank you for your patience.</span></div>");
                } else {
                    $("#infoAlert").removeClass("alert-success").addClass("alert alert-warning").html(data);
                }
                // $("#findMeARiderBTN").append("<span class='alert alert-info'>"+data+"</span>");    

            }
        });
        e.preventDefault();
    });






    $('#topUpForm').on('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Gather form data
        const formData = {
            amount: $('#topUpAmount').val(),
        };

        // Send the data via AJAX
        $.ajax({
            url: 'ajax_top_up_wallet.php', // Target URL for top-up
            type: 'POST',
            data: formData,
            dataType: 'json', // Expecting JSON response
            success: function (response) {
                if (response.success) {
                    $('#topUpModal').modal('hide'); // Close the modal
                    loadTransactionHistory(); // Refresh the transaction history
                    //if(isElementLoaded('#WalletBalance')){
                    getWalletBalance();
                    //}
                } else {
                    alert(response.error || 'Top-up failed. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred. Please try again later.');
            }
        });
    });






});



//----------------------------angkas

// Function to get the wallet balance and update the UI

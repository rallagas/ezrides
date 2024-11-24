// Global variables
let currentPage = 1;
const pageSize = 5;
let transactions = [];

//snippets.js


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




function clog(logMsg) {
    console.log(logMsg);
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
                    <td>$${transaction.amount}</td>
                    <td>${transaction.type}</td>
                    <td>${transaction.status}</td>
                    <td>${transaction.date}</td>
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

// Change page and re-render data
function changePage(page) {
    if (page >= 1 && page <= Math.ceil(transactions.length / pageSize)) {
        currentPage = page;
        renderTransactions();
        renderPagination();
    }
}

// Make a payment
function makePayment(estimatedCost, action = null) {
    $.ajax({
        url: 'ajax_make_payment.php',
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            amount: estimatedCost,
            wallet_action: action
        }),
        contentType: 'application/json',
        success: function (response) {
            if (response.success) {
                console.log("Payment Successful.");
                $('#TransactionStatus').addClass("alert alert-success").text("Paid Total of Php " + response.amount);
                $('.btn-pay').prop('disabled', true);
                getWalletBalance();
            } else {
                console.log('Payment failed. Please try again.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Payment error:', error);
        }
    });
}

// Fetch wallet balance
function getWalletBalance() {
    $('.WalletBalance').text('Loading...');

    $.ajax({
        url: 'ajax_get_balance.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $('.WalletBalance').text(response.balance ? `Php ${response.balance}` : 'Error fetching balance');
        },
        error: function (xhr, status, error) {
            console.error('Error fetching balance:', error);
            $('.WalletBalance').text('An error occurred. Please try again.');
        }
    });
}

function updatePaymentStatus(bookingReference, newStatus) {
    $.ajax({
        type: "POST",
        url: "ajax_update_payment_status.php", // Replace with the actual URL of your PHP endpoint
        data: {
            bookingReference: bookingReference,
            newStatus: newStatus
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


// Check booking status
function chkBookingStatus(bookingId) {
    // Check if the necessary elements are loaded and have valid text content
    if (isElementLoaded('#riderInfoBookingStatus') && isElementLoaded('#BookingReferenceNumber')) {
        const bookingRef = $('#BookingReferenceNumber').text().trim();

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
                    const bookingStatusText = response.booking.booking_status_text || 'Unknown Status';
                    const paymentStatusText = response.booking.payment_status_text || 'Unpaid';

                    // Update booking status
                    $('#riderInfoBookingStatus').html(
                        bookingStatusText === 'Waiting for Driver' ?
                        `
                    <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                    <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                    <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                ` :
                        bookingStatusText
                    );
                    console.log("Start count down to update booking info.");
                    setTimeout(() => {
                        chkBooking()
                    }, 10000);
                    // Show payment button if not paid
                    if (paymentStatusText !== 'Paid' && bookingStatusText !== 'Waiting for Driver') {
                        $('#btnPayRider').removeClass('d-none');
                        console.log("Show Payment Button.");
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
                        $("#BookingInfoTable").addClass("d-none").hide();
                        $("#formFindAngkas").removeClass("d-none").show();
                        resolve(0); // Indicate no active booking
                        return;
                    }

                    // Update BookingInfoTable with current booking details
                    $("#BookingInfoTable").removeClass("d-none").show();
                    $("#formFindAngkas").addClass("d-none").hide();

                    // Populate table elements
                    $("#BookingReferenceNumber").text(booking.angkas_booking_reference || "N/A");
                    $("#BookedElaseTime").text(`${elapsedTimeInMinutes} min ago.`);
                    $("#RideEstCost").text(`Php ${booking.form_Est_Cost || "0.00"}`);
                    $("#paymentStatus").text(`( ${booking.payment_status_text || "Checking..."} )`);
                    $("#CustomerOrigin").text(booking.form_from_dest_name || "N/A");
                    $("#CustomerDestination").text(booking.form_to_dest_name || "N/A");
                    $("#riderInfoBookingStatus").text(booking.booking_status_text || "N/A");

                    $("#btnPayRider").attr('data-payment-app', booking.angkas_booking_reference);
                    // Handle payment button visibility
                    if (booking.payment_status_text === "Paid") {
                        $("#btnPayRider").addClass("d-none").hide();

                    } else {
                        $("#btnPayRider").removeClass("d-none").show();
                    }

                    // Handle driver info
                    if (booking.booking_status_text !== "Waiting for Driver") {
                        $("#riderInfoPI")
                            .html(`${booking.rider_firstname || "N/A"}, ${booking.rider_lastname || "N/A"}`);
                    } else {
                        $("#riderInfoPI").html(`
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                            <div class="spinner-grow text-danger spinner-grow-sm" role="status"></div>
                        `);
                    }

                    resolve(1); // Indicate an active booking exists
                } else {
                    // No active booking
                    $("#BookingInfoTable").addClass("d-none").hide();
                    $("#formFindAngkas").trigger('reset');
                    $("#formFindAngkas").removeClass("d-none").show();
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


let lastBookingStatus = null; // Store the last known value
let debounceTimeout = null;

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

async function handleCheckPendingBooking() {
    try {
        const response = await checkPendingBooking();
        if (response.bookingStatus) {
            console.log("Pending booking found:", response.bookingDetails);
            let BookingStatus = response.bookingDetails.booking_status;
            let bookingStatusText = "";

            switch (BookingStatus) {
                case 'P':
                    bookingStatusText = 'Waiting for Driver';
                    textColor = 'text-bg-danger';
                    break;
                case 'A':
                    bookingStatusText = 'Driver Found';
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
            let finalAmountToPay = response.bookingDetails.angkas_booking_estimated_cost;

            if (response.bookingDetails.shop_order_reference_number != null) {
                let $shopItemsReference = response.bookingDetails.shop_order_reference_number;
                finalAmountToPay += response.bookingDetails.total_amount_to_pay; //shop amount
            }
           
            // Construct the booking card view dynamically
            var bookingCardView = `
            <div class="card-header bg-warning clear-fix">
                <span class="fw-bolder" id="BookingReferenceNum">${response.bookingDetails.angkas_booking_reference}</span>
                <span class="badge ${textColor} fw-bold float-end" id="bookingStatus">${bookingStatusText}</span>
            </div>
            <div class="card-body" style="--bs-bg-opacity: .2;" id="bookingDetails">
                <div class="container-fluid">
                    <div class="row g-1">
                    
                        <div class="col-6">
                            <span class="fw-bold">Total Amount to Pay</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="totalAmountToPay">Php ${response.bookingDetails.total_amount_to_pay}</span>
                        </div>
                    
                        <div class="col-6">
                            <span class="fw-bold">ETA Duration</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="etaDuration">${response.bookingDetails.angkas_booking_eta_duration} mins</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-bold">Estimated Cost</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="estimatedCost">Php ${response.bookingDetails.angkas_booking_estimated_cost}</span>
                        </div>
                   
                        <div class="col-6">
                            <span class="fw-bold">Payment Status</span>
                        </div>
                        <div class="col-6">
                            <span class="fw-lighter" id="paymentStatus">${response.bookingDetails.payment_status}</span>
                        </div>
                    </div>
                </div>
               </div>
            </div>
            `;

            // Ensure the element with id 'bookingDetails' exists in the DOM
            $('#BookingDetails').html(bookingCardView); // Set the content to the container

        } else {
            console.log("No pending bookings.");
        }
    } catch (error) {
        console.error("Error checking booking status:", error);
    }
}



// Function to update the DOM element
function updateBookingStatus(newStatus) {
    const statusElement = $('#bookingStatus');
     switch (newStatus) {
                case 'P':
                    bookingStatusText = 'Waiting for Driver';
                    textColor = 'text-bg-danger';
                    break;
                case 'A':
                    bookingStatusText = 'Driver Found';
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
            const container = $("#BookingHistoryContent");
            container.empty(); // Clear previous content

            if (response.hasBooking && response.bookings && response.bookings.length > 0) {
                response.bookings.forEach((booking) => {
                    // Dynamically create a card for each booking
                    const defaultImage =
                        booking.customer_gender === 'M' ? '../icons/male_person1.jpg' :
                        booking.customer_gender === 'F' ? '../icons/female_person1.jpg' :
                        '../icons/male_person2.jpg';

                    const bookingCard = ` <div class="card mb-3 w-100 mx-1">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="${booking.rider_profile_image || defaultImage}" class="img-fluid rounded-start" alt="Rider Image">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
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
            $("#BookingHistoryContent").html(`<p>Failed to fetch booking information. Please try again later.</p>`);
        }
    });
}

function processAngkasBooking(data, successCallback, errorCallback) {
    $.ajax({
        type: "POST",
        url: "ajax_process_find_angkas.php",
        data: data,
        dataType: "json", // Expect JSON response
        success: function (response) {
            if (response.hasPendingBooking) {
                console.log("Pending booking exists:", response.pendingBookings);

                if (typeof successCallback === "function") {
                    successCallback(response, "pending");
                }
            } else if (response.bookingReference) {
                var bookingNum = response.bookingReference;
                console.log("New booking created with reference:", bookingNum);
                if (typeof successCallback === "function") {
                    successCallback(response, "new", bookingNum);
                }
            } else if (response.error) {
                console.error("Error:", response.message);
                if (typeof errorCallback === "function") {
                    errorCallback(response.message);
                }
            } else {
                console.warn("Unexpected response format:", response);
                if (typeof errorCallback === "function") {
                    errorCallback("Unexpected response format");
                }
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            if (typeof errorCallback === "function") {
                errorCallback(error);
            }
        }
    });
}



// Success callback function
function onSuccess(response, status, bookingNum) {
    if (status === "pending") {
        chkBooking(); // Handle pending bookings
    } else if (status === "new") {
        chkBooking(); // Update interface with booking details
        chkBookingStatus(bookingNum); // Pass booking reference
    }
}

// Error callback function
function onError(errorMessage) {
    console.error("Error occurred:", errorMessage);
}


// Document ready event
$(document).ready(function () {

    // Call the function
    handleCheckPendingBooking();
    //setInterval(fetchBookingDetails, 10000);

    $(".appMenuBtn").on("click", handleCheckPendingBooking());


    $("#LoadBookingHistory").on("click", () => {
        loadBookingInfo();
    });
    chkBooking();

    getWalletBalance();


    setInterval(() => {
        if (isElementLoaded('#BookingReferenceNumber')) {
            const bookingNum = $('#BookingReferenceNumber').text().trim();

            if (bookingNum.length > 0) { // Check if bookingNum has text (non-empty)
                //                console.log("Start watching booking status.");
                chkBookingStatus(bookingNum)

            }
        }
    }, 5000);

    //rating
    setTimeout(() => {
        if (isElementLoaded('#myRatingCustomFeedback') && isElementLoaded('#myRatingCustomFeedbackEnd') && isElementLoaded('#BookingReferenceNumber')) {
            const myRatingCustomFeedback = $('#myRatingCustomFeedback');
            const myRatingCustomFeedbackEnd = $('#myRatingCustomFeedbackEnd');
            const bookingReferenceNumberElement = $('#BookingReferenceNumber');


            // Check if the #BookingReferenceNumber has text value
            if (bookingReferenceNumberElement.length && bookingReferenceNumberElement.text().trim()) {
                const bookingReference = bookingReferenceNumberElement.text().trim();

                // Initialize default rating value (e.g., 5 for "Very Good")
                let currentRatingValue = 5;
                const labels = {
                    1: 'Very bad',
                    2: 'Bad',
                    3: 'Meh',
                    4: 'Good',
                    5: 'Very good'
                };

                // Initialize the CoreUI Rating (assuming CoreUI rating is already loaded in the page)
                const optionsCustomFeedback = {
                    value: currentRatingValue
                };
                new coreui.Rating(myRatingCustomFeedback[0], optionsCustomFeedback);

                // Update the rating label text when a rating is selected
                myRatingCustomFeedback.on('change.coreui.rating', function (event) {
                    const selectedRating = event.value;
                    clog(event.value);
                    // Show the rating text in the feedback section
                    myRatingCustomFeedbackEnd.text(labels[selectedRating]);

                    // Call the function to update the rating in the database
                    updateRatingInDatabase(bookingReference, selectedRating);
                });

                // Update the rating text on hover (show what the rating will be before confirming)
                myRatingCustomFeedback.on('hover.coreui.rating', function (event) {
                    myRatingCustomFeedbackEnd.text(event.value ? labels[event.value] : labels[currentRatingValue]);
                });
            } else {
                console.warn('Booking reference number is not available. Skipping rating update.');
            }
            chkBooking;

        }
    }, 2500);


    if (isElementLoaded('#transactionHistoryTable')) {
        const transactionTable = $('#transactionHistoryTable');

        if (transactionTable.length) {
            console.log("Transaction history table loaded. Initializing data load...");
            loadTransactionHistory();
        } else {
            console.warn("Transaction history table element is not fully loaded. Skipping data load.");
        }
    } else {
        console.warn("Transaction history table is not found. Load operation skipped.");
    }


    $('#btnPayRider').click(function (event) {
        const dataPaymentApp = $(this).attr('data-payment-app');
        event.preventDefault();
        const row = $(this).closest('tr');
        const estimatedCost = parseFloat(
            row.find('.text-secondary').text().replace('Php ', '').replace(',', '').trim()
        );

        if (!isNaN(estimatedCost) && estimatedCost > 0) {
            makePayment(estimatedCost, dataPaymentApp);
            updatePaymentStatus(dataPaymentApp, 'C');
        } else {
            console.warn("Invalid or missing estimated cost.");
        }
    });


    if (isElementLoaded(".add-destination-button", "#btnRideInfo", '#findMeARiderBTN')) {
        $(".add-destination-button").click(() => {
            chkBooking();
            $("#findMeARiderBTN").removeClass("d-none");
            $("#btnRideInfo").click();
        });
    }


    $('form#formRegistration').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "_action_register_user.php",
            data: $(this).serialize(),
            success: function (data) {
                if (data) {
                    $("button.reset-button").click();
                    $("div.status").addClass("alert alert-success").html(data);
                }
            }
        });
    });


    $('#formCarRental').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "ajax_process_car_rental.php",
            data: $(this).serialize(),
            success: function (data) {
                $("div#RentalAlert").addClass("alert-success mt-3").html("<div class='spinner'></div> Processing...");
                setTimeout(() => $("div#RentalAlert").html(data), 1000);
            }
        });
    });

    $('#formFindAngkas').submit(function (e) {
        e.preventDefault();
        var bookingStatusInterval = 0;


        let requestData = $(this).serialize();

        processAngkasBooking(requestData, onSuccess, onError);

        //        $.ajax({
        //            type: "POST",
        //            url: "ajax_process_find_angkas.php",
        //            data: $(this).serialize(),
        //            dataType: "json", // Expect JSON response
        //            success: function (response) {
        //                if (response.hasPendingBooking) {
        //                    // Handle pending bookings (if applicable)
        //                    console.log("Pending booking exists:", response.pendingBookings);
        //                    chkBooking(); // Updates the interface with current booking details
        //                } else if (response.bookingReference) {
        //                    // Use the booking reference from the JSON response
        //                    var bookingNum = response.bookingReference;
        //                    console.log("New booking created with reference:", bookingNum);
        //                    chkBooking(); // Updates the interface with new booking details
        //                    chkBookingStatus(bookingNum); // Pass booking reference to this function
        //                } else if (response.error) {
        //                    console.error("Error:", response.message);
        //                } else {
        //                    console.warn("Unexpected response format:", response);
        //                }
        //            },
        //            error: function (xhr, status, error) {
        //                console.error("AJAX Error:", status, error);
        //            }
        //        });
    });



    $('#topUpForm').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: 'ajax_top_up_wallet.php',
            type: 'POST',
            data: {
                amount: $('#topUpAmount').val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#topUpModal').modal('hide');
                    loadTransactionHistory();
                    getWalletBalance();
                } else {
                    alert(response.error || 'Top-up failed. Please try again.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again later.');
            }
        });
    });
});

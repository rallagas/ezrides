let isBookingAccepted = false; // Tracks if a booking has been accepted


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
        } else {
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

    $("#availableBookings").html(`<span class="badge text-bg-info">Current Queue: ${currentQueue}, Status: ${status} </span>`);
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
                    const card = ViewBookingCard(booking); // Use the accepted booking card view
                    $("#bookingCards").append(card);
                });
            } else {
                availableBookings.html('<div class="alert alert-info">No current bookings available.</div>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching current bookings:", error);
        }
    });
}


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
function ViewBookingCard(booking) {
    // Get the current date and time
    const currentDate = new Date();
    // Parse booking.date_booked as a Date object
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

    <!-- Action Button -->
    <div class="col-md-3 d-flex align-items-center justify-content-center">
        <a href="_current_booking_map.php" 
           class="btn shadow btn-success w-75">
           Show Location
        </a>
    </div>
</div>


    `;
}


/*Call functions upon document ready*/

$(document).ready(function() {

    // Initial data fetch
    
    
    const queueInterval = setInterval(() => {
        updateQueue();
    }, 1000); // Check every 5 seconds
    
    
});

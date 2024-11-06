$(document).ready(function() {
    let lastQueueData = null; // Variable to store the last queue data

    // Function to fetch queue data
    function fetchQueueData() {
        $.ajax({
            url: "ajax_rider_queue.php",
            dataType: "json",
            success: function(data) {
                if (data) {
                    // Compare current data with last known data
                    if (JSON.stringify(data) !== JSON.stringify(lastQueueData)) {
                        lastQueueData = data; // Update lastQueueData to current data

                        // Assign parsed data to variables
                        const currentQueue = data.current_queue;
                        const queueList = data.queue_list;
                        const status = data.status;

                        // Update the HTML with the current queue status
                        $("#availableBookings").html(`Current Queue: ${currentQueue}, Status: ${status}`);

                        // Clear previous bookings
                        $("#bookingCards").remove(); // Remove previous cards container
                        $("#availableBookings").append('<div id="bookingCards" class="col-12"></div>');

                        // Handle queueList
                        if (Array.isArray(queueList) && queueList.length > 0) {    
                            queueList.forEach(booking => {
                                // Create a card container for each booking
                                const card = createBookingCard(booking);
                                $("#bookingCards").append(card);
                            });
                        } else { 
                            // Fetch current bookings if queueList is empty
                            fetchCurrentBookings();
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching queue data:", error);
            }
        });
    }

    // Function to fetch current bookings
    function fetchCurrentBookings() {
        $.ajax({
            url: "ajax_get_booking.php",
            dataType: "json",
            success: function(data) {
                if (data && Array.isArray(data.queue_list) && data.queue_list.length > 0) {
                    $("#bookingCards").remove(); // Remove previous cards container
                    $("#availableBookings").append('<div id="bookingCards" class="col-12"></div>');
                    data.queue_list.forEach(booking => {
                        // Create a card for current booking
                        const card = ViewBookingCard(booking);
                        $("#bookingCards").append(card);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching current bookings:", error);
            }
        });
    }

    // Function to create booking card
    function ViewBookingCard(booking) {
        return `
            <div class="card col-lg-12 mb-3">
                <div class="row g-0">
                    <div class="col-md-3 d-none d-md-block position-relative">
                        <img src="../icons/${booking.user_profile_image}" class="img-fluid rounded-start position-absolute top-50 start-50 translate-middle" alt="${booking.user_firstname} ${booking.user_lastname}" style="max-height:200px">
                    </div>
                    <div class="col-md-6 col-sm-8">
                        <div class="card-header">
                            <h5 class="card-title">${booking.angkas_booking_reference}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><strong>From:</strong> ${booking.form_from_dest_name}</p>
                            <p class="card-text"><strong>To:</strong> ${booking.form_to_dest_name}</p>
                            <p class="card-text"><strong>ETA Duration:</strong> ${booking.form_ETA_duration} mins <strong>Total Distance:</strong> ${booking.form_TotalDistance} km</p>
                            <p class="card-text"><strong>Contact:</strong> ${booking.user_contact_no} (${booking.user_email_address})</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 position-relative">

                           
                            <a href="_current_booking_map.php"
                               class="show-map-btn m-1 p-5 btn btn-outline-success position-absolute top-50 start-50 translate-middle text-center align-middle">
                             Show Location
                            </a>
                    </div>
                </div>
            </div>
        `;
    }
    function createBookingCard(booking) {
        return `
            <div class="card col-lg-12 mb-3">
                <div class="row g-0">
                    <div class="col-md-3 d-none d-md-block position-relative">
                        <img src="../icons/${booking.user_profile_image}" class="img-fluid rounded-start position-absolute top-50 start-50 translate-middle" alt="${booking.user_firstname} ${booking.user_lastname}" style="max-height:200px">
                    </div>
                    <div class="col-md-6 col-sm-8">
                        <div class="card-header">
                            <h5 class="card-title">${booking.angkas_booking_reference}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><strong>From:</strong> ${booking.form_from_dest_name}</p>
                            <p class="card-text"><strong>To:</strong> ${booking.form_to_dest_name}</p>
                            <p class="card-text"><strong>ETA Duration:</strong> ${booking.form_ETA_duration} mins <strong>Total Distance:</strong> ${booking.form_TotalDistance} km</p>
                            <p class="card-text"><strong>Contact:</strong> ${booking.user_contact_no} (${booking.user_email_address})</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 position-relative">
                        <form id="map${booking.angkas_booking_reference}" class="booking_form" method="POST">
                            <input type="hidden" value="${booking.angkas_booking_reference}" name="booking_ref" />
                            <button type="submit" class="booking-btn w-100 py-5 btn btn-outline-success position-absolute top-50 start-50 translate-middle text-center align-middle">Accept Booking <br>( <strong>Php ${booking.form_Est_Cost}</strong> )</button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    
    $(document).on("submit", ".booking_form", function(e) {
        e.preventDefault(); // Prevent the form from submitting

        // Get the booking reference (or ID) from the hidden input field
        const booking_id = $(this).find('input[name="booking_ref"]').val();
        console.log(booking_id); // Log the booking ID

        $.ajax({
            type: "POST",
            url: "ajax_rider_accept_booking.php",
            data: $(this).serialize(), // Serialize the form data
            success: function(data) {
                console.log(data); // Log the response from the server
                fetchCurrentBookings();
            },
            error: function(xhr, status, error) {
                console.error("Error accepting booking:", error);
                  fetchQueueData();
            }
        });
    });
    
    
    
    // Initial data fetch
    fetchQueueData();
    setInterval(fetchQueueData, 1000);

    
    
});

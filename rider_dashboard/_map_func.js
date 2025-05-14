let map;
let currentMarker;
let destinationMarker;
let directionsService;
let directionsRenderer;
let watchId;

function createMarkerContentWithImage(src, width = '32px') {
    const img = document.createElement('img');
    img.src = src;
    img.style.width = width;
    img.style.height = 'auto'; // Optional
    return img;
}



function clog(log){
    console.log(log);
}

function initMap() {
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Example pickup location (should be fetched from your booking system)
                const pickupCoords = document.getElementById("form_to_dest").value.split(",");
                const pickupLocation = {
                    lat: parseFloat(pickupCoords[0]),
                    lng: parseFloat(pickupCoords[1])
                };

                // Initialize map
                CONFIGURATION.mapOptions.center = currentLocation;
                map = new google.maps.Map(document.getElementById("map"), CONFIGURATION.mapOptions);
                directionsRenderer.setMap(map);

                // Place marker for current location
              currentMarker = new google.maps.marker.AdvancedMarkerElement({
                  position: currentLocation,
                  map: map,
                  content: createMarkerContentWithImage('../icons/car.png', '40px')
              });
                // Place marker for customer's pickup location
                destinationMarker = new google.maps.marker.AdvancedMarkerElement({
                    position: pickupLocation,
                    map: map,
                    content: createMarkerContentWithImage('../icons/user-location.png', '40px')
                });

                // Draw route from current location to pickup location
                calculateAndDisplayRoute(currentLocation, pickupLocation);

                // Start tracking current location
           
                    watchId = navigator.geolocation.watchPosition(
                        updateCurrentLocation,
                        handleLocationError,
                    { enableHighAccuracy: true }
                    );
               
            },
            () => handleLocationError(true)
        );
    } else {
        handleLocationError(false);
    }
}



function updateCurrentLocation(position) {
    const newLocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };
    
    let distance = 0;

    // Update current marker position
    currentMarker.setMap(null); // Remove the old marker
    currentMarker = new google.maps.marker.AdvancedMarkerElement({
        position: newLocation,
        map: map,
      //  title: "Your Current Location",
         content: createMarkerContentWithImage('../icons/car.png', '40px')
    });

    // Check proximity to destination
    distance = checkProximityToDestination(newLocation);
    if (distance <= 10) {
        document.getElementById("ConfirmArrivalButton").style.display = "block";
    }

    // Update route
    const destinationCoords = document.getElementById("form_to_dest").value.split(",");
    const destinationLocation = {
        lat: parseFloat(destinationCoords[0]),
        lng: parseFloat(destinationCoords[1])
    };
    calculateAndDisplayRoute(newLocation, destinationLocation);
}

// Function to create marker content
function createMarkerContent(title) {
    const content = document.createElement("div");
    content.style.color = "blue";
    content.style.fontWeight = "bold";
    content.innerText = title;
    return content;
}


// Function to check if the user is within 10 meters of the destination
function checkProximityToDestination(currentLocation) {
    const destinationCoords = document.getElementById("form_to_dest").value.split(",");
    const destinationLocation = {
        lat: parseFloat(destinationCoords[0]),
        lng: parseFloat(destinationCoords[1])
    };

    const distance = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(currentLocation),
        new google.maps.LatLng(destinationLocation)
    );
    
    return distance;
    // Show the confirm arrival button if within 10 meters
//    if (distance <= 10) {
//        document.getElementById("ConfirmArrivalButton").style.display = "block";
//    }
}


// Function to post the updated booking status to the database
function updateBookingStatus(status , booking_id) {
    // Prepare the data with booking status and booking ID
    const data = {
        booking_status: status,
        booking_id: booking_id // Replace with the actual booking ID you want to update
    };

    // Make the AJAX request using fetch
    fetch('ajax_update_booking_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log("Booking status updated successfully in the database.");
        } else {
            console.error("Failed to update booking status:", result.error);
        }
    })
    .catch(error => {
        console.error("Error in AJAX request:", error);
    });
}

// Function to post the updated booking status to the database
function updatePaymentStatus(status , booking_id) {
    // Prepare the data with booking status and booking ID
    const data = {
        payment_status: status,
        booking_id: booking_id // Replace with the actual booking ID you want to update
    };

    // Make the AJAX request using fetch
    fetch('ajax_update_payment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log("Payment status updated successfully in the database.");
        } else {
            console.error("Failed to update booking status:", result.error);
        }
    })
    .catch(error => {
        console.error("Error in AJAX request:", error);
    });
}
function checkPaymentStatus(bookingId) {
    return new Promise((resolve, reject) => {
        fetch("ajax_get_payment_status.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ booking_id: bookingId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resolve(data.payment_status);  // Resolve with the payment status
            } else {
                reject(data.error || "Failed to retrieve payment status");
            }
        })
        .catch(error => {
            console.error("Error checking payment status:", error);
            reject(error);
        });
    });
}



document.getElementById("DropOffCustomer").addEventListener("click", () => {
    const bookingId = document.getElementById("angkas_booking_ref").value;
    const AmountToPay = document.getElementById("AmountToPay").value;
    clog("BookingId: " + bookingId );
    clog("Amount To Pay: " + AmountToPay );

    // Show the drop-off modal
    const dropOffModal = new bootstrap.Modal(document.getElementById('dropOffModal'));
    document.getElementById('dropOffMessage').textContent = "Are you sure you want to drop off the customer?";
    document.getElementById("amountToPayText").textContent = AmountToPay;

    // Hide payment confirmation section initially
    //document.getElementById("paymentSection").style.display = "none";
    document.getElementById("confirmPaymentBtn").style.display = "none";
    document.getElementById("confirmDropOffBtn").style.display = "inline-block";

    // Show the modal
    dropOffModal.show();

    // When "Confirm Drop-Off" is clicked
    document.getElementById("confirmDropOffBtn").onclick = () => {
        // Update the booking status to 'F' for drop-off
        updateBookingStatus('F', bookingId);

        // Check payment status
        checkPaymentStatus(bookingId)
            .then(paymentStatus => {
                if (paymentStatus === 'C') {
                    // If payment is already completed, skip payment confirmation
                    console.log("Payment is already completed. Skipping payment confirmation.");
                    updateBookingStatus('C', bookingId);
                    deleteRiderFromQueue();
                    window.location.href = "index.php";
                } else {
                    // Show payment confirmation section if payment is not completed
                    document.getElementById("paymentSection").style.display = "block";
                    document.getElementById("confirmDropOffBtn").style.display = "none";
                    document.getElementById("confirmPaymentBtn").style.display = "inline-block";
                    deleteRiderFromQueue();
                }
            })
            .catch(error => {
                console.error("Error checking payment status:", error);
            });
    };

    // When "Confirm Payment" is clicked
    document.getElementById("confirmPaymentBtn").onclick = () => {
        updateBookingStatus('C', bookingId);
        updatePaymentStatus('C', bookingId);

        // Call function to delete the rider from angkas_rider_queue table
        deleteRiderFromQueue();
        dropOffModal.hide();
        window.location.href = "index.php";
    };
});



// Function to delete rider from angkas_rider_queue
function deleteRiderFromQueue() {
    // Make the AJAX request to delete the rider from the queue
    fetch('delete_rider_from_queue.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log("Rider deleted successfully from angkas_rider_queue.");
        } else {
            console.error("Failed to delete rider:", result.error);
        }
    })
    .catch(error => {
        console.error("Error in AJAX request:", error);
    });
}




// Function to calculate distance between two coordinates using the Haversine formula
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371e3; // Earth radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lng2 - lng1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // Distance in meters
}

// Function to check if the car is at least 5 meters away from the pickup location
function isCarAtLeastFiveMetersAway(currentLocation, pickupLocation) {
    const distance = calculateDistance(
        currentLocation.lat, currentLocation.lng,
        pickupLocation.lat, pickupLocation.lng
    );
    return distance >= 5;
}


function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        // Check if geolocation is available
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    resolve(currentLocation); // Return the location object
                },
                (error) => {
                    reject(error); // Handle any errors that occur during geolocation
                }
            );
        } else {
            reject(new Error("Geolocation is not supported by this browser."));
        }
    });
}
// Event handler for ConfirmArrivalButton
document.getElementById("ConfirmArrivalButton").addEventListener("click", () => {
    // Get new starting location from customer's pickup coordinates
    let form_to_dest = document.getElementById("form_to_dest");
    let booking_id = document.getElementById("angkas_booking_ref");
    let form_cust_to_dest = document.getElementById("form_customer_to_dest").value;

    const pickupCoords = form_to_dest.value.split(",");
    const pickupLocation = {
        lat: parseFloat(pickupCoords[0]),
        lng: parseFloat(pickupCoords[1])
    };

    updateBookingStatus('R', booking_id.value);

    // Remove the current marker from the map
    if (currentMarker) {
        currentMarker.setMap(null);
    }

    // Create a new marker for the customer's pickup location
    currentMarker = new google.maps.marker.AdvancedMarkerElement({
        position: pickupLocation,
        map: map,
        title: "Customer's Pickup Location",
        content: createMarkerContent("Customer's Pickup Location")
    });

    // Hide confirm arrival button
    document.getElementById("DropOffCustomer").classList.remove("d-none");
    document.getElementById("ConfirmArrivalButton").classList.add("d-none");
    

    // Set intended destination (should come from another input field)
    const intendedCoords = form_cust_to_dest.split(",");
    form_to_dest.value = form_cust_to_dest;
    
    const intendedLocation = {
        lat: parseFloat(intendedCoords[0]),
        lng: parseFloat(intendedCoords[1])
    };

    // Remove the old destination marker and create a new one for the customer's intended location
    if (destinationMarker) {
        destinationMarker.setMap(null);
    }
    destinationMarker = new google.maps.marker.AdvancedMarkerElement({
        position: intendedLocation,
        map: map,
        title: "Customer's Intended Location",
        content: createMarkerContent("Customer's Intended Location")
    });

    // Calculate the new route based on the updated starting location
    calculateAndDisplayRoute(pickupLocation, intendedLocation);

    // Periodically check if the car is at least 5 meters away from the pickup location
    const intervalId = setInterval(() => {
        // Assume getCurrentLocation() fetches the car's current location (lat, lng)
        const currentLocation = getCurrentLocation();

        if (isCarAtLeastFiveMetersAway(currentLocation, pickupLocation)) {
            updateBookingStatus('I', booking_id.value);
            clearInterval(intervalId); // Stop checking after the condition is met
        }
    }, 3000); // Check every 3 seconds
});






function calculateAndDisplayRoute(currentLocation, destinationLocation) {
    const request = {
        origin: currentLocation,
        destination: destinationLocation,
        travelMode: google.maps.TravelMode[CONFIGURATION.defaultTravelMode]
    };

    directionsService.route(request, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);

            // Set estimated time and distance
            const leg = result.routes[0].legs[0];
            document.getElementById("form_ETA_duration").value = leg.duration.text;
            document.getElementById("form_TotalDistance").value = leg.distance.text;
        } else {
            console.error("Directions request failed due to " + status);
        }
    });
}

function handleLocationError(browserHasGeolocation) {
    const errorMessage = browserHasGeolocation
        ? "Error: The Geolocation service failed."
        : "Error: Your browser doesn't support geolocation.";
    console.error(errorMessage);
    CONFIGURATION.mapOptions.center = { lat: 13.1390621, lng: 123.7437995 }; // Default location
    map = new google.maps.Map(document.getElementById("map"), CONFIGURATION.mapOptions);
}

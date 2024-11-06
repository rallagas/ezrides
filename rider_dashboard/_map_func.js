let map;
let currentMarker;
let destinationMarker;
let directionsService;
let directionsRenderer;
let watchId;


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
                    title: "Your Current Location",
                    content: createMarkerContent("Your Current Location")
                });

                // Place marker for customer's pickup location
                destinationMarker = new google.maps.marker.AdvancedMarkerElement({
                    position: pickupLocation,
                    map: map,
                    title: "Customer's Pickup Location",
                    content: createMarkerContent("Customer's Pickup Location")
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

    // Update current marker position
    currentMarker.setMap(null); // Remove the old marker
    currentMarker = new google.maps.marker.AdvancedMarkerElement({
        position: newLocation,
        map: map,
        title: "Your Current Location",
        content: createMarkerContent("Your Current Location") // Create content for the marker
    });

    // Check proximity to destination
    checkProximityToDestination(newLocation);

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

    // Show the confirm arrival button if within 10 meters
    if (distance <= 10) {
        document.getElementById("ConfirmArrivalButton").style.display = "block";
    }
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

document.getElementById("DropOffCustomer").addEventListener("click", () => {
    
    const bookingId = document.getElementById("angkas_booking_ref");
    const AmountToPay = document.getElementById("AmountToPay").value;
    // Confirm with the user if they want to proceed with dropping off the customer
    const confirmDropOff = confirm("Are you sure you want to drop off the customer?");
    const confirmPayment = confirm("Did the customer Pay? <br> Amount: " + AmountToPay);
    if (confirmDropOff) {
        // Update the booking status to 'C' for completed drop-off
           updateBookingStatus('F' , bookingId.value);
        
        if(confirmPayment) {
           updateBookingStatus('C', bookingId.value);

        
        // Call function to delete the rider from angkas_rider_queue table
        deleteRiderFromQueue();
            
        window.location.href = "index.php" ;
        }
     }
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




// Event handler for ConfirmArrivalButton
document.getElementById("ConfirmArrivalButton").addEventListener("click", () => {
    // Get new starting location from customer's pickup coordinates
    
    
    let form_to_dest = document.getElementById("form_to_dest");
    
    let booking_id = document.getElementById("angkas_booking_ref");
    
    let form_cust_to_dest = document.getElementById("form_customer_to_dest").value;
    
    const pickupCoords = form_to_dest.value.split(",");
    const newStartingLocation = {
        lat: parseFloat(pickupCoords[0]),
        lng: parseFloat(pickupCoords[1])
    };

    // Remove the current marker from the map
    if (currentMarker) {
        currentMarker.setMap(null);
    }

    // Create a new marker for the customer's pickup location
    currentMarker = new google.maps.marker.AdvancedMarkerElement({
        position: newStartingLocation,
        map: map,
        title: "Customer's Pickup Location",
        content: createMarkerContent("Customer's Pickup Location")
    });

    // Hide confirm arrival button
    document.getElementById("ConfirmArrivalButton").style.display = "none";

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
    calculateAndDisplayRoute(newStartingLocation, intendedLocation);
    updateBookingStatus('R',booking_id.value);
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

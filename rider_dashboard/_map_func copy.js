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

                // Initialize map
                CONFIGURATION.mapOptions.center = currentLocation;
                map = new google.maps.Map(document.getElementById("map"), CONFIGURATION.mapOptions);
                directionsRenderer.setMap(map);

                // Place marker for current location
                currentMarker = new google.maps.Marker({
                    position: currentLocation,
                    map: map,
                    title: "Your Current Location"
                });

                // Set initial destination from form_to_dest input
                const destinationCoords = document.getElementById("form_to_dest").value.split(",");
                const destinationLocation = {
                    lat: parseFloat(destinationCoords[0]),
                    lng: parseFloat(destinationCoords[1])
                };

                destinationMarker = new google.maps.Marker({
                    position: destinationLocation,
                    map: map,
                    title: "Intended Destination"
                });

                // Draw route
                calculateAndDisplayRoute(currentLocation, destinationLocation);

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
    currentMarker.setPosition(newLocation);

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

// Event handler for ConfirmArrivalButton
document.getElementById("ConfirmArrivalButton").addEventListener("click", () => {
    // Get new starting location from form_to_dest input
    const newStartingCoords = document.getElementById("form_to_dest").value.split(",");
    const newStartingLocation = {
        lat: parseFloat(newStartingCoords[0]),
        lng: parseFloat(newStartingCoords[1])
    };

    // Update current marker to the new starting location
    currentMarker.setPosition(newStartingLocation);
    currentMarker.setTitle("Your New Starting Location");

    // Hide confirm arrival button
   // document.getElementById("ConfirmArrivalButton").style.display = "none";

    // Update route to the current destination based on the new starting location
    const destinationCoords = document.getElementById("form_customer_to_dest").value.split(",");
    const destinationLocation = {
        lat: parseFloat(destinationCoords[0]),
        lng: parseFloat(destinationCoords[1])
    };

    // Calculate the new route based on the updated starting location
    calculateAndDisplayRoute(newStartingLocation, destinationLocation);
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

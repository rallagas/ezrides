const CONFIGURATION = {
    "defaultTravelMode": "DRIVING",
    "distanceMeasurementType": "IMPERIAL",
    "mapOptions": {
        "fullscreenControl": true,
        "mapTypeControl": false,
        "streetViewControl": false,
        "zoom": 14,
        "zoomControl": true,
        "maxZoom": 20,
        "mapId": "b3394d825c3c2f44"
    },
    "mapsApiKey": "AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A"
};



function initMap() {
    // Check if the browser supports geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Get the user's current position
                const currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Update the map's center to the current location
                CONFIGURATION.mapOptions.center = currentLocation;

                // Initialize the map with the updated configuration
                new Commutes(CONFIGURATION);

                // After initializing the map, get the readable address and lat/lng
                getReadableAddress(currentLocation);
                setLatLngInputs(currentLocation);
            },
            function() {
                // Handle error in case user denies geolocation access or there's an issue
                handleLocationError(true);
            }
        );
    } else {
        // Browser doesn't support Geolocation
        handleLocationError(false);
    }
}

// Function to handle geolocation errors
function handleLocationError(browserHasGeolocation) {
    const errorMessage = browserHasGeolocation
        ? "Error: The Geolocation service failed."
        : "Error: Your browser doesn't support geolocation.";
    console.error(errorMessage);
    // Optionally, set a fallback center point if geolocation fails
    CONFIGURATION.mapOptions.center = {lat: 13.1390621, lng: 123.7437995};  // Default location
    new Commutes(CONFIGURATION);
}

// Function to get the readable address using the Geocoding API
function getReadableAddress(location) {
    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ 'location': location }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                const address = results[0].formatted_address;

                // Put the address into the input element with ID 'form_from_dest'
                document.getElementById('form_from_dest').value = address;
            } else {
                console.error('No results found');
            }
        } else {
            console.error('Geocoder failed due to: ' + status);
        }
    });
}

// Function to set latitude and longitude into input elements
function setLatLngInputs(location) {
    // Set the longitude in the input element with ID 'currentLoc_long'
    document.getElementById('currentLoc_long').value = location.lng;
    // (Optional) If you also need to store latitude in another field, for example, 'currentLoc_lat'
    document.getElementById('currentLoc_lat').value = location.lat;
}


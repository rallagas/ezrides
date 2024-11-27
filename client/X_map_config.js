/* ============================
 * CONFIGURATIONURATION
 * ============================ */

// Default CONFIGURATIONuration
const CONFIGURATION = {
  defaultTravelMode: "DRIVING",
  distanceMeasurementType: "METRIC",
  mapOptions: {
    fullscreenControl: true,
    mapTypeControl: false,
    streetViewControl: false,
    zoom: 14,
    zoomControl: true,
    maxZoom: 20,
    mapId: "b3394d825c3c2f44",
    center: { lat: 37.7749, lng: -122.4194 }, // Default San Francisco coordinates
  },
};

/* ============================
 * HELPER FUNCTIONS
 * ============================ */

// Show a message in case of Geolocation failure
function handleLocationError(errorMessage) {
  alert(errorMessage);
}

// Function to initialize the map
function initMap(mapOptions) {
  const map = new google.maps.Map(document.querySelector('.map-view'), mapOptions);
  return map;
}

// Function to get user's current location
async function getCurrentLocation() {
  if (!navigator.geolocation) {
    throw new Error("Geolocation is not supported by this browser.");
  }
  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        resolve({ lat: latitude, lng: longitude });
      },
      (error) => {
        reject("Geolocation failed. Ensure location services are enabled.");
      }
    );
  });
}

// Function to initialize autocomplete for destination input
function initAutocomplete(map) {
  const inputElement = document.querySelector('input[name="destination-address"]');
  const autocomplete = new google.maps.places.Autocomplete(inputElement, {
    bounds: map.getBounds(),
    fields: ['place_id', 'geometry', 'name'],
  });

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (place.geometry && place.geometry.location) {
      console.log("Selected Place:", place.name, place.geometry.location.toString());
      map.panTo(place.geometry.location);
    } else {
      alert("Place details not found.");
    }
  });
}

// Function to create a marker
function addMarker(map, position, title) {
  new google.maps.Marker({
    map,
    position,
    title,
  });
}

/* ============================
 * MAIN INITIALIZATION
 * ============================ */

async function initializeApp() {
  console.log("Google Maps API loaded successfully.");

  try {
    // Get user's current location
    const currentLocation = await getCurrentLocation();
    console.log("User's Current Location:", currentLocation);
    
    $('#currentLocCoor').val(currentLocation.lat+","+currentLocation.lng);
    // Update map options with user's location
    CONFIGURATION.mapOptions.center = currentLocation;
    // Initialize the map
    const map = initMap(CONFIGURATION.mapOptions);
    // Add a marker at user's current location
    addMarker(map, currentLocation, "Your Current Location");
    // Initialize autocomplete functionality
    initAutocomplete(map);

  } catch (error) {
    console.error("Error during initialization:", error);
    const fallbackMessage = "Using default location (San Francisco) as fallback.";
    handleLocationError(fallbackMessage);
    const map = initMap(CONFIGURATION.mapOptions);
    addMarker(map, CONFIGURATION.mapOptions.center, "Default Location");
  }
}

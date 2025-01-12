'use strict';

/* ============================
 * CONSTANTS & CONFIGURATION
 * ============================ */

// Default Configuration
// const config = {
//   defaultTravelMode: "DRIVING",
//   distanceMeasurementType: "METRIC",
//   mapOptions: {
//     fullscreenControl: true,
//     mapTypeControl: false,
//     streetViewControl: false,
//     zoom: 14,
//     zoomControl: true,
//     maxZoom: 20,
//     mapId: "b3394d825c3c2f44"
//   },
//   mapsApiKey: "AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs"
// };

// Constants
const MAX_NUM_DESTINATIONS = 1;
const BIAS_BOUND_DISTANCE = 0.5; // ~50 km
const HOUR_IN_SECONDS = 3600;
const MIN_IN_SECONDS = 60;

// Stroke and Marker Colors for different states
const STROKE_COLORS = {
  active: { innerStroke: '#4285F4', outerStroke: '#185ABC' },
  inactive: { innerStroke: '#BDC1C6', outerStroke: '#80868B' },
};

const MARKER_ICON_COLORS = {
  active: { fill: '#EA4335', stroke: '#C5221F', label: '#FFF' },
  inactive: { fill: '#F1F3F4', stroke: '#9AA0A6', label: '#3C4043' }
};

// Travel Modes and Destination Operations
const DestinationOperation = { ADD: 'ADD', EDIT: 'EDIT', DELETE: 'DELETE' };
const TravelMode = { DRIVING: 'DRIVING', TRANSIT: 'TRANSIT', BICYCLING: 'BICYCLING', WALKING: 'WALKING' };

/* ============================
 * DOM ELEMENT SELECTORS
 * ============================ */

const commutesEl = {
  map: document.querySelector('.map-view'),
  initialStatePanel: document.querySelector('.commutes-initial-state'),
  destinationPanel: document.querySelector('.commutes-destinations'),
  modal: document.querySelector('.commutes-modal-container'),
};

const destinationPanelEl = {
  addButton: commutesEl.destinationPanel.querySelector('.add-button'),
  container: commutesEl.destinationPanel.querySelector('.destinations-container'),
  list: commutesEl.destinationPanel.querySelector('.destination-list'),
  scrollLeftButton: commutesEl.destinationPanel.querySelector('.left-control'),
  scrollRightButton: commutesEl.destinationPanel.querySelector('.right-control'),
  getActiveDestination: () => commutesEl.destinationPanel.querySelector('.destination.active'),
};

const destinationModalEl = {
  title: commutesEl.modal.querySelector('h2'),
  form: commutesEl.modal.querySelector('form'),
  destinationInput: commutesEl.modal.querySelector('input[name="destination-address"]'),
  errorMessage: commutesEl.modal.querySelector('.error-message'),
  addButton: commutesEl.modal.querySelector('.add-destination-button'),
  deleteButton: commutesEl.modal.querySelector('.delete-destination-button'),
  editButton: commutesEl.modal.querySelector('.edit-destination-button'),
  cancelButton: commutesEl.modal.querySelector('.cancel-button'),
  getTravelModeInput: () => commutesEl.modal.querySelector('input[name="travel-mode"]:checked'),
};

/* ============================
 * HELPER FUNCTIONS
 * ============================ */

// Show and Hide Elements
function hideElement(el, focusEl) {
  el.style.display = 'none';
  if (focusEl) focusEl.focus();
}

function showElement(el, focusEl) {
  el.style.display = 'flex';
  if (focusEl) focusEl.focus();
}

// Convert miles to kilometers
function convertToKilometers(distanceText) {
  const distanceValue = parseFloat(distanceText);
  return distanceText.includes('mi') ? (distanceValue * 1.60934).toFixed(2) : distanceText;
}

// Compute cost based on distance
function computeCostByDistance(distanceText) {
  const distanceValue = parseFloat(distanceText);
  const currentHour = new Date().getHours();
  const flagDownRate = currentHour >= 18 || currentHour < 5 ? 100 : 60;
  const MIN_DISTANCE = 3.0;
  const rateAfter3KMs = 10.0;
  const totalDistance = distanceText.includes('mi') ? distanceValue * 1.60934 : distanceValue;
  return totalDistance > MIN_DISTANCE
    ? (((totalDistance - MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2)
    : ((MIN_DISTANCE * rateAfter3KMs) + flagDownRate).toFixed(2);
}

// Get Bias Bounds for Autocomplete
function getBiasBounds(latitude = 0, longitude = 0, radius = 50000) {
  const center = new google.maps.LatLng(latitude, longitude);
  const bounds = new google.maps.LatLngBounds();
  bounds.extend(google.maps.geometry.spherical.computeOffset(center, radius, 0)); // North
  bounds.extend(google.maps.geometry.spherical.computeOffset(center, radius, 180)); // South
  bounds.extend(google.maps.geometry.spherical.computeOffset(center, radius, 90)); // East
  bounds.extend(google.maps.geometry.spherical.computeOffset(center, radius, 270)); // West
  return bounds;
}
/**
 * Event handler function for scroll buttons.
 */
function handleScrollButtonClick(e) {
  const multiplier = 1.25;
  const direction = e.target.dataset.direction;
  const cardWidth = destinationPanelEl.list.firstElementChild.offsetWidth;

  destinationPanelEl.container.scrollBy(
      {left: (direction * cardWidth * multiplier), behavior: 'smooth'});
}


/* ============================
 * INITIALIZATION FUNCTIONS
 * ============================ */

// Initialize Map
function initMap() {
  if (!config || !config.mapOptions) {
    throw new Error("Map options are not defined in the configuration.");
  }
  const commutesMap = new google.maps.Map(commutesEl.map, config.mapOptions);
  return { commutesMap };
}

// Initialize Destination Panel Listeners
function initDestinationPanelListeners() {
  destinationPanelEl.addButton.addEventListener('click', () => {
    destinationModalEl.title.textContent = 'Add Destination';
    showModal(destinationModalEl.addButton);
  });

  destinationPanelEl.scrollLeftButton.addEventListener('click', handleScrollButtonClick);
  destinationPanelEl.scrollRightButton.addEventListener('click', handleScrollButtonClick);
}

// Initialize Modal with Autocomplete for destinations
function initModal() {
  const autocomplete = new google.maps.places.Autocomplete(destinationModalEl.destinationInput, {
    bounds: getBiasBounds(coordinates.lat, coordinates.lng, 50000), // 50 km radius
    fields: ['place_id', 'geometry', 'name'],
  });

  let destinationToAdd = null;
  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (place.geometry && place.geometry.location) {
      destinationToAdd = place;
    }
  });

  destinationModalEl.addButton.addEventListener('click', () => {
    if (destinationToAdd) {
      const travelMode = destinationModalEl.getTravelModeInput().value;
      addDestination(destinationToAdd, travelMode);
    }
  });
}

/* ============================
 * CORE FUNCTIONALITIES
 * ============================ */

// Add new destination
function addDestination(destination, travelMode) {
  const destinationConfig = {
    name: destination.name,
    placeId: destination.place_id,
    travelMode,
    distance: convertToKilometers('10 mi'), // Example distance
    cost: computeCostByDistance('10 mi'),   // Example cost
  };
  updateDestinationPanel(destinationConfig);
}

// Update the destination panel with a new destination
function updateDestinationPanel(destination) {
  const template = generateDestinationTemplate(destination);
  destinationPanelEl.list.insertAdjacentHTML('beforeend', template);
}

// Generate HTML for the destination template
function generateDestinationTemplate(destination) {
  return `
    <div class="destination">
      <h5>${destination.name}</h5>
      <p>Distance: ${destination.distance} km</p>
      <p>Estimated Cost: Php ${destination.cost}</p>
    </div>
  `;
}

/* ============================
 * CURRENT LOCATION FUNCTION
 * ============================ */

// Get the current location and update fields
async function GetCurrentLocation2() {

  if (!navigator.geolocation) {
    throw new Error("Geolocation is not supported by this browser.");
  }

  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        const geocoder = new google.maps.Geocoder();
        const latlng = new google.maps.LatLng(latitude, longitude);

        geocoder.geocode({ location: latlng }, (results, status) => {
          if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
              const address = results[0].formatted_address;
           
              resolve({
                address: address,
                coordinates: { lat: latitude, lng: longitude }
              });
            } else {
              reject(new Error("No address found."));
            }
          } else {
            reject(new Error("Geocoder failed due to: " + status));
          }
        });



      },
      (error) => {
        handleLocationError(true);
        reject(error);
      },
      {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
      }
    );
  });
}

// Handle Geolocation Errors
function handleLocationError(isGeolocationError) {
  const errorMessage = isGeolocationError
    ? "Geolocation service failed. Please enable location services in your browser."
    : "Your browser does not support geolocation.";
  alert(errorMessage);
}

/* ============================
 * START APPLICATION
 * ============================ */


document.addEventListener('DOMContentLoaded', async () => {
  try {
    const currentLocation = await GetCurrentLocation2();

    const { coordinates } = currentLocation;
    // Define configuration with current location
    const configuration = {
      mapOptions: {
        center: { lat: coordinates.lat, lng: coordinates.lng },
        zoom: 12,
      },
    };

    if (!configuration.mapOptions) {
      throw new Error("Map options are not defined.");
    }

    // Initialize Map and components
    const { commutesMap } = initMap(configuration);
    initDestinationPanelListeners();
    initModal();

  } catch (error) {
    console.error("Error fetching current location:", error.message);

    // Fallback to default location if there's an error
    const fallbackConfig = {
      mapOptions: {
        center: { lat: 37.7749, lng: -122.4194 }, // Default location (San Francisco)
        zoom: 12,
      },
    };

    if (!fallbackConfig.mapOptions) {
      throw new Error("Fallback map options are not defined.");
    }

    const { commutesMap } = initMap(fallbackConfig);
    initDestinationPanelListeners();
    initModal();
  }
});



'use strict';

/**
 * Element selectors for commutes widget.
 */
const commutesEl = {
  map: document.querySelector('.map-view'),
  initialStatePanel: document.querySelector('.commutes-initial-state'),
  destinationPanel: document.querySelector('.commutes-destinations'),
  modal: document.querySelector('.commutes-modal-container'),
};

/** 
 * Element selectors for commutes destination panel.
 */
const destinationPanelEl = {
  addButton: commutesEl.destinationPanel.querySelector('.add-button'),
  container: commutesEl.destinationPanel.querySelector('.destinations-container'),
  list: commutesEl.destinationPanel.querySelector('.destination-list'),
  scrollLeftButton: commutesEl.destinationPanel.querySelector('.left-control'),
  scrollRightButton: commutesEl.destinationPanel.querySelector('.right-control'),
  getActiveDestination: () => commutesEl.destinationPanel.querySelector('.destination.active'),
};

/**
 * Element selectors for commutes modal popup.
 */
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

/**
 * Max number of destination allowed to be added to commutes panel.
 */
const MAX_NUM_DESTINATIONS = 1;

/**
 * Bounds to bias search within ~50km distance.
 */
const BIAS_BOUND_DISTANCE = 0.5;

/**
 * Hour in seconds.
 */
const HOUR_IN_SECONDS = 3600;

/**
 * Minutes in seconds.
 */
const MIN_IN_SECONDS = 60;

/**
 * Stroke colors for destination direction polylines for different states.
 */
const STROKE_COLORS = {
  active: {
    innerStroke: '#4285F4',
    outerStroke: '#185ABC',
  },
  inactive: {
    innerStroke: '#BDC1C6',
    outerStroke: '#80868B',
  },
};

/**
 * Marker icon colors for different states.
 */
const MARKER_ICON_COLORS = {
  active: {
    fill: '#EA4335',
    stroke: '#C5221F',
    label: '#FFF',
  },
  inactive: {
    fill: '#F1F3F4',
    stroke: '#9AA0A6',
    label: '#3C4043',
  },
};

/**
 * List of operations to perform on destinations.
 */
const DestinationOperation = {
  ADD: 'ADD',
  EDIT: 'EDIT',
  DELETE: 'DELETE',
};

/**
 * List of available commutes travel mode.
 */
const TravelMode = {
  DRIVING: 'DRIVING',
  TRANSIT: 'TRANSIT',
  BICYCLING: 'BICYCLING',
  WALKING: 'WALKING',
};

/**
 * Defines instance of Commutes widget to be instantiated when Map library
 * loads.
 */

function setDestinationLatLng(location) {
    
        // Log the lat/lng values for debugging
    console.log('Longitude:', location.lng());
    console.log('Latitude:', location.lat());
    // Set longitude and latitude values in the respective input fields
    document.getElementById('formToDest_long').value = location.lng();
    document.getElementById('formToDest_lat').value = location.lat();
}


function Commutes(configuration) {
  let commutesMap;
  let activeDestinationIndex;
  let origin = configuration.mapOptions.center;
  let destinations = configuration.destination || [];
  let markerIndex = 0;
  let lastActiveEl;
  const markerIconConfig = {
    path:
        'M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6',
    fillOpacity: 1,
    strokeWeight: 1,
    anchor: new google.maps.Point(15, 29),
    scale: 1.2,
    labelOrigin: new google.maps.Point(10, 9),
  };
  const originMarkerIcon = {
    ...markerIconConfig,
    fillColor: MARKER_ICON_COLORS.active.fill,
    strokeColor: MARKER_ICON_COLORS.active.stroke,
  };
  const destinationMarkerIcon = {
    ...markerIconConfig,
    fillColor: MARKER_ICON_COLORS.inactive.fill,
    strokeColor: MARKER_ICON_COLORS.inactive.stroke,
  };
  const bikeLayer = new google.maps.BicyclingLayer();
  const publicTransitLayer = new google.maps.TransitLayer();

  initMapView();
  initDestinations();
  initCommutesPanel();
  initCommutesModal();
    

  /**
   * Initializes map view on commutes widget.
   */
  function initMapView() {
    const mapOptionConfig = configuration.mapOptions;
    commutesMap = new google.maps.Map(commutesEl.map, mapOptionConfig);

    configuration.defaultTravelModeEnum =
        parseTravelModeEnum(configuration.defaultTravelMode);
    setTravelModeLayer(configuration.defaultTravelModeEnum);
   createMarker(origin,null);
   
  }

  /**
   * Initializes commutes widget with destinations info if provided with a list
   * of initial destinations and update view.
   */
  function initDestinations() {
    if (!configuration.initialDestinations) return;
    let callbackCounter = 0;
    const placesService = new google.maps.places.PlacesService(commutesMap);
    for (const destination of configuration.initialDestinations) {
      destination.travelModeEnum = parseTravelModeEnum(destination.travelMode);
      const label = getNextMarkerLabel();
      const request = {
        placeId: destination.placeId,
        fields: ['place_id', 'geometry', 'name'],
      };
      placesService.getDetails(
          request,
          function(place) {
            if (!place.geometry || !place.geometry.location) return;
            const travelModeEnum =
                destination.travelModeEnum || configuration.defaultTravelModeEnum;
            const destinationConfig =
                createDestinationConfig(place, travelModeEnum, label);
              
              
            getDirections(destinationConfig).then((response) => {
              if (!response) return;
              destinations.push(destinationConfig);
              getCommutesInfo(response, destinationConfig);
              callbackCounter++;
              // Update commutes panel and click event objects after getting
              // direction to all destinations.
              if (callbackCounter === configuration.initialDestinations.length) {
                destinations.sort(function(a, b) {
                  return a.label < b.label ? -1 : 1;
                });
                let bounds = new google.maps.LatLngBounds();
                for (let i = 0; i < destinations.length; i++) {
                  assignMapObjectListeners(destinations[i], i);
                  updateCommutesPanel(destinations[i], i, DestinationOperation.ADD);
                  bounds.union(destinations[i].bounds);
                }
                const lastIndex = destinations.length - 1;
                handleRouteClick(destinations[lastIndex], lastIndex);
                commutesMap.fitBounds(bounds);
              }
            });
          },
          () => {
            console.error('Failed to retrieve places info due to ' + e);
          });
    }
  }

  /**
   * Initializes the bottom panel for updating map view and displaying commutes
   * info.
   */
  function initCommutesPanel() {
    const addCommutesButtonEls = document.querySelectorAll('.add-button');
    addCommutesButtonEls.forEach(addButton => {
      addButton.addEventListener('click', () => {
        destinationModalEl.title.innerHTML = 'Add destination';
        hideElement(destinationModalEl.deleteButton);
        hideElement(destinationModalEl.editButton);
        showElement(destinationModalEl.addButton);
        showModal();
        const travelModeEnum = configuration.defaultTravelModeEnum || TravelMode.DRIVING;
        const travelModeId = travelModeEnum.toLowerCase() + '-mode';
        document.forms['destination-form'][travelModeId].checked = true;
          
        
        
      });
    });

    destinationPanelEl.scrollLeftButton.addEventListener(
        'click', handleScrollButtonClick);
    destinationPanelEl.scrollRightButton.addEventListener(
        'click', handleScrollButtonClick);
    destinationPanelEl.list.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' &&
          e.target !== destinationPanelEl.getActiveDestination()) {
        e.target.click();
        e.preventDefault();
      }
    });
  }

  /**
   * Initializes commutes modal to gathering destination inputs. Configures the
   * event target listeners to update view and behaviors on the modal.
   */
  function initCommutesModal() {
    const boundConfig = {
      north: origin.lat + BIAS_BOUND_DISTANCE,
      south: origin.lat - BIAS_BOUND_DISTANCE,
      east: origin.lng + BIAS_BOUND_DISTANCE,
      west: origin.lng - BIAS_BOUND_DISTANCE,
    };

    const destinationFormReset = function() {
      destinationModalEl.destinationInput.classList.remove('error');
      destinationModalEl.errorMessage.innerHTML = '';
      destinationModalEl.form.reset();
      destinationToAdd = null;
    };

    const autocompleteOptions = {
      bounds: boundConfig,
      fields: ['place_id', 'geometry', 'name'],
    };
    const autocomplete = new google.maps.places.Autocomplete(
        destinationModalEl.destinationInput, autocompleteOptions);
    let destinationToAdd;
    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (!place.geometry || !place.geometry.location) {
        return;
      } else {
        destinationToAdd = place;
        destinationModalEl.getTravelModeInput().focus();
      }
      destinationModalEl.destinationInput.classList.remove('error');
      destinationModalEl.errorMessage.innerHTML = '';
    });

/*Added Long Lat value*/
destinationModalEl.addButton.addEventListener('click', () => {
    const isValidInput = validateDestinationInput(destinationToAdd);
    if (!isValidInput) return;

    const selectedTravelMode = destinationModalEl.getTravelModeInput().value;

    // Add this line to set longitude and latitude in the input fields
    // Ensure the coordinates are extracted correctly
    if (destinationToAdd) {
        console.log('Destination object:', destinationToAdd); // Log destination object

        if (destinationToAdd.geometry && destinationToAdd.geometry.location) {
            console.log('Location object:', destinationToAdd.geometry.location); // Log location
            
            // Make sure lat() and lng() methods are available
            setDestinationLatLng(destinationToAdd.geometry.location);
        } else {
            console.error('Geometry or location is not defined.');
        }
    } else {
        console.error('Destination is not defined.');
    }

    addDestinationToList(destinationToAdd, selectedTravelMode);
    destinationFormReset();
    hideModal();
});


    destinationModalEl.editButton.addEventListener('click', () => {
      const destination = {...destinations[activeDestinationIndex]};
      const selectedTravelMode = destinationModalEl.getTravelModeInput().value;
      const isSameDestination =
          destination.name === destinationModalEl.destinationInput.value;
      const isSameTravelMode = destination.travelModeEnum === selectedTravelMode;
      if (isSameDestination && isSameTravelMode) {
        hideModal();
        return;
      }
      if (!isSameDestination) {
        const isValidInput = validateDestinationInput(destinationToAdd);
        if (!isValidInput) return;
        destination.name = destinationToAdd.name;
        destination.place_id = destinationToAdd.place_id;
        destination.url = generateMapsUrl(destinationToAdd, selectedTravelMode);
      }
      if (!isSameTravelMode) {
        destination.travelModeEnum = selectedTravelMode;
        destination.url = generateMapsUrl(destination, selectedTravelMode);
      }
      destinationFormReset();
      getDirections(destination)
          .then((response) => {
            if (!response) return;
            const currentIndex = activeDestinationIndex;
            // Remove current active direction before replacing it with updated
            // routes.
            removeDirectionsFromMapView(destination);
            destinations[activeDestinationIndex] = destination;
            getCommutesInfo(response, destination);
            assignMapObjectListeners(destination, activeDestinationIndex);
            updateCommutesPanel(
                destination, activeDestinationIndex, DestinationOperation.EDIT);
            handleRouteClick(destination, activeDestinationIndex);
            const newEditButton = destinationPanelEl.list.children
                .item(activeDestinationIndex)
                .querySelector('.edit-button');
            newEditButton.focus();
          })
          .catch((e) => console.error('Editing directions failed due to ' + e));
      hideModal();
    });

    destinationModalEl.cancelButton.addEventListener('click', () => {
      destinationFormReset();
      hideModal();
    });

    destinationModalEl.deleteButton.addEventListener('click', () => {
      removeDirectionsFromMapView(destinations[activeDestinationIndex]);
      updateCommutesPanel(
          destinations[activeDestinationIndex], activeDestinationIndex,
          DestinationOperation.DELETE);
      activeDestinationIndex = undefined;
      destinationFormReset();
      let elToFocus;
      if (destinations.length) {
        const lastIndex = destinations.length - 1;
        handleRouteClick(destinations[lastIndex], lastIndex);
        elToFocus = destinationPanelEl.getActiveDestination();
      } 
        else {
        elToFocus = commutesEl.initialStatePanel.querySelector('.add-button');
      }
      hideModal(elToFocus);
    });

    window.onmousedown = function(event) {
      if (event.target === commutesEl.modal) {
        destinationFormReset();
        hideModal();
      }
    };

    commutesEl.modal.addEventListener('keydown', (e) => {
      switch(e.key) {
        case 'Enter':
          if (e.target === destinationModalEl.cancelButton ||
              e.target === destinationModalEl.deleteButton) {
            return;
          }
          if (destinationModalEl.addButton.style.display !== 'none') {
            destinationModalEl.addButton.click();
          } else if (destinationModalEl.editButton.style.display !== 'none') {
            destinationModalEl.editButton.click();
          }
          break;
        case "Esc":
        case "Escape":
          hideModal();
          break;
        default:
          return;
      }
      e.preventDefault();
    });

    // Trap focus in the modal so that tabbing on the last interactive element
    // focuses on the first, and shift-tabbing on the first interactive element
    // focuses on the last.

    const firstInteractiveElement = destinationModalEl.destinationInput;
    const lastInteractiveElements = [
      destinationModalEl.addButton,
      destinationModalEl.editButton,
    ];

    firstInteractiveElement.addEventListener(
        'keydown', handleFirstInteractiveElementTab);
    for (const el of lastInteractiveElements) {
      el.addEventListener('keydown', handleLastInteractiveElementTab);
    }

    function handleFirstInteractiveElementTab(event) {
      if (event.key === 'Tab' && event.shiftKey) {
        for (const el of lastInteractiveElements) {
          if (el.style.display !== 'none') {
            event.preventDefault();
            el.focus();
            return;
          }
        }
      }
    }

    function handleLastInteractiveElementTab(event) {
      if (event.key === 'Tab' && !event.shiftKey) {
        event.preventDefault();
        firstInteractiveElement.focus();
      }
    }
  }

  /**
   * Checks if destination input is valid and ensure no duplicate places or more
   * than max number places are added.
   */
  function validateDestinationInput(destinationToAdd) {
    let errorMessage;
    let isValidInput = false;
    if (!destinationToAdd) {
      errorMessage = 'No details available for destination input';
    } else if (destinations.length > MAX_NUM_DESTINATIONS) {
      errorMessage =
          'Cannot add more than ' + MAX_NUM_DESTINATIONS + ' destinations';
    } else if (
        destinations &&
        destinations.find(
            destination =>
                destination.place_id === destinationToAdd.place_id)) {
      errorMessage = 'Destination is already added';
    } else {
      isValidInput = true;
    }
    if (!isValidInput) {
      destinationModalEl.errorMessage.innerHTML = errorMessage;
      destinationModalEl.destinationInput.classList.add('error');
    }
    return isValidInput;
  }

  /**
   * Removes polylines and markers of currently active directions.
   */
  function removeDirectionsFromMapView(destination) {
    destination.polylines.innerStroke.setMap(null);
    destination.polylines.outerStroke.setMap(null);
    destination.marker.setMap(null);
  }

  /**
   * Generates destination card template, attach event target listeners, and
   * adds template to destination list depending on the operations:
   * - add new destination card template to the end of the list on add.
   * - replace destination card template for current selected on edit.
   * - do nothing on default or delete.
   */
  function buildDestinationCardTemplate(
      destination, destinationIdx, destinationOperation) {
    let editButtonEl;
    switch (destinationOperation) {
      case DestinationOperation.ADD:
            destinationPanelEl.list.insertAdjacentHTML(
                'beforeend',
                '<div class="destination-container">' +
                    generateDestinationTemplate(destination) + '</div>'
            );
            const destinationContainerEl = destinationPanelEl.list.lastElementChild;
            destinationContainerEl.addEventListener('click', () => {
              handleRouteClick(destination, destinationIdx);
            });
            editButtonEl = destinationContainerEl.querySelector('.edit-button');
            destinationPanelEl.container.scrollLeft =
                destinationPanelEl.container.scrollWidth;
            break;
      case DestinationOperation.EDIT:
        const activeDestinationContainerEl =
            destinationPanelEl.getActiveDestination().parentElement;
        activeDestinationContainerEl.innerHTML = generateDestinationTemplate(destination);
        activeDestinationContainerEl.addEventListener('click', () => {
          handleRouteClick(destination, destinationIdx);
        });
        editButtonEl = activeDestinationContainerEl.querySelector('.edit-button');
        break;
      case DestinationOperation.DELETE:
      default:
    }

    editButtonEl.addEventListener('click', () => {
      destinationModalEl.title.innerHTML = 'Edit destination';
      destinationModalEl.destinationInput.value = destination.name;
      showElement(destinationModalEl.deleteButton);
      showElement(destinationModalEl.editButton);
      hideElement(destinationModalEl.addButton);
      showModal();
      const travelModeId = destination.travelModeEnum.toLowerCase() + '-mode';
      document.forms['destination-form'][travelModeId].checked = true;
      // Update the autocomplete widget as if it was user input.
      destinationModalEl.destinationInput.dispatchEvent(new Event('input'));
    });
  }

  /**
   * Updates view of commutes panel depending on the operation:
   * - build/update destination template if add or edit.
   * - remove destination from destination list and rebuild template.
   */
  function updateCommutesPanel(
      destination, destinationIdx, destinationOperation) {
    switch (destinationOperation) {
      case DestinationOperation.ADD:
        hideElement(commutesEl.initialStatePanel);
        showElement(commutesEl.destinationPanel);
        // fall through
      case DestinationOperation.EDIT:
        buildDestinationCardTemplate(
            destination, destinationIdx, destinationOperation);
        break;
      case DestinationOperation.DELETE:
        destinations.splice(destinationIdx, 1);
        destinationPanelEl.list.innerHTML = '';
        for (let i = 0; i < destinations.length; i++) {
          buildDestinationCardTemplate(
              destinations[i], i, DestinationOperation.ADD);
          assignMapObjectListeners(destinations[i], i);
        }
      default:
    }
    if (!destinations.length) {
      showElement(commutesEl.initialStatePanel, commutesEl.initialStatePanel);
      hideElement(commutesEl.destinationPanel);
      activeDestinationIndex = undefined;
      return;
    }
    destinationPanelEl.container.addEventListener('scroll', handlePanelScroll);
    destinationPanelEl.container.dispatchEvent(new Event('scroll'));
  }

  /**
   * Adds new destination to the list and get directions and commutes info.
   */
//function addDestinationToList(destinationToAdd, travelModeEnum) {
//    const destinationConfig = createDestinationConfig(destinationToAdd, travelModeEnum);
//    const newDestinationIndex = destinations.length;
//
//    getDirections(destinationConfig)
//        .then((response) => {
//            if (!response) return;
//
//            destinations.push(destinationConfig);
//            getCommutesInfo(response, destinationConfig);
//
//            // Assign map object listeners after confirming `content` exists
//            if (destinationConfig.marker && destinationConfig.marker.content) {
//                assignMapObjectListeners(destinationConfig, newDestinationIndex);
//            } else {
//                console.error("Marker content is not defined.");
//            }
//
//            updateCommutesPanel(destinationConfig, newDestinationIndex, DestinationOperation.ADD);
//            handleRouteClick(destinationConfig, newDestinationIndex);
//            destinationPanelEl.addButton.focus();
//        })
//        .catch((e) => console.error('Adding destination failed due to ' + e));
//}

function addDestinationToList(destinationToAdd, travelModeEnum) {
    const destinationConfig = createDestinationConfig(destinationToAdd, travelModeEnum);
    const newDestinationIndex = destinations.length;

    getDirections(destinationConfig)
        .then((response) => {
            if (!response) return;

            destinations.push(destinationConfig);
            getCommutesInfo(response, destinationConfig);

            // Ensure marker content is initialized
            if (!destinationConfig.marker.content) {
                destinationConfig.marker.content = createMarkerIconSVG(destinationConfig.label);
            }

            // Add map object listeners if marker and content are initialized
            if (destinationConfig.marker && destinationConfig.marker.content) {
                assignMapObjectListeners(destinationConfig, newDestinationIndex);
            } else {
                console.error("Marker content is not defined.");
            }

            updateCommutesPanel(destinationConfig, newDestinationIndex, DestinationOperation.ADD);
            handleRouteClick(destinationConfig, newDestinationIndex);

            // Safely call focus on addButton if it exists
//            if (destinationPanelEl.addButton) {
//                destinationPanelEl.addButton.focus();
//            } else {
//                console.warn("addButton is not defined, focus skipped.");
//            }
        })
        .catch((e) => console.error('Adding destination failed due to ' + e));
}

    
    // Helper function to create SVG for marker icon with a label
// Function to create a custom SVG marker icon with a specific label
function createMarkerIconSVG(label) {
    const svgIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svgIcon.setAttribute("width", "30");
    svgIcon.setAttribute("height", "30");
    svgIcon.setAttribute("fill", MARKER_ICON_COLORS.inactive.label);
    svgIcon.setAttribute("class", "text-danger");
    svgIcon.setAttribute("viewBox", "0 0 16 16");

    // First path element for the main shape of the marker
    const path1 = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path1.setAttribute("d", "M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10");
    svgIcon.appendChild(path1);

    // Second path element for the circle marker center
    const path2 = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path2.setAttribute("d", "M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6");
    svgIcon.appendChild(path2);

    return svgIcon;
}


  /**
   * Returns a new marker label on each call. Marker labels are the capital
   * letters of the alphabet in order.
   */
  function getNextMarkerLabel() {
    const markerLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const label = markerLabels[markerIndex];
    markerIndex = (markerIndex + 1) % markerLabels.length;
    return label;
  }

  /**
   * Creates a destination config object from the given data. The label argument
   * is optional; a new label will be generated if not provided.
   */
function createDestinationConfig(destinationToAdd, travelModeEnum, label) {
    const destinationConfig = {
        name: destinationToAdd.name,
        place_id: destinationToAdd.place_id,
        label: label || getNextMarkerLabel(),
        travelModeEnum: travelModeEnum,
        url: generateMapsUrl(destinationToAdd, travelModeEnum),
        marker: destinationToAdd.marker || {
            content: createMarkerIconSVG(label || getNextMarkerLabel()), // Assign default SVG content
            color: MARKER_ICON_COLORS.inactive.label, // Ensure color is initialized
            position: null  // Placeholder for AdvancedMarker position if needed
        }
    };
    return destinationConfig;
}


  /**
   * Gets directions to destination from origin, add route to map view, and
   * update commutes panel with distance and directions info.
   */
  function getDirections(destination) {
    const request = {
      origin: origin,
      destination: {'placeId': destination.place_id},
      travelMode: destination.travelModeEnum,
      unitSystem: configuration.distanceMeasurementType === 'METRIC' ?
              google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL,
    };
    const directionsService = new google.maps.DirectionsService();
    return directionsService.route(request).then(response => {
      return response;
    });
  }

  /**
   * Adds route polyline, marker, and commutes info to map and destinations
   * list.
   */
  function getCommutesInfo(directionResponse, destination) {
    if (!directionResponse) return;
    const path = directionResponse.routes[0].overview_path;
    const bounds = directionResponse.routes[0].bounds;
    const directionLeg = directionResponse.routes[0].legs[0];
    const destinationLocation = directionLeg.end_location;
    const distance = directionLeg.distance.text;
//    const duration = convertDurationValueAsString(directionLeg.duration.value);
    const duration =   (directionLeg.duration.value / 60).toFixed(2); //in minutes

    const innerStroke = new google.maps.Polyline({
      path: path,
      strokeColor: STROKE_COLORS.inactive.innerStroke,
      strokeOpacity: 1.0,
      strokeWeight: 3,
      zIndex: 10
    });

    const outerStroke = new google.maps.Polyline({
      path: path,
      strokeColor: STROKE_COLORS.inactive.outerStroke,
      strokeOpacity: 1.0,
      strokeWeight: 6,
      zIndex: 1
    });

    const marker = createMarker(destinationLocation, destination.label);
          //  updateMarkerColor(marker, "red");  // Update the label color to red


    innerStroke.setMap(commutesMap);
    outerStroke.setMap(commutesMap);

    destination.distance = distance;
    destination.duration = duration;
    destination.marker = marker;
    destination.polylines = {innerStroke, outerStroke};
    destination.bounds = bounds;
  }

  /**
   * Assigns event target listeners to map objects of corresponding destination
   * index.
   */
  function assignMapObjectListeners(destination, destinationIdx) {
    google.maps.event.clearListeners(destination.marker, 'click');

    google.maps.event.addListener(destination.marker, 'click', () => {
      handleRouteClick(destination, destinationIdx);
      destinationPanelEl.list.querySelectorAll('.destination')[destinationIdx].focus();
    });
    google.maps.event.addListener(destination.marker, 'mouseover', () => {
      changeMapObjectStrokeWeight(destination, true);
    });
    google.maps.event.addListener(destination.marker, 'mouseout', () => {
      changeMapObjectStrokeWeight(destination, false);
    });
    for (const strokeLine in destination.polylines) {
      google.maps.event.clearListeners(destination.polylines[strokeLine], 'click');
      google.maps.event.clearListeners(destination.polylines[strokeLine], 'mouseover');

      google.maps.event.addListener(destination.polylines[strokeLine], 'click', () => {
        handleRouteClick(destination, destinationIdx);
        destinationPanelEl.list.querySelectorAll('.destination')[destinationIdx].focus();
      });
      google.maps.event.addListener(destination.polylines[strokeLine], 'mouseover', () => {
        changeMapObjectStrokeWeight(destination, true);
      });
      google.maps.event.addListener(destination.polylines[strokeLine], 'mouseout', () => {
        changeMapObjectStrokeWeight(destination, false);
      });
    }
  }

  /**
   * Generates the Google Map url for direction from origin to destination with
   * corresponding travel mode.
   */
  function generateMapsUrl(destination, travelModeEnum) {
    let googleMapsUrl = 'https://www.google.com/maps/dir/?api=1';
    googleMapsUrl += `&origin=${origin.lat},${origin.lng}`;
    googleMapsUrl += '&destination=' + encodeURIComponent(destination.name) +
        '&destination_place_id=' + destination.place_id;
    googleMapsUrl += '&travelmode=' + travelModeEnum.toLowerCase();
    return googleMapsUrl;
  }

/**
 * Handles changes to destination polyline and marker content style.
 */
function changeMapObjectStrokeWeight(destination, mouseOver) {
    if (destination.marker && destination.marker.content) { // Check that content exists
        if (mouseOver) {
            destination.polylines.outerStroke.setOptions({ strokeWeight: 8 });
            destination.marker.content.style.color = "#C5221F"; // Change text color on hover
        } else {
            destination.polylines.outerStroke.setOptions({ strokeWeight: 6 });
            destination.marker.content.style.color = "#9AA0A6"; // Reset color
        }
    } else {
        console.error("Marker content is not defined.");
    }
}



  /**
   * Handles route clicks. Originally active routes are set to inactive
   * states. Newly selected route's map polyline/marker objects and destination
   * template are assigned active class styling and coloring.
   */
function handleRouteClick(destination, destinationIdx) {
    if (activeDestinationIndex !== undefined) {
        // Set currently active stroke to inactive
        destinations[activeDestinationIndex].polylines.innerStroke.setOptions({
            strokeColor: STROKE_COLORS.inactive.innerStroke,
            zIndex: 2
        });
        destinations[activeDestinationIndex].polylines.outerStroke.setOptions({
            strokeColor: STROKE_COLORS.inactive.outerStroke,
            zIndex: 1
        });

        // Ensure `marker` and `content` exist before updating color
        const activeMarker = destinations[activeDestinationIndex].marker;
        if (activeMarker && activeMarker.content) {
            activeMarker.content.setAttribute("fill", MARKER_ICON_COLORS.inactive.label);
        } else {
            console.error("Active marker or its content is not defined.");
        }

        // Remove styling of the current active destination
        const activeDestinationEl = destinationPanelEl.getActiveDestination();
        if (activeDestinationEl) {
            activeDestinationEl.classList.remove('active');
        }
    }
      // Set the new active destination
    activeDestinationIndex = destinationIdx;

    setTravelModeLayer(destination.travelModeEnum);
    // Add active class to the new destination element
    const newDestinationEl = destinationPanelEl.list.querySelectorAll('.destination')[destinationIdx];
    newDestinationEl.classList.add('active');
    newDestinationEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Make the new active route stroke color and marker color active
    destination.polylines.innerStroke.setOptions({
        strokeColor: STROKE_COLORS.active.innerStroke,
        zIndex: 101
    });
    destination.polylines.outerStroke.setOptions({
        strokeColor: STROKE_COLORS.active.outerStroke,
        zIndex: 99
    });

    // Update the marker color if it has content
    if (destination.marker && destination.marker.content) {
        destination.marker.content.setAttribute("fill", MARKER_ICON_COLORS.active.label);
    } else {
        console.error("Destination marker or its content is not defined.");
    }

    commutesMap.fitBounds(destination.bounds);
}
/**
 * Generates new marker based on location and label.
 */

/**
 * Creates a marker using google.maps.marker.AdvancedMarkerElement.
 */
// Adjust createMarker to handle undefined properties
function createMarker(location, label) {
    const isOrigin = label === undefined;
    const labelColor = isOrigin ? MARKER_ICON_COLORS.active.label : MARKER_ICON_COLORS.inactive.label;
    const labelText = isOrigin ? '●' : label;

    // Create SVG content for marker
    const svgIcon = createMarkerIconSVG(labelText);
    svgIcon.setAttribute("fill", labelColor);

    const marker = new google.maps.marker.AdvancedMarkerElement({
        position: location,
        map: commutesMap,
        content: svgIcon  // Set SVG as content
    });

    return marker;
}




  /**
  * Returns a TravelMode enum parsed from the input string, or null if no match is found.
  */
  function parseTravelModeEnum(travelModeString) {
    switch (travelModeString) {
      case 'DRIVING':
        return TravelMode.DRIVING;
      case 'BICYCLING':
        return TravelMode.BICYCLING;
//      case 'PUBLIC_TRANSIT':
//        return TravelMode.TRANSIT;
//      case 'WALKING':
//        return TravelMode.WALKING;
      default:
        return null;
    }
  }

  /**
   * Sets map layer depending on the chosen travel mode.
   */
  function setTravelModeLayer(travelModeEnum) {
    switch (travelModeEnum) {
      case TravelMode.BICYCLING:
        publicTransitLayer.setMap(null);
        bikeLayer.setMap(commutesMap);
        break;
      case TravelMode.TRANSIT:
        bikeLayer.setMap(null);
        publicTransitLayer.setMap(commutesMap);
        break;
      default:
        publicTransitLayer.setMap(null);
        bikeLayer.setMap(null);
    }
  }

  /**
   * Convert time from durationValue in seconds into readable string text.
   */
  function convertDurationValueAsString(durationValue) {
    if (!durationValue) {
      return '';
    }
    if (durationValue < MIN_IN_SECONDS) {
      return '<1 min';
    }
    if (durationValue > HOUR_IN_SECONDS * 10) {
      return '10+ hours';
    }
    const hours = Math.floor(durationValue / HOUR_IN_SECONDS);
    const minutes = Math.floor(durationValue % HOUR_IN_SECONDS / 60);
    const hoursString = hours > 0 ? hours + ' h' : '';
    const minutesString = minutes > 0 ? minutes + ' min' : '';
    const spacer = hoursString && minutesString ? ' ' : '';
    return hoursString + spacer + minutesString;
  }

  /**
   * Shows the destination modal window, saving a reference to the currently
   * focused element so that focus can be restored by hideModal().
   */
  function showModal() {
    lastActiveEl = document.activeElement;
    showElement(commutesEl.modal, destinationModalEl.destinationInput);
  }

  /**
   * Hides the destination modal window, setting focus to focusEl if provided.
   * If no argument is passed, focus is restored to where it was when
   * showModal() was called.
   */
  function hideModal(focusEl) {
    hideElement(commutesEl.modal, focusEl || lastActiveEl);
  }
}

/**
 * Hides a DOM element and optionally focuses on focusEl.
 */
function hideElement(el, focusEl) {
  el.style.display = 'none';
  if (focusEl) focusEl.focus();
}

/**
 * Shows a DOM element that has been hidden and optionally focuses on focusEl.
 */
function showElement(el, focusEl) {
  el.style.display = 'flex';
  if (focusEl) focusEl.focus();
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

/**
 * Event handler on scroll to add scroll buttons only if scroll width is larger
 * than width. Hide scroll buttons if scrolled to the start or end of the panel.
 */
function handlePanelScroll() {
  const position = destinationPanelEl.container.scrollLeft;
  const scrollWidth = destinationPanelEl.container.scrollWidth;
  const width = destinationPanelEl.container.offsetWidth;

  if (scrollWidth > width) {
    if (position === 0) {
      destinationPanelEl.scrollLeftButton.classList.remove('visible');
    } else {
      destinationPanelEl.scrollLeftButton.classList.add('visible');
    }

    if (Math.ceil(position + width) >= scrollWidth) {
      destinationPanelEl.scrollRightButton.classList.remove('visible');
    } else {
      destinationPanelEl.scrollRightButton.classList.add('visible');
    }
  }
}

/**
 * Generates new destination template based on destination info properties.
 */

function generateDestinationTemplate(destination) {
  const travelModeIconTemplate = '<use href="#commutes-' +
      destination.travelModeEnum.toLowerCase() + '-icon"/>';

  // Function to convert distance to kilometers
  function convertToKilometers(distanceText) {
    const distanceValue = parseFloat(distanceText); // Extract numeric value
    if (distanceText.includes('mi')) {
      // Convert miles to kilometers
      return (distanceValue * 1.60934).toFixed(2);
    }
    return distanceText; // Assume already in kilometers
  }

  // Function to compute cost by distance
  function computeCostbyDistance(distanceText) {
    const distanceValue = parseFloat(distanceText); // Extract numeric value
    const currentDate = new Date();
    const currentHour = currentDate.getHours();
    let flagDownRate = 0.00;  // Changed from const to let for reassignment

    // Set flag down rate based on time (6PM to 5AM)
    if (currentHour >= 18 || currentHour < 5) {
      flagDownRate = 100;
    } else {
      flagDownRate = 60;
    }

    const rateAfter3KMs = 10.00;
    const MIN_DISTANCE = 3.00;

    // Convert distance if in miles and compute the cost
    if (distanceText.includes('mi')) {
        if(distanceValue * 1.60934 > 3){
            return ((((distanceValue * 1.60934) - MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);   
        }
        else{
            return (((MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);   
        }
    }
    // If already in kilometers, subtract minimum distance and calculate cost
    return (((distanceValue - MIN_DISTANCE) * rateAfter3KMs) + flagDownRate).toFixed(2);
  }

  // Set the duration and converted distance in the input fields
  if (document.getElementById('form_ETA_duration')) {
    document.getElementById('form_ETA_duration').value = destination.duration;
  }

  if (document.getElementById('form_TotalDistance')) {
    const distanceInKm = convertToKilometers(destination.distance);
    document.getElementById('form_TotalDistance').value = distanceInKm;
  }

  if (document.getElementById('form_Est_Cost')) {
    const estCost = computeCostbyDistance(destination.distance);
    document.getElementById('form_Est_Cost').value = estCost;
  }

  // Set destination name in form_to_dest
  if (document.getElementById('form_to_dest')) {
    document.getElementById('form_to_dest').value = destination.name;
  }


  // Return the updated template
  return `
    <div class="destination" tabindex="0" role="button">
      <div class="destination-content">
        <div class="metadata">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              ${travelModeIconTemplate}
          </svg>

  <div class="destination-eta"> ${convertToKilometers(destination.distance)}</div>
         
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <use href="#commutes-arrow-icon"/>
          </svg>
          <span class="location-marker">${destination.label}</span>
            <div class="address">To
                <abbr title="${destination.name}">${destination.name}</abbr>
                <div class="destination-eta">ETA ${destination.duration} mins</div>
            </div>
        </div>
        
      
      </div>
    </div>

    <div class="destination-controls">
      <button class="edit-button" aria-label="Edit Destination">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <use href="#commutes-edit-icon"/>
        </svg>
        Edit
      </button>
    </div>`;
}




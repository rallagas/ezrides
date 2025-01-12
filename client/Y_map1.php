<!DOCTYPE html>
<html>
<head>
  <title>Map Example - Directions</title>
</head>
<body>
  <div id="map" style="width: 100%; height: 400px;"></div>

  <label for="to_location">To:</label>
  <input type="text" id="to_location" placeholder="Enter destination">
  <br>

  <button id="calculate_button">Calculate Directions</button>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&libraries=places,routes,directions,drawing,marker&map_ids=f8a5e937724a0990" async defer></script>
<script>
  $(document).ready(async function() {
      
      
function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: 13.1357, lng: 123.7879},
    zoom: 8,
    mapId: 'f8a5e937724a0990' // Add your Map ID here
  });
}
      
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

      initMap();
//    var map = new google.maps.Map($("#map")[0], {
//      center: {lat: 13.1357, lng: 123.7879}, // Initial center
//      zoom: 13
//    });

    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer({
      map: map
    });

    // Create a drawing manager
    var drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode: google.maps.drawing.OverlayType.MARKER,
      // Other options as needed
    });
    drawingManager.setMap(map);

    // Handle marker placement
    google.maps.event.addListener(drawingManager, 'markercomplete', function(event) {
      console.log('Marker placed');

      marker = new AdvancedMarkerElement({
        position: event.latLng,
        map: map // Assuming 'map' is your Google Map instance
      });

      // Check if the marker is defined before accessing its properties
      if (event && event.marker) {
        marker = event.marker; // Store the marker

        // Now you can safely access marker.getPosition()
        var markerPosition = marker.getPosition().toString();
        console.log('Marker position:', markerPosition);
        calculateDirections(markerPosition, $('#to_location').val());
      } else {
        console.error("Marker not defined in the event.");
        // Handle the case where the marker is not defined (optional)
      }
    });

    $('#calculate_button').click(function() {
      console.log('Calculate button clicked');
      if (marker) { // Ensure a marker has been placed
        var fromLocation = marker.getPosition().toString();
        var toLocation = $('#to_location').val();
        calculateDirections(fromLocation, toLocation);
      } else {
        alert("Please place a marker on the map first.");
      }
    });
  });

  function calculateDirections(fromLocation, toLocation) {
    console.log('Calculating directions:', fromLocation, 'to', toLocation);
    directionsService.route({
      origin: fromLocation,
      destination: toLocation,
      travelMode: 'DRIVING' // Change as needed
    }, function(response, status) {
      if (status === 'OK') {
        directionsRenderer.setDirections(response);

        // Extract and display distance and time
        var distance = response.routes[0].legs[0].distance.value / 1000; // Convert meters to kilometers
        var duration = response.routes[0].legs[0].duration.value / 60; // Convert seconds to minutes
        console.log('Distance:', distance.toFixed(2) + ' km');
        console.log('Time:', duration.toFixed(2) + ' minutes');
      } else {
        console.error('Directions request failed:', status);
      }
    });
  }
</script>
</html>
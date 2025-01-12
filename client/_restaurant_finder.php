<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="_restaurant_finder.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs&callback=initMap&libraries=places,geometry&solution_channel=GMP_QB_neighborhooddiscovery_v3_cADEF&loading=async" async defer></script>

    <script src="https://ajax.googleapis.com/ajax/libs/handlebars/4.7.7/handlebars.min.js"></script>
    <script src="_restaurant_finder.js"></script>
    <script>
        const CONFIGURATION = {
            "capabilities": {
                "search": true,
                "distances": false,
                "directions": false,
                "contacts": true,
                "atmospheres": true,
                "thumbnails": true
            },
            "pois": [], // Initially empty, will be populated dynamically
            "mapRadius": 50000,
            "mapOptions": {
                "center": {
                    "lat": 13.2523425,
                    "lng": 123.5378081
                }, // Default center
                "fullscreenControl": true,
                "mapTypeControl": true,
                "streetViewControl": false,
                "zoom": 16,
                "zoomControl": true,
                "maxZoom": 20,
                "mapId": ""
            },
            "mapsApiKey": "AIzaSyBWi3uSAaNEmBLrAdLt--kMWsoN4lKm9Hs"
        };

        let map, service;

        function initMap() {
            // Create the map centered at a default location initially.
            map = new google.maps.Map(document.getElementById("map"), CONFIGURATION.mapOptions);

            // Try to get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(currentLocation);

                    // Initialize the places service
                    service = new google.maps.places.PlacesService(map);

                    // Perform the nearby search
                    fetchNearbyPlaces(currentLocation);
                });
            } else {
                console.error("Geolocation not supported by this browser.");
            }
        }

        function fetchNearbyPlaces(location) {
            service.nearbySearch({
                location: location,
                radius: CONFIGURATION.mapRadius,
                type: ["grocery_or_supermarket"] // Set place types as per your requirement
            }, (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    // Update CONFIGURATION.pois with results
                    CONFIGURATION.pois = results.map(place => ({
                        placeId: place.place_id
                    }));

                    // Display markers on the map
                    results.forEach(place => {
                        const marker = new google.maps.Marker({
                            map,
                            position: place.geometry.location,
                            title: place.name
                        });
                    });

                    // Initialize NeighborhoodDiscovery with the updated CONFIGURATION
                    new NeighborhoodDiscovery(CONFIGURATION);

                    console.log("Nearby places added to CONFIGURATION:", CONFIGURATION.pois);
                } else {
                    console.error("Nearby search failed:", status);
                }
            });
        }
//        function fetchNearbyPlaces(location) {
//            const placeTypes = ["grocery_or_supermarket"];
//            let allResults = [];
//            let pendingRequests = placeTypes.length; // Track pending requests
//
//            placeTypes.forEach(type => {
//                service.nearbySearch({
//                    location: location,
//                    radius: CONFIGURATION.mapRadius,
//                    type: type
//                }, (results, status) => {
//                    if (status === google.maps.places.PlacesServiceStatus.OK) {
//                        allResults = allResults.concat(results);
//                        console.log(`Results for ${type}:`, results); // Debugging line to verify results per type
//
//                        // Create markers immediately for each result in this type
//                        results.forEach(place => {
//                            const location = place.geometry.location;
//
//                            if (!location || typeof location.lat !== 'function' || typeof location.lng !== 'function') {
//                                console.error("Invalid location format:", location);
//                                return;
//                            }
//
//                            const marker = new google.maps.Marker({
//                                map,
//                                position: location,
//                                title: place.name
//                            });
//                        });
//                    } else {
//                        console.error(`Nearby search failed for type ${type}:`, status);
//                    }
//
//                    // Decrement pending requests and initialize NeighborhoodDiscovery once complete
//                    pendingRequests--;
//                    if (pendingRequests === 0) {
//                        CONFIGURATION.pois = allResults.map(place => ({
//                            placeId: place.place_id
//                        }));
//                        new NeighborhoodDiscovery(CONFIGURATION);
//                        console.log("All nearby places added to CONFIGURATION:", CONFIGURATION.pois);
//                    }
//                });
//            });
//        }


    </script>
    <script id="nd-place-results-tmpl" type="text/x-handlebars-template">
        {{#each places}}
            <li class="place-result">
                <div class="text">
                    <button class="name">{{name}}</button>
                    <div class="info">
                        {{#if rating}}
                            <span>{{rating}}</span>
                            <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star/v15/24px.svg" alt="rating" class="star-icon" />
                        {{/if}}
                        {{#if numReviews}}
                            <span>&nbsp;({{numReviews}})</span>
                        {{/if}}
                        {{#if priceLevel}}
                            &#183;&nbsp;<span>{{#each dollarSigns}}${{/each}}&nbsp;</span>
                        {{/if}}
                    </div>
                    <div class="info">{{type}}</div>
                </div>
                <button class="photo" style="background-image:url({{photos.0.urlSmall}})" aria-label="show photo in viewer"></button>
            </li>
        {{/each}}
    </script>
    <script id="nd-place-details-tmpl" type="text/x-handlebars-template">
        <div class="navbar">
        <button class="back-button">
          <img class="icon" src="https://fonts.gstatic.com/s/i/googlematerialicons/arrow_back/v11/24px.svg" alt="back"/>
          Back
        </button>
      </div>
      <header>
        <h2>{{name}}</h2>
        <div class="info">
          {{#if rating}}
            <span class="star-rating-numeric">{{rating}}</span>
            <span>
              {{#each fullStarIcons}}
                <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star/v15/24px.svg" alt="full star" class="star-icon"/>
              {{/each}}
              {{#each halfStarIcons}}
                <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star_half/v17/24px.svg" alt="half star" class="star-icon"/>
              {{/each}}
              {{#each emptyStarIcons}}
                <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star_outline/v9/24px.svg" alt="empty star" class="star-icon"/>
              {{/each}}
            </span>
          {{/if}}
          {{#if numReviews}}
            <a href="{{url}}" target="_blank">{{numReviews}} reviews</a>
          {{else}}
            <a href="{{url}}" target="_blank">See on Google Maps</a>
          {{/if}}
          {{#if priceLevel}}
            &#183;
            <span class="price-dollars">
              {{#each dollarSigns}}${{/each}}
            </span>
          {{/if}}
        </div>
        {{#if type}}
          <div class="info">{{type}}</div>
        {{/if}}
      </header>
      <div class="section">
        {{#if address}}
          <div class="contact">
            <img src="https://fonts.gstatic.com/s/i/googlematerialicons/place/v10/24px.svg" alt="Address" class="icon"/>
            <div class="text">
              {{address}}
            </div>
          </div>
        {{/if}}
        {{#if website}}
          <div class="contact">
            <img src="https://fonts.gstatic.com/s/i/googlematerialicons/public/v10/24px.svg" alt="Website" class="icon"/>
            <div class="text">
              <a href="{{website}}" target="_blank">{{websiteDomain}}</a>
            </div>
          </div>
        {{/if}}
        {{#if phoneNumber}}
          <div class="contact">
            <img src="https://fonts.gstatic.com/s/i/googlematerialicons/phone/v10/24px.svg" alt="Phone number" class="icon"/>
            <div class="text">
              {{phoneNumber}}
            </div>
          </div>
        {{/if}}
        {{#if openingHours}}
          <div class="contact">
            <img src="https://fonts.gstatic.com/s/i/googlematerialicons/schedule/v12/24px.svg" alt="Opening hours" class="icon"/>
            <div class="text">
              {{#each openingHours}}
                <div>
                  <span class="weekday">{{days}}</span>
                  <span class="hours">{{hours}}</span>
                </div>
              {{/each}}
            </div>
          </div>
        {{/if}}
      </div>
      {{#if photos}}
        <div class="photos section">
          {{#each photos}}
            <button class="photo" style="background-image:url({{urlSmall}})" aria-label="show photo in viewer"></button>
          {{/each}}
        </div>
      {{/if}}
      {{#if reviews}}
        <div class="reviews section">
          <p class="attribution">Reviews by Google users</p>
          {{#each reviews}}
            <div class="review">
              <a class="reviewer-identity" href="{{author_url}}" target="_blank">
                <div class="reviewer-avatar" style="background-image:url({{profile_photo_url}})"></div>
                <div class="reviewer-name">{{author_name}}</div>
              </a>
              <div class="rating info">
                <span>
                  {{#each fullStarIcons}}
                    <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star/v15/24px.svg" alt="full star" class="star-icon"/>
                  {{/each}}
                  {{#each halfStarIcons}}
                    <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star_half/v17/24px.svg" alt="half star" class="star-icon"/>
                  {{/each}}
                  {{#each emptyStarIcons}}
                    <img src="https://fonts.gstatic.com/s/i/googlematerialicons/star_outline/v9/24px.svg" alt="empty star" class="star-icon"/>
                  {{/each}}
                </span>
                <span class="review-time">{{relative_time_description}}</span>
              </div>
              <div class="info">{{text}}</div>
            </div>
          {{/each}}
        </div>
      {{/if}}
      {{#if html_attributions}}
        <div class="section">
          {{#each html_attributions}}
            <p class="attribution">{{{this}}}</p>
          {{/each}}
        </div>
      {{/if}}
    </script>
</head>

<body>
    <div class="neighborhood-discovery">
        <div class="places-panel panel no-scroll">
            <header class="navbar">
                <div class="search-input">
                    <input class="place-search-input" placeholder="Search nearby places">
                    <button class="place-search-button">
                        <img src="https://fonts.gstatic.com/s/i/googlematerialicons/search/v11/24px.svg" alt="search" />
                    </button>
                </div>
            </header>
            <div class="results">
                <ul class="place-results-list"></ul>
            </div>
            <button class="show-more-button sticky">
                <span>Show More</span>
                <img class="right" src="https://fonts.gstatic.com/s/i/googlematerialicons/expand_more/v11/24px.svg" alt="expand" />
            </button>
        </div>
        <div class="details-panel panel"></div>
        <div class="map" id="map"></div>
        <div class="photo-modal">
            <img alt="place photo" />
            <div>
                <button class="back-button">
                    <img class="icon" src="https://fonts.gstatic.com/s/i/googlematerialicons/arrow_back/v11/24px.svg" alt="back" />
                </button>
                <div class="photo-text">
                    <div class="photo-place"></div>
                    <div class="photo-attrs">Photo by <span></span></div>
                </div>
            </div>
        </div>
        <svg class="marker-pin" xmlns="http://www.w3.org/2000/svg" width="26" height="38" fill="none">
            <path d="M13 0C5.817 0 0 5.93 0 13.267c0 7.862 5.59 10.81 9.555 17.624C12.09 35.248 11.342 38 13 38c1.723 0 .975-2.817 3.445-7.043C20.085 24.503 26 21.162 26 13.267 26 5.93 20.183 0 13 0Z" />
        </svg>
    </div>

</body>


</html>
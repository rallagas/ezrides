<!--
    Copyright 2023 Google LLC

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

        https://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
  -->
<!DOCTYPE html>
<html>
  <head>
    <title>Address Selection</title>
    <style>
      body {
        margin: 0;
      }

      .sb-title {
        position: relative;
        top: -12px;
        font-family: Roboto, sans-serif;
        font-weight: 500;
      }

      .sb-title-icon {
        position: relative;
        top: -5px;
      }

      gmpx-split-layout {
        height: 1000px;
        width: 100%;
      }

      gmpx-split-layout:not(:defined) {
        visibility: hidden;
      }

      .panel {
        background: white;
        box-sizing: border-box;
        height: 100%;
        width: 100%;
        padding: 20px;
       /* display: flex; */
        flex-direction: column;
        justify-content: space-around;
      }

      .half-input-container {
        display: flex;
        justify-content: space-between;
      }

      .half-input {
        max-width: 120px;
      }

      h2 {
        margin: 0;
        font-family: Roboto, sans-serif;
      }

      input {
        height: 30px;
      }

      input {
        border: 0;
        border-bottom: 1px solid black;
        font-size: 14px;
        font-family: Roboto, sans-serif;
        font-style: normal;
        font-weight: normal;
      }

      input:focus::placeholder {
        color: white;
      }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">   

    <script type="module">
      "use strict";

      // This loads helper components from the Extended Component Library,
      // https://github.com/googlemaps/extended-component-library.
      // Please note unpkg.com is unaffiliated with Google Maps Platform.
      import {APILoader} from 'https://unpkg.com/@googlemaps/extended-component-library@0.6';

      const CONFIGURATION = {
        "ctaTitle": "Find",
        "mapOptions": {"center":{"lat":37.4221,"lng":-122.0841},"fullscreenControl":true,"mapTypeControl":false,"streetViewControl":true,"zoom":11,"zoomControl":true,"maxZoom":22,"mapId":""},
        "mapsApiKey": "AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A",
        "capabilities": {"addressAutocompleteControl":true,"mapDisplayControl":true,"ctaControl":true}
      };

      const SHORT_NAME_ADDRESS_COMPONENT_TYPES =
          new Set(['street_number', 'administrative_area_level_1', 'postal_code']);

      const ADDRESS_COMPONENT_TYPES_IN_FORM = [
        'location',
        'locality',
        'administrative_area_level_1',
        'postal_code',
        'country',
      ];

      function getFormInputElement(componentType) {
        return document.getElementById(`${componentType}-input`);
      }

      function fillInAddress(place) {
        function getComponentName(componentType) {
          for (const component of place.address_components || []) {
            if (component.types[0] === componentType) {
              return SHORT_NAME_ADDRESS_COMPONENT_TYPES.has(componentType) ?
                  component.short_name :
                  component.long_name;
            }
          }
          return '';
        }

        function getComponentText(componentType) {
          return (componentType === 'location') ?
              `${getComponentName('street_number')} ${getComponentName('route')}` :
              getComponentName(componentType);
        }

        for (const componentType of ADDRESS_COMPONENT_TYPES_IN_FORM) {
          getFormInputElement(componentType).value = getComponentText(componentType);
        }
      }

      function renderAddress(place) {
        const mapEl = document.querySelector('gmp-map');
        const markerEl = document.querySelector('gmp-advanced-marker');

        if (place.geometry && place.geometry.location) {
          mapEl.center = place.geometry.location;
          markerEl.position = place.geometry.location;
        } else {
          markerEl.position = null;
        }
      }

      async function initMap() {
        const {Autocomplete} = await APILoader.importLibrary('places');

        const mapOptions = CONFIGURATION.mapOptions;
        mapOptions.mapId = mapOptions.mapId || 'DEMO_MAP_ID';
        mapOptions.center = mapOptions.center || {lat: 37.4221, lng: -122.0841};

        await customElements.whenDefined('gmp-map');
        document.querySelector('gmp-map').innerMap.setOptions(mapOptions);
        const autocomplete = new Autocomplete(getFormInputElement('location'), {
          fields: ['address_components', 'geometry', 'name'],
          types: ['address'],
        });

        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert(`No details available for input: '${place.name}'`);
            return;
          }
          renderAddress(place);
          fillInAddress(place);
        });
      }

      initMap();
    </script>
  </head>
  <body>
    <gmpx-api-loader key="AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A" solution-channel="GMP_QB_addressselection_v3_cABC">
    </gmpx-api-loader>
    <gmpx-split-layout row-layout-min-width="600">
      <div class="panel card" slot="fixed">
        <div>
          <img class="sb-title-icon" src="https://fonts.gstatic.com/s/i/googlematerialicons/location_pin/v5/24px.svg" alt="">
          <span class="sb-title">Address Selection</span>
        </div>
        <input type="text" placeholder="Address" id="location-input" class="form-control mb-3"/>
        <input type="text" placeholder="Apt, Suite, etc (optional)" class="form-control mb-3"/>
        <input type="text" placeholder="City" id="locality-input" class="form-control mb-3"/>
        <div class="half-input-container">
          <input type="text" class="half-input form-control mb-3" placeholder="State/Province" id="administrative_area_level_1-input" />
          <input type="text" class="half-input form-control mb-3" placeholder="Zip/Postal code" id="postal_code-input"/>
        </div>
        <input type="text" placeholder="Country" id="country-input" class="form-control mb-3"/>
        <gmpx-icon-button variant="filled">Find</gmpx-icon-button>
      </div>
      <gmp-map slot="main">
        <gmp-advanced-marker></gmp-advanced-marker>
      </gmp-map>
    </gmpx-split-layout>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVFQWjxhNqHJ8VZN9lmOUWCwS2y9Nbg5zBY1RZ5dBjD/jdkXYo3z" crossorigin="anonymous"></script>
  
  </body>
</html>
/**
 * @file
 * Court Finder autocomplete.
 */
((Drupal, once) => {

  console.log('test');

  let placesAutocompleteInput;
  let placesDropdown;
  let debounceTimer;
  let sessionToken;

  /**
   * Initializes Court Finder autocomplete.
   *
   * @param {Element} courtFinder - The Court Finder block.
   */
  function init(courtFinder) {

    placesAutocompleteInput = courtFinder.querySelector('.court-finder-block__form--location .court-finder-block__input');
    placesDropdown = courtFinder.querySelector('.court-finder-block__dropdown');

    placesAutocompleteInput.addEventListener('keyup', (e) => {

      // Clear the existing timer to reset the debounce.
      clearTimeout(debounceTimer);

      const enteredText = placesAutocompleteInput.value;

      if (enteredText.length < 2) {
        return;
      }

      if (!sessionToken) {
        // Generate a new session token only if not already generated.
        sessionToken = new google.maps.places.AutocompleteSessionToken();
      }

      // Set a new timer to make the API call after a delay (e.g., 500 milliseconds)
      debounceTimer = setTimeout(() => {
        // Make the API request
        const autocompleteService = new google.maps.places.AutocompleteService();
        // Set options (based on the requirements).
        // Restrict results to the US and its territories.
        const options = {
          input: enteredText,
          types: ['geocode'],
          sessionToken: sessionToken,
          componentRestrictions: {
            country: ['us', 'gu', 'pr', 'mp', 'vi']
          },
        };

        autocompleteService.getPlacePredictions(options, (predictions, status) => {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            // Clear existing dropdown options and add an empty placeholder.
            placesDropdown.innerHTML = '<option value="" disabled selected>Select a location</option>';

            // Populate dropdown with suggestions
            predictions.forEach((prediction) => {
              const option = document.createElement('option');
              option.value = prediction.description;
              option.text = prediction.description;
              option.setAttribute('data-place-id', prediction.place_id);
              placesDropdown.appendChild(option);
            });
          } else {
            console.error('Places Autocomplete API request failed:', status);
          }
        });
      }, 500); // Adjust the delay (in milliseconds) as needed.

    });

    // Event listener for dropdown selection.
    placesDropdown.addEventListener('change', () => {

      const selectedOption = placesDropdown.options[placesDropdown.selectedIndex];

      if (selectedOption) {
        const placeId = selectedOption.getAttribute('data-place-id');

        // Make a Place Details API call to get geometry.
        const placesService = new google.maps.places.PlacesService(document.createElement('div'));
        placesService.getDetails({
            placeId,
            sessionToken: sessionToken
          },
          (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
              const latitude = place.geometry.location.lat();
              const longitude = place.geometry.location.lng();
              const formattedAddress = place.formatted_address;

              // Reset the session token after user selection.
              sessionToken = null;

              // Redirect to the Court Finder page.
              const redirectUrl = `/federal-court-finder/find?field_geofield[distance][from]=10000&field_geofield[value]=${latitude},${longitude}&location=${encodeURIComponent(formattedAddress)}`;
              window.location.href = redirectUrl;

            } else {
              console.error('Place Details API request failed:', status);
            }
          }
        );
      }
    });
  }

  Drupal.behaviors.placesAutocomplete = {
    attach(context) {
      once('places-autocomplete', '.court-finder-block', context).forEach(init);
    },
  };

})(Drupal, once);

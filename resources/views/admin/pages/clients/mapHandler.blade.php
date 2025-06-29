<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
        defer></script>

<script>

let formMap;
        let marker;
        let lat = 24.7136;
        let lng = 46.6753;




        window.initAutocomplete = initMap2;

        function initMap2() {
            const latInput = document.getElementById('lat_order_hidden');
            const lngInput = document.getElementById('long_order_hidden');

            lat = 24.7136;
            lng = 46.6753;

            const initialLocation = {
                lat: lat,
                lng: lng
            };

            formMap = new google.maps.Map(document.getElementById("formMap"), {
                center: initialLocation,
                zoom: 13,
                mapTypeId: "roadmap",
            });

            marker = new google.maps.Marker({
                position: initialLocation,
                map: formMap
            });

            // Extract Lat/Lng from Google Maps URL
            function extractLatLngFromLink(link) {
                const regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
                const match = link.match(regex);
                if (match) {
                    const extractedLat = parseFloat(match[1]);
                    const extractedLng = parseFloat(match[2]);
                    return {
                        lat: extractedLat,
                        lng: extractedLng
                    };
                }
                return null;
            }

            // Function to update the map with new lat/lng
            function updateMap(lat, lng) {
                const newPosition = {
                    lat: lat,
                    lng: lng
                };
                formMap.setCenter(newPosition);
                if (marker) marker.setMap(null);

                marker = new google.maps.Marker({
                    position: newPosition,
                    map: formMap
                });
                console.log(marker);


                // Update the input fields
                // latInput.value = lat;
                // lngInput.value = lng;
            }


            // Handle shortened Google Maps URLs
            function resolveShortUrl(shortUrl) {
                $.ajax({
                    url: '{{ route('resolve-url') }}',
                    method: 'POST',
                    data: {
                        url: shortUrl,
                        _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
                    },
                    success: function(response) {
                        // Check if the response has lat and lng
                        if (response.lat && response.lng) {
                            // Log the resolved coordinates for debugging
                            console.log("Resolved coordinates:", response.lat, response.lng);
                            const lat = parseFloat(response.lat);
                            const lng = parseFloat(response.lng);
                            updateMap(lat, lng);

                            // Update the map with resolved lat/lng
                        } else {
                            alert('Unable to resolve the shortened URL.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while resolving URL.');
                    }
                });
            }

            // Search by link functionality
            const searchLinkInput = document.getElementById('search-link');
            searchLinkInput.addEventListener('change', function() {
                const mapLink = searchLinkInput.value;

                if (mapLink.includes('goo.gl')) {
                    // If it's a shortened URL, resolve it first
                    console.log('Shortened URL detected:', mapLink);
                    resolveShortUrl(mapLink); // No need for a callback; handle it in the success function
                } else {
                    // Handle full Google Maps links directly
                    const extractedLatLng = extractLatLngFromLink(mapLink);
                    if (extractedLatLng) {
                        console.log("Extracted coordinates from full URL:", extractedLatLng);
                        updateMap(extractedLatLng.lat, extractedLatLng
                            .lng); // Update the map with extracted lat/lng
                    } else {
                        alert('Invalid Google Maps link format.');
                    }
                }
            });


            // Click event on map to update lat/lng inputs and place a marker
            formMap.addListener('click', function(event) {
                lat = event.latLng.lat();
                lng = event.latLng.lng();

                if (marker) marker.setMap(null);

                marker = new google.maps.Marker({
                    position: {
                        lat: lat,
                        lng: lng
                    },
                    map: formMap
                });

                // Update the lat/lng input fields when clicking the map
                latInput.value = lat;
                lngInput.value = lng;
            });

            // Add event listeners to lat/lng inputs to update the map when values change
            function updateMapByLatLng() {
                const newLat = parseFloat(latInput.value);
                const newLng = parseFloat(lngInput.value);

                if (!isNaN(newLat) && !isNaN(newLng)) {
                    updateMap(newLat, newLng);
                }
            }

            latInput.addEventListener('change', updateMapByLatLng);
            lngInput.addEventListener('change', updateMapByLatLng);
        }

</script>

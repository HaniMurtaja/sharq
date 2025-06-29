@extends('admin.layouts.app')


<div id="map" style="height: 800px;"></div>

<script>
    let map;
    const markers = [];
    const mapDataUrl = "{{ route('getMapDataNew') }}";
    let markerCluster;
    let directionsService;
    let directionsRenderer;

    // Initialize the Google Map
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 23.8859, lng: 45.0792 },
            zoom: 6,
        });

        // Initialize the MarkerClusterer
        markerCluster = new markerClusterer.MarkerClusterer({
            map,
            markers: [],
        });

        // Initialize Directions Service and Renderer
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map,
            suppressMarkers: true, // Don't show default markers for waypoints
        });

        // Load initial data
        loadMapData();

        // Fetch new data when the map bounds change
        map.addListener("idle", () => {
            const bounds = map.getBounds();
            const boundsData = {
                northEastLat: bounds.getNorthEast().lat(),
                northEastLng: bounds.getNorthEast().lng(),
                southWestLat: bounds.getSouthWest().lat(),
                southWestLng: bounds.getSouthWest().lng(),
            };
            loadMapData(boundsData);
        });
    }

    // Create custom markers for delegates based on their status
    function createDelegateMarker(delegate) {
        const delegatePosition = {
            lat: parseFloat(delegate.lat),
            lng: parseFloat(delegate.lng),
        };

        // Define circle color based on status
        const borderColor = delegate.status === 1 ? "green" : "red";

        // Create a custom SVG marker with a circular photo
        const svgMarker = `
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60">
                <circle cx="30" cy="30" r="28" fill="${borderColor}" />
                <clipPath id="clip-circle">
                    <circle cx="30" cy="30" r="25" />
                </clipPath>
                <image
                    href="${delegate.photo}"
                    x="5" y="5"
                    height="50" width="50"
                    clip-path="url(#clip-circle)"
                    preserveAspectRatio="xMidYMid slice"
                />
            </svg>
        `;

        // Create a marker for the delegate
        const delegateMarker = new google.maps.Marker({
            position: delegatePosition,
            icon: {
                url: `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgMarker)}`,
                scaledSize: new google.maps.Size(60, 60),
            },
            title: delegate.full_name,
        });

        // Add an info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <strong>${delegate.full_name}</strong><br>
                Phone: ${delegate.phone}<br>
                Orders: ${delegate.order_count}<br>
                <img src="${delegate.photo}" style="width:100px;" />
            `,
        });

        delegateMarker.addListener("click", () => {
            infoWindow.open(map, delegateMarker);

            // Show directions to each order
            if (delegate.orders && delegate.orders.length > 0) {
                const waypoints = delegate.orders.map((order) => ({
                    location: { lat: parseFloat(order.finallat), lng: parseFloat(order.finallng) },
                    stopover: true,
                }));

                directionsService.route(
                    {
                        origin: delegatePosition, // Start from the delegate's location
                        destination: waypoints[waypoints.length - 1].location, // Last order as destination
                        waypoints: waypoints.slice(0, -1), // Intermediate orders as waypoints
                        travelMode: google.maps.TravelMode.DRIVING,
                    },
                    (response, status) => {
                        if (status === google.maps.DirectionsStatus.OK) {
                            directionsRenderer.setDirections(response);
                        } else {
                            console.error("Directions request failed due to " + status);
                        }
                    }
                );
            } else {
                console.warn("No orders available for this delegate.");
            }
        });

        return delegateMarker;
    }

    // Fetch data from the backend and display on the map
    function loadMapData(bounds = null) {
        const url = bounds
            ? `${mapDataUrl}?neLat=${bounds.northEastLat}&neLng=${bounds.northEastLng}&swLat=${bounds.southWestLat}&swLng=${bounds.southWestLng}`
            : mapDataUrl;

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                // Clear existing markers
                markerCluster.clearMarkers();
                markers.length = 0;

                // Add delegate markers
                data.delegates.forEach((delegate) => {
                    const delegateMarker = createDelegateMarker(delegate);
                    markers.push(delegateMarker);
                });

                // Add markers to cluster
                markerCluster.addMarkers(markers);
            });
    }
</script>

<!-- Load Google Maps and MarkerClusterer -->




<!-- Load Google Maps and MarkerClusterer -->
<script
    src="https://maps.googleapis.com/maps/api/js?key={{env('GOGOOLE_API_KEY')}}&callback=initMap"
    async
    defer
></script>
<script src="https://cdn.jsdelivr.net/npm/@googlemaps/markerclusterer/dist/index.min.js"></script>


<!-- Load the Google Maps API -->

<!-- Load the Google Maps and MarkerClusterer libraries -->




<!-- Load the Google Maps API -->






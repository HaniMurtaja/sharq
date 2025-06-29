<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps with Marker Clustering</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&libraries=places"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/markerclustererplus/2.1.4/markerclusterer.min.js"></script>

    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script>
        let map;
        let markers = [];
        let markerCluster; // Marker clustering
        const mapDataUrl = "{{ route('getMapDataNew') }}"; // Backend URL for map data
        let directionsService;
        let directionsRenderer;

        // Initialize Google Map
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 23.8859, lng: 45.0792 },
                zoom: 6,
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
            });

            directionsRenderer.setMap(map);

            // Load map data
            loadMapData();

            // Fetch new data when the map bounds change
            map.addListener("idle", () => {
                const bounds = map.getBounds();
                const ne = bounds.getNorthEast();
                const sw = bounds.getSouthWest();

                loadMapData({
                    northEastLat: ne.lat(),
                    northEastLng: ne.lng(),
                    southWestLat: sw.lat(),
                    southWestLng: sw.lng(),
                });
            });
        }

        // Create branch icon
        function createBranchIcon(imageUrl) {
            return {
                url: imageUrl,
                scaledSize: new google.maps.Size(50, 50),
                anchor: new google.maps.Point(25, 25),
            };
        }

        // Create delegate icon
        function createDelegateIcon(photoUrl, status) {
            const borderColor = status === 1 ? "green" : "red";
            const canvas = document.createElement("canvas");
            canvas.width = 50;
            canvas.height = 50;
            const ctx = canvas.getContext("2d");

            ctx.beginPath();
            ctx.arc(25, 25, 25, 0, 2 * Math.PI);
            ctx.fillStyle = "white";
            ctx.fill();
            ctx.lineWidth = 3;
            ctx.strokeStyle = borderColor;
            ctx.stroke();

            const img = new Image();
            img.onload = function () {
                ctx.save();
                ctx.beginPath();
                ctx.arc(25, 25, 23, 0, 2 * Math.PI);
                ctx.clip();
                ctx.drawImage(img, 0, 0, 50, 50);
                ctx.restore();
            };
            img.src = photoUrl;

            return {
                url: canvas.toDataURL(),
                scaledSize: new google.maps.Size(50, 50),
                anchor: new google.maps.Point(25, 25),
            };
        }

        // Validate latitude and longitude
        function isValidLatLng(lat, lng) {
            return (
                typeof lat === "number" &&
                typeof lng === "number" &&
                !isNaN(lat) &&
                !isNaN(lng) &&
                lat >= -90 &&
                lat <= 90 &&
                lng >= -180 &&
                lng <= 180
            );
        }

        // Load map data
        function loadMapData(bounds = null) {
            const url = bounds
                ? `${mapDataUrl}?neLat=${bounds.northEastLat}&neLng=${bounds.northEastLng}&swLat=${bounds.southWestLat}&swLng=${bounds.southWestLng}`
                : mapDataUrl;

            fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    // Clear previous markers
                    markers.forEach((marker) => marker.setMap(null));
                    markers = [];

                    // Add branch markers
                    if (data.branches) {
                        data.branches.forEach((branch) => {
                            if (isValidLatLng(branch.lat, branch.lng)) {
                                const branchMarker = new google.maps.Marker({
                                    position: { lat: parseFloat(branch.lat), lng: parseFloat(branch.lng) },
                                    icon: createBranchIcon(branch.image_url),
                                    title: branch.name || "Unnamed Branch",
                                });

                                const infoWindow = new google.maps.InfoWindow({
                                    content: `
                                        <strong>${branch.name || "Unnamed Branch"}</strong><br>
                                        Orders: ${branch.orders_count}
                                    `,
                                });

                                branchMarker.addListener("click", () => {
                                    infoWindow.open(map, branchMarker);
                                });

                                markers.push(branchMarker);
                            }
                        });
                    }

                    // Add delegate markers
                    if (data.delegates) {
                        data.delegates.forEach((delegate) => {
                            if (isValidLatLng(delegate.lat, delegate.lng)) {
                                const delegateMarker = new google.maps.Marker({
                                    position: { lat: parseFloat(delegate.lat), lng: parseFloat(delegate.lng) },
                                    icon: createDelegateIcon(delegate.photo, delegate.status),
                                    title: delegate.full_name || "Unnamed Delegate",
                                });

                                const infoWindow = new google.maps.InfoWindow({
                                    content: `
                                        <strong>${delegate.full_name || "Unnamed Delegate"}</strong><br>
                                        Phone: ${delegate.phone || "N/A"}
                                    `,
                                });

                                delegateMarker.addListener("click", () => {
                                    infoWindow.open(map, delegateMarker);

                                    if (delegate.orders && delegate.orders.length > 0) {
                                        const waypoints = delegate.orders
                                            .filter((order) => isValidLatLng(order.finallat, order.finallng))
                                            .map((order) => ({
                                                location: new google.maps.LatLng(parseFloat(order.finallat), parseFloat(order.finallng)),
                                                stopover: true,
                                            }));

                                        if (waypoints.length > 0) {
                                            const request = {
                                                origin: new google.maps.LatLng(parseFloat(delegate.lat), parseFloat(delegate.lng)),
                                                destination: waypoints[waypoints.length - 1].location,
                                                waypoints: waypoints.slice(0, -1),
                                                travelMode: google.maps.TravelMode.DRIVING,
                                            };

                                            directionsService.route(request, (result, status) => {
                                                if (status === google.maps.DirectionsStatus.OK) {
                                                    directionsRenderer.setDirections(result);
                                                } else {
                                                    alert("Could not display directions: " + status);
                                                }
                                            });
                                        } else {
                                            alert("No valid orders with coordinates found.");
                                        }
                                    } else {
                                        alert(`${delegate.full_name} has no orders to show directions.`);
                                    }
                                });

                                markers.push(delegateMarker);
                            }
                        });
                    }

                    // Add markers to cluster
                    if (markerCluster) {
                        markerCluster.clearMarkers();
                    }
                    markerCluster = new MarkerClusterer({
                        map: map,
                        markers: markers,
                    });
                })
                .catch((error) => console.error("Error loading map data:", error));
        }

        // Initialize the map on window load
        window.onload = initMap;
    </script>
</body>
</html>

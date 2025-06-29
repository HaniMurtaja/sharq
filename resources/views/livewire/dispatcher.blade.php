<div wire:poll.15s>

    <div wire:ignore id="map" style="width:auto; height:100%;" class="rounded-4"></div>

</div>


@include('admin.includes.popup-js')

<script>
    let markers = [];
    let map;
    let infoWindow;
    const defaultUserIcon = "{{ asset('user.png') }}";
    const defaultHomeIcon = "{{ asset('shop.jfif') }}";

    async function initMap() {
        console.log('Initializing map...');

        // Initialize the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: @js($lat),
                lng: @js($lng)
            },
            zoom: 5 // Adjust zoom level as needed
        });

        // Clear existing markers
        markers.forEach(marker => marker.setMap(null));
        markers = [];

        // Create a new info window instance
        infoWindow = new google.maps.InfoWindow();

        // Drivers data from Livewire
        let drivers = @js($drivers);
        let orders = @js($orders);

        // Create markers for each driver
        drivers.forEach(driver => {
            const markerElement = createMarkerElement(driver.profile_image, true);
            const marker = createCustomMarker(driver, markerElement, driver.lat, driver.lng);
            if (driver.status === 1) {
                markerElement.style.border = '3px solid green'; // Green border for status 1
            } else {
                markerElement.style.border = '3px solid red'; // Red border for other statuses
            }
            // Click event to show info window with driver details
            google.maps.event.addDomListener(markerElement, 'click', () => {
                // const orderDetails = driver.orders.length > 0 ?
                //     driver.orders.map(order => `
                //     <div>
                //         <strong>Order Number:</strong> ${order.order_number}<br>
                //         <strong>Shop:</strong> ${order.shop_name}<br>
                //         <strong>Branch:</strong> ${order.branch_name}<br>
                //         <strong>Status:</strong> ${order.status}
                //     </div>
                // `).join('<hr>') :
                //     '<div>No recent orders</div>';

                infoWindow.setContent(driver.infoWindowContent);
                infoWindow.setPosition(new google.maps.LatLng(parseFloat(driver.lat), parseFloat(
                    driver.lng)));
                infoWindow.open(map);
            });

            markers.push(marker);
        });

        // Create markers for each order
        orders.forEach(order => {
            const markerElement = createMarkerElement(order.shop_profile, false);
            const orderMarker = createCustomMarker(order, markerElement, order.lat, order.lng);

            // Add click event listener to the order marker
            google.maps.event.addDomListener(markerElement, 'click', () => {
                // Ensure branch location exists
                if (order.branch && order.branch.lat && order.branch.lng) {
                    const orderLocation = new google.maps.LatLng(parseFloat(order.lat), parseFloat(
                        order.lng));
                    const branchLocation = new google.maps.LatLng(parseFloat(order.branch.lat),
                        parseFloat(order.branch.lng));

                    // Center and zoom the map to the order location
                    map.setCenter(orderLocation);
                    map.setZoom(10); // Adjust zoom level as needed

                    // Draw a black line between the order and the branch
                    const polyline = new google.maps.Polyline({
                        path: [orderLocation, branchLocation],
                        geodesic: true,
                        strokeColor: '#000000', // Black color for the line
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                    });

                    polyline.setMap(map);

                    // Place a marker at the branch location
                    const branchMarker = new google.maps.Marker({
                        position: branchLocation,
                        map: map,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', // Example pin icon
                            scaledSize: new google.maps.Size(30,
                                30) // Adjust size as needed
                        },
                        title: 'Branch Location'
                    });

                    // Increase space between the popup and the line
                    const infoWindowPosition = new google.maps.LatLng(
                        parseFloat(order.lat) +
                        0.05, // Adjust this offset for more space (larger value = more space)
                        parseFloat(order.lng)
                    );

                    // Show the order info window
                    infoWindow.setContent(order.infoWindowContent);
                    infoWindow.setPosition(infoWindowPosition);
                    infoWindow.open(map);

                    // Add listener to remove the polyline when the popup is closed
                    google.maps.event.addListener(infoWindow, 'closeclick', () => {
                        polyline.setMap(null); // Remove the line from the map
                    });
                }
            });

            // Store order markers for future reference
            markers.push(orderMarker);
        });







    }

    // Helper function to create a marker element with a profile image or default icon
    function createMarkerElement(imageUrl, isDriver = false) {
        const markerElement = document.createElement('div');
        markerElement.style.width = '40px';
        markerElement.style.height = '40px';

        if (isDriver) {
            // Use driver profile image or default user icon
            markerElement.style.backgroundImage = `url(${imageUrl || defaultUserIcon})`;
        } else {
            // Use shop profile image or default home icon
            markerElement.style.backgroundImage = `url(${imageUrl || defaultHomeIcon})`;
        }

        markerElement.style.backgroundSize = 'cover';
        markerElement.style.backgroundPosition = 'center';
        markerElement.style.borderRadius = '50%';
        markerElement.style.border = '2px solid #fff';
        markerElement.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
        return markerElement;
    }


    

   








    // Helper function to create a custom Google Maps marker
    function createCustomMarker(data, markerElement, lat, lng) {
        const customMarker = new google.maps.OverlayView();

        customMarker.onAdd = function() {
            const panes = this.getPanes();
            panes.overlayImage.appendChild(markerElement);
        };

        customMarker.draw = function() {
            const position = this.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(parseFloat(lat),
                parseFloat(lng)));
            markerElement.style.position = 'absolute';
            markerElement.style.left = `${position.x - 20}px`; // Adjust to center the marker
            markerElement.style.top = `${position.y - 20}px`; // Adjust to center the marker
        };

        customMarker.onRemove = function() {
            if (markerElement.parentNode) {
                markerElement.parentNode.removeChild(markerElement);
            }
        };

        customMarker.setMap(map);
        return customMarker;
    }





     function openDriverPopup(lat, lng, id) {
        console.log(id);

        $.ajax({
            url: '{{ route('get-driver-popup') }}',
            method: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                const orderLocation = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));



                map.setCenter(orderLocation);
                map.setZoom(10); // Adjust zoom level as needed

                // Draw a black line between the order and the branch






                // Increase space between the popup and the line
                const infoWindowPosition = new google.maps.LatLng(
                    parseFloat(lat) +
                    0.05, // Adjust this offset for more space (larger value = more space)
                    parseFloat(lng)
                );

                // Open the info window with the popup content
                infoWindow.setContent(response.infoWindowContent);
                infoWindow.setPosition(infoWindowPosition);
                infoWindow.open(map);

                // Add listener to remove the polyline when the popup is closed

            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function openOrderPopup(id, lat, lng, branch_lat, branch_lng) {

        // if (!(map instanceof google.maps.Map)) {
        //     console.error('Map object is not initialized properly.');
        //     return;
        // }

        $.ajax({
            url: '{{ route('get-order-popup') }}',
            method: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                const orderLocation = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
                const branchLocation = new google.maps.LatLng(parseFloat(branch_lat),
                    parseFloat(
                        branch_lng));

                // Center the map and zoom in on the order location

                // map = new google.maps.Map(document.getElementById('map'), {
                //     center: {
                //         lat: 24.7136,
                //         lng: 46.6753
                //     },
                //     zoom: 5
                // });

                // console.log(map);




                map.setCenter(orderLocation);
                map.setZoom(10); // Adjust zoom level as needed

                // Draw a black line between the order and the branch
                const polyline = new google.maps.Polyline({
                    path: [orderLocation, branchLocation],
                    geodesic: true,
                    strokeColor: '#000000', // Black color for the line
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                polyline.setMap(map);

                // Place a marker at the branch location (optional)
                const branchMarker = new google.maps.Marker({
                    position: branchLocation,
                    map: map,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', // Example pin icon
                        scaledSize: new google.maps.Size(30,
                            30) // Adjust size as needed
                    },
                    title: 'Branch Location'
                });

                // Increase space between the popup and the line
                const infoWindowPosition = new google.maps.LatLng(
                    parseFloat(lat) +
                    0.05, // Adjust this offset for more space (larger value = more space)
                    parseFloat(lng)
                );
                let infoWindow = new google.maps.InfoWindow();
                // Open the info window with the popup content
                infoWindow.setContent(response.infoWindowContent);
                infoWindow.setPosition(infoWindowPosition);
                infoWindow.open(map);

                // Add listener to remove the polyline when the popup is closed
                google.maps.event.addListener(infoWindow, 'closeclick', () => {
                    polyline.setMap(null); // Remove the line from the map
                    branchMarker.setMap(null); // Optionally remove the branch marker
                });
            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }





    // Call initMap when the page is loaded
    document.addEventListener('DOMContentLoaded', initMap);
</script>

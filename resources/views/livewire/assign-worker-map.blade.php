<div>
    <div wire:ignore id="assignDriverMap"  style="width:100%; height:150px;"></div>
</div>




<script>
    let markers = [];
    let map;
    let infoWindow;
    const defaultUserIcon = "{{ asset('user.png') }}";
    const defaultHomeIcon = "{{ asset('shop.jfif') }}";

    async function initMapAssignWorker() {
        console.log('Initializing initMapAssignWorker...');

        // Initialize the map
        map = new google.maps.Map(document.getElementById('assignDriverMap'), {
            center: {
                lat: 24.7136,
                lng: 46.6753
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
        

        // Create markers for each driver
        drivers.forEach(driver => {
            const markerElement = createMarkerElement(driver.profile_image, true);
            const marker = createCustomMarker(driver, markerElement, driver.lat, driver.lng);

            // Click event to show info window with driver details
            google.maps.event.addDomListener(markerElement, 'click', () => {
                const orderDetails = driver.orders.length > 0 ?
                    driver.orders.map(order => `
                    <div>
                        <strong>Order Number:</strong> ${order.order_number}<br>
                        <strong>Shop:</strong> ${order.shop_name}<br>
                        <strong>Branch:</strong> ${order.branch_name}<br>
                        <strong>Status:</strong> ${order.status}
                    </div>
                `).join('<hr>') :
                    '<div>No recent orders</div>';

                // infoWindow.setContent(driver.infoWindowContent);
                infoWindow.setPosition(new google.maps.LatLng(parseFloat(driver.lat), parseFloat(
                    driver.lng)));
                infoWindow.open(map);
            });

            markers.push(marker);
        });



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

    // Call initMap when the page is loaded
    
    $(document).on('click', '.open-modal-test', function(e) {
            e.preventDefault();
            
            console.log('Delete button clicked');
            initMap;
        });
    document.addEventListener('DOMContentLoaded', initMapAssignWorker);
</script>

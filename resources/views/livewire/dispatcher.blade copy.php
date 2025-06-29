<div class="card card-default" wire:poll.3s>
    <div class="tab-content">
        <div wire:ignore id="map" style="width:750px;height:570px;"></div>
    </div>
</div>


<script>
    let markers = [];
    let map;

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

        // Orders data from Livewire
        var orders = @json($all_orders);

        if (orders.length > 0) {
            console.log('Adding markers for orders', orders);

            // Loop through orders and add markers
            orders.forEach(function(order) {
                let position = {
                    lat: parseFloat(order.lat),
                    lng: parseFloat(order.lng)
                };
                let marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: order.customer_name
                });
                let driverFullName = order.driver && order.driver.driver ? order.driver.driver.full_name :
                    'N/A';

                console.log(driverFullName);

                // Create info window content
                let infoWindowContent = `
                <div class= "custom-info-window " id="window-info-div">

                    <div class="row">
                          <div class="col-sm-4">
                            <strong>ID:</strong> ${order.id}<br>
                        </div>
                        <div class="col-sm-4">
                            <strong>Driver:</strong> ${driverFullName}<br>
                        </div>
                        <div class="col-sm-4">
                            <strong>Phone:</strong> ${order.shop.full_name}<br>
                        </div>
                    </div>
                    <br>
                     <div class="row">
                          <div class="col-sm-4">
                            <strong>Status:</strong> ${order.status_label}<br>
                        </div>
                        <div class="col-sm-4">
                            <strong>Branch:</strong> ${order.branch.name}<br>
                        </div>
                        <div class="col-sm-4">
                            <strong>Shop:</strong> ${order.shop.full_name}<br>
                        </div>
                     </div>
                      <br>
                     <div class="row">
                          <div class="col-sm-4">
                            <strong>Value:</strong> ${order.value}<br>
                        </div>
                        <div class="col-sm-4">
                            <strong>Payment:</strong> ${order.payment_type_label}<br>
                        </div>
                     </div>
                </div>
            `;

                // Create info window
                let infoWindow = new google.maps.InfoWindow({
                    content: infoWindowContent
                });

                // Store marker and infoWindow
                markers[order.id] = {
                    marker: marker,
                    infoWindow: infoWindow
                };

                // Add click event to marker to open info window and close others
                marker.addListener('click', function() {
                    // Close all open info windows
                    closeAllInfoWindows();

                    // Open the clicked info window
                    infoWindow.open(map, marker);
                });
            });

            // Optionally, fit the map to the markers
            var bounds = new google.maps.LatLngBounds();
            orders.forEach(function(order) {
                var position = new google.maps.LatLng(parseFloat(order.lat), parseFloat(order.lng));
                bounds.extend(position);
            });
            map.fitBounds(bounds);
        } else {
            console.log('No orders found.');
        }
    }

    // Function to close all open info windows
    function closeAllInfoWindows() {
        for (let id in markers) {
            if (markers[id].infoWindow) {
                markers[id].infoWindow.close();
            }
        }
    }

    // Add click event listeners to table rows
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('.order-row');

        tableRows.forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const order = markers[id];

                if (order) {
                    // Close all open info windows
                    closeAllInfoWindows();

                    // Open the info window associated with the marker
                    order.infoWindow.open(map, order.marker);

                    // Optionally, center the map on the marker
                    map.setCenter(order.marker.getPosition());
                }
            });
        });
    });
</script>

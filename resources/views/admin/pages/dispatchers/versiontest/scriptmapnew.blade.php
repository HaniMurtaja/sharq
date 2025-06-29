
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap&libraries=places&v=weekly"
    defer></script>
<script>
    let map, markers = [], lines = [];

    function initMap() {
        const mapCenter = { lat: 23.8859, lng: 45.0792 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: mapCenter,
        });

        loadMapData();
    }


    function clearMap() {
        // Remove all markers and lines
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        lines.forEach(line => line.setMap(null));
        lines = [];
    }

    function loadMapData() {
        //clearMap();

        // Fetch updated data from the server
        fetch('{{route('getMapDataNew')}}')
            .then(response => response.json())
            .then(data => {
                const { branches, delegates } = data;

                // Add Branch Markers
                branches.forEach(branch => {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(branch.lat), lng: parseFloat(branch.lng) },
                        map: map,
                        title: branch.name_branch,
                        icon: {
                            url: branch.image_url,
                            scaledSize: new google.maps.Size(40, 40),
                        },
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="text-align: center;">
                                <strong>${branch.name_branch}</strong><br>
                                <strong>عدد الطلبات: ${branch.orders_count}</strong>
                            </div>
                        `,
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                });

                // Add Delegate Markers and Lines
                delegates.forEach(delegate => {
                    const delegateMarker = new google.maps.Marker({
                        position: { lat: parseFloat(delegate.lat), lng: parseFloat(delegate.lng) },
                        map: map,
                        title: delegate.full_name,
                        icon: {
                            url: delegate.photo,
                            scaledSize: new google.maps.Size(30, 30),
                        },
                    });

                    const delegateInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="text-align: center;">
                                <strong>${delegate.full_name}</strong>
                                <br> <strong>رقم الهاتف: ${delegate.phone}</strong>
                                <br> <strong>عدد الطلبات: ${delegate.order_count}</strong>
                            </div>
                        `,
                    });

                    delegateMarker.addListener('click', () => {
                        delegateInfoWindow.open(map, delegateMarker);
                    });

                    markers.push(delegateMarker);

                    delegate.orders.forEach(order => {
                        const orderLocation = {
                            lat: parseFloat(order.finallat),
                            lng: parseFloat(order.finallng),
                        };

                        const orderMarker = new google.maps.Marker({
                            position: orderLocation,
                            map: map,
                            title: `Order ID: ${order.id}`,
                            icon: {
                                url: 'https://cdn-icons-png.flaticon.com/512/3344/3344140.png',
                                scaledSize: new google.maps.Size(30, 30),
                            },
                        });

                        const orderInfoWindow = new google.maps.InfoWindow({
                            content: `
                                <div style="text-align: center;">
                                    <strong>Order ID: ${order.id}</strong><br>
                                    <strong>Destination</strong>
                                </div>
                            `,
                        });

                        orderMarker.addListener('click', () => {
                            orderInfoWindow.open(map, orderMarker);
                        });

                        markers.push(orderMarker);

                        const line = new google.maps.Polyline({
                            path: [
                                { lat: parseFloat(delegate.lat), lng: parseFloat(delegate.lng) },
                                orderLocation,
                            ],
                            geodesic: true,
                            strokeColor: '#FF0000',
                            strokeOpacity: 1.0,
                            strokeWeight: 2,
                        });

                        line.setMap(map);
                        lines.push(line);
                    });
                });
            });
    }

    // Refresh map data every 30 seconds
    setInterval(loadMapData, 10000);
</script>

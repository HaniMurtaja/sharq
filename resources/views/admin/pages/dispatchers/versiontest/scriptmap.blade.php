<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>
<script>
    const branches = {!! json_encode($branches) !!};
    const delegates = {!! json_encode($delegates) !!};
</script>

<script>

    function initMap() {
        // Map Center
        const mapCenter = { lat: 23.8859, lng: 45.0792 };

        // Create Map
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: mapCenter,
        });

        // Add Branch Markers
        branches.forEach(branch => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(branch.lat), lng: parseFloat(branch.lng) },
                map: map,
                title: branch.name_branch,
                icon: {
                    url: branch.image_url, // Path to branch logo
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
        });

        // Add Delegate Markers and Draw Lines to Orders
        delegates.forEach(delegate => {

            const delegateMarker = new google.maps.Marker({
                position: { lat: parseFloat(delegate.lat), lng: parseFloat(delegate.lng) },
                map: map,
                title: delegate.full_name,
                icon: {
                    url: delegate.photo, // Path to delegate icon
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

            // Draw lines to each order
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
                        url: 'https://cdn-icons-png.flaticon.com/512/3344/3344140.png', // Order icon
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

                // Draw line between delegate and order
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
            });
        });
    }

    // Initialize Map
    window.onload = initMap;
    function createMarkerElement(imageUrl, isDriver = false) {
        alert(imageUrl);
        const markerElement = document.createElement('div');
        markerElement.style.width = '40px';
        markerElement.style.height = '40px';

        if (isDriver) {
            markerElement.style.backgroundImage = `url(${imageUrl || defaultUserIcon})`;
        } else {
            markerElement.style.backgroundImage = `url(${imageUrl || defaultHomeIcon})`;
        }

        markerElement.style.backgroundSize = 'cover';
        markerElement.style.backgroundPosition = 'center';
        markerElement.style.borderRadius = '50%';
        markerElement.style.border = '2px solid #fff';
        markerElement.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
        return markerElement;
    }
</script>
<script>

</script>

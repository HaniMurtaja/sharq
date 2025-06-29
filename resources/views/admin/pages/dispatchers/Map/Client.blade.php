<script>
    function setBoundsDispatcherMap() {
        const bounds = new google.maps.LatLngBounds();
        if (driverboundes) {
            driverboundes.forEach(location => {
                bounds.extend(new google.maps.LatLng(location.lat, location.lng));
            });
        }

        if (orderboundes) {
            orderboundes.forEach(location => {
                bounds.extend(new google.maps.LatLng(location.lat, location.lng));
            });
        }

        if (branchLocations) {
            branchLocations.forEach(location => {
                bounds.extend(new google.maps.LatLng(location.lat, location.lng));
            });
        }


        if (!bounds.isEmpty()) {
            map.fitBounds(bounds);
        }

        google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
            map.setZoom(4); // Set zoom level to 6
        });
    }

    function fetchMapData() {
        if (mapCurrentRequest) {
            console.log('Aborting previous request');
            mapCurrentRequest.abort();
        }


        return new Promise((resolve, reject) => {
            mapCurrentRequest = $.ajax({
                url: get_data_map_route,
                method: 'GET',
                success: function(response) {
                    updateMap(response.drivers, response.orders, response.branches);
                    driverboundes = response.driverLocations;
                    orderboundes = response.orderLocations;
                    branchLocations = response.branchLocations;
                    map_center = response.map_center;
                    resolve();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    reject(error);
                }
            });
        });
    }


    function updateMap(drivers, orders, branches) {

        markers.forEach(marker => marker.setMap(null));
        markers = [];


        const orderArray = Array.isArray(orders) ? orders : Object.values(orders);
        const driverArray = Array.isArray(drivers) ? drivers : Object.values(drivers);


        const cityGroups = {};


        orderArray.forEach(cityGroup => {
            const {
                city_id,
                city_lat,
                city_lng,
                orders
            } = cityGroup;
            if (!cityGroups[city_id]) {
                cityGroups[city_id] = {
                    city_lat,
                    city_lng,
                    drivers: [],
                    orders: []
                };
            }
            cityGroups[city_id].orders = orders; // Add orders to city group
        });

        // Process Drivers and group by city
        driverArray.forEach(cityGroup => {
            const {
                city_id,
                city_lat,
                city_lng,
                drivers
            } = cityGroup;
            if (!cityGroups[city_id]) {
                cityGroups[city_id] = {
                    city_lat,
                    city_lng,
                    drivers: [],
                    orders: []
                };
            }
            cityGroups[city_id].drivers = drivers; // Add drivers to city group
        });

        Object.values(cityGroups).forEach(cityGroup => {
            const {
                city_lat,
                city_lng,
                drivers,
                orders
            } = cityGroup;


            const offsetLat = 0.9;
            const offsetLng = 0.9;
            if (drivers.length > 0) {
                const driverMarkerElement = document.createElement('div');
                driverMarkerElement.className = 'city-marker';
                driverMarkerElement.style.width = '40px';
                driverMarkerElement.style.height = '40px';
                driverMarkerElement.style.borderRadius = '50%';
                driverMarkerElement.style.backgroundColor = 'green'; // Color for drivers
                driverMarkerElement.style.display = 'flex';
                driverMarkerElement.style.alignItems = 'center';
                driverMarkerElement.style.zIndex = 9999;
                driverMarkerElement.style.justifyContent = 'center';
                driverMarkerElement.style.color = 'white';
                driverMarkerElement.style.fontWeight = 'bold';
                driverMarkerElement.innerHTML = `${drivers.length}`;

                const driverCityMarker = createCustomMarker({
                        lat: city_lat + offsetLat,
                        lng: city_lng + offsetLng
                    },
                    driverMarkerElement,
                    city_lat + offsetLat,
                    city_lng + offsetLng
                );

                markers.push(driverCityMarker);

                google.maps.event.addDomListener(driverMarkerElement, 'click', () => {
                    console.log('clicked');

                    const bounds = new google.maps.LatLngBounds();

                    // Loop through drivers in the city and extend the bounds
                    drivers.forEach(driver => {
                        bounds.extend(new google.maps.LatLng(driver.lat, driver.lng));

                        // Create marker element for each driver
                        const markerElement = createMarkerElement(driver.profile_image, true);
                        const marker = createCustomMarker(driver, markerElement, driver.lat,
                            driver.lng);
                        console.log('driver', driver, driver.lat, driver.lng);

                        // Set marker border based on driver status
                        if (driver.status === 1) {
                            markerElement.style.border = '3px solid green';
                        } else {
                            markerElement.style.border = '3px solid red';
                        }

                        // Add click event to individual driver markers
                        google.maps.event.addDomListener(markerElement, 'click', () => {
                            console.log(driver);
                            openDriverPopup(driver.lat, driver.lng, driver.id);
                        });

                        // Add the marker to the detailMarkers array
                        detailMarkers.push(marker);
                    });

                    // Fit the map to the bounds of all drivers in the city
                    if (!bounds.isEmpty()) {
                        map.fitBounds(bounds, 100); // Add padding (100px) around the bounds
                    } else {
                        console.error('No valid driver locations found to display.');
                    }
                });



                // // Push the driver marker to the markers array
            }

            if (orders.length > 0) {
                const orderMarkerElement = document.createElement('div');
                orderMarkerElement.className = 'city-marker';
                orderMarkerElement.style.width = '40px';
                orderMarkerElement.style.height = '40px';
                orderMarkerElement.style.borderRadius = '50%';
                orderMarkerElement.style.backgroundColor = 'blue'; // Color for orders
                orderMarkerElement.style.display = 'flex';
                orderMarkerElement.style.alignItems = 'center';
                orderMarkerElement.style.justifyContent = 'center';
                orderMarkerElement.style.zIndex = 9999;
                orderMarkerElement.style.color = 'white';
                orderMarkerElement.style.fontWeight = 'bold';
                orderMarkerElement.innerHTML = `${orders.length}`;

                // Create the order city marker
                const orderCityMarker = createCustomMarker({
                    lat: city_lat - offsetLat,
                    lng: city_lng - offsetLng
                }, orderMarkerElement, city_lat - offsetLat, city_lng - offsetLng);

                markers.push(orderCityMarker);

                // Create the hover card element
                const hoverCard = document.createElement('div');
                hoverCard.style.position = 'fixed';
                hoverCard.style.backgroundColor = 'white';
                hoverCard.style.border = '1px solid #ccc';
                hoverCard.style.borderRadius = '5px';
                hoverCard.style.padding = '10px';
                hoverCard.style.display = 'none';
                hoverCard.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
                hoverCard.style.zIndex = '999';


                document.body.appendChild(hoverCard);


                google.maps.event.addDomListener(orderMarkerElement, 'click', () => {
                    const bounds = new google.maps.LatLngBounds();

                    orders.forEach(order => {
                        if (order.branch_lat && order.branch_lng && !isNaN(order.branch_lat) &&
                            !isNaN(order.branch_lng)) {
                            bounds.extend(new google.maps.LatLng(order.branch_lat, order
                                .branch_lng));

                            const markerElement = createMarkerElement(order.shop_profile,
                                false);
                            const orderMarker = createCustomMarker(order, markerElement, order
                                .lat, order.lng);

                            google.maps.event.addDomListener(markerElement, 'click', () => {
                                openOrderPopup(
                                    order.id,
                                    order.lat,
                                    order.lng,
                                    order.branch_lat,
                                    order.branch_lng,
                                    order.userRole
                                );
                            });

                            detailMarkers.push(orderMarker);
                        } else {
                            console.warn(`Invalid coordinates for order ID ${order.id}:`, order
                                .lat, order.lng);
                        }
                    });

                    if (!bounds.isEmpty()) {
                        // Adjust map view to fit all valid order locations
                        map.fitBounds(bounds);
                    } else {
                        console.error('No valid order locations to display.');
                    }
                });
            }

        });



        branches.forEach(order => {
            const markerElement = createMarkerElement(order.shop_profile, false);
            const branchMarker = createCustomMarker(order, markerElement, order.lat, order.lng);



            markers.push(branchMarker);
        });
    }


    function createMarkerElement(imageUrl, isDriver = false) {
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

    function createCustomMarker(data, markerElement, lat, lng) {
        const customMarker = new google.maps.OverlayView();

        customMarker.onAdd = function() {
            const panes = this.getPanes();
            panes.overlayImage.appendChild(markerElement);
        };

        customMarker.draw = function() {
            const position = this.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(
                parseFloat(lat), parseFloat(lng)));
            markerElement.style.position = 'absolute';
            markerElement.style.left = `${position.x - 20}px`;
            markerElement.style.top = `${position.y - 20}px`;
        };

        customMarker.onRemove = function() {
            if (markerElement.parentNode) {
                markerElement.parentNode.removeChild(markerElement);
            }
        };

        customMarker.setMap(map);
        return customMarker;
    }
</script>

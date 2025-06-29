<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js">
</script>




<script>
    //free
    //const map = L.map('map').setView([23.8859, 45.0792], 6);

    // Initialize the map with OpenStreetMap tiles
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     attribution: '<a target="_blank" href="https://alshrouqdelivery.com">© alshrouqdelivery.com</a>'
    // }).addTo(map);
    //end free

    const apiKey = "{{ env('GEOAPIFY_API_KEY') }}"; // Your Geoapify API key

    // Initialize the map
    const map = L.map('map').setView([23.8859, 45.0792], 6);

    // Add Geoapify tile layer
    L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-carto/{z}/{x}/{y}.png?apiKey=${apiKey}`, {
        attribution: '<a href="https://www.alshrouqdelivery.com/" target="_blank">© alshrouqdelivery.com</a>',
        maxZoom: 20,
    }).addTo(map);

    map.attributionControl.setPrefix(false); // Remove default Leaflet attribution

    const markers = L.markerClusterGroup(); // Marker cluster for better UI
    const mapDataUrl = "{{ route('getMapDataNew') }}"; // Backend URL for map data

    let activeRoutingControl = null; // Manage active route control

    // Function to create branch icons
    function createBranchIcon(imageUrl) {
        return L.divIcon({
            className: "custom-branch-icon",
            html: `
        <div style="
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #007bff;
            background-image: url('${imageUrl}');
            background-size: cover;
            background-position: center;
        "></div>
    `,
            iconSize: [50, 50],
            iconAnchor: [25, 25],
        });
    }

    // Function to create delegate icons
    function createDelegateIcon(photoUrl, status) {
        const borderColor = status === 1 ? 'green' : 'red';
        return L.divIcon({
            className: "custom-delegate-icon",
            html: `
        <div style="
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid ${borderColor};
            background-image: url('${photoUrl}');
            background-size: cover;
            background-position: center;
        "></div>
    `,
            iconSize: [50, 50],
            iconAnchor: [25, 25],
        });
    }

    // Function to fetch data and display on the map
    function loadMapData(bounds = null) {
        console.log('hoo');

        const url = bounds ?
            `${mapDataUrl}?neLat=${bounds.northEastLat}&neLng=${bounds.northEastLng}&swLat=${bounds.southWestLat}&swLng=${bounds.southWestLng}` :
            mapDataUrl;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                markers.clearLayers(); 
                if (data.branches) {
                    data.branches.forEach(branch => {
                        let branchData = branch.data;
                        // console.log('hi', branchData);

                        if (branchData.lat && branchData.lng) { // Ensure coordinates are valid
                            const branchIcon = createBranchIcon(branchData.image_url);
                            const branchMarker = L.marker([branchData.lat, branchData.lng], {
                                    icon: branchIcon
                                })
                                .bindTooltip(`
                                <strong>${branchData.name || "Unnamed Branch"}</strong><br>
                                Orders: ${branchData.orders_count}
                            `, {
                                    permanent: false,
                                    direction: "top",
                                    className: "branch-tooltip",
                                });
                            markers.addLayer(branchMarker);
                        }
                    });
                }

             
                if (data.delegates) {
                    Object.values(data.delegates).forEach(delegate => {
                        if (delegate.lat && delegate.lng) {
                            const delegateIcon = createDelegateIcon(delegate.photo, delegate.status);
                            const delegateMarker = L.marker([delegate.lat, delegate.lng], {
                                icon: delegateIcon
                            });

                            delegateMarker.bindTooltip(`
                        <strong>${delegate.full_name || "Unnamed Delegate"}</strong><br>
                        Phone: ${delegate.phone || "N/A"}
                    `, {
                                permanent: false,
                                direction: "top",
                                className: "delegate-tooltip",
                            });

                            delegateMarker.on('click', () => {
                                if (activeRoutingControl) {
                                    map.removeControl(activeRoutingControl);
                                    activeRoutingControl = null;
                                }

                                if (delegate.orders && delegate.orders.length > 0) {
                                    const waypoints = delegate.orders
                                        .filter(order => order.finallat && order.finallng)
                                        .map(order => L.latLng(order.finallat, order.finallng));

                                    if (waypoints.length > 0) {
                                        waypoints.unshift(L.latLng(delegate.lat, delegate.lng));
                                        activeRoutingControl = L.Routing.control({
                                            waypoints: waypoints,
                                            routeWhileDragging: false,
                                            addWaypoints: false,
                                            show: false,
                                            lineOptions: {
                                                styles: [{
                                                    color: 'blue',
                                                    weight: 4,
                                                    opacity: 0.7
                                                }]
                                            },
                                            createMarker: function(i, waypoint, n) {
                                                return L.marker(waypoint.latLng, {
                                                    icon: i === 0 ?
                                                        createDelegateIcon(
                                                            delegate.photo,
                                                            delegate.status) :
                                                        L.divIcon({
                                                            className: 'custom-order-icon',
                                                            html: `<div style="
                                                width: 40px;
                                                height: 40px;
                                                background-color: #f0ad4e;
                                                border-radius: 50%;
                                                text-align: center;
                                                line-height: 40px;
                                                color: white;
                                                font-weight: bold;
                                            ">${i}</div>`,
                                                            iconSize: [40,
                                                                40
                                                            ],
                                                            iconAnchor: [20,
                                                                20
                                                            ],
                                                        })
                                                });
                                            }
                                        }).addTo(map);
                                    } else {
                                        alert("No valid orders with coordinates found.");
                                    }
                                } else {
                                    alert(
                                    `${delegate.full_name} has no orders to show directions.`);
                                }
                            });

                            markers.addLayer(delegateMarker);
                        }
                    });
                }


                map.addLayer(markers); // Add markers to the map
            })
            .catch(error => console.error("Error loading map data:", error));
    }

    // Initial load
    loadMapData();

    // Fetch new data every 5 seconds
    setInterval(() => {
        const bounds = map.getBounds();
        loadMapData({
            northEastLat: bounds.getNorthEast().lat,
            northEastLng: bounds.getNorthEast().lng,
            southWestLat: bounds.getSouthWest().lat,
            southWestLng: bounds.getSouthWest().lng,
        });
    }, 5000);





    //// {44 => {5, testt}, 34 => {5, testt}}


    // Fetch new data when the map moves
    map.on('moveend', () => {
        const bounds = map.getBounds();
        loadMapData({
            northEastLat: bounds.getNorthEast().lat,
            northEastLng: bounds.getNorthEast().lng,
            southWestLat: bounds.getSouthWest().lat,
            southWestLng: bounds.getSouthWest().lng,
        });
    });

    document.body.addEventListener('click', function(event) {
        if (event.target.closest('.close-order-popup-map')) {
            console.log('close popup');
            map.closePopup();
            const fixedPopup = document.getElementById('fixed-popup');
            if (fixedPopup) {
                fixedPopup.remove();
            }
        }
    });

    map.on('popupclose', function() {
        const fixedPopup = document.getElementById('fixed-popup');
        if (fixedPopup) {
            fixedPopup.remove();
        }
    });
    map.on('popupopen', function(e) {
        map.closePopup();
        const leafletPopup = document.querySelector('.leaflet-popup');

        if (leafletPopup) {
            let fixedPopup = document.getElementById('fixed-popup');

            if (!fixedPopup) {
                fixedPopup = document.createElement('div');
                fixedPopup.id = 'fixed-popup';
                fixedPopup.classList.add('fixedContainer');
                const mapContainer = document.getElementById('mapContainer');
                mapContainer.appendChild(fixedPopup);
            }

            const popupContent = leafletPopup.querySelector('.leaflet-popup-content');
            fixedPopup.innerHTML = popupContent ? popupContent.innerHTML : 'No content found in the popup.';

            if (document.querySelector('.fixedContainer .driverPopup')) {
                document.querySelector('.fixedContainer').style.left = '67%';
            }
            if (!window._popupListenerAdded) {
                document.addEventListener("click", function(e) {
                    if (!e.target || !e.target.matches(".mainPopup .seeMoreBtn")) return;

                    const collapseFirst = document.getElementById("collapseFirst");
                    const collapseSecond = document.getElementById("collapseSecond");
                    const colFirst = document.getElementById("colFirst");
                    const colSecond = document.getElementById("colSecond");

                    const bothOpen = collapseFirst.classList.contains("show") && collapseSecond
                        .classList.contains("show");

                    if (bothOpen) {
                        console.log("here 4 - hiding both");
                        collapseFirst.classList.remove("show");
                        collapseSecond.classList.remove("show");
                        colFirst.classList.add("collapsed");
                        colSecond.classList.add("collapsed");
                    } else {
                        console.log("here 2 - showing both");
                        collapseFirst.classList.add("show");
                        collapseSecond.classList.add("show");
                        colFirst.classList.remove("collapsed");
                        colSecond.classList.remove("collapsed");
                    }
                });

                window._popupListenerAdded = true;
            }



        }

    });
</script>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-database-compat.js"></script>

<!-- Leaflet & Plugins -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- Geoapify Tiles -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js">
</script>


<script>
    const firebaseConfig = {
        apiKey: "AIzaSyDFnoM5nwPdB-43me0sxO5hSysTvrMQxWI",
        authDomain: "alshrouqexpress-97ebd.firebaseapp.com",
        databaseURL: "https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com",
        projectId: "alshrouqexpress-97ebd",
        storageBucket: "alshrouqexpress-97ebd.appspot.com",
        messagingSenderId: "556213764824",
        appId: "1:556213764824:web:29d8ace147869174100dad",
        measurementId: "G-6DKM5SR2XV"
    };
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    // ✅ Leaflet Setup
    const apiKey = "{{ env('GEOAPIFY_API_KEY') }}";
    const map = L.map('map').setView([23.8859, 45.0792], 6);
    const markers = L.markerClusterGroup();
    let activeRoutingControl = null;

    L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-carto/{z}/{x}/{y}.png?apiKey=${apiKey}`, {
        attribution: '<a href="https://www.alshrouqdelivery.com/" target="_blank">© alshrouqdelivery.com</a>',
        maxZoom: 20,
    }).addTo(map);

    map.attributionControl.setPrefix(false);

    // ✅ Utility: Create Icons
    function createBranchIcon(imageUrl) {
        return L.divIcon({
            className: "custom-branch-icon",
            html: `<div style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #007bff; background-image: url('${imageUrl}'); background-size: cover; background-position: center;"></div>`,
            iconSize: [50, 50],
            iconAnchor: [25, 25],
        });
    }

    function createDelegateIcon(photoUrl, status) {
        const borderColor = status === 1 ? 'green' : 'red';
        return L.divIcon({
            className: "custom-delegate-icon",
            html: `<div style="width: 50px; height: 50px; border-radius: 50%; border: 3px solid ${borderColor}; background-image: url('${photoUrl}'); background-size: cover; background-position: center;"></div>`,
            iconSize: [50, 50],
            iconAnchor: [25, 25],
        });
    }


    async function fetchFilteredData(user_role, filters = {}, shopId = null, branchId = null, city_ids = [],
        country_id = null) {
        const branchesSnapshot = await database.ref('branches').get();
        const mapSnapshot = await database.ref('map').get();

        const filteredBranches = [];
        const filteredDelegates = [];
        const now = new Date();
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);

        if (!branchesSnapshot.exists() || !mapSnapshot.exists()) return {
            branches: [],
            delegates: {}
        };

        const allBranches = branchesSnapshot.val();
        const allMapData = mapSnapshot.val();

        for (const [key, branch] of Object.entries(allBranches)) {
            const createdAt = branch.created_at ? new Date(branch.created_at) : null;
            const isDateMatch = createdAt && createdAt >= yesterday && createdAt <= now;
            const hasOrders = branch.orders_count && branch.orders_count !== 0;
            const hasLatLng = branch.lat && branch.lng;

            const matchClient = !filters.client_id || branch.client_id == filters.client_id;
            const matchId = !filters.id || branch.id == filters.id;

            const roleMatch = (
                (user_role === 'admin' && (!country_id || branch.country_id == country_id)) ||
                (user_role === 'dispatcher' && city_ids.includes(branch.city_id))
            );

            if (matchClient && matchId && roleMatch && hasOrders && hasLatLng && isDateMatch) {
                filteredBranches.push({
                    firebase_key: key,
                    data: branch
                });
            }
        }

        for (const [orderId, mapEntry] of Object.entries(allMapData)) {
            if (!mapEntry.order_created_at) continue;

            const createdAt = new Date(mapEntry.order_created_at);
            const isDateMatch = createdAt >= yesterday && createdAt <= now;

            const shopMatch = !shopId || mapEntry.ingr_shop_id == shopId;
            const branchMatch = !branchId || mapEntry.ingr_branch_id == branchId;


            const roleMatch = (
                (user_role === 'admin' && (!country_id || mapEntry.country_id == country_id)) ||
                (user_role === 'dispatcher' && city_ids.includes(mapEntry.city_id))
            );

            if (shopMatch && branchMatch && roleMatch && isDateMatch) {
                filteredDelegates.push({
                    firebase_key: orderId,
                    data: mapEntry
                });
            }
        }



        return {
            branches: filteredBranches,
            delegates: filteredDelegates
        };
    }

    // ✅ Load & Render Data on Map
    async function loadMapData() {
        const data = await fetchFilteredData(
            "{{ strtolower(auth()->user()->user_role->name) }}", // user_role
            @json($filters ?? []), // filters from controller
            {{ $shopId ?? 'null' }},
            {{ $branchId ?? 'null' }},
            @json($city_ids ?? []),
            {{ $country_id ?? 'null' }}
        );



        markers.clearLayers();

        // Render branches
        if (data.branches) {
            data.branches.forEach(branch => {
                const b = branch.data;
                if (b.lat && b.lng) {
                    const marker = L.marker([b.lat, b.lng], {
                        icon: createBranchIcon(b.image_url)
                    }).bindTooltip(
                        `<strong>${b.name || "Branch"}</strong><br>Orders: ${b.orders_count}`, {
                            direction: "top"
                        });

                    markers.addLayer(marker);
                }
            });
        }

        // Render delegates
        if (data.delegates) {
            data.delegates.forEach(delegate => {
                const d = delegate.data;
                if (d.lat && d.lng) {
                    const marker = L.marker([d.lat, d.lng], {
                        icon: createDelegateIcon(d.photo, d.status)
                    }).bindTooltip(
                        `<strong>${d.full_name}</strong><br>Phone: ${d.phone}`, {
                            direction: "top"
                        });

                    // On click: show route to orders
                    marker.on('click', () => {
                        if (activeRoutingControl) {
                            map.removeControl(activeRoutingControl);
                            activeRoutingControl = null;
                        }

                        const orders = d.orders || [];

                        const waypoints = orders
                            .filter(o => o.finallat && o.finallng)
                            .map(o => L.latLng(o.finallat, o.finallng));

                        if (waypoints.length > 0) {
                            waypoints.unshift(L.latLng(d.lat, d.lng));

                            activeRoutingControl = L.Routing.control({
                                waypoints: waypoints,
                                addWaypoints: false,
                                show: false,
                                routeWhileDragging: false,
                                createMarker: (i, wp) => L.marker(wp.latLng, {
                                    icon: i === 0 ?
                                        createDelegateIcon(d.photo, d.status) : L
                                        .divIcon({
                                            html: `<div style="width:40px;height:40px;background:#f0ad4e;border-radius:50%;text-align:center;line-height:40px;color:white;font-weight:bold;">${i}</div>`,
                                            iconSize: [40, 40],
                                            iconAnchor: [20, 20]
                                        })
                                }),
                                lineOptions: {
                                    styles: [{
                                        color: 'blue',
                                        weight: 4
                                    }]
                                }
                            }).addTo(map);
                        } else {
                            alert(`${d.full_name} has no orders`);
                        }
                    });

                    markers.addLayer(marker);
                }
            });
        }

        map.addLayer(markers);
    }

    @if (auth()->user()->user_role !== \App\Enum\UserRole::DISPATCHER)
        loadMapData();
        setInterval(loadMapData, 10000);
        map.on('moveend', loadMapData);
    @endif


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

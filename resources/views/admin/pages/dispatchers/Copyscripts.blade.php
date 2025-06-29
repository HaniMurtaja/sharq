<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"></script>
<style>
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>


<script src=" https://cdn.jsdelivr.net/npm/sortablejs@1.15.4/Sortable.min.js "></script>




<script type="text/javascript">
    // let dragBox = document.querySelector(".collapse-wrapper");
    // console.log("thisi is the drag box");
    // new Sortable(dragBox, {
    //     animation: 150,
    //     ghostClass: 'blue-background-class'
    // });


    document.addEventListener('livewire:init', () => {
        Livewire.on('openModal', (event) => {
            console.log('Modal opened');
            setTimeout(() => {
                console.log('Attempting to initialize maps');
                const formMapElement = document.getElementById("formMap");
                const formMap2Element = document.getElementById("formMap2");
                console.log('formMap:', formMapElement);
                console.log('formMap2:', formMap2Element);

                if (formMapElement) {
                    initFormMap();
                } else {
                    console.error('formMap element not found');
                }

                if (formMap2Element) {
                    initFormMap2();
                } else {
                    console.error('formMap2 element not found');
                }


                document.querySelectorAll('.nextBtn').forEach(button => {
                    button.addEventListener('click', () => {

                        console.log('clicked')

                        setTimeout(() => {

                            const button2 = document.getElementById(
                                'nextBtn2');
                            button.addEventListener('click', () => {
                                setTimeout(() => {
                                    if (
                                        formMap2Element
                                    ) {
                                        initFormMap2();
                                    }

                                }, 1000);
                            });

                        }, 1000);
                    });
                });


            }, 1000);
        });
    });
</script>

<script>
    var currentTab = 0;
    let formMap;
    let formMap2
    let marker;



    document.addEventListener("DOMContentLoaded", function(event) {
        resetSteps();
        // initFormMap();
        initFormMap2();
        // $(function() {
        //     $('#reservationdate').datetimepicker();
        // })
        getStatistics();
        setInterval(getStatistics, 3000);
        function getStatistics() {
            $.ajax({
                url: '{{ route('get-statistics') }}',
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#all-count').html(response.totalOrders)
                },
                error: function(xhr) {
                    console.error('Error resetting current step');
                }
            });
        }

        document.getElementById('date_time').addEventListener('change', updateDriverIn);
        document.getElementById('preperation_time').addEventListener('input', updateDriverIn);

        function updateDriverIn() {
            const dateTime = new Date(document.getElementById('date_time').value);
            const now = new Date();
            const preparationTime = parseInt(document.getElementById('preperation_time').value);

            if (isNaN(preparationTime) || dateTime == 'Invalid Date') {
                document.getElementById('driver_in').value = '';
                return;
            }


            const diffInMs = dateTime - now;

            const diffInMinutes = Math.round(diffInMs / (1000 * 60)) + preparationTime;

            document.getElementById('driver_in').value = diffInMinutes;
        };




    });

    function closeOrderDrawer() {
        document.getElementById('drawer-overlay').style.display = 'none';
        document.getElementById('drawer').style.transform = 'translateX(100%)'; // Slide out of view
    }



    $(document).ready(function() {
        // $('#exampleModal').on('hidden.bs.modal', function() {
        //     console.log('Modal closed');
        //     $('input').removeClass('is-invalid');
        //     $('#regForm')[0].reset();
        //     $('select').removeClass('is-invalid');
        //     $('.invalid-feedback').remove();
        //     $('.text-danger').remove();
        //     resetSteps();
        // });



        const openDrawerButtons = document.querySelectorAll('.open-drawer');

        openDrawerButtons.forEach((button) => {
            button.addEventListener('click', function() {
                document.getElementById('drawer-overlay').style.display = 'block';
                document.getElementById('drawer').style.transform = 'translateX(0)';

                console.log('Modal closed');
                $('input').removeClass('is-invalid');
                $('#regForm')[0].reset();
                $('select').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.text-danger').remove();
                resetSteps();






            });
        });
    });







    $(document).on('change', '#client_id', function() {
        var clientID = $(this).val();
        var areaSelect = $('#branch_id');

        if (clientID) {
            $.ajax({
                url: '{{ route('client-branches') }}',
                type: 'GET',
                data: {
                    clientID: clientID
                },
                success: function(response) {
                    console.log(response);
                    areaSelect.prop('disabled', false);
                    areaSelect.empty();
                    areaSelect.append(
                        '<option value="" selected="selected" disabled>Branch</option>'
                    );

                    $.each(response.branches, function(key, branch) {

                        areaSelect.append('<option value="' + branch.id + '">' +
                            branch.name + '</option>');
                    });
                    console.log(response.fees);

                    $('#service_fees').val(response.fees)
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        } else {
            areaSelect.prop('disabled', true);
            areaSelect.empty();
            areaSelect.append('<option value="" selected="selected" disabled>Branch</option>');
        }
    });

    $('#branch_id').on('change', function() {

        var branch_id = $(this).val();
        var url = "{{ route('get-branch', ':id') }}";
        url = url.replace(':id', branch_id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {

                $('#lat_client_branch_hidden').val(response.lat);
                $('#lng_client_branch_hidden').val(response.lng);
                getDurationDistance(response.lat, response.lng);
                drawMapCoordinatesLine();
            }


        });



    });

    function getDurationDistance(lat, lng) {
        // Define the API endpoint and parameters

        var origins = lat + "," + lng;

        var destinations = $('#lat_order_hidden').val() + "," + $('#long_order_hidden').val();
        var params = {
            origins: origins,
            destinations: destinations,
            mode: "driving",
            key: "AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"
        };


        $.ajax({
            url: '{{ route('distance-matrix') }}',
            type: 'GET',
            data: params,
            success: function(response) {


                if (response.status === "OK") {
                    var distance = response.distance;
                    var duration = response.duration;
                    console.log('distacce: ');

                    console.log(distance);
                    $('#distance').val(distance);
                    $('#driver_arrive_time').val(duration);
                } else {
                    console.log('error');


                }


            },
            error: function(xhr, status, error) {
                console.error("Error occurred: " + status + " " + error);
                console.log("An error occurred while fetching the data.");
            }
        });
    }







    function showTab(n) {
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        // if (n == 0) {
        //     document.getElementById("prevBtn").style.display = "none";

        // } else {
        //     document.getElementById("prevBtn").style.display = "inline";
        // }
        // if (n == (x.length - 1)) {
        //     document.getElementById("nextBtn").innerHTML = "Submit";
        // } else {
        //     document.getElementById("nextBtn").innerHTML = "Next";
        // }
        // fixStepIndicator(n)
    }



    function nextPrev(n) {
        var x = document.getElementsByClassName("tab");

        if (n == 1) {

            $('input').removeClass('is-invalid');
            $('select').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.text-danger').remove();
            var formData = $('#regForm').serialize();
            console.log(999999999999999999999)
            console.log(formData)

            $.ajax({
                type: 'GET',
                url: '{{ route('increment-step') }}',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                    x[currentTab].style.display = "none";
                    currentTab = currentTab + n;

                    if (currentTab >= x.length) {
                        // Handle end of form
                        document.getElementById("nextprevious").style.display = "none";
                        document.getElementById("all-steps").style.display = "none";
                        document.getElementById("register").style.display = "none";
                        document.getElementById("text-message").style.display = "block";
                    } else {
                        showTab(currentTab);
                    }
                },
                error: function(error) {
                    console.log(error); // Debugging log to inspect the error object

                    if (error.status === 422) {
                        // Check if the response is already parsed as JSON
                        var response = error.responseJSON || JSON.parse(error.responseText);

                        // Check if errors exist in the response
                        if (response.errors) {
                            var errors = response.errors;

                            // Loop through each error and display it
                            $.each(errors, function(key, value) {
                                if (key === 'lat_order_hidden' || key === 'lng_order_hidden') {
                                    // Display the custom error message in the #map_error span
                                    $('#map_error').html('<span class="text-danger ">' +
                                        'Location Required' +
                                        '</span>');
                                } else {
                                    var inputElement = $('[name="' + key + '"]');

                                    // Add invalid class to the input field
                                    inputElement.addClass('is-invalid');

                                    // Display the error message after the input field
                                    inputElement.after(
                                        '<span class="text-danger invalid-feedback">' +
                                        value[0] + '</span>');
                                }
                            });
                        }
                    } else {
                        console.error('AJAX request failed:', error);
                    }
                }


            });
        } else {


            $.ajax({
                type: 'GET',
                url: '{{ route('decrement-step') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    x[currentTab].style.display = "none";
                    currentTab = currentTab + n;
                    if (currentTab >= x.length) {
                        // Handle end of form
                        document.getElementById("nextprevious").style.display = "none";
                        document.getElementById("all-steps").style.display = "none";
                        document.getElementById("register").style.display = "none";
                        document.getElementById("text-message").style.display = "block";
                    } else {
                        showTab(currentTab);
                    }
                },
                error: function(error) {
                    console.log(error); // Debugging log to inspect the error object

                    if (error.status === 422) {
                        // Check if the response is already parsed as JSON
                        var response = error.responseJSON || JSON.parse(error.responseText);

                        // Check if errors exist in the response
                        if (response.errors) {
                            var errors = response.errors;

                            // Loop through each error and display it
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');

                                // Add invalid class to the input field
                                inputElement.addClass('is-invalid');

                                // Display the error message after the input field
                                inputElement.after('<span class="text-danger invalid-feedback">' +
                                    value[0] + '</span>');
                            });
                        }
                    } else {
                        console.error('AJAX request failed:', error);
                    }
                }

            });

        }
    }

    function resetModalTab() {
        currentTab = 0;
        console.log(22);

        var x = document.getElementsByClassName("tab");
        x[2].style.display = "none";
        x[1].style.display = "none";
        showTab(0)
        $('#regForm')[0].reset();
        console.log(33);




    }


    function showToast() {
        const toast = document.getElementById('toastCreateOrder');
        toast.style.display = 'block'; // Show the toast
        setTimeout(() => {
            toast.style.opacity = '1'; // Add a fade-in effect
        }, 10);

        // Automatically hide the toast after 5 seconds
        setTimeout(hideToast, 5000);
    }

    function hideToast() {
        const toast = document.getElementById('toastCreateOrder');
        toast.style.opacity = '0'; // Fade out
        setTimeout(() => {
            toast.style.display = 'none'; // Hide after fade-out
        }, 500); // Match the fade-out duration
    }


    function saveOrder() {
        var formData = $('#regForm').serialize();
        $('input').removeClass('is-invalid');
        $('select').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('.text-danger').remove();
        $.ajax({
            type: 'GET',
            url: '{{ route('save-order') }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);

                currentTab = 0;
                $('#regForm')[0].reset();
                // alert('Order saved successfully');
                // const toastElement = $('#toastCreateOrder');

                // toastElement.find('.toast-header strong').text('');
                // toastElement.find('.toast-body').text('Order saved successfully');
                // toastElement.find('img').attr('src', '');


                closeOrderDrawer();
                // toastElement.toast('show');
                showToast();




            },
            error: function(error) {
                if (error.status === 403 && error.responseJSON.redirect) {
                    window.location.href = error.responseJSON.redirect;
                } else if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var inputElement = $('[name="' + key + '"]');
                        inputElement.addClass('is-invalid');
                        inputElement.after('<span class="text-danger invalid-feedback">' +
                            value[0] + '</span>');
                    });
                } else {
                    console.error('AJAX request failed:', error);
                }
            }
        });



    }

    function resetSteps() {
        currentTab = 0;
        $.ajax({
            url: '{{ route('reset-step') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Current step reset to 1');
            },
            error: function(xhr) {
                console.error('Error resetting current step');
            }
        });
    }



    window.initAutocomplete = initMap2;

    function initMap2() {
        const lat = parseFloat(document.getElementById('lat_order_hidden').value) || 24.7136;
        const lng = parseFloat(document.getElementById('long_order_hidden').value) || 46.6753;
        const initialLocation = {
            lat: lat,
            lng: lng
        };
        const formMap = new google.maps.Map(document.getElementById("formMap"), {
            center: initialLocation,
            zoom: 13,
            mapTypeId: "roadmap",
        });
        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);

        formMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        formMap.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        let markers = [];

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach((marker) => {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };

                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                        formMap,
                        icon,
                        title: place.name,
                        position: place.geometry.location,
                    }),
                );
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            formMap.fitBounds(bounds);
        });

        var marker;
        formMap.addListener('click', function(event) {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng();
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: formMap
            });
            $('#lat_order_hidden').val(lat);
            $('#long_order_hidden').val(lng);



        });
    }

    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: formMap
            });
        }

        // Set the latitude and longitude values to the hidden fields
        document.getElementById('lat_order_hidden').value = location.lat();
        document.getElementById('long_order_hidden').value = location.lng();
    }


    // async function initFormMap() {
    //     console.log('Initializing form map...');
    //     console.log(document.getElementById("formMap"))
    //     try {
    //         const {
    //             Map
    //         } = await google.maps.importLibrary("maps");
    //         formMap = new Map(document.getElementById("formMap"), {
    //             zoom: 7,
    //             center: {
    //                 lat: 24.7136,
    //                 lng: 46.6753
    //             },
    //             mapId: "DEMO_MAP_ID_2",
    //         });
    //         var marker;
    //         formMap.addListener('click', function(event) {
    //             var lat = event.latLng.lat();
    //             var lng = event.latLng.lng();
    //             if (marker) {
    //                 marker.setMap(null);
    //             }

    //             marker = new google.maps.Marker({
    //                 position: {
    //                     lat: lat,
    //                     lng: lng
    //                 },
    //                 map: formMap
    //             });
    //             $('#lat_order_hidden').val(lat);
    //             $('#long_order_hidden').val(lng);



    //         });

    //         console.log('Form map initialized successfully');
    //     } catch (error) {
    //         console.error('Error initializing form map:', error);
    //     }
    // }

    async function initFormMap2() {

        console.log('Initializing form map 2...');


        try {
            const {
                Map
            } = await google.maps.importLibrary("maps");
            formMap2 = new Map(document.getElementById("formMap2"), {
                zoom: 4,
                center: {
                    lat: 24.7136,
                    lng: 46.6753
                },
                mapId: "DEMO_MAP_ID_3",

            });





            console.log('Form map 2 initialized successfully');
        } catch (error) {
            console.error('Error initializing form map:', error);
        }
    }

    function drawMapCoordinatesLine() {
        var _latA = parseFloat($('#lat_client_branch_hidden').val());
        var _lngA = parseFloat($('#lng_client_branch_hidden').val());
        var _latB = parseFloat($('#lat_order_hidden').val());
        var _lngB = parseFloat($('#long_order_hidden').val());


        const pointA = {
            lat: _latA,
            lng: _lngA
        };
        const pointB = {
            lat: _latB,
            lng: _lngB
        };


        const map = new google.maps.Map(document.getElementById("formMap2"), {
            zoom: 7,
            center: {
                lat: (pointA.lat + pointB.lat) / 2,
                lng: (pointA.lng + pointB.lng) / 2
            },
        });


        new google.maps.Marker({
            position: pointA,
            map: map,
            title: "Riyadh, Saudi Arabia", // can get it from matrix api service (origins address)

        });

        new google.maps.Marker({
            position: pointB,
            map: map,
            title: "Geddah, Saudi Arabia", // can get it from matrix api service (destination address)

        });


        const line = new google.maps.Polyline({
            path: [pointA, pointB],
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
        });

        // Set the line on the map
        line.setMap(map);
    }
</script>



<script>
    let markersDriversAssign = [];
    let mapDriversAssign;
    let infoWindowDriversAssign;
    const defaultUserIcon2 = "{{ asset('user.png') }}";
    const defaultHomeIcon2 = "{{ asset('shop.jfif') }}";


    function openDriverOrdersPopup(order_id, driver_id) {
        console.log(54);

        $.ajax({
            url: '{{ route('get-driver-orders') }}',
            type: 'GET',
            data: {
                order_id: order_id,
                driver_id: driver_id
            },
            success: function(response) {
                console.log('Success:', response);

                $('#driver-order-popup').html(response.order.id);
                $('#driver_name').html(response.driver.full_name);
                $('#driver_name2').html(response.driver.full_name);
                $('#driver_phone').html(response.driver.phone);
                $('#assignBtn').attr('data-order', response.order.id);
                $('#assignBtn').attr('data-id', response.driver.id);
                // Clear the previous driver data
                $('#driver-orders').empty();

                response.orders.forEach(order => {
                    const collapseId = `order_${order.jop_id}`;
                    const orderHtml = `
                    <div class="collapse-container">
                        <a class="collapse-tab collapsed" data-bs-toggle="collapse" href="#${collapseId}" role="button"
                            aria-expanded="false" aria-controls="${collapseId}">
                            <div class="collapseNameIcon">
                                <div class="bullet"></div>
                                <div class="collapseIcon">
                                    <p>D</p>
                                    <p>1</p>
                                </div>
                                <div class="driverOrdersDetailsContent">
                                    <p>${order.shop_name + '-' + order.branch_name|| 'Shop Name'}</p>
                                    <p>${order.branch_area || 'Area'}</p>
                                    <p>${order.branch_phone || '+966'}</p>
                                </div>
                            </div>
                        </a>
                        <div class="collapse" id="${collapseId}">
                            <div class="card card-body">
                                <div class="driverDetails">
                                    <div class="detailItem">
                                        <div class="shopName">
                                            <span>Area</span>
                                            <p>${order.branch_area || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="detailItem">
                                        <div class="shopName">
                                            <span>Contact Phone</span>
                                            <p>${order.branch_phone || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="detailItem">
                                        <div class="shopName">
                                            <span>Job ID</span>
                                            <p>${order.jop_id || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="detailItem">
                                        <div class="shopName">
                                            <span>Task Progress Status</span>
                                            <p>${order.status_label || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    $('#driver-orders').append(orderHtml);
                });

            },

            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
            }
        });
    }


    function handelAssignDriverModalOpen(id) {
        $.ajax({
            url: '{{ route('assign-driver-modal') }}',
            type: 'GET',
            data: {
                id: id,
            },
            success: function(response) {
                console.log('Success:', response);
                let order = response.order;
                $('#order_id').html(response.order.id);
                $('#branch_name').html(response.branch_name);

                // Clear the previous driver data
                $('#assign-driver-table').empty();

                // Loop through drivers and append rows
                response.drivers.forEach(driver => {
                    // Determine border color based on driver status
                    let borderStyle = driver.status === 1 ? 'border: 3px solid green;' :
                        'border: 3px solid red;';

                    let driverRow = `
            <tr class="table-row">
                <td onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                    <div class="driverImageData">
                        <div class="driverImageWrapper ">
                            <img style ="${borderStyle}" src="${driver.profile_image || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}"
                                 alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="driverData">
                            <p class="driverName">${driver.full_name || '-'}</p>
                            <p class="driverPhone">${driver.phone || '-'}</p>
                        </div>
                    </div>
                </td>
                <td data-bs-toggle="modal"   onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-target="#orderDetailsModal">${driver.vehicle || '-'}</td>
                <td data-bs-toggle="modal"  onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-target="#orderDetailsModal">${driver.completed_jops || '0'}</td>
                <td data-bs-toggle="modal"  onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-target="#orderDetailsModal">&thickapprox;${driver.distance || '0'}km</td>
                <td data-bs-toggle="modal" onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-target="#orderDetailsModal" style = "color:red">${driver.tasks || '0'}</td>
                <td class="destinationContainer"  onclick="openDriverOrdersPopup( ${ order.id },${ driver.id })" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                    <svg width="4rem" height="4rem" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M40 20C40 31.0457 31.0457 40 20 40C8.9543 40 0 31.0457 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z" fill="black"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M31.1416 15.6379C28.5058 3.99173 11.2516 3.97828 8.60233 15.6245C7.05578 22.4562 11.3054 28.2524 15.0172 31.8297C17.7337 34.4386 22.0103 34.4386 24.7134 31.8297C28.4385 28.2524 32.6882 22.4697 31.1416 15.6379ZM24.2835 17.0236C24.6774 16.6297 24.6774 15.9911 24.2835 15.5972C23.8896 15.2033 23.251 15.2033 22.8571 15.5972L18.191 20.2633L16.8869 18.9592C16.493 18.5653 15.8544 18.5653 15.4605 18.9592C15.0666 19.3531 15.0666 19.9917 15.4605 20.3856L17.4778 22.4029C17.8717 22.7968 18.5103 22.7968 18.9042 22.4029L24.2835 17.0236Z" fill="white"></path>
                    </svg>
                    <div class="destination">
                        <h5>Destination: -</h5>
                        <p>Distance: -</p>
                    </div>
                </td>
                <td>
                    <button class="assignBtn" data-order = "${response.order.id}"  data-id = "${driver.id}">
                        Assign
                    </button>
                </td>
            </tr>
        `;
                    $('#assign-driver-table').append(driverRow);
                });

                // Initialize map with driver locations
                initMapAssignWorker(response.drivers);
            },

            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
            }
        });
    }

    function openAssignDriverModal(order_id) {
        const modalElement = document.getElementById('exampleModal');

        // Create a new Bootstrap modal instance
        const modal = new bootstrap.Modal(modalElement);

        // Show the modal
        modal.show();
        handelAssignDriverModalOpen(order_id);
    }

    // $(document).on('click'. )
    $(document).on('click', '.open-modal-test, .assignDriverBtn', function(e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        console.log('Open modal button clicked, data-id:', dataId);
        handelAssignDriverModalOpen(dataId);

    });

    $(document).on('click', '.order-history-btn', function(e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        console.log('Open modal button clicked, data-id:', dataId);
        $.ajax({
            url: '{{ route('get-order-history') }}',
            type: 'GET',
            data: {
                order_id: dataId,
            },
            success: function(response) {
                console.log('Success:', response);

                // Populate modal content
                let client_order_id = response.order.client_order_id || '---';
                $('#modalTitle').text(
                    `Logs - #${response.order.id} Order ID - #${client_order_id} `
                );
                $('#brand').text(response.brand || '---');
                $('#branch').text(response.branch || '---');
                $('#customerPhone').text(response.order.customer_phone || '---');
                $('#customerName').text(response.order.customer_name || '---');

                // Populate history table
                let historyRows = '';

                response.histories.forEach(history => {
                    let description = history.description || '--';
                    historyRows += `
                    <tr>
                        <td colspan="2">${new Date(history.created_at).toLocaleString()} &nbsp;&nbsp;</td>
                        <td></td>
                        <td colspan="3">${history.action} &nbsp;&nbsp;</td>
                        <td></td>
                        <td colspan="5">${description} &nbsp;&nbsp;</td>
                    </tr>`;
                });
                $('#historyTable').html(historyRows);

                // Show the modal
                $('#orderHistoryModal').fadeIn();
            },

            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
            }
        });

    });



    function test() {
        alert('helo');
    }
    document.querySelectorAll('#myTable tbody tr').forEach(row => {
        row.addEventListener('dblclick', function() {
            const rowId = this.getAttribute('data-id');
            alert('You double-clicked on row with ID: ' + rowId);
            // Add your specific action here
        });
    });


    $(document).on('click', '.assignBtn', function(e) {
        e.preventDefault();
        $('#loader').show();
        $('#overlay').show();

        // Get driver_id and order_id from the button's data attributes
        let driver_id = $(this).data('id');
        let order_id = $(this).data('order');
        console.log('Open modal button clicked, driver_id:', driver_id, 'order_id:', order_id);

        $.ajax({
            url: '{{ route('assign-driver') }}', // Ensure this route is defined in your Laravel routes
            type: 'POST', // Or 'POST', depending on your route's method
            data: {
                driver_id: driver_id,
                order_id: order_id,
            },
            success: function(response) {
                console.log('Success:', response);

                // Close the modal on success
                $('#exampleModal').modal('hide');
                $('#orderDetailsModal').modal('hide');
                // Optionally display a success message
                $('#loader').hide();
                $('#overlay').hide();
                alert('Driver assigned successfully!');

            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);

                // Optionally display an error message

            }
        });
    });



    $(document).on('click', '#request-client-cancel', function(e) {
        e.preventDefault();

        var orderId = $(this).data('id');

        if (confirm('Are you sure you want to cancel this order?')) {
            $.ajax({
                url: "{{ route('client-cancel-order') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_id: orderId,
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Order status updated to canceled.');
                    } else {
                        console.log('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to update order status:', error);
                }
            });
        }
    });

    function initMapAssignWorker(drivers) {
        console.log('Initializing initMapAssignWorker...');

        // Initialize the map
        mapDriversAssign = new google.maps.Map(document.getElementById('assignDriverMap'), {
            center: {
                lat: 24.7136,
                lng: 46.6753
            },
            zoom: 5,
            mapTypeId: "roadmap", // Adjust zoom level as needed
        });

        // Clear existing markers
        markersDriversAssign.forEach(marker => marker.setMap(null));
        markersDriversAssign = [];

        // Create a new info window instance
        infoWindowDriversAssign = new google.maps.InfoWindow();

        // Create markers for each driver
        drivers.forEach(driver => {
            // Create a marker element
            const markerElement = createMarkerElement2(driver.profile_image, true);

            // Apply border color based on driver status
            if (driver.status === 1) {
                markerElement.style.border = '3px solid green'; // Green border for status 1
            } else {
                markerElement.style.border = '3px solid red'; // Red border for other statuses
            }

            // Create a custom marker
            const marker = createCustomMarker2(driver, markerElement, driver.lat, driver.lng);

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

                infoWindowDriversAssign.setContent(orderDetails);
                infoWindowDriversAssign.setPosition(new google.maps.LatLng(parseFloat(driver.lat),
                    parseFloat(driver.lng)));
                infoWindowDriversAssign.open(mapDriversAssign);
            });

            markersDriversAssign.push(marker);
        });
    }


    function createMarkerElement2(imageUrl, isDriver = false) {
        const markerElement = document.createElement('div');
        markerElement.style.width = '40px';
        markerElement.style.height = '40px';

        if (isDriver) {
            // Use driver profile image or default user icon
            markerElement.style.backgroundImage = `url(${imageUrl || defaultUserIcon2})`;
        } else {
            // Use shop profile image or default home icon
            markerElement.style.backgroundImage = `url(${imageUrl || defaultHomeIcon2})`;
        }

        markerElement.style.backgroundSize = 'cover';
        markerElement.style.backgroundPosition = 'center';
        markerElement.style.borderRadius = '50%';
        markerElement.style.border = '2px solid #fff';
        markerElement.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
        return markerElement;
    }
    // Helper function to create a custom Google Maps marker
    function createCustomMarker2(data, markerElement, lat, lng) {
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

        customMarker.setMap(mapDriversAssign);
        return customMarker;
    }
</script>











<script>
    let debounceTimer;
    $(document).on('input', '#order_search', function() {
        clearTimeout(debounceTimer);
        var searchTerm = $(this).val();
        debounceTimer = setTimeout(function() {
            $.ajax({
                url: '{{ '' }}',
                data: {
                    searchItem: searchTerm
                },
                success: function(data) {
                    // Handle the response data
                }
            });
        }, 1000); // 1 second delay
    });
</script>

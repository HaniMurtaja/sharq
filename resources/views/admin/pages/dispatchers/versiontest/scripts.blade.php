<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>




<script src="//cdn.jsdelivr.net/npm/sortablejs@1.15.4/Sortable.min.js "></script>




<script>
    var currentTab = 0;
    let formMap;
    let formMap2
    let marker;
    let statistics_route = '{{ route('get-statistics') }}';
    let client_branches_route = '{{ route('client-branches') }}';
    let get_branch_distance_route = "{{ route('get-branch', ':id') }}";
    let get_distance_matrix_route = '{{ route('distance-matrix') }}';
    let save_order_route = '{{ route('save-order') }}';
    document.addEventListener("DOMContentLoaded", function(event) {
        // resetSteps();
        // initFormMap();
        initFormMap2();
        getStatistics();
        setInterval(getStatistics, 5000);
        function getStatistics() {
            $.ajax({
                url: statistics_route,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $.each(response, function(index, element) {
                        // console.log(index, element);

                        $('.' + index).text(element);
                    });

                },
                error: function(xhr) {
                    console.error('Error statistics');
                }
            });
        }






    });

    function closeOrderDrawer() {
        document.getElementById('drawer-overlay').style.display = 'none';
        document.getElementById('drawer').style.transform = 'translateX(100%)'; // Slide out of view
    }



    $(document).ready(function() {

        const openDrawerButtons = document.querySelectorAll('.open-drawer');
        openDrawerButtons.forEach((button) => {
            button.addEventListener('click', function() {
                document.getElementById('drawer-overlay').style.display = 'block';
                document.getElementById('drawer').style.transform = 'translateX(0)';
                // console.log('Modal closed');
                $('input').removeClass('is-invalid');
                $('#regForm')[0].reset();
                $('select').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.text-danger').remove();

                // $('#client_id').select2({
                //     allowClear: true,
                //     placeholder: 'Shop',
                //     escapeMarkup: function(markup) {
                //         return markup;
                //     },
                //     templateResult: function(data) {
                //         if (!data.id) {
                //             return data.text;
                //         }
                //         return data.text;
                //     },
                //     templateSelection: function(data) {
                //         if (!data.id) {
                //             return data.text;
                //         }
                //         return data.text;
                //     }
                // });



                // $('#branch_id').select2({
                //     allowClear: true,
                //     placeholder: 'Branch',
                //     escapeMarkup: function(markup) {
                //         return markup;
                //     },
                //     templateResult: function(data) {
                //         if (!data.id) {
                //             return data.text;
                //         }
                //         return data.text;
                //     },
                //     templateSelection: function(data) {
                //         if (!data.id) {
                //             return data.text;
                //         }
                //         return data.text;
                //     }
                // });





            });
        });
    });







    $(document).on('change', '#client_id', function() {
        var clientID = $(this).val();
        var areaSelect = $('#branch_id');

        if (clientID) {
            $.ajax({
                url: client_branches_route,
                type: 'GET',
                data: {
                    clientID: clientID
                },
                success: function(response) {
                    // console.log(response);
                    areaSelect.prop('disabled', false);
                    areaSelect.empty();
                    areaSelect.append(
                        '<option value="" selected="selected" disabled>Branch</option>'
                    );

                    $.each(response.branches, function(key, branch) {

                        areaSelect.append('<option value="' + branch.id + '">' +
                            branch.name + '</option>');
                    });
                    // console.log(response.fees);

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
        var url = get_branch_distance_route;
        url = url.replace(':id', branch_id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {

                $('#lat_client_branch_hidden').val(response.lat);
                $('#lng_client_branch_hidden').val(response.lng);
                getDurationDistance(response.lat, response.lng);

            }


        });



    });


    $(document).ready(function() {
        $('#payment_method').on('change', function() {
            const selectedValue = parseInt($(this).val());
            if (selectedValue === 2 || selectedValue === 1) {
                $('#order_value').removeAttr('hidden');
            } else {
                $('#order_value').attr('hidden', true).val('');
            }
        });
    });

    function getDurationDistance(lat, lng) {

        var origins = lat + "," + lng;

        var destinations = $('#lat_order_hidden').val() + "," + $('#long_order_hidden').val();
        var params = {
            origins: origins,
            destinations: destinations,
            mode: "driving",
            key: "AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"
        };


        $.ajax({
            url: get_distance_matrix_route,
            type: 'GET',
            data: params,
            success: function(response) {


                if (response.status === "OK") {
                    var distance = response.distance;
                    var duration = response.duration;
                    console.log('distacce: ');

                    console.log(distance);
                    $('#distance').val(distance);

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

        $('#overlay_order').css('display', 'block');
        $('#loader_order').css('display', 'block');

        $.ajax({
            type: 'GET',
            url: save_order_route,
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


                $('#overlay_order').css('display', 'none');
                $('#loader_order').css('display', 'none');

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
                $('#overlay_order').css('display', 'none');
                $('#loader_order').css('display', 'none');
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
            searchBox.setBounds(formMap.getBounds());
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
</script>



<script>
    let markersDriversAssign = [];
    let mapDriversAssign;
    let infoWindowDriversAssign;
    const defaultUserIcon2 = "{{ asset('user.png') }}";
    const defaultHomeIcon2 = "{{ asset('shop.jfif') }}";

    let get_drivers_orders_route = '{{ route('get-driver-orders') }}';
    let assign_driver_modal_route = '{{ route('assign-driver-modal') }}';
    let get_order_history_route = '{{ route('get-order-history') }}';
    let assign_driver_route = '{{ route('assign-driver') }}';
    let client_cancel_order_route = "{{ route('client-cancel-order') }}";


    function openDriverOrdersPopup(order_id, driver_id) {
        console.log(54);

        $.ajax({
            url: get_drivers_orders_route,
            type: 'GET',
            data: {
                order_id: order_id,
                driver_id: driver_id
            },
            success: function(response) {
                console.log('Success:', response);

                $('#driver-order-popup').html('#' + response.order.id);
                $('#driver_name').html('Assign orders to ' + response.driver.full_name);
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
                               <div class="order-number" style="text-align: right;">${order.order_number}</div>

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
                                            <p>${order.order_number || 'N/A'}</p>
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
            url: assign_driver_modal_route,
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
            url: get_order_history_route,
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


        let driver_id = $(this).data('id');
        let order_id = $(this).data('order');
        console.log('Open modal button clicked, driver_id:', driver_id, 'order_id:', order_id);

        $.ajax({
            url: assign_driver_route,
            type: 'GET',
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



    $(document).on('click', '.request-client-cancel', function(e) {
        e.preventDefault();

        var orderId = $(this).data('id');

        if (confirm('Are you sure you want to cancel this order?')) {
            $.ajax({
                url: client_cancel_order_route,
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


    $(document).on('input', '#driver_search', function() {
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



{{-- //fathy --}}
<script>
    $(document).ready(function() {
        var status;
        let currentRequest = null;
        let driverCurrentRequest = null;



        let get_driver_data_route = '{{ route('GetDriversData') }}';
        let get_search_order_data_route = '{{ route('GetSearchOrdersData') }}';
        let accept_cancel_request_route = '{{ route('acceptCancelRequest') }}';
        let get_orders_data_route = '{{ route('GetOrdersData') }}';



        const userPermissions = {
            can_assign_orders: @json(auth()->user()->can('can_assign_orders')),
            can_cancel_orders: @json(auth()->user()->can('can_cancel_orders')),
            can_make_cancel_request: @json(auth()->user()->can('can_make_cancel_request'))
        };


        $('#on-demand-btn').on('click', function() {
            $('#on-demond-search-dev').css('display', 'flex');
            $('#driver-search-dev').css('display', 'none');
        })
        $('#drivers-btn').on('click', function() {
            $('#on-demond-search-dev').css('display', 'none');
            $('#driver-search-dev').css('display', 'flex');
        })

        $("#order_search").on("keyup change ", function() {
            toggleDisplay();
        });



        $("#driver_search").on("keyup change ", function() {
            $('#available').empty();
            $('#busy').empty();
            $('#away').empty();
            $('#offline').empty();
            PAGE_NUMBER = 1;
            fetchDriverData();

        });


        initDispatcherMap();

        function toggleDisplay() {
            console.log($('#order_search').val())

            if ($('#order_search').val() != "") {
                ResetCollapsible();
                $('#jobs-screen').css('display', 'block');
                $('.demondDriver').css('display', 'none');
                $('#pending-order').empty();
                $('#active_order').empty();
                $('#completed_order').empty();
                $('#CancelledOrder').empty();
                fetchSearchData();



            } else {
                console.log('empty')

                $('.demondDriver').css('display', 'block');
                $('#jobs-screen').css('display', 'none');
            }
        }


        $(".toggleButton").on("click", function() {
            status = $(this).data("status");
            var $content = $(".collapseContent-" + status);

            if ($content.css("maxHeight") === "0px") {
                $content.find('.order_items').empty()
                fetchData(status, orders_details, $content.find('.order_items'));

            }
        });


        if (@json(auth()->user()->user_role) == 1 || @json(auth()->user()->user_role) == 4) {
            setInterval(fetchDriverData, 10000);
        }
















        function fetchDriverData() {
            if (driverCurrentRequest) {
                console.log('aborting previous request');
                driverCurrentRequest.abort();
            }

            driverCurrentRequest = $.ajax({
                url: get_driver_data_route,
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,
                    search: $('#driver_search').val()
                },
                success: function(response) {
                    // Update counts
                    for (const [status, count] of Object.entries(response.operator_counts)) {
                        $(`#${status}_count`).text(count);
                    }




                    const statuses = ['available', 'away', 'busy', 'offline'];
                    statuses.forEach(status => {
                        const drivers = response[status];

                        if (drivers && drivers.length > 0) {

                            drivers.forEach(driver => {
                                const driverCard = `
                        <div class="customcard">
                            <div class="cardLeftSide mous-click-new"

                                 onclick="openDriverPopup(${driver.lat}, ${driver.lng}, ${driver.id})">
                                <div class="cardImageWrapper">

                                </div>
                                <div class="cardContent">
                                    <span>${driver.full_name || 'No Name'}</span>
                                    <span>${driver.phone || 'No Phone'}</span>
                                </div>
                            </div>
                            <div class="cardRightSide">
                                <span>${driver.filtered_orders_count || 0}</span>
                            </div>
                        </div>`;

                                // Append driver card to respective tab
                                $(`#${status}`).append(driverCard);
                            });
                        }
                    });
                    // PAGE_NUMBER++;
                },
                complete: function() {
                    driverCurrentRequest =
                        null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }

            });
        }



        function fetchSearchData() {
            if (currentRequest) {
                console.log('aborting previous request');
                currentRequest.abort();
            }

            currentRequest = $.ajax({
                url: get_search_order_data_route,
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,
                    search: $('#order_search').val()
                },
                success: function(response) {
                    $('#count').html(response.order_data.order_count + ' top results');
                    Object.keys(response).forEach(function(key) {
                        if (key !== 'order_data') {
                            var tabElement = $('#' + key);
                            var userRole = response.order_data.user_role;

                            response[key].forEach(function(data) {
                                let dropdownItems = '';


                                dropdownItems += `
                            @can('can_assign_orders')
                                    <li>
                                        <a class="dropdown-item open-modal-test" href="#"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        data-id="${data.id}">
                                        Assign
                                        </a>
                                    </li>
                                    @endcan
                                    `;


                                // Check if the user can cancel orders and the order status matches
                                if ((data.status_value === 22 || data
                                        .status_value === 21)) {
                                    dropdownItems += `
                           @can('can_accept_cancel_request')
                            <li>
                                <a class="dropdown-item accept-cancel-request" href="#"
                                data-id="${data.id}">
                                Accept
                                </a>
                            </li>
                            @endcan`;
                                }

                                // Check if the user can make a cancel request and the status does not match specific values
                                if (![9, 10, 22].includes(data.status_value)) {
                                    dropdownItems += `
                            @can('can_make_cancel_request')
                                <li>
                                    <a class="dropdown-item request-client-cancel" href="#"
                                    data-id="${data.id}">
                                    Cancel
                                    </a>
                                </li>
                                @endcan`;
                                }


                                // Always add the log item
                                dropdownItems += `
                                <li>
                                    <a class="dropdown-item order-history-btn" href="#"
                                    data-id="${data.id}" data-bs-toggle="modal"
                                    data-bs-target="#historyModal">
                                    Log
                                    </a>
                                </li>`;

                                var customerCardHtml = `
                            <div class="customcard">
                                <div class="cardLeftSide mous-click-new" data-popup=""
                                     ${ userPermissions.can_assign_orders ? `ondblclick="openAssignDriverModal(${data.id})"` : ''}
                                     onclick="openOrderPopup(${data.id}, ${data.lat}, ${data.lng}, ${data.branch_lat}, ${data.branch_lng}, ${userRole})">
                                    <div class="cardImageWrapper">
                                        <img src="${data.shop_profile || 'https://fakeimg.pl/300/'}" alt="Branch Image" width="100" height="100">
                                    </div>
                                    <div class="cardContent">
                                        <span>${data.branch_name || 'N/A'}</span>
                                        <span>${data.order_address || 'Unknown Location'}</span>
                                        <span class="status">${data.status_label || 'Unknown'}</span>
                                    </div>
                                </div>
                                <div class="cardRightSide">
                                    <div class="cardIdStatus">
                                        <span>#${data.order_id || 'N/A'}</span>
                                        <div class="dropdown">
                                            <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                <svg width="1.6rem" height="1.6rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.33333 9.33325C2.6 9.33325 2 8.73325 2 7.99992C2 7.26659 2.6 6.66659 3.33333 6.66659C4.06667 6.66659 4.66667 7.26659 4.66667 7.99992C4.66667 8.73325 4.06667 9.33325 3.33333 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                                    <path d="M12.6673 9.33325C11.934 9.33325 11.334 8.73325 11.334 7.99992C11.334 7.26659 11.934 6.66659 12.6673 6.66659C13.4007 6.66659 14.0007 7.26659 14.0007 7.99992C14.0007 8.73325 13.4007 9.33325 12.6673 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                                    <path d="M7.99935 9.33325C7.26602 9.33325 6.66602 8.73325 6.66602 7.99992C6.66602 7.26659 7.26602 6.66659 7.99935 6.66659C8.73268 6.66659 9.33268 7.26659 9.33268 7.99992C9.33268 8.73325 8.73268 9.33325 7.99935 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                                </svg>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">${dropdownItems}</ul>
                                        </div>
                                    </div>
                                    <div class="cardTimeOperations">
                                        <div class="driverCardImage">
                                            <img src="${data.driver_photo || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}" alt="Driver Image" width="100" height="100" />
                                        </div>

                                        <p class="time-difference" data-cancelled_date = "${data.cancelled_date}" data-delivery-date ="${data.delivery_date}" data-created-date = "${data.created_date}" data-status-value= "${data.status_value}" data-time="${data.status_date_time}"></p>
                                    </div>
                                </div>
                            </div>`;

                                // Append the card HTML to the tab
                                tabElement.append(customerCardHtml);
                            });
                        }
                    });


                    setInterval(function() {
                        $('.time-difference').each(function() {
                            const statusValue = $(this).data('status-value');
                            const createdDate = new Date($(this).data(
                                'created-date')); // Created date
                            const deliveryDate = new Date($(this).data(
                                'delivery-date'));

                            const cancelledDate = new Date($(this).data(
                                'cancelled_date'));


                            if (statusValue == 9) {

                                const diff = Math.max(0, Math.floor((deliveryDate -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            } else if (statusValue == 10) {
                                // Calculate the static difference between cancelled and created dates
                                const diff = Math.max(0, Math.floor((cancelledDate -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            } else {

                                const now = new Date();
                                const riyadhOffset = 3 * 60; // Riyadh is UTC+3
                                const riyadhNow = new Date(
                                    now.getTime() + now.getTimezoneOffset() *
                                    60000 + riyadhOffset * 60000
                                );

                                const diff = Math.max(0, Math.floor((riyadhNow -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            }
                        });
                    }, 1000);




                    PAGE_NUMBER++;
                },
                complete: function() {
                    currentRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }







        $('.tab-pane-orders').on('scroll', function() {
            console.log('scrole');
            var $this = $(this);
            console.log({
                scrollTop: $this.scrollTop(),
                innerHeight: $this.innerHeight(),
                scrollHeight: $this[0].scrollHeight
            });

            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 1) {
                console.log(7777);
                fetchSearchData();
            }

        });


        $('.tab-pane-drivers').on('scroll', function() {

            var $this = $(this);


            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 1) {

                fetchDriverData();
            }

        });



        $('.nav-link').on('click', function() {
            const otherContent = $(".tab-pane");

        })



        $(document).on('scroll', '#pending-order', function() {
            var $this = $(this);




        });

        $(document).on('click', '.accept-cancel-request', function() {

            var orderId = $(this).data('id');
            $.ajax({
                url: accept_cancel_request_route,
                method: 'GET',
                data: {
                    id: orderId,

                },
                success: function(response) {



                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });



        })




        function fetchData(status, orders_details, element_class) {
            if (currentRequest) {
                currentRequest.abort();
            }

            currentRequest = $.ajax({
                url: get_orders_data_route,
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,
                    status: status,
                    search: $('#order_search').val()
                },
                success: function(response) {
                    const userRole = response.user_role; // Get the user's role

                    response.orders.forEach(function(data) {

                        let dropdownItems = '';

                        // Check if the user can assign orders
                        dropdownItems += `
                            @can('can_assign_orders')
                                    <li>
                                        <a class="dropdown-item open-modal-test" href="#"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        data-id="${data.id}">
                                        Assign
                                        </a>
                                    </li>
                                    @endcan
                                    `;


                        // Check if the user can cancel orders and the order status matches
                        if ((data.status_value === 22 || data.status_value === 21)) {
                            dropdownItems += `
                            @can('can_accept_cancel_request')
                            <li>
                                <a class="dropdown-item accept-cancel-request" href="#"
                                data-id="${data.id}">
                                Accept
                                </a>
                            </li>
                            @endcan`;
                        }

                        // Check if the user can make a cancel request and the status does not match specific values
                        if (![9, 10, 22].includes(data.status_value)) {
                            dropdownItems += `
                            @can('can_make_cancel_request')
                                <li>
                                    <a class="dropdown-item request-client-cancel" href="#"
                                    data-id="${data.id}">
                                    Cancel
                                    </a>
                                </li>
                                @endcan`;
                        }

                        // Always add the log item
                        dropdownItems += `
                                <li>
                                    <a class="dropdown-item order-history-btn" href="#"
                                    data-id="${data.id}" data-bs-toggle="modal"
                                    data-bs-target="#historyModal">
                                    Log
                                    </a>
                                </li>`;
                        const statusDateTime = new Date(data
                            .status_date_time); // Convert to Date object

                        var customerCardHtml = `
                <div class="customcard">
                    <div class="cardLeftSide mous-click-new"
                           ${userPermissions.can_assign_orders ? `ondblclick="openAssignDriverModal(${data.id})"` : ''}
                         onclick="openOrderPopup(${data.id}, ${data.lat}, ${data.lng}, ${data.branch_lat}, ${data.branch_lng}, ${userRole})">
                        <div class="cardImageWrapper">
                            <img src="${data.shop_profile || 'https://fakeimg.pl/300/'}" alt="Branch Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>${data.branch_name}</span>
                            <span>${data.shop_name}</span>
                            <span class="status">${data.status_label}</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">
                            <span>#${data.order_id}</span>
                            <div class="dropdown">
                                <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg width="1.6rem" height="1.6rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.33333 9.33325C2.6 9.33325 2 8.73325 2 7.99992C2 7.26659 2.6 6.66659 3.33333 6.66659C4.06667 6.66659 4.66667 7.26659 4.66667 7.99992C4.66667 8.73325 4.06667 9.33325 3.33333 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                        <path d="M12.6673 9.33325C11.934 9.33325 11.334 8.73325 11.334 7.99992C11.334 7.26659 11.934 6.66659 12.6673 6.66659C13.4007 6.66659 14.0007 7.26659 14.0007 7.99992C14.0007 8.73325 13.4007 9.33325 12.6673 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                        <path d="M7.99935 9.33325C7.26602 9.33325 6.66602 8.73325 6.66602 7.99992C6.66602 7.26659 7.26602 6.66659 7.99935 6.66659C8.73268 6.66659 9.33268 7.26659 9.33268 7.99992C9.33268 8.73325 8.73268 9.33325 7.99935 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">${dropdownItems}</ul>
                            </div>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                                <img src="${data.driver_photo || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}" alt="Driver Image"/>
                            </div>
                            <p class="time-difference" data-cancelled_date = "${data.cancelled_date}" data-delivery-date ="${data.delivery_date}" data-created-date = "${data.created_date}" data-status-value= "${data.status_value}" data-time="${data.status_date_time}"></p>
                        </div>
                    </div>
                </div>
                `;

                        element_class.append(customerCardHtml);
                    });

                    setInterval(function() {
                        $('.time-difference').each(function() {
                            const statusValue = $(this).data('status-value');
                            const createdDate = new Date($(this).data(
                                'created-date')); // Created date
                            const deliveryDate = new Date($(this).data(
                                'delivery-date'));

                            const cancelledDate = new Date($(this).data(
                                'cancelled_date'));


                            if (statusValue == 9) {

                                const diff = Math.max(0, Math.floor((deliveryDate -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            } else if (statusValue == 10) {
                                // Calculate the static difference between cancelled and created dates
                                const diff = Math.max(0, Math.floor((cancelledDate -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            } else {

                                const now = new Date();
                                const riyadhOffset = 3 * 60; // Riyadh is UTC+3
                                const riyadhNow = new Date(
                                    now.getTime() + now.getTimezoneOffset() *
                                    60000 + riyadhOffset * 60000
                                );

                                const diff = Math.max(0, Math.floor((riyadhNow -
                                    createdDate) / 1000));

                                const hours = Math.floor(diff / 3600);
                                const minutes = Math.floor((diff % 3600) / 60);
                                const seconds = diff % 60;

                                $(this).text(
                                    `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                                );
                            }
                        });
                    }, 1000);





                    PAGE_NUMBER++;
                },
                complete: function() {
                    currentRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }



        $('.order_items').on('scroll', function() {
            var $this = $(this);
            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight) {

                fetchData(status, orders_details, $(".collapseContent-" + status).find('.order_items'));
                // PAGE_NUMBER++;
            }
        });
    });


    function ResetCollapsible() {
        const collapsibleSections = document.querySelectorAll(".collapsible");
        collapsibleSections.forEach(otherSection => {
            const otherContent = otherSection.querySelector(".collapseContent");
            otherContent.style.maxHeight = "0px";
            otherContent.classList.add("max-h-0");
        });
        PAGE_NUMBER = 1;
        orders_details.length = 0;
    }






















    //dispatcher map


    let get_data_map_route = '{{ route('getMapData') }}'
    let markers = [];
    let detailMarkers = [];
    let map;
    let infoWindow;
    const defaultUserIcon = "{{ asset('user.png') }}";
    const defaultHomeIcon = "{{ asset('shop.jfif') }}";
    let mapCurrentRequest = null;
    let openInfoWindows = [];
    let activePolylines = [];
    let activeBranchMarkers = [];
    let popup;
    let driverboundes;
    let orderboundes;
    let branchLocations;
    let map_center;




    function openDriverPopup(lat, lng, id) {
        console.log(id, lat, lng);

        $.ajax({
            url: '{{ route('get-driver-popup') }}',
            method: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                // Close any open InfoWindows and clear markers/polylines
                openInfoWindows.forEach(infoWindow => infoWindow.close());
                openInfoWindows = [];
                activePolylines.forEach(polyline => polyline.setMap(null));
                activePolylines = [];
                activeBranchMarkers.forEach(marker => marker.setMap(null));
                activeBranchMarkers = [];

                // Parse the order location
                const orderLocation = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));

                // Adjust map center and zoom
                map.setCenter(orderLocation);
                map.setZoom(8); // Adjust zoom level as needed

                // Calculate the popup's position with an offset
                const infoWindowPosition = new google.maps.LatLng(
                    parseFloat(lat) + 0.05, // Adjust the vertical offset
                    parseFloat(lng)
                );

                // Create the InfoWindow
                const infoWindow = new google.maps.InfoWindow({
                    content: response.infoWindowContent,
                    position: infoWindowPosition,
                });

                // Open the InfoWindow
                infoWindow.open(map);
                openInfoWindows.push(infoWindow);

                // // Ensure the InfoWindow is fully visible on the map
                // const bounds = new google.maps.LatLngBounds();
                // bounds.extend(orderLocation);
                // bounds.extend(infoWindowPosition);
                // map.fitBounds(bounds, 100); // Add padding of 100px to ensure the popup fits

                // Handle InfoWindow close event
                google.maps.event.addListener(infoWindow, 'closeclick', () => {
                    console.log({{ $InitializingMap['lat'] }});

                    map.setCenter({
                        lat: {{ $InitializingMap['lat'] }},
                        lng: {{ $InitializingMap['lng'] }}
                    });
                    map.setZoom({{ $InitializingMap['zoom'] }});

                    detailMarkers.forEach(marker => marker.setMap(null));
                    detailMarkers = [];
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }



    $(document).on('click', '.close-order-popup-map', function(e) {
        e.preventDefault();
        if ($('#mapContainer').find('.mainPopup').length > 0) {
            $('#mapContainer').find('.mainPopup').remove();
        }
        activePolylines.forEach(polyline => polyline.setMap(null));
        activePolylines = [];
        console.log(map_center.lat, map_center.lng);

        map.setCenter({
            lat: {{ $InitializingMap['lat'] }},
            lng: {{ $InitializingMap['lng'] }}
        });


        map.setZoom({{ $InitializingMap['zoom'] }});
        detailMarkers.forEach(marker => marker.setMap(null));
        detailMarkers = [];


    });

    function openOrderPopup(id, lat, lng, branch_lat, branch_lng) {

        if ($('#mapContainer').find('.mainPopup').length > 0) {
            $('#mapContainer').find('.mainPopup').remove();
        }


        activePolylines.forEach(polyline => polyline.setMap(null));
        activePolylines = [];


        if (!(map instanceof google.maps.Map)) {
            console.error('Map object is not initialized properly.');
            return;
        }

        $.ajax({
            url: '{{ route('get-order-popup') }}',
            method: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                openInfoWindows.forEach(infoWindow => infoWindow.close());
                openInfoWindows = [];
                activePolylines.forEach(polyline => polyline.setMap(null));
                activePolylines = [];

                activeBranchMarkers.forEach(marker => marker.setMap(null));
                activeBranchMarkers = [];

                const orderLocation = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
                const branchLocation = new google.maps.LatLng(parseFloat(branch_lat),
                    parseFloat(
                        branch_lng));

                // Center the map and zoom in on the order location



                console.log(map);




                map.setCenter(orderLocation);
                map.setZoom(8); // Adjust zoom level as needed

                // Draw a black line between the order and the branch
                const polyline = new google.maps.Polyline({
                    path: [orderLocation, branchLocation],
                    geodesic: true,
                    strokeColor: '#000000', // Black color for the line
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                polyline.setMap(map);
                activePolylines.push(polyline);

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
                activeBranchMarkers.push(branchMarker);
                // Increase space between the popup and the line
                // const infoWindowPosition = new google.maps.LatLng(
                //     parseFloat(lat) +
                //     0.05, // Adjust this offset for more space (larger value = more space)
                //     parseFloat(lng)
                // );
                // let infoWindow = new google.maps.InfoWindow();
                // Open the info window with the popup content
                // infoWindow.setContent(response.infoWindowContent);
                // infoWindow.setPosition(infoWindowPosition);
                // infoWindow.open(map);
                const mapContainer = $('#mapContainer');
                console.log(mapContainer, response.infoWindowContent);

                mapContainer.append(response.infoWindowContent);
                // openInfoWindows.push(infoWindow);


                // google.maps.event.addListener(infoWindow, 'closeclick', () => {
                //     polyline.setMap(null);
                //     branchMarker.setMap(null);
                // });
            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }























    function initDispatcherMap() {
        console.log('Initializing initDispatcherMap map2222...');


        map = new google.maps.Map(document.getElementById('map'), {


            center: {
                lat: {{ $InitializingMap['lat'] }},
                lng: {{ $InitializingMap['lng'] }}

            },
            zoom: {{ $InitializingMap['zoom'] }}
        });

        console.log('zoom', {{ $InitializingMap['zoom'] }});

        fetchMapData();

        // Fetch data periodically
        setInterval(fetchMapData, 15000);
    }


    //end dispatcher map
</script>
{{-- //fathy --}}
@if (auth()->user()->user_role == \App\Enum\UserRole::CLIENT)
    @include('admin.pages.dispatchers.Map.Client')
@elseif(auth()->user()->user_role == \App\Enum\UserRole::BRANCH)
    @include('admin.pages.dispatchers.Map.Branch')
@else
    @include('admin.pages.dispatchers.Map.Admin')
@endif

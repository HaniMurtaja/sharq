<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>



<script src="//cdn.jsdelivr.net/npm/sortablejs@1.15.4/Sortable.min.js "></script>


<script>
    var currentTab = 0;
    let formMap;
    let formMap2
    let marker;


    let statistics_route = "{{ route('get-statistics') }}";
    let client_branches_route = '{{ route('client-branches') }}';
    let get_branch_distance_route = "{{ route('get-branch', ':id') }}";
    let get_distance_matrix_route = '{{ route('distance-matrix') }}';
    let save_order_route = '{{ route('save-order') }}';
    let Order_UnAssign_route = '{{ route('UnassignDriver') }}';
    let ChangeStatusToDelivered = '{{ route('ChangeStatusToDelivered') }}';


    var orderFilter = 1;

    document.addEventListener("DOMContentLoaded", function(event) {
        // resetSteps();
        // initFormMap();


        getStatistics();
        //setInterval(getStatistics, 5000);









        function getStatistics() {
            $.ajax({
                url: statistics_route,
                method: 'GET',
                data: {
                    filter: orderFilter,
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


        $('.jobs-filter').on('click', function() {
            console.log('hiiiiii');
            
            var selectedText = $(this).text().trim();
            var filterVal = $(this).data('value');

            orderFilter = filterVal;

            $('#allJobsDropdown h4').text(selectedText);
            $('#allJobsDropdown').dropdown('hide');

            if (orderFilter === 1) {
                $('.collapseCustomBtn').show();
            } else if (orderFilter === 0) {
                $('.collapseCustomBtn').each(function() {
                    var status = $(this).data('status');
                    if (status === 'pending-order') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });


        if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 5) {
            //  alert('تم الوصول للنهاية');
            fetchData(status, orders_details, $(".collapseContent-" + status).find('.order_items'),
                orderFilter);
        }

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
        let get_search_order_data_delivered_route = '{{ route('GetSearchOrdersDataDELIVERED') }}';
        let accept_cancel_request_route = '{{ route('acceptCancelRequest') }}';
        let get_orders_data_route = '{{ route('GetOrdersData') }}';

        let currentFilter = '';

        let currentTab = 'active-tab';

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

        // $("#order_search").on("keyup change ", function() {
        //     toggleDisplay();
        // });



        $("#driver_search").on("keyup change ", function() {
            $('#available').empty();
            $('#busy').empty();
            $('#away').empty();
            $('#offline').empty();
            PAGE_NUMBER = 1;
            // fetchDriverData();

        });




        function toggleDisplay() {
            // console.log($('#order_search').val())

            if ($('#order_search').val() != "") {
                ResetCollapsible();
                $('#jobs-screen').css('display', 'block');
                $('.demondDriver').css('display', 'none');
                // $('#pending-order').empty();
                // $('#active_order').empty();
                // $('#completed_order').empty();
                // $('#CancelledOrder').empty();

                $('#jobsTabs').empty();
                //  fetchSearchData();
            } else {
                // console.log('empty')

                $('.demondDriver').css('display', 'block');
                $('#jobs-screen').css('display', 'none');
            }
        }
        $(document).on('input', '#order_search', function() {
            // alert('f');
            clearTimeout(debounceTimer);
            var searchTerm = $(this).val();
            debounceTimer = setTimeout(function() {
                toggleDisplay()
            }, 1000); // 1 second delay
        });

        $(".toggleButton").on("click", function() {
            status = $(this).data("status");
            var $content = $(".collapseContent-" + status);

            if ($content.css("maxHeight") === "0px") {
                $content.find('.order_items').empty()
                fetchData(status, orders_details, $content.find('.order_items'));

            }
        });


        if (@json(auth()->user()->user_role) == 1 || @json(auth()->user()->user_role) == 4) {
            // setInterval(fetchDriverData, 10000);
            fetchDriverData();
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
                                    <img src="${driver.photo || 'https://fakeimg.pl/300/'}"
                                         alt="Driver Image"
                                         width="100" height="100">
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



        // ✅ عند الضغط على تبويب (Active أو Completed)
        $(document).on('click', '#ordersearchTabs button', function() {
            console.log('🔄 Tab clicked:', $(this).attr('id'));

            $('#ordersearchTabs button').removeClass('active');
            $(this).addClass('active');

            // تحديث التبويب النشط
            currentTab = $(this).attr('id');
            currentFilter = (currentTab === 'completed-tab') ? 9 : '';

            // **امسح البيانات فورًا عند تغيير التبويب**
            $('#jobsTabs').empty().html(
                "<p  id='loading_section' style='width:fit-content;' class='text-center mx-auto'  class='text-center w-100 mx-auto'> <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200' width='50' height='50'><radialGradient id='a12' cx='.66' fx='.66' cy='.3125' fy='.3125' gradientTransform='scale(1.5)'><stop offset='0' stop-color='#B21F26'></stop><stop offset='.3' stop-color='#B21F26' stop-opacity='.9'></stop><stop offset='.6' stop-color='#B21F26' stop-opacity='.6'></stop><stop offset='.8' stop-color='#B21F26' stop-opacity='.3'></stop><stop offset='1' stop-color='#B21F26' stop-opacity='0'></stop></radialGradient><circle transform-origin='center' fill='none' stroke='url(#a12)' stroke-width='15' stroke-linecap='round' stroke-dasharray='200 1000' stroke-dashoffset='0' cx='100' cy='100' r='70'><animateTransform type='rotate' attributeName='transform' calcMode='spline' dur='2' values='360;0' keyTimes='0;1' keySplines='0 0 1 1' repeatCount='indefinite'></animateTransform></circle><circle transform-origin='center' fill='none' opacity='.2' stroke='#B21F26' stroke-width='15' stroke-linecap='round' cx='100' cy='100' r='70'></circle></svg> </p>"
            );
            PAGE_NUMBER = 1;
            fetchSearchData();
        });


        $('#order_search').on('input', function() {
            console.log('🔍 Search input changed:', $(this).val());

            fetchSearchData();
        });



        function fetchSearchData() {
            if (currentRequest) {
                currentRequest.abort();
            }
            console.log('Fetching data with tapFilter:', currentFilter); // Debugging

            let searchQuery = $('#order_search').val(); // Get search input value

            let requestUrl = get_search_order_data_route +
                `?page=${PAGE_NUMBER}&search=${encodeURIComponent(searchQuery)}`;

            // If completed tab is selected, add tapFilter=9 to the URL
            if (currentFilter) {
                requestUrl += `&tapFilter=${currentFilter}`;
            }
            currentRequest = $.ajax({
                url: requestUrl,
                method: 'GET',
                // data: {
                //     page: PAGE_NUMBER,
                //     search: $('#order_search').val()
                // },
                beforeSend: function() {
                    console.log('Fetching data...');
                    if ($('#jobsTabs').html() === '') {
                        // $('#jobsTabs').html('<p class="text-center">Loading...</p>');
                    }
                },
                success: function(response) {
                    // console.log(response)
                    // $('#jobsTabs').empty();
                    // $('#jobsTabs').empty();
                    $('#loading_section').css('display', 'none');
                    if (!response.result.length && PAGE_NUMBER == 1) {
                        console.log('howaida', PAGE_NUMBER);

                        $('#jobsTabs').html('<p class="text-center">❌ No orders found</p>');
                        return;
                    }

                    $('#search_order_count').html(response.order_count + ' top results');
                    var userRole = response.user_role


                    response.result.forEach(function(data) {
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
                        dropdownItems += `
                            @can('can_unassign_orders')
                                <li>
                                    <a class="dropdown-item UnAssign"
                                     data-id="${data.id}"
                                     href="#">
                                        UnAssign
                                        </a>
                                    </li>
                                    @endcan
                                `;
                        dropdownItems += `
                            @can('can_change_status_to_delivered_orders')
                                <li>
                                    <a class="dropdown-item ChangeStatusToDelivered"
                                     data-id="${data.id}"
                                     href="#">
                                       Delivered
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
                                 <div class="customcard" id="order-id-${data.id}">

                                    <input type="checkbox" class=" card-checkbox-search position-absolute top-8px" data-order-id="${data.id}" >

                                <div class="cardLeftSide mous-click-new" data-popup=""
                                     ${ userPermissions.can_assign_orders ? `ondblclick="openAssignDriverModal(${data.id})"` : ''}
                                     onclick="openOrderPopup(${data.id}, ${data.lat}, ${data.lng}, ${data.branch_lat}, ${data.branch_lng}, ${userRole})">
                                    <div class="cardImageWrapper">
                                        <img src="${data.shop_profile || 'https://fakeimg.pl/300/'}" alt="Branch Image" width="100" height="100">
                                    </div>
                                    <div class="cardContent">
                                        <span>${data.branch_name || 'N/A'}</span>
                                        <span>${data.order_address || 'Unknown Location'}</span>
                                        <span class="status" style="background-color: ${getStatusColor(data.status_label)};color: #fff;">
                                ${data.status_label}
                            </span>
                            <!--<span class="status">${data.status_label}</span>-->
                                    </div>
                                </div>
                                <div class="cardRightSide">
                                    <div class="cardIdStatus">
                                        <div class="text-slide-wrapper">
                                            <span class="text-slide"> #${data.order_id || 'N/A'}</span>
                            </div>
                                        <!--<span>#${data.order_id || 'N/A'}</span>-->
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
                        $('#jobsTabs').append(customerCardHtml);

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
                    // search: $('#order_search').val()
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
    //let markers = [];
    let detailMarkers = [];
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

    let driverRoutingControl = null; // Track the driver route control

    let driverPopup = null; // Track the driver popup
    let orderMarkers = []; // Track order markers
    let driverRoutingControls = []; // Track all routing controls

    function openDriverPopup(lat, lng, id) {
        $.ajax({
            url: '{{ route('get-driver-popup') }}',
            method: 'GET',
            data: {
                id: id, // Pass driver ID
            },
            success: function(response) {
                const driverInfo = response.infoWindowContent;
                const orders = response.orders;

                // Clear existing markers and routes
                if (driverPopup) {
                    map.closePopup(driverPopup);
                }
                orderMarkers.forEach(marker => map.removeLayer(marker));
                driverRoutingControls.forEach(control => map.removeControl(control));
                orderMarkers = [];
                driverRoutingControls = [];

                // Display driver popup
                driverPopup = L.popup({
                        // maxWidth: 600,
                    })
                    .setLatLng([lat, lng]) // Driver's location
                    .setContent(driverInfo || `<p>Driver Info Unavailable</p>`)
                    .openOn(map);

                // Add markers for each order
                orders.forEach(order => {
                    const orderLat = parseFloat(order.lat_order);
                    const orderLng = parseFloat(order.lng_order);
                    const marker = L.marker([orderLat, orderLng], {
                            icon: L.divIcon({
                                className: "custom-order-icon",
                                html: `<div style="width: 40px; height: 40px; background-color: #f46624; border-radius: 50%; text-align: center; line-height: 40px; color: white; font-weight: bold;">${order.order_number}</div>`,
                                iconSize: [40, 40],
                                iconAnchor: [20, 20],
                            }),
                        })
                        .addTo(map)
                        .bindPopup(`
                        <div>
                            <h4>${order.shop_name}</h4>
                            <p><strong>Branch:</strong> ${order.branch_name}</p>
                            <p><strong>Phone:</strong> ${order.customer_phone}</p>
                            <p><strong>Status:</strong> ${order.status}</p>
                        </div>
                    `);

                    orderMarkers.push(marker);

                    // Draw route between driver and the order
                    const routeControl = L.Routing.control({
                        waypoints: [
                            L.latLng(lat, lng), // Driver's location
                            L.latLng(orderLat, orderLng), // Order location
                        ],
                        routeWhileDragging: false,
                        addWaypoints: false,
                        show: false, // Hide the routing panel
                        lineOptions: {
                            styles: [{
                                color: 'blue',
                                weight: 4,
                                opacity: 0.7
                            }],
                        },
                    }).addTo(map);

                    driverRoutingControls.push(routeControl);
                });

                // Adjust map view to fit all points (driver and orders)
                const bounds = L.latLngBounds(
                    orders.map(order => [parseFloat(order.lat_order), parseFloat(order.lng_order)])
                    .concat([
                        [lat, lng]
                    ])
                );
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching driver or order details:', error);
                alert('Failed to load data. Please try again.');
            },
        });
    }


    $(document).on('click', '.close-order-popup-map', function(e) {



    });


    // Function to fetch order details and show on the map
    let infoPopup = null; // Global variable to track the popup
    let orderRoutingControl = null; // Global variable to track the routing control

    function openOrderPopup(id, lat, lng, branch_lat, branch_lng) {

        // Validate latitude and longitude values
        function isValidLatLng(lat, lng) {
            return (
                typeof lat === 'number' &&
                typeof lng === 'number' &&
                !isNaN(lat) &&
                !isNaN(lng) &&
                lat >= -90 &&
                lat <= 90 &&
                lng >= -180 &&
                lng <= 180
            );
        }

        // Check if the order location and branch location are valid
        if (!isValidLatLng(lat, lng)) {
            alert('Invalid order location coordinates.');
            return;
        }

        if (!isValidLatLng(branch_lat, branch_lng)) {
            alert('Invalid branch location coordinates.');
            return;
        }

        // Proceed with the AJAX request
        $.ajax({
            url: '{{ route('get-order-popup-newmap') }}',
            method: 'GET',
            data: {
                id: id, // Pass the order ID to fetch the relevant data
            },
            success: function(response) {
                const htmlContent = response.infoWindowContent;
                const driverInfo = response.infoDriver;

                // Remove existing popup if present
                if (infoPopup) {
                    map.closePopup(infoPopup);
                }

                // Center the map and zoom in on the order location
                map.setView([lat, lng], 15);

                // Create a new popup with the HTML content
                infoPopup = L.popup()
                    .setLatLng([lat, lng])
                    .setContent(htmlContent)
                    .openOn(map);

                // Remove existing route if present
                if (orderRoutingControl) {
                    map.removeControl(orderRoutingControl);
                }

                // Add a marker for the branch location
                L.marker([branch_lat, branch_lng], {
                    icon: L.divIcon({
                        className: 'custom-branch-icon',
                        html: `<div style="
                        width: 40px;
                        height: 40px;
                        background-color: #28a745;
                        border-radius: 50%;
                        text-align: center;
                        line-height: 40px;
                        color: white;
                        font-weight: bold;
                    ">B</div>`,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                    }),
                }).addTo(map).bindPopup(`Branch Location`);

                // Check if driver info exists and has valid coordinates

                if (driverInfo && !isValidLatLng(driverInfo.lat, driverInfo.lng)) {
                    // Add a marker for the driver's location

                    L.marker([driverInfo.lat, driverInfo.lng], {
                        icon: L.divIcon({
                            className: 'custom-driver-icon',
                            html: `<div style="
                            width: 40px;
                            height: 40px;
                            background-color: #007bff;
                            border-radius: 50%;
                            text-align: center;
                            line-height: 40px;
                            color: white;
                            font-weight: bold;
                        ">D</div>`,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20],
                        }),
                    }).addTo(map).bindPopup(`Driver Location`);

                    // Draw a route from the branch to the driver, and then to the order location
                    orderRoutingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(branch_lat, branch_lng),
                            L.latLng(driverInfo.lat, driverInfo.lng),
                            L.latLng(lat, lng),
                        ],
                        routeWhileDragging: false,
                        addWaypoints: false,
                        show: false,
                        lineOptions: {
                            styles: [{
                                color: 'blue',
                                weight: 4,
                                opacity: 0.7
                            }],
                        },
                    }).addTo(map);
                } else {
                    // If driver location is invalid, draw a route only from the branch to the order location
                    orderRoutingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(branch_lat, branch_lng),
                            L.latLng(lat, lng),
                        ],
                        routeWhileDragging: false,
                        addWaypoints: false,
                        show: false,
                        lineOptions: {
                            styles: [{
                                color: 'blue',
                                weight: 4,
                                opacity: 0.7
                            }],
                        },
                    }).addTo(map);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching order details:', error);
                alert('Failed to load order details. Please try again.');
            },
        });
    }
</script>

<script>
    function getStatusColor(status) {
        switch (status) {
            case 'Order created':
                return '#4CAF50'; // Green - Success
            case 'Pending driver acceptance':
                return '#2196F3'; // Blue - Information
            case 'Pending order preparation':
                return '#03A9F4'; // Light Blue - Informational
            case 'Arrived to pickup':
                return '#FF9800'; // Orange - In Progress
            case 'Order picked up':
                return '#F06292'; // Amber - In Progress
            case 'Arrived to dropoff':
                return '#009688'; // Teal - In Progress
            case 'Order delivered':
                return '#388E3C'; // Dark Green - Success
            case 'Order cancelled':
                return '#F44336'; // Red - Error
            case 'Driver acceptance timeout':
                return '#FFEB3B'; // Yellow - Warning
            case 'Driver accepted the order':
                return '#3F51B5'; // Indigo - Confirmation
            case 'Driver rejected the order':
                return '#FF5722'; // Deep Orange - Rejection
            case 'Order Unassigned':
                return '#9E9E9E'; // Grey - Neutral
            case 'Order failed':
                return '#B71C1C'; // Dark Red - Critical Error
            case 'Order Cancellation is being Proccessed':
                return '#9C27B0'; // Purple - Special
            case 'Client Cancellation Request is being Proccessed':
                return '#673AB7'; // Deep Purple - Special
            default:
                return '#9E9E9E'; // Grey - Default Neutral
        }
    }
</script>

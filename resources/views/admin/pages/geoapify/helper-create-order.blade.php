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
    document.addEventListener("DOMContentLoaded", function(event) {
        // resetSteps();
        // initFormMap();


        getStatistics();










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
                currentTab = 0;
                $('#regForm')[0].reset();
                closeOrderDrawer();
                showToast();
                $('#overlay_order').css('display', 'none');
                $('#loader_order').css('display', 'none');
                // Refresh the page after a successful order submission
                window.location.reload();
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




</script>


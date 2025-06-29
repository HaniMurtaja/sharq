<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>

<script>
    let formMap;
    let marker;
    let lat = 24.7136;
    let lng = 46.6753;


    window.initAutocomplete = initMap2;

    function updateCheckboxValues() {
        $('input[type="checkbox"]').each(function() {
            $(this).val(this.checked ? 1 : 0);
        });
    }


    function initMap2() {
        console.log('999999');

        const latInput = document.getElementById('lat_order_hidden');
        const lngInput = document.getElementById('long_order_hidden');

        lat = parseFloat(latInput.value) || 24.7136;
        lng = parseFloat(lngInput.value) || 46.6753;

        const initialLocation = {
            lat: lat,
            lng: lng
        };

        formMap = new google.maps.Map(document.getElementById("formMap"), {
            center: initialLocation,
            zoom: 13,
            mapTypeId: "roadmap",
        });

        marker = new google.maps.Marker({
            position: initialLocation,
            map: formMap
        });

        // Extract Lat/Lng from Google Maps URL
        function extractLatLngFromLink(link) {
            const regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
            const match = link.match(regex);
            if (match) {
                const extractedLat = parseFloat(match[1]);
                const extractedLng = parseFloat(match[2]);
                return {
                    lat: extractedLat,
                    lng: extractedLng
                };
            }
            return null;
        }

        // Function to update the map with new lat/lng
        function updateMap(lat, lng) {
            const newPosition = {
                lat: lat,
                lng: lng
            };
            formMap.setCenter(newPosition);
            if (marker) marker.setMap(null);

            marker = new google.maps.Marker({
                position: newPosition,
                map: formMap
            });
            console.log(marker);


            // Update the input fields
            latInput.value = lat;
            lngInput.value = lng;
        }


        // Handle shortened Google Maps URLs
        function resolveShortUrl(shortUrl) {
            $.ajax({
                url: '{{ route('resolve-url') }}',
                method: 'POST',
                data: {
                    url: shortUrl,
                    _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
                },
                success: function(response) {
                    // Check if the response has lat and lng
                    if (response.lat && response.lng) {
                        // Log the resolved coordinates for debugging
                        console.log("Resolved coordinates:", response.lat, response.lng);
                        const lat = parseFloat(response.lat);
                        const lng = parseFloat(response.lng);
                        updateMap(lat, lng);

                        // Update the map with resolved lat/lng
                    } else {
                        alert('Unable to resolve the shortened URL.');
                    }
                },
                error: function() {
                    alert('Error occurred while resolving URL.');
                }
            });
        }

        // Search by link functionality
        const searchLinkInput = document.getElementById('search-link');
        searchLinkInput.addEventListener('change', function() {
            const mapLink = searchLinkInput.value;

            if (mapLink.includes('goo.gl')) {
                // If it's a shortened URL, resolve it first
                console.log('Shortened URL detected:', mapLink);
                resolveShortUrl(mapLink); // No need for a callback; handle it in the success function
            } else {
                // Handle full Google Maps links directly
                const extractedLatLng = extractLatLngFromLink(mapLink);
                if (extractedLatLng) {
                    console.log("Extracted coordinates from full URL:", extractedLatLng);
                    updateMap(extractedLatLng.lat, extractedLatLng
                        .lng); // Update the map with extracted lat/lng
                } else {
                    alert('Invalid Google Maps link format.');
                }
            }
        });


        // Click event on map to update lat/lng inputs and place a marker
        formMap.addListener('click', function(event) {
            lat = event.latLng.lat();
            lng = event.latLng.lng();

            if (marker) marker.setMap(null);

            marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: formMap
            });

            // Update the lat/lng input fields when clicking the map
            latInput.value = lat;
            lngInput.value = lng;
        });

        // Add event listeners to lat/lng inputs to update the map when values change
        function updateMapByLatLng() {
            const newLat = parseFloat(latInput.value);
            const newLng = parseFloat(lngInput.value);

            if (!isNaN(newLat) && !isNaN(newLng)) {
                updateMap(newLat, newLng);
            }
        }

        latInput.addEventListener('change', updateMapByLatLng);
        lngInput.addEventListener('change', updateMapByLatLng);
    }



    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.select2').select2({
            allowClear: true
        });
        initializeCountriesDataTable();


        //countries

        $('#countries').on('click', function(e) {
            e.preventDefault();
            initializeCountriesDataTable()
        });

        $('#save-country-btn').click(function() {
            $('#country_name_error').text('');

            var formData = $('#country-form').serialize(); // Serialize form data
            $.ajax({
                type: 'POST',
                url: '{{ route('save-country') }}',
                data: formData,
                success: function(response) {
                    console.log(response);
                    $('#country_name').val('');
                    $('#country_id').val('');
                    $('#country_title').html('New Country');
                    $('#save-country-btn').html('Save');
                    // alert('Country saved successfully');
                    closeDrawer();
                    initializeCountriesDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });


        $(document).on('click', '.edit-country', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('drawer_countries');
            drawer.classList.remove('translate-x-full');

            const countryId = $(this).data('id');

            const updateURL = '{{ url('admin/edit-country') }}/' + countryId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#country_name').val(response.country.name);
                    $('#country_id').val(response.country.id);



                    $('#country_title').html('Edit Country');
                    $('#save-country-btn').html('Save Changes');


                    $('.nav-pills a[href="#countries_menu"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading country details');
                }
            });
        });

        $(document).on('click', '.delete-country', function(e) {
            e.preventDefault();

            const countryId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-country') }}/${countryId}`;

            if (confirm('Are you sure you want to delete this country?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeCountriesDataTable();
                    },
                    error: function(xhr) {
                        alert('Error deleting country');
                    }
                });
            }
        });

        $('#new_country').click(function() {
            $('#country_name').val('');
            $('#country_id').val('');
            $('#country_title').html('New Country');
            $('#save-country-btn').html('Save');
        })

        function initializeCountriesDataTable() {
            console.log(45);

            if ($.fn.DataTable.isDataTable('#countries-table')) {
                $('#countries-table').DataTable().destroy();
            }
            $('#countries-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('country-list') }}",
                    "type": "GET",
                    "dataSrc": function(json) {
                        console.log('AJAX Response:', json);
                        return json.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },
                "columns": [{
                        "data": "name"
                    },

                    {
                        "data": "id"
                    },


                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-shift') }}/${data.id}`;
                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-country">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-country">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;

                        },
                        "orderable": false
                    }
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }






        //cities


        $('#cities').on('click', function(e) {
            e.preventDefault();
            initializeCitiesDataTable();
        });


        $('#save-city-btn').click(function() {
            updateCheckboxValues();
            $('#city_name_error').text('');
            $('#country_id_error').text('');


            var formData = $('#city-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-city') }}',
                data: formData,
                success: function(response) {

                    console.log(response);

                    $('#city_name').val('');
                    $('#city_id').val('');
                    $('#country_id option').each(function() {
                        if ($(this).text() === "Country") {
                            $(this).prop('selected', true).prop('disabled', true);
                        } else {
                            $(this).prop('selected',
                                false);
                        }
                    });
                    $('#country_id').trigger('change');

                    $('#city_title').html('New City');
                    $('#save-city-btn').html('Save');
                    alert('City saved successfully');
                    $('.select2').val(null).trigger('change');
                    lat = 24.7136;
                    lng = 46.6753;


                    updateMap(lat, lng);
                    closeDrawer();
                    initializeCitiesDataTable();
                },
                error: function(error) {

                    if (error.status === 422) {

                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });


        $(document).on('click', '.edit-city', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('drawer_city');
            drawer.classList.remove('translate-x-full');

            const cityId = $(this).data('id');

            const updateURL = '{{ url('admin/edit-city') }}/' + cityId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#city_name').val(response.city.name);
                    $('#city_id').val(response.city.id);
                    $('select[name="country_id"]').val(response.city.country_id)
                        .trigger('change');


                    if (response.city.auto_dispatch == 1) {
                        $('#auto_dispatch').prop('checked', true).val(1);
                    } else {
                        $('#auto_dispatch').prop('checked', false).val(0);
                    }

                    updateMap(parseFloat(response.city.lat), parseFloat(response.city.lng));

                    $('#city_title').html('Edit City');
                    $('#save-city-btn').html('Save Changes');


                    $('.nav-pills a[href="#cities_menu"]').tab('show');

                },
                error: function(xhr) {
                    console.log('Error loading country details');
                }
            });
        });

        $(document).on('click', '.delete-city', function(e) {
            e.preventDefault();

            const cityId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-city') }}/${cityId}`;

            if (confirm('Are you sure you want to delete this city?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeCitiesDataTable();
                    },
                    error: function(xhr) {
                        alert('Error deleting city');
                    }
                });
            }
        });


        $('#new_city').click(function() {
            $('#city_name').val('');
            $('#city_id').val('');
            $('#country_id option').each(function() {
                if ($(this).text() === "Country") {
                    $(this).prop('selected', true).prop('disabled', true);
                } else {
                    $(this).prop('selected',
                        false);
                }
            });
            $('#country_id').trigger('change');
            lat = 24.7136;
            lng = 46.6753;


            updateMap(lat, lng);

            $('#city_title').html('New City');
            $('#save-city-btn').html('Save');
        })

        function initializeCitiesDataTable() {
            if ($.fn.DataTable.isDataTable('#cities-table')) {
                $('#cities-table').DataTable().destroy();
            }
            $('#cities-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('city-list') }}",
                    "type": "GET",
                    "dataSrc": function(json) {
                        console.log('AJAX Response:', json);
                        return json.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "id"
                    },

                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-shift') }}/${data.id}`;
                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-city">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-city">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;

                        },
                        "orderable": false
                    }
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }










        // areas

        $('#areas').on('click', function(e) {
            e.preventDefault();
            initializeAreasDataTable();
        });

        $('#save-area-btn').click(function() {
            // Clear previous errors
            $('#area_name_error').text('');
            $('#city_id_error').text('');


            var formData = $('#area-form').serialize(); // Serialize form data
            $.ajax({
                type: 'POST',
                url: '{{ route('save-area') }}',
                data: formData,
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // Clear form fields
                    $('#area_name').val('');
                    $('#area_id').val('');
                    $('#city_id option').each(function() {
                        if ($(this).text() === "City") {
                            $(this).prop('selected', true).prop('disabled', true);
                        } else {
                            $(this).prop('selected',
                                false); // Deselect other options
                        }
                    });
                    $('#city_id').trigger('change');

                    $('#area_title').html('New Area');
                    $('#save-area-btn').html('Save');

                    $('.select2').val(null).trigger('change');
                    closeDrawer();
                    alert('Area saved successfully');

                    initializeAreasDataTable();
                },
                error: function(error) {
                    // Handle error response
                    if (error.status === 422) {
                        // Display validation errors
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });

        $('#new_area').click(function() {
            $('#area_name').val('');
            $('#area_id').val('');
            $('#city_id option').each(function() {
                if ($(this).text() === "City") {
                    $(this).prop('selected', true).prop('disabled', true);
                } else {
                    $(this).prop('selected',
                        false); // Deselect other options
                }
            });
            $('#city_id').trigger('change');

            $('#area_title').html('New Area');
            $('#save-area-btn').html('Save');
        })

        $(document).on('click', '.edit-area', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('drawer_area');
            drawer.classList.remove('translate-x-full');

            const areaId = $(this).data('id');

            const updateURL = '{{ url('admin/edit-area') }}/' + areaId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#area_name').val(response.area.name);
                    $('#area_ia').val(response.area.id);
                    $('select[name="city_id"]').val(response.area.city_id)
                        .trigger('change');



                    $('#area_title').html('Edit Area');
                    $('#save-area-btn').html('Save Changes');


                    $('.nav-pills a[href="#areas_menu"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading area details');
                }
            });
        });

        $(document).on('click', '.delete-area', function(e) {
            e.preventDefault();

            const areaId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-area') }}/${areaId}`;

            if (confirm('Are you sure you want to delete this area?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeAreasDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting area');
                    }
                });
            }
        });

        function initializeAreasDataTable() {
            if ($.fn.DataTable.isDataTable('#areas-table')) {
                $('#areas-table').DataTable().destroy();
            }
            $('#areas-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('area-list') }}",
                    "type": "GET",
                    "dataSrc": function(json) {
                        console.log('AJAX Response:', json);
                        return json.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },

                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-shift') }}/${data.id}`;
                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-area">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-area">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;

                        },
                        "orderable": false
                    }
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }







        function updateMap(lat, lng) {
            console.log('update');

            const newPosition = {
                lat: lat,
                lng: lng
            };
            formMap.setCenter(newPosition); // Center the map on the new position

            if (marker) {
                marker.setPosition(newPosition); // Update marker's position
            } else {
                // If the marker doesn't exist, create it
                marker = new google.maps.Marker({
                    position: newPosition,
                    map: formMap
                });
            }

            // Update any other elements that reflect latitude and longitude values
            document.getElementById('lat_order_hidden').value = lat;
            document.getElementById('long_order_hidden').value = lng;
        }


    });
</script>

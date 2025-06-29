<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function updateCheckboxValues() {
        $('input[type="checkbox"]').each(function() {
            $(this).val(this.checked ? 1 : 0);
        });
    }
    $(document).ready(function() {


        initializeClientsDataTable();
        $('.select2').select2({
            allowClear: true
        });


        $('#integration_id').select2({
            allowClear: true,
            placeholder: 'Integration',
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            }
        });




        $(document).on('change', '#is_integration', function(e) {
            e.preventDefault();
            if ($(this).is(':checked')) {
                $('#integration-div').show();
            } else {
                $('#integration-div').hide();
            }
        });


        function enableAreaSelect() {
            $('.city-select').each(function() {
                var areaSelect = $(this).closest('.flex').find('.area-select');
                if ($(this).val()) {
                    areaSelect.prop('disabled', false);
                } else {
                    areaSelect.prop('disabled', true);
                }
            });
        }


        $(document).on('change', '.city-select', function() {
            var areaSelect = $(this).closest('.flex').find('.area-select');
            if ($(this).val()) {
                areaSelect.prop('disabled', false);
            } else {
                areaSelect.prop('disabled', true);
            }
        });
        $(document).on('change', '.city-select', function() {
            var cityId = $(this).val();
            var areaSelect = $(this).closest('.flex').find('.area-select');

            if ($(this).val()) {
                $.ajax({
                    url: '{{ route('city-areas') }}',
                    type: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        console.log(response); // Log the response to check the data
                        areaSelect.prop('disabled', false);
                        areaSelect.empty();
                        areaSelect.append(
                            '<option value="" selected="selected" disabled>Area</option>'
                        );

                        $.each(response, function(key, area) {

                            areaSelect.append('<option value="' + area.id + '">' +
                                area.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            } else {
                areaSelect.prop('disabled', true);
                areaSelect.empty();
                areaSelect.append('<option value="" selected="selected" disabled>Area</option>');
            }
        });



        // clients ----------------------------------


        $('#clients').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('client-list') }}",
                type: 'GET',
                success: function(response) {
                    const tableList = document.getElementById('table-list');
                    tableList.innerHTML = `@include('admin.pages.clients.list')`;
                    console.log(tableList)
                    initializeClientsDataTable();

                },
                error: function(xhr, status, error) {
                    console.error('Error loading content:', error);
                }
            });
        });

        function initializeClientsDataTable() {

            if ($.fn.DataTable.isDataTable('#clients-table')) {
                $('#clients-table').DataTable().destroy();
            }
            $('#clients-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('client-list') }}",
                    "type": "GET",
                    "dataSrc": function(json) {
                        console.log('AJAX Response:', json);
                        return json.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },

                "columns": [
                    {
                        "data": "name"
                    },
                    {
                        "data": "id"
                    },
                    {
                        "data": "total_orders"
                    },
                    {
                        "data": "total_balance"
                    },
                    {
                        "data": "country"
                    },

                    {
                        "data": "city"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/edit-client') }}/${data.id}/edit`;
                            return `
                            <div class="flex gap-4 px-4 py-4">
                                <form method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')


                                    <button
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-client" data-id="${data.id}">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                </form>


                                    <a href="/client-details.html">
                                        <a href="${updateUrl}"
                                            class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                                            <img src="{{ asset('new/src/assets/icons/view.svg') }}" alt="" />
                                        </a>
                                        </div>
                                        `

                            ;
                        },
                        "orderable": false
                    }
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }

        $(document).on('click', '.delete-client', function(e) {
            e.preventDefault();

            const clientId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-client') }}/${clientId}`;

            if (confirm('Are you sure you want to delete this client?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeClientsDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting client');
                    }
                });
            }
        });

        $('#save-client-btn').click(function() {
            console.log(65);

            updateCheckboxValues();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-form')[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route('save-client') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-form')[0].reset();
                    $('#file-upload').val('');
                    $('#upload-label').css('background-image', '');
                    document.getElementById('user-icon').style.display = 'block';

                    $('.select2').val(null).trigger('change');

                    closeDrawer();
                    initializeClientsDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');
                            inputElement.after(
                                '<span class="text-danger invalid-feedback" role="alert">' +
                                value[0] + '</span>');
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });




        //branches ---------------------------

        $('#branches').on('click', function(e) {
            e.preventDefault();

            initializeBranchesDataTable();


        });

        $(document).on('click', '.edit-branch', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('drawer_branches')

            drawer.classList.remove('translate-x-full');
            const branchId = $(this).data('id');

            const updateURL = '{{ url('admin/edit-branch') }}/' + branchId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#branch_name').val(response.branch.name);
                    $('#branch_id').val(response.branch.id);
                    $('select[name="driver_id"]').val(response.branch.driver_id)
                        .trigger('change');


                    $('#branches-title').html('Edit Branch');
                    $('#save-branch-btn').html('Save Changes');


                    // $('.nav-pills a[href="#branches3"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });

        $('#new-branch').on('click', function() {
            $('#branch_name').val('');
            $('.select2').val(null).trigger('change');
            $('#branches-title').html('New Branch');
            $('#save-branch-btn').html('Save');
        })

        $(document).on('click', '.delete-branch', function(e) {
            e.preventDefault();

            const branchId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-branch') }}/${branchId}`;

            if (confirm('Are you sure you want to delete this branch?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeBranchesDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting zone');
                    }
                });
            }
        });

        $('#save-branch-btn').click(function() {

            $('#branch_name_error').text('');
            $('#driver_id_error').text('');


            var formData = $('#branch-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-branch') }}',
                data: formData,
                success: function(response) {

                    console.log(response);

                    $('#branch_name').val('');
                    $('.select2').val(null).trigger('change');
                    $('#branches-title').html('New Branch');
                    $('#save-branch-btn').html('Save');

                    // const toastElement = $('#toastClients');

                    // toastElement.find('.toast-header strong').text('');
                    // toastElement.find('.toast-body').text('Branch saved successfully');



                    // toastElement.toast('show');
                    closeDrawer();
                    initializeBranchesDataTable();
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

        function initializeBranchesDataTable() {
            if ($.fn.DataTable.isDataTable('#branches-table')) {
                $('#branches-table').DataTable().destroy();
            }
            $('#branches-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('branches-list') }}",
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

                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-branch">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Groups"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-branch">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;
                        },
                        "orderable": false
                    },
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }













        //groups --------------------------

        $('#groups').on('click', function(e) {

            initializeGroupsDataTable();


        });

        function initializeGroupsDataTable() {
            if ($.fn.DataTable.isDataTable('#groups-table')) {
                $('#groups-table').DataTable().destroy();
            }
            $('#groups-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('clients-group-list') }}",
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

                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-group">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Groups"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-group">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;
                        },
                        "orderable": false
                    },

                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }

        $('#btn-save-group').click(function() {
            // Clear previous errors
            $('#group_name_error').text('');
            $('#calculation_method_error').text('');
            $('#default_delivery_fee_error').text('');
            $('#collection_amount_error').text('');
            $('#service_type_error').text('');




            var formData = $('#group-form').serialize(); // Serialize form data
            $.ajax({
                type: 'POST',
                url: '{{ route('save-clients-group') }}',
                data: formData,
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // Clear form fields


                    $('#title-group').html('New Group');
                    $('#btn-save-group').html('Save');
                    $('#group_name').val('');
                    $('#calculation_method').val('');
                    $('#default_delivery_fee').val('');
                    $('#collection_amount').val('');
                    $('#client_group_id').val('')
                    $('#service_type').val('');
                    $('.select2').val(null).trigger('change');
                    initializeGroupsDataTable();
                    const calcMethod = document.getElementById('calcMethod');
                    calcMethod.innerHTML = '';

                    // Show success message
                    // const toastElement = $('#toastClients');

                    // toastElement.find('.toast-header strong').text('');
                    // toastElement.find('.toast-body').text('Group saved successfully');



                    // toastElement.toast('show');

                    closeDrawer();

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
        $('#new-group').on('click', function(e) {
            e.preventDefault();

            $('#title-group').html('New Group');
            $('#btn-save-group').html('Save');
            $('#group_name').val('');
            $('#calculation_method').val('');
            $('#default_delivery_fee').val('');
            $('#collection_amount').val('');
            $('#client_group_id').val('')
            $('#service_type').val('');
            $('.select2').val(null).trigger('change');

            const calcMethod = document.getElementById('calcMethod');
            calcMethod.innerHTML = '';

        })

        $(document).on('click', '.edit-group', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('groups_drawer')

            drawer.classList.remove('translate-x-full');
            const groupId = $(this).data('id');
            console.log(groupId)
            const updateURL = '{{ url('admin/edit-clients-group') }}/' + groupId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.group.name)
                    $('#group_name').val(response.group.name);
                    $('#client_group_id').val(response.group.id);

                    $('select[name="calculation_method"]').val(response.group
                            .calculation_method_label)
                        .trigger('change');

                    $('#default_delivery_fee').val(response.group.default_delivery_fee);
                    $('#collection_amount').val(response.group.collection_amount);

                    $('select[name="service_type"]').val(response.group.service_type)
                        .trigger('change');

                    setTimeout(function() {
                        const calcMethod = document.getElementById('calcMethod');
                        calcMethod.innerHTML = response.viewContent;
                    }, 1000);
                    $('#title-group').html('Edit Group');
                    $('#btn-save-group').html('Save Changes');


                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });


        $(document).on('click', '.delete-group', function(e) {
            e.preventDefault();

            const groupId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-clients-group') }}/${groupId}`;

            if (confirm('Are you sure you want to delete this group?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeGroupsDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting client');
                    }
                });
            }
        });




        //zones-----------------------------------

        $('#zones').on('click', function(e) {
            e.preventDefault();

            initializeZonesDataTable();


        });


        $(document).on('click', '.edit-zone', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('zone_drawer')

            drawer.classList.remove('translate-x-full');
            const zoneId = $(this).data('id');
            console.log(zoneId)
            const updateURL = '{{ url('admin/edit-zone') }}/' + zoneId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#zone_name').val(response.zone.name);
                    $('#zone_id').val(response.zone.id);



                    $('#title-zone').html('Edit Zone');
                    $('#save-zone-btn').html('Save Changes');
                    // $.each(response.locations, function(index, data) {
                    //     addRow('repeater', data);
                    // });

                    initializeRepeater('repeater', response.locations || []);


                    // $('.nav-pills a[href="#zone_menu"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });

        const openDrawerButtons = document.querySelectorAll('.open-drawer');

        openDrawerButtons.forEach((button) => {
            button.addEventListener('click', function() {
                initializeRepeater('repeater', []);
                enableAreaSelect();


            });
        });


        $(document).on('click', '.delete-zone', function(e) {
            e.preventDefault();

            const zoneId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-zone') }}/${zoneId}`;

            if (confirm('Are you sure you want to delete this zone?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeZonesDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting zone');
                    }
                });
            }
        });


        $('#new-zone').on('click', function() {
            $('#zone_name').val('');
            $('#zone_id').val('');
            initializeRepeater('repeater', []);
            $('#title-zone').html('New Zone');
            $('#save-zone-btn').html('Save');
        })


        $('#save-zone-btn').click(function() {
            // Clear previous errors
            $('input, select').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = $('#zone-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-zone') }}',
                data: formData,
                success: function(response) {
                    console.log(response);
                    // Reset only the zone name input field
                    $('#zone_name').val('');
                    $('#zone_id').val('');
                    initializeRepeater('repeater', []);
                    $('#title-zone').html('New Zone');
                    $('#save-zone-btn').html('Save');
                    // const toastElement = $('#toastClients');

                    // toastElement.find('.toast-header strong').text('');
                    // toastElement.find('.toast-body').text('Zone saved successfully');



                    // toastElement.toast('show');
                    closeDrawer();
                    initializeZonesDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');
                            inputElement.after(
                                '<span class="text-danger invalid-feedback">' +
                                value[0] + '</span>');
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });


        function initializeZonesDataTable() {
            if ($.fn.DataTable.isDataTable('#zones-table')) {
                $('#zones-table').DataTable().destroy();
            }
            $('#zones-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('zone-list') }}",
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

                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10  delete-zone">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Groups"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-zone">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                </div>`;
                        },
                    }
                ],
                "pageLength": 20,
                "lengthChange": false
            });
        }






        function initializeRepeater(repeaterId, data) {
            var repeater = document.getElementById(repeaterId);
            repeater.innerHTML = '';

            console.log('test');

            if (data && data.length > 0) {

                data.forEach(function(rowData) {
                    addRow(repeaterId, rowData);
                });
            } else {
                // Add one empty row to start with
                addRow(repeaterId);
            }

            // Ensure buttons are updated
            updateDeleteButtons(repeaterId);
        }

        function addRow(repeaterId, rowData = {}) {
            console.log(rowData);
            var repeater = document.getElementById(repeaterId);
            var newRow = document.createElement('div');

            newRow.classList.add('flex', 'items-center', 'justify-center', 'col-span-2', 'gap-2');

            var citiesOptions = '';
            @foreach ($all_cities as $city)
                citiesOptions +=
                    `<option value="{{ $city->id }}" ${rowData.city_id == '{{ $city->id }}' ? 'selected' : ''}>{{ $city->name }}</option>`;
            @endforeach

            var areasOptions = '';
            @foreach ($all_areas as $area)
                areasOptions +=
                    `<option value="{{ $area->id }}" ${rowData.area_id == '{{ $area->id }}' ? 'selected' : ''}>{{ $area->name }}</option>`;
            @endforeach

            newRow.innerHTML = `



                        <select
                            class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3 select2 city-select"
                            style="width: 100%;" name="city[]">
                            <option value="" selected="selected" disabled>City</option>
                            ${citiesOptions}

                        </select>

                        <select
                            class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3  select2 area-select"
                            style="width: 100%;" name="area[]" disabled>
                            <option value="" selected="selected" disabled>Area</option>
                                 ${areasOptions}
                        </select>

                        <button type="button"
                            class="flex items-center justify-center w-8 h-8 text-xl text-blue-400 border rounded-full outline-none min-w-8 min-h-8 focus:outline-none btn-add">
                            <i class="text-sm fas fa-plus"></i>
                        </button>
                        <button type="button"
                            class=" btn-delete flex items-center justify-center w-8 h-8 text-xl border rounded-full outline-none min-w-8 min-h-8 text-mainColor focus:outline-none">
                            <i class="text-sm far fa-trash-alt"></i>
                        </button>




            `;

            // Initialize Select2 for the new elements
            $('.select2').select2();

            // Enable or disable the area select based on the city selection
            enableAreaSelect();

            repeater.appendChild(newRow);

            // Add event listeners for the new buttons
            newRow.querySelector('.btn-add').addEventListener('click', function() {
                addRow(repeaterId);
                updateDeleteButtons(repeaterId);
            });
            newRow.querySelector('.btn-delete').addEventListener('click', function() {
                newRow.remove();
                updateDeleteButtons(repeaterId);
            });

            updateDeleteButtons(repeaterId);
            $('.select2').select2({
                allowClear: true
            });
        }


        function updateDeleteButtons(repeaterId) {
            var repeater = document.getElementById(repeaterId);
            var rows = repeater.querySelectorAll('.row');
            rows.forEach(function(row, index) {
                var deleteButton = row.querySelector('.btn-delete');
                if (rows.length === 1) {
                    deleteButton.disabled = true;
                } else {
                    deleteButton.disabled = false;
                }
            });
            $('.select2').select2({
                allowClear: true
            });
        }
        console.log(document.getElementById("calcMethod"));
        document.getElementById("calcMethod").addEventListener("DOMContentLoaded", (event) => {
            console.log('9832644444444444444444444')
        });




        // enableAreaSelect();

    });





    // const fileUpload = document.getElementById('file-upload');
    // const uploadLabel = document.getElementById('upload-label');
    // const userIcon = document.getElementById('user-icon');

    // fileUpload.addEventListener('change', (event) => {
    //     const file = event.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = (e) => {
    //             uploadLabel.style.backgroundImage = `url(${e.target.result})`;
    //             userIcon.style.display = 'none';
    //         };
    //         reader.readAsDataURL(file);
    //     }
    // });
</script>

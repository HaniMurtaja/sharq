<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        initializeIntegrationsDataTable();

        $('.select2').select2({
            allowClear: true
        });



        document.querySelectorAll('.switch-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                console.log(454);
                updateCheckboxValues();
            });
        });

        function updateCheckboxValues() {
            $('input[type="checkbox"]').each(function() {
                $(this).val(this.checked ? 1 : 0);
            });
        }

        $('#new-integration').click(function() {
            $('#integration-form')[0].reset();
            $('.select2').val(null).trigger('change');
            $('#title-vehicle').html('New Integration');
            $('#save-vehicle-btn').html('Save');

        })




        $('#save-integration-btn').click(function() {
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#integration-form')[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route('save-integration') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#integration-form')[0].reset();

                    $('#title-vehicle').html('New Integration');
                    $('#save-vehicle-btn').html('Save');

                    const toastElement = $('#toastIntegrations');

                    toastElement.find('.toast-header strong').text('');
                    toastElement.find('.toast-body').text('Integration saved successfully');



                    toastElement.toast('show')
                    closeDrawer();
                    initializeIntegrationsDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');

                            // Special handling for days_off field
                            if (key === 'days_off') {
                                var errorHtml =
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>';
                                inputElement.closest('.form-group').append(
                                    errorHtml);
                                inputElement.closest('.form-group').find(
                                    '.select2-selection').addClass('is-invalid');
                            } else {
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            }
                        });
                    } else {
                        console.error(error);
                    }
                }
            });

            $('.custom-file-input').on('change', function() {

                var fileName = $(this).val().split('\\').pop();

                $(this).next('.custom-file-label').html(fileName);
            });


        });








        $(document).on('click', '.edit-integration', function(e) {
            e.preventDefault();

            const drawer = document.getElementById('integrationDrawer');
            drawer.classList.remove('translate-x-full');


            const vehicleId = $(this).data('id');
            const updateURL = '{{ url('admin/update-integration') }}/' + vehicleId;

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {


                    $('#name').val(response.integration.name);


                    $('#integration_id').val(response.integration.id);

                    $('select[name="client_type"]').val(response.integration
                            .client_type)
                        .trigger(
                            'change');
                    $('select[name="otp_awb"]').val(response.integration
                            .otp_awb)
                        .trigger(
                            'change');


                    if (response.integration.has_cancel_reason == 1) {
                        $('#switch-has-cancel-reason').prop('checked', true);
                    } else {
                        $('#switch-has-cancel-reason').prop('checked', false);
                    }



                    $('#title-vehicle').html('Edit Integration');
                    $('#save-vehicle-btn').html('Save Changes');

                    // $('.nav-pills a[href="#activity"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading integration details');
                }
            });
        });


        $('#webhooks').click(function() {
            initializeWebhooksDataTable();
        })







        $(document).on('click', '.edit-webhook', function(e) {
            e.preventDefault();

            const drawer = document.getElementById('webhook_drawer');
            drawer.classList.remove('translate-x-full');


            const vehicleId = $(this).data('id');
            const updateURL = '{{ url('admin/update-webhook') }}/' + vehicleId;

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {


                    $('#name_webhook').val(response.integration.name);
                    $('#url').val(response.integration.url);

                    $('#webhook_id').val(response.integration.id);



                    $('select[name="integration_company_id"]').val(response.integration
                            .integration_company_id)
                        .trigger(
                            'change');


                    $('select[name="type"]').val(response.integration.type)
                        .trigger(
                            'change');


                    $('select[name="format"]').val(response.integration.format).trigger(
                        'change');












                    $('#webhook-title').html('Edit Integration');
                    $('#save-webhook-btn').html('Save Changes');

                    // $('.nav-pills a[href="#activity"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading webhook details');
                }
            });
        });


        $('#new-webhook').click(function() {
            $('#webhook-form')[0].reset();

            $('#webhook-title').html('New Integration');
            $('#save-webhook-btn').html('Save');
            $('.select2').val(null).trigger('change');
        })



        $('#save-webhook-btn').click(function() {
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#webhook-form')[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route('save-webhook') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#webhook-form')[0].reset();

                    $('#webhook-title').html('New Integration');
                    $('#save-webhook-btn').html('Save');
                    $('.select2').val(null).trigger('change');
                    const toastElement = $('#toastIntegrations');

                    toastElement.find('.toast-header strong').text('');
                    toastElement.find('.toast-body').text('Integration saved successfully');



                    toastElement.toast('show')
                    closeDrawer();
                    initializeWebhooksDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');

                            // Special handling for days_off field
                            if (key === 'days_off') {
                                var errorHtml =
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>';
                                inputElement.closest('.form-group').append(
                                    errorHtml);
                                inputElement.closest('.form-group').find(
                                    '.select2-selection').addClass('is-invalid');
                            } else {
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            }
                        });
                    } else {
                        console.error(error);
                    }
                }
            });

            $('.custom-file-input').on('change', function() {

                var fileName = $(this).val().split('\\').pop();

                $(this).next('.custom-file-label').html(fileName);
            });


        });





        function initializeIntegrationsDataTable() {
            console.log(4544);

            if ($.fn.DataTable.isDataTable('#integrations-table')) {
                $('#integrations-table').DataTable().destroy();
            }
            $('#integrations-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('integration-list') }}",
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

                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-integration">
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


        function initializeWebhooksDataTable() {
            console.log(4544);

            if ($.fn.DataTable.isDataTable('#webhook-table')) {
                $('#webhook-table').DataTable().destroy();
            }
            $('#webhook-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('webhook-list') }}",
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
                        "data": "company"
                    },


                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-shift') }}/${data.id}`;
                            return `<div class="flex gap-4 px-4 py-4">
                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-webhook">
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


    });
</script>

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
                    $('.select2').val(null).trigger('change');
                    const toastElement = $('#toastIntegrations');

                    toastElement.find('.toast-header strong').text('');
                    toastElement.find('.toast-body').text('Integration saved successfully');



                    toastElement.toast('show')

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



        $(document).on('click', '.delete-integration', function(e) {
            e.preventDefault();

            const vehicleId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-integration') }}/${vehicleId}`;

            if (confirm('Are you sure you want to delete this integration?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeIntegrationsDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting user');
                    }
                });
            }
        });

        $(document).on('click', '.edit-integration', function(e) {
            e.preventDefault();

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
                    $('#url').val(response.integration.url);
           
                    $('#integration_id').val(response.integration.id);


                   
                        $('select[name="type"]').val(response.integration.type)
                            .trigger(
                                'change');
                    
                 
                        $('select[name="format"]').val(response.integration.format).trigger(
                            'change');
                   

                 





                   



                    $('#title-vehicle').html('Edit Integration');
                    $('#save-vehicle-btn').html('Save Changes');

                    // $('.nav-pills a[href="#activity"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading integration details');
                }
            });
        });



        function initializeIntegrationsDataTable() {
            console.log(4544);
            
            if ($.fn.DataTable.isDataTable('#test')) {
                $('#test').DataTable().destroy();
            }
            $('#test').DataTable({
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
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },

                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-integration') }}/${data.id}`;
                            return `<a href="#activity" data-toggle="tab" data-id="${data.id}" class="edit-integration"><i class="fas fa-eye "></i></a>`;
                        },
                        "orderable": false
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <form method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 align-baseline delete-integration" data-id="${data.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>`;
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




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>


<script type="text/javascript">
    $(document).ready(function() {
        initializeVehiclesDataTable();

        $('.select2').select2({
            allowClear: true
        });

        const currentYear = new Date().getFullYear();
        const select = $('#yearSelect');
        for (let year = currentYear; year >= currentYear - 50; year--) {
            select.append(new Option(year, year));
        }




        $('#save-vehicle-btn').click(function() {
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#vehicle-form')[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route('save-vehicles') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#vehicle-form')[0].reset();
                    $('#id_card_privew_section').html(`<img id='profile_image' />`);

                    $('#upload-label').css('background-image', '');
                    document.getElementById('user-icon').style.display = 'block';
                    $('#title-vehicle').html('New Vehicle');
                    $('#save-vehicle-btn').html('Save');
                    $('.select2').val(null).trigger('change');
                    const toastElement = $('#toastVehicles');

                    toastElement.find('.toast-header strong').text('');
                    toastElement.find('.toast-body').text('Vehicle saved successfully');



                    toastElement.toast('show')

                    initializeVehiclesDataTable();
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



        $(document).on('click', '.delete-vehicle', function(e) {
            e.preventDefault();

            const vehicleId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-vehicle') }}/${vehicleId}`;

            if (confirm('Are you sure you want to delete this vehicle?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeVehiclesDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting user');
                    }
                });
            }
        });

        $(document).on('click', '.edit-vehicle', function(e) {
            e.preventDefault();

            const vehicleId = $(this).data('id');
            const updateURL = '{{ url('admin/update-vehicle') }}/' + vehicleId;

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {


                    $('#name').val(response.vehicle.name);
                    $('#plate_number').val(response.vehicle.plate_number);
                    $('#vin_number').val(response.vehicle.vin_number);
                    $('#make').val(response.vehicle.make);
                    $('#model').val(response.vehicle.model);
                    $('#vehicle_milage').val(response.vehicle.vehicle_milage);
                    $('#last_service_milage').val(response.vehicle.last_service_milage);
                    $('#due_service_milage').val(response.vehicle.due_service_milage);
                    $('#service_milage_limit').val(response.vehicle.service_milage_limit);
                    $('#vehicle_id').val(response.vehicle.id);


                    if (response.vehicle.operator_id) {
                        $('select[name="operator_id"]').val(response.vehicle.operator_id)
                            .trigger(
                                'change');
                    }
                    if (response.vehicle.type) {
                        $('select[name="type"]').val(response.vehicle.type).trigger(
                            'change');
                    }

                    if (response.vehicle.year) {
                        $('select[name="year"]').val(response.vehicle.year).trigger(
                            'change');
                    }

                    if (response.vehicle.color) {
                        $('select[name="color"]').val(response.vehicle.color).trigger(
                            'change');
                    }





                    if (response.vehicle_image_url) {
                        document.getElementById('user-icon').style.display = 'none';

                        $('#upload-label').css('background-image',
                            `url(${response.vehicle_image_url})`);
                    }



                    if (response.id_card_image_url) {
                        $('#profile_image').attr('src', response.id_card_image_url);
                    }



                    $('#title-vehicle').html('Edit Vehicle');
                    $('#save-vehicle-btn').html('Save Changes');

                    // $('.nav-pills a[href="#activity"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading vehicle details');
                }
            });
        });



        function initializeVehiclesDataTable() {
            if ($.fn.DataTable.isDataTable('#vehicles-table')) {
                $('#vehicles-table').DataTable().destroy();
            }
            $('#vehicles-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('vehicle-list') }}",
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-vehicle">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-vehicle">
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

<script>
    function readURLProfile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#profile_image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imgInpProfile").change(function() {
        readURLProfile(this);
    });

    const fileUpload = document.getElementById('file-upload');
    const uploadLabel = document.getElementById('upload-label');
    const userIcon = document.getElementById('user-icon');

    fileUpload.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                uploadLabel.style.backgroundImage = `url(${e.target.result})`;
                userIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
</script>

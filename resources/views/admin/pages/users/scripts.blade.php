<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>

<script>
    function updateCheckboxValues() {
        $('input[type="checkbox"]').each(function() {
            $(this).val(this.checked ? 1 : 0);
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        const openDrawerButtons = document.querySelectorAll('.open-drawer');

        openDrawerButtons.forEach((button) => {
            button.addEventListener('click', function() {
                console.log('open');
                const drawer = document.getElementById('drawer');

                drawer.classList.remove('translate-x-full');

                $('.select2').select2({
                    allowClear: true,
                });
                $('.groups').select2({
                    placeholder: "Groups",
                    allowClear: true,
                });


            });
        });






    });



    $(document).ready(function() {



        document.querySelector('#exportForm').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch("{{ url('admin/export-users-template') }}?", {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.download_url) {
                        window.location.href = data.download_url;
                    } else {
                        alert("Download URL not found.");
                    }
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert("Something went wrong while exporting.");
                });
        });


        initializeUsersDataTable();


        $('input[type="checkbox"]').change(function() {
            $(this).val(this.checked ? 1 : 0);
        });

        $('#users').on('click', function(e) {
            e.preventDefault();

            initializeUsersDataTable();


        });

        $('#new-user').click(function() {
            $('#user-form')[0].reset();
            $('#upload-label').css('background-image', '');
            document.getElementById('user-icon').style.display = 'block';
            $('#title-user').html('New User');
            $('#save-user-btn').html('Save');
        })

        $('#save-user-btn').click(function() {

            $('span.text-danger').text('');


            updateCheckboxValues();

            var formData = new FormData($('#user-form')[0]);


            $.ajax({
                type: 'POST',
                url: '{{ route('save-user') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    // Clear form inputs if necessary
                    $('#user-form')[0].reset();
                    $('.select2').val(null).trigger('change');
                    $('#upload-label').css('background-image', '');
                    document.getElementById('user-icon').style.display = 'block';
                    $('#title-user').html('New User');
                    $('#save-user-btn').html('Save');
                    const toastElement = $('#toastUserTemplate');

                    // toastElement.find('.toast-header strong').text('');
                    // toastElement.find('.toast-body').text('User saved successfully');



                    // toastElement.toast('show');
                    closeDrawer();

                    initializeUsersDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');

                            // Special handling for days_off field
                            if (key === 'groups') {
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

                            if (key === 'city_ids') {
                                $('.errorcity_ids').html(
                                    '<span class="text-danger " role="alert">' +
                                    value[0] + '</span>');
                            }
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });


        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            console.log(999999999999999999999999999999)
            const userId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-user') }}/${userId}`;

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeUsersDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting user');
                    }
                });
            }
        });

        $(document).on('click', '.edit-user', function(e) {
            e.preventDefault();

            const userId = $(this).data('id');
            const updateURL = '{{ url('admin/edit-user') }}/' + userId + '/edit';
            const drawer = document.getElementById('usersDrawer');
            drawer.classList.remove('translate-x-full');
            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.user_detail)
                    if (response.user.user) {
                        $('#locked').val(response.user_detail.locked);
                        $('#marketplace_access').val(response.user_detail
                            .marketplace_access);
                        $('#mac_address').val(response.user_detail.mac_address);
                        $('#sim_card').val(response.user_detail.sim_card);
                        $('#sn').val(response.user_detail.sn);
                        $('#request_per_second').val(response.user_detail
                            .request_per_second);


                        if (response.user_detail.locked == 1) {
                            $('#locked').prop('checked', true);
                        } else {
                            $('#locked').prop('checked', false);
                        }

                        if (response.user_detail.marketplace_access == 1) {
                            $('#marketplace_access').prop('checked', true);
                        } else {
                            $('#marketplace_access').prop('checked', false);
                        }
                    }

                    $('#first_name').val(response.user.first_name);
                    $('#last_name').val(response.user.last_name);
                    $('#user_id').val(response.user.id);
                    $('#email').val(response.user.email);

                    if (response.group_ids != []) {
                        $('select[name="groups[]"]').val(response.group_ids).trigger(
                            'change');
                    }
                    if (response.city_ids != []) {
                        $('select[name="city_ids[]"]').val(response.city_ids).trigger(
                            'change');
                    }

                    $('select[name="country_id"]').val(response.user.country_id).trigger(
                        'change');


                    $('select[name="role"]').val(response.last_role).trigger(
                        'change');

                    if (response.user_role) {
                        $('select[name="user_role"]').val(response.user_role).trigger(
                            'change');
                    }







                    document.getElementById('user-icon').style.display = 'none';

                    $('#upload-label').css('background-image',
                        `url(${response.profile_url})`);


                    $('#title-user').html('Edit User');
                    $('#save-user-btn').html('Save Changes');

                    $('.nav-pills a[href="#users_menu"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });



        function initializeUsersDataTable() {
            if ($.fn.DataTable.isDataTable('#users-table')) {
                $('#users-table').DataTable().destroy();
            }
            $('#users-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('user-list') }}",
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
                        "data": "first_name"
                    },
                    {
                        "data": "last_name"
                    },
                    {
                        "data": "email"
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-user">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-user">
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


        //template

        $('#save-template-btn').click(function() {

            $('span.text-danger').text('');


            updateCheckboxValues();

            var formData = new FormData($('#regForm')[0]);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '{{ route('save-template') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    // Clear form inputs if necessary
                    $('#regForm')[0].reset();

                    document.getElementById('user-icon').style.display = 'block';
                    $('#title-template').html('New User');
                    $('#save-template-btn').html('Save');
                    const toastElement = $('#toastUserTemplate');

                    toastElement.find('.toast-header strong').text('');
                    toastElement.find('.toast-body').text('Template saved successfully');




                    // toastElement.toast('show');
                    closeDrawer();
                    initializeUsersDataTable();
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');

                            // Special handling for days_off field
                            if (key === 'groups') {
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
        });


        $('#templates').on('click', function(e) {
            e.preventDefault();
            initializeTemplatesDataTable();



        });



        $(document).on('click', '.delete-template', function(e) {
            e.preventDefault();

            const templateId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-template') }}/${templateId}`;

            if (confirm('Are you sure you want to delete this template?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeTemplatesDataTable();
                    },
                    error: function(xhr) {
                        console.log('Error deleting user');
                    }
                });
            }
        });

        $(document).on('click', '.edit-template', function(e) {
            e.preventDefault();

            const drawer = document.getElementById('Templates');
            drawer.classList.remove('translate-x-full');
            const templateId = $(this).data('id');
            const updateURL = '{{ url('admin/edit-template') }}/' + templateId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#role_name').val(response.role.name);
                    $('#template_id').val(response.role.id);

                    // Track all permissions received from the response
                    const receivedPermissions = response.permissions || [];

                    // Handle permissions
                    $('input[type="checkbox"]').each(function() {
                        const permissionName = $(this).attr('name');

                        // Check or uncheck the checkbox based on received permissions
                        if (receivedPermissions.includes(permissionName)) {
                            $(this).prop('checked',
                                true); // Match found, check the box
                        } else {
                            $(this).prop('checked',
                                false); // No match, uncheck the box
                        }
                    });

                    $('#title-template').html('Edit Template');
                    $('#save-template-btn').html('Save Changes');

                    // Switch to the 'settings' tab
                    $('.nav-pills a[href="#settings"]').tab('show');
                },

                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });


        function initializeTemplatesDataTable() {
            if ($.fn.DataTable.isDataTable('#templates-table')) {
                $('#templates-table').DataTable().destroy();
            }
            $('#templates-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('template-list') }}",
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-template">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Users"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-template">
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

    $(function() {
        $('#toggle-one').bootstrapToggle();
    })
</script>

<script>
    function updateCheckboxValues() {
        $('input[type="checkbox"]').each(function() {
            $(this).val(this.checked ? 1 : 0);
        });
    }

    $(document).ready(function() {


        initializeUsersDataTable();


        $('input[type="checkbox"]').change(function() {
            $(this).val(this.checked ? 1 : 0);
        });

        $('#save-user-btn').click(function() {

            $('span.text-danger').text('');


            updateCheckboxValues();

            var formData = new FormData($('#user-form')[0]);
            console.log(formData);
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
                    $('#upload-label').css('background-image', '');
                    document.getElementById('user-icon').style.display = 'block';
                    $('#title-user').html('New User');
                    $('#save-user-btn').html('Save');
                    alert('User saved successfully');
                    initializeUsersDataTable();
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

                    $('#firstName').val(response.user.first_name);
                    $('#lastName').val(response.user.last_name);
                    $('#user_id').val(response.user.id);
                    $('#email').val(response.user.email);

                    if (response.group_ids != []) {
                        $('select[name="groups[]"]').val(response.group_ids).trigger(
                            'change');
                    }




                    document.getElementById('user-icon').style.display = 'none';

                    $('#upload-label').css('background-image',
                        `url(${response.profile_url})`);


                    $('#title-user').html('Edit User');
                    $('#save-user-btn').html('Save Changes');

                    $('.nav-pills a[href="#activity"]').tab('show');
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
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },

                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const updateUrl = `{{ url('admin/update-operator') }}/${data.id}`;
                            return `<a href="#activity" data-toggle="tab" data-id="${data.id}" class="edit-user"><i class="fas fa-eye "></i></a>`;
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
                                    <button type="button" class="btn btn-link text-danger p-0 m-0 align-baseline delete-user" data-id="${data.id}">
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

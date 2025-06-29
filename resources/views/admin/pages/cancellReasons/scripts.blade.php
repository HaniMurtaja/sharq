<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.select2').select2({
            allowClear: true
        });
        initializeReasonsDataTable();


        //countries

        $('#countries').on('click', function(e) {
            e.preventDefault();
            initializeReasonsDataTable()
        });

        $('#save-reason-btn').click(function() {
            $('#name_error').text('');

            var formData = $('#reason-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-reason') }}',
                data: formData,
                success: function(response) {
                    console.log(response);
                    $('#name').val('');
                    $('#reason_id').val('');
                    $('#reason_title').html('New Reason');
                    $('#save-reason-btn').html('Save');

                    closeDrawer();
                    initializeReasonsDataTable();
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


        $(document).on('click', '.edit-reason', function(e) {
            e.preventDefault();
            const drawer = document.getElementById('drawer_reasons');
            drawer.classList.remove('translate-x-full');

            const reasonId = $(this).data('id');

            const updateURL = '{{ url('admin/edit-reason') }}/' + reasonId + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.reason.name);
                    
                    $('#name').val(response.reason.name);
                    $('#reason_id').val(response.reason.id);



                    $('#reason_title').html('Edit Reason');
                    $('#save-reason-btn').html('Save Changes');


                    $('.nav-pills a[href="#reasons_menu"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading reason details');
                }
            });
        });

        $(document).on('click', '.delete-reason', function(e) {
            e.preventDefault();

            const reasonId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-reason') }}/${reasonId}`;

            if (confirm('Are you sure you want to delete this reason?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        initializeReasonsDataTable();
                    },
                    error: function(xhr) {
                        alert('Error deleting reason');
                    }
                });
            }
        });

        $('#new_reason').click(function() {
            $('#name').val('');
            $('#reason_id').val('');
            $('#reason_title').html('New Reason');
            $('#save-reason-btn').html('Save');
        })

        function initializeReasonsDataTable() {
            console.log(45);

            if ($.fn.DataTable.isDataTable('#reasons-table')) {
                $('#reasons-table').DataTable().destroy();
            }
            $('#reasons-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('reason-list') }}",
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-reason">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Reasons"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-reason">
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

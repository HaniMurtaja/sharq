<script>
    $(document).ready(function() {




        $('#client_active_input').on('change', function() {
            let isChecked = $(this).is(':checked');
            let clientId = $(this).data('client_id');

            $.ajax({
                url: "{{ route('clientupdated.changeClientStatus') }}",
                method: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    client_id: clientId,
                    is_active: isChecked ? 1 : 0
                },
                success: function(response) {
                    
                    console.log('Status updated successfully');
                },
                error: function(xhr) {
                    console.error('Error updating status', xhr);
                    alert('Failed to update status.');
                }
            });
        });



        renderClientOrdersDataTable({{ $client['id'] }})
        renderClientBranchesDataTable({{ $client['id'] }})
        renderClientUsersDataTable({{ $client['id'] }})


        function renderClientOrdersDataTable(id) {
            var ordersTable = $('#orders-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('clientupdated.getOrders') }}",
                    "type": "GET",
                    'data': {
                        'id': id
                    }
                },

                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "branch"
                    },
                    {
                        "data": "customer_name"
                    },
                    {
                        "data": "customer_area"
                    },

                    {
                        "data": "status"
                    },

                ],
                "pageLength": 5,
                "lengthChange": false,
                "searching": true,
            });
            $('.dt-input').attr('placeholder', 'Search here... ');

        }

        function renderClientBranchesDataTable(id) {
            console.log('hi');

            if ($.fn.DataTable.isDataTable('#branches-table')) {
                $('#branches-table').DataTable().destroy();
            }

            var branchesTable = $('#branches-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('clientupdated.getClientBranches') }}",
                    "type": "GET",
                    "data": {
                        'id': id
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": null
                    },
                    {
                        "data": null
                    },
                    {
                        "data": null
                    }
                ],
                "createdRow": function(row, data, dataIndex) {
                    console.log('hh');


                    let cardHtml = `

                    <div class="clientBranchCards">
                            <div class="clientBranchCard p-3">
                                <div class="clientBranchCardInfo">
                                    <div>
                                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2.75C3.31421 2.75 2.75 3.31421 2.75 4V6C2.75 6.68579 3.31421 7.25 4 7.25H7C7.68579 7.25 8.25 6.68579 8.25 6V5V4C8.25 3.31421 7.68579 2.75 7 2.75H4ZM9.75 4V4.25H12.5H15.25V4.20001C15.25 3.12581 16.1258 2.25 17.2 2.25H20.8C21.8742 2.25 22.75 3.12581 22.75 4.20001V5.79999C22.75 6.87419 21.8742 7.75 20.8 7.75H17.2C16.1258 7.75 15.25 6.87419 15.25 5.79999V5.75H13.25V11.75H15.25V11.7C15.25 10.6258 16.1258 9.75 17.2 9.75H20.8C21.8742 9.75 22.75 10.6258 22.75 11.7V13.3C22.75 14.3742 21.8742 15.25 20.8 15.25H17.2C16.1258 15.25 15.25 14.3742 15.25 13.3V13.25H13.25V18C13.25 18.6858 13.8142 19.25 14.5 19.25H15.25V19.2C15.25 18.1258 16.1258 17.25 17.2 17.25H20.8C21.8742 17.25 22.75 18.1258 22.75 19.2V20.8C22.75 21.8742 21.8742 22.75 20.8 22.75H17.2C16.1258 22.75 15.25 21.8742 15.25 20.8V20.75H14.5C12.9858 20.75 11.75 19.5142 11.75 18V12.5V5.75H9.75V6C9.75 7.51421 8.51421 8.75 7 8.75H4C2.48579 8.75 1.25 7.51421 1.25 6V4C1.25 2.48579 2.48579 1.25 4 1.25H7C8.51421 1.25 9.75 2.48579 9.75 4ZM16.75 20V20.8C16.75 21.0458 16.9542 21.25 17.2 21.25H20.8C21.0458 21.25 21.25 21.0458 21.25 20.8V19.2C21.25 18.9542 21.0458 18.75 20.8 18.75H17.2C16.9542 18.75 16.75 18.9542 16.75 19.2V20ZM16.75 13.3V12.5V11.7C16.75 11.4542 16.9542 11.25 17.2 11.25H20.8C21.0458 11.25 21.25 11.4542 21.25 11.7V13.3C21.25 13.5458 21.0458 13.75 20.8 13.75H17.2C16.9542 13.75 16.75 13.5458 16.75 13.3ZM16.75 5.79999V5V4.20001C16.75 3.95421 16.9542 3.75 17.2 3.75H20.8C21.0458 3.75 21.25 3.95421 21.25 4.20001V5.79999C21.25 6.04579 21.0458 6.25 20.8 6.25H17.2C16.9542 6.25 16.75 6.04579 16.75 5.79999Z" fill="#585858"></path></svg>
                                    </div>
                                    <p class="branchName">${data.name} [${data.id}]</p>
                                    <p>${data.city}</p>
                                </div>
                                <div class="clientBranchCardActions">

                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="status_toggle_${data.id}">Status</label>
                                            <input class="form-check-input status-toggle  position-relative" data-id="${data.id}" type="checkbox" ${data.is_active ? 'checked' : ''} role="switch" id="status_toggle_${data.id}" name="c">
                                        </div>


                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="auto_dispatch_${data.id}"> Auto Dispatch</label>
                                            <input class="form-check-input position-relative auto-dispatch-toggle"
                                                ${data.auto_dispatch ? 'checked' : ''} id="auto_dispatch_${data.id}"
                                                data-id="${data.id}" type="checkbox" role="switch" name="c">
                                        </div>
                                        <button class="edit-client-branch"  data-bs-toggle="modal" data-id="${data.id}"
                                                            data-bs-target="#editBranch"> <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>
                                        </button>


                                </div>
                            </div>
                    </div>



                    `;
                    $(row).html(cardHtml);
                },
                "paging": true,
                "pageLength": 10,
                "lengthChange": false,
                "ordering": false,
                "searching": true,
                "info": false
            });
            $('.dt-input').attr('placeholder', 'Search here... ');
        }

        function renderClientUsersDataTable(id) {

            if ($.fn.DataTable.isDataTable('#users-table')) {
                $('#users-table').DataTable().destroy();
            }
            var usersTable = $('#users-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '{{ route('clientupdated.getUsers') }}',
                    "type": "GET",
                    'data': function(d) {
                        d.id = id;
                    },
                    "dataSrc": function(response) {
                        console.log('AJAX Response:', response);



                        $('#client-exist-user-form #user_id').empty();
                        $('#client-exist-user-form #user_id').append(
                            '<option value="" selected="selected" disabled>User</option>'
                        );

                        if (response.all_users) {
                            response.all_users.forEach(function(user) {
                                $('#client-exist-user-form #user_id').append(
                                    '<option value="' +
                                    user.id + '">' + user.name + '</option>'
                                );
                            });

                            $('#client-exist-user-form #user_id').select2({
                                width: '100%'
                            });
                        }

                        // Return only the data part to DataTables
                        return response.data;
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
                        "data": null
                    },
                    {
                        "data": null
                    }
                ],
                "createdRow": function(row, data, dataIndex) {
                    let cardHtml = `

                  <div class="clientBranchCards">
                        <div class="clientBranchCard p-3">
                            <div class="clientBranchCardInfo">
                                <div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.1197 12.7796C12.0497 12.7696 11.9597 12.7696 11.8797 12.7796C10.1197 12.7196 8.71973 11.2796 8.71973 9.50955C8.71973 7.69955 10.1797 6.22955 11.9997 6.22955C13.8097 6.22955 15.2797 7.69955 15.2797 9.50955C15.2697 11.2796 13.8797 12.7196 12.1197 12.7796Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18.7398 19.3799C16.9598 21.0099 14.5998 21.9999 11.9998 21.9999C9.39976 21.9999 7.03977 21.0099 5.25977 19.3799C5.35977 18.4399 5.95977 17.5199 7.02977 16.7999C9.76977 14.9799 14.2498 14.9799 16.9698 16.7999C18.0398 17.5199 18.6398 18.4399 18.7398 19.3799Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 22.0005C17.5228 22.0005 22 17.5233 22 12.0005C22 6.47764 17.5228 2.00049 12 2.00049C6.47715 2.00049 2 6.47764 2 12.0005C2 17.5233 6.47715 22.0005 12 22.0005Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </div>
                                <p class="branchName">${data.name}</p>
                                <p>${data.email}</p>
                            </div>
                            <div class="clientBranchCardActions">




                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="status_toggle_${data.id}">Status</label>
                                            <input class="form-check-input position-relative status-toggle "
                                                ${data.status ? 'checked' : ''} id="status_toggle_${data.id}"
                                                data-id="${data.id}" type="checkbox" role="switch" name="c">
                                        </div>
                                <button class="edit-client-user" data-role = '${data.last_role}'  data-id = '${data.id}'  data-bs-toggle="modal"
                                                            data-bs-target="#editUserModal">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>
                                </button>

                            </div>
                        </div>
                    </div>



                    `;
                    $(row).html(cardHtml);
                },
                "paging": true,
                "pageLength": 3,
                "lengthChange": false,
                "ordering": false,
                "searching": true,
                "info": false
            });
            $('.dt-input').attr('placeholder', 'Search here... ');
        }


        $('#branches-table').on('change', '.status-toggle', function() {
            var branchId = $(this).data('id');
            var newStatus = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route('clientupdated.changeClientBranchStatus') }}',
                method: 'GET',
                data: {
                    id: branchId,
                    status: newStatus
                },
                success: function(response) {
                    console.log('Status updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update status.');
                }
            });
        });


        $('#branches-table').on('change', '.auto-dispatch-toggle', function() {
            var branchId = $(this).data('id');
            var autoDispatch = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route('clientupdated.changeClientBranchAutoDispatch') }}',
                method: 'GET',
                data: {
                    id: branchId,
                    auto_dispatch: autoDispatch
                },
                success: function(response) {
                    console.log('Auto dispatch updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update auto dispatch.');
                }
            });
        });





        // branches modal



        $(document).on('click', '#save-client-branch-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-branch-form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route('clientupdated.saveClientBranch') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    alert('branch saved successfully');
                    $('#addNewBranchModal .btnClose').trigger('click');

                    lat = 24.7136;
                    lng = 46.6753;
                    renderClientBranchesDataTable({{ $client['id'] }});


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


        $(document).on('change', '#branch-city', function() {
            console.log(999);

            var cityId = $(this).val();
            var areaSelect = $('#branch-area');

            if (cityId) {
                $.ajax({
                    url: '{{ route('city-areas') }}',
                    type: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        console.log(response);
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




        // users modal

        $(document).on('click', '#save-client-user-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-user-form')[0]);
            formData.append('id', {{ $client['id'] }});
            $.ajax({
                type: 'POST',
                url: '{{ route('clientupdated.saveClientUser') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    alert('User saved successfully');
                    $('#addNewUserModal .btnClose').trigger('click');

                    renderClientUsersDataTable({{ $client['id'] }});

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


        $(document).on('change', '#users-table .status-toggle', function() {

            var newStatus = $(this).is(':checked');
            var status = 0;
            var id = $(this).data('id');
            if (newStatus) {
                status = 1;
            }

            $.ajax({
                url: '{{ route('clientupdated.changeClientUserStatus') }}',
                method: 'GET',
                data: {
                    id: id,
                    status: status
                },
                success: function(response) {
                    console.log('Status updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update status.');
                }
            });
        });


        $(document).on('click', '#save-client-exist-user-btn', function(e) {
            e.preventDefault();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-exist-user-form')[0]);
            formData.append('id', {{ $client['id'] }});
            $.ajax({
                type: 'POST',
                url: '{{ route('clientupdated.saveClientExistUser') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#user_id').find('option:first').prop('selected', true).prop(
                        'disabled',
                        true);

                    $('#user_id').trigger('change');


                    alert('User saved successfully');
                    $('#addNewUserModal .btnClose').trigger('click');

                    renderClientUsersDataTable({{ $client['id'] }});


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

            $('#usersModel').on('shown.bs.modal', function() {
                $('#nav-home-tab').tab('show');
            });
        });



        $(document).on('click', '#edit-client-user-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#edit-user-form')[0]);
            formData.append('id', {{ $client['id'] }});
            $.ajax({
                type: 'POST',
                url: '{{ route('clientupdated.updateClientExistUser') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    $('#croppedImage').empty();
                    alert('User saved successfully');
                    $('#editUserModal .btnClose').trigger('click');
                    renderClientUsersDataTable({{ $client['id'] }});


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


        $(document).on('click', '#client-user-delete-btn', function(e) {
            e.preventDefault();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#edit-user-form')[0]);
            formData.append('id', {{ $client['id'] }});
            $.ajax({
                type: 'POST',
                url: '{{ route('clientupdated.deleteClientExistUser') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    $('#croppedImage').empty();
                    alert('User Deleted successfully');
                    $('#editUserModal .btnClose').trigger('click');
                    renderClientUsersDataTable({{ $client['id'] }});



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












    });
</script>

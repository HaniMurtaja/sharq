 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>


 <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
 <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
 <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>







 <script>
     $(document).ready(function() {



         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });
         initializeOperatorsDataTable();

         $(document).ready(function() {
             $('#car_type').on('change', function() {
                 var selectedValue = $(this).val();
                 if (selectedValue === 'company') {
                     $('#company-vehicles').show();
                     $('#driver-vehicle').hide();
                 }
                 if (selectedValue === 'driver') {

                     $('#driver-vehicle').show();
                     $('#company-vehicles').hide();
                 }

                 if (selectedValue === '') {

                     $('#driver-vehicle').hide();
                     $('#company-vehicles').hide();
                 }
             });
         });







         $('#save-shift-btn').click(function() {
             // Clear previous errors
             $('#shift_name_error').text('');
             $('#shift_from_error').text('');
             $('#shift_from_type_error').text('');
             $('#shift_to_error').text('');
             $('#shift_to_type_error').text('');

             var formData = $('#shift-form').serialize(); // Serialize form data
             $.ajax({
                 type: 'POST',
                 url: '{{ route('save-shift', ['id' => isset($shift) ? $shift->id : null]) }}',
                 data: formData,
                 success: function(response) {
                     // Handle success response
                     console.log(response);
                     // Clear form fields
                     $('#shift_name').val('');
                     $('#shift_id').val('');
                     $('#shift_from').val('');
                     $('#shift_from_type').val('PM');
                     $('#shift_to').val('');
                     $('#shift_to_type').val('PM');
                     // Show success message
                     initializeShiftsDataTable();
                     //  const toastElement = $('#toastOperators');

                     //  toastElement.find('.toast-header strong').text('');
                     //  toastElement.find('.toast-body').text('Shift saved successfully');




                     //  toastElement.toast('show');
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





         $('#groups').on('click', function(e) {
             e.preventDefault();

             $('[name="group_name"]').val('');
             $('[name="group_id"]').val('');
             $('[name="min_feed_order"]').val('');
             $('select[name="type_feed_order"]').val('')
                 .trigger('change');

             $('select[name="additional_type_feed"]').val('').trigger('change');


             // Clear existing repeater rows
             $('#repeater').empty();





             initializeRepeater('repeater', []);
             initializeRepeater('repeater2', []);

             // Update UI elements to indicate editing mode
             $('#title-group').text('New Group');
             $('#save-group-btn').html('Save');
             initializeGroupsDataTable()


         });


         $('#new-group').on('click', function(e) {
             e.preventDefault();

             $('[name="group_name"]').val('');
             $('[name="group_id"]').val('');
             $('[name="min_feed_order"]').val('');
             $('select[name="type_feed_order"]').val('')
                 .trigger('change');

             $('select[name="additional_type_feed"]').val('').trigger('change');


             // Clear existing repeater rows
             $('#repeater').empty();





             initializeRepeater('repeater', []);
             initializeRepeater('repeater2', []);
         })



         function initializeShiftsDataTable() {
             console.log(99);

             if ($.fn.DataTable.isDataTable('#shifts-table')) {
                 $('#shifts-table').DataTable().destroy();
             }
             $('#shifts-table').DataTable({
                 "processing": true,
                 "serverSide": true,
                 "ajax": {
                     "url": "{{ route('shifts') }}",
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
                         "data": "from"
                     },
                     {
                         "data": "to"
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-shift">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Individuals"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-shift">
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




         $(document).on('click', '.edit-shift', function(e) {
             e.preventDefault();

             const drawer = document.getElementById('shift-drawer')

             drawer.classList.remove('translate-x-full');

             const shiftId = $(this).data('id');
             const updateURL = '{{ url('admin/update-shifts') }}/' + shiftId;

             $.ajax({
                 url: updateURL,
                 type: 'get',
                 data: {
                     _token: '{{ csrf_token() }}'
                 },
                 success: function(response) {
                     console.log(response);
                     $('#shift_name').val(response.name);
                     $('#shift_id').val(response.id);
                     $('#shift_from').val(response.from).trigger('change');
                     $('#shift_from_type').val(response.shift_from_type).trigger('change');
                     $('#shift_to').val(response.to).trigger('change');
                     $('#shift_to_type').val(response.shift_to_type).trigger('change');
                     // Show the "Shifts" tab
                     $('#title').text('Edit Shift');
                     $('#save-shift-btn').html('Save Changes');
                     $('.nav-pills a[href="#timeline"]').tab('show');
                 },
                 error: function(xhr) {
                     console.log('Error loading shift details');
                 }
             });
         });

         $('#shifts-new').on('click', function(e) {
             e.preventDefault();

             $('#shift_name').val('');
             $('#shift_id').val('');
             $('#shift_from').val('').trigger('change');
             $('#shift_from_type').val('PM').trigger('change');
             $('#shift_to').val('').trigger('change');
             $('#shift_to_type').val('PM').trigger('change');
             // Show the "Shifts" tab
             $('#title').text('New Shift');
             $('#save-shift-btn').html('Save');
         })


         $('#shifts').on('click', function(e) {
             e.preventDefault();

             $('#shift_name').val('');
             $('#shift_id').val('');
             $('#shift_from').val('').trigger('change');
             $('#shift_from_type').val('PM').trigger('change');
             $('#shift_to').val('').trigger('change');
             $('#shift_to_type').val('PM').trigger('change');
             // Show the "Shifts" tab
             $('#title').text('New Shift');
             $('#save-shift-btn').html('Save');

             initializeShiftsDataTable();


         });


         $(document).on('click', '.delete-shift', function(e) {
             e.preventDefault();

             const shiftId = $(this).data('id');
             const deleteUrl = `{{ url('admin/delete-shift') }}/${shiftId}`;

             if (confirm('Are you sure you want to delete this shift?')) {
                 $.ajax({
                     url: deleteUrl,
                     type: 'POST',
                     data: {
                         _method: 'DELETE',
                         _token: '{{ csrf_token() }}'
                     },
                     success: function(response) {

                         initializeShiftsDataTable();
                     },
                     error: function(xhr) {
                         console.log('Error deleting shift');
                     }
                 });
             }
         });



     });




     $(document).on('click', '.delete-group', function(e) {
         e.preventDefault();

         const groupId = $(this).data('id');
         const deleteUrl = `{{ url('admin/delete-group') }}/${groupId}`;

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
                     console.log('Error deleting shift');
                 }
             });
         }
     });

     $(document).on('click', '.edit-group', function(e) {
         e.preventDefault();

         const groupId = $(this).data('id');
         const updateURL = '{{ url('admin/update-group') }}/' + groupId;

         const drawer = document.getElementById('group-drawer')

         drawer.classList.remove('translate-x-full');

         $.ajax({
             url: updateURL,
             type: 'GET',
             data: {
                 _token: '{{ csrf_token() }}'
             },
             success: function(response) {
                 console.log(response)
                 // Populate the group form fields with response data
                 $('[name="group_name"]').val(response.group.name);
                 $('[name="group_id"]').val(response.group.id);
                 $('[name="min_feed_order"]').val(response.group.min_feed_order);
                 $('select[name="type_feed_order"]').val(response.group.type_feed_order)
                     .trigger('change');

                 $('select[name="additional_type_feed"]').val(response.group
                     .additional_feed_order).trigger('change');


                 // Clear existing repeater rows
                 $('#repeater').empty();
                 $('#title-group').text('Edit Group');
                 $('#save-group-btn').html('Save Changes');

                 // Populate repeater with response data
                 $.each(response.main_conditions, function(index, condition) {
                     addRow('repeater', condition.data);
                 });

                 $.each(response.additional_condditions, function(index, condition) {
                     addRow('repeater2', condition.data);
                 });



                 initializeRepeater('repeater', response.main_conditions || []);
                 initializeRepeater('repeater2', response.additional_condditions || []);

                 // Update UI elements to indicate editing mode
                 $('#title-group').text('Edit Group');
                 $('#save-group-btn').html('Save Changes');
                 $('.nav-pills a[href="#settings"]').tab('show');
             },
             error: function(xhr) {
                 console.log('Error loading group details');
             }
         });
     });



     function initializeRepeater(repeaterId, data) {
         console.log(123);
         console.log(repeaterId);


         var repeater = document.getElementById(repeaterId);
         repeater.innerHTML = '';


         if (data && data.length > 0) {
             // Populate repeater with existing data

             data.forEach(function(rowData) {
                 addRow(repeaterId, rowData.data, rowData.id);
             });
         } else {
             // Add one empty row to start with
             addRow(repeaterId);
         }

         // Ensure buttons are updated
         updateDeleteButtons(repeaterId);
     }

     function addRow(repeaterId, rowData = {}, id = '') {
         var repeater = document.getElementById(repeaterId);
         var newRow = document.createElement('div');
         newRow.classList.add('grid', 'w-full', 'grid-cols-[1fr_1fr_1fr_1fr_40px]', 'gap-2', 'mb-2');

         newRow.innerHTML = `



        <select
            class="w-full p-2 px-2 h-12 !mt-2 bg-white border custom-margin !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city"
            name="${repeaterId === 'repeater' ? 'type[]' : 'additional_type[]'}">
            <option value="Between" ${rowData.type === 'Between' ? 'selected' : ''}>Between</option>
            <option value=">" ${rowData.type === '>' ? 'selected' : ''}>></option>
            <option value="<" ${rowData.type === '<' ? 'selected' : ''}><</option>
        </select>


        <input type="text" placeholder="From" id="" name="${repeaterId === 'repeater' ? 'from[]' : 'additional_from[]'}"
            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
            value="${rowData.from || ''}" />
        <input type="text" placeholder="To" id="" name="${repeaterId === 'repeater' ? 'to[]' : 'additional_to[]'}"
            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
            value="${rowData.to || ''}" />
        <input type="text" placeholder="Percentage" id="" name="${repeaterId === 'repeater' ? 'percentage[]' : 'additional_percentage[]'}"
            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
            value="${rowData.percentage || ''}" />
        <div class="flex items-center justify-center">
            <button type="button" class="text-xl add-row" style="color: green;">+</button>
            <button type="button" class="text-xl text-red-600 delete-row hidden">×</button>
        </div>
    `;

         repeater.appendChild(newRow);

         // Add event listeners for new buttons
         newRow.querySelector('.add-row').addEventListener('click', function() {
             addRow(repeaterId);
             updateDeleteButtons(repeaterId);
         });

         newRow.querySelector('.delete-row').addEventListener('click', function() {
             newRow.remove();
             updateDeleteButtons(repeaterId);
         });

         updateDeleteButtons(repeaterId);
     }

     function updateDeleteButtons(repeaterId) {
         var repeater = document.getElementById(repeaterId);
         var rows = repeater.querySelectorAll('.grid');
         rows.forEach(function(row, index) {
             var deleteButton = row.querySelector('.delete-row');
             if (rows.length === 1) {
                 deleteButton.classList.add('hidden');
             } else {
                 deleteButton.classList.remove('hidden');
             }
         });
     }





     function initializeGroupsDataTable() {

         if ($.fn.DataTable.isDataTable('#group-table')) {
             $('#group-table').DataTable().destroy();
         }
         $('#group-table').DataTable({
             "processing": true,
             "serverSide": true,
             "ajax": {
                 "url": "{{ route('groups') }}",
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
                     "data": "min_free"
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
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-group">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Individuals"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-group">
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

     $('#save-group-btn').click(function() {
         // Clear previous errors
         $('input').removeClass('is-invalid');
         $('.invalid-feedback').remove();

         var formData = $('#group-form').serialize(); // Serialize form data
         $.ajax({
             type: 'POST',
             url: '{{ route('save-group') }}', // Adjust your route as needed
             data: formData,
             success: function(response) {
                 // Handle success response
                 console.log(response);
                 // Clear form fields
                 $('#group-form')[0].reset();
                 $('#title-group').text('New Group');
                 $('#save-group-btn').html('Save');
                 initializeRepeater('repeater', []);
                 initializeRepeater('repeater2', []);


                 //  const toastElement = $('#toastOperators');

                 //  toastElement.find('.toast-header strong').text('');
                 //  toastElement.find('.toast-body').text('Group saved successfully');




                 //  toastElement.toast('show');
                 initializeGroupsDataTable();
                 closeDrawer();
             },
             error: function(error) {
                 // Handle error response
                 if (error.status === 422) {
                     // Display validation errors
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


     $('#operators').on('click', function(e) {
         e.preventDefault();
         console.log(454);

         $('#operator-form')[0].reset();
         $('#file-upload').val('');

         $('.select2').val(null).trigger('change');

         $('#back_image_lice_privew_section').html(`
                     <img id='back_image_lice_privew' class = "image_preview_section" />
                      <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

         $('#front_image_lice_privew_section').html(`
                      <img id='front_image_lice_privew' class="image_preview_section" />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);


         $('#id_card_preview_section').html(`
                      <img id='id_card_privew' />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

         $('#upload-label').css('background-image', '');
         document.getElementById('user-icon').style.display = 'block';

         $('#driver-vehicle').hide();
         $('#company-vehicles').hide();


         $('#save-operator-btn').html('Save');

         initializeOperatorsDataTable();
     });

     $('#operators-new').on('click', function(e) {
         e.preventDefault();
         $('#verificaation_div').attr('hidden', true);

         $('#operator-form')[0].reset();
         $('#file-upload').val('');

         $('.select2').val(null).trigger('change');

         $('#back_image_lice_privew_section').html(`
                     <img id='back_image_lice_privew' class = "image_preview_section" />
                      <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

         $('#front_image_lice_privew_section').html(`
                      <img id='front_image_lice_privew' class="image_preview_section" />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);


         $('#id_card_preview_section').html(`
                      <img id='id_card_privew' />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

         $('#upload-label').css('background-image', '');
         document.getElementById('user-icon').style.display = 'block';

         $('#driver-vehicle').hide();
         $('#company-vehicles').hide();


         $('#save-operator-btn').html('Save');
     })


     $(document).on('click', '.delete-operator', function(e) {
         e.preventDefault();
         console.log(999999999999999999999999999999)
         const operatorId = $(this).data('id');
         const deleteUrl = `{{ url('admin/delete-operator') }}/${operatorId}`;

         if (confirm('Are you sure you want to delete this operator?')) {
             $.ajax({
                 url: deleteUrl,
                 type: 'POST',
                 data: {
                     _method: 'DELETE',
                     _token: '{{ csrf_token() }}'
                 },
                 success: function(response) {

                     initializeOperatorsDataTable();
                 },
                 error: function(xhr) {
                     console.log('Error deleting operator');
                 }
             });
         }
     });

     $(document).on('change', '#verification_checkbox', function() {
         console.log(8888888);

         const isChecked = $(this).is(':checked');
         const operatorId = $('#operator_verification_id').val();
         console.log(operatorId);

         const is_verified = isChecked ? 2 : 0;

         $.ajax({
             url: "{{ route('changeOperatorVerificationStatus') }}",
             method: 'POST',
             data: {
                 id: operatorId,
                 is_verified: is_verified,
                 _token: $('meta[name="csrf-token"]').attr('content')
             },
             success: function(response) {


                 $('#verification_status').html(response.verification_status);
                 if (response.verification_status_value == 2) {
                     $('#verification_checkbox').prop('checked', true);
                 } else {
                     $('#verification_checkbox').prop('checked', false);
                 }
                 console.log('Verification status updated:', response);
             },
             error: function(xhr) {
                 console.error('Failed to update verification status', xhr.responseText);
             }
         });
     });



     $(document).on('click', '.edit-operator', function(e) {
         e.preventDefault();
         console.log('clicked');
         const drawer = document.getElementById('drawer');



         drawer.classList.remove('translate-x-full');
         $('.city').select2({
             placeholder: "Select a city",
             allowClear: true
         });
         $('.select2').select2({
             allowClear: true
         });

         $('.days').select2({
             placeholder: "Days off",
             allowClear: true
         });
         const groupId = $(this).data('id');
         const updateURL = '{{ url('admin/update-operator') }}/' + groupId;

         $.ajax({
             url: updateURL,
             type: 'GET',
             data: {
                 _token: '{{ csrf_token() }}'
             },
             success: function(response) {
                 $('#verificaation_div').removeAttr('hidden');


                


                 const defaultImage = '/new/src/assets/images/No_Image_Available.jpeg';



               
                




                 $('#firstName').val(response.operator?.first_name || '');
                 $('#lastName').val(response.operator?.last_name || '');
                 $('#email').val(response.operator?.email || '');
                 $('#operator_id').val(response.operator?.id || '');
                 $('#phone').val(response.operator?.phone || '');
                 $('#verification_status').html(response.verification_status);

                 $('#birth_date').val(response.operator?.operator?.birth_date || '');
                 $('#order_value').val(response.operator?.operator?.order_value || '');
                
                 $('#emergency_contact_name').val(response.operator?.operator
                     ?.emergency_contact_name || '');
                 $('#emergency_contact_phone').val(response.operator?.operator
                     ?.emergency_contact_phone || '');
                 $('#iban').val(response.operator?.operator?.iban || '');
                 $("#latitude").val(response.operator?.operator?.lat || '');
                 $("#longitude").val(response.operator?.operator?.lng || '');
                 $('#car_type').val(response.vehicle_type)
                     .trigger('change');
                 // Set the profile image preview
                 //  $('#profile_image_icon').html(
                 //      `<label for="file-upload" class="upload-label" id="upload-label">

                //         </label>`
                 //  )
                 document.getElementById('user-icon').style.display = 'none';

                 $('#upload-label').css('background-image',
                     `url(${response.profile_url})`);



                 if (response.url_card_image) {
                     handleFilePreview(response.url_card_image, 'id_card_preview',
                         'pdf_download_link');
                 }


                 if (response.city_ids && Array.isArray(response.city_ids)) {
                     $('select[name="city[]"]').val(response.city_ids).trigger('change');
                 }


                 if (response.operator?.operator?.group_id) {
                     console.log("Setting group_id:", response.operator.operator.group_id);
                     $('#operator_group_id').val(response.operator.operator.group_id).trigger(
                         'change');

                     // Reinitialize Select2 if used
                     if ($('#operator_group_id').hasClass('select2-hidden-accessible')) {
                         $('#operator_group_id').select2();
                     }
                 }

                 if (response.operator?.operator?.jop_type) {
                     $('#jop_type').val(response.operator.operator.jop_type).trigger('change');

                     // Reinitialize Select2 if used
                     if ($('#jop_type').hasClass('select2-hidden-accessible')) {
                         $('#jop_type').select2();
                     }
                 }

                 if (response.operator?.operator?.branch_group_id) {
                     console.log("Setting branch_group_id:", response.operator.operator
                         .branch_group_id);
                     $('select[name="branch_group_id"]').val(response.operator.operator
                         .branch_group_id).trigger('change');

                     // Reinitialize Select2 if used
                     if ($('select[name="branch_group_id"]').hasClass('select2-hidden-accessible')) {
                         $('select[name="branch_group_id"]').select2();
                     }
                 }

                 if (response.operator?.operator?.shift_id) {
                     console.log("Setting shift_id:", response.operator.operator.shift_id);
                     $('#operator-shift-id').val(response.operator.operator.shift_id).trigger(
                         'change');

                     // Reinitialize Select2 if used
                     if ($('#operator-shift-id').hasClass('select2-hidden-accessible')) {
                         $('#operator-shift-id').select2();
                     }
                 }

                 if (Array.isArray(response.daysOff) && response.daysOff.length > 0) {
                     $('select[name="days_off[]"]').val(response.daysOff).trigger('change');
                 }

                 if (response.vehicle_type === 'company') {
                     $('#company-vehicles').show();
                     $('#company_vehicle_id').val(response.vehicle?.id || '').trigger('change');
                 }

                 if (response.vehicle_type === 'driver') {
                     $('#driver-vehicles').show();

                     $('#name').val(response.vehicle?.name || '');
                     $('#plate_number').val(response.vehicle?.plate_number || '');
                     $('#vin_number').val(response.vehicle?.vin_number || '');
                     $('#make').val(response.vehicle?.make || '');
                     $('#model').val(response.vehicle?.model || '');
                     $('#vehicle_milage').val(response.vehicle?.vehicle_milage || '');
                     $('#last_service_milage').val(response.vehicle?.last_service_milage || '');
                     $('#due_service_milage').val(response.vehicle?.due_service_milage || '');
                     $('#service_milage_limit').val(response.vehicle?.service_milage_limit || '');
                     $('#vehicle_id').val(response.vehicle?.id || '');

                     if (response.vehicle?.type) {
                         $('#type').val(response.vehicle.type).trigger('change');
                     }

                     if (response.vehicle?.year) {
                         $('select[name="year"]').val(response.vehicle.year).trigger('change');
                     }

                     if (response.vehicle?.color) {
                         $('select[name="color"]').val(response.vehicle.color).trigger('change');
                     }

                     if (response.vehicle_image_url) {
                         document.getElementById('user-icon-vehicle').style.display = 'none';
                         $('#upload-label-vehicle').css('background-image',
                             `url(${response.vehicle_image_url})`);
                     }

                     if (response.id_card_image_url) {
                         $('#profile_image').attr('src', response.id_card_image_url);
                     }
                 }



                 $('#title-operator').html('Edit Individual');
                 $('#save-operator-btn').html('Save Changes');

                 $('.nav-pills a[href="#activity"]').tab('show');
             },
             error: function(xhr) {
                 console.log('Error loading group details');
             }
         });
     });

     function handleFilePreview(fileUrl, imageElementId, downloadLinkId) {
         const fileExtension = fileUrl.split('.').pop().toLowerCase();

         if (fileExtension === 'pdf') {
             // Hide the image preview and show the PDF download link
             $('#' + imageElementId).hide();
             $('#' + downloadLinkId).attr('href', fileUrl).show();
         } else {
             // It's an image, show the image preview and hide the download link
             $('#' + imageElementId).attr('src', fileUrl).show();
             $('#' + downloadLinkId).hide();
         }
     }

     $('#save-operator-btn').click(function() {
         // closeDrawer();
         $('input').removeClass('is-invalid');
         $('.invalid-feedback').remove();

         var formData = new FormData($('#operator-form')[0]);

         formData.forEach((value, key) => {
             console.log(key, value); // This will display all key-value pairs in FormData
         });





         $.ajax({
             type: 'POST',
             url: '{{ route('save-operator') }}',
             data: formData,
             processData: false,
             contentType: false,
             success: function(response) {
                 $('#operator-form')[0].reset();
                 $('#file-upload').val('');
                 // $('#profile_image_icon').html(
                 //      `<label for="file-upload" class="upload-label" id="upload-label">
                //              <svg viewBox="0 0 24 24" id="user-icon">
                //                 <path
                //                     d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                //             </svg>
                //         </label>`
                 //  )
                 $('.select2').val(null).trigger('change');

                 $('#back_image_lice_privew_section').html(`
                     <img id='back_image_lice_privew' class = "image_preview_section" />
                      <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

                 $('#front_image_lice_privew_section').html(`
                      <img id='front_image_lice_privew' class="image_preview_section" />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);


                 $('#id_card_preview_section').html(`
                      <img id='id_card_privew' />
                       <a id="pdf_license_back_download" href="#" target="_blank" style="display: none;"></a>`);

                 $('#upload-label').css('background-image', '');
                 document.getElementById('user-icon').style.display = 'block';

                 $('#driver-vehicle').hide();
                 $('#company-vehicles').hide();

                 $('#title-operator').html('New Individual');
                 $('#save-operator-btn').html('Save');
                 initializeOperatorsDataTable();

                 const toastElement = $('#toastOperators');

                 //  toastElement.find('.toast-header strong').text('');
                 //  toastElement.find('.toast-body').text('Operator saved successfully');




                 //  toastElement.toast('show');

                 closeDrawer();
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


     $('#operators-table').on('change', '.change-status-toggle', function() {
         var branchId = $(this).data('id');
         var newAutoDispatch = $(this).is(':checked');
         var auto_dispatch = 0;
         if (newAutoDispatch) {
             auto_dispatch = 1;
         }
         $.ajax({
             url: '{{ route('change-operator-status') }}',
             method: 'POST',
             data: {
                 id: branchId,
                 status: auto_dispatch
             },
             success: function(response) {
                 console.log('Auto dispatch updated successfully.');
             },
             error: function(error) {
                 console.log('Failed to update auto dispatch.');
             }
         });
     });


     function initializeOperatorsDataTable() {
         if ($.fn.DataTable.isDataTable('#operators-table')) {
             $('#operators-table').DataTable().destroy();
         }
         $('#operators-table').DataTable({
             "processing": true,
             "serverSide": true,
             "ajax": {
                 "url": "{{ route('operators-list') }}",
                 "type": "GET",
                 "dataSrc": function(json) {
                     console.log('AJAX Response:', json);
                     return json.data;
                 },
                 "error": function(xhr, status, error) {
                     console.error('AJAX Error:', status, error);
                 }
             },
             //  $columns = ['id', 'name', 'phone', 'total_orders', 'group_name'];
             "columns": [

                 {
                     "data": 'name',

                 },
                 {
                     "data": 'id',


                 },
                 {
                     "data": 'phone',


                 },

                 {
                     "data": 'national_no',


                 },

                 {
                     "data": 'total_orders',


                 },
                 {
                     "data": 'group_name',


                 },

                 {
                     "data": 'verification_status',
                 },


                 {
                     "data": null,
                     "render": function(data, type, row) {
                         const updateUrl = `{{ url('admin/update-operator') }}/${data.id}`;
                         let input_id = 'status_toggel' + data.id;
                         return `


                         <div class="flex justify-content-center gap-4 px-4 py-4">

                             <div class="switch-container">
                                                <input type="checkbox" id="${input_id}"
                                                     data-id="${data.id}" ${data.status == 1 ? 'checked' : ''}

                                                   class="switch-checkbox status-toggle change-status-toggle" />
                                                <label for="${input_id}" class="switch-label">
                                                    <span class="switch-button"></span>
                                                </label>
                                            </div>


                                <form method="POST"  style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-operator">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                    </form>
                                    <button
                                        type="button"
                                        data-id="${data.id}"
                                        data-drawer="Individuals"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-operator">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>

                                     <a href="/client-details.html" class="d-none"></a>
                                        <a  href="#"
                                            data-id="${data.id}" data-bs-toggle="modal"
                                            data-bs-target="#historyModal"

                                            class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                                            <img src="{{ asset('new/src/assets/icons/verify_shield.svg') }}" alt="" width="19px" height="19px" />
                                        </a>


                                </div>`;
                     },
                     "orderable": false
                 },

             ],
             "pageLength": 20,
             "lengthChange": false
         });
     }

     $('#historyModal').on('hidden.bs.modal', function() {
         initializeOperatorsDataTable();
     });


     $(document).on('click', '.order-driver-btn', function(e) {
         e.preventDefault();

         let dataId = $(this).data('id');
         console.log('Open modal button clicked, data-id:', dataId);
         const URL = '{{ url('admin/get-verification-data') }}/' + dataId;
         $.ajax({
             url: URL,
             type: 'GET',
             data: {
                 id: dataId,
             },
             success: function(response) {
                 // console.log(response.operator.operator_id, response.operator);

                 $('#verification_checkbox').prop('checked', response.verification_status_value ==
                     2);
                 $('#verification_status').html(response.verification_status ?? '');
                 $('#social_id_no').html(response.operator?.operator?.social_id_no ?? '');
                 $('#operator_verification_id').val(response.operator.id)
                 const defaultImage =
                     "{{ asset('new/src/assets/images/No_Image_Available.jpeg') }}";

                 $('#id_card_image_front').attr('src', response.id_card_image_front || defaultImage);
                 $('#id_card_image_back').attr('src', response.id_card_image_back || defaultImage);
                 $('#license_image_front').attr('src', response.url_license_front_image ||
                     defaultImage);
                 $('#license_image_back').attr('src', response.url_license_back_image ||
                     defaultImage);
             },

             error: function(xhr, status, error) {
                 console.error('Error:', xhr.responseText);
             }
         });

     });












     const fileUpload = document.getElementById('file-upload2');
     const uploadLabel = document.getElementById('upload-label2');
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

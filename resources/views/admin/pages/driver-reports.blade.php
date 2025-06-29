@extends('admin.layouts.app')

@section('content')
    

    <div class="flex flex-col p-6">
        <!-- Filter -->
        <div class="h-16 px-4 py-3 mb-4 transition-all duration-300 bg-white border rounded-lg border-gray1" id="filter-body">
            <button type="button" class="flex items-center justify-between w-full" id="filter-btn">
                <span>Report Detail</span>

                <span class="flex items-center justify-center bg-white border rounded-lg w-9 h-9 border-gray1"
                    id="filter-icon">
                    <img src="{{ asset('new/src/assets/icons/arrow-right-table.svg') }}" alt="" />
                </span>
            </button>

            <!-- Form -->
            <form id="get-orders-form" class="hidden grid-cols-2 mt-6 lg:gap-x-20 gap-x-2 gap-y-6" id="filter-body-inner">

                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">

                            <select class="form-control select2" multiple="multiple" name="drivers[]" id="drivers" style="width: 100%;">
                                <option value="" selected="selected" disabled>Driver</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{$driver->id}}">{{$driver->full_name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="date" class="form-control float-right"
                                    id="reservation" placeholder="from:-to:-" value="">
                            </div>
                        </div>
                    </div>


                </div>

               
            







                <!-- Button -->
                <div class="flex items-center justify-end col-span-2 md:justify-end">
                    <button id="get-order-list" type="button" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.58 11.9999C15.58 13.9799 13.98 15.5799 12 15.5799C10.02 15.5799 8.42004 13.9799 8.42004 11.9999C8.42004 10.0199 10.02 8.41992 12 8.41992C13.98 8.41992 15.58 10.0199 15.58 11.9999Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M12 20.2707C15.53 20.2707 18.82 18.1907 21.11 14.5907C22.01 13.1807 22.01 10.8107 21.11 9.4007C18.82 5.8007 15.53 3.7207 12 3.7207C8.46997 3.7207 5.17997 5.8007 2.88997 9.4007C1.98997 10.8107 1.98997 13.1807 2.88997 14.5907C5.17997 18.1907 8.46997 20.2707 12 20.2707Z"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <span>View Report list</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="p-4 bg-white border rounded-lg border-gray1">

            <!-- Table -->
         
                                
            <div class="w-full overflow-x-auto">
                <table  id="order-list" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                ID
                            </th>
                            <th class="px-4 py-3 font-medium">Driver</th>
                            <th class="px-4 py-3 font-medium">Order number</th>
                            <th class="px-4 py-3 font-medium">Reason</th>
                            <th class="px-4 py-3 font-medium">Details</th>
                         
                            <th class="px-4 py-3 font-medium">Created at</th>
                   


                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>


    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#drivers').select2({
                allowClear: true,
                placeholder: 'Driver',
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
           


            initializeOrdersDataTable();
            

            

            
            $('#get-order-list').click(function() {
                initializeOrdersDataTable();


                var formData = $('#get-orders-form').serialize();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('save-history') }}',
                    data: formData,
                    success: function(response) {


                    },
                });
              


            });

            

           


            


           

            function initializeOrdersDataTable() {
                if ($.fn.DataTable.isDataTable('#order-list')) {
                    $('#order-list').DataTable().destroy();
                }
                

                
                $('#order-list').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('get-reports-list') }}",
                        "type": "GET",
                        "data": function(d) {
                            $.each($('#get-orders-form').serializeArray(), function(_, field) {
                                d[field.name] = field.value;
                            });

                            d['drivers'] = $('#drivers').val(); 
                        },
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
                            "data": "driver"
                        },
                        {
                            "data": "order_number"
                        },
                        {
                            "data": "reason"
                        },
                        {
                            "data": "details"
                        },
                       
                        {
                            "data": "created_at"
                        }
                    ],
                    "pageLength": 20,
                    "lengthChange": false,
                    
                  
                });
            }



           


        });

        $(function() {
            $('#reservation').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                startDate: moment().startOf('day'),
                endDate: moment().endOf('day'),
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                }
            });

            $('#reservation').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
            });

            $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endsection

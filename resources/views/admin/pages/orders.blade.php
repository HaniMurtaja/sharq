@extends('admin.layouts.app')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />






<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">

@section('content')


    <div class="flex flex-col h-screen p-6">
        <!-- Filter -->
        <div class="h-16 px-4 py-3 mb-4 transition-all duration-300 bg-white border rounded-lg border-gray1"
            id="filter-body">
            <button type="button" class="flex items-center justify-between w-full" id="filter-btn">
                <span>Filter</span>

                <span class="flex items-center justify-center bg-white border rounded-lg w-9 h-9 border-gray1"
                    id="filter-icon">
                    <img src="{{ asset('new/src/assets/icons/arrow-right-table.svg') }}" alt="" />
                </span>
            </button>

            <!-- Form -->
            <form class="hidden mt-6 md:grid-cols-2 md:gap-x-20 lg:gap-x-20 gap-y-6" id="get-orders-form">
                @csrf

                <!-- Type Select -->
                <div>
                    <label for="Type">Type</label>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        style="width: 100%;" id="type" name="type">
                        <option value="" selected="selected" disabled>Type</option>
                        <option>Delivery</option>
                    </select>
                </div>
                <!-- Status Select -->
                <label class="flex flex-col gap-3">
                    <span>Status</span>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md status focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        style="width: 100%;" multiple="multiple" id="status" name="status[]">

                        <option value="-1">All</option>
                        @foreach (App\Enum\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                        @endforeach
                    </select>
                </label>

                <!-- Status Select -->
                @if (auth()->user()->user_role?->value != 5)
                    <label class="flex flex-col gap-3">
                        <span>Client</span>
                        <select
                            class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                            style="width: 100%;" id="client_id" name="client_id">
                            <option value="" selected="selected" disabled>CLient</option>
                            @if (auth()->user()->user_role?->value == 2)
                                <option>{{ auth()->user()->full_name }}</option>
                            @else
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                @endif
                <!-- Driver Select -->
                <label class="flex flex-col gap-3">
                    <span>Driver</span>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        style="width: 100%;" id ="driver_id" name="driver_id">
                        <option value="" selected="selected" disabled>Drivre</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                        @endforeach
                    </select>
                </label>

                <div>
                    <span>From:- To:- </span>
                    <input type="text"
                        class=" shadow-none w-full border mt-2 rounded-md focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        name="date" value="" />
                </div>

                <div>
                    <span>Search Order ID or Client ID </span>
                    <input type="text" name="search2" id="search"
                        class=" shadow-none w-full border rounded-md mt-2 focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3" />
                </div>

                <!-- Button -->
                <div class="flex items-center justify-end col-span-2">
                    <button type="button" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1" id="get-order-list">
                        <img src="{{ asset('new/src/assets/icons/filter.svg') }}" alt="" />
                        <span>Apply Filter</span>
                    </button>
                </div>
            </form>
        </div>
        <!-- Table -->
        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-4 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">Orders</h3>
                </div>

            </div>

            <!-- Table -->
            <div class="w-full overflow-x-auto">z
                <table id="order-list" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium">ID</th>
                            <th class="w-32 px-4 py-3 font-medium">Customer name</th>
                            <th class="w-32 px-4 py-3 font-medium">Customer phone</th>
                            <th class="px-4 py-3 font-medium">Driver</th>
                            <th class="w-[15%] px-4 py-3 font-medium">Shop</th>
                            <th class="w-[15%] px-4 py-3 font-medium">Branch</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Order value</th>
                            <th class="px-4 py-3 font-medium">Fees</th>
                            <th class="px-4 py-3 font-medium text-center">Total order</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: center; width:100%">

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->

    </div>
@endsection


<script>
    $(document).ready(function() {
        $('.status').select2({
            placeholder: "Status",
            allowClear: true
        });

        $('#type').select2({
            allowClear: true,
            placeholder: 'Type',
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


        $('#client_id').select2({
            allowClear: true,
            placeholder: 'Client',
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

        $('#driver_id').select2({
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
            let type = $('#type').val();
            let status = $('#status').val(); // This will return an array
            let client_id = $('#client_id').val();
            let driver_id = $('#driver_id').val();
            let date = $('#reservation').val();
            let search = $('#search').val();

            // Set the values to the hidden fields in the export form
            $('#typeExportInput').val(type);
            $('#reportSelectExport').val(client_id);
            $('#driverExportInput').val(driver_id);
            $('#dateExportInput').val(date);
            $('#searchExportInput').val(search);


            $('#export-div').find('input[name="status-export-form[]"]').remove();


            if (status && status.length > 0) {
                $.each(status, function(index, value) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'status-export-form[]',
                        value: value
                    }).appendTo('#export-div form');
                });
            }
            initializeOrdersDataTable();





        });








        function initializeOrdersDataTable() {
            if ($.fn.DataTable.isDataTable('#order-list')) {
                $('#order-list').DataTable().destroy();
            }

            $('#order-list').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('orders-data-table') }}",
                    "type": "GET",
                    "data": function(d) {
                        $.each($('#get-orders-form').serializeArray(), function(_, field) {
                            d[field.name] = field.value;
                        });




                        d['status'] = $('#status').val();
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
                        "data": "customer_name"
                    },
                    {
                        "data": "customer_phone"
                    },
                    {
                        "data": "driver"
                    },
                    {
                        "data": "shop"
                    },
                    {
                        "data": "branch"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "order_value"
                    },
                    {
                        "data": "fees"
                    },
                    {
                        "data": "total"
                    },
                    {
                        "data": "created_at"
                    }
                ],


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

        $('#reservation').on('change.daterangepicker', function() {
            console.log('Change event triggered for #reservation');
        });
    });


    // $(function() {
    //     $('input[name="date"]').daterangepicker({
    //         opens: 'left',
    //         autoUpdateInput: false
    //     }, function(start, end) {
    //         // Update the input value manually
    //         $('input[name="date"]').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    //     });
    // });


    $(function() {
    $('input[name="date"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false // Prevent automatic update to preserve your custom logic
    }, function(start, end) {
        // Manually set the value in the format you want
        $('input[name="date"]').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});
</script>

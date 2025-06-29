@extends('admin.layouts.app')
<style>
    .form-control,
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .dt-buttons {
        float: right !important;


        border: none;

        padding: 6px 12px;

        margin-left: 5px;
        margin-bottom: 10px;

    }

    .dt-buttons .dt-button {
        background-color: #F46624 !important;

        color: white !important;
    }
</style>


<style>
    #reports_scroll {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    #reports_scroll::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
    }
</style>


@section('content')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Include Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">

    @include('admin.includes.content-header', ['header' => 'Driver Reports', 'title' => 'Reports'])
    <section id="reports_scroll" class="content table-responsive p-0" style="height:700px;">
        <div class="container">

            <!-- Filters -->
            <div class="mb-4">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="status">Status:</label>
                            <select id="status" class="form-control">
                                <option value="">All</option>
                                <option value="1">Online</option>
                                <option value="2">Busy</option>
                                <option value="3">Away</option>
                                <option value="4">Offline</option>
                                <!-- Add other statuses as necessary -->
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="operator_name">Operator Name:</label>
                            <input type="text" id="operator_name" class="form-control" placeholder="Enter Operator Name">
                        </div>

                        <div class="col-md-4">
                            <label for="date">Date Range:</label>
                            <input type="text" id="date" class="form-control" placeholder="Select Date Range">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="applyFilter" class="btn btn-primary">Apply Filters</button>
                        <button type="button" id="resetFilter" class="btn btn-secondary">Reset Filters</button>
                    </div>
                </form>
            </div>

            <!-- DataTable -->
            <h2>Driver Status List</h2>
            <table id="driverStatusTable" class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Driver Name</th>
                    <th>Driver Phone</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <br>
            <!-- DataTable for Total Orders -->
            <h3>Total Orders per Driver</h3>
            <table id="totalOrdersTable" class="table table-bordered">
                <thead>
                <tr>
                    <th>Driver ID</th>
                    <th>Driver Name</th>
                    <th>Driver Phone</th>
                    <th>Total Orders</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </section>


    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>


    <script>
        $(document).ready(function() {
            // Initialize Date Range Picker
            $('#date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Initialize Driver Status List DataTable
            var driverStatusTable = $('#driverStatusTable').DataTable({
                processing: true,
                serverSide: true,
                dom: '<"top"i>rt<"bottom"lp><"clear">',
                ajax: {
                    url: "{{ route('driver-status-list') }}", // Add route to your method
                    data: function (d) {
                        d.status = $('#status').val();
                        d.operator_name = $('#operator_name').val();
                        d.date = $('#date').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'driver_name', name: 'driver_name' },
                    { data: 'driver_phone', name: 'driver_phone' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' }
                ]
            });

            // Initialize Total Orders DataTable
            var totalOrdersTable = $('#totalOrdersTable').DataTable({
                processing: true,
                serverSide: true,
                dom: '<"top"i>rt<"bottom"lp><"clear">',
                ajax: {
                    url: "{{ route('driver-order-list') }}", // Add route to your method
                    data: function (d) {
                        d.operator_name = $('#operator_name').val();
                        d.date = $('#date').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'driver_id' },
                    { data: 'driver_name', name: 'driver_name' },
                    { data: 'driver_phone', name: 'driver_phone' },
                    { data: 'total_orders', name: 'total_orders' }
                ]
            });

            // Apply filter to both tables
            $('#applyFilter').click(function() {
                driverStatusTable.draw();
                totalOrdersTable.draw();
            });

            // Reset filters for both tables
            $('#resetFilter').click(function() {
                $('#status').val('');
                $('#operator_name').val('');
                $('#date').val('');
                driverStatusTable.draw();
                totalOrdersTable.draw();
            });
        });

    </script>
@endsection

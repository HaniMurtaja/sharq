@extends('admin.layouts.app')


<style>
    .modal.show {
        opacity: 1 !important;
    }

    #order-list td {
        vertical-align: middle;
    }

    .dt-input {
        background: white !important;
    }

    .dt-layout-row:last-child .dt-info {
        font-size: 11.2px;
        color: #585858;
    }

    .dt-layout-row:last-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }

    .dt-layout-table {
        height: 380px;
    }

    #order-detail-table tbody tr td {
        border-bottom: .8px solid #dfdfdf;
    }

    .select2-container.select2-container--open:has(.select2-dropdown--below) .select2-dropdown--below {
        width: fit-content !important;
    }
</style>


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />

@section('title')
    Clients Sales Report
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">


        <!-- Table -->
        <div class="">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-192 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-192">
                    <h3 class="text-black fs-192 fw-bold m-0 ">Clients Sales Report</h3>
                </div>

            </div>

            <!-- Filter -->
            <div class="pxy-256 bg-white br-96 mb-192">

                <p class="fs-128 gap-2 gray-94 fw-semibold mb-3 pb-3 d-flex align-items-center border-bottom">
                    <img src="{{ asset('new/src/assets/icons/filter.svg') }}" class="brightness-50" width="16"
                        alt="" />
                    Filters
                </p>
                <!-- Filter -->
                <link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

                <form id="operatoe-data-filter-form">
                    <div class="row">
                        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
                            <span class="fs-112 gray-94 fw-semibold">Data From</span>
                            <div class="form-floating">
                                <input type='text'
                                    class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                                    value="{{ @request()->fromtime }}" autocomplete="off" name="fromtime" id="fromtime" />
                                <label for="tb-fnameddd" class="d-none">Data From</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
                            <span class="fs-112 gray-94 fw-semibold">Data To</span>
                            <div class="form-floating">
                                <input type='text'
                                    class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                                    value="{{ @request()->totime }}" autocomplete="off" id="totime" name="totime" />
                                <label for="tb-fnameddd" class="d-none">Data To</label>
                            </div>
                        </div>

                        <div class="col-md-12 text-end mt-4">
                            <a class="p-9228 black-1a bg-light br-96  fs-112 fw-semibold border" href="#"
                                id="back-btn">Back</a>
                            <button type="button" id="operatoe-data-filter-btn"
                                class="pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold">
                                <span>Apply Filter</span>
                            </button>

                        </div>


                    </div>
                </form>


            </div>
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">

            <div class="pxy-256 bg-white br-96 mb-192 ">
                <div class="table-responsive w-full overflow-x-auto  br-64 ">
                    <h1>Total Orders : <label id="total_orders_sum_display">0</label></h1>
                    <h1>Total Amount : <label id="total_amount_sum_display">0</label></h1>
                    <h1>Average Price Per Order : <label id="average_total_amount_sum_display">0</label></h1>
                    <h1>Total Branchs: <label id="total_branches_sum_display">0</label></h1>
                    <h1>Total Brands: <label id="total_clients_sum_display">0</label></h1>
                    <table id="order-list" class="table datatables table-boreder table-hover table-responsive">
                        <thead class="">
                            <tr>

                                <th class="px-4 py-3 font-medium">Id</th>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Total Branches</th>
                                <th class="px-4 py-3 font-medium">Total Drivers</th>
                                <th class="px-4 py-3 font-medium">Total Orders</th>
                                <th class="px-4 py-3 font-medium">Price Orders</th>
                                <th class="px-4 py-3 font-medium">Total </th>
                                <th class="px-4 py-3 font-medium">Detalis</th>


                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>



                    </table>

                </div>
            </div>



            <!-- Pagination -->

        </div>

        <!-- Operator Detail Modal -->
        <div class="modal fade " id="operatorReportDetail" aria-labelledby="operatorReportDetail" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2 border-0">
                        <h5 class="modal-title fw-bold">
                            Clients Sales report
                        </h5>
                        <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                    fill="black"></path>
                            </svg>

                        </button>
                    </div>
                    <div class="modal-body px-0">
                        <h1>Total Orders : <label id="total_orders_sum_perCity_display">0</label></h1>
                        <h1>Total Amount : <label id="total_amount_sum_perCity_display">0</label></h1>
                        <h1>Average Price Per Order : <label id="average_total_amount_sum_perCity_display">0</label></h1>
                        <h1>Total Branchs: <label id="total_branches_sum_perCity_display">0</label></h1>
                        <!-- Table of data -->

                        <table id="order-detail-table" class="w-full">
                            <thead class="">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Client Name</th>
                                    <th class="px-4 py-3 font-medium">Order Count</th>
                                    <th class="px-4 py-3 font-medium">Average Time</th>
                                </tr>


                            </thead>
                            <tbody>

                            </tbody>
                        </table>





                    </div>


                </div>
            </div>
        </div>


        <div class="modal fade " id="clientSaleDetail" aria-labelledby="operatorReportDetail" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2 pb-4 border-0">
                        <h5 class="modal-title fw-bold">
                            Client Sale Detail
                        </h5>
                        <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                    fill="black"></path>
                            </svg>

                        </button>
                    </div>
                    <div class="modal-body px-0">

                        <!-- Table of data -->

                        <table id="client-sale-table" class="w-full">
                            <thead class="">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Id</th>
                                    <th class="px-4 py-3 font-medium">Branch Name</th>
                                    <th class="px-4 py-3 font-medium">Total Orders</th>
                                    <th class="px-4 py-3 font-medium">Total Operators</th>
                                    <th class="px-4 py-3 font-medium">Price Order</th>
                                    <th class="px-4 py-3 font-medium">Total Amount</th>

                                </tr>


                            </thead>
                            <tbody>

                            </tbody>
                        </table>





                    </div>


                </div>
            </div>
        </div>

        <script src="{{ asset('maps/datepickerf/jquery.datetimepicker.full.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $(function() {

                    $('.datetimepicker1').datetimepicker({
                        locale: 'ru'
                    });
                });
            });
        </script>
    @endsection
    @include('livewire.order-history')


    <!-- Client Sales Modal -->


    <script>
        $(document).ready(function() {
            var driver_Id = null;
            var client_id = null;



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



            $('#operatoe-data-filter-btn').on('click', function() {
                $('#operatoe-data-filter-btn').prop('disabled', true).text('Loading...');

                operatorsTable();
            });

            $('#back-btn').on('click', function() {

                $('#operatoe-data-filter-form')[0].reset();


                $('#operator_id').val('').trigger('change');
                $('#city_id').val('').trigger('change');
                $('#fromtime').datetimepicker('clear');
                $('#totime').datetimepicker('clear');
            });




            function operatorsTable() {
                if ($.fn.DataTable.isDataTable('#order-list')) {
                    $('#order-list').DataTable().destroy();
                }

                let formData = $('#operatoe-data-filter-form').serialize();
                console.log('test');

                let sortUsed = false;

let table = $('#order-list').DataTable({
    processing: true,
    serverSide: true,
    order: [[4, 'DESC']],
    paging: false,
    lengthChange: false,

    ajax: {
        url: '{{ route('report.getClientsSalesReportData') }}',
        data: function(d) {
            let formData = $('#operatoe-data-filter-form').serializeArray();
            $.each(formData, function(index, field) {
                d[field.name] = field.value;
            });
        }
    },

    columns: [
        { data: 'client_id', name: 'client_id', orderable: true },
        { data: 'fullname', name: 'fullname', orderable: true },
        { data: 'client_branches', name: 'client_branches', orderable: true },
        { data: 'drivers_count', name: 'drivers_count', orderable: true },
        { data: 'total_orders', name: 'total_orders', orderable: true },
        { data: 'price_order', name: 'price_order', orderable: true },
        { data: 'total_amount', name: 'total_amount', orderable: true },
        { data: 'action', name: 'action', searchable: false, orderable: false }
    ],

    drawCallback: function(settings) {
        let api = this.api();
        let json = api.ajax.json();

        if (json && json.total_amount_sum !== undefined) {
            $('#total_orders_sum_display').html(json.total_orders_sum.toFixed(2));
            $('#total_amount_sum_display').html(json.total_amount_sum.toFixed(2));
            $('#average_total_amount_sum_display').html((json.total_amount_sum / json.total_orders_sum).toFixed(2));
            $('#total_branches_sum_display').html(json.total_branchs_sum.toFixed(2));
            $('#total_clients_sum_display').html(json.total_clients_sum.toFixed(2));
        }

        $('#operatoe-data-filter-btn').prop('disabled', false).html('<span>Apply Filter</span>');
    }
});

// ✅ إضافة حدث order فقط بعد الإنشاء
table.on('order.dt', function () {
    if (!sortUsed) {
        sortUsed = true;

        // اجعل كل الأعمدة غير قابلة للترتيب بعد أول مرة
        let settings = table.settings()[0];
        settings.aoColumns.forEach(function (col, i) {
            col.bSortable = false;
        });

        // أعد رسم الجدول بدون فقدان الصفحة الحالية
        table.draw(false);
    }
});

            }





            $(document).on('click', '.order-driver-btn', function() {

                let client_id = $(this).data('id');
                let fromTime = $('#fromtime').val();
                let toTime = $('#totime').val();

                if ($('#order-detail-table').length) {

                    if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                        $('#order-detail-table').DataTable().destroy();
                    }

                    $('#order-detail-table').DataTable({
                        processing: true,
                        serverSide: true,
                        order: [
                            [2, 'ASC']
                        ],
                        ajax: {
                            url: '{{ route('report.getClientsSalesReportDataPerCity') }}',
                            type: 'GET',
                            data: {
                                client_id: client_id,
                                fromtime: fromTime,
                                totime: toTime
                            }
                        },
                        columns: [{
                                data: "cityId",
                                title: "City ID"
                            },
                            {
                                data: "city",
                                title: "City Name"
                            },
                            {
                                data: "total_branches",
                                title: "Total Branches"
                            },
                            {
                                data: "total_drivers",
                                title: "Total Drivers"
                            },
                            {
                                data: "total_orders",
                                title: "Total Orders"
                            },
                            {
                                data: "price_order",
                                title: "Price Order"
                            },
                            {
                                data: "total_amount",
                                title: "Total Amount"
                            },
                            {
                                data: null,
                                render: function(data) {
                                    let fromTime = $('#fromtime').val();
                                    let toTime = $('#totime').val();
                                    let city_id = data
                                        .cityId; // Correctly use cityId from data
                                    // let client_id = client_id;

                                    let url =
                                        `{{ route('OrderDashboard') }}?datesearch=created_at&fromtime=${fromTime}&totime=${toTime}&client_id=${client_id}&city_id=${city_id}`;

                                    return `
                        <a type="button"

                           href="#"
                           class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 moreDetailsBtn client-sale-detail-btn" data-client_id = "${client_id}" data-city_id = "${city_id}">
                            <img src="{{ asset('new/src/assets/icons/view.svg') }}" alt="View" />
                        </a>`;
                                },
                                orderable: false,
                                searchable: false
                            }
                        ],
                        drawCallback: function(settings) {
                            // This callback will execute each time DataTables draws the table.
                            let api = this.api();
                            let json = api.ajax.json();

                            if (json && json.total_amount_sum !== undefined) {
                                $('#total_amount_sum_perCity_display').html(json
                                    .total_amount_sum.toFixed(2));
                                $('#total_orders_sum_perCity_display').html(json
                                    .total_orders_sum.toFixed(2));
                                $('#average_total_amount_sum_perCity_display').html((json
                                    .total_amount_sum / json.total_orders_sum).toFixed(
                                    2));

                                $('#total_branches_sum_perCity_display').html(json
                                    .total_branchs_sum.toFixed(2));
                            }
                        },
                        pageLength: 100,
                        lengthChange: false,
                        ordering: true,
                        searching: false,
                        paging: true,
                        info: true
                    });
                } else {
                    console.error("Table not found in the modal");
                }
            });










            $(document).on('hidden.bs.modal', '#operatorReportDetail', function() {
                console.log("Modal closed, destroying DataTable");

                if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                    $('#order-detail-table').DataTable().destroy();
                    console.log("DataTable destroyed");
                }
            });









            $(document).on('click', '.client-sale-detail-btn', function() {
                $('#clientSaleDetail').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#clientSaleDetail').modal('show');
            });



            $(document).on('click', '.client-sale-detail-btn', function() {
                console.log(9898);
                let client_id = $(this).data('client_id');
                let city_id = $(this).data('city_id');
                let fromTime = $('#fromtime').val();
                let toTime = $('#totime').val();
                if ($('#client-sale-table').length) {

                    if ($.fn.DataTable.isDataTable('#client-sale-table')) {
                        $('#client-sale-table').DataTable().destroy();
                    }



                    $('#client-sale-table').DataTable({
                        processing: true,
                        serverSide: true,
                        order: [
                            [2, 'ASC']
                        ],
                        ajax: {
                            url: '{{ route('getPerCityBranchesDetailes') }}',
                            type: 'GET',
                            data: {
                                client_id: client_id,
                                city_id: city_id,
                                fromTime: fromTime,
                                toTime: toTime

                            }
                        },
                        columns: [{
                                data: "branch_id",
                                title: "Branch ID"
                            },
                            {
                                data: "branch_name",
                                title: "Branch Name"
                            },
                            {
                                data: "total_orders",
                                title: "Total Orders"
                            },
                            {
                                data: "total_drivers",
                                title: "Total Drivers"
                            },
                            {
                                data: "price_order",
                                title: "Order Price"
                            },

                            {
                                data: "total_amount",
                                title: "Total Amount"
                            },

                        ],

                        pageLength: 100,
                        lengthChange: false,
                        ordering: true,
                        searching: false,
                        paging: true,
                        info: true
                    });

                } else {
                    console.error("Table not found in the modal");
                }
            });


            $(document).on('hidden.bs.modal', '#clientSaleDetail', function() {
                if ($.fn.DataTable.isDataTable('#client-sale-table')) {
                    $('#client-sale-table').DataTable().destroy();
                    console.log("DataTable destroyed");
                }
            });



        });





        $(function() {
            $('input[name="date"]').daterangepicker({
                opens: 'left',
                autoUpdateInput: false // Prevent automatic update to preserve your custom logic
            }, function(start, end) {
                // Manually set the value in the format you want
                $('input[name="date"]').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });


            $('.select2all').select2({
                allowClear: true,
                placeholder: 'Please choose',
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



        });
    </script>

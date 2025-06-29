<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('new/src/css/acceptanceRatePopup.css') }}">

<div class="modal fade clientModalPopup" id="operatorsAcceptanceRateModal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="rechargeModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">Acceptance Rate</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tab s" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fs-112 custom-tab-btn-for-rate" id="pending-orders-rate"
                            data-bs-toggle="tab" data-bs-target="#newUser" type="button" role="tab"
                            aria-controls="newUser" aria-selected="true">
                            
                            Pending driver acceptance for more than 2 minutes.
                            <i class="fas fa-sync-alt me-1"></i> <!-- Refresh icon -->
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fs-112 custom-tab-btn-for-rate" id="acceptance-order-rate-more2"
                            data-bs-toggle="tab" data-bs-target="#existingUser" type="button" role="tab"
                            aria-controls="existingUser" aria-selected="false">
                            Acceptance rate is greater than 2 minutes
                            <i class="fas fa-sync-alt me-1"></i> <!-- Refresh icon -->
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3 d-block" id="userTabsContent">
                    <div class="tab-pane show" id="newUser" role="tabpanel" aria-labelledby="newUser-tab">
                        <!-- Table of data -->

                        <table id="less-than-or-equal-2-minutes" class="w-full">
                            <thead class="">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Id</th>
                                    <th class="px-4 py-3 font-medium">Name</th>
                                    <th class="px-4 py-3 font-medium">City</th>
                                    <th class="px-4 py-3 font-medium">Total Orders</th>
                                    <th class="px-4 py-3 font-medium">AVG Acceptance</th>
                                    <th class="px-4 py-3 font-medium">Detalis</th>
                                </tr>


                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="existingUser" role="tabpanel" aria-labelledby="existingUser-tab">
                        <table id="greater-than-2-minutes" class="w-full">
                            <thead class="">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Id</th>
                                    <th class="px-4 py-3 font-medium">Name</th>
                                    <th class="px-4 py-3 font-medium">City</th>
                                    <th class="px-4 py-3 font-medium">Total Orders</th>
                                    <th class="px-4 py-3 font-medium">AVG Acceptance</th>
                                    <th class="px-4 py-3 font-medium">Detalis</th>
                                </tr>


                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- Operator Detail Modal -->
<div class="modal fade " id="operatorReportDetail" aria-labelledby="operatorReportDetail" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2 pb-4 border-0">
                <h5 class="modal-title fw-bold">
                    Operator Report Detail
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







<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>

<script>
    $(document).ready(function() {
        $(document).on('shown.bs.modal', '#operatorsAcceptanceRateModal', function(e) {
            $('.tab-pane').removeClass('active fade');
        });

        $(document).on('click', '#pending-orders-rate', function() {
            operatorsLessThan2Table()
        })

        $(document).on('click', '#acceptance-order-rate-more2', function() {
            operatorsMoreThan2Table()
        })

        $(document).on('click', '#operatorsAcceptanceRateModal .nav-link', function() {
            $('.tab-pane').removeClass('active fade');
            var target = $(this).data('tab');
            $('.tab-pane[data-type="' + target + '"]').addClass('active');
            $('.nav-link').removeClass('active fade');
            $(this).addClass('active');
        });

        $(document).on('shown.bs.modal', '#operatorsAcceptanceRateModal', function() {
            if ($('#less-than-or-equal-2-minutes').length) {
                operatorsLessThan2Table()
            } else {
                console.error("Table not found in the modal");
            }
        });

        $(document).on('hidden.bs.modal', '#operatorsAcceptanceRateModal', function() {
            if ($.fn.DataTable.isDataTable('#less-than-or-equal-2-minutes')) {
                $('#less-than-or-equal-2-minutes').DataTable().destroy();
                console.log("DataTable destroyed");
            }
            if ($.fn.DataTable.isDataTable('#greater-than-2-minutes')) {
                $('#greater-than-2-minutes').DataTable().destroy();
                console.log("DataTable destroyed");
            }

            $('#operatorsAcceptanceRateModal .nav-link').removeClass('active');
            $('#operatorsAcceptanceRateModal .tab-pane').removeClass('active show');
            $('#operatorsAcceptanceRateModal .nav-link:first').addClass('active');
            $('#operatorsAcceptanceRateModal .tab-pane:first').addClass('show');
        });

        $(document).on('click', '.order-driver-btn', function() {
            $('#operatorReportDetail').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#operatorReportDetail').modal('show');
        });

        $(document).on('click', '.order-driver2-btn', function() {
            $('#operatorReportDetail').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#operatorReportDetail').modal('show');
        });


        $(document).on('click', '.order-driver2-btn', function() {
            console.log(9898);
            driver_Id = $(this).data('id')


            if ($('#order-detail-table').length) {

                if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                    $('#order-detail-table').DataTable().destroy();
                }

                $('#order-detail-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [2, 'ASC']
                    ],
                    ajax: {
                        url: '{{ route('getOperatorsPendingRateDetailes') }}',
                        type: 'GET',
                        data: {
                            driver_id: driver_Id,

                        }
                    },
                    columns: [{
                            data: "full_name",
                            title: "Client Name"
                        },
                        {
                            data: "pending_orders_count",
                            orderable: false
                        },
                        {
                            data: "avg_pending_time",
                            title: "Average Time"
                        },
                        {
                            "data": 'client_id',
                            "render": function(data) {
                                let today = new Date();


                                let yesterday = new Date(today);
                                yesterday.setDate(today.getDate() - 1);


                                let yyyyFrom = yesterday.getFullYear();
                                let mmFrom = String(yesterday.getMonth() + 1).padStart(
                                    2, '0');
                                let ddFrom = String(yesterday.getDate()).padStart(2,
                                    '0');


                                let yyyyTo = today.getFullYear();
                                let mmTo = String(today.getMonth() + 1).padStart(2,
                                    '0');
                                let ddTo = String(today.getDate()).padStart(2, '0');

                                let fromTime =
                                    `${yyyyFrom}-${mmFrom}-${ddFrom} 00:00:00`;
                                let toTime = `${yyyyTo}-${mmTo}-${ddTo} 23:59:59`;

                                let driverId =
                                    driver_Id;

                                let url =
                                    `{{ route('OrderDashboard') }}?assigned_by=&datesearch=created_at&fromtime=${fromTime}&totime=${toTime}&id=&client_order_id=&client_order_id_string=&customer_name=&customer_phone=&client_id=${data}&driver_id=${driverId}&city_id=`;

                                return `
                                            <a
                                                type="button"
                                                data-user-id="${data}"
                                                target="_blank"
                                                href="${url}"
                                                class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 moreDetailsBtn">
                                                <img src="{{ asset('new/src/assets/icons/view.svg') }}" alt="" />
                                            </a>
                                        `;
                            },
                            "orderable": false
                        }



                    ],
                    pageLength: 5,
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

        $(document).on('click', '.order-driver-btn', function() {
            console.log(9898);
            driver_Id = $(this).data('id')


            if ($('#order-detail-table').length) {

                if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                    $('#order-detail-table').DataTable().destroy();
                }

                $('#order-detail-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [2, 'ASC']
                    ],
                    ajax: {
                        url: '{{ route('getOperatorsAcceptanceRateDetailes') }}',
                        type: 'GET',
                        data: {
                            driver_id: driver_Id,

                        }
                    },
                    columns: [{
                            data: "full_name",
                            title: "Client Name"
                        },
                        {
                            data: "orders_count",
                            orderable: false
                        },
                        {
                            data: "avg_accept_time",
                            title: "Average Time"
                        },
                        {
                            "data": 'client_id',
                            "render": function(data) {
                                let today = new Date();


                                let yesterday = new Date(today);
                                yesterday.setDate(today.getDate() - 1);


                                let yyyyFrom = yesterday.getFullYear();
                                let mmFrom = String(yesterday.getMonth() + 1).padStart(
                                    2, '0');
                                let ddFrom = String(yesterday.getDate()).padStart(2,
                                    '0');


                                let yyyyTo = today.getFullYear();
                                let mmTo = String(today.getMonth() + 1).padStart(2,
                                    '0');
                                let ddTo = String(today.getDate()).padStart(2, '0');

                                let fromTime =
                                    `${yyyyFrom}-${mmFrom}-${ddFrom} 00:00:00`;
                                let toTime = `${yyyyTo}-${mmTo}-${ddTo} 23:59:59`;

                                let driverId =
                                    driver_Id;

                                let url =
                                    `{{ route('OrderDashboard') }}?assigned_by=&datesearch=created_at&fromtime=${fromTime}&totime=${toTime}&id=&client_order_id=&client_order_id_string=&customer_name=&customer_phone=&client_id=${data}&driver_id=${driverId}&city_id=`;

                                return `
                                            <a
                                                type="button"
                                                data-user-id="${data}"
                                                target="_blank"
                                                href="${url}"
                                                class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 moreDetailsBtn">
                                                <img src="{{ asset('new/src/assets/icons/view.svg') }}" alt="" />
                                            </a>
                                        `;
                            },
                            "orderable": false
                        }



                    ],
                    pageLength: 5,
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
            if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                $('#order-detail-table').DataTable().destroy();
                console.log("DataTable destroyed");
            }
        });



        function operatorsLessThan2Table() {
            if ($.fn.DataTable.isDataTable('#less-than-or-equal-2-minutes')) {
                $('#less-than-or-equal-2-minutes').DataTable().destroy();
            }



            $('#less-than-or-equal-2-minutes').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [4, 'DESC']
                ],
                "lengthMenu": [10, 15, 20, 50, 100, 500, 1000, 2000],
                ajax: {
                    url: '{{ route('getOperatorsAcceptanceRateLessTwo') }}',

                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'full_name',
                        name: 'first_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'city_name',
                        name: 'city_name',
                        searchable: false
                    },
                    {
                        data: 'pending_orders_count',
                        name: 'pending_orders_count',
                        searchable: false,
                        orderable: true
                    },

                    {
                        data: 'avg_pending_time',
                        name: 'avg_pending_time',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],

            });



        }


        function operatorsMoreThan2Table() {
            if ($.fn.DataTable.isDataTable('#greater-than-2-minutes')) {
                $('#greater-than-2-minutes').DataTable().destroy();
            }



            $('#greater-than-2-minutes').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [4, 'DESC']
                ],
                "lengthMenu": [10, 15, 20, 50, 100, 500, 1000, 2000],
                ajax: {
                    url: '{{ route('getOperatorsAcceptanceRateMoreTwo') }}',

                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'full_name',
                        name: 'first_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'city_name',
                        name: 'city_name',
                        searchable: false
                    },
                    {
                        data: 'orders_count',
                        name: 'orders_count',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'avg_accept_time',
                        name: 'avg_accept_time',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],

            });



        }


    });
</script>

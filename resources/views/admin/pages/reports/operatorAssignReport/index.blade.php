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
    Operator Report
@endsection
@section('content')
    <!-- Operator Detail Modal -->
    <div class="modal fade " id="operatorReportDetail" aria-labelledby="operatorReportDetail" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2 border-0">
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





    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">

        <!-- Table -->
        <div class="">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-192 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-192">
                    <h3 class="text-black fs-192 fw-bold m-0 ">Operator Report</h3>
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
                @include('admin.pages.reports.operatorAssignReport.search')

            </div>
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">

            <div class="pxy-256 bg-white br-96 mb-192 ">
                <div class="col-md-12 text-end mt-4">
                    <form id="exportForm" method="GET" style="display:inline;">
                        @csrf
                        {{-- @method('POST') --}}

                        <input type="hidden" name="date_export_form" id="dateFromExportInput">
                        <input type="hidden" name="date_export_to" id="dateToExportInput">



                        <div class="flex items-center justify-end col-span-2 md:justify-end">
                            <button class="pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold" type="submit">


                                <span>Export</span>
                            </button>
                        </div>
                    </form>


                </div>
                <br>
                <div class="table-responsive w-full overflow-x-auto  br-64 ">
                    <table id="order-list" class="table datatables table-boreder table-hover table-responsive">
                        <thead class="">
                            <tr>

                                <th class="px-4 py-3 font-medium">Id</th>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">City</th>
                                <th class="px-4 py-3 font-medium">Total Orders</th>
                                <th class="px-4 py-3 font-medium">AVG Acceptance</th>
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
    <script>
        $(document).ready(function() {
            var driver_Id = null;

            $('.status').select2({
                placeholder: "Status",
                allowClear: true
            });

            $('.acceptance_rate').select2({
                placeholder: "Acceptance rate",
                allowClear: true,
                minimumResultsForSearch: Infinity

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



            $('#operatoe-data-filter-btn').on('click', function() {

                operatorsTable();
            });

            document.querySelector('#exportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                document.getElementById('dateFromExportInput').value = document.getElementById('fromtime')
                    .value;
                document.getElementById('dateToExportInput').value = document.getElementById('totime')
                .value;

                const form = e.target;
                const formData = new URLSearchParams(new FormData(form)).toString();


                fetch("{{ url('admin/export-operator-assign-report-data') }}?" + formData, {
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




            $('#back-btn').on('click', function() {

                $('#operatoe-data-filter-form')[0].reset();


                $('#operator_id').val('').trigger('change');
                $('#city_id').val('').trigger('change');
                $('#fromtime').datetimepicker('clear');
                $('#totime').datetimepicker('clear');
            });


            operatorsTable();

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
    ordering: true, // نفعّل الفرز الآن
    order: [[0, 'desc']], // الترتيب الافتراضي

    ajax: {
        url: '{{ route('report.getOperatorAssignReportData') }}',
        data: function(d) {
            let formData = $('#operatoe-data-filter-form').serializeArray();
            $.each(formData, function(index, field) {
                d[field.name] = field.value;
            });
        }
    },

    columns: [
        { data: 'id', name: 'id' },
        { data: 'full_name', name: 'first_name' },
        { data: 'city_name', name: 'city_name' },
        { data: 'orders_count', name: 'orders_count' },
        { data: 'avg_accept_time', name: 'avg_accept_time' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});

                // الحدث عند ترتيب المستخدم لأي عمود
table.on('order.dt', function () {
    if (!sortUsed) {
        sortUsed = true;

        // انتظر قليلًا حتى يتم الترتيب
        setTimeout(() => {
            // أعد تهيئة الجدول بدون ترتيب
            let currentSettings = table.settings().init();
            currentSettings.ordering = false; // إلغاء الترتيب تمامًا

            table.destroy(); // إزالة الجدول القديم
            $('#order-list').DataTable(currentSettings); // إعادة بناء الجدول بدون ترتيب
        }, 300); // قليل من الوقت بعد أول sort
    }
});
            }





            $(document).on('click', '.order-driver-btn', function() {
                console.log(9898);
                driver_Id = $(this).data('id')
                let driverId = $(this).data('id');
                let fromTime = $('#fromtime').val();
                let toTime = $('#totime').val();

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
                            url: '{{ route('report.getOperatorOrderSummaryData') }}',
                            type: 'GET',
                            data: {
                                driver_id: driverId,
                                fromtime: fromTime,
                                totime: toTime
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
                                    let fromTime = $('#fromtime').val();
                                    let toTime = $('#totime').val();
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
                console.log("Modal closed, destroying DataTable");

                if ($.fn.DataTable.isDataTable('#order-detail-table')) {
                    $('#order-detail-table').DataTable().destroy();
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
<script>
    $(document).ready(function() {
        $('#operatoe-data-filter-btn').on('click', function (e) {
            e.preventDefault();

            // تعطيل الزر
            let btn = $(this);
            btn.attr('disabled', true).text('Loading...');

            // إعادة تحميل الجدول
            $('#order-list').DataTable().ajax.reload();

            // إعادة تفعيل الزر بعد 60 ثانية
            setTimeout(function() {
                btn.attr('disabled', false).text('Apply Filter');
            }, 60000); // 60000 مللي ثانية = 1 دقيقة
        });
    });
</script>

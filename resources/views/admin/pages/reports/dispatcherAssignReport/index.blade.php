@extends('admin.layouts.app')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


<link rel="stylesheet" href="{{ asset('new/src/css/dashboard.css') }}" />

@section('title')
    {{ trans('dashboard.dispatcherAssignReport') }}
@endsection
@section('content')
    <div class="flex flex-col h-screen p-6">
        <!-- Filter -->
        @include('admin.pages.reports.clientsSalesreport.search')
        <!-- Table -->
        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-4 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">{{ trans('dashboard.dispatcherAssignReport') }}
                        ({{ $dispatcherCount }})</h3>
                </div>

            </div>


            {{-- <div class="col-md-12 text-end mt-4"> --}}
            <form id="exportForm" method="GET" style="display:inline;">
                @csrf
                <input type="hidden" name="fromtime" id="export_fromtime">
                <input type="hidden" name="totime" id="export_totime">
                <input type="hidden" name="assigned_by" id="export_assigned_by">
                <input type="hidden" name="city_id" id="export_city_id">

                <div class="flex items-center justify-end col-span-2 md:justify-end">
                    <button type="submit" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1">
                        <span>Export</span>
                    </button>
                </div>
            </form>

            <br>

            {{-- </div> --}}


            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">

            <div class="table-responsive w-full overflow-x-auto">

                <table id="order-list" class="table datatables table-boreder table-hover table-responsive">
                    <thead class="bg-gray-50">
                        <tr>

                            <th onclick='sortTable(0)'>Id <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(1)'>Name <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(2)'>Total Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(3)'>AVG Assign <span style="opacity: 0.5;">↑↓</span></th>
                            <th>Detalis</th>


                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $row)
                            <tr>
                                <td>{{ $row->user_id }}</td>
                                <td>{{ $row->full_name }}</td>
                                <td>{{ $row->total_orders }}</td>
                                <td>{{ $row->avg_assign_time }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm moreDetailsBtn"
                                        data-user-id="{{ $row->user_id }}">Show Report</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>



                </table>

            </div>
            <div class="d-flex justify-content-center">


                {!! $reportData->appends(request()->all())->links() !!}
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


        <div class="modal fade dashboardModal" id="reportModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="rechargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="head">
                            <h4>
                                Orders
                            </h4>
                            <p id="totalOrders">

                            </p>
                        </div>
                        <button class="closeBtn" aria-label="Close" data-bs-dismiss="modal">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                    fill="black"></path>
                            </svg>
                        </button>

                    </div>

                    <div class="modal-body">
                        <div class="scrollable-table">
                            <table class="table" id="reportTable">
                                <thead>
                                    <th>City </th>
                                    <th>Total Orders</th>
                                    <th>AVG Assign</th>
                                    <th>#</th>
                                <tbody>



                                </tbody>
                            </table>

                        </div>


                    </div>
                </div>

            </div>

        </div>
    @endsection
    <script>
        $(document).ready(function() {

            document.querySelector('#exportForm').addEventListener('submit', function(e) {
                e.preventDefault(); 
                document.getElementById('export_fromtime').value = document.getElementById('from_date')
                    .value;
                document.getElementById('export_totime').value = document.getElementById('to_date').value;
                document.getElementById('export_assigned_by').value = document.getElementById('assigned_by')
                    .value;
                document.getElementById('export_city_id').value = document.getElementById('city_id').value;

                // Prepare form data
                const form = e.target;
                const formData = new URLSearchParams(new FormData(form)).toString();

                // Send AJAX request
                fetch("{{ url('admin/export-dispatchers-assign-report-data') }}?" + formData, {
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



            $(".moreDetailsBtn").on("click", function() {
                var orderDashboardRoute =
                    "{{ route('OrderDashboard') }}"; // Pass the route from Blade to JavaScript

                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                var clientFilter = $('#clientFilter').val();
                var client_id = $('#client_id').val();
                var city_id = $('#city_id').val();
                let userId = $(this).data("user-id");

                // Clear previous data
                $('#reportTable tbody').empty();
                $('#totalOrders').empty();

                // Call the report API via AJAX.
                $.ajax({
                    url: '{{ route('report.dispatcherAssignReportShowBy') }}', // Replace with your actual route URL
                    type: 'GET',
                    data: {

                        from_date: fromDate,
                        to_date: toDate,
                        clientFilter: clientFilter,
                        client_id: client_id,
                        city_id: city_id,
                        assigned_by: userId
                    },
                    success: function(response) {
                        // Loop through each user object from the API response
                        $.each(response.data, function(index, user) {

                            var reportUrl = orderDashboardRoute + '?fromtime=' +
                                fromDate + '&totime=' + toDate + '&assigned_by=' +
                                userId + '&city_id=' + user.city_id +
                                '&datesearch=driver_assigned_at';

                            var row = '<tr>' +

                                '<td>' + user.city_name + '</td>' +
                                '<td>' + user.total_orders + '</td>' +
                                '<td>' + user.avg_assign_time + '</td>' +
                                '<td><a class="btn btn-primary btn-sm" target="_blank" href="' +
                                reportUrl + '">Show</a></td>' +

                                '</tr>';
                            $('#reportTable tbody').append(row);
                        });
                        // Display the total orders count
                        $('#totalOrders').text('Total Orders: ' + response.counts_orders);

                        // Open the Bootstrap modal
                        var modal = new bootstrap.Modal(document.getElementById('reportModal'));
                        modal.show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching report data: " + error);
                    }
                });
            });
        });
    </script>

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
        let sortOrder = {};

        function sortTable(columnIndex) {

            const table = document.querySelector("#order-list");
            if (!table) return;

            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));
            const headers = table.querySelectorAll("th");


            headers.forEach(th => th.style.background = "");

            sortOrder[columnIndex] = !sortOrder[columnIndex];

            rows.sort((rowA, rowB) => {
                let valA = rowA.cells[columnIndex].textContent.trim().toLowerCase();
                let valB = rowB.cells[columnIndex].textContent.trim().toLowerCase();

                valA = isNaN(valA) ? valA : parseFloat(valA);
                valB = isNaN(valB) ? valB : parseFloat(valB);

                return sortOrder[columnIndex] ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
            });
            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));

            headers.forEach((th, index) => {
                th.style.background = "";
                th.style.color = "";
                th.style.borderRadius = "";
                th.innerHTML = th.innerHTML.replace(/<span.*<\/span>/, "") +
                    ` <span style="opacity: 0.5;">↑↓</span>`;
            });

            headers[columnIndex].style.color = "#f46624";

            headers[columnIndex].innerHTML = headers[columnIndex].innerHTML.replace(/<span.*<\/span>/, "") +
                ` <span style="opacity: 1; font-weight: bold;">${sortOrder[columnIndex] ? "↑" : "↓"}</span>`;
        }
    </script>



    >

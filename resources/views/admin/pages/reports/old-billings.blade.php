@extends('admin.layouts.app')

@section('content')
    <div class="flex flex-col p-6">
        <!-- Tabs and Button -->
        <div class="flex flex-col-reverse justify-between md:flex-row">
            <div class="flex mb-4 space-x-8 border-b operator_billings_tabs">
                <button class="px-4 py-3 font-semibold border-b-2 text-mainColor border-mainColor operator_billings_tab"
                    data-tab="Operator Billings" id="operator_billings">
                    Operator Billings
                </button>
                <button id="cod_billings" class="px-6 py-4 text-gray-600 operator_billings_tab" data-tab="COD Billings">
                    COD Billings
                </button>
            </div>

            
        </div>

        <!-- Operator Billings -->
        <div class="operator_billings_tab_content" data-tab="Operator Billings">
            <!-- Balance setions -->
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div
                    class="flex flex-col justify-center p-5 bg-white border rounded-lg md:justify-between md:flex-row md:px-8 md:p-10 border-gray1">
                    <div class="flex flex-col gap-6">
                        <h4 class="text-sm font-medium text-black1">
                            Total Balance
                        </h4>
                        <p class="text-lg md:text-3xl">
                            <span class="text-sm">SAR</span> {{ $total_balance }}
                        </p>
                    </div>

                    <div class="flex items-center justify-center">
                        <img src="{{ asset('new/src/assets/icons/Wallet.svg') }}" alt="" />
                    </div>
                </div>
                <div
                    class="flex flex-col justify-center p-10 px-8 bg-white border rounded-lg md:flex-row md:justify-between border-gray1">
                    <div class="flex flex-col gap-6">
                        <h4 class="text-sm font-medium text-black1">
                            Total Balance after taxes
                        </h4>
                        <p class="text-lg md:text-3xl">
                            <span class="text-sm">SAR</span> {{ $total_balance_after_tax }}
                        </p>
                    </div>

                    <div class="flex items-center justify-center">
                        <img src="{{ asset('new/src/assets/icons/Tax.svg') }}" alt="" />
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white border rounded-lg border-gray1">
                <!-- Navigation Tabs -->
                <div class="flex flex-col items-center justify-between mb-4 border-b md:flex-row">


                </div>
                <!-- Table -->
                <div class="w-full overflow-x-auto">
                    <table id="operator-billings-table" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                    Name
                                </th>
                                <th class="px-4 py-3 font-medium">ID</th>
                                <th class="px-4 py-3 font-medium">Total Order</th>
                                <th class="px-4 py-3 font-medium">Service fees</th>
                                <th class="px-4 py-3 font-medium">Operator fees</th>
                                <th class="px-4 py-3 font-medium">Balance</th>
                                <th class="px-4 py-3 font-medium">After tax</th>
                            </tr>


                        </thead>
                        <tbody >

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- COD Billings -->
        <div class="hidden operator_billings_tab_content" data-tab="COD Billings">
            <!-- Balance setions -->
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div class="flex justify-between p-10 px-8 bg-white border rounded-lg border-gray1">
                    <div class="flex flex-col gap-6">
                        <h4 class="text-sm font-medium text-black1">
                            Total Balance
                        </h4>
                        <p class="text-3xl">
                            <span class="text-sm">SAR</span> {{ $total_balance }}
                        </p>
                    </div>

                    <div>
                        <img src="{{ asset('new/src/assets/icons/Wallet.svg') }}" alt="" />
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white border rounded-lg border-gray1">
                <!-- Navigation Tabs -->
                <div class="flex items-center justify-between mb-4 border-b">

                </div>
                <!-- Table -->
                <div class="w-full overflow-x-auto">
                    <table id="cod-billings-table" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                        <thead class="bg-gray-50">
                            <tr>


                                <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                    Driver
                                </th>
                                <th class="px-4 py-3 font-medium">Balance</th>

                            </tr>
                        </thead>
                        <tbody >

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    var id = 123; // Ensure `id` is globally available

    $(document).ready(function() {
        console.log('begin');
        initializeOperatorBillingsDataTable(id); // Initialize the DataTable

        $('#operator_billings').on('click', function(e) {
            e.preventDefault();

            
            initializeOperatorBillingsDataTable(id);
        });

        $('#cod_billings').on('click', function(e) {
            e.preventDefault();

            
            initializeCODBillingsDataTable(id);
        });
    });

    function initializeOperatorBillingsDataTable(id) {
        if ($.fn.DataTable.isDataTable('#operator-billings-table')) {
            $('#operator-billings-table').DataTable().destroy(); // Destroy existing table
        }

        $('#operator-billings-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('get-billings-report') }}",
                "type": "GET",
                "data": function(d) {
                    d.id = id;
                }
            },
            "columns": [{
                    "data": "driver"
                },
                {
                  "data": 'id'
                },
                {
                    "data": "order_count"
                },
                {
                    "data": "service_fees"
                },
                {
                    "data": "operator_fees"
                },
                {
                    "data": "balance"
                },
                {
                    "data": "after_tax"
                }
            ],
            "pageLength": 20,
            "lengthChange": false
        });
    }



    function initializeCODBillingsDataTable(id) {
        if ($.fn.DataTable.isDataTable('#cod-billings-table')) {
            $('#cod-billings-table').DataTable().destroy(); // Destroy existing table
        }

        $('#cod-billings-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('get-cod-billings-report') }}",
                "type": "GET",
                "data": function(d) {
                    d.id = id;
                }
            },
            "columns": [{
                    "data": "driver"
                },

                {
                    "data": "balance"
                }
            ],
            "pageLength": 20,
            "lengthChange": false
        });
    }
</script>

@extends('admin.layouts.app')
<style>
    #get-orders-form .select2-container .select2-selection--single,
    #get-orders-form .select2-container .select2-selection--multiple {
        height: auto !important;
    }
</style>
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

            <form id="get-orders-form" class="hidden grid-cols-1 mt-6 " id="filter-body-inner">

                @csrf

                <div class="row">


                    <div class="col-md-6">
                        <div class="form-group">

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="date" class="form-control float-right" id="reservation"
                                    placeholder="from:-to:-" value="">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">


                            <select
                                class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3 status"
                                multiple="multiple" style="width: 100%;" name="clients[]" id="clients">
                                <option value="-1">All</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                @endforeach
                            </select>
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
                <div class="flex flex-col mb-3">
                    <form id="exportForm" method="GET" style="display:inline;">
                        @csrf
                        {{-- @method('POST') --}}

                        <input type="hidden" name="date-export-form" id="dateExportInput">
                        <input type="hidden" name="clients-export-form[]" id="clientsExportInput">



                        <div class="flex items-center justify-end col-span-2 md:justify-end">
                            <button class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1" type="submit">


                                <span>Export</span>
                            </button>
                        </div>
                    </form>



                </div>
                <table id="order-list" class="w-full text-sm  text-gray-700 ">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium">ID</th>
                            <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                Name
                            </th>

                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Account Number</th>

                            <th class="px-4 py-3 font-medium">Country</th>
                            <th class="px-4 py-3 font-medium">City</th>
                            <th class="px-4 py-3 font-medium">Currency</th>
                            <th class="px-4 py-3 font-medium">Parial Pay</th>
                            <th class="px-4 py-3 font-medium">Group</th>
                            <th class="px-4 py-3 font-medium">Note</th>
                            <th class="px-4 py-3 font-medium">Integration Company</th>
                            <th class="px-4 py-3 font-medium">Default Prepration Time</th>
                            <th class="px-4 py-3 font-medium">Min. Prepration Time</th>

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
            $('#clients').select2({
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



            document.querySelector('#exportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var dateValue = $('#reservation').val();
                var clientsValues = $('#clients').val();
                $('#dateExportInput').val(dateValue);

                if (clientsValues && clientsValues.length > 0) {
                    $('#clientsExportInput').val(clientsValues.join(
                        ','));
                } else {
                    $('#clientsExportInput').val('');
                }
                const form = e.target;
                const formData = new URLSearchParams(new FormData(form)).toString();


                fetch("{{ url('admin/export-clients-data') }}?" + formData, {
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




            initializeOrdersDataTable();




            $('#get-order-list').click(function() {
                initializeOrdersDataTable();









            });







            function initializeOrdersDataTable() {
                const $table = $('#order-list');

                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().destroy();
                }

                $table.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('get-clients-list') }}",
                        type: "GET",
                        data: function(d) {

                            $('#get-orders-form').serializeArray().forEach(function(field) {
                                d[field.name] = field.value;
                            });


                            d['clients'] = $('#clients').val() || [];
                        },
                        dataSrc: function(json) {
                            console.log('AJAX Response:', json);
                            return json.data;
                        },
                        error: function(xhr, status, error) {
                            console.error('DataTable AJAX Error:', status, error);
                        }
                    },
                    columns: [{
                            data: "id",
                            name: "id"
                        },
                        {
                            data: "name",
                            name: "first_name"
                        },
                        {
                            data: "phone",
                            name: "Phone"
                        },
                        {
                            data: "email",
                            name: "email"
                        },
                        {
                            data: "account_no",
                            name: "client.account_number"
                        },
                        {
                            data: "country",
                            name: "client.country.name"
                        },
                        {
                            data: "city",
                            name: "client.city.name"
                        },
                        {
                            data: "currency",
                            title: "Currency",
                            orderable: false
                        },
                        {
                            data: "parial_pay",
                            name: "client.partial_pay",
                            orderable: false
                        },
                        {
                            data: "group",
                            name: "client_group_name"
                        },
                        {
                            data: "note",
                            name: "note"
                        },
                        {
                            data: "integration_company",
                            name: "client.integration.name"
                        },
                        {
                            data: "defualt_prepration_time",
                            name: "client.default_prepartion_time",
                            orderable: false
                        },
                        {
                            data: "min_prepration_time",
                            name: "client.min_prepartion_time",
                            orderable: false
                        }
                    ],
                    pageLength: 20,
                    lengthChange: false,
                    ordering: true,
                    responsive: true,
                    autoWidth: false
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

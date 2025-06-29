@extends('admin.layouts.app')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Include Date Range Picker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Tempus Dominus Bootstrap 5 CSS -->
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.0.0/dist/css/tempus-dominus.min.css">


<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        /* Reduced width */
        height: 20px;
        /* Reduced height */
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        /* Reduced height */
        width: 14px;
        /* Reduced width */
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #f46624;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #f46624;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(20px);
        /* Adjusted for smaller width */
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 20px;
        /* Adjusted for smaller height */
    }

    .slider.round:before {
        border-radius: 50%;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
        background-color: #e9ecef;
    }


    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;

    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }
</style>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
        background-color: #e9ecef;
    }


    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;

    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }
</style>
<style>
    .file-upload {
        display: none;
    }

    .upload-label {
        display: inline-block;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background-color: #f0f0f0;
        border: 2px solid #ccc;
        background-size: cover;
        background-position: center;
    }

    .upload-label svg {
        width: 50%;
        height: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        fill: #aaa;
    }

    .nav-link.active,
    .nav-pills .show>.nav-link {
        color: #6c757d !important;
        background-color: #fff !important;
    }

    .file-upload:focus+.upload-label,
    .file-upload:active+.upload-label {
        outline: 2px solid #007bff;
    }

    .custom-system-settings a {
        text-decoration: none;
    }

    .custom-system-settings a:hover {
        text-decoration: none;
        color: inherit;
    }
</style>
@section('content')
    <div class="flex flex-row min-h-screen p-4 custom-system-settings">
        <div class="flex flex-col w-full p-4 bg-white border rounded-lg md:flex-row border-gray1">
            <!-- Sidebar -->
            <div
                class="w-full h-screen py-3 pl-4 overflow-y-scroll bg-white border rounded lg:w-1/4 md:w-1/3 border-gray1 settings_sidebar">
                <div class="space-y-6">
                    <!-- Account Section -->
                    <div>
                        <h2 class="text-base font-bold text-gray-600">Account</h2>
                        @if (auth()->user()->user_role?->value == 1)
                            <!-- Account -->
                            <a href="#account"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-user-tie"></i>
                                    <span>Account</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Privacy and Security -->
                            <a href="#privacy"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa fa-lock"></i>

                                    <span> Privacy and Security</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Api Settings -->
                            <a href="#api"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa fa-code"></i>

                                    <span>Api Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        @endif

                        @if (auth()->user()->user_role?->value == 2 ||
                                auth()->user()->user_role?->value == 4 ||
                                auth()->user()->user_role?->value == 5)
                            <!-- Account -->
                            <a href="#account"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-user-tie"></i>
                                    <span>Account</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Privacy and Security -->

                            <!-- Api Settings -->
                            <a href="#api"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa fa-code"></i>

                                    <span>Api Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        @endif
                    </div>

                    <!-- System Section -->

                    @if (auth()->user()->user_role?->value == 1)
                        <div>
                            <h2 class="text-base font-bold text-gray-600">System</h2>

                            <!-- Business Hours -->
                            <a href="#business_hours"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa-solid fa-clock"></i>
                                    <span>Business Hours</span>
                                </div>
                                <span>
                                    <i class="text-lg fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Special Business Hours -->
                            <a href="#special_business_hours"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa-solid fa-clock"></i>
                                    <span>Special Business Hours</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Auto Dispatch Settings -->
                            <a href="#auto_dispatch"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-square-check"></i>
                                    <span>Auto Dispatch Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Dispatcher Page Settings -->
                            <a href="#dispatcher"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-user"></i>

                                    <span>Dispatcher Page Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Dashboard Page Settings -->
                            <a href="#dashboard"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-house"></i>

                                    <span>Dashboard Page Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        </div>
                    @endif


                    @if (auth()->user()->user_role?->value == 2 ||
                            auth()->user()->user_role?->value == 5 ||
                            auth()->user()->user_role?->value == 4)
                        <div>
                            <h2 class="text-base font-bold text-gray-600">System</h2>




                            <!-- Dispatcher Page Settings -->
                            <a href="#dispatcher"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-user"></i>

                                    <span>Dispatcher Page Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Dashboard Page Settings -->
                            <a href="#dashboard"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-house"></i>

                                    <span>Dashboard Page Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        </div>
                    @endif


                    @if (auth()->user()->user_role?->value == 1)
                        <!-- Service Section -->
                        <div>
                            <h2 class="text-base font-bold text-gray-600">Service</h2>

                            <!-- Service -->
                            <a href="#service"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg fa-solid fa-clock"></i>
                                    <span>Service</span>
                                </div>
                                <span>
                                    <i class="text-lg fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!--  ETA Settings -->
                            <a href="#eta"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-xl nav-icon fas fa-stopwatch"></i>
                                    <span>ETA Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Customer Messages -->
                            <a href="#customer_messages"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-xl nav-icon fas fa-user"></i>
                                    <span>Customer Messages</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Dropoff OTP Messages -->
                            <a href="#dropoff_messages"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-xl nav-icon fas fa-message"></i>

                                    <span>Dropoff OTP Messages</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Announcements -->
                            <a href="#announcements"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-xl nav-icon fas fa-microphone"></i>

                                    <span>Announcements</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Taxes -->
                            <a href="#taxes"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-xl nav-icon fas fa-dollar-sign"></i>
                                    <span>Taxes</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        </div>

                        <!-- Integrations Section -->
                        <div>
                            <h2 class="text-base font-bold text-gray-600">Integrations</h2>

                            <!-- Service -->
                            <a href="#dispatching"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-door-open"></i>
                                    <span>Dispatching Settings</span>
                                </div>
                                <span>
                                    <i class="text-lg fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Payment Gateway Settings -->
                            <a href="#payment_gateway"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-users"></i>
                                    <span>Payment Gateway Settings</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Vehicles -->
                            <a href="#vehicles"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-truck"></i>
                                    <span>Vehicles</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                            <!-- Foodics Connection -->
                            <a href="#foodics_connection"
                                class="flex items-center justify-between px-3 py-3 mt-3 text-sm border rounded-md border-gray1 text-black1 bg-gray4">
                                <div class="flex items-center gap-2">
                                    <i class="text-lg nav-icon fas fa-user"></i>
                                    <span>Foodics Connection</span>
                                </div>
                                <span>
                                    <i class="text-xl fa fa-angle-right text-mainColor"></i>
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3 md:ml-6 lg:w-3/4 md:w-2/3 ">
                <!-- Content Section -->
                @include('admin.pages.settings.account')
                <!-- Privacy and Security -->
                @include('admin.pages.settings.privacy')
                <!-- Api Settings -->
                @include('admin.pages.settings.api')
                <!-- Business Hours -->
                @include('admin.pages.settings.working_hourse')
                <!-- Payment Gateway Settings -->
                @include('admin.pages.settings.payment_gateway')
                <!-- Taxes -->
                @include('admin.pages.settings.taxes')
                <!-- Announcements -->
                @include('admin.pages.settings.announcements')
                <!-- Foodics Connection -->
                @include('admin.pages.settings.foodics_connection')
                <!-- Dispatching Settings -->
                @include('admin.pages.settings.dispatching')
                <!-- Dropoff OTP messages -->
                @include('admin.pages.settings.dropoff_messages')
                <!-- Customer Messages -->
                @include('admin.pages.settings.customer_messages')
                <!-- Eta Settings -->
                @include('admin.pages.settings.eta')
                <!-- Service -->
                @include('admin.pages.settings.service')
                <!-- Vehicle Types -->
                @include('admin.pages.settings.vehicles')
                <!-- Auto Dispatch Settings -->
                @include('admin.pages.settings.auto_dispatch')
                <!-- Dashboard Page Settings -->
                @include('admin.pages.settings.dashboard')
                <!-- Dispatcher Page Settings -->
                @include('admin.pages.settings.dispatcher')
                <!-- Special Business Hours -->
                <div class="w-full p-6 bg-white border rounded border-gray1 settings_content"
                    data-system="special_business_hours" id="special_business_hours">
                    <h2 class="mb-6 text-base font-medium">Special Business Hours</h2>
                    <form id="special-hours-form" method="post">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

                            <div>
                                <input type="datetime-local" placeholder="Start" name="special_start_time"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                    <div style="color: red" id="special_start_time_error"></div>
                            </div>

                            <div>
                                <input type="datetime-local" placeholder="End" name="special_end_time"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                    <div  style="color: red" id="special_end_time_error"></div>
                            </div>

                            <div>
                                <label>Clients</label>
                                <select type="text" placeholder="End"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3 select2 days"
                                    multiple="multiple" name="clients[]" style="width: 100%;">
                                    @foreach ($clients as $client)
                                        <option @if (in_array($client->id, $special_clients)) selected @endif
                                            value="{{ $client->id }}">
                                            {{ $client->full_name }} </option>
                                    @endforeach
                                </select>
                                @error('clients')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>



                        <!-- Action Buttons -->
                        <div class="flex flex-col justify-center gap-3 mt-6 md:flex-row">

                            <button id="save-special-business-hours" type="button"
                                class="px-16 py-3 mr-4 font-bold text-white rounded-md border-gray1 bg-blue1">
                                Save
                            </button>
                        </div>

                        <div class="mt-5">
                            <h3 class="text-lg font-medium">Special Business Hours</h3>
                            <table id="branches-table" class="table mt-3 table-head-fixed text-nowrap">
                                <thead>
                                    <td>Start date</td>
                                    <td>Start time</td>
                                    <td>End date</td>
                                    <td>End time</td>
                                    <td>Client</td>
                                </thead>
                                <tbody  >

                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        document.querySelectorAll('.switch-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                console.log(454);
                updateCheckboxValues();
            });
        });
        $('.days').select2({
            placeholder: "Clients",
            allowClear: true
        });
    });







    function updateCheckboxValues() {
        $('input[type="checkbox"]').each(function() {
            $(this).val(this.checked ? 1 : 0);
        });
    }
</script>


<script>
    $(document).ready(function() {

        $('.select2').select2({
            allowClear: true
        });

        $('#save-special-business-hours').click(function() {
            console.log('clicked');

            $('#special_end_time_error').text('');
            $('#special_start_time_error').text('');


            var formData = $('#special-hours-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-special-business-hours') }}',
                data: formData + "&_token={{ csrf_token() }}",  // Add CSRF token manually

                success: function(response) {

                    console.log(response);






                    initializeHistoriesDataTable();

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




        initializeHistoriesDataTable();






        // var branchesTable = $('#branches-table').DataTable({
        //     "processing": true,
        //     "serverSide": true,
        //     "ajax": {
        //         "url": "{{ route('get-special-hours') }}",
        //         "type": "GET",
        //     },
        //     "columns": [{
        //             "data": "start_date"
        //         },
        //         {
        //             "data": "start_time"
        //         },
        //         {
        //             "data": "end_date"
        //         },
        //         {
        //             "data": "end_time"
        //         },
        //         {
        //             "data": "client"
        //         },
        //     ],
        //     "pageLength": 3,
        //     "lengthChange": false
        // });


        function initializeHistoriesDataTable() {
            console.log(44);

            if ($.fn.DataTable.isDataTable('#branches-table')) {
                $('#branches-table').DataTable().destroy();
            }




            $('#branches-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-special-hours') }}",
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
                        "data": "start_date"
                    },
                    {
                        "data": "start_time"
                    },
                    {
                        "data": "end_date"
                    },
                    {
                        "data": "end_time"
                    },
                    {
                        "data": "client"
                    },
                ],
                "pageLength": 6,
                "lengthChange": false
            });
        }





    });
    $(function() {
        $('#reservation').daterangepicker({
            opens: 'left'
        });
    });
</script>

@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/clients.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/viewClients.css') }}" />


@section('title')
    Show Clients
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 gap-192 ">

        <!-- Header -->
        <div class="flex flex-col items-center justify-center pb-192 border-b md:flex-row md:justify-between">

            <div>
                <p id="tabDescription" class="text-black fs-192 fw-bold">
                    <a href="{{ route('clientupdated') }}">Clients</a>
                    <span class="fs-118 gray-94">»</span>

                    <span class="">{{ $client['full_name'] }}</span>
                </p>
                <div class="text-slide-wrapper w-75 bg-transparent">
                    <p class="text-slide fs-112 fw-bold mt-96px">Token: <span
                            id="integration_token">{{ $client['integration_token'] }}</span>

                    </p>
                </div>
            </div>


            <a href="{{ route('clientupdated.edit', ['id' => $client['id']]) }}"

                class="bg-white px-4 py-2 text-black br-96 fw-bold  fs-112 border">
                Edit
            </a>

        </div>

        <!-- Client Status -->
        <div class="bg-white br-96 d-flex align-items-center py-96px px-192  justify-content-between">
            <p class="sectionTitle">Client Status</p>
            <div class=" p-0 form-switch d-flex justify-content-between align-items-center">
                <input class="form-check-input position-relative client-active-toggle"  type="checkbox" role="switch"
                    id="client_active_input" name="client_active_input" data-client_id="{{$client['id']}}"

                    {{ old('client_active_input', $client['is_active']?? false) ? 'checked' : '' }}>

            </div>
        </div>

        <div class="p-4 bg-white br-96 d-flex flex-column gap-4">
            <p class="sectionTitle">Information</p>
            <!-- information cards -->
            <div class="d-flex flex-md-nowrap flex-wrap justify-content-center align-items-center gap-3">

                <!--Card 1-->
                <div class="informationDetailsCard">
                    <div>
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.03906 15.52V11.22" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.62183 1.25C5.23501 1.25 4.10114 1.51137 3.27545 2.27405C2.45963 3.02762 2.08656 4.14206 1.89894 5.49711C1.8977 5.50605 1.89662 5.51502 1.89571 5.524L1.61568 8.274L1.61551 8.27572C1.5132 9.30391 1.77678 10.2563 2.29881 11.0157C2.28047 11.0806 2.27066 11.1492 2.27066 11.22V15.71C2.27066 18.0284 2.72814 19.8478 3.94752 21.0687C5.16709 22.2898 6.98679 22.75 9.3107 22.75H9.3306H14.7006H14.7206C17.0445 22.75 18.8642 22.2898 20.0837 21.0687C21.3031 19.8478 21.7606 18.0284 21.7606 15.71V11.22C21.7606 11.136 21.7468 11.0552 21.7213 10.9798C22.2253 10.226 22.4773 9.2871 22.3766 8.27572L22.0964 5.524C22.0955 5.51502 22.0944 5.50605 22.0932 5.49711C21.9056 4.14206 21.5325 3.02762 20.7167 2.27405C19.891 1.51137 18.7571 1.25 17.3703 1.25H14.3599H14.3203H9.68996C9.68692 1.25 9.68389 1.25002 9.68086 1.25005C9.67785 1.25002 9.67484 1.25 9.67182 1.25H6.62183ZM8.89702 11.1255C8.13861 12.1076 6.94812 12.75 5.67182 12.75C4.97762 12.75 4.33299 12.5893 3.77066 12.3035V15.71C3.77066 17.8815 4.20819 19.2071 5.00883 20.0087C5.58796 20.5886 6.44185 20.9791 7.69392 21.1508C7.74076 21.1485 7.78849 21.1507 7.83663 21.1576C8.28226 21.2212 8.77534 21.25 9.3306 21.25H14.7006C15.2559 21.25 15.749 21.2212 16.1946 21.1576C16.2428 21.1507 16.2905 21.1485 16.3373 21.1508C17.5894 20.9791 18.4433 20.5886 19.0224 20.0087C19.8231 19.2071 20.2606 17.8815 20.2606 15.71V12.2877C19.6924 12.5833 19.0377 12.75 18.3303 12.75C17.0654 12.75 15.8935 12.1208 15.1336 11.1592C14.4645 12.1247 13.3441 12.75 12.03 12.75C10.7021 12.75 9.56658 12.1107 8.89702 11.1255ZM3.10814 8.42424L3.3866 5.68964C3.55932 4.45304 3.86558 3.77094 4.29324 3.37592C4.71253 2.98862 5.38866 2.75 6.62183 2.75H8.84308L8.2255 8.9357L8.22531 8.93762C8.10285 10.2005 6.93843 11.25 5.67182 11.25C4.09408 11.25 2.95268 9.99091 3.10814 8.42424ZM14.274 9.08454L13.6415 2.75H10.3685L9.76638 8.75347C9.63034 10.1404 10.6417 11.25 12.03 11.25C13.2627 11.25 14.1946 10.374 14.2883 9.20089C14.2833 9.16451 14.2787 9.12799 14.2748 9.09135L14.274 9.08454ZM18.3303 11.25C17.1318 11.25 16.0223 10.3061 15.7946 9.11926C15.7993 8.95114 15.7934 8.78004 15.7764 8.60664L15.1889 2.75H17.3703C18.6035 2.75 19.2796 2.98862 19.6989 3.37592C20.1266 3.77094 20.4328 4.45304 20.6055 5.68964L20.8841 8.42501C21.0399 9.99477 19.905 11.25 18.3303 11.25Z"
                                fill="#585858"></path>
                        </svg>
                    </div>

                    <div class="text-slide-wrapper bg-transparent">

                        <p id="client_name2" class="text-slide clientName">{{ $client['full_name'] }} </p>
                    </div>
                    <p id="client_id">{{ $client['id'] }}</p>


                </div>

                <!--Card 2-->
                <div class="informationDetailsCard">
                    <div>
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M18.0168 2.75C17.3176 2.75174 16.75 3.32011 16.75 4.01V10.25H19.42C20.0494 10.25 20.4903 10.054 20.7722 9.77217C21.054 9.4903 21.25 9.04943 21.25 8.42V6C21.25 5.10967 20.8867 4.29651 20.2979 3.69852C19.7158 3.11742 18.9052 2.75912 18.0168 2.75ZM15.25 4.01C15.25 2.48095 16.5006 1.25 18.02 1.25H18.0269L18.0269 1.25003C19.3161 1.26186 20.5033 1.78264 21.3603 2.63967L21.364 2.64333L21.364 2.64334C22.214 3.50518 22.75 4.69125 22.75 6V8.42C22.75 9.37056 22.446 10.2197 21.8328 10.8328C21.2197 11.446 20.3706 11.75 19.42 11.75H16C15.5858 11.75 15.25 11.4142 15.25 11V4.01Z"
                                fill="#1A1A1A"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.30736 2.68106C3.11215 1.74912 4.34646 1.25 6 1.25H18C18.4142 1.25 18.75 1.58579 18.75 2C18.75 2.41421 18.4142 2.75 18 2.75C17.3142 2.75 16.75 3.31421 16.75 4V21C16.75 22.4418 15.1035 23.2564 13.9529 22.4022L13.9506 22.4004L12.2327 21.1145C12.1465 21.0482 12.0086 21.0521 11.9203 21.1403L10.2403 22.8203C9.55744 23.5032 8.44256 23.5032 7.75967 22.8203L7.75808 22.8187L6.09967 21.1503C6.09948 21.1501 6.09986 21.1505 6.09967 21.1503C6.00044 21.0516 5.85765 21.0468 5.76 21.12L4.05289 22.3978C4.05221 22.3983 4.05153 22.3989 4.05084 22.3994C2.89266 23.2743 1.25 22.4434 1.25 21V6C1.25 4.79713 1.52033 3.59244 2.30736 2.68106ZM15.5517 2.75H6C4.65354 2.75 3.88785 3.14588 3.44264 3.66144C2.97967 4.19756 2.75 4.99287 2.75 6V21C2.75 21.2157 2.9854 21.3247 3.14711 21.2022L3.15056 21.1996L4.86 19.92C5.56217 19.3934 6.53959 19.4689 7.16033 20.0897L7.16192 20.0913L8.82033 21.7597C8.82051 21.7598 8.82015 21.7595 8.82033 21.7597C8.91751 21.8563 9.08274 21.8566 9.17967 21.7597L10.8597 20.0797C11.4701 19.4693 12.4493 19.3922 13.1428 19.9221L14.8471 21.1978C14.8473 21.198 14.8476 21.1982 14.8479 21.1984C15.0173 21.3232 15.25 21.1979 15.25 21V4C15.25 3.55044 15.3589 3.12542 15.5517 2.75Z"
                                fill="#1A1A1A"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.25 13.01C8.25 12.5958 8.58579 12.26 9 12.26H12C12.4142 12.26 12.75 12.5958 12.75 13.01C12.75 13.4242 12.4142 13.76 12 13.76H9C8.58579 13.76 8.25 13.4242 8.25 13.01Z"
                                fill="#1A1A1A"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.25 9.01001C8.25 8.5958 8.58579 8.26001 9 8.26001H12C12.4142 8.26001 12.75 8.5958 12.75 9.01001C12.75 9.42422 12.4142 9.76001 12 9.76001H9C8.58579 9.76001 8.25 9.42422 8.25 9.01001Z"
                                fill="#1A1A1A"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.99561 13C4.99561 12.4477 5.44332 12 5.99561 12H6.00459C6.55687 12 7.00459 12.4477 7.00459 13C7.00459 13.5523 6.55687 14 6.00459 14H5.99561C5.44332 14 4.99561 13.5523 4.99561 13Z"
                                fill="#1A1A1A"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.99561 9C4.99561 8.44772 5.44332 8 5.99561 8H6.00459C6.55687 8 7.00459 8.44772 7.00459 9C7.00459 9.55228 6.55687 10 6.00459 10H5.99561C5.44332 10 4.99561 9.55228 4.99561 9Z"
                                fill="#1A1A1A"></path>
                        </svg>
                    </div>


                    <p class="text-black fs-112 fw-bold"><span id="total_orders_count">{{ $client['total_orders'] }}</span>

                        Order</p>
                    <p>Total order</p>

                </div>

                <!--Card 3-->
                <div class="informationDetailsCard" data-bs-toggle="modal" data-bs-target="#rechargeModal">
                    <div>
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12ZM12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM12 5.25C12.4142 5.25 12.75 5.58579 12.75 6V6.58984H13.1219C14.8 6.58984 16.0919 8.00043 16.0919 9.66984C16.0919 10.0841 15.7561 10.4198 15.3419 10.4198C14.9277 10.4198 14.5919 10.0841 14.5919 9.66984C14.5919 8.75926 13.9037 8.08984 13.1219 8.08984H12.75V11.464L14.2724 11.9929C14.7216 12.1521 15.2086 12.3907 15.5684 12.8496C15.9351 13.3174 16.0919 13.9125 16.0919 14.6298C16.0919 16.1317 14.9177 17.4098 13.4019 17.4098H12.75V18C12.75 18.4142 12.4142 18.75 12 18.75C11.5858 18.75 11.25 18.4142 11.25 18V17.4098H10.8919C9.2137 17.4098 7.92188 15.9993 7.92188 14.3298C7.92188 13.9156 8.25766 13.5798 8.67188 13.5798C9.08609 13.5798 9.42188 13.9156 9.42188 14.3298C9.42188 15.2404 10.11 15.9098 10.8919 15.9098H11.25V12.5309L9.74132 12.0068C9.29218 11.8476 8.80512 11.609 8.44537 11.15C8.07866 10.6822 7.92188 10.0872 7.92188 9.36984C7.92188 7.86796 9.09606 6.58984 10.6119 6.58984H11.25V6C11.25 5.58579 11.5858 5.25 12 5.25ZM10.6119 8.08984H11.25V10.9429L10.2403 10.5922C9.90085 10.4716 9.72842 10.3554 9.62588 10.2246C9.53009 10.1024 9.42188 9.87252 9.42188 9.36984C9.42188 8.63173 9.98769 8.08984 10.6119 8.08984ZM12.75 15.9098V13.052L13.7735 13.4075C14.1129 13.5281 14.2853 13.6442 14.3879 13.775C14.4837 13.8972 14.5919 14.1272 14.5919 14.6298C14.5919 15.368 14.0261 15.9098 13.4019 15.9098H12.75Z"
                                fill="#1A1A1A"></path>
                        </svg>
                    </div>

                    <p class="totalBalance text-black fs-112 fw-bold"><span

                            id="total_balance_client">{{ $client['total_balance'] }}</span> SAR</p>

                    <p>Total balance</p>

                </div>
            </div>

            <!-- information fields -->

            <div class="informationFields">
                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Account Number <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field" id="account_number" value="{{ $client['account_number'] }}"

                        disabled="">

                </div>
                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        client country <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field" id="client_country" value="{{ $client['country'] }}"

                        disabled="">

                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        City <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field" id="client_city"value="{{ $client['city'] }}"

                        disabled="">

                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Currency <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field" id="client_currency" value="{{ $client['currency'] }}"

                        disabled="">

                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Parial Pay <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" value="{{ $client['client_partial_pay'] }}" class="input-field"

                        id="client_parial_pay" disabled="">

                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        def preparation time <span class="text-danger d-none">*</span>
                    </label>
                    <input type="text" class="input-field" id="client_defualt_preperation_time"

                        value="{{ $client['client_default_preperation_time'] }}" disabled="">


                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        min preperation time <span class="text-danger d-none">*</span>
                    </label>
                    <input type="text" class="input-field" id="client_min_preperation_time"

                        value="{{ $client['client_min_preperation_time'] }}" disabled="">


                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Client Groups <span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field" value="{{ $client['client_client_group'] }}"

                        id="client_client_group" disabled="">

                </div>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Operators Group <span class="text-danger d-none">*</span>
                    </label>
                    <input type="text" class="input-field" id="client_operator_group"

                        value="{{ $client['client_operator_group'] }}" disabled="">


                </div>
                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Price Of Order<span class="text-danger d-none">*</span>
                    </label>

                    <input type="text" class="input-field " value="{{ $client['price_order'] }}"

                        id="price_order_group" disabled="">

                </div>


            </div>


            <!--Client Orders -->
            <div class="sectionGlobalForm clientOrders p-0 ">
                <div class="d-flex justify-content-between align-items-center position-absolute">
                    <p class="sectionTitle">Orders</p>
                    <div class=" searchBox flex items-center justify-between gap-3 p-2 bg-white  rounded-md">
                        <!-- Input -->
                        <input type="text" placeholder="Search here..." class=" bg-transparent outline-none border-0"
                            id="order_search">
                        <!-- Icon -->
                        <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                fill="#A30133"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                fill="#A30133"></path>
                        </svg>
                    </div>
                </div>

                <!-- Orders Table -->

                <div class="scrollable-table-custom border-0">
                    <table id="orders-table" class="table">
                        <thead>



                            <th>ID</th>
                            <th>Order time</th>
                            <th>Branch</th>
                            <th>Customer name</th>
                            <th>Customer area</th>
                            <th>Status</th>

                        </thead>
                        <tbody>



                        </tbody>
                    </table>

                </div>




            </div>

            <!--Client Branches -->
            <div class="sectionGlobalForm clientOrders branches p-0">
                <div class="d-flex justify-content-between align-items-center position-absolute">
                    <p class="sectionTitle d-flex align-items-center">
                        Branches <button id="create-client-branch" class="addBtn" data-bs-toggle="modal"
                            data-bs-target="#addNewBranchModal">+ Add New Branch</button> |
                        <button id="upload-client-branch" class="addBtn d-flex uploadBranchesBtn align-items-center"
                            data-bs-toggle="modal" data-bs-target="#uploadBranchModal"> <svg
                                xmlns="http://www.w3.org/2000/svg" fill="#a30133" width="12.8px" height="12.8px"
                                viewBox="0 0 24 24">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"></path>
                                </g>
                            </svg>
                            Upload Branches</button>

                    </p>

                </div>

                <!-- Orders Table -->

                <div class="scrollable-table-custom border-0">
                    {{-- <div class="clientBranchCards">
                <div class="clientBranchCard">
                    <div class="clientBranchCardInfo">
                        <div>
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2.75C3.31421 2.75 2.75 3.31421 2.75 4V6C2.75 6.68579 3.31421 7.25 4 7.25H7C7.68579 7.25 8.25 6.68579 8.25 6V5V4C8.25 3.31421 7.68579 2.75 7 2.75H4ZM9.75 4V4.25H12.5H15.25V4.20001C15.25 3.12581 16.1258 2.25 17.2 2.25H20.8C21.8742 2.25 22.75 3.12581 22.75 4.20001V5.79999C22.75 6.87419 21.8742 7.75 20.8 7.75H17.2C16.1258 7.75 15.25 6.87419 15.25 5.79999V5.75H13.25V11.75H15.25V11.7C15.25 10.6258 16.1258 9.75 17.2 9.75H20.8C21.8742 9.75 22.75 10.6258 22.75 11.7V13.3C22.75 14.3742 21.8742 15.25 20.8 15.25H17.2C16.1258 15.25 15.25 14.3742 15.25 13.3V13.25H13.25V18C13.25 18.6858 13.8142 19.25 14.5 19.25H15.25V19.2C15.25 18.1258 16.1258 17.25 17.2 17.25H20.8C21.8742 17.25 22.75 18.1258 22.75 19.2V20.8C22.75 21.8742 21.8742 22.75 20.8 22.75H17.2C16.1258 22.75 15.25 21.8742 15.25 20.8V20.75H14.5C12.9858 20.75 11.75 19.5142 11.75 18V12.5V5.75H9.75V6C9.75 7.51421 8.51421 8.75 7 8.75H4C2.48579 8.75 1.25 7.51421 1.25 6V4C1.25 2.48579 2.48579 1.25 4 1.25H7C8.51421 1.25 9.75 2.48579 9.75 4ZM16.75 20V20.8C16.75 21.0458 16.9542 21.25 17.2 21.25H20.8C21.0458 21.25 21.25 21.0458 21.25 20.8V19.2C21.25 18.9542 21.0458 18.75 20.8 18.75H17.2C16.9542 18.75 16.75 18.9542 16.75 19.2V20ZM16.75 13.3V12.5V11.7C16.75 11.4542 16.9542 11.25 17.2 11.25H20.8C21.0458 11.25 21.25 11.4542 21.25 11.7V13.3C21.25 13.5458 21.0458 13.75 20.8 13.75H17.2C16.9542 13.75 16.75 13.5458 16.75 13.3ZM16.75 5.79999V5V4.20001C16.75 3.95421 16.9542 3.75 17.2 3.75H20.8C21.0458 3.75 21.25 3.95421 21.25 4.20001V5.79999C21.25 6.04579 21.0458 6.25 20.8 6.25H17.2C16.9542 6.25 16.75 6.04579 16.75 5.79999Z" fill="#585858"></path></svg>
                        </div>
                        <p class="branchName">Rabie RDH [41716]</p>
                        <p>Riyadh, Kingdom City</p>
                    </div>
                    <div class="clientBranchCardActions">

                            <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                <label class="form-check-label" for="c">Auto Dispatch</label>
                                <input class="form-check-input position-relative" type="checkbox" role="switch" id="c" name="c">
                            </div>


                            <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                <label class="form-check-label" for="c">Status</label>
                                <input class="form-check-input position-relative" type="checkbox" role="switch" id="c" name="c">
                            </div>

                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>


                    </div>
                </div>
            </div> --}}


                    <table id="branches-table" class="table">
                        <thead>




                        </thead>
                        <tbody>



                        </tbody>
                    </table>


                </div>




            </div>

            <!--Client Users -->
            <div class="sectionGlobalForm clientOrders users p-0 ">
                <div class="d-flex justify-content-between align-items-center position-absolute">
                    <p class="sectionTitle">
                        Users <button id="create-client-user" class="addBtn" data-bs-toggle="modal"
                            data-bs-target="#addNewUserModal">+ Add New User</button>
                    </p>
                    <div class=" searchBox flex items-center justify-between gap-3 p-2 bg-white  rounded-md">
                        <!-- Input -->
                        <input type="text" placeholder="Search here..." class=" bg-transparent outline-none border-0"
                            id="order_search">
                        <!-- Icon -->
                        <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                fill="#A30133"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                fill="#A30133"></path>
                        </svg>
                    </div>
                </div>

                <!-- Orders Table -->

                <div class="scrollable-table-custom border-0">

                    <table id="users-table" class="table">
                        <thead>


                        </thead>
                        <tbody>



                        </tbody>
                    </table>

                    {{-- <div class="clientBranchCards">
                <div class="clientBranchCard">
                    <div class="clientBranchCardInfo">
                        <div>
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.1197 12.7796C12.0497 12.7696 11.9597 12.7696 11.8797 12.7796C10.1197 12.7196 8.71973 11.2796 8.71973 9.50955C8.71973 7.69955 10.1797 6.22955 11.9997 6.22955C13.8097 6.22955 15.2797 7.69955 15.2797 9.50955C15.2697 11.2796 13.8797 12.7196 12.1197 12.7796Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18.7398 19.3799C16.9598 21.0099 14.5998 21.9999 11.9998 21.9999C9.39976 21.9999 7.03977 21.0099 5.25977 19.3799C5.35977 18.4399 5.95977 17.5199 7.02977 16.7999C9.76977 14.9799 14.2498 14.9799 16.9698 16.7999C18.0398 17.5199 18.6398 18.4399 18.7398 19.3799Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 22.0005C17.5228 22.0005 22 17.5233 22 12.0005C22 6.47764 17.5228 2.00049 12 2.00049C6.47715 2.00049 2 6.47764 2 12.0005C2 17.5233 6.47715 22.0005 12 22.0005Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </div>
                        <p class="branchName">Baraka App</p>
                        <p>admin@barakaapp.com, Client Template</p>
                    </div>
                    <div class="clientBranchCardActions">

                            <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                <label class="form-check-label" for="c">Status</label>
                                <input class="form-check-input position-relative" type="checkbox" role="switch" id="c" name="c">
                            </div>

                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>


                    </div>
                </div>
            </div> --}}

                </div>



            </div>


        </div>





    </div>
@endsection
<script type="text/javascript" src="{{ asset('new/src/js/textSlide.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">


@include('admin.pages.clientsUpdated.viewScripts')

@include('admin.pages.clientsUpdated.branches')

@include('admin.pages.clientsUpdated.uploadBranches')


@include('admin.pages.clientsUpdated.users')

@include('admin.pages.clientsUpdated.mapHandler')

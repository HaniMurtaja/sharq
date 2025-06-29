@extends('admin.layouts.app')

@section('title')
    Maps
@endsection
@section('content')
    <link rel="stylesheet" href="{{ asset('new/src/css/index.css') }}" />


    <link rel="icon" type="image/jpg" href="{{ asset('new/src/assets/images/favicon (2).jpg') }}">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" />
    <link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
    <link rel="stylesheet" href="{{ asset('new/src/css/index.css') }}" />


    @if (auth()->user()->user_role == \App\Enum\UserRole::CLIENT)
        <style>
            .searchBTN:disabled {
                background-color: #ddd !important;
            }

            .searchBTN:disabled svg path {
                fill: #fff !important;
            }

            .searchDiv:has(#order_search:disabled) {
                background-color: #ddd !important;
            }

            #jobsTabs {
                height: 81% !important;
            }

            .leaflet-fade-anim .leaflet-map-pane .leaflet-popup {
                visibility: hidden;
            }

            #mapContainer {
                position: relative;
            }

            .mainPopup {
                padding: 16px;
                border-radius: 12.8px;
                border-radius: 12.8px !important;
                position: relative !important;
                transform: translate(0%, 0%);
                width: 75% !important;
            }

            .fixedContainer {
                position: absolute;
                z-index: 1000;
                bottom: 2%;
                left: 0;
                background-color: transparent;
                box-shadow: none !important;
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .leaflet-right .leaflet-routing-container.leaflet-routing-container-hide {
                display: none;
            }

            .close-order-popup-map {
                cursor: pointer;
            }


            .mainPopup {
                padding: 0;
            }

            .driverPopup {
                padding: 0 11.2px;
                border-radius: 12.8px !important;
                position: fixed;
                width: 600px !important;
                bottom: 13px;

            }

            .leaflet-marker-icon {
                border-radius: 50% !important;
            }

            @media (max-width: 992px) {
                #map {
                    height: 600px !important;
                    margin: 12.8px 0;

                }

                .fixedContainer {
                    position: relative !important;
                    bottom: unset;
                    left: unset;

                }

                .mainPopup {
                    width: 100% !important;
                    overflow-x: scroll;

                }

            }
        </style>
    @endif
    <style>
        .searchBTN:disabled {
            background-color: #ddd !important;
        }

        .searchBTN:disabled svg path {
            fill: #fff !important;
        }

        .searchDiv:has(#order_search:disabled) {
            background-color: #ddd !important;
        }

        #jobsTabs {
            height: 81% !important;
        }

        #mapContainer {
            position: relative;
        }

        .mainPopup {
            padding: 16px;
            border-radius: 12.8px;
            border-radius: 12.8px !important;
            position: relative !important;
            transform: translate(0%, 0%);
            width: 75% !important;
        }

        .fixedContainer {
            position: absolute;
            z-index: 1000;
            bottom: 2%;
            left: 0;
            background-color: transparent;
            box-shadow: none !important;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .leaflet-right .leaflet-routing-container.leaflet-routing-container-hide {
            display: none;
        }

        .close-order-popup-map {
            cursor: pointer;
        }


        .mainPopup {
            padding: 0;
        }

        .driverPopup {
            padding: 0 11.2px;
            border-radius: 12.8px !important;
            position: fixed;
            width: 600px !important;
            bottom: 13px;


        }

        #selectedOrders,
        #selectedOrdersSearch {
            font-weight: 600;
            padding: 11.2px 7.125px 11.2px 17.125px;
            margin: 6px 0 9px;
            font-size: 9.6px;
            box-shadow: 0 .5rem 1rem -.8rem #949494;
        }

        #selectedOrdersDropdown:after,
        #selectedOrdersSearchDropdown:after {
            display: none;
        }

        .leaflet-marker-icon {
            border-radius: 50% !important;
        }

        @media (max-width: 992px) {
            #map {
                height: 600px !important;
                margin: 12.8px 0;

            }

            .fixedContainer {
                position: relative !important;
                bottom: unset;
                left: unset;

            }

            .mainPopup {
                width: 100% !important;
                overflow-x: scroll;

            }

        }
    </style>
    <div class="w-full h-full">
        <!-- Navbar -->

        <!-- Drawer Overlay -->
        <div id="drawer-overlay" data-drawer="Dispatcher"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay"></div>
        <!-- New Individual -->
        <!-- Drawer -->
        @include('admin.pages.dispatchers.create-order')

        <!-- End Drawer -->
        <div class="flex  flex-col p-2 h-full sideSectionMapContainer">
            <div class="bg-white  rounded-lg h-full">
                <!-- Navigation Tabs -->
                <div class="flex d-none items-center justify-between border-b">
                    <div class="flex  flex-col mb-4">

                        <h3 class="mb-2 text-base font-medium text-black">
                            {{ $auth_name }}
                        </h3>

                    </div>

                    <!-- <a href="#"> -->

                    <!-- </a> -->
                </div>

                <div class="flex flex-col w-full h-full md:flex-row sideSectionContainer">

                    <!-- Sidebar -->
                    <div class="rounded-4 sideSection "
                        style="width:33%; background-color: #f9f9f9; padding: 12.8px 20.8px 20.8px; height: 97vh;">
                        <div class="flex items-center justify-between pb-3 border-bottom">
                            <div class="flex  flex-col">
                                <h3 class="text-base font-bold text-black">
                                    {{ $auth_name }}
                                </h3>
                            </div>

                            <!-- <a href="#"> -->
                            <button
                                class="flex items-center justify-center  gap-3 px-4 py-2 text-white rounded-md open-drawer"
                                style="width: 96px;background-color: #A30133;border-radius: 9.6px; line-height: normal;}"
                                data-drawer="Dispatcher">
                                <span style="font-size: 11.2px; font-weight: 600">+ New</span>
                            </button>
                            <!-- </a> -->
                        </div>
                        <!-- Search -->
                        <div id="on-demond-search-dev" class="flex items-center justify-between gap-2 my-3 ">


                            <!-- Input -->
                            <div
                                class="bg-white rounded-md position-relative d-inline-block w-100 py-1 px-2 border searchDiv">
                                <input type="text" placeholder="Search order here..."
                                    class="bg-transparent outline-none border-0"
                                    style="font-size: 11.2px; color: #585858; width: 100%; padding-right: 25px;"
                                    id="order_search" required />
                                <button type="button" id="clear_search"
                                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
                                           background: none; border: none; cursor: pointer; color: #888; font-size: 12px; display: none;">
                                    ✖
                                </button>
                            </div>

                            <script>
                                const input = document.getElementById('order_search');
                                const clearButton = document.getElementById('clear_search');

                                // input.addEventListener('input', () => {
                                //     clearButton.style.display = input.value ? 'block' : 'none';
                                // });

                                // clearButton.addEventListener('click', () => {
                                //     input.value = '';
                                //     clearButton.style.display = 'none';
                                //     // input.focus(); // Keep focus on input after clearing
                                //     $('#jobsTabs').empty();
                                //     $('#searchOrderBtn').prop('disabled', false);
                                //     $('#order_search').prop('disabled', false);
                                //     $('#jobs-screen').css('display', 'none');
                                //     $('.demondDriver').css('display', 'block');


                                // });
                            </script>
                            <!-- Icon -->
                            <button type="button" class="searchBTN bg-white rounded-md p-2 border" id="searchOrderBtn">
                                <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                        fill="#A30133" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                        fill="#A30133" />
                                </svg>
                            </button>
                        </div>
                        <div id="driver-search-dev" style="display: none"
                            class="flex items-center justify-between gap-2 my-3 ">


                            <!-- Input -->
                            <div class="bg-white rounded-md position-relative d-inline-block w-100 py-1 px-2 border">
                                <input type="text" placeholder="Search driver here..."
                                    class="bg-transparent outline-none border-0"
                                    style="font-size: 11.2px; color: #585858; width: 100%; padding-right: 25px;"
                                    id="driver_search" required />
                                <button type="button" id="clear_search"
                                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
                                           background: none; border: none; cursor: pointer; color: #888; font-size: 12px; display: none;">
                                    ✖
                                </button>
                            </div>
                            <!-- Icon -->

                            <button type="button" class="searchBTN bg-white rounded-md p-2 border" id="searchDriverBtn">
                                <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                        fill="#A30133" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                        fill="#A30133" />
                                </svg>
                            </button>
                        </div>
                        <script>
                            const input = document.getElementById('order_search');
                            const clearButton = document.getElementById('clear_search');

                            input.addEventListener('input', () => {
                                clearButton.style.display = input.value ? 'block' : 'none';
                            });

                            clearButton.addEventListener('click', () => {
                                input.value = '';
                                clearButton.style.display = 'none';
                                // input.focus(); // Keep focus on input after clearing
                                $('#jobs-screen').css('display', 'none');
                                $('.demondDriver').css('display', 'block');
                                $('#jobsTabs').empty();

                            });
                        </script>

                        @include('admin.pages.dispatchers.search-data')


                        <div class="flex flex-col demondDriver">

                            <!-- Tabs -->
                            <div class="grid grid-cols-2 border-top dispatcher-tabs">
                                <button type="button" id="on-demand-btn"
                                    class="p-2 text-sm font-medium text-center border-b dispatch-tab-active focus:outline-none"
                                    style="font-size: 9.6px" data-tab="On Demand">
                                    <span>
                                        <div>
                                            <span> On Demand </span>
                                            ( <span style="color: red; display:inline"
                                                class="All-orders">({{ $order_count }})</span>)
                                        </div>
                                    </span>
                                </button>
                                @if (auth()->user()->user_role->value == 1 || auth()->user()->user_role->value == 4)
                                    <button type="button" id="drivers-btn"
                                        class="p-2 text-sm text-center border-b border-gray1 focus:outline-none"
                                        style="font-size: 9.6px" data-tab="Drivers">
                                        <span>
                                            <div>

                                                Drivers <p style="color: red; display:inline">
                                                    ({{ $order_count }})</p>

                                            </div>
                                        </span>
                                    </button>
                                @endif

                            </div>
                            <!-- Filter -->



                            <!-- On Demand Tab Content -->
                            <div class="dispatcher-tab-content px-2" data-tab="On Demand">
                                <div class="flex justify-between p-2 pt-3">
                                    <h4 class="text-sm text-gray6" style="font-size: 9.6px">Filter:</h4>



                                    <div class="flex items-center justify-center gap-1">
                                        <div class="dropdown">
                                            <button type="button"
                                                class="flex items-center justify-center gap-1 dropdown-toggle"
                                                id="allJobsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <h4 class="text-xs text-gray6 d-flex gap-1" style="font-size: 9.6px">
                                                    All Jobs
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.2801 5.9668L8.93343 10.3135C8.42009 10.8268 7.58009 10.8268 7.06676 10.3135L2.72009 5.9668"
                                                            stroke="#A30133" stroke-miterlimit="10"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>

                                                </h4>

                                            </button>

                                            <ul class="dropdown-menu allJobs " aria-labelledby="allJobsDropdown">
                                                <li><a class="dropdown-item jobs-filter" data-value="1"
                                                        href="#">All jobs</a></li>
                                                <li><a class="dropdown-item jobs-filter" data-value = "0"
                                                        href="#">Delayed Jobs</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <span class="w-[1px] h-[15px] bg-gray7 block"></span>
                                        <button type="button">
                                            <img src="{{ asset('/new/src/assets/icons/eye.svg') }}" alt="" />
                                        </button>
                                    </div>
                                </div>

                                <div id="selectedOrders"
                                    class=" bg-white rounded-lg fs-96 d-none justify-content-between align-items-center px-7125 "
                                    style=font-weight: bold;">
                                    <div class="selectedOrdersCount"></div>
                                    <div class="dropdown">
                                        <button type="button"
                                            class="flex items-center justify-center gap-1 dropdown-toggle show"
                                            id="selectedOrdersDropdown" data-bs-toggle="dropdown" aria-expanded="true">
                                            <svg width="12.8px" height="12.8px" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3.33333 9.33325C2.6 9.33325 2 8.73325 2 7.99992C2 7.26659 2.6 6.66659 3.33333 6.66659C4.06667 6.66659 4.66667 7.26659 4.66667 7.99992C4.66667 8.73325 4.06667 9.33325 3.33333 9.33325Z"
                                                    stroke="#949494" stroke-width="1.2"></path>
                                                <path
                                                    d="M12.6673 9.33325C11.934 9.33325 11.334 8.73325 11.334 7.99992C11.334 7.26659 11.934 6.66659 12.6673 6.66659C13.4007 6.66659 14.0007 7.26659 14.0007 7.99992C14.0007 8.73325 13.4007 9.33325 12.6673 9.33325Z"
                                                    stroke="#949494" stroke-width="1.2"></path>
                                                <path
                                                    d="M7.99935 9.33325C7.26602 9.33325 6.66602 8.73325 6.66602 7.99992C6.66602 7.26659 7.26602 6.66659 7.99935 6.66659C8.73268 6.66659 9.33268 7.26659 9.33268 7.99992C9.33268 8.73325 8.73268 9.33325 7.99935 9.33325Z"
                                                    stroke="#949494" stroke-width="1.2"></path>
                                            </svg>

                                        </button>

                                        <ul class="dropdown-menu selectedOrdersDropdown"
                                            aria-labelledby="selectedOrdersDropdown"
                                            style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(0px, 18.4px, 0px);"
                                            data-popper-placement="bottom-end">
                                            <li><a class="dropdown-item assign-driver-multe-orders" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal" href="#">Assign</a></li>
                                            <li>
                                                <a class="dropdown-item unassign-driver-multi-orders"
                                                    href="#">UnAssign</a>
                                            </li>

                                            <li><a class="dropdown-item complate-driver-mulite-orders"
                                                    href="#">Complete</a></li>
                                            <li><a class="dropdown-item cancel-driver-mulite-orders "
                                                    data-bs-toggle="modal" data-bs-target="#cancelRequestModal"
                                                    href="#">Cancel</a></li>
                                        </ul>
                                    </div>
                                </div>


                                @include('admin.pages.dispatchers.orders')
                            </div>

                            <!-- Driver Tab Content -->
                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher'))
                                <div class="hidden dispatcher-tab-content" data-tab="Drivers">

                                    @include('admin.pages.dispatchers.drivers')

                                </div>
                            @endif

                        </div>



                    </div>
                    <!-- Content Section mt-3 -->
                    <div class="bg-white border rounded-4 md:ml-6 md:w-3/4 border-gray1 md:mt-0 mapContainer"
                        id="mapContainer">
                        @if (auth()->user()->user_role !== \App\Enum\UserRole::DISPATCHER)
                            <div id="map" style="width:auto; height:100%;" class="rounded-4"></div>
                        @endif
                        <!-- </div> -->
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="toastCreateOrder"
            style="display: none; background: rgba(244, 205, 237, 0.8); width: 350px; height: 100px; border-radius: 10px; color: rgb(10, 9, 9);"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-header">

                <strong class="me-auto">Order</strong>
                <small class="text-muted">Just now</small>
                <button type="button" class="btn-close" aria-label="Close" onclick="hideToast()">X</button>
            </div>
            <div class="toast-body">
                Order saved successfully
            </div>
        </div>
    </div>


    @include('admin.pages.geoapify.script')
    @include('livewire.assign-worker')
    @include('livewire.order-history')
    @include('livewire.order-details')

    @include('admin.pages.geoapify.helper-script-update')



    @include('admin.pages.dispatchers.cancel-reasons')



    @include('admin.pages.dispatchers.operators-acceptance-rate-popup')


    @include('admin.pages.dispatchers.popupScript')
    @include('admin.pages.dispatchers.orderSummaryPopup')
@endsection

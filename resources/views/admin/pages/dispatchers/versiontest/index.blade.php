@extends('admin.layouts.app')



{{-- <link rel="stylesheet" href="//alshrouqdelivery.b-cdn.net/public/new/src/css/index.css" /> --}}

<link rel="stylesheet" href="{{ asset('new/src/css/index.css')}}" />


@section('content')
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
                         style="width:33%; background-color: #f9f9f9; padding: 12.8px 20.8px 20.8px;">
                        <div class="flex items-center justify-between pb-3 border-bottom">
                            <div class="flex  flex-col">
                                <h3 class="text-base font-bold text-black">
                                    {{ $auth_name }}
                                </h3>
                            </div>

                            <!-- <a href="#"> -->
                            <button
                                class="flex items-center justify-center  gap-3 px-4 py-2 text-white rounded-md open-drawer"
                                style="width: 96px;background-color: #f46624;border-radius: 9.6px; line-height: normal;}"
                                data-drawer="Dispatcher">
                                <span style="font-size: 11.2px; font-weight: 600">+ New</span>
                            </button>
                            <!-- </a> -->
                        </div>
                        <!-- Search -->
                        <div id="on-demond-search-dev"
                             class="flex items-center justify-between gap-3 p-2 my-3 bg-white  rounded-md">


                            <!-- Input -->
                            <input type="text" placeholder="Search order here..."
                                   class=" bg-transparent outline-none border-0"
                                   style="font-size: 11.2px; color: #585858;width: 100%" id="order_search" />
                            <!-- Icon -->
                            <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                      fill="#A30133" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                      fill="#A30133" />
                            </svg>
                        </div>
                        <div id="driver-search-dev" style="display: none"
                             class="flex items-center justify-between gap-3 p-2 my-3 bg-white  rounded-md">


                            <!-- Input -->
                            <input type="text" placeholder="Search driver here..."
                                   class=" bg-transparent outline-none border-0"
                                   style="font-size: 11.2px; color: #585858;width: 100%" id="driver_search" />
                            <!-- Icon -->
                            <svg width="16" height="16" viewBox="0 0 22 23" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                                      fill="#A30133" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                                      fill="#A30133" />
                            </svg>
                        </div>

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
                                           ( <span style="color: red; display:inline" class="All-orders">({{ $order_count }})</span>)
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
                                        <button type="button" class="flex items-center justify-center gap-1">
                                            <h4 class="text-xs text-gray6" style="font-size: 9.6px">All Jobs</h4>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13.2801 5.9668L8.93343 10.3135C8.42009 10.8268 7.58009 10.8268 7.06676 10.3135L2.72009 5.9668"
                                                    stroke="#A30133" stroke-miterlimit="10" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <span class="w-[1px] h-[15px] bg-gray7 block"></span>
                                        <button type="button">
                                            <img src="{{ asset('/new/src/assets/icons/eye.svg') }}" alt="" />
                                        </button>
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
                    <div class="bg-white border rounded-4 md:ml-6 md:w-3/4 border-gray1 md:mt-0 mapContainer" id="mapContainer">
                        <!-- <div style="width: 100%"> -->

                        {{-- @livewire('dispatcher') --}}

                        <div  id="map" style="width:auto; height:100%;" class="rounded-4"></div>

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
@endsection

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>
@include('livewire.assign-worker')
@include('livewire.order-history')
@include('livewire.order-details')
@include('admin.pages.dispatchers.versiontest.scripts')

@include('admin.pages.dispatchers.pusher')

@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />

<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/orders.css') }}" />


@section('title')
    Show Orders
@endsection
@section('content')
    @include('admin.pages.orderdashboard.view-order-modal')

    @include('admin.pages.orderdashboard.edit-order-modal')

    @include('admin.pages.orderdashboard.unifonic-response-modal')


    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">

        <!-- Table -->
        <div class="">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-192 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-192">
                    <h3 class="text-black fs-192 fw-bold m-0 ">Orders</h3>
                </div>

            </div>

            <div class="row d-flex gap-3 px-192 mb-176">
                <div class="col-lg-2 col-12 flex  br-64 gap-128 align-items-center  p-128 bg-white ">
                    <div class="flex items-center  border-ce p-2 rounded-5">
                        <img src="{{ asset('new/src/assets/icons/orderCount2.svg') }}" alt="" width="19.2px"
                            height="19.2px" />
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">
                            {{ $items->total() }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Order Count
                        </h4>

                    </div>


                </div>

                <div class="col-lg-2 col-12 flex br-64 gap-128 align-items-center p-128 bg-white ">
                    <div class="flex items-center justify-center border-ce p-2 rounded-5">
                        <img src="{{ asset('new/src/assets/icons/totalOrders.svg') }}" alt="" width="19.2px"
                            height="19.2px" />
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">

                            {{ $sumvalue }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Order value
                        </h4>

                    </div>


                </div>

                <div class="col-lg-2 col-12 flex  br-64 gap-128 align-items-center  p-128 bg-white ">
                    <div class="flex items-center  border-ce p-2 rounded-5">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.92548 10.4166C5.24965 10.5936 4.63768 10.9153 4.13533 11.3558C3.35535 12.0383 2.83984 13.0049 2.83984 14.1097C2.83984 14.2051 2.84381 14.2992 2.85156 14.3922V18.8894C2.85156 21.1205 4.9014 22.7494 7.18158 22.7494C9.46465 22.7494 11.5016 21.118 11.5016 18.8894V14.11C11.5016 13.0079 10.9878 12.03 10.2055 11.3455L10.2011 11.3418C9.41291 10.6627 8.34848 10.25 7.18158 10.25C6.74992 10.25 6.32724 10.3078 5.92548 10.4166ZM5.68979 12.1015C4.86105 12.5336 4.35156 13.3085 4.35156 14.11V14.3204C4.38231 14.5941 4.47257 14.8489 4.61085 15.0804C5.0383 15.7727 5.97611 16.2997 7.1698 16.2997C8.3683 16.2997 9.30252 15.7793 9.72786 15.0819C9.90158 14.7916 9.99982 14.4647 9.99982 14.1097C9.99982 13.4927 9.71501 12.917 9.20968 12.4959L9.20022 12.488C8.68845 12.0471 7.97289 11.7598 7.15979 11.7598C6.612 11.7598 6.11211 11.8854 5.68979 12.1015ZM9.99979 16.9398C9.22226 17.4984 8.22087 17.7997 7.1698 17.7997C6.12251 17.7997 5.12774 17.4967 4.3536 16.9452C4.4013 17.943 5.28485 18.8385 6.66777 19.0175C6.83205 19.0384 7.00349 19.0494 7.18158 19.0494C8.85601 19.0494 9.94973 18.0604 9.99979 16.9398ZM4.60511 19.855C5.16318 20.1938 5.81481 20.4161 6.49952 20.5052C6.72419 20.5349 6.95227 20.55 7.18158 20.55C8.10692 20.55 9.01017 20.3055 9.7495 19.8559C9.32142 20.6477 8.38086 21.2494 7.18158 21.2494C5.98028 21.2494 5.03545 20.6464 4.60511 19.855Z"
                                fill="black"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.30405 4.30128C6.52398 4.26744 6.75575 4.25 7 4.25H16C16.2446 4.25 16.4468 4.2598 16.6267 4.28978C16.6389 4.29181 16.6511 4.29354 16.6634 4.29497C17.7851 4.42541 18.6691 4.89822 19.2747 5.59999C19.8817 6.30336 20.25 7.2847 20.25 8.5V9.19922H18.9202C18.1626 9.19922 17.4612 9.49736 16.9495 10.0192C16.6294 10.3338 16.3854 10.7266 16.2477 11.1635C16.2446 11.1736 16.2415 11.1836 16.2384 11.1937C16.1381 11.5248 16.0987 11.8806 16.133 12.2467C16.2623 13.7693 17.6304 14.8 19.04 14.8H20.25V15.5C20.25 16.8296 19.8101 17.8793 19.0947 18.5947C18.3793 19.3101 17.3296 19.75 16 19.75H13.5C13.0858 19.75 12.75 20.0858 12.75 20.5C12.75 20.9142 13.0858 21.25 13.5 21.25H16C17.6704 21.25 19.1207 20.6899 20.1553 19.6553C21.1899 18.6207 21.75 17.1704 21.75 15.5V14.6194C22.341 14.3324 22.7502 13.7321 22.7502 13.0292V10.9692C22.7502 10.2663 22.341 9.666 21.75 9.37903V8.5C21.75 6.9753 21.2833 5.63163 20.4103 4.62C19.5395 3.6109 18.305 2.97974 16.8545 2.80711C16.5608 2.75975 16.2697 2.75 16 2.75H7C6.68789 2.75 6.38313 2.77204 6.08633 2.81713C4.64812 2.9999 3.42737 3.63519 2.56796 4.64349C1.70803 5.65239 1.25 6.98713 1.25 8.5V10.5C1.25 10.9142 1.58579 11.25 2 11.25C2.41421 11.25 2.75 10.9142 2.75 10.5V8.5C2.75 7.29287 3.11197 6.31761 3.70955 5.61651C4.30559 4.91721 5.17606 4.44312 6.28339 4.30416L6.30405 4.30128ZM17.6774 11.6177C17.7403 11.4159 17.8533 11.2337 18.0039 11.0867L18.0173 11.0732C18.2451 10.8392 18.5609 10.7 18.92 10.7H20.993C21.149 10.7149 21.2502 10.8388 21.2502 10.9692V13.0292C21.2502 13.1624 21.1447 13.2888 20.983 13.2992H19.0402C18.2911 13.2992 17.6806 12.7519 17.6277 12.1169L17.6269 12.1081C17.6107 11.938 17.6294 11.7721 17.6774 11.6177Z"
                                fill="black"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">
                            {{ $sumservice_fees }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Total Fees
                        </h4>

                    </div>


                </div>

                <div class="col-lg-2 col-12 flex br-64 gap-128 align-items-center p-128 bg-white ">
                    <div class="flex items-center justify-center border-ce p-2 rounded-5">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.75 9C2.75 6.57397 3.2398 5.07086 4.15533 4.15533C5.07086 3.2398 6.57397 2.75 9 2.75H15C17.426 2.75 18.9291 3.2398 19.8447 4.15533C20.7602 5.07086 21.25 6.57397 21.25 9V15C21.25 17.426 20.7602 18.9291 19.8447 19.8447C18.9291 20.7602 17.426 21.25 15 21.25H9C6.57397 21.25 5.07086 20.7602 4.15533 19.8447C3.2398 18.9291 2.75 17.426 2.75 15V9ZM9 1.25C6.42603 1.25 4.42914 1.7602 3.09467 3.09467C1.7602 4.42914 1.25 6.42603 1.25 9V15C1.25 17.574 1.7602 19.5709 3.09467 20.9053C4.42914 22.2398 6.42603 22.75 9 22.75H15C17.574 22.75 19.5709 22.2398 20.9053 20.9053C22.2398 19.5709 22.75 17.574 22.75 15V9C22.75 6.42603 22.2398 4.42914 20.9053 3.09467C19.5709 1.7602 17.574 1.25 15 1.25H9ZM15.6406 9.2608C15.9335 8.96791 15.9335 8.49303 15.6406 8.20014C15.3477 7.90725 14.8729 7.90725 14.58 8.20014L8.03998 14.7401C7.74709 15.033 7.74709 15.5079 8.03998 15.8008C8.33288 16.0937 8.80775 16.0937 9.10064 15.8008L15.6406 9.2608ZM8.98001 8.66016C8.71491 8.66016 8.5 8.87507 8.5 9.14017C8.5 9.40524 8.71489 9.62015 8.98001 9.62015C9.24511 9.62015 9.45999 9.40526 9.45999 9.14017C9.45999 8.87505 9.24508 8.66016 8.98001 8.66016ZM7 9.14017C7 8.04664 7.88649 7.16016 8.98001 7.16016C10.0736 7.16016 10.96 8.04667 10.96 9.14017C10.96 10.2337 10.0735 11.1201 8.98001 11.1201C7.88651 11.1201 7 10.2337 7 9.14017ZM15.04 14.8608C15.04 14.5957 15.2549 14.3809 15.52 14.3809C15.7851 14.3809 16 14.5958 16 14.8608C16 15.1259 15.7851 15.3409 15.52 15.3409C15.2549 15.3409 15.04 15.126 15.04 14.8608ZM15.52 12.8809C14.4265 12.8809 13.54 13.7673 13.54 14.8608C13.54 15.9543 14.4265 16.8409 15.52 16.8409C16.6135 16.8409 17.5 15.9544 17.5 14.8608C17.5 13.7673 16.6135 12.8809 15.52 12.8809Z"
                                fill="#292D32"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">

                            {{ $sumvalue + $sumservice_fees }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Total order
                        </h4>

                    </div>


                </div>

            </div>

            <!-- Filter -->
            <div class="pxy-256 bg-white br-96 mb-192">

                <p class="fs-128 gap-2 gray-94 fw-semibold mb-3 pb-3 d-flex align-items-center border-bottom">
                    <img src="{{ asset('new/src/assets/icons/filter.svg') }}" class="brightness-50" width="16"
                        alt="" />
                    Filters
                </p>

                @include('admin.pages.orderdashboard.search')
            </div>



            <!-- Table -->
            <table class="table table-boreder table-hover table-responsive d-none">
                <thead>
                    <tr>
                        <th>Order Count</th>
                        <th>Order value</th>
                        <th>Total Fees</th>
                        <th>Total order</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> {{ $items->total() }} </th>
                        <th>{{ $sumvalue }}</th>
                        <th>{{ $sumservice_fees }}</th>
                        <th>{{ $sumvalue + $sumservice_fees }}</th>
                    </tr>
                </tbody>
            </table>

            <div class="pxy-256 bg-white br-96 mb-192 ">
                <div class="table-responsive w-full overflow-x-auto  border br-64 ">
                    <table id="order-list" class="text-center fw-semibold">
                        <thead class="">
                            <tr>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">ID</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Client Order Id</th>
                                <th class="w-32 fs-112 gray-b4 pxy-1288 font-semibold">Customer name</th>
                                <th class="w-32 fs-112 gray-b4 pxy-1288 font-semibold">Customer phone</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Driver</th>
                                <th class="w-[15%] fs-112 gray-b4 pxy-1288 font-semibold">Shop</th>
                                <th class="w-[15%] fs-112 gray-b4 pxy-1288 font-semibold">Branch</th>
                                <th class="w-[15%] fs-112 gray-b4 pxy-1288 font-semibold">Madar OTP</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Status</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Order value</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Fees</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold text-center">Total order</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Created Date</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Delivery Date</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold"> Unifonic Response </th>

                                <th class="fs-112 gray-b4 pxy-1288 font-semibold"> History </th>
                                </th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center; width:100%">
                            @forelse ($items as $order)
                                <tr class="fs-112 text-center black-58">
                                    <td class="pxy-1288">{{ @$order->id }}</td>
                                    <td class="pxy-1288">
                                        <p class=" text-slide-wrapper">
                                            <span class="text-slide"> {{ @$order->OrderNumber }} </span>
                                        </p>
                                    </td>
                                    <td class="pxy-1288">{{ @$order->customer_name }}</td>
                                    <td class="pxy-1288">{{ @$order->customer_phone }}</td>
                                    <td class="pxy-1288">{{ @$order->DriverDataSearch->id }} |
                                        {{ @$order->DriverDataSearch->full_name }}</td>
                                    <td class="pxy-1288">
                                        {{ @$order->shop?->first_name ?? $order->branchIntegration?->client?->first_name }}
                                    </td>
                                    <td class="pxy-1288">{{ @$order->branch?->name ?? $order->branchIntegration?->name }}
                                    </td>
                                    <td class="pxy-1288">{{ @$order->otp }}</td>
                                    <td class="pxy-1288">{{ @$order->status->getLabel() }}</td>

                                    <td class="pxy-1288">{{ @$order->value }}</td>
                                    <td class="pxy-1288">{{ @$order->service_fees }}</td>
                                    <td class="pxy-1288">{{ @$order->service_fees + $order->value }}</td>
                                    <td class="pxy-1288">{{ @$order->created_at }}</td>
                                    <td class="pxy-1288">{{ @$order->delivered_at }}</td>


                                    <td class="pxy-1288">
                                        <a href="#" data-id="{{ @$order->id }}" data-bs-toggle="modal"
                                            data-bs-target="#unifonic-response-modal"
                                            class="flex items-center m-auto order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 unifonic-response-btn">
                                            <img src="{{ asset('new/src/assets/icons/message-text.svg') }}"
                                                alt="" width="19.2px" height="19.2px" />
                                        </a>
                                    </td>


                                    <td class="pxy-1288 white-space-no">
                                        <a href="#" data-id="{{ @$order->id }}" data-bs-toggle="modal"
                                            data-bs-target="#historyModal"
                                            class=" items-center m-auto inline-flex order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-history-btn">
                                            <img src="{{ asset('new/src/assets/icons/logIcon.svg') }}" alt=""
                                                width="19.2px" height="19.2px" />
                                        </a>
                                        <a href="#" data-id="{{ @$order->id }}" data-bs-toggle="modal"
                                            data-bs-target="#viewOrderModal"
                                            class="m-auto inline-flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-view-btn">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M11.9998 2.9707C8.15186 2.9707 4.64826 5.2393 2.25734 8.99767C1.71214 9.85208 1.46484 10.9467 1.46484 11.9957C1.46484 13.0445 1.71205 14.1389 2.25703 14.9932C4.64795 18.7519 8.1517 21.0207 11.9998 21.0207C15.848 21.0207 19.3517 18.7519 21.7427 14.9932C22.2876 14.1389 22.5348 13.0445 22.5348 11.9957C22.5348 10.9469 22.2876 9.8525 21.7427 8.99816C19.3517 5.2395 15.848 2.9707 11.9998 2.9707ZM3.52266 9.80325C5.71174 6.3619 8.78799 4.4707 11.9998 4.4707C15.2117 4.4707 18.288 6.3619 20.477 9.80325L20.4777 9.80423C20.8322 10.3597 21.0348 11.1549 21.0348 11.9957C21.0348 12.8365 20.8322 13.6317 20.4777 14.1872L20.477 14.1882C18.288 17.6295 15.2117 19.5207 11.9998 19.5207C8.78799 19.5207 5.71174 17.6295 3.52266 14.1882L3.52204 14.1872C3.16745 13.6317 2.96484 12.8365 2.96484 11.9957C2.96484 11.1549 3.16745 10.3597 3.52204 9.80423L3.52266 9.80325ZM9.17188 11.9999C9.17188 10.4341 10.4361 9.16992 12.0019 9.16992C13.5677 9.16992 14.8319 10.4341 14.8319 11.9999C14.8319 13.5657 13.5677 14.8299 12.0019 14.8299C10.4361 14.8299 9.17188 13.5657 9.17188 11.9999ZM12.0019 7.66992C9.60766 7.66992 7.67188 9.60571 7.67188 11.9999C7.67188 14.3941 9.60766 16.3299 12.0019 16.3299C14.3961 16.3299 16.3319 14.3941 16.3319 11.9999C16.3319 9.60571 14.3961 7.66992 12.0019 7.66992Z"
                                                    fill="#aeaeae"></path>
                                            </svg>
                                        </a>
                                        <a href="#" data-id="{{ @$order->id }}" data-bs-toggle="modal"
                                            data-bs-target="#editOrderModal"
                                            class="m-auto inline-flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-history-btn">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.59631 2.59656C3.70138 1.49148 5.35723 1.06666 7.49974 1.06666H9.16641C9.49778 1.06666 9.76641 1.33528 9.76641 1.66666C9.76641 1.99803 9.49778 2.26666 9.16641 2.26666H7.49974C5.47558 2.26666 4.21476 2.67516 3.44484 3.44509C2.67491 4.21501 2.26641 5.47583 2.26641 7.49999V12.5C2.26641 14.5241 2.67491 15.785 3.44484 16.5549C4.21476 17.3248 5.47558 17.7333 7.49974 17.7333H12.4997C14.5239 17.7333 15.7847 17.3248 16.5546 16.5549C17.3246 15.785 17.7331 14.5241 17.7331 12.5V10.8333C17.7331 10.502 18.0017 10.2333 18.3331 10.2333C18.6644 10.2333 18.9331 10.502 18.9331 10.8333V12.5C18.9331 14.6425 18.5082 16.2983 17.4032 17.4034C16.2981 18.5085 14.6423 18.9333 12.4997 18.9333H7.49974C5.35723 18.9333 3.70138 18.5085 2.59631 17.4034C1.49123 16.2983 1.06641 14.6425 1.06641 12.5V7.49999C1.06641 5.35748 1.49123 3.70163 2.59631 2.59656Z"
                                                    fill="#949494"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M15.1661 0.869525C16.084 0.77943 16.9969 1.18191 17.9073 2.09239C18.8178 3.00287 19.2203 3.91576 19.1302 4.8336C19.0432 5.71998 18.5083 6.45658 17.9073 7.05759L11.3337 13.6312C11.1484 13.8106 10.9054 13.9679 10.6702 14.0861C10.4364 14.2036 10.1619 14.3067 9.90126 14.344L7.38987 14.7027C6.77804 14.7869 6.19452 14.6198 5.78571 14.2126C5.37605 13.8046 5.20761 13.221 5.29759 12.6057L5.29777 12.6045L5.6555 10.1004L5.65565 10.0993C5.69268 9.83517 5.7953 9.55871 5.91365 9.32325C6.03224 9.0873 6.19153 8.84301 6.37548 8.65906L12.9421 2.09239C13.5432 1.49138 14.2798 0.956531 15.1661 0.869525ZM15.2834 2.06379C14.8114 2.11011 14.323 2.4086 13.7907 2.94092L7.224 9.50759C7.15795 9.57364 7.06724 9.70018 6.98583 9.86215C6.90434 10.0243 6.85708 10.1724 6.84398 10.2662L6.84371 10.2682L6.48538 12.7765L6.48504 12.7788C6.44209 13.0714 6.52786 13.2581 6.63252 13.3624C6.73813 13.4675 6.92917 13.5545 7.22487 13.5141L7.22628 13.5139L9.73155 13.156C9.8209 13.1433 9.96726 13.0964 10.1313 13.0139C10.2919 12.9332 10.4226 12.8419 10.4962 12.7716L17.0588 6.20906C17.5911 5.67673 17.8896 5.18833 17.9359 4.71637C17.9792 4.27589 17.815 3.69711 17.0588 2.94092C16.3026 2.18473 15.7238 2.02055 15.2834 2.06379Z"
                                                    fill="#949494"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.2628 2.88059C12.5819 2.79115 12.913 2.97729 13.0025 3.29637C13.5041 5.08572 14.9047 6.48763 16.705 6.99771C17.0238 7.08805 17.209 7.41973 17.1187 7.73855C17.0284 8.05737 16.6967 8.2426 16.3778 8.15227C14.1781 7.52902 12.4621 5.81425 11.847 3.62028C11.7576 3.30121 11.9437 2.97004 12.2628 2.88059Z"
                                                    fill="#949494"></path>
                                            </svg>
                                        </a>

                                    </td>



                                </tr>

                            @empty
                            @endforelse

                        </tbody>
                    </table>

                </div>
                <div class="d-flex justify-content-between pagination mt-192">
                    {!! $items->appends(request()->all())->links() !!}
                </div>
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

        $(document).on('click', '.order-history-btn', function(e) {
            e.preventDefault();

            let dataId = $(this).data('id');

            $.ajax({
                url: '{{ route('get-order-history') }}',
                type: 'GET',
                data: {
                    order_id: dataId,
                },
                success: function(response) {
                    // console.log('Success:', response);

                    // Populate modal content
                    let createdAt = response.order.created_at ? new Date(response.order
                            .created_at) :
                        null;
                    let client_order_id = response.order_number || '---';
                    $('#modalTitle').text(
                        `Logs - #${response.order.id} Order ID - #${client_order_id} `
                    );
                    $('#brand').text(response.brand || '---');

                    $('#created_at').text(createdAt ? createdAt.toLocaleString() : '---');
                    $('#cancel_reason').text(response.cancel_reason);
                    $('#branch').text(response.branch || '---');
                    $('#customerPhone').text(response.order.customer_phone || '---');
                    $('#customerName').text(response.order.customer_name || '---');

                    // Populate history table
                    let historyRows = '';

                    response.histories.forEach(history => {
                        let description = history.description || '--';
                        historyRows += `
                    <tr>
                     <td colspan="2">${history.date_log} &nbsp;&nbsp;</td>
                         <td></td>
                        <td colspan="3">${history.action} &nbsp;&nbsp;</td>
                        <td></td>
                        <td colspan="5">${description} &nbsp;&nbsp;</td>
                    </tr>`;
                    });
                    $('#historyTable').html(historyRows);

                    // Show the modal
                    $('#orderHistoryModal').fadeIn();
                },

                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                }
            });

        });



        $(document).on('click', '.unifonic-response-btn', function(e) {
            e.preventDefault();

            let dataId = $(this).data('id');

            $.ajax({
                url: '{{ route('UnifonicResponse') }}',
                type: 'GET',
                data: {
                    order_id: dataId,
                },
                success: function(response) {



                    const container = $('#messages_contenair');
                    container.empty(); 

                    response.logs.forEach(log => {
                       
                        
                        container.append(`<p>${log.response_body}</p>`);
                    });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                }
            });

        });



        // $(document).on('click', '.order-view-btn', function(e) {
        //     e.preventDefault();

        //     let dataId = $(this).data('id');

        //     $.ajax({
        //         url: '{{ route('get-order-history') }}',
        //         type: 'GET',
        //         data: {
        //             order_id: dataId,
        //         },
        //         success: function(response) {

        //             let createdAt = response.order.created_at ? new Date(response.order
        //                     .created_at) :
        //                 null;
        //             let client_order_id = response.order_number || '---';
        //             $('#modalTitle').text(
        //                 `Logs - #${response.order.id} Order ID - #${client_order_id} `
        //             );
        //             $('#brand').text(response.brand || '---');

        //             $('#created_at').text(createdAt ? createdAt.toLocaleString() : '---');
        //             $('#cancel_reason').text(response.cancel_reason);
        //             $('#branch').text(response.branch || '---');
        //             $('#customerPhone').text(response.order.customer_phone || '---');
        //             $('#customerName').text(response.order.customer_name || '---');

        //             // Populate history table
        //             let historyRows = '';

        //             response.histories.forEach(history => {
        //                 let description = history.description || '--';
        //                 historyRows += `
        //                 <tr>
        //                  <td colspan="2">${history.date_log} &nbsp;&nbsp;</td>
        //                      <td></td>
        //                     <td colspan="3">${history.action} &nbsp;&nbsp;</td>
        //                     <td></td>
        //                     <td colspan="5">${description} &nbsp;&nbsp;</td>
        //                 </tr>`;
        //             });
        //             $('#historyTable').html(historyRows);

        //             // Show the modal
        //             $('#orderHistoryModal').fadeIn();
        //         },

        //         error: function(xhr, status, error) {
        //             console.error('Error:', xhr.responseText);
        //         }
        //     });

        // });

        $(document).on('shown.bs.modal', '#viewOrderModal', function() {
            function initializeSelect2(parent, id, placeholder = null, search = Infinity) {
                $(`#${id}`).val(null).trigger("change.select2"); // Clear any existing value

                // Select2 configuration
                const select2Config = {
                    dropdownParent: parent,
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: search, // Ensures the search box is always visible
                    language: {
                        searching: function() {
                            return "Searching...";
                        },
                        noResults: function() {
                            return "No matching results found";
                        }
                    }
                };

                // Add placeholder only if provided
                if (placeholder) {
                    select2Config.placeholder = placeholder;
                } else {
                    const firstOptionValue = $(`#${id} option:first`).val();
                    if (firstOptionValue) {
                        $(`#${id}`).val(firstOptionValue).trigger("change");
                    }
                }

                // Initialize Select2
                $(`#${id}`).select2(select2Config);


            }

            initializeSelect2("#viewOrderModal .modal-body .viewJobStatus", "job-status");
            initializeSelect2("#viewOrderModal .modal-body .viewOrderType", "order-type");
            initializeSelect2("#viewOrderModal .modal-body .viewPaymentMethod", "order-payment-method");
            initializeSelect2("#viewOrderModal .modal-body .viewClientLocations", "clients", null, 0);
            initializeSelect2("#viewOrderModal .modal-body .viewBranchsLocation", "branchs", null, 0);
        });

        $(document).on('shown.bs.modal', '#editOrderModal', function() {
            function initializeSelect2(parent, id, placeholder = null, search = Infinity) {
                $(`#${id}`).val(null).trigger("change.select2"); // Clear any existing value

                // Select2 configuration
                const select2Config = {
                    dropdownParent: parent, // Dynamically find the closest modal body
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: search, // Ensures the search box is always visible
                    language: {
                        searching: function() {
                            return "Searching...";
                        },
                        noResults: function() {
                            return "No matching results found";
                        }
                    }
                };
                select2Config.dropdownAutoWidth = true;


                // Add placeholder only if provided
                if (placeholder) {
                    select2Config.placeholder = placeholder;
                } else {
                    const firstOptionValue = $(`#${id} option:first`).val();
                    if (firstOptionValue) {
                        $(`#${id}`).val(firstOptionValue).trigger("change");
                    }
                }

                // Initialize Select2
                $(`#${id}`).select2(select2Config);


            }

            initializeSelect2("#editOrderModal .modal-body .jobStatus", "edit-job-status");
            initializeSelect2("#editOrderModal .modal-body .typeOne", "edit-order-type-one");
            initializeSelect2("#editOrderModal .modal-body .typeTwo", "edit-order-type-two");
            initializeSelect2("#editOrderModal .modal-body .paymentMethod",
                "edit-order-payment-method");
            initializeSelect2("#editOrderModal .modal-body .paidStatus", "order-paid");
            initializeSelect2("#editOrderModal .modal-body .clientsLocation", "edit-clients", null, 0);
            initializeSelect2("#editOrderModal .modal-body .branchsLocation", "edit-branchs", null, 0);
            initializeSelect2("#editOrderModal .modal-body .operatorsList", "edit-operators",
                "operators", 0);


            $(document).on("change", "#edit-operators", function() {
                let selectedText = $("#edit-operators option:selected").text();

                $("#editOrderModal .text-slide").text(selectedText);
            });



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



    $(document).on("mouseenter", ".text-slide-wrapper", function() {

        const $textSlide = $(this).find(".text-slide");
        const textContent = $textSlide.text();
        console.log(textContent)
        const textLength = textContent.length;
        const $wrapper = $(this);

        const textWidth = $textSlide[0].scrollWidth;
        const wrapperWidth = $wrapper.outerWidth();

        const moveDistance = textWidth - wrapperWidth;
        const calcValue = `-${moveDistance}px`;

        const keyframes = `
                @keyframes textSlide {
                    0% {
                        transform: translateX(0); /* Start position */
                    }
                    50% {
                        transform: translateX(${calcValue}); /* Move to the last letter */
                    }
                    100% {
                        transform: translateX(0); /* Return to start */
                    }
                }`;
        // Check if the keyframes are already appended to avoid duplication
        if ($("style#textSlideKeyframe").length === 0) {
            $("head").append(`<style id="textSlideKeyframe">${keyframes}</style>`);
        } else {
            // If style already exists, update the keyframes
            $("style#textSlideKeyframe").html(keyframes);
        }



    });
</script>

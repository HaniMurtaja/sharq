@extends('admin.layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


<link rel="stylesheet" href="{{ asset('new/src/css/dashboard.css')}}" />

@section('content')


<section class="dashboardContainer">
    <div class="dashboardHeader">
        <h1>Dashboard</h1>
        <div class="dashboardFilters">
            <div class="custom-fieldset">
                <label for="template-name" class="custom-legend">
                    Client <span class="text-danger d-none">*</span>
                </label>
                <select class="operator " name="client" id="clientFilter">
                    <option value="-1">all</option>
                    @foreach ($clients as $client)
                        <option value="{{$client->id}}">{{$client->full_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="custom-fieldset">
                <label for="template-name" class="custom-legend">
                    Selected data to be export <span class="text-danger d-none">*</span>
                </label>
                <!-- Date Picker -->
                <input type="text" id="datePicker" name="date" placeholder="Select a date or range" readonly />

            </div>


        </div>

    </div>
    <div class="itemListDivider">

    </div>

    <div class="chartsContainer">
        <!-- First Chart -->
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
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
                    <h2>Orders</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="total_orders_count">5002</p>
                    <a href="#" id="seeMoreDetailBtn" class="moreDetailsBtn" data-bs-toggle="modal"
                        data-bs-target="#OrdersDetailsDashBoard">See more details</a>
                </div>

            </div>
            <canvas id="stackedBarChart"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
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
                    <h2>Orders per city</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter">1724</p>
                </div>

            </div>
            <div id="chartLegend" style="margin-left: 20px;"></div>
            <canvas id="donutChart"></canvas>
        </div>
        <div class="ChartContainer">

            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12 1.25C6.08579 1.25 1.25 6.08579 1.25 12C1.25 17.9142 6.08579 22.75 12 22.75C17.9142 22.75 22.75 17.9142 22.75 12C22.75 6.08579 17.9142 1.25 12 1.25ZM2.75 12C2.75 6.91421 6.91421 2.75 12 2.75C17.0858 2.75 21.25 6.91421 21.25 12C21.25 17.0858 17.0858 21.25 12 21.25C6.91421 21.25 2.75 17.0858 2.75 12ZM16.7799 9.70072C17.073 9.40808 17.0734 8.93321 16.7808 8.64006C16.4882 8.34691 16.0133 8.34649 15.7201 8.63912L10.5805 13.7697L8.28033 11.4696C7.98744 11.1767 7.51256 11.1767 7.21967 11.4696C6.92678 11.7625 6.92678 12.2374 7.21967 12.5303L10.0497 15.3603C10.3424 15.653 10.8169 15.6532 11.1099 15.3607L16.7799 9.70072Z"
                            fill="#585858"></path>
                    </svg>
                    <h2>Acceptance rate</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter">100%</p>
                </div>

            </div>

            <canvas id="acceptanceChart"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10.2973 3.13399C10.7276 2.89241 11.3429 2.75 12.0001 2.75C12.6573 2.75 13.2727 2.89242 13.703 3.134L19.0468 6.09615C19.3976 6.29037 19.7495 6.61381 20.0516 7.01894L11.9999 11.6829L3.94621 7.02221C4.24896 6.6155 4.60203 6.29073 4.95373 6.09599L10.2937 3.13597L10.2973 3.13399ZM3.26764 8.36259C3.18398 8.64577 3.14014 8.92041 3.14014 9.17V14.83C3.14014 15.354 3.33336 15.9883 3.68243 16.5803C4.03146 17.1723 4.49347 17.6492 4.95343 17.9039L10.2973 20.866C10.5577 21.0122 10.8858 21.1221 11.25 21.1861V12.982L3.26764 8.36259ZM12.75 21.1861C13.1143 21.1222 13.4425 21.0123 13.703 20.8661L13.7077 20.8634L14.8223 20.2483C14.4549 19.5773 14.2501 18.807 14.2501 18C14.2501 16.5059 14.9387 15.1621 16.0329 14.2933C16.8488 13.6431 17.8818 13.25 19.0001 13.25C19.6626 13.25 20.2901 13.3838 20.8601 13.6284V9.17C20.8601 8.91925 20.8159 8.64321 20.7314 8.35863L12.75 12.9819V21.1861ZM12.0001 1.25C12.8422 1.25 13.7261 1.42729 14.4354 1.82498L14.4373 1.82601L19.7737 4.78406C20.5236 5.19939 21.1615 5.89736 21.6099 6.65782C22.0583 7.41831 22.3601 8.31395 22.3601 9.17V14.83L22.3601 14.8385C22.3602 14.8886 22.3604 14.9927 22.3356 15.1171C22.2831 15.3793 22.0951 15.5937 21.842 15.6799C21.5888 15.7661 21.309 15.7111 21.1074 15.5354C20.5401 15.0411 19.814 14.75 19.0001 14.75C18.2386 14.75 17.5317 15.0169 16.9676 15.4665L16.9663 15.4675C16.2211 16.0588 15.7501 16.9746 15.7501 18C15.7501 18.6094 15.9206 19.1833 16.2185 19.6663C16.2238 19.6749 16.2289 19.6836 16.2338 19.6923C16.2945 19.8002 16.3737 19.9101 16.4696 20.0219C16.6164 20.1932 16.6783 20.4215 16.6381 20.6435C16.598 20.8655 16.46 21.0576 16.2625 21.1667L14.4373 22.174L14.4349 22.1754C13.7256 22.5728 12.842 22.75 12.0001 22.75C11.158 22.75 10.2741 22.5727 9.56477 22.175L9.56296 22.174L4.22651 19.216C3.4766 18.8007 2.83873 18.1027 2.39033 17.3422C1.94191 16.5817 1.64014 15.6861 1.64014 14.83V9.17C1.64014 8.31395 1.94191 7.41831 2.39033 6.65782C2.83873 5.89736 3.4769 5.19922 4.22681 4.78389L9.56481 1.82498C10.2742 1.42729 11.1581 1.25 12.0001 1.25Z"
                            fill="#585858"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19 14.75C18.2385 14.75 17.5316 15.0169 16.9675 15.4665L16.9662 15.4675L16.9662 15.4675C16.221 16.0588 15.75 16.9745 15.75 18C15.75 19.7958 17.2042 21.25 19 21.25C19.8167 21.25 20.5636 20.9439 21.1457 20.4359C21.8216 19.841 22.25 18.973 22.25 18C22.25 16.2042 20.7958 14.75 19 14.75ZM16.0332 14.293C16.849 13.6429 17.8818 13.25 19 13.25C21.6242 13.25 23.75 15.3758 23.75 18C23.75 19.4265 23.1189 20.6981 22.1353 21.5632L22.1333 21.5649L22.1333 21.5649C21.2956 22.2965 20.2029 22.75 19 22.75C16.3758 22.75 14.25 20.6242 14.25 18C14.25 16.5058 14.9387 15.1617 16.0332 14.293Z"
                            fill="#585858"></path>
                        <path
                            d="M23 18C23 19.2 22.47 20.27 21.64 21C20.93 21.62 20.01 22 19 22C16.79 22 15 20.21 15 18C15 16.74 15.58 15.61 16.5 14.88C17.19 14.33 18.06 14 19 14C21.21 14 23 15.79 23 18Z"
                            fill="#22AD2F"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19.4241 15.4511C19.3116 15.3386 19.159 15.2754 18.9998 15.2754C18.8407 15.2754 18.6881 15.3386 18.5756 15.4511L17.0581 16.9686C16.8237 17.2029 16.8237 17.5828 17.0581 17.8172C17.2924 18.0515 17.6723 18.0515 17.9066 17.8172L18.3999 17.3238V20.1259C18.3999 20.4572 18.6685 20.7259 18.9999 20.7259C19.3313 20.7259 19.5999 20.4572 19.5999 20.1259V17.324L20.0931 17.8172C20.3274 18.0515 20.7073 18.0515 20.9416 17.8172C21.1759 17.5828 21.1759 17.2029 20.9416 16.9686L19.4241 15.4511Z"
                            fill="white"></path>
                    </svg>

                    <h2>Avg arrival time to pickup</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="total_average_picked_time">00:05:00</p>
                </div>

            </div>
            <canvas id="avgArrivalPickup"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9 1.25C8.58579 1.25 8.25 1.58579 8.25 2C8.25 2.41421 8.58579 2.75 9 2.75H15C15.4142 2.75 15.75 2.41421 15.75 2C15.75 1.58579 15.4142 1.25 15 1.25H9ZM12 5.25C7.58421 5.25 4 8.83421 4 13.25C4 17.6658 7.58421 21.25 12 21.25C16.4158 21.25 20 17.6658 20 13.25C20 8.83421 16.4158 5.25 12 5.25ZM2.5 13.25C2.5 8.00579 6.75579 3.75 12 3.75C17.2442 3.75 21.5 8.00579 21.5 13.25C21.5 18.4942 17.2442 22.75 12 22.75C6.75579 22.75 2.5 18.4942 2.5 13.25ZM12 7.25C12.4142 7.25 12.75 7.58579 12.75 8V13C12.75 13.4142 12.4142 13.75 12 13.75C11.5858 13.75 11.25 13.4142 11.25 13V8C11.25 7.58579 11.5858 7.25 12 7.25Z"
                            fill="#585858"></path>
                    </svg>
                    <h2>Avg Delivery Time</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="avg_delivery_time_text">00:29:00</p>
                </div>

            </div>
            <canvas id="avgDeliveryTime"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9 1.25C8.58579 1.25 8.25 1.58579 8.25 2C8.25 2.41421 8.58579 2.75 9 2.75H15C15.4142 2.75 15.75 2.41421 15.75 2C15.75 1.58579 15.4142 1.25 15 1.25H9ZM12 5.25C7.58421 5.25 4 8.83421 4 13.25C4 17.6658 7.58421 21.25 12 21.25C16.4158 21.25 20 17.6658 20 13.25C20 8.83421 16.4158 5.25 12 5.25ZM2.5 13.25C2.5 8.00579 6.75579 3.75 12 3.75C17.2442 3.75 21.5 8.00579 21.5 13.25C21.5 18.4942 17.2442 22.75 12 22.75C6.75579 22.75 2.5 18.4942 2.5 13.25ZM12 7.25C12.4142 7.25 12.75 7.58579 12.75 8V13C12.75 13.4142 12.4142 13.75 12 13.75C11.5858 13.75 11.25 13.4142 11.25 13V8C11.25 7.58579 11.5858 7.25 12 7.25Z"
                            fill="#585858"></path>
                        <path
                            d="M23 18C23 19.2 22.47 20.27 21.64 21C20.93 21.62 20.01 22 19 22C16.79 22 15 20.21 15 18C15 16.74 15.58 15.61 16.5 14.88C17.19 14.33 18.06 14 19 14C21.21 14 23 15.79 23 18Z"
                            fill="#22AD2F"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19 14.75C18.2385 14.75 17.5316 15.0169 16.9675 15.4665L16.9662 15.4675L16.9662 15.4675C16.221 16.0588 15.75 16.9745 15.75 18C15.75 19.7958 17.2042 21.25 19 21.25C19.8167 21.25 20.5636 20.9439 21.1457 20.4359C21.8216 19.841 22.25 18.973 22.25 18C22.25 16.2042 20.7958 14.75 19 14.75ZM16.0332 14.293C16.849 13.6429 17.8818 13.25 19 13.25C21.6242 13.25 23.75 15.3758 23.75 18C23.75 19.4265 23.1189 20.6981 22.1353 21.5632L22.1333 21.5649L22.1333 21.5649C21.2956 22.2965 20.2029 22.75 19 22.75C16.3758 22.75 14.25 20.6242 14.25 18C14.25 16.5058 14.9387 15.1617 16.0332 14.293Z"
                            fill="#585858"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19.4241 15.4511C19.3116 15.3386 19.159 15.2754 18.9998 15.2754C18.8407 15.2754 18.6881 15.3386 18.5756 15.4511L17.0581 16.9686C16.8237 17.2029 16.8237 17.5828 17.0581 17.8172C17.2924 18.0515 17.6723 18.0515 17.9066 17.8172L18.3999 17.3238V20.1259C18.3999 20.4572 18.6685 20.7259 18.9999 20.7259C19.3313 20.7259 19.5999 20.4572 19.5999 20.1259V17.324L20.0931 17.8172C20.3274 18.0515 20.7073 18.0515 20.9416 17.8172C21.1759 17.5828 21.1759 17.2029 20.9416 16.9686L19.4241 15.4511Z"
                            fill="white"></path>
                    </svg>
                    <h2>Avg waiting time at pickup</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="totalAverageWaitingTime">00:12:00</p>
                </div>

            </div>
            <canvas id="avgWaitingPickup"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9 1.25C8.58579 1.25 8.25 1.58579 8.25 2C8.25 2.41421 8.58579 2.75 9 2.75H15C15.4142 2.75 15.75 2.41421 15.75 2C15.75 1.58579 15.4142 1.25 15 1.25H9ZM12 5.25C7.58421 5.25 4 8.83421 4 13.25C4 17.6658 7.58421 21.25 12 21.25C16.4158 21.25 20 17.6658 20 13.25C20 8.83421 16.4158 5.25 12 5.25ZM2.5 13.25C2.5 8.00579 6.75579 3.75 12 3.75C17.2442 3.75 21.5 8.00579 21.5 13.25C21.5 18.4942 17.2442 22.75 12 22.75C6.75579 22.75 2.5 18.4942 2.5 13.25ZM12 7.25C12.4142 7.25 12.75 7.58579 12.75 8V13C12.75 13.4142 12.4142 13.75 12 13.75C11.5858 13.75 11.25 13.4142 11.25 13V8C11.25 7.58579 11.5858 7.25 12 7.25Z"
                            fill="#585858"></path>
                        <path
                            d="M23 18C23 19.2 22.47 20.27 21.64 21C20.93 21.62 20.01 22 19 22C16.79 22 15 20.21 15 18C15 16.74 15.58 15.61 16.5 14.88C17.19 14.33 18.06 14 19 14C21.21 14 23 15.79 23 18Z"
                            fill="#FF4B36"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M19 14.75C18.2385 14.75 17.5316 15.0169 16.9675 15.4665L16.9662 15.4675L16.9662 15.4675C16.221 16.0588 15.75 16.9745 15.75 18C15.75 19.7958 17.2042 21.25 19 21.25C19.8167 21.25 20.5636 20.9439 21.1457 20.4359C21.8216 19.841 22.25 18.973 22.25 18C22.25 16.2042 20.7958 14.75 19 14.75ZM16.0332 14.293C16.849 13.6429 17.8818 13.25 19 13.25C21.6242 13.25 23.75 15.3758 23.75 18C23.75 19.4265 23.1189 20.6981 22.1353 21.5632L22.1333 21.5649L22.1333 21.5649C21.2956 22.2965 20.2029 22.75 19 22.75C16.3758 22.75 14.25 20.6242 14.25 18C14.25 16.5058 14.9387 15.1617 16.0332 14.293Z"
                            fill="#585858"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M18.5759 20.5489C18.6884 20.6614 18.841 20.7246 19.0002 20.7246C19.1593 20.7246 19.3119 20.6614 19.4244 20.5489L20.9419 19.0314C21.1763 18.7971 21.1763 18.4172 20.9419 18.1828C20.7076 17.9485 20.3277 17.9485 20.0934 18.1828L19.6001 18.6762L19.6001 15.8741C19.6001 15.5428 19.3315 15.2741 19.0001 15.2741C18.6687 15.2741 18.4001 15.5428 18.4001 15.8741L18.4001 18.676L17.9069 18.1828C17.6726 17.9485 17.2927 17.9485 17.0584 18.1828C16.8241 18.4172 16.8241 18.7971 17.0584 19.0314L18.5759 20.5489Z"
                            fill="white"></path>
                    </svg>
                    <h2>Avg Driver waiting time at dropoff</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="totalAverageWaitingTime2">00:02:00</p>
                </div>

            </div>
            <canvas id="avgdriverWaitingDropOff"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M11.55 1.25C10.6931 1.25 10.0025 1.54608 9.55119 2.12775C9.12661 2.67498 9 3.36648 9 4V21.25H7.75V10C7.75 9.3804 7.64103 8.70345 7.26741 8.1617C6.86119 7.57267 6.21655 7.25 5.4 7.25H4.6C3.78345 7.25 3.13881 7.57267 2.73259 8.1617C2.35897 8.70345 2.25 9.3804 2.25 10V21.25H2C1.58579 21.25 1.25 21.5858 1.25 22C1.25 22.4142 1.58579 22.75 2 22.75H3H7H9.75H14.25H17H21H22C22.4142 22.75 22.75 22.4142 22.75 22C22.75 21.5858 22.4142 21.25 22 21.25H21.75V15C21.75 14.3804 21.641 13.7035 21.2674 13.1617C20.8612 12.5727 20.2165 12.25 19.4 12.25H18.6C17.7835 12.25 17.1388 12.5727 16.7326 13.1617C16.359 13.7035 16.25 14.3804 16.25 15V21.25H15V4C15 3.36648 14.8734 2.67498 14.4488 2.12775C13.9975 1.54608 13.3069 1.25 12.45 1.25H11.55ZM17.75 21.25H20.25V15C20.25 14.5196 20.159 14.1965 20.0326 14.0133C19.9388 13.8773 19.7835 13.75 19.4 13.75H18.6C18.2165 13.75 18.0612 13.8773 17.9674 14.0133C17.841 14.1965 17.75 14.5196 17.75 15V21.25ZM6.25 10V21.25H3.75V10C3.75 9.5196 3.84103 9.19655 3.96741 9.0133C4.06119 8.87733 4.21655 8.75 4.6 8.75H5.4C5.78345 8.75 5.93882 8.87733 6.03259 9.0133C6.15897 9.19655 6.25 9.5196 6.25 10ZM13.5 21.25V4C13.5 3.53352 13.4016 3.22502 13.2637 3.04725C13.1525 2.90392 12.9431 2.75 12.45 2.75H11.55C11.0569 2.75 10.8475 2.90392 10.7363 3.04725C10.5984 3.22502 10.5 3.53352 10.5 4V21.25H13.5Z"
                            fill="#585858"></path>
                    </svg>
                    <h2>Avg Orders/Hour</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter" id="totalAverageOfOrdersPerHour">204</p>
                </div>

            </div>
            <canvas id="avgOrderPerHourChart"></canvas>

        </div>
        <div class="ChartContainer">
            <div class="chartHeader">
                <div class="d-flex align-items-center chartTitle">
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
                    <h2>Operators</h2>
                </div>
                <div class="text-right">
                    <p class="chartCounter">5002</p>
                    <a href="#" id="driver-data-btn" class="moreDetailsBtn" data-bs-toggle="modal"
                        data-bs-target="#OperatorsDetailsDashBoard">See more details</a>
                </div>

            </div>
            <canvas id="operators"></canvas>

        </div>



    </div>

</section>

<!-- OrdersDetailsDashBoard Modal -->
<div class="modal fade dashboardModal" id="OrdersDetailsDashBoard" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="head">
                    <h4>
                        Orders
                    </h4>
                    <p>
                        2234 (Successfully Delivered: 85.94%)
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
                    <table class="table" >
                        <thead>
                            <th>ID</th>
                            <th></th>
                            <th>Clients</th>
                            <th>Pending</th>
                            <th>In progress</th>
                            <th>Failed</th>
                            <th>Cancelled</th>
                            <th>Delivered</th>
                            <th>Avg operator waiting</th>
                            <th>Avg delivery</th>
                        </thead>
                        <tbody id="clients-orders-data">
                            <tr class="table-row">
                                <td>
                                    425
                                </td>
                                <td class="clientImageContainer">
                                    <div class="clientImageWrapper">
                                        <img src="https://picsum.photos/200/300" alt="clientImage">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-column align-items-center">
                                        <p class="smFontInTable">McDonald's WST</p>
                                    </div>
                                </td>
                                <td class="smFontInTable">
                                    1
                                </td>
                                <td class="smFontInTable">
                                    62
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    15
                                </td>
                                <td>
                                    514
                                </td>
                                <td>
                                    00:14:00
                                </td>
                                <td>
                                    00:12:00
                                </td>
                            </tr>


                        </tbody>
                    </table>

                </div>


            </div>
        </div>

    </div>

</div>




<div class="modal fade dashboardModal" id="OperatorsDetailsDashBoard" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="head">
                    <h4>
                        Operators
                    </h4>
                    <p>
                        please choose a shift
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

            <div class="modal-body p-0">

                <div wire:ignore id="formMap"></div>

                <div class="operatorsDetailsFilters">
                    {{-- <div class="custom-fieldset">
                        <label for="template-name" class="custom-legend">
                            Shift <span class="text-danger d-none">*</span>
                        </label>
                        <select class="operator " id="shiftFilter">
                            <option value="1">all</option>
                            <option value="2">Page 2</option>
                            <option value="3">Page 3</option>
                            <option value="1">Page 1</option>
                            <option value="2">Page 2</option>
                            <option value="3">Page 3</option>
                            <option value="1">Page 1</option>
                            <option value="2">Page 2</option>
                            <option value="3">Page 3</option>
                            <option value="1">Page 1</option>
                            <option value="2">Page 2</option>
                            <option value="3">Page 3</option>
                        </select>
                    </div> --}}

                    <div class=" searchBox flex items-center justify-between gap-3 p-2 bg-white  rounded-md">
                                        <!-- Input -->
                                        <input type="text" placeholder="Search with driver name or phone..." class=" bg-transparent outline-none border-0" style="font-size: 11.2px; color: #585858;width: 100%" id="search">
                                        <!-- Icon -->
                                        <svg width="16" height="16" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z" fill="#A30133"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z" fill="#A30133"></path>
                                        </svg>
                                    </div>

                </div>

                <div class="scrollable-table">
                    <table class="table" >
                        <thead >
                            <th></th>
                            <th>Driver</th>
                            <th>App OS & version</th>
                            <th>Vehicle</th>
                            <th>Attendance Rate</th>
                            <th>Acceptance Rate</th>
                            <th>Status</th>

                        </thead>
                        <tbody id="driver-data-table">
                            <tr class="table-row">
                                <td class="DriverImageContainer">
                                    <div class="DriverImageWrapper">
                                        <img src="https://picsum.photos/200/300" alt="DriverImage">
                                    </div>
                                </td>
                                <td class="text-left">
                                    <p class="mb-1">Abadi Omar Mohammed</p>
                                    <p class="smFontInTable">+966581291752</p>
                                </td>

                                <td>
                                    <div class="d-flex gap-1 flex-column align-items-center">
                                        <p class=" d-flex align-items-center gap-2">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9751 3.01903L15.9351 1.28703C15.9474 1.26483 15.9552 1.24044 15.958 1.21524C15.9609 1.19003 15.9588 1.16452 15.9517 1.14014C15.9447 1.11577 15.933 1.09301 15.9172 1.07317C15.9014 1.05334 15.8818 1.03681 15.8596 1.02453C15.8374 1.01225 15.813 1.00446 15.7878 1.00161C15.7626 0.998761 15.7371 1.0009 15.7127 1.00791C15.6884 1.01492 15.6656 1.02667 15.6458 1.04247C15.6259 1.05828 15.6094 1.07783 15.5971 1.10003L14.6271 2.85003C13.7991 2.48692 12.9048 2.29944 12.0006 2.29944C11.0965 2.29944 10.2022 2.48692 9.37413 2.85003L8.40413 1.10003C8.37933 1.05494 8.33763 1.02155 8.28822 1.00721C8.2388 0.992859 8.18571 0.99873 8.14063 1.02353C8.09554 1.04833 8.06215 1.09002 8.0478 1.13943C8.03346 1.18885 8.03933 1.24194 8.06413 1.28703L9.02413 3.01903C8.111 3.46988 7.33952 4.16328 6.79415 5.0233C6.24879 5.88333 5.95056 6.87683 5.93213 7.89503H18.0691C18.0505 6.87662 17.752 5.88297 17.2062 5.02293C16.6605 4.16288 15.8886 3.4696 14.9751 3.01903ZM9.20013 5.67403C9.09981 5.67403 9.00175 5.64427 8.91835 5.58851C8.83495 5.53276 8.76997 5.45352 8.73162 5.36082C8.69328 5.26812 8.6833 5.16612 8.70294 5.06775C8.72259 4.96937 8.77097 4.87904 8.84198 4.80817C8.91298 4.73731 9.00341 4.6891 9.10183 4.66965C9.20024 4.6502 9.30222 4.66038 9.39484 4.69891C9.48746 4.73744 9.56658 4.80257 9.62217 4.88608C9.67776 4.96959 9.70732 5.06771 9.70713 5.16803C9.70686 5.30232 9.65333 5.43102 9.55828 5.52589C9.46322 5.62075 9.33442 5.67403 9.20013 5.67403ZM14.8021 5.67403C14.7018 5.67403 14.6037 5.64427 14.5203 5.58851C14.437 5.53276 14.372 5.45352 14.3336 5.36082C14.2953 5.26812 14.2853 5.16612 14.3049 5.06775C14.3246 4.96937 14.373 4.87904 14.444 4.80817C14.515 4.73731 14.6054 4.6891 14.7038 4.66965C14.8022 4.6502 14.9042 4.66038 14.9968 4.69891C15.0895 4.73744 15.1686 4.80257 15.2242 4.88608C15.2798 4.96959 15.3093 5.06771 15.3091 5.16803C15.3089 5.30232 15.2553 5.43102 15.1603 5.52589C15.0652 5.62075 14.9364 5.67403 14.8021 5.67403ZM5.93013 17.171C5.92986 17.3641 5.96771 17.5553 6.04151 17.7337C6.1153 17.9121 6.22358 18.0742 6.36015 18.2107C6.49672 18.3472 6.65888 18.4554 6.83734 18.529C7.01581 18.6027 7.20706 18.6404 7.40013 18.64H8.37313V21.64C8.37313 22.0009 8.51646 22.3469 8.77161 22.602C9.02675 22.8572 9.3728 23.0005 9.73362 23.0005C10.0945 23.0005 10.4405 22.8572 10.6956 22.602C10.9508 22.3469 11.0941 22.0009 11.0941 21.64V18.64H12.9081V21.64C12.9081 22.0007 13.0514 22.3466 13.3065 22.6017C13.5615 22.8567 13.9074 23 14.2681 23C14.6288 23 14.9747 22.8567 15.2298 22.6017C15.4848 22.3466 15.6281 22.0007 15.6281 21.64V18.64H16.6021C16.7949 18.6402 16.9859 18.6023 17.1641 18.5286C17.3422 18.4548 17.5041 18.3467 17.6405 18.2104C17.7768 18.074 17.8849 17.9121 17.9586 17.734C18.0324 17.5558 18.0703 17.3648 18.0701 17.172V8.37503H5.93013V17.171ZM4.06313 8.14103C3.88444 8.14103 3.70751 8.17624 3.54244 8.24465C3.37738 8.31306 3.22741 8.41332 3.10111 8.53972C2.97481 8.66611 2.87465 8.81615 2.80636 8.98127C2.73807 9.14639 2.70299 9.32335 2.70313 9.50203V15.171C2.70313 15.3496 2.7383 15.5265 2.80665 15.6915C2.875 15.8565 2.97517 16.0064 3.10146 16.1327C3.22775 16.259 3.37767 16.3592 3.54268 16.4275C3.70768 16.4959 3.88453 16.531 4.06313 16.531C4.24172 16.531 4.41857 16.4959 4.58357 16.4275C4.74858 16.3592 4.8985 16.259 5.02479 16.1327C5.15108 16.0064 5.25126 15.8565 5.3196 15.6915C5.38795 15.5265 5.42313 15.3496 5.42313 15.171V9.50203C5.42313 9.32343 5.38795 9.14658 5.3196 8.98158C5.25126 8.81658 5.15108 8.66665 5.02479 8.54036C4.8985 8.41408 4.74858 8.3139 4.58357 8.24555C4.41857 8.17721 4.24172 8.14203 4.06313 8.14203M19.9351 8.14203C19.7564 8.14203 19.5795 8.17724 19.4144 8.24565C19.2494 8.31406 19.0994 8.41432 18.9731 8.54072C18.8468 8.66711 18.7466 8.81715 18.6784 8.98227C18.6101 9.14739 18.575 9.32435 18.5751 9.50303V15.172C18.5751 15.3506 18.6103 15.5275 18.6786 15.6925C18.747 15.8575 18.8472 16.0074 18.9735 16.1337C19.0997 16.26 19.2497 16.3602 19.4147 16.4285C19.5797 16.4969 19.7565 16.532 19.9351 16.532C20.1137 16.532 20.2906 16.4969 20.4556 16.4285C20.6206 16.3602 20.7705 16.26 20.8968 16.1337C21.0231 16.0074 21.1233 15.8575 21.1916 15.6925C21.2599 15.5275 21.2951 15.3506 21.2951 15.172V9.50203C21.2951 9.14133 21.1518 8.79541 20.8968 8.54036C20.6417 8.28531 20.2958 8.14203 19.9351 8.14203Z" fill="#CECECE"></path></svg>
                                            <span>10.1.39</span>
                                        </p>
                                    </div>
                                </td>
                                <td class="smFontInTable">
                                    -
                                </td>
                                <td class="smFontInTable">
                                    23.06%
                                </td>
                                <td>
                                    100%
                                </td>
                                <td>
                                    offline
                                </td>

                            </tr>


                        </tbody>
                    </table>

                </div>


            </div>

        </div>

    </div>






    {{-- @include('admin.pages.clients.mapHandler') --}}
    @include('admin.pages.charts.dashboardScripts')
    @endsection

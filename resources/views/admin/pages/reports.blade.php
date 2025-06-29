@extends('admin.layouts.app')

{{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js" defer></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" defer></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/2.1.0/js/dataTables.min.js" defer></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/2.1.0/js/dataTables.min.js" defer></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    label {
        display: inline-block;
        margin-bottom: .5rem;
        margin-left: 20px !important;
    }
</style>
@section('content')
    <div class="flex flex-col h-full p-6">
        <!-- Filter -->
        <div class="h-16 px-4 py-3 mb-4 transition-all duration-300 bg-white border rounded-lg border-gray1"
            id="filter-body">
            <button type="button" class="flex items-center justify-between w-full" id="filter-btn">
                <span>Report Detail</span>

                <span class="flex items-center justify-center bg-white border rounded-lg w-9 h-9 border-gray1"
                    id="filter-icon">
                    <img src="{{ asset('new/src/assets/icons/arrow-right-table.svg') }}" alt="" />
                </span>
            </button>



            <form class="hidden mt-6 md:grid-cols-2 md:gap-x-20 lg:gap-x-20 gap-y-6" id="get-orders-form">
                @csrf

                <!-- Type Select -->
                <div>
                    <label for="Type">Report name</label>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        style="width: 100%;" name="reportSelect" id="reportSelect">

                    </select>
                </div>
                <!-- Status Select -->
                <label class="flex flex-col gap-3">
                    <span>Type</span>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md status focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        style="width: 100%;" id="export-type" name="type">

                        <option value="" selected="selected" disabled>Type</option>
                        <option value="csv">csv</option>
                        <option value="excel">excel</option>
                    </select>
                </label>

                <!-- Status Select -->
                <label class="flex flex-col gap-3">
                    <span>Status</span>
                    <select
                        class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3 status"
                        multiple="multiple" style="width: 100%;" name="status[]" id="status">
                        <option value="-1">All</option>
                        @foreach (App\Enum\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                        @endforeach
                    </select>
                </label>




                <div class="row">
                    <link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

                    {{-- <span>From:- To:- </span> --}}
                    {{-- <input type="text"
                        class="shadow-none w-full border mt-2 rounded-md focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                        name="date" value="" id="date" /> --}}
                    <div class="col-md-6">
                        <span>Data From</span>
                        <div class="form-floating mb-3">
                            <input type='text' class="form-control datetimepicker1 fromtime"
                                value="{{ @request()->fromtime }}" autocomplete="off" name="fromtime" />
                            <label for="tb-fnameddd">Data From</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <span>Data To</span>
                        <div class="form-floating mb-3">
                            <input type='text' class="form-control datetimepicker1 totime"
                                value="{{ @request()->totime }}" autocomplete="off" name="totime" />
                            <label for="tb-fnameddd">Data To</label>
                        </div>
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
                </div>



                @if (auth()->user()->user_role == App\Enum\UserRole::CLIENT)
                    <label class="flex flex-col gap-3">
                        <span>Branch</span>
                        <select
                            class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                            style="width: 100%;" id="client_branch" name="client_branch">

                            <option value="" selected="selected" disabled>Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}"> {{$branch->name}} </option>
                                
                            @endforeach
                        </select>
                    </label>
                @endif



                <!-- Button -->
                <div class="flex items-center justify-end col-span-2 md:justify-end">
                    <button type="button" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1" id="get-order-list">
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
        </div>
        </form>



        <!-- Table -->
        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-between mb-4 border-b md:flex-row">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">
                        Reports List
                    </h3>
                </div>

                <div class="flex flex-col mb-3" id="export-div" style="display: none">
                    <form action="{{ url('admin/export-order-data-table') }}" method="GET" style="display:inline;">
                        @csrf
                        {{-- @method('POST') --}}
                        <input type="hidden" name="reportSelect-export-form" id="reportSelectExport">
                        <input type="hidden" name="type-export-form" id="typeExportInput">
                        <input type="hidden" name="date-export-form" id="dateExportInput">
                        <input type="hidden" name="fromtime" id="fromtimeExportInput">
                        <input type="hidden" name="totime" id="totimeExportInput">
                        <input type="hidden" name="status-export-form[]" id="statusExportInput">
                        <input type="hidden" name="client_branch_form" id="client_branch_form">


                        <div class="flex items-center justify-end col-span-2 md:justify-end">
                            <button class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1" type="submit">


                                <span>Export</span>
                            </button>
                        </div>
                    </form>



                </div>

            </div>

            <!-- Table -->
            <div class="w-full overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 " id="order-list">
                    <thead class="bg-gray-50">
                        <!-- <tr id="tableHeader">

                                </tr> -->
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->












        <br>

        <div class="collapsible"
            class="h-16 px-4 py-3 mb-4 transition-all duration-300 bg-white border rounded-lg border-gray1">

            <button type="button"
                class="flex items-center justify-between w-full px-3 py-3 mt-3 text-sm border rounded-md toggleButton border-gray1 text-black1 bg-white ">
                <div class="flex items-center gap-4">
                    <span class="w-0.5 h-6 bg-gray7"></span>
                    <span>Templates <span>
                </div>
                <span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.92 8.94995L13.4 15.47C12.63 16.24 11.37 16.24 10.6 15.47L4.07996 8.94995"
                            stroke="#A30133" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
            </button>
            <div
                class="mt-4 overflow-hidden text-sm transition-all duration-500 ease-in-out collapseContent max-h-0 md:text-base bg-white ">
                <div class="flex flex-col gap-3  rounded shadow-md  ">
                    <br>
                    <h3 class="card-title"> &nbsp; &nbsp;
                        <a href="#" id="create-client-branch" class="open-drawer" data-drawer="Shifts">
                            <span style="color: #bd69ad;"> +Add New Template </span>
                        </a>
                    </h3>



                    <table id="branches-table" class="table table-head-fixed ">
                        <thead>
                            <tr>

                                <th>Name</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Branches data -->
                        </tbody>
                    </table>

                </div>
            </div>
        </div>



        <!-- Table -->
        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-between mb-4 border-b md:flex-row">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">
                        Reports history list
                    </h3>
                </div>



            </div>

            <!-- Table -->
            <div class="w-full overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 " id="history-list">
                    <thead class="bg-gray-50">
                        <tr>
                            <td>ID</td>
                            <td>Name</td>
                            <td>Status</td>
                            <td>Type</td>
                            <td>From</td>
                            <td>To</td>
                            <td>Created at</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>







    </div>








    <div id="drawer" data-drawer="Shifts"
        class="fixed top-0 right-0 z-50 min-h-full  p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
        <div class="flex flex-col h-screen overflow-scroll">
            <div class="flex items-center justify-between mb-6">
                <h5 class="text-xl font-bold text-blue-gray-700">
                    New Shift
                </h5>
                <button id="close-drawer" class="text-gray-500 close-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-2 p-8 overflow-scroll">



                <form id="checkboxForm">
                    @csrf
                    <div class="row">
                        <input hidden name="id" id="template_id">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="form-control" id="report_name_creator" name="report_name_creator"
                                    placeholder="Name" style="width: 100%;">

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <select id="type_report_creator" name= "type_report_creator" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected="selected" disabled>Type</option>
                                    <option value="delivery">Delivery</option>



                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="row">
                        <!-- First column -->
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Business date" id="businessDate"
                                    name="business_date">
                                <label class="form-check-label" for="businessDate">Business date</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Tracking ID" name="tracking_id"
                                    id="trackingId">
                                <label class="form-check-label" for="trackingId">Tracking ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Ref ID" name = "ref_id"
                                    id="refId">
                                <label class="form-check-label" for="refId">Ref ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="integration_channel"
                                    value="Integration channel" id="integrationChannel">
                                <label class="form-check-label" for="integrationChannel">Integration
                                    channel</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="delivered_by" value="Delivered by"
                                    id="deliveredBy">
                                <label class="form-check-label" for="deliveredBy">Delivered by</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="driver_id" value="Driver ID"
                                    id="driverId">
                                <label class="form-check-label" for="driverId">Driver ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="driver_name" value="Driver name"
                                    id="driverName">
                                <label class="form-check-label" for="driverName">Driver name</label>
                            </div>
                        </div>

                        <!-- Second column -->
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="driver_group" value="Driver group"
                                    id="driverGroup">
                                <label class="form-check-label" for="driverGroup">Driver group</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="customer_name"
                                    value="Customer name" id="customerName">
                                <label class="form-check-label" for="customerName">Customer name</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="customer_phone"
                                    value="Customer phone" id="customerPhone">
                                <label class="form-check-label" for="customerPhone">Customer phone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="shop_id" value="Shop ID"
                                    id="shopId">
                                <label class="form-check-label" for="shopId">Shop ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Shop" name="shop"
                                    id="shop">
                                <label class="form-check-label" for="shop">Shop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_id" value="Branch ID"
                                    id="branchId">
                                <label class="form-check-label" for="branchId">Branch ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch" id="branch"
                                    value="Branch">
                                <label class="form-check-label" for="branch">Branch</label>
                            </div>
                        </div>

                        <!-- Third column -->
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_area" id="branchArea"
                                    value="Branch area">
                                <label class="form-check-label" for="branchArea">Branch area</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="city" value="City"
                                    id="city">
                                <label class="form-check-label" for="city">City</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="area" value="Area"
                                    id="area">
                                <label class="form-check-label" for="area">Area</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Order type" name="order_type"
                                    id="orderType">
                                <label class="form-check-label" for="orderType">Order type</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Client Account Number" name="client_account_number"
                                    id="client_account_number">
                                <label class="form-check-label" for="orderType">Client Account Number</label>
                            </div>



                        </div>

                        <!-- Fourth column -->
                        <div class="col-md-3">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Order status" name="order_status"
                                    id="orderStatus">
                                <label class="form-check-label" for="orderStatus">Order status</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Created Date" name="created_date"
                                    id="createdDate">
                                <label class="form-check-label" for="createdDate">Created Date</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Arrived To Pickup Time"
                                    name="arrived_to_pickup_time" id="arrivedToPickupTime">
                                <label class="form-check-label" for="arrivedToPickupTime">Arrived To Pickup
                                    Time</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Pickup Time" name="pickup_time"
                                    id="pickupTime">
                                <label class="form-check-label" for="pickupTime">Pickup Time</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Arrived To Dropoff Time"
                                    name="arrived_to_dropoff_time" id="arrivedToDropoffTime">
                                <label class="form-check-label" for="arrivedToDropoffTime">Arrived To Dropoff
                                    Time</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Delivered Time"
                                    name="delivered_time" id="deliveredTime">
                                <label class="form-check-label" for="deliveredTime">Delivered Time</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Preparation Time"
                                    name="preparation_time" id="preparationTime">
                                <label class="form-check-label" for="preparationTime">Preparation Time</label>
                            </div>







                        </div>


                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Assignment Time"
                                    name="assignment_time" id="assignmentTime">
                                <label class="form-check-label" for="assignmentTime">Assignment Time</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Accepted Time"
                                    name="accepted_time" id="acceptedTime">
                                <label class="form-check-label" for="acceptedTime">Accepted Time</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Assign" name="assign"
                                    id="assign">
                                <label class="form-check-label" for="assign">Assign</label>
                            </div>

                        </div>

                        <!-- Second column -->


                        <!-- Third column -->
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Order Value" name="order_value"
                                    id="orderValue">
                                <label class="form-check-label" for="orderValue">Order Value</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Delivery Charge"
                                    name="delivery_charge" id="deliveryCharge">
                                <label class="form-check-label" for="deliveryCharge">Delivery Charge</label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Payment Type" name="payment_type"
                                    id="paymentType">
                                <label class="form-check-label" for="paymentType">Payment Type</label>
                            </div>



                        </div>

                        <!-- Fourth column -->
                        <div class="col-md-3">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Pickup Distance KM"
                                    name="pickup_distance_km" id="pickupDistanceKm">
                                <label class="form-check-label" for="pickupDistanceKm">Pickup Distance KM</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Delivery Distance KM"
                                    name="delivery_distance_km" id="deliveryDistanceKm">
                                <label class="form-check-label" for="deliveryDistanceKm">Delivery Distance
                                    KM</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Order Comments"
                                    name="order_comments" id="orderComments">
                                <label class="form-check-label" for="orderComments">Order Comments</label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Fail Reason" name="fail_reason"
                                    id="failReason">
                                <label class="form-check-label" for="failReason">Fail Reason</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Cancel Reason"
                                    name="cancel_reason" id="cancelReason">
                                <label class="form-check-label" for="cancelReason">Cancel Reason</label>
                            </div>
                        </div>
                    </div>


                </form>

                <div class="flex items-center justify-center pt-16">
                    <button type="button" class="p-3 !px-20 text-xl text-white rounded-md bg-blue1"
                        id="save-template-btn">Save</button>
                </div>



            </div>
        </div>
    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>

<script>
    $(document).ready(function() {

        toggleGetOrderButton();


        $('.status').select2({
            placeholder: "Status",
            allowClear: true
        });
        

        $('#client_branch').select2({
            allowClear: true,
            placeholder: 'Branch',
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


        $('#save-template-btn').on('click', function() {
            var formData = $('#checkboxForm').serialize();

            $.ajax({
                url: '{{ route('save-report-template') }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    closeDrawer();
                    fetchReportTemplates()
                    branchesTable.ajax.reload();

                    $('#checkboxForm')[0].reset();

                    // Optionally, if you're using Select2 for select inputs, reset them like this
                    $('#checkboxForm select').val(null).trigger('change');

                    // If you want to manually uncheck all checkboxes in the form
                    $('#checkboxForm input[type="checkbox"]').prop('checked', false);

                    // Remove validation error messages if any exist
                    $('#checkboxForm .is-invalid').removeClass('is-invalid');
                    $('#checkboxForm .invalid-feedback').remove();
                    console.log(response.message); // Use the message from the response
                },
                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {

                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');
                            inputElement.after(
                                '<span class="text-danger invalid-feedback" role="alert">' +
                                value[0] + '</span>');
                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });


        $('#reportSelect').select2({
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




        $('#reportSelect').on('change', function() {

            var selectedOption = $(this).find('option:selected');
            var columnsData = selectedOption.data('columns');
            var selectedValue = selectedOption.val();


            if (columnsData) {
                try {
                    var columns;
                    // Check if it's a string or already an object
                    if (typeof columnsData === 'string') {
                        columns = JSON.parse(columnsData);
                    } else {
                        columns = columnsData;
                    }

                    // Update table headers and reinitialize DataTable

                    // initializeOrdersDataTable(columns);
                    // updateTableHeaders(columns);
                } catch (e) {
                    console.error('Error parsing columns data:', e);
                }
            }
        });

        function updateTableHeaders(columns) {
            console.log(columns);
            
            var headerRow = $('#order-list thead tr');
            headerRow.empty();

            // Append new headers based on the selected columns
            $.each(columns, function(key, value) {
                headerRow.append('<th>' + value + '</th>');
            });

            // Clear the body of the table (optional to remove old data)
            $('#order-list tbody').empty();
        }

        initializeHistoriesDataTable();



        fetchReportTemplates()

        function toggleGetOrderButton() {
            console.log('toggle');

            if ($('#export-type').val() && $('#reportSelect').val()) {
                $('#get-order-list').prop('disabled', false);
            } else {
                $('#get-order-list').prop('disabled', true);
            }
        }

        $('#export-type, #reportSelect').on('change', function() {
            console.log('cliced');

            toggleGetOrderButton();
        });

        $('#get-order-list').click(function() {

            var selectedOption = $('#reportSelect').find('option:selected');
            var columnsData = selectedOption.data('columns');



            var reportSelectValue = $('#reportSelect').val();
            var typeValue = $('#export-type').val();
            var dateValue = $('#date').val();
            var statusValues = $('#status').val(); // This will be an array
            var datefromtime = $('.fromtime').val();
            var datetotime = $('.totime').val();
            var clientBranch = $('#client_branch').val();
            if(clientBranch) {
                $('#client_branch_form').val(clientBranch);
            }
            $('#reportSelectExport').val(reportSelectValue);
            $('#typeExportInput').val(typeValue);
            $('#dateExportInput').val(dateValue);
            $('#fromtimeExportInput').val(datefromtime);
            $('#totimeExportInput').val(datetotime);
            // Convert status array to comma-separated string if it's not empty
            if (statusValues && statusValues.length > 0) {
                $('#statusExportInput').val(statusValues.join(
                    ',')); // Join the array into a comma-separated string
            } else {
                $('#statusExportInput').val(''); // Clear if no status selected
            }
            $('#export-div').css('display', 'block');

            if (columnsData) {
                // Use the columnsData directly
                var columns = columnsData;

                console.log('test', columns);

                initializeOrdersDataTable(columns);
                updateTableHeaders(columns);
                // Serialize form data and make the AJAX request
                var formData = $('#get-orders-form').serialize();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('save-history') }}',
                    data: formData,
                    success: function(response) {
                        // Success callback
                    }
                });


                initializeHistoriesDataTable();
            }
        });

        $('#create-client-branch').click(function() {
            $('#checkboxForm')[0].reset();

            // Optionally, if you're using Select2 for select inputs, reset them like this
            $('#checkboxForm select').val(null).trigger('change');

            // If you want to manually uncheck all checkboxes in the form
            $('#checkboxForm input[type="checkbox"]').prop('checked', false);

            // Remove validation error messages if any exist
            $('#checkboxForm .is-invalid').removeClass('is-invalid');
            $('#checkboxForm .invalid-feedback').remove();

        })



        $(document).on('click', '.edit-template', function(e) {
            e.preventDefault();
            $('#checkboxForm')[0].reset();

            // Optionally, if you're using Select2 for select inputs, reset them like this
            $('#checkboxForm select').val(null).trigger('change');

            // If you want to manually uncheck all checkboxes in the form
            $('#checkboxForm input[type="checkbox"]').prop('checked', false);

            // Remove validation error messages if any exist
            $('#checkboxForm .is-invalid').removeClass('is-invalid');
            $('#checkboxForm .invalid-feedback').remove();
            const ID = $(this).data('id');
            console.log(ID)

            const drawer = document.getElementById('drawer');



            drawer.classList.remove('translate-x-full');

            const updateURL = '{{ url('admin/edit-report-template') }}/' + ID + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    $('#report_name_creator').val(response.template.name);
                    $('#template_id').val(response.template.id);
                    $('#type_report_creator').val(response.template.template_type).trigger(
                        'change');

                    $.each(response.template.columns, function(key, value) {

                        let checkbox = $('input[type="checkbox"][name="' + key +
                            '"]');


                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });



                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });




        $(document).on('click', ' .delete-template', function(e) {

            e.preventDefault();
            e.stopPropagation(); // Prevent the dropdown from closing
            const ID = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-report-template') }}/${ID}`;

            if (confirm('Are you sure you want to delete this template?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        fetchReportTemplates();
                        branchesTable.ajax.reload(); // Refresh the templates after deletion
                    },
                    error: function(xhr) {
                        console.log('Error deleting template');
                    }
                });
            }
        });




        var branchesTable = $('#branches-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('get-template-report-data-table') }}",
                "type": "GET",

            },
            "columns": [{
                    "data": "name"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {

                        return `
                         <button
                                        type="button"
                                       data-drawer="Shifts"
                                        data-id="${data.id}"
                                        data-drawer="Individuals"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg open-drawer border-gray10 edit-template">
                                        <img src="{{ asset('new/src/assets/icons/edit.svg') }}" alt="" />
                                    </button>`;
                    },
                    "orderable": false
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                                <form method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')


                                     <button
                                        type="button"
                                        data-id="${data.id}"
                                        class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10 delete-template">
                                        <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="" />
                                    </button>
                                </form>`;
                    },
                    "orderable": false
                }
            ],
            "pageLength": 3,
            "lengthChange": false
        });



        function initializeHistoriesDataTable() {
            if ($.fn.DataTable.isDataTable('#history-list')) {
                $('#history-list').DataTable().destroy();
            }




            $('#history-list').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('history-list') }}",
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
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },

                    {
                        "data": "status"
                    },
                    {
                        "data": "type"
                    },
                    {
                        "data": "from"
                    },
                    {
                        "data": "to"
                    },
                    {
                        "data": "created_at"
                    },

                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const form = `
                                <form action="{{ url('admin/export-orders') }}/${data.id}" method="POST" style="display:inline;" id="export-form-${data.id}">
                                    @csrf
                                    @method('GET')

                                     <button
                type="submit"
                class="flex items-center justify-center w-10 h-10 bg-white border rounded-full md:w-12 md:h-12 border-gray1"
              >
                <svg
                  width="24"
                  height="25"
                  viewBox="0 0 24 25"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M9 22.5H15C20 22.5 22 20.5 22 15.5V9.5C22 4.5 20 2.5 15 2.5H9C4 2.5 2 4.5 2 9.5V15.5C2 20.5 4 22.5 9 22.5Z"
                    stroke="#A30133"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M2 13.5H5.76C6.52 13.5 7.21 13.93 7.55 14.61L8.44 16.4C9 17.5 10 17.5 10.24 17.5H13.77C14.53 17.5 15.22 17.07 15.56 16.39L16.45 14.6C16.79 13.92 17.48 13.49 18.24 13.49H21.98"
                    stroke="#A30133"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M10.34 7.5H13.67"
                    stroke="#A30133"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M9.5 10.5H14.5"
                    stroke="#A30133"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </button>
                                </form>
                            `;
                            return form;
                        },
                        "orderable": false
                    }

                ],
                "pageLength": 10,
                "lengthChange": false
            });
        }






















        function initializeOrdersDataTable(columns) {
            // Destroy DataTable if it already exists to prevent duplicate initialization
            if ($.fn.DataTable.isDataTable('#order-list')) {
                $('#order-list').DataTable().clear(); // Clear any remaining data
                $('#order-list').DataTable().destroy(); // Destroy the instance fully
                $('#order-list').empty(); // Clear the table structure
            }

            // Prepare the columns array for DataTable initialization
            var columnDefs = [];
            $.each(columns, function(key) {
                columnDefs.push({
                    data: key, // Use the key from the columnsData as the data source
                });
            });

            $('#order-list').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('order-list') }}",
                    "type": "GET",
                    "data": function(d) {
                        $.each($('#get-orders-form').serializeArray(), function(_, field) {
                            d[field.name] = field.value;
                        });
                        d['status'] = $('#status').val();
                        d['fromtime'] = $('.fromtime').val();
                        d['totime'] = $('.totime').val();

                    },
                    "dataSrc": function(json) {
                        return json.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },
                "columns": columnDefs, // Use dynamically generated column definitions
                "pageLength": 10,
                "lengthChange": false
            });
        }





    });


    function fetchReportTemplates() {
        $.ajax({
            type: 'GET',
            url: '{{ route('get-template-name') }}',
            success: function(response) {
                if (response.templates) {

                    // Clear existing options and add placeholder and add new option
                    $('#reportSelect').html(
                        '<option value="" selected="selected" disabled>Report Name</option>'
                    );

                    // Append options with edit and delete icons
                    $.each(response.templates, function(index, template) {
                        var option = new Option(template.name, template.id, false, false);
                        // Set custom data for columns
                        $(option).attr('data-columns', JSON.stringify(template.columns));
                        $('#reportSelect').append(option);
                    });

                    // Initialize Select2
                    $('#reportSelect').select2({
                        templateResult: function(option) {
                            if (!option.id) {
                                return option.text; // Return the text for the placeholder
                            }

                            // Create a custom option with icons only if it's not the "modal" option
                            var isModal = option.id ===
                                "modal"; // Adjust this to match your actual modal value

                            var $option = $(`
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>${option.text}</span>

                                        </div>
                                    `);

                            return $option;
                        },
                        templateSelection: function(option) {
                            return option.text; // Display only the text in the selected item
                        }
                    });

                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching report templates:', error);
            }
        });
    }

    $(function() {
        $('input[name="date"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false
        }, function(start, end) {
            // Update the input value manually
            $('input[name="date"]').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
</script>

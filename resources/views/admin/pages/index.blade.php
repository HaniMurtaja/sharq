@extends('admin.layouts.app')

<style>
    #pac-input {
        position: absolute;
        right: 53px;
        height: 40px;

        background-color: #fff !important;
        border-radius: 0 !important;
        padding: 10px !important;
        width: 250px !important;
        box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
        left: unset !important;
        border-radius: .3rem !important;
    }

    .gm-fullscreen-control {
        border-radius: .3rem !important;
    }
</style>
<style>
    .stepwizard-step p {
        margin-top: 10px;
    }

    .stepwizard-row {
        display: table-row;
    }

    .stepwizard {
        display: table;
        width: 100%;
        position: relative;
    }

    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }

    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-order: 0;
    }

    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }

    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }


    .modal-backdrop {
        z-index: 1040;
        /* Default Bootstrap backdrop z-index */
    }

    .modal {
        z-index: 1050;
        /* Default Bootstrap modal z-index */
    }

    #drawer-overlay {
        z-index: 1050;
        /* Behind the drawer but above other elements */
        position: fixed;
        inset: 0;
        /* Full screen */
        background: rgba(0, 0, 0, 0.5);
        /* Semi-transparent overlay */
        display: none;
        /* Initially hidden */
    }

    #drawer {
        z-index: 1060;
        /* Higher than the modal */
        position: fixed;
        top: 0;
        right: 0;
        height: 100%;
        width: 1000px;
        /* Adjust width as needed */
        background: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
        /* Optional shadow */
        transform: translateX(100%);
        /* Initially off-screen */
        transition: transform 0.3s ease-in-out;
        /* Smooth open/close */
    }
</style>


<style>
    .toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1055;
    }

    .toast {
        display: none;
        /* Hidden by default */
        max-width: 350px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.25rem;
        background-color: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .toast-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .toast-body {
        font-size: 0.875rem;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        line-height: 1;
        color: #000;
        opacity: 0.5;
        cursor: pointer;
    }

    .btn-close:hover {
        opacity: 0.75;
    }

    .dispatcher-tab-content {
        overflow-y: scroll;
        height: 73vh;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .dispatcher-tab-content::-webkit-scrollbar {
        display: none;
    }
</style>

@include('admin.includes.popup-style')

@include('admin.includes.assign-driver-style')
@include('admin.includes.driver-style')
@include('admin.includes.order-details-style')


@section('content')
    <div class="w-full">
        <!-- Navbar -->

        <!-- Drawer Overlay -->
        <div id="drawer-overlay" data-drawer="Dispatcher"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay"></div>


        <!-- New Individual -->
        <!-- Drawer -->
        @include('admin.pages.dispatchers.create-order')
        <!-- End Drawer -->


        <div class="flex  flex-col px-2">
            <div class="bg-white  rounded-lg">
                <!-- Navigation Tabs -->
                <div class="flex d-none items-center justify-between border-b">
                    <div class="flex  flex-col mb-4">
                        <h3 class="mb-2 text-base font-medium text-black">
                            Dispatcher
                        </h3>
                    </div>

                    <!-- <a href="#"> -->
                    <button
                        class="flex items-center justify-center w-[11.25rem] h-12 gap-3 px-4 py-2 text-white rounded-md bg-blue1 border-blue1 open-drawer"
                        data-drawer="Dispatcher" onclick="resetModalTab()">
                        <span>+ New</span>
                    </button>
                    <!-- </a> -->
                </div>

                <div class="flex flex-col w-full min-h-[70vh] md:flex-row">

                    <!-- Sidebar -->
                    <div
                        style="width:33%; background-color: #f9f9f9; padding: 12.8px 20.8px 20.8px; border-radius: 22.6px;height: 96vh">
                        <div class="flex items-center justify-between pb-3 border-bottom">
                            <div class="flex  flex-col">
                                <h3 class="text-base font-bold text-black">
                                    Dispatcher
                                </h3>
                            </div>

                            <!-- <a href="#"> -->
                            <button
                                class="flex items-center justify-center  gap-3 px-4 py-2 text-white rounded-md open-drawer"
                                style="width: 96px;background-color: #f46624;border-radius: 9.6px; line-height: normal;}"
                                data-drawer="Dispatcher" onclick="resetModalTab()">
                                <span style="font-size: 11.2px; font-weight: 600">+ New</span>
                            </button>
                            <!-- </a> -->
                        </div>
                        <!-- Search -->
                        <div class="flex items-center justify-between gap-3 p-2 my-3 bg-white  rounded-md">


                            <!-- Input -->
                            <input type="text" placeholder="Search here..."
                                class=" bg-transparent outline-none border-0 w-100"
                                style="font-size: 11.2px; color: #585858" id="order_search" />
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

                        <div id="jobs-screen">
                            <div class="flex items-center justify-between jobs">
      <h1 >Jobs</h1>
      <p>3 top results</p>
</div>

      <!-- Tabs navigation -->
      <ul class="nav nav-tabs" id="jobsTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
            Pending
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">
            Active
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
            Completed
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">
            Cancelled
          </button>
        </li>
      </ul>

      <!-- Tabs content -->
      <div class="tab-content mt-3" style="display:block !important;">
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">

            <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>

        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>


</div>



        <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">

        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>
        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>






        </div>
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">

        </div>
        <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">

        <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup=""
                                            ondblclick="openAssignDriverModal()"
                                            ondblclick="openAssignDriverModal()"
                                            onclick="openOrderPopup(this)">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>Test Branch</span>
                            <span>Moharum bek , Alexandria</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#407</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>17:09</p>
                        </div>

                    </div>
        </div>

        </div>
      </div>
    </div>



                        <div class="flex flex-col demondDriver">

                            <!-- Tabs -->
                            <div class="grid grid-cols-2 border-top dispatcher-tabs">
                                <button type="button"
                                    class="p-2 text-sm font-medium text-center border-b dispatch-tab-active focus:outline-none"
                                    style="font-size: 9.6px" data-tab="On Demand">
                                    <span>@livewire('order')</span>
                                </button>
                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher'))
                                    <button type="button"
                                        class="p-2 text-sm text-center border-b border-gray1 focus:outline-none"
                                        style="font-size: 9.6px" data-tab="Drivers">
                                        <span>@livewire('drivers')</span>
                                    </button>
                                @endif

                            </div>

                            <!-- On Demand Tab Content -->
                            <div class="dispatcher-tab-content px-2" data-tab="On Demand">

                            <!-- Filter -->
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
                                        <img src="/new/src/assets/icons/eye.svg" alt="" />
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
                    <div class="bg-white border md:ml-6 md:w-3/4 border-gray1 md:mt-0" style="border-radius: 22.6px">
                        <!-- <div style="width: 100%"> -->

                        @livewire('dispatcher')



                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-- <div class="top-0 p-3 toast-container position-fixed end-0" style="z-index: 1055;">
                                                <div id="toastCreateOrder" class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                                                    data-delay="5000">
                                                    <div class="toast-header">
                                                        <img src="..." class="mr-2 rounded" alt="...">
                                                        <strong class="mr-auto">Notification</strong>
                                                        <small class="text-muted">Just now</small>
                                                        <button type="button" class="mb-1 ml-2 close" data-dismiss="toast" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="toast-body">
                                                        Hello, world! This is a toast message.
                                                    </div>
                                                </div>
                                            </div> -->

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

@include('livewire.assign-worker')
@include('livewire.order-history')
@include('livewire.order-details')

@include('admin.pages.dispatchers.scripts')




<script defer>
   document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search-input");
  const clearBtn = document.getElementById("clear-btn");
  const jobsScreen = document.getElementById("jobs-screen");
    const demondDriver = document.querySelector(".demondDriver");

  searchInput.addEventListener("input", toggleDisplay);
  searchInput.addEventListener("focus", toggleDisplay);


  clearBtn.addEventListener("click", function () {
    searchInput.value = "";
    jobsScreen.style.display = "none";
    clearBtn.style.display = "none";
    searchInput.focus();
  });

  function toggleDisplay() {
    const value = searchInput.value.trim();
    if (value !== "") {
      jobsScreen.style.display = "block";

      demondDriver.style.display = "none";
    } else {
      jobsScreen.style.display = "none";

      demondDriver.style.display = "block";
    }
  }
});

</script>

<style>
    .nav-link.active {
        border: none !important;
        border-bottom: 1px solid #A30133 !important;
        font-weight: 600;
    }

    .nav-link:hover,
    .nav-link:focus {
        border-left: none;
        border-right: none;
        border-top-color: #fff !important;
    }

    .sideSection #jobs-screen .nav-tabs .nav-link.active {
        color: #A30133 !important;
    }
</style>
<div id="jobs-screen" style="display: none">
    <ul class="nav nav-tabs" id="ordersearchTabs" role="tablist">
        <li class="nav-item w-50" role="presentation">
            <button
                class="nav-link w-100 active p-2 text-sm font-medium text-center border-b dispatch-tab-active focus:outline-none"
                style="font-size: 9.6px;" id="pending-tab" data-bs-toggle="tab" data-bs-target="#orderActive"
                type="button" role="tab" aria-controls="orderActive">
                <span class=""> Active </span>
            </button>
        </li>
        <li class="nav-item w-50" role="presentation">
            <button
                class="nav-link w-100 p-2 text-sm font-medium text-center border-b dispatch-tab-active focus:outline-none"
                style="font-size: 9.6px;" id="completed-tab" data-bs-toggle="tab" data-bs-target="#ordercompleted"
                type="button" role="tab" aria-controls="ordercompleted">
                Completed
            </button>
        </li>

    </ul>
    <div class="flex items-center justify-between jobs">
        <h1>Jobs</h1>
        <p id="search_order_count"> </p>
    </div>




    <div id="selectedOrdersSearch"
        class=" bg-white rounded-lg fs-96 d-none justify-content-between align-items-center px-7125 " style=font-weight:
        bold;">
        <div class="selectedOrdersCount"></div>
        <div class="dropdown">
            <button type="button" class="flex items-center justify-center gap-1 dropdown-toggle show"
                id="selectedOrdersSearchDropdown" data-bs-toggle="dropdown" aria-expanded="true">
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

            <ul class="dropdown-menu selectedOrdersDropdown" aria-labelledby="selectedOrdersSearchDropdown"
                style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(0px, 18.4px, 0px);"
                data-popper-placement="bottom-end">
                <li><a class="dropdown-item assign-driver-multe-orders" data-bs-toggle="modal" data-bs-target="#exampleModal" href="#">Assign</a></li>
                <li>
                    <a class="dropdown-item unassign-driver-multi-orders"  href="#">UnAssign</a>
                </li>

                <li><a class="dropdown-item complate-driver-mulite-orders"  href="#">Complete</a></li>
                <li><a class="dropdown-item cancel-driver-mulite-orders " data-bs-toggle="modal" data-bs-target="#cancelRequestModal"
                        href="#">Cancel</a></li>
            </ul>
        </div>
    </div>





    <!-- Tabs navigation -->

    <div id="jobsTabs">
        {{-- <div id="loading_section"></div> --}}
    </div>
    {{--    <ul class="nav nav-tabs" id="jobsTabs" role="tablist"> --}}
    {{--        <li class="nav-item" role="presentation"> --}}
    {{--            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-order" --}}
    {{--                type="button" role="tab" aria-controls="pending-order" aria-selected="true"> --}}
    {{--                Pending --}}
    {{--            </button> --}}
    {{--        </li> --}}
    {{--        <li class="nav-item" role="presentation"> --}}
    {{--            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active_order" type="button" --}}
    {{--                role="tab" aria-controls="active_order" aria-selected="false"> --}}
    {{--                Active --}}
    {{--            </button> --}}
    {{--        </li> --}}
    {{--        <li class="nav-item" role="presentation"> --}}
    {{--            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed_order" --}}
    {{--                type="button" role="tab" aria-controls="completed_order" aria-selected="false"> --}}
    {{--                Completed --}}
    {{--            </button> --}}
    {{--        </li> --}}
    {{--        <li class="nav-item" role="presentation"> --}}
    {{--            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#CancelledOrder" --}}
    {{--                type="button" role="tab" aria-controls="CancelledOrder" aria-selected="false"> --}}
    {{--                Cancelled --}}
    {{--            </button> --}}
    {{--        </li> --}}
    {{--    </ul> --}}

    <!-- Tabs content -->
    {{-- <div class="tab-content mt-3" style="display:block !important;">
        <div class="tab-pane  tab-pane-orders fade show active" style="height: 500px; overflow-y: auto;" id="pending-order"
            role="tabpanel" aria-labelledby="pending-tab">



        </div>



        <div class="tab-pane tab-pane-orders fade" id="active_order" role="tabpanel"
            style="height: 500px; overflow-y: auto;" aria-labelledby="active-tab">







        </div>
        <div class="tab-pane tab-pane-orders fade" id="completed_order" role="tabpanel"
            style="height: 500px; overflow-y: auto;" aria-labelledby="completed-tab">

        </div>
        <div class="tab-pane tab-pane-orders fade" id="CancelledOrder" role="tabpanel"
            style="height: 500px; overflow-y: auto;" aria-labelledby="cancelled-tab">



        </div>
    </div> --}}



</div>

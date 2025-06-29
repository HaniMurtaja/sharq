<style>
    .sidebarScroll::-webkit-scrollbar {
    width: 0; /* Remove scrollbar width */
    height: 0; /* Remove scrollbar height */
}

/* Hide scrollbar for other browsers (Firefox, etc.) */
.sidebarScroll {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}
</style>
<!-- Sidebar -->
 <div class="lg:w-[260px] overflow-hidden   fixed top-0 lg:left-0 left-full h-screen z-50"
     style="height: 1150px;" id="sidebar">
     <div class="w-full h-screen bg-sidebar rounded-tr-[32px] rounded-br-[32px] p-6 py-16" style="position:fixed; width: fit-content;">
         <img src="{{ asset('new/src/assets/images/logo.png') }}" class="sidebar_logo" alt="" />

         <button type="button" id="sidebar_toggle"
             class="w-7 h-7 rounded-full bg-white border border-gray1 justify-center items-center lg:flex hidden absolute -right-2.5 top-6 shadow-xl">
             <img src="{{ asset('new/src/assets/icons/arrow-left.svg') }}" alt="" />
         </button>
         <button type="button" id=""
             class="absolute flex items-center justify-center bg-white border rounded-full shadow-xl w-7 h-7 border-gray1 lg:hidden right-5 top-5"
             onclick="closeSidebarInMobile()">
             <img src="{{ asset('new/src/assets/icons/arrow-left.svg') }}" alt="" />
         </button>

         @if (auth()->user()->hasRole('admin'))

             <div class="flex flex-col gap-2 mt-10 overflow-y-auto sidebarScroll" style="max-height: calc(100vh - 150px);">
                 <!-- Dashboard -->
                 <a href="{{ route('dashboard') }}"
                     class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                     <img src="{{ asset('new/src/assets/icons/dashboardIcon.svg') }}" alt="" />
                     <h3 class="text-sm sidebar_page_title" id="sidebar-page-label">
                         Dashboard
                     </h3>
                 </a>

                 <!-- Dispatcher -->
                 <a href="{{ route('index') }}"
                     class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M3.31835 7.78248C5.12418 -0.155852 16.885 -0.146685 18.6817 7.79165C19.7359 12.4483 16.8392 16.39 14.3 18.8283C12.4575 20.6066 9.54252 20.6066 7.69085 18.8283C5.16085 16.39 2.26418 12.4391 3.31835 7.78248Z"
                             stroke="#fff" stroke-width="1.5" />
                         <path d="M12.8334 11.88L9.20337 8.25" stroke="#fff" stroke-width="1.5" stroke-miterlimit="10"
                             stroke-linecap="round" stroke-linejoin="round" />
                         <path d="M12.7966 8.28668L9.16663 11.9167" stroke="#fff" stroke-width="1.5"
                             stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>

                     <h3 class="text-sm sidebar_page_title">Dispatcher</h3>
                 </a>

                 <!-- Operators -->
                 <div class="relative">
                     <button
                         class="flex items-center justify-between w-full gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item"
                         id="sidebar_operators">
                         <div class="flex gap-4">
                             <img src="{{ asset('new/src/assets/icons/operatorsIcon.svg') }}" alt="" />
                             <h3 class="text-sm sidebar_page_title" id="sidebar-page-label">
                                 Operators
                             </h3>
                         </div>
                         <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg" class="sidebar_page_title">
                             <path fill-rule="evenodd" clip-rule="evenodd"
                                 d="M2.69041 4.6037C2.49712 4.80251 2.49712 5.12485 2.69041 5.32366L6.65 9.39639C6.74283 9.49186 6.86872 9.5455 6.99999 9.5455C7.13125 9.5455 7.25715 9.49186 7.34997 9.39639L11.3096 5.32366C11.5029 5.12485 11.5029 4.80251 11.3096 4.6037C11.1163 4.40489 10.8029 4.40489 10.6096 4.6037L6.99999 8.31644L3.39037 4.6037C3.19708 4.40489 2.8837 4.40489 2.69041 4.6037Z"
                                 fill="#fff" />
                         </svg>
                     </button>

                     <!-- opt -->
                     <div class="flex-col hidden" id="sidebar_operators_list">
                         <a href="{{ route('operators') }}"
                             class="relative flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full after:w-[1px] after:h-8 after:bg-white after:absolute after:left-[22px] after:top-6">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">Operators</h3>
                         </a>
                         <a href="{{ route('reports.billings') }}"
                             class="relative flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full after:w-[1px] after:h-8 after:bg-white after:absolute after:left-[22px] after:top-6">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">
                                 Operators Billings
                             </h3>
                         </a>
                         <a href="{{ route('operators.operator-reports') }}"
                             class="flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">Operator Report</h3>
                         </a>
                     </div>
                 </div>

                 <!-- Clients -->
                 <a href="{{ route('clients') }}"
                     class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                     <!-- Icon -->
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M16.5 6.56399C16.445 6.55482 16.3808 6.55482 16.3258 6.56399C15.0608 6.51815 14.0525 5.48232 14.0525 4.19899C14.0525 2.88815 15.1067 1.83398 16.4175 1.83398C17.7283 1.83398 18.7825 2.89732 18.7825 4.19899C18.7733 5.48232 17.765 6.51815 16.5 6.56399Z"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M15.5558 13.2375C16.8117 13.4483 18.1958 13.2283 19.1675 12.5775C20.46 11.7158 20.46 10.3041 19.1675 9.44247C18.1867 8.79163 16.7842 8.57163 15.5283 8.79163"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M5.47244 6.56399C5.52744 6.55482 5.59161 6.55482 5.64661 6.56399C6.91161 6.51815 7.91994 5.48232 7.91994 4.19899C7.91994 2.88815 6.86578 1.83398 5.55494 1.83398C4.24411 1.83398 3.18994 2.89732 3.18994 4.19899C3.19911 5.48232 4.20744 6.51815 5.47244 6.56399Z"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M6.41661 13.2375C5.16078 13.4483 3.77661 13.2283 2.80495 12.5775C1.51245 11.7158 1.51245 10.3041 2.80495 9.44247C3.78578 8.79163 5.18828 8.57163 6.44411 8.79163"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M11 13.4116C10.945 13.4025 10.8808 13.4025 10.8258 13.4116C9.56082 13.3658 8.55249 12.33 8.55249 11.0466C8.55249 9.73581 9.60666 8.68164 10.9175 8.68164C12.2283 8.68164 13.2825 9.74498 13.2825 11.0466C13.2733 12.33 12.265 13.375 11 13.4116Z"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M8.33253 16.2991C7.04003 17.1607 7.04003 18.5724 8.33253 19.4341C9.7992 20.4149 12.2009 20.4149 13.6675 19.4341C14.96 18.5724 14.96 17.1607 13.6675 16.2991C12.21 15.3274 9.7992 15.3274 8.33253 16.2991Z"
                             stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>

                     <h3 class="text-sm sidebar_page_title">Clients</h3>
                 </a>

                 <!-- Users -->
                 <a href="{{ route('users') }}"
                     class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                     <!-- Icon -->
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M11 11.0007C13.5313 11.0007 15.5833 8.94862 15.5833 6.41732C15.5833 3.88601 13.5313 1.83398 11 1.83398C8.46865 1.83398 6.41663 3.88601 6.41663 6.41732C6.41663 8.94862 8.46865 11.0007 11 11.0007Z"
                             stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M18.8742 20.1667C18.8742 16.6192 15.345 13.75 11 13.75C6.65502 13.75 3.12585 16.6192 3.12585 20.1667"
                             stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                     </svg>

                     <h3 class="text-sm sidebar_page_title">Users</h3>
                 </a>

                 <!-- Vehicles -->
                 <a href="{{ route('vehicles') }}"
                     class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                     <!-- Icon -->
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path
                             d="M14.2175 2.59412H7.78246C5.49996 2.59412 4.99579 3.73078 4.70246 5.12412L3.66663 10.0833H18.3333L17.2975 5.12412C17.0041 3.73078 16.5 2.59412 14.2175 2.59412Z"
                             stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path
                             d="M20.1575 18.1684C20.2583 19.2409 19.3967 20.1667 18.2967 20.1667H16.5733C15.5833 20.1667 15.4458 19.745 15.2717 19.2225L15.0883 18.6725C14.8317 17.9209 14.6667 17.4167 13.3467 17.4167H8.65334C7.33334 17.4167 7.14084 17.985 6.91167 18.6725L6.72834 19.2225C6.55417 19.745 6.41667 20.1667 5.42667 20.1667H3.70334C2.60334 20.1667 1.74167 19.2409 1.8425 18.1684L2.35584 12.5859C2.48417 11.2109 2.75 10.0834 5.15167 10.0834H16.8483C19.25 10.0834 19.5158 11.2109 19.6442 12.5859L20.1575 18.1684Z"
                             stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path d="M3.66667 7.33337H2.75" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path d="M19.25 7.33337H18.3334" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path d="M11 2.75V4.58333" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path d="M9.625 4.58337H12.375" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path d="M5.5 13.75H8.25" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path d="M13.75 13.75H16.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                     </svg>

                     <h3 class="text-sm sidebar_page_title">Vehicles</h3>
                 </a>

                 <!-- Orders -->
                 <a href="{{ route('orders') }}"
                     class="flex items-center justify-start gap-4 p-3 bg-transparent text-white rounded-full sidebar_page_item">
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                         <path d="M2.90576 6.82007L10.9999 11.5042L19.0391 6.84754" stroke="#fff" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" />
                         <path d="M10.9999 19.8092V11.495" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" />
                         <path
                             d="M9.10244 2.27337L4.20745 4.9959C3.09828 5.61007 2.1908 7.15005 2.1908 8.41505V13.5942C2.1908 14.8592 3.09828 16.3992 4.20745 17.0134L9.10244 19.7359C10.1474 20.3134 11.8616 20.3134 12.9066 19.7359L17.8016 17.0134C18.9108 16.3992 19.8183 14.8592 19.8183 13.5942V8.41505C19.8183 7.15005 18.9108 5.61007 17.8016 4.9959L12.9066 2.27337C11.8524 1.68671 10.1474 1.68671 9.10244 2.27337Z"
                             stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                         <path d="M15.5833 12.1366V8.78167L6.88416 3.7583" stroke="#fff" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" />
                     </svg>

                     <h3 class="text-sm sidebar_page_title">Orders</h3>
                 </a>

                 <!-- Reports -->

                 <div class="relative">
                     <button
                         class="flex items-center justify-between w-full gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item"
                         id="sidebar_reports">
                         <div class="flex gap-4">
                             <img src="{{ asset('new/src/assets/icons/reportsIcon.svg') }}" alt="" />
                             <h3 class="text-sm sidebar_page_title" id="sidebar-page-label">
                                 Reports
                             </h3>
                         </div>
                         <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg" class="sidebar_page_title">
                             <path fill-rule="evenodd" clip-rule="evenodd"
                                 d="M2.69041 4.6037C2.49712 4.80251 2.49712 5.12485 2.69041 5.32366L6.65 9.39639C6.74283 9.49186 6.86872 9.5455 6.99999 9.5455C7.13125 9.5455 7.25715 9.49186 7.34997 9.39639L11.3096 5.32366C11.5029 5.12485 11.5029 4.80251 11.3096 4.6037C11.1163 4.40489 10.8029 4.40489 10.6096 4.6037L6.99999 8.31644L3.39037 4.6037C3.19708 4.40489 2.8837 4.40489 2.69041 4.6037Z"
                                 fill="#fff" />
                         </svg>
                     </button>

                     <!-- opt -->
                     <div class="flex-col hidden" id="sidebar_reports_list">
                         <a href="{{ route('reports') }}"
                             class="relative flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full after:w-[1px] after:h-8 after:bg-white after:absolute after:left-[22px] after:top-6">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">Orders</h3>
                         </a>
                         <a href="{{ route('reports.billings') }}"
                             class="relative flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full after:w-[1px] after:h-8 after:bg-white after:absolute after:left-[22px] after:top-6">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">
                                 Drivers
                             </h3>
                         </a>
                         <a href="{{ route('operators.operator-reports') }}"
                             class="flex items-center justify-start h-10 gap-4 p-3 text-white bg-transparent rounded-full">
                             <span class="relative flex w-[14px] h-[14px] bg-white rounded-full"></span>
                             <h3 class="text-sm sidebar-page-label">Brands</h3>
                         </a>
                     </div>
                 </div>



             </div>
         @endif


         @if (auth()->user()->hasRole('Client') || auth()->user()->hasRole('dispatcher'))
         <div class="flex flex-col gap-2 mt-10 overflow-y-auto" style="max-height: calc(100vh - 150px);">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                <img src="{{ asset('new/src/assets/icons/dashboardIcon.svg') }}" alt="" />
                <h3 class="text-sm sidebar_page_title" id="sidebar-page-label">
                    Dashboard
                </h3>
            </a>

            <!-- Dispatcher -->
            <a href="{{ route('index') }}"
                class="flex items-center justify-start gap-4 p-3 text-white bg-transparent rounded-full sidebar_page_item">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.31835 7.78248C5.12418 -0.155852 16.885 -0.146685 18.6817 7.79165C19.7359 12.4483 16.8392 16.39 14.3 18.8283C12.4575 20.6066 9.54252 20.6066 7.69085 18.8283C5.16085 16.39 2.26418 12.4391 3.31835 7.78248Z"
                        stroke="#fff" stroke-width="1.5" />
                    <path d="M12.8334 11.88L9.20337 8.25" stroke="#fff" stroke-width="1.5" stroke-miterlimit="10"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12.7966 8.28668L9.16663 11.9167" stroke="#fff" stroke-width="1.5"
                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <h3 class="text-sm sidebar_page_title">Dispatcher</h3>
            </a>



            <!-- Orders -->
            <a href="{{ route('orders') }}"
                class="flex items-center justify-start gap-4 p-3 bg-transparent text-white rounded-full sidebar_page_item">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.90576 6.82007L10.9999 11.5042L19.0391 6.84754" stroke="#fff" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M10.9999 19.8092V11.495" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path
                        d="M9.10244 2.27337L4.20745 4.9959C3.09828 5.61007 2.1908 7.15005 2.1908 8.41505V13.5942C2.1908 14.8592 3.09828 16.3992 4.20745 17.0134L9.10244 19.7359C10.1474 20.3134 11.8616 20.3134 12.9066 19.7359L17.8016 17.0134C18.9108 16.3992 19.8183 14.8592 19.8183 13.5942V8.41505C19.8183 7.15005 18.9108 5.61007 17.8016 4.9959L12.9066 2.27337C11.8524 1.68671 10.1474 1.68671 9.10244 2.27337Z"
                        stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15.5833 12.1366V8.78167L6.88416 3.7583" stroke="#fff" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <h3 class="text-sm sidebar_page_title">Orders</h3>
            </a>

            <!-- Reports -->

            <a href="{{ route('reports') }}"
                class="flex items-center justify-start gap-4 p-3 bg-transparent text-white rounded-full sidebar_page_item">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="sidebar_page_title">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M2.69041 4.6037C2.49712 4.80251 2.49712 5.12485 2.69041 5.32366L6.65 9.39639C6.74283 9.49186 6.86872 9.5455 6.99999 9.5455C7.13125 9.5455 7.25715 9.49186 7.34997 9.39639L11.3096 5.32366C11.5029 5.12485 11.5029 4.80251 11.3096 4.6037C11.1163 4.40489 10.8029 4.40489 10.6096 4.6037L6.99999 8.31644L3.39037 4.6037C3.19708 4.40489 2.8837 4.40489 2.69041 4.6037Z"
                            fill="#fff" /></svg>

                <h3 class="text-sm sidebar_page_title">Reports</h3>
            </a>




        </div>
        @endif
     </div>
 </div>

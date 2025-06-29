<div class="modal-content driverPopup ">
    <button class="closeBtn" data-dismiss="modal" aria-label="Close"><svg width="2.4rem" height="2.4rem" viewBox="0 0 24 24"
            fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                fill="white"></path>
        </svg></button>
    <!-- Frist Row in popup -->
    <div class="driver-metadata">

        <!-- Driver Name -->
        <div class="driverNamePhone">
            <div class="driverImageData">
                <div class="driverImageWrapper">
                    <img src="{{ $driver->getFirstMediaUrl('profile') ? $driver->getFirstMediaUrl('profile') : 'https://fakeimg.pl/300/' }}"
                        alt="" class="w-100 h-100 rounded-full">
                </div>
                <div class="driverData">
                    <p class="driverName ">
                        {{ $driver->full_name }}
                    </p>
                    <p class="driverPhone">
                        {{ $driver->phone }}
                    </p>
                </div>
            </div>
            <div class="driverCar">
                <svg width="2.9rem" height="2.9rem" viewBox="0 0 29 29" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.1878 2.7959C8.59491 2.7959 7.50294 3.19482 6.76623 3.94849C6.05138 4.67979 5.76361 5.65833 5.56847 6.58522L4.2099 13.0895C3.86293 13.2687 3.57497 13.4974 3.33974 13.7763C2.70305 14.5311 2.56966 15.5345 2.48691 16.4212L1.81497 23.7285C1.64902 25.4974 3.06838 27.0001 4.84839 27.0001H7.10439C7.82952 27.0001 8.37761 26.8461 8.76992 26.4738C9.11538 26.146 9.25981 25.7096 9.36154 25.4022L9.3776 25.3538L9.6176 24.6338C9.77749 24.1542 9.87682 23.8982 10.0646 23.7211C10.2255 23.5693 10.5433 23.4001 11.3284 23.4001H17.4724C18.2743 23.4001 18.5777 23.5558 18.7274 23.694C18.9057 23.8585 19.0026 24.1051 19.1839 24.6359L19.1846 24.638L19.4232 25.3538L19.4392 25.4022C19.5409 25.7096 19.6854 26.146 20.0308 26.4738C20.4231 26.8461 20.9712 27.0001 21.6964 27.0001H23.9524C25.7326 27.0001 27.1521 25.4971 26.9857 23.7279L26.3138 16.4203C26.231 15.5337 26.0977 14.5311 25.461 13.7763C25.2255 13.497 24.9371 13.2681 24.5895 13.0889L23.2309 6.58429C23.0358 5.6574 22.7482 4.67979 22.0334 3.94849C21.2966 3.19482 20.2047 2.7959 18.6118 2.7959H10.1878ZM5.53807 12.5999L6.74293 6.8315C6.93177 5.93463 7.16629 5.25592 7.62436 4.78731C8.06065 4.34098 8.79268 3.9959 10.1878 3.9959H13.7998V5.3999H12.6001V6.5999H16.2001V5.3999H14.9998V3.9959H18.6118C20.0069 3.9959 20.7389 4.34098 21.1752 4.78731C21.6334 5.25601 21.8678 5.9344 22.0567 6.8315L23.2615 12.5999H5.53807ZM3.00976 23.8403L3.68179 16.5319C3.76704 15.6187 3.89179 14.983 4.25704 14.5499C4.5936 14.1509 5.25222 13.8001 6.74439 13.8001H22.0564C23.5485 13.8001 24.2072 14.1509 24.5437 14.5499C24.909 14.983 25.0337 15.6185 25.119 16.5319L25.791 23.8403C25.8887 24.8791 25.0522 25.8001 23.9524 25.8001H21.6964C21.1255 25.8001 20.9356 25.6781 20.8569 25.6034C20.7499 25.5019 20.6867 25.3498 20.5616 24.9744L20.3216 24.2544L20.3202 24.2502L20.2967 24.1812C20.1473 23.7411 19.9645 23.2028 19.5413 22.8122C19.0671 22.3744 18.3984 22.2001 17.4724 22.2001H11.3284C10.3855 22.2001 9.71329 22.4029 9.24122 22.8481C8.81199 23.2529 8.63236 23.7935 8.49446 24.2084L8.47918 24.2544L8.23918 24.9744C8.11403 25.3498 8.05084 25.5019 7.94386 25.6034C7.86517 25.6781 7.67526 25.8001 7.10439 25.8001H4.84839C3.7486 25.8001 2.91209 24.8791 3.00976 23.8403ZM3.6001 9H4.8001V10.2H3.6001V9ZM25.2 9H24V10.2H25.2V9ZM10.8002 18.5999H7.2002V17.3999H10.8002V18.5999ZM18 18.5999H21.6V17.3999H18V18.5999Z"
                        fill="#585858"></path>
                </svg>
            </div>
        </div>

        <div class="driverTasks">

            <span> {{ $tasks }} tasks are left</span>

        </div>




    </div>



    <!-- Collapse  -->
    <div class="collapse-wrapper py-3">
        <!-- Frist  Collapse container -->
        @foreach ($orders as $order)
            <div class="collapse-container">

                <svg class="scroll-icon" width="2.4rem" height="2.4rem" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.5977 7C16.7022 7 17.5977 6.10457 17.5977 5C17.5977 3.89543 16.7022 3 15.5977 3C14.4931 3 13.5977 3.89543 13.5977 5C13.5977 6.10457 14.4931 7 15.5977 7Z"
                        fill="#585858"></path>
                    <path
                        d="M8.59766 7C9.70223 7 10.5977 6.10457 10.5977 5C10.5977 3.89543 9.70223 3 8.59766 3C7.49309 3 6.59766 3.89543 6.59766 5C6.59766 6.10457 7.49309 7 8.59766 7Z"
                        fill="#585858"></path>
                    <path
                        d="M15.5977 14C16.7022 14 17.5977 13.1046 17.5977 12C17.5977 10.8954 16.7022 10 15.5977 10C14.4931 10 13.5977 10.8954 13.5977 12C13.5977 13.1046 14.4931 14 15.5977 14Z"
                        fill="#585858"></path>
                    <path
                        d="M15.5977 21C16.7022 21 17.5977 20.1046 17.5977 19C17.5977 17.8954 16.7022 17 15.5977 17C14.4931 17 13.5977 17.8954 13.5977 19C13.5977 20.1046 14.4931 21 15.5977 21Z"
                        fill="#585858"></path>
                    <path
                        d="M8.59766 14C9.70223 14 10.5977 13.1046 10.5977 12C10.5977 10.8954 9.70223 10 8.59766 10C7.49309 10 6.59766 10.8954 6.59766 12C6.59766 13.1046 7.49309 14 8.59766 14Z"
                        fill="#585858"></path>
                    <path
                        d="M8.59766 21C9.70223 21 10.5977 20.1046 10.5977 19C10.5977 17.8954 9.70223 17 8.59766 17C7.49309 17 6.59766 17.8954 6.59766 19C6.59766 20.1046 7.49309 21 8.59766 21Z"
                        fill="#585858"></path>
                </svg>
                <div class="w-100">

                    <a class="collapse-tab collapsed" data-bs-toggle="collapse" href="#firstOrder" role="button"
                        aria-expanded="false" aria-controls="firstOrder">


                        <div class="collapseNameIcon">
                            <div class="bullet"></div>
                            <div class="collapseIcon">

                                <svg width="2.0rem" height="2.0rem" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.0088 7.64151V12.3582C18.0088 12.3999 18.0088 12.4332 18.0005 12.4748C17.4172 11.9665 16.6672 11.6665 15.8338 11.6665C15.0505 11.6665 14.3255 11.9415 13.7505 12.3999C12.9838 13.0082 12.5005 13.9499 12.5005 14.9999C12.5005 15.6249 12.6755 16.2165 12.9838 16.7165C13.0588 16.8499 13.1505 16.9749 13.2505 17.0915L11.7255 17.9332C10.7755 18.4665 9.2255 18.4665 8.2755 17.9332L3.82551 15.4665C2.81717 14.9082 1.99219 13.5082 1.99219 12.3582V7.64151C1.99219 6.49151 2.81717 5.09152 3.82551 4.53319L8.2755 2.0665C9.2255 1.53317 10.7755 1.53317 11.7255 2.0665L16.1755 4.53319C17.1838 5.09152 18.0088 6.49151 18.0088 7.64151Z"
                                        fill="none" stroke="#585858" stroke-width="1.25" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M2.64258 6.19971L10.0009 10.458L17.3092 6.22468" stroke="#585858"
                                        stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M10.002 18.008V10.4497" stroke="#585858" stroke-width="1.25"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M19.1667 14.9998C19.1667 15.9998 18.725 16.8915 18.0333 17.4998C17.4417 18.0165 16.675 18.3332 15.8333 18.3332C13.9917 18.3332 12.5 16.8415 12.5 14.9998C12.5 13.9498 12.9833 13.0082 13.75 12.3998C14.325 11.9415 15.05 11.6665 15.8333 11.6665C17.675 11.6665 19.1667 13.1582 19.1667 14.9998Z"
                                        fill="#22AD2F" stroke="#585858" stroke-width="1.25" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M17.0995 14.4936L15.8349 13.229L14.5703 14.4936M15.835 16.7707V13.2644"
                                        stroke="white" stroke-miterlimit="10" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <h4>
                                {{$order['shop_name']}} - {{$order['branch_name']}}
                            </h4>

                        </div>
                        <div class="collapseArrowsBadges">
                            <div class="collapseBadge">
                                {{$order['status']}}
                            </div>

                            <div class="editIcon downarrow">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path
                                            d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                            fill="#dadada"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <div class="collapse" id="firstOrder">
                        <div class="card card-body">
                            <div class="driverDetails">

                                <div class="detailItem">
                                    <div class="shopName">
                                        <span>Area</span>
                                        <p>{{$order['area'] ?? '-'}}</p>
                                    </div>
                                </div>
                                <div class="detailItem">
                                    <div class="shopName">
                                        <span>Contact Phone</span>
                                        <p>{{$order['customer_phone']}}</p>
                                    </div>
                                </div>
                                <div class="detailItem">
                                    <div class="shopName">
                                        <span>Job ID</span>
                                        <p>{{$order['client_order_id'] ?? $order['id']}}</p>
                                    </div>
                                </div>
                                <div class="detailItem">
                                    <div class="shopName">
                                        <span>Task Progress Status</span>
                                        <p>{{$order['status']}}</p>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach




    </div>



</div>
<script src=" https://cdn.jsdelivr.net/npm/sortablejs@1.15.4/Sortable.min.js " defer></script>

<script defer>
    document.addEventListener('DOMContentLoaded', () => {
        const list = document.querySelectorAll(".collapse-wrapper");
        console.log(list)
        if (list) {
            new Sortable(list, {
                animation: 150,
                ghostClass: 'blue-background-class'
            });
        }
    });
</script>

<script>
    const popup_model_data = (data) => `
<div class="modal-content mainPopup">

    <div class="mainCloseBtn">
        <a class="close-order-popup-map"><svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z" fill="black"></path></svg> </a>
    </div>

    <!-- Frist Row in popup -->
    <div class="driver-metadata">

        <!-- Driver Name -->
        <div class="driverNamePhone">
            <div class="driverImageData">
                <div class="driverImageWrapper">
                    ${data.driver_name ?
        `<img src="${data.driver_photo || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}"
                          alt="Driver Image" width="100" height="100">` :
        `<svg style="width: 100%; height: 100%; fill: #f46624;" xmlns="http://www.w3.org/2000/svg"
                          width="100" height="100" class="bi bi-person-plus" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 1a5 5 0 0 0-4.546 2.914C3.486 13.1 4.418 14 6 14h4c1.582 0 2.514-.9 2.546-2.086A5 5 0 0 0 8 9zm5-2a.5.5 0 0 1 .5.5V8h1.5a.5.5 0 0 1 0 1H13.5v1.5a.5.5 0 0 1-1 0V9H11a.5.5 0 0 1 0-1h1.5V6.5a.5.5 0 0 1 .5-.5z"/>
                    </svg>`
    }
                </div>
                <div class="driverData">
                    ${data.driver_name ?
        `<p class="driverName" style="color: #f46624;">${data.driver_name}</p>` :
        (data.can_assign_orders ?
                `<a href="#" class="assignDriverBtn" id="assignDriverBtn" data-id="${data.id}"
                        data-bs-toggle="modal" data-bs-target="#exampleModal"
                        style="color: #f46624; text-decoration: none; border: none; outline: none; cursor: pointer;">
                        Assign to Driver
                    </a>` : ''
        )
    }
                    <p class="driverPhone">
                        ${data.driver_phone}
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

        <div class="icons">
            <div class="icon">

                <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M19.4628 2.33119C20.2464 2.94559 20.7864 3.93799 21.0072 4.97719C21.0339 4.98565 21.06 4.99608 21.0852 5.00839L23.5392 6.14359C23.6764 6.20658 23.7926 6.3075 23.8743 6.43445C23.956 6.56139 23.9996 6.70905 24 6.85999V21.7004C23.9991 21.8237 23.9697 21.9451 23.914 22.0551C23.8583 22.1651 23.778 22.2607 23.6791 22.3344C23.5803 22.4081 23.4658 22.4579 23.3445 22.4799C23.2232 22.5019 23.0984 22.4955 22.98 22.4612L16.1772 20.5412L8.0892 22.7708C7.94327 22.8109 7.78903 22.8092 7.644 22.766L0.5688 20.66C0.405265 20.612 0.261588 20.5125 0.159114 20.3763C0.0566412 20.2401 0.000839592 20.0744 0 19.904V4.79719C0 4.26919 0.5136 3.88999 1.026 4.03879L7.8684 6.03199L11.346 4.96759C11.3939 4.95346 11.4428 4.94342 11.4924 4.93759C11.6268 4.15039 12.0024 3.39439 12.6324 2.65759C13.38 1.78159 14.6916 1.26799 15.9696 1.20679C17.2956 1.14319 18.3084 1.42639 19.4616 2.32999M1.5996 5.85679V19.3148L7.4436 21.0536V7.55719L1.5996 5.85679ZM11.4504 6.59359L9.0432 7.32919V20.864L15.1212 19.1912V15.2372C15.1212 14.8004 15.48 14.4464 15.9216 14.4464C16.3632 14.4464 16.7208 14.8004 16.7208 15.2384V19.0508L22.4004 20.6528V7.36279L21.0564 6.73879C21.0336 6.87079 21.0048 7.00039 20.9688 7.12639C20.7079 8.0465 20.2701 8.90694 19.68 9.65959L16.7076 13.3712C16.6293 13.4688 16.5294 13.5468 16.4157 13.5989C16.302 13.6511 16.1777 13.676 16.0527 13.6717C15.9277 13.6674 15.8054 13.634 15.6955 13.5741C15.5857 13.5142 15.4914 13.4295 15.42 13.3268L12.642 9.30319C12.1836 8.66239 11.862 8.09119 11.6808 7.57999C11.5671 7.26093 11.4898 6.93002 11.4504 6.59359ZM16.0464 2.78839C15.1752 2.83039 14.28 3.18079 13.854 3.67879C13.3416 4.27879 13.0896 4.84039 13.0392 5.40919C12.9792 6.09439 13.02 6.57799 13.1904 7.05679C13.3164 7.41079 13.5672 7.85959 13.9548 8.40079L16.128 11.5472L18.42 8.68519C18.8824 8.09426 19.2252 7.41886 19.4292 6.69679C19.7172 5.68879 19.2828 4.20799 18.4692 3.57199C17.6352 2.91799 17.004 2.74159 16.0464 2.78839ZM16.2132 3.62479C17.538 3.62479 18.6132 4.68679 18.6132 5.99719C18.6115 6.31057 18.548 6.62055 18.4265 6.9094C18.3049 7.19825 18.1276 7.46032 17.9047 7.68064C17.6818 7.90095 17.4177 8.07519 17.1275 8.1934C16.8373 8.31162 16.5266 8.37149 16.2132 8.36959C14.8884 8.36959 13.8132 7.30759 13.8132 5.99719C13.8132 4.68679 14.8884 3.62479 16.2132 3.62479ZM16.2132 5.20639C16.1087 5.20576 16.0051 5.22571 15.9084 5.26511C15.8116 5.30451 15.7235 5.36258 15.6492 5.43602C15.5749 5.50945 15.5158 5.5968 15.4752 5.69309C15.4346 5.78938 15.4134 5.89271 15.4128 5.99719C15.4128 6.43399 15.7716 6.78799 16.2132 6.78799C16.3177 6.78846 16.4212 6.76835 16.5179 6.72881C16.6146 6.68926 16.7026 6.63106 16.7768 6.55751C16.851 6.48397 16.91 6.39653 16.9505 6.30018C16.9909 6.20383 17.0119 6.10047 17.0124 5.99599C17.0108 5.7854 16.9258 5.58403 16.776 5.43602C16.6262 5.28801 16.4238 5.20543 16.2132 5.20639Z"
                        fill="#CECECE"></path>
                </svg>
            </div>
            <div class="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path
                            d="M16 4.00195C18.175 4.01406 19.3529 4.11051 20.1213 4.87889C21 5.75757 21 7.17179 21 10.0002V16.0002C21 18.8286 21 20.2429 20.1213 21.1215C19.2426 22.0002 17.8284 22.0002 15 22.0002H9C6.17157 22.0002 4.75736 22.0002 3.87868 21.1215C3 20.2429 3 18.8286 3 16.0002V10.0002C3 7.17179 3 5.75757 3.87868 4.87889C4.64706 4.11051 5.82497 4.01406 8 4.00195"
                            stroke="#cecece" stroke-width="1.5"></path>
                        <path d="M10.5 14L17 14" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M7 14H7.5" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M7 10.5H7.5" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M7 17.5H7.5" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M10.5 10.5H17" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M10.5 17.5H17" stroke="#cecece" stroke-width="1.5" stroke-linecap="round"></path>
                        <path
                            d="M8 3.5C8 2.67157 8.67157 2 9.5 2H14.5C15.3284 2 16 2.67157 16 3.5V4.5C16 5.32843 15.3284 6 14.5 6H9.5C8.67157 6 8 5.32843 8 4.5V3.5Z"
                            stroke="#cecece" stroke-width="1.5"></path>
                    </g>
                </svg>
            </div>
            <div class="icon">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="1.6rem" height="1.6rem" viewBox="0 0 16 16" fill="none"
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
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @can('can_assign_orders')
    <li><a class="dropdown-item open-modal-test" href="#" data-id="${data.id}"
                                   data-bs-toggle="modal" data-bs-target="#exampleModal">Reassign</a></li>
                        @endcan
                         @can('can_unassign_orders')
                        <li>
                            <a class="dropdown-item UnAssign"
                             data-id="${data.id}"
                                     href="#">
                                        UnAssign
                                        </a>
                                    </li>
                                    @endcan

                                       @can('can_change_status_to_delivered_orders')
                        <li>
                            <a class="dropdown-item ChangeStatusToDelivered"
                             data-id="${data.id}"
                                     href="#">
                                       Complete task
                                        </a>
                                    </li>
                                    @endcan


                                    @can('can_make_cancel_request')
                                <li>
                                    <a class="dropdown-item request-client-cancel" href="#"
                                    data-id="${data.id}">
                                    Cancel
                                    </a>
                                </li>
                                @endcan

                                
    {{-- <li><a class="dropdown-item" href="#">Unassign</a></li>
<li><a class="dropdown-item" href="#">Comlete Task</a></li>
<li><a class="dropdown-item text-danger" href="#">Cancel</a></li> --}}
    </ul>
</div>
</div>
</div>



</div>

<!-- Timeline -->

<div class="stepper-wrapper">
<div class="stepper-item completed">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Order created
                </p>
                <p>
                    <span>${data.created_time??"--"}</span>

                </p>
            </div>
        </div>
        <div class="stepper-item ${data.assign_date != null ? 'completed' : ''}">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Assigned
                </p>
                <p>
                    <span>${data.assign_date??"--"}</span>

                </p>
            </div>
        </div>
        <div class="stepper-item ${data.accept_date !== null ? 'completed' : ''} ">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Accepted
                </p>
                <p>
                    <span>${data.accept_date??"--"}</span>
                </p>
            </div>
        </div>
        <div class="stepper-item ${data.arrive_branch_date !== null ? 'completed' : ''}">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Operatot at Pickup
                </p>
                <p>
                    <span>${data.arrive_branch_date??"--"}</span>
            </div>
        </div>

        <div class="stepper-item  ${data.recive_date !== null ? 'completed' : ''}">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Picked
                </p>
                <p>
                    <span>${data.recive_date??"--"}</span>
                </p>
            </div>
        </div>

        <div class="stepper-item  ${data.arrive_client_date !== null ? 'completed' : ''}">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Operator at Dropoff
                </p>
                <p>
                    <span>${data.arrive_client_date??"--"}</span>
                </p>
            </div>
        </div>
        <div class="stepper-item ${data.delivery_date !== null ? 'completed' : ''}">
            <div class="step-counter">
                <svg fill="#4bb543" width="64px" height="64px" viewBox="0 0 200.00 200.00" data-name="Layer 1"
                     id="Layer_1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.002">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                       stroke-width="1.2"></g>
                    <g id="SVGRepo_iconCarrier">
                        <title></title>
                        <path
                            d="M100,15a85,85,0,1,0,85,85A84.93,84.93,0,0,0,100,15Zm0,150a65,65,0,1,1,65-65A64.87,64.87,0,0,1,100,165Zm25-91.5-29,35L76,94c-4.5-3.5-10.5-2.5-14,2s-2.5,10.5,2,14c6,4.5,12.5,9,18.5,13.5,4.5,3,8.5,7.5,14,8,1.5,0,3.5,0,5-1l3-3,22.5-27c4-5,8-9.5,12-14.5,3-4,4-9,.5-13L138,71.5c-3.5-2.5-9.5-2-13,2Z">
                        </path>
                    </g>
                </svg>

            </div>
            <div class="step-name">
                <p>
                    Complete
                </p>
                <p>
                    <span>${data.delivery_date ??"--"}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Collapse  -->
    <div class="collapse-wrapper">
        <!-- Frist  Collapse container -->
        <div class="collapse-container">


            <a class="collapse-tab collapsed" id="colFirst" data-bs-toggle="collapse" href="#collapseFirst"
               role="button" aria-expanded="false" aria-controls="collapseExample">
                <div class="collapseNameIcon">
                    <div class="collapseIcon">
                        <svg width="4rem" height="4rem" viewBox="0 0 40 40" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#F46624"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M31.1424 15.6382C28.5065 3.99203 11.2524 3.97858 8.60308 15.6248C7.05652 22.4565 11.3062 28.2527 15.0179 31.83C17.7345 34.4389 22.011 34.4389 24.7141 31.83C28.4393 28.2527 32.6889 22.47 31.1424 15.6382ZM24.0682 18.0723C24.0682 20.3896 22.1896 22.2681 19.8723 22.2681C17.555 22.2681 15.6764 20.3896 15.6764 18.0723C15.6764 15.755 17.555 13.8764 19.8723 13.8764C22.1896 13.8764 24.0682 15.755 24.0682 18.0723Z"
                                  fill="white"></path>
                        </svg>
                    </div>
                    <h4>
                        ${data.order_address}
                    </h4>

                </div>
                <div class="collapseArrowsBadges">
                    <div class="collapseBadge">
                        ${data.status_label}
                    </div>
                    <div class="editIcon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                    stroke="#dadada" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path
                                    d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                    stroke="#dadada" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </g>
                        </svg>
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

            <div class="collapse" id="collapseFirst">
                <div class="card card-body">
                    <div class="driverDetails">
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Shop Name</span>
                                <p>${data.shop_name}</p>
                            </div>
                            <div class="shopImage">
                                <img src="${data.shop_profile || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}"
                                     alt="Driver Image" width="200" height="200">

                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Branch Name</span>
                                <p>${data.branch_name}</p> 
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Branch Phone</span>
                                <p>${data.branch_phone}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Branch City</span>
                                <p>${data.branch_area}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Prep Time</span>
                                <p>${data.preparation_time} min</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Order Value</span>
                                <p>${data.value}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Payment Method</span>
                                <p>${data.payment_label}</p>
                            </div>
                        </div>
                    </div>

                    <div class="shipmentId">
                        <span>Shipment ID</span>

                        <p>#${data.order_id}</p>
                    </div>

                </div>
            </div>

        </div>

        <!-- Second Collapse Contaiber -->

        <div class="collapse-container">


            <a class="collapse-tab collapsed" id="colSecond" data-bs-toggle="collapse" href="#collapseSecond"
               role="button" aria-expanded="false" aria-controls="collapseExample">
                <div class="collapseNameIcon">
                    <div class="collapseIcon">
                        <svg width="4rem" height="4rem" viewBox="0 0 40 40" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M40 20C40 31.0457 31.0457 40 20 40C8.9543 40 0 31.0457 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z"
                                fill="black"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M31.1416 15.6379C28.5058 3.99173 11.2516 3.97828 8.60233 15.6245C7.05578 22.4562 11.3054 28.2524 15.0172 31.8297C17.7337 34.4386 22.0103 34.4386 24.7134 31.8297C28.4385 28.2524 32.6882 22.4697 31.1416 15.6379ZM24.2835 17.0236C24.6774 16.6297 24.6774 15.9911 24.2835 15.5972C23.8896 15.2033 23.251 15.2033 22.8571 15.5972L18.191 20.2633L16.8869 18.9592C16.493 18.5653 15.8544 18.5653 15.4605 18.9592C15.0666 19.3531 15.0666 19.9917 15.4605 20.3856L17.4778 22.4029C17.8717 22.7968 18.5103 22.7968 18.9042 22.4029L24.2835 17.0236Z"
                                  fill="white"></path>
                        </svg>
                    </div>
                    <h4>
                        ${data.customer_name || 'Customer'}
                    </h4>

                </div>
                <div class="collapseArrowsBadges">
                    <div class="collapseBadge">
                        ${data.status_label}
                    </div>
                    <div class="editIcon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                    stroke="#dadada" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path
                                    d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                    stroke="#dadada" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </g>
                        </svg>
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

            <div class="collapse" id="collapseSecond">
                <div class="card card-body">
                    <div class="driverDetails">
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Customer Name</span>


                                <p>  ${data.customer_name || 'Customer'}</p>
                            </div>
                            <div class="shopImage">
                                <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image"
                                     width="100" height="100">
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Address name</span>
                                <p> ${data.order_address || '-'}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Address area</span>
                                <p>-</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Customer phone</span>
                                <p> ${data.customer_phone || '-'}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Delivary time</span>
                                <p>${data.delivered_in || '-'}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Distance</span>
                                <p>${data.distance || '-'}</p>
                            </div>
                        </div>
                        <div class="detailItem">
                            <div class="shopName">
                                <span>Order Value</span>
                                <p>${data.value || '-'} SAR</p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>

    <button id="toggleCollapseBtn" class="seeMoreBtn">See More</button>



</div>

`;
</script>

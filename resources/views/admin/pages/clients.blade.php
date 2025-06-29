@extends('admin.layouts.app')

<!-- Include the select2 CSS and JS (make sure to include these in your HTML file) -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/css/select2.min.css" rel="stylesheet" /> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" defer></script>


<link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/clients.css') }}" />


@section('content')
    <div class="w-full h-full">

        <div class="flex  flex-col p-2 h-full sideSectionMapContainer">
            <div class="bg-white  rounded-lg h-full">


                <div class="flex flex-col w-full h-full md:flex-row sideSectionContainer">

                    <!-- Sidebar -->
                    <div class="rounded-4 sideSection "
                        style="width:33%; background-color: #f9f9f9; padding: 12.8px 20.8px 20.8px;">
                        <div class="flex items-center justify-between pb-3 border-bottom">
                            <div class="flex  flex-col">
                                <h3 class="text-base font-bold text-black">
                                    Clients
                                </h3>
                            </div>

                            <div class="dropdown">
                                <button
                                    class="flex items-center justify-center  gap-3 px-4 py-2 text-white rounded-md NewButton"
                                    id="newDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span style="font-size: 11.2px; font-weight: 600">+ New</span>
                                </button>

                                <ul class="dropdown-menu" aria-labelledby="newDropdown">
                                    <li> <button class="dropdown-item new-option" data-type="client">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M3.2735 2.27405C4.09919 1.51137 5.23306 1.25 6.61988 1.25H9.66987L9.67988 1.25007L9.68996 1.25H14.3201H14.3599H17.3701C18.7569 1.25 19.8907 1.51137 20.7164 2.27405C21.5323 3.02762 21.9053 4.14206 22.093 5.49711C22.0942 5.50605 22.0953 5.51502 22.0962 5.524L22.3764 8.27572C22.477 9.28721 22.225 10.2262 21.7209 10.9801C21.7463 11.0554 21.7601 11.1361 21.7601 11.22V15.71C21.7601 18.0284 21.3026 19.8478 20.0832 21.0687C18.8637 22.2898 17.044 22.75 14.7201 22.75H9.33011C8.85863 22.75 8.41009 22.7311 7.98207 22.6865C7.16648 23.3601 6.12715 23.75 5 23.75C3.55017 23.75 2.25229 23.0955 1.3949 22.0714L1.3901 22.0656C1.38191 22.0572 1.37128 22.0461 1.36037 22.0341C1.34694 22.0192 1.3304 22.0001 1.31248 21.9768C1.1778 21.8177 1.04664 21.6392 0.933962 21.4412C0.496613 20.7269 0.25 19.8855 0.25 19C0.25 17.5079 0.936775 16.1655 2.02852 15.2967C2.11337 15.2271 2.20055 15.1612 2.29004 15.0993V11.22C2.29004 11.1572 2.29775 11.0962 2.31227 11.038C1.78003 10.2749 1.51024 9.31398 1.61355 8.27572L1.61373 8.274L1.89376 5.524C1.89467 5.51502 1.89575 5.50605 1.89699 5.49711C2.08461 4.14206 2.45768 3.02762 3.2735 2.27405ZM3.37628 16.1906C3.39204 16.1827 3.40747 16.1742 3.42257 16.1653C3.89138 15.9011 4.42996 15.75 5 15.75C5.81383 15.75 6.53997 16.0411 7.10724 16.5354C7.12106 16.5474 7.13531 16.559 7.14998 16.57C7.2152 16.6189 7.28866 16.6895 7.37503 16.7845L7.37713 16.7868C7.91943 17.3784 8.25 18.1507 8.25 19C8.25 19.6094 8.07947 20.1834 7.7816 20.6664L7.78152 20.6663L7.77509 20.6772C7.61789 20.9419 7.43823 21.1633 7.24363 21.3291C7.22524 21.3448 7.20764 21.3613 7.19087 21.3786C7.18296 21.3868 7.17524 21.3952 7.1677 21.4037C7.16233 21.4098 7.15706 21.4159 7.15189 21.4222L7.13918 21.4329L7.12828 21.4425C6.56449 21.9499 5.82269 22.25 5 22.25C4.01778 22.25 3.14182 21.8115 2.55912 21.1252C2.54059 21.1011 2.52345 21.0812 2.50962 21.0659C2.49872 21.0539 2.48809 21.0427 2.4799 21.0344L2.46947 21.0219C2.37353 20.91 2.29433 20.8002 2.2337 20.6924C2.22877 20.6836 2.22367 20.6749 2.2184 20.6664C1.92053 20.1834 1.75 19.6094 1.75 19C1.75 17.9745 2.221 17.0588 2.96618 16.4675L2.96626 16.4676L2.97678 16.459C3.10433 16.3539 3.23741 16.2648 3.37628 16.1906ZM9.1773 21.2493C9.54498 20.5781 9.75 19.8073 9.75 19C9.75 17.7499 9.261 16.6227 8.48392 15.7744C8.3755 15.6552 8.2372 15.5139 8.07311 15.3876C7.24317 14.6722 6.17689 14.25 5 14.25C4.58253 14.25 4.17677 14.3052 3.79004 14.4084V12.3143C4.34732 12.5933 4.98443 12.75 5.66987 12.75C6.94675 12.75 8.13774 12.1071 8.89611 11.1242C9.56555 12.1101 10.7015 12.75 12.03 12.75C13.344 12.75 14.4644 12.1248 15.1335 11.1593C15.8934 12.1208 17.0653 12.75 18.3301 12.75C19.0374 12.75 19.692 12.5833 20.2601 12.2878V15.71C20.2601 17.8815 19.8226 19.2071 19.0219 20.0087C18.2215 20.8102 16.8962 21.25 14.7201 21.25H9.33011C9.27865 21.25 9.22771 21.2498 9.1773 21.2493ZM3.38465 5.68964L3.10618 8.42424C2.95073 9.99091 4.09213 11.25 5.66987 11.25C6.93648 11.25 8.1009 10.2005 8.22336 8.93762L8.22355 8.9357L8.84113 2.75H6.61988C5.3867 2.75 4.71058 2.98862 4.29128 3.37592C3.86363 3.77094 3.55736 4.45304 3.38465 5.68964ZM13.6412 2.75L14.2738 9.08454L14.2745 9.09135C14.2785 9.12837 14.2831 9.16527 14.2882 9.20202C14.194 10.3745 13.2623 11.25 12.03 11.25C10.6417 11.25 9.63034 10.1404 9.76638 8.75347L10.3685 2.75H13.6412ZM15.7945 9.12037C16.0227 10.3067 17.132 11.25 18.3301 11.25C19.9047 11.25 21.0396 9.99477 20.8838 8.42501L20.6053 5.68964C20.4326 4.45304 20.1263 3.77094 19.6987 3.37592C19.2794 2.98862 18.6032 2.75 17.3701 2.75H15.1889L15.7764 8.60664C15.7935 8.78042 15.7993 8.95189 15.7945 9.12037ZM2.76001 18.98C2.76001 18.5658 3.0958 18.23 3.51001 18.23H4.25V17.52C4.25 17.1058 4.58579 16.77 5 16.77C5.41421 16.77 5.75 17.1058 5.75 17.52V18.23H6.48999C6.9042 18.23 7.23999 18.5658 7.23999 18.98C7.23999 19.3942 6.9042 19.73 6.48999 19.73H5.75V20.51C5.75 20.9242 5.41421 21.26 5 21.26C4.58579 21.26 4.25 20.9242 4.25 20.51V19.73H3.51001C3.0958 19.73 2.76001 19.3942 2.76001 18.98Z"
                                                    fill="#585858"></path>
                                            </svg>
                                            Client
                                        </button>
                                    </li>
                                    <li> <button class="dropdown-item new-option" data-type="group">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M18.069 9.89563C18.206 9.37985 18.2791 8.83799 18.2791 8.27907C18.2791 4.81124 15.4678 2 12 2C8.53217 2 5.72093 4.81124 5.72093 8.27907C5.72093 8.83799 5.79396 9.37985 5.93099 9.89563C3.62636 10.8255 2 13.0833 2 15.7209C2 19.1888 4.81124 22 8.27907 22C9.67181 22 10.9586 21.5466 12 20.7792C13.0414 21.5466 14.3282 22 15.7209 22C19.1888 22 22 19.1888 22 15.7209C22 13.0833 20.3736 10.8255 18.069 9.89563ZM12 3.39535C9.3028 3.39535 7.11628 5.58187 7.11628 8.27907C7.11628 8.70844 7.17169 9.12486 7.27575 9.52159C7.4009 9.99873 7.59642 10.4474 7.85055 10.8558C8.39196 11.7258 9.19939 12.4131 10.1591 12.8039C10.6013 12.984 11.0758 13.1012 11.5715 13.1442C11.7127 13.1565 11.8556 13.1628 12 13.1628C12.1444 13.1628 12.2873 13.1565 12.4285 13.1442C12.9242 13.1012 13.3987 12.984 13.8409 12.8039C14.8006 12.4131 15.608 11.7258 16.1495 10.8558C16.4036 10.4474 16.5991 9.99873 16.7243 9.52159C16.8283 9.12486 16.8837 8.70844 16.8837 8.27907C16.8837 5.58187 14.6972 3.39535 12 3.39535ZM9.65192 14.1044C8.26732 13.5457 7.12754 12.5078 6.43818 11.1961C4.65343 11.9229 3.39535 13.675 3.39535 15.7209C3.39535 18.4181 5.58187 20.6047 8.27907 20.6047C9.26648 20.6047 10.1855 20.3116 10.9538 19.8078C11.3663 19.5318 11.5929 19.3298 12 18.8842C12.7252 18.032 13.1628 16.9276 13.1628 15.7209C13.1628 15.2916 13.1074 14.8751 13.0033 14.4784C12.6766 14.5309 12.3415 14.5581 12 14.5581C11.6585 14.5581 11.3234 14.5309 10.9967 14.4784C10.4501 14.3784 10.1522 14.302 9.65192 14.1044ZM13.0462 19.8078C13.9887 18.7094 14.5581 17.2817 14.5581 15.7209C14.5581 15.162 14.4851 14.6202 14.3481 14.1044C15.7327 13.5457 16.8725 12.5078 17.5618 11.1961C19.3466 11.9229 20.6047 13.675 20.6047 15.7209C20.6047 18.4181 18.4181 20.6047 15.7209 20.6047C14.7335 20.6047 13.8145 20.3116 13.0462 19.8078Z"
                                                    fill="#585858"></path>
                                            </svg>
                                            Group
                                        </button></li>
                                    <li> <button class="dropdown-item new-option" data-type="zone">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M8.54839 2.58897C8.97742 2.578 9.44954 2.65 9.85036 2.84663L9.85495 2.84888L15.1105 5.47167C15.234 5.53458 15.446 5.58272 15.7007 5.57529C15.9547 5.56787 16.1668 5.50756 16.289 5.43819L18.6371 4.09928C19.598 3.54874 20.6444 3.41154 21.4966 3.90638C22.3489 4.40123 22.75 5.37809 22.75 6.48997V16.22C22.75 16.7061 22.5807 17.1909 22.3491 17.5879C22.1173 17.9852 21.7806 18.3677 21.3672 18.6082L21.3628 18.6108L17.0328 21.0908L17.0308 21.0919C16.6399 21.3142 16.1701 21.41 15.7417 21.421C15.3127 21.4319 14.8405 21.3599 14.4397 21.1633L14.4341 21.1606L9.17959 18.5283C9.0561 18.4654 8.84406 18.4172 8.58943 18.4247C8.33535 18.4321 8.12329 18.4924 8.0011 18.5617L5.653 19.9007C4.69202 20.4512 3.64568 20.5884 2.79344 20.0936C1.9412 19.5987 1.54004 18.6218 1.54004 17.51V7.77997C1.54004 7.30032 1.70592 6.81843 1.93674 6.42162C2.16777 6.02443 2.5059 5.64015 2.92729 5.39916L7.25931 2.918C7.65018 2.69574 8.12002 2.59991 8.54839 2.58897ZM3.67279 6.70078L7.81006 4.33117V17C7.81006 17.0112 7.81031 17.0224 7.8108 17.0335C7.61775 17.0868 7.43023 17.1608 7.25853 17.2584L4.90853 18.5984L4.90708 18.5993C4.22825 18.9884 3.77918 18.9314 3.54664 18.7964C3.31388 18.6612 3.04004 18.2981 3.04004 17.51V7.77997C3.04004 7.62962 3.09916 7.40651 3.23334 7.17582C3.36719 6.94571 3.53448 6.77982 3.67279 6.70078ZM9.85802 17.1904C9.68517 17.1027 9.49948 17.0393 9.31006 16.9964V4.25339L14.4327 6.80981C14.6053 6.8974 14.7908 6.96067 14.98 7.00349V19.7563L9.86048 17.1917L9.85802 17.1904ZM16.48 19.6788L20.6129 17.3117L20.6146 17.3107C20.7509 17.2308 20.9183 17.0637 21.0535 16.8321C21.1894 16.599 21.25 16.3738 21.25 16.22V6.48997C21.25 5.70185 20.9762 5.33871 20.7434 5.20356C20.5109 5.06854 20.0618 5.01158 19.383 5.40066L19.3815 5.40149L17.0315 6.74149C16.8601 6.83901 16.6728 6.91297 16.48 6.96622V19.6788Z"
                                                    fill="#585858"></path>
                                            </svg>
                                            Zone
                                        </button></li>
                                    <li> <button class="dropdown-item new-option" data-type="branch">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4 2.75C3.31421 2.75 2.75 3.31421 2.75 4V6C2.75 6.68579 3.31421 7.25 4 7.25H7C7.68579 7.25 8.25 6.68579 8.25 6V5V4C8.25 3.31421 7.68579 2.75 7 2.75H4ZM9.75 4V4.25H12.5H15.25V4.20001C15.25 3.12581 16.1258 2.25 17.2 2.25H20.8C21.8742 2.25 22.75 3.12581 22.75 4.20001V5.79999C22.75 6.87419 21.8742 7.75 20.8 7.75H17.2C16.1258 7.75 15.25 6.87419 15.25 5.79999V5.75H13.25V11.75H15.25V11.7C15.25 10.6258 16.1258 9.75 17.2 9.75H20.8C21.8742 9.75 22.75 10.6258 22.75 11.7V13.3C22.75 14.3742 21.8742 15.25 20.8 15.25H17.2C16.1258 15.25 15.25 14.3742 15.25 13.3V13.25H13.25V18C13.25 18.6858 13.8142 19.25 14.5 19.25H15.25V19.2C15.25 18.1258 16.1258 17.25 17.2 17.25H20.8C21.8742 17.25 22.75 18.1258 22.75 19.2V20.8C22.75 21.8742 21.8742 22.75 20.8 22.75H17.2C16.1258 22.75 15.25 21.8742 15.25 20.8V20.75H14.5C12.9858 20.75 11.75 19.5142 11.75 18V12.5V5.75H9.75V6C9.75 7.51421 8.51421 8.75 7 8.75H4C2.48579 8.75 1.25 7.51421 1.25 6V4C1.25 2.48579 2.48579 1.25 4 1.25H7C8.51421 1.25 9.75 2.48579 9.75 4ZM16.75 20V20.8C16.75 21.0458 16.9542 21.25 17.2 21.25H20.8C21.0458 21.25 21.25 21.0458 21.25 20.8V19.2C21.25 18.9542 21.0458 18.75 20.8 18.75H17.2C16.9542 18.75 16.75 18.9542 16.75 19.2V20ZM16.75 13.3V12.5V11.7C16.75 11.4542 16.9542 11.25 17.2 11.25H20.8C21.0458 11.25 21.25 11.4542 21.25 11.7V13.3C21.25 13.5458 21.0458 13.75 20.8 13.75H17.2C16.9542 13.75 16.75 13.5458 16.75 13.3ZM16.75 5.79999V5V4.20001C16.75 3.95421 16.9542 3.75 17.2 3.75H20.8C21.0458 3.75 21.25 3.95421 21.25 4.20001V5.79999C21.25 6.04579 21.0458 6.25 20.8 6.25H17.2C16.9542 6.25 16.75 6.04579 16.75 5.79999Z"
                                                    fill="#585858"></path>
                                            </svg>
                                            Branch
                                        </button>
                                    </li>
                                </ul>
                            </div>

                        </div>
                        <!-- Search -->
                        <div id="client-search-dev"
                            class="flex items-center justify-between gap-3 p-2 my-3 bg-white  rounded-md">


                            <!-- Input -->
                            <input type="text" placeholder="Search here..." class=" bg-transparent outline-none border-0"
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


                        <div class="clientsSearch">
                            <div class="clientSearchHeader">
                                <p>Clients</p>
                                <p><span>60</span> top results</p>
                            </div>

                            <div class="clientsSearchResults">
                                <div class="itemlistContainer" data-id="${item.id}" data-type="${tab}">
                                    <div class="itemlistCard">
                                        <div class="itemListInfoContainer">
                                            <div class="itemListIcon">
                                                <img src="https://via.placeholder.com/150" alt="client image" width="100"
                                                    height="100">
                                            </div>

                                            <div class="itemListInfo">
                                                <div class="text-slide-wrapper">
                                                    <p class="text-slide">Client Name</p>
                                                </div>

                                                <small>2555</small>
                                            </div>
                                        </div>
                                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z"
                                                fill="#1A1A1A"></path>
                                        </svg>
                                    </div>
                                    <div class="itemListDivider"></div>
                                    <div class="itemListBadges">
                                        <div class="itemListBadge">
                                            <p>Riyadh</p>
                                        </div>
                                        <div class="itemListBadge">
                                            <p><span>0</span> SAR</p>
                                        </div>
                                        <div class="itemListBadge">
                                            <p><span>1</span> Branch</p>
                                        </div>
                                        <div class="itemListBadge">
                                            <p><span>28</span> Orders</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>



                        <div class="flex flex-col demondDriver">

                            <!-- Tabs -->
                            <div class="d-flex justify-content-between border-top ">
                                <button type="button" id="clients-btn"
                                    class="nav-link w-100 p-2 text-sm font-medium text-center border-b  focus:outline-none"
                                    style="font-size: 9.6px" data-tab="client">
                                    <span>
                                        <div class="d-flex flex-column lh-sm">
                                            <span> Clients </span>
                                            <span style="color: red; display:inline" id="clients_count">100</span>
                                        </div>
                                    </span>
                                </button>
                                <button type="button" id="group-btn"
                                    class="nav-link w-100 p-2 text-sm text-center border-b border-gray1 focus:outline-none"
                                    style="font-size: 9.6px" data-tab="group">
                                    <span>
                                        <div class="d-flex flex-column lh-sm">
                                            <span> Groups </span>
                                            <span style="color: red; display:inline"
                                                id="groups_count">({{ $groups }})</span>
                                        </div>
                                    </span>
                                </button>
                                <button type="button" id="zone-btn"
                                    class="nav-link w-100 p-2 text-sm font-medium text-center border-b  focus:outline-none"
                                    style="font-size: 9.6px" data-tab="zone">
                                    <span>
                                        <div class="d-flex flex-column lh-sm">
                                            <span> Zones </span>
                                            <span style="color: red; display:inline"
                                                id="zones_count">({{ $zones }})</span>
                                        </div>
                                    </span>
                                </button>
                                <button type="button" id="branch-btn"
                                    class="nav-link w-100 p-2 text-sm font-medium text-center border-b  focus:outline-none"
                                    style="font-size: 9.6px" data-tab="branch">
                                    <span>
                                        <div class="d-flex flex-column lh-sm">
                                            <span> Branches </span>
                                            <span style="color: red; display:inline"
                                                id="branches_count">({{ $branches }})</span>
                                        </div>
                                    </span>
                                </button>


                            </div>

                            <!-- Tab Content -->
                            <div class="tabContentContainer px-2" id="itemList">

                            </div>



                        </div>



                    </div>
                    <!-- Content Section-->
                    <div class="border rounded-4 md:ml-6 md:w-3/4 border-gray1 md:mt-0 main-container ">
                        <div class="main-placeholder">
                            <img src="https://via.placeholder.com/150" alt="Placeholder">
                            <h4>Manage Items</h4>
                            <p>Select an item from the dropdown or list to proceed.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Recharge Modal -->
    <div class="modal fade" id="rechargeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="rechargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="head">
                        <h4>
                            In order to recharge, please insert the amount you want to be add
                        </h4>
                    </div>
                    <button class="closeBtn btnClose" aria-label="Close" data-bs-dismiss="modal">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                fill="black"></path>
                        </svg>
                    </button>

                </div>
                <form class="d-flex flex-column p-0">
                    <div class="modal-body">

                        <fieldset class="floating-label-input">
                            <input type="number" name="amount" id="amount" required />
                            <legend>Recharge amount <span class="text-danger">*</span></legend>
                        </fieldset>
                    </div>

                    <!-- Buttons -->
                    <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                        <div>
                            <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" id="charge-client-balance" class="templateSaveBtn">
                                Save changes
                            </button>
                        </div>
                    </div>
            </div>

        </div>
        </form>
    </div>


    <!-- New Branch Modal -->
    @include('admin.pages.clients.branches')
    <!-- upload Branch Modal -->
    @include('admin.pages.clients.uploadBranches')

    <!-- New User Modal -->
    @include('admin.pages.clients.users')

    <!-- add image Modal in new client  -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold " id="photoModalLabel">Crop and Resize Your Photo</h5>
                    <svg data-bs-dismiss="modal" aria-label="Close" width="19.2px" height="19.2px" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="crop-container">
                        <img id="uploadedImage" src="" alt="Image to crop">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                    <div>
                        <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" id="cropAndSaveButton" class="templateSaveBtn">
                            Save changes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection









@include('admin.pages.clients.mapHandler')

<script>
    $(document).ready(function() {

        let currentClientRequest = null;
        let driverCurrentRequest = null;
        let currentTab = 'client';
        let clientID = null;

        let currentGroupRequest = null;
        let groupID = null;
        let isFirstRow = true;


        let currentZoneRequest = null;
        let zoneID = null;

        let currentBranchRequest = null;
        let branchID = null;

        // Clients
        $(document).on('click', '#uploadBranchesButton', function() {
            $("#client_id_for_uplaod_branches").val(clientID);
            let formData = new FormData($('#uploadBranchesForm')[0]);

            $.ajax({
                url: "{{ route('upload-branches') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#uploadBranchModal .btnClose').trigger('click');

                    if (response.existingBranchIds && response.existingBranchIds.length >
                        0) {
                        alert("The following Branch IDs already exist: " + response
                            .existingBranchIds.join(', '));
                    } else {
                        alert("Branches uploaded successfully!");
                    }

                    renderClientBranchesDataTable(clientID);
                },
                error: function(error) {
                    let errorMessage = 'An error occurred';
                    if (error.responseJSON) {
                        if (error.responseJSON.message) {
                            errorMessage = error.responseJSON
                                .message;
                        } else if (error.responseJSON.errors) {

                            errorMessage = Object.values(error.responseJSON.errors)[0][0];
                        }
                    } else if (error.responseText) {

                        errorMessage = error.responseText;
                    }

                    alert(errorMessage);
                }
            });
        });





        $(document).on('change', '#pickup_id', function() {

            const fieldset = $('#custom_id_fieldset');
            if ($(this).val() === '2') {
                fieldset.removeClass('invisible');
            } else {
                fieldset.addClass('invisible');
            }
        });
       
       
        $(document).on('change', '#branch-city', function() {
            console.log(999);

            var cityId = $(this).val();
            var areaSelect = $('#branch-area');

            if (cityId) {
                $.ajax({
                    url: '{{ route('city-areas') }}',
                    type: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        console.log(response);
                        areaSelect.prop('disabled', false);
                        areaSelect.empty();
                        areaSelect.append(
                            '<option value="" selected="selected" disabled>Area</option>'
                        );

                        $.each(response, function(key, area) {

                            areaSelect.append('<option value="' + area.id + '">' +
                                area.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            } else {
                areaSelect.prop('disabled', true);
                areaSelect.empty();
                areaSelect.append('<option value="" selected="selected" disabled>Area</option>');
            }
        });



        $(document).on('change', '#branch-city2', function() {
            console.log(999);

            var cityId = $(this).val();
            var areaSelect = $('#branch-area2');

            if (cityId) {
                $.ajax({
                    url: '{{ route('city-areas') }}',
                    type: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        console.log(response);
                        areaSelect.prop('disabled', false);
                        areaSelect.empty();
                        areaSelect.append(
                            '<option value="" selected="selected" disabled>Area</option>'
                        );

                        $.each(response, function(key, area) {

                            areaSelect.append('<option value="' + area.id + '">' +
                                area.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            } else {
                areaSelect.prop('disabled', true);
                areaSelect.empty();
                areaSelect.append('<option value="" selected="selected" disabled>Area</option>');
            }
        });

        $('.clientsSearch').hide();
        $('.demondDriver').show();


        function toggleDisplay() {


            $('#itemList').empty();

            if (currentTab == 'client') {
                console.log(66);

                fetchClientData();
            }

            if (currentTab == 'group') {
                console.log(66);

                fetchGroupsData();
            }


            if (currentTab == 'zone') {
                console.log(66);

                fetchZonesData();
            }

            if (currentTab == 'branch') {
                console.log(88);

                fetchBranchesData();
            }




        }

        $("#order_search").on("keyup change ", function() {
            PAGE_NUMBER = 1;
            toggleDisplay();
        });


        function updateMap(lat, lng) {
            console.log('update');

            const newPosition = {
                lat: lat,
                lng: lng
            };
            formMap.setCenter(newPosition);
            if (marker) {
                marker.setPosition(newPosition);
            } else {

                marker = new google.maps.Marker({
                    position: newPosition,
                    map: formMap
                });
            }


            document.getElementById('lat_order_hidden').value = lat;
            document.getElementById('long_order_hidden').value = lng;
        }


        function modalCLosed() {
            console.log('closed');

            // Reset latitude and longitude
            lat = 24.7136;
            lng = 46.6753;

            // Update the map and marker with the new coordinates
            updateMap(lat, lng);
        }




        function fetchClientData() {
            console.log('hii');
            var element_class = $('#itemList')

            var clientCardHtml = '';
            if (currentClientRequest) {
                currentClientRequest.abort();
            }

            currentClientRequest = $.ajax({
                url: '{{ route('client-list') }}',
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,

                    search: $('#order_search').val()
                },
                success: function(response) {
                    // console.log(response.clients_count);

                    // const userRole = response.user_role;
                    // console.log(response.clients_count);
                    var count = '(' + response.clients_count + ')';
                    $('#clients_count').html(count)
                    response.clients.forEach(function(item) {
                        console.log('howaida', item.is_active);

                        var activeIconeColor = 'clientActiveGrayIcon';
                        if (item.is_active == 1) {
                            activeIconeColor = 'clientActiveGreenIcon';
                        }

                        var customerCardHtml = `
                <div class="itemlistContainer itemlistContainerClient" data-is_active= "${item.is_active}" data-name="${item.full_name}" data-total_orders_count = "${item.total_orders}" data-total_balance_client = "${item.total_balance}" data-integration_token = "${item.integration_token}" data-client_country = "${item.country}" data-client_city = "${item.city}" data-client_currency = "${item.currency}" data-client_parial_pay = "${item.client_parial_pay}" data-client_defualt_preperation_time = "${item.client_defualt_preperation_time}" data-account_number = "${item.account_number}" data-client_min_preperation_time = "${item.client_min_preperation_time}" data-client_client_group = "${item.client_client_group}" data-client_operator_group="${item.client_operator_group}" data-price_order_group="${item.price_order}" data-id="${item.id}" data-type="client">
                                <div class="itemlistCard">
                                    <div class="itemListInfoContainer">
                                    <div class="itemListIcon">
                                        <div class="${activeIconeColor}"></div>
                                    <img src="${item.shop_profile || 'https://via.placeholder.com/150'}"alt="client image" width="100" height="100">
                                    </div>

                                    <div class="itemListInfo">
                                        <div class="text-slide-wrapper">
                                            <p class="text-slide"> ${item.full_name}</p>
                                        </div>

                                        <small>${item.id}</small>
                                    </div>
                                    </div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z" fill="#1A1A1A"></path></svg>
                                </div>
                                <div class="itemListDivider"></div>
                                <div class="itemListBadges">
                                    <div class="itemListBadge">
                                        <p>${item.city}</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.total_balance}</span> SAR</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.total_branches}</span> Branch</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p><span>${item.total_orders}</span> Orders</p>
                                    </div>
                                </div>
                            </div>
                `;

                        element_class.append(customerCardHtml);
                        console.log(element_class);

                    });





                    // console.log(clientCardHtml);
                    PAGE_NUMBER++;
                    return clientCardHtml;


                },
                complete: function() {
                    currentClientRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }


        $(document).on('click', '#save-client-btn', function(e) {
            e.preventDefault();
            console.log('huda')

            updateCheckboxValues();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-form')[0]);

            $.ajax({
                type: 'POST',
                url: '{{ route('save-client') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-form')[0].reset();
                    $('#croppedImage').empty();
                    // $('#file-upload').val('');
                    // $('#upload-label').css('background-image', '');
                    // document.getElementById('user-icon').style.display = 'block';

                    $('.select2').val(null).trigger('change');
                    var element_class = $('#itemList');
                    element_class.empty();
                    PAGE_NUMBER = 1;
                    fetchClientData();
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
        $(document).on('click', '#save-client-branch-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-branch-form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route('save-client-branch') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    alert('branch saved successfully');
                    $('#addNewBranchModal .btnClose').trigger('click');

                    lat = 24.7136;
                    lng = 46.6753;
                    renderClientBranchesDataTable(clientID);


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


        $(document).on('click', '#save-client-user-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-user-form')[0]);
            formData.append('id', clientID);
            $.ajax({
                type: 'POST',
                url: '{{ route('save-client-user') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    alert('User saved successfully');
                    $('#addNewUserModal .btnClose').trigger('click');

                    renderClientUsersDataTable(clientID);

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

        $(document).on('click', '#save-client-exist-user-btn', function(e) {
            e.preventDefault();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#client-exist-user-form')[0]);
            formData.append('id', clientID);
            $.ajax({
                type: 'POST',
                url: '{{ route('save-client-exist-user') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#user_id').find('option:first').prop('selected', true).prop(
                        'disabled',
                        true);

                    $('#user_id').trigger('change');


                    alert('User saved successfully');
                    $('#addNewUserModal .btnClose').trigger('click');

                    renderClientUsersDataTable(clientID);


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

            $('#usersModel').on('shown.bs.modal', function() {
                $('#nav-home-tab').tab('show');
            });
        });


        $(document).on('click', '#charge-client-balance', function(e) {
            e.preventDefault();
            $('#amountError').text('');

            $.ajax({
                url: "{{ route('charge-client-wallet') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: $('#amount').val(),
                    client_id: clientID
                },
                success: function(response) {
                    if (response.success) {

                        console.log('Client wallet charged successfully!');

                        $('#amount').val('')
                        $('#rechargeModal .btnClose').trigger('click');
                        console.log(response.amount, $('#total_balance_client'));

                        $('#total_balance_client').html(response.amount);
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.amount) {
                            $('#amountError').text(errors.amount[0]);
                        }
                    }
                }
            });
        });



        function updateCheckboxValues() {
            $('input[type="checkbox"]').each(function() {
                $(this).val(this.checked ? 1 : 0);
            });
        }


        $(document).on('change', '#auto_dispatch', function(e) {
            e.preventDefault();
            console.log(454);

            updateCheckboxValues();
        });


        $(document).on('change', '#is_integration', function(e) {
            e.preventDefault();
            console.log(454);

            updateCheckboxValues();
        });

        $(document).on('click', '.edit-client', function(e) {
            e.preventDefault();

            const updateURL = '{{ url('admin/edit-client') }}/' + clientID + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // console.log(response.group.name)
                    $('#name').val(response.client.first_name);
                    $('#phone').val(response.client.phone);
                    $('#id').val(response.client.id);
                    if (response.client_detail) {
                        $('#auto_dispatch').prop('checked', response.client_detail
                            .auto_dispatch == 1);


                        $('#is_integration').prop('checked', response.client_detail
                            .is_integration == 1);

                        if (response.client_detail.is_integration == 1) {
                            $('#integration-div').show();
                        }
                        $('select[name="country_id"]').val(response.client_detail
                                .country_id)
                            .trigger('change');

                        $('select[name="city_id"]').val(response.client_detail.city_id)
                            .trigger('change');

                        $('#email').val(response.client.email);

                        $('#price_order').val(response.client_detail.price_order);
                        $('#defaultPreparationTime').val(response.client_detail
                            .default_prepartion_time);

                        $('#minPreparationTime').val(response.client_detail
                            .min_prepartion_time);

                        $('#account_number').val(response.client_detail
                            .account_number);
                        $('#partialPay').val(response.client_detail.partial_pay);

                        $('#note').val(response.client_detail.note);



                        $('select[name="client_group_id"]').val(response.client_detail
                                .client_group_id)
                            .trigger('change');


                        $('select[name="driver_group_id"]').val(response.client_detail
                                .driver_group_id)
                            .trigger('change');


                        $('select[name="integration_id"]').val(response.client_detail
                                .integration_id)
                            .trigger('change');
                    }



                    $('select[name="currency"]').val(response.currency)
                        .trigger('change');

                    if (response.profile_url) {
                        const img = document.createElement('img');
                        img.src = response.profile_url;

                        img.style.borderRadius = '50%';
                        img.style.maxWidth = '300px';

                        $('#croppedImage').html(img);
                    } else {
                        $('#croppedImage').empty();
                    }

                    $('#client-title').html('Edit Client');
                    $('#save-client-btn').html('Save Changes');


                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });





        $(document).on('click', '.edit-client-user', function(event) {
            var data = $(this).data('user');
            var role = $(this).data('role');
            console.log(data)
            var user = data.model.user;

            if (user) {

                $('select[name="role"]').val(role).trigger(
                    'change');

                $('#edit-user-form input[name="first_name"]').val(user.first_name);
                $('#edit-user-form input[name="last_name"]').val(user.last_name);
                $('#edit-user-form input[name="email"]').val(user.email);
                $('#edit-user-form input[name="phone"]').val(user.phone);
                $('#edit-user-form input[name="edit_user_client_id"]').val(user.id);
                if (data.user) {
                    $('#edit-user-form input[name="mac_address"]').val(data.user.mac_address);
                }

                $('#edit-user-form input[name="password"]').val(
                    '');
                var profilePhotoUrl = data.profile_url;
                console.log('jasd', profilePhotoUrl);

                if (profilePhotoUrl) {
                    const img = document.createElement('img');
                    img.src = profilePhotoUrl;

                    img.style.borderRadius = '50%';
                    img.style.maxWidth = '300px';

                    $('#croppedImage').html(img);
                } else {
                    $('#croppedImage').empty();
                }
            }


        });



        $(document).on('click', '#edit-client-user-btn', function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#edit-user-form')[0]);
            formData.append('id', clientID);
            $.ajax({
                type: 'POST',
                url: '{{ route('update-client-user') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    $('#croppedImage').empty();
                    alert('User saved successfully');
                    $('#editUserModal .btnClose').trigger('click');
                    renderClientUsersDataTable(clientID);


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


        $(document).on('click', '#client-user-delete-btn', function(e) {
            e.preventDefault();
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = new FormData($('#edit-user-form')[0]);
            formData.append('id', clientID);
            $.ajax({
                type: 'POST',
                url: '{{ route('delete-client-user') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#client-branch-form')[0].reset();
                    $('#croppedImage').empty();
                    alert('User Deleted successfully');
                    $('#editUserModal .btnClose').trigger('click');
                    renderClientUsersDataTable(clientID);



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





        $(document).on('shown.bs.modal', '#editUserModal', function() {
            initializeSelectModal(".select2insidemodal",
                "#editUserModal .modal-body .phoneNewBranch", "Code");

            initializeSelectModal(".userTemplate",
                "#editUserModal .modal-body .userTemplateNewUser", "User template");
            initializeSelectModal(".grandAccess",
                "#editUserModal .modal-body .grandAccessNewUser", "");
            initializeSelectModal(".clientBranch",
                "#editUserModal .modal-body .clientBranchNewUser", "Client");
            initializeSelectModal(".branchesBranch",
                "#editUserModal .modal-body .branchesBranchNewUser", "Branches");
            $("#clientsAccessSelectInEdit").select2({
                placeholder: "Clients",
                minimumResultsForSearch: Infinity,
                allowClear: false,
                width: '100%',
                closeOnSelect: false,
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var checkbox = $(
                        '<input type="checkbox" style="margin-right: 8px;" />'
                    );
                    var dataMulti = $('<span class="select2MultiOption">' + data
                        .text + '</span>');
                    if (data.selected) {
                        checkbox.prop("checked", true);
                    }
                    var span = $('<span class="d-flex align-items-center">')
                        .append(
                            checkbox).append(dataMulti);
                    return span;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })
            $("#branchesAccessSelectInEdit").select2({
                placeholder: "Branches",
                minimumResultsForSearch: Infinity,
                allowClear: false,
                width: '100%',
                closeOnSelect: false,
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var checkbox = $(
                        '<input type="checkbox" style="margin-right: 8px;" />'
                    );
                    var dataMulti = $('<span class="select2MultiOption">' + data
                        .text + '</span>');
                    if (data.selected) {
                        checkbox.prop("checked", true);
                    }
                    var span = $('<span class="d-flex align-items-center">')
                        .append(
                            checkbox).append(dataMulti);
                    return span;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })
            $(".clientsUserSelect").on("select2:select", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });
            $(".clientsUserSelect").on("select2:opening", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });
        });



        // End CLients





        // Groups




        function fetchGroupsData() {
            console.log('hii');
            var element_class = $('#itemList')

            var clientCardHtml = '';
            if (currentGroupRequest) {
                currentGroupRequest.abort();
            }

            currentGroupRequest = $.ajax({
                url: '{{ route('clients-group-list') }}',
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,

                    search: $('#order_search').val()
                },
                success: function(response) {
                    console.log(response);

                    // const userRole = response.user_role;
                    // console.log(response.clients_count);
                    var count = '(' + response.groups_count + ')';
                    $('#groups_count').html(count)
                    response.groups.forEach(function(item) {


                        var customerCardHtml = `
                <div class="itemlistContainer itemlistContainerGroup  " data-id="${item.id}" data-type="group">
                                <div class="itemlistCard">
                                    <div class="itemListInfoContainer">
                                    <div class="itemListIcon">
                                    <img src="https://via.placeholder.com/150" alt="client image" width="100" height="100">
                                    </div>

                                    <div class="itemListInfo">
                                        <div class="text-slide-wrapper">
                                            <p class="text-slide">${item.name}</p>
                                        </div>

                                        <small>${item.id}</small>
                                    </div>
                                    </div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z" fill="#1A1A1A"></path></svg>
                                </div>
                            </div>
                `;

                        element_class.append(customerCardHtml);
                        console.log(element_class);

                    });





                    // console.log(clientCardHtml);
                    PAGE_NUMBER++;
                    return clientCardHtml;


                },
                complete: function() {
                    currentClientRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }






        function resetGroupForm() {
            $('#title-group').html('New Group');
            $('#btn-save-group').html('Save');
            $('#group-form')[0].reset();

            $('.select2').val(null).trigger('change');

            $('#group_name').val('');
            $('#calculation_method').val('');
            $('#default_delivery_fee').val('');
            $('#collection_amount').val('');
            $('#client_group_id').val('')
            $('#service_type').val('');
            $('.select2').val(null).trigger('change');



            $('#group_name_error').text('');
            $('#calculation_method_error').text('');
            $('#default_delivery_fee_error').text('');
            $('#collection_amount_error').text('');
            $('#service_type_error').text('');

            const calcMethod = document.getElementById('calcMethod');
            calcMethod.innerHTML = '';
            var element_class = $('#itemList');
            element_class.empty();
            PAGE_NUMBER = 1;
            fetchGroupsData();
        }

        $(document).on('click', '#btn-save-group', function(e) {
            e.preventDefault();

            $('#group_name_error').text('');
            $('#calculation_method_error').text('');
            $('#default_delivery_fee_error').text('');
            $('#collection_amount_error').text('');
            $('#service_type_error').text('');




            var formData = $('#group-form').serialize(); // Serialize form data
            $.ajax({
                type: 'POST',
                url: '{{ route('save-clients-group') }}',
                data: formData,
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // Clear form fields



                    resetGroupForm();



                },

                error: function(error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputElement = $('[name="' + key + '"]');
                            inputElement.addClass('is-invalid');
                            inputElement.after(
                                '<span class="text-danger invalid-feedback" role="alert" id="' +
                                key + '_error">' +
                                key + ': ' + value[0] +
                                '</span>');

                        });
                    } else {
                        console.error(error);
                    }
                }
            });
        });



        $(document).on('click', '.itemlistContainerGroup', function() {



            const itemId = $(this).data('id');
            groupID = itemId;
            $("#client_id").val(itemId)
            $('.itemlistContainer').css('border', '');
            $(this).css('border-color', '#1a1a1a');
            const updateURL = '{{ url('admin/edit-clients-group') }}/' + groupID + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.group.name)
                    $('#group_name').val(response.group.name);
                    $('#client_group_id').val(response.group.id);

                    $('select[name="calculation_method"]').val(response.group
                            .calculation_method_label)
                        .trigger('change');

                    $('#default_delivery_fee').val(response.group.default_delivery_fee);
                    $('#collection_amount').val(response.group.collection_amount);

                    $('select[name="service_type"]').val(response.group.service_type)
                        .trigger('change');
                    initializeSelect2InForms();
                    setTimeout(function() {
                        const calcMethod = document.getElementById('calcMethod');
                        calcMethod.innerHTML = response.viewContent;
                    }, 1000);


                    $('#delete-client-group-btn').css('display', 'flex');
                    $('#title-group').html('Edit Group');
                    $('#btn-save-group').html('Save Changes');


                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });


        $(document).on('click', '#delete-client-group-btn', function(e) {
            e.preventDefault();
            const groupId = $(this).data('id');
            const deleteUrl = `{{ url('admin/delete-clients-group') }}/${groupID}`;

            if (confirm('Are you sure you want to delete this group?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        resetGroupForm();
                        $('#delete-client-group-btn').css('display', 'none');
                    },
                    error: function(xhr) {
                        console.log('Error deleting client');
                    }
                });
            }
        });


        // End Groups


        // ZOnes


        function fetchZonesData() {
            console.log('hii');
            var element_class = $('#itemList')

            var clientCardHtml = '';
            if (currentZoneRequest) {
                currentZoneRequest.abort();
            }

            currentZoneRequest = $.ajax({
                url: '{{ route('zone-list') }}',
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,

                    search: $('#order_search').val()
                },
                success: function(response) {
                    console.log(response);

                    var count = '(' + response.zones_count + ')';
                    $('#zones_count').html(count)
                    response.zones.forEach(function(item) {


                        var customerCardHtml = `

                         <div class="itemlistContainer itemlistContainerZone" data-id="${item.id}" data-type="zone">
                                <div class="itemlistCard">
                                    <div class="itemListInfoContainer">
                                    <div class="itemListIcon">
                                    <img src="https://via.placeholder.com/150" alt="client image" width="100" height="100">
                                    </div>

                                    <div class="itemListInfo">
                                        <div class="text-slide-wrapper">
                                            <p class="text-slide">${item.name}</p>
                                        </div>

                                        <small>${item.id}</small>
                                    </div>
                                    </div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z" fill="#1A1A1A"></path></svg>
                                </div>
                                <div class="itemListDivider"></div>
                                <div class="itemListBadges">
                                    <div class="itemListBadge">
                                        <p><span>3</span> Zones</p>
                                    </div>
                                </div>
                            </div>
                        `;


                        element_class.append(customerCardHtml);
                        console.log(element_class);

                    });





                    // console.log(clientCardHtml);
                    PAGE_NUMBER++;
                    return clientCardHtml;


                },
                complete: function() {
                    currentClientRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }




        $(document).on('click', '#save-zone-btn', function(e) {
            e.preventDefault();
            // Clear previous errors
            $('input, select').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = $('#zone-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-zone') }}',
                data: formData,
                success: function(response) {
                    console.log(response);

                    $('#zone_name').val('');
                    $('#zone_id').val('');


                    $('#title-zone').html('New Zone');
                    $('#save-zone-btn').html('Save');
                    var element_class = $('#itemList');
                    element_class.empty();
                    PAGE_NUMBER = 1;
                    fetchZonesData();
                    initializeRepeater([]);

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



        function initializeRepeater(data) {
            $('#dynamic-fields').empty();
            console.log(989, data.length);

            if (data && data.length > 0) {
                console.log(8888);

                data.forEach(function(rowData) {
                    addRow(rowData);
                });
            } else {
                console.log(545);

                addRow();
            }

        }


        function updateDeleteButtons() {
            console.log(88);

            var repeater = document.getElementById('dynamic-fields');
            var rows = repeater.querySelectorAll('.dynamic-row');
            console.log(rows.length);

            rows.forEach(function(row, index) {
                var deleteButton = row.querySelector('.remove-btn');
                if (rows.length === 1) {
                    isFirstRow = true;
                    deleteButton.disabled = true;
                } else {
                    isFirstRow = false
                    deleteButton.disabled = false;
                }
            });

        }


        $(document).on('click', '.itemlistContainerZone', function() {



            const itemId = $(this).data('id');
            zoneID = itemId

            const updateURL = '{{ url('admin/edit-zone') }}/' + zoneID + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    $('#zone_name').val(response.zone.name);
                    $('#zone_id').val(response.zone.id);



                    $('#title-zone').html('Edit Zone');
                    $('#save-zone-btn').html('Save Changes');
                    $('#delete-zone-btn').css('display', 'flex');
                    initializeRepeater(response.locations || []);



                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });


        $(document).on('click', '#delete-zone-btn', function(e) {
            e.preventDefault();
            const deleteUrl = `{{ url('admin/delete-zone') }}/${zoneID}`;

            if (confirm('Are you sure you want to delete this zone?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        $('#zone_name').val('');
                        $('#zone_id').val('');


                        $('#title-zone').html('New Zone');
                        $('#save-zone-btn').html('Save');
                        $('#delete-zone-btn').css('display', 'none');
                        var element_class = $('#itemList');
                        element_class.empty();
                        PAGE_NUMBER = 1;
                        fetchZonesData();
                        initializeRepeater([]);

                    },
                    error: function(xhr) {
                        console.log('Error deleting zone');
                    }
                });
            }
        });



        // end zones




        // branches



        function fetchBranchesData() {
            console.log('hii');
            var element_class = $('#itemList')

            var clientCardHtml = '';
            if (currentBranchRequest) {
                currentBranchRequest.abort();
            }

            currentBranchRequest = $.ajax({
                url: '{{ route('branches-list') }}',
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,

                    search: $('#order_search').val()
                },
                success: function(response) {
                    console.log(response);

                    var count = '(' + response.branches_count + ')';
                    $('#branches_count').html(count)
                    response.branches.forEach(function(item) {


                        var customerCardHtml = `

                             <div class="itemlistContainer itemlistContainerBranch" data-id="${item.id}" data-type="branch">
                                <div class="itemlistCard">
                                    <div class="itemListInfoContainer">
                                    <div class="itemListIcon">
                                    <img src="https://via.placeholder.com/150" alt="client image" width="100" height="100">
                                    </div>

                                    <div class="itemListInfo">
                                        <div class="text-slide-wrapper">
                                            <p class="text-slide">${item.name}</p>
                                        </div>

                                        <small>${item.id}</small>
                                    </div>
                                    </div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.9603 5.39966C14.6674 5.10677 14.1926 5.10677 13.8997 5.39966C13.6068 5.69256 13.6068 6.16743 13.8997 6.46032L18.6893 11.25H3.5C3.08579 11.25 2.75 11.5858 2.75 12C2.75 12.4142 3.08579 12.75 3.5 12.75H18.6893L13.8997 17.5397C13.6068 17.8326 13.6068 18.3074 13.8997 18.6003C14.1926 18.8932 14.6674 18.8932 14.9603 18.6003L21.0303 12.5303C21.171 12.3897 21.25 12.1989 21.25 12C21.25 11.8011 21.171 11.6103 21.0303 11.4697L14.9603 5.39966Z" fill="#1A1A1A"></path></svg>
                                </div>
                                <div class="itemListDivider"></div>
                                <div class="itemListBadges">
                                    <div class="itemListBadge">
                                        <p>No driver</p>
                                    </div>
                                    <div class="itemListBadge">
                                        <p>No branch</p>
                                    </div>

                                </div>
                            </div>

                         `;


                        element_class.append(customerCardHtml);
                        console.log(element_class);

                    });





                    // console.log(clientCardHtml);
                    PAGE_NUMBER++;
                    return clientCardHtml;


                },
                complete: function() {
                    currentClientRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }


        function resetBranchForm() {
            $('#branch_name').val('');
            $('#branch_id').val('');
            $('#driver_id').val(null).trigger('change');
            $('#branches-title').html('New Branch');
            $('#save-branch-btn').html('Save');

            var element_class = $('#itemList');
            element_class.empty();
            PAGE_NUMBER = 1;
            fetchBranchesData()
        }




        $(document).on('click', '#save-branch-btn', function(e) {
            e.preventDefault();
            $('#branch_name_error').text('');
            $('#driver_id_error').text('');


            var formData = $('#branch-form').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('save-branch') }}',
                data: formData,
                success: function(response) {

                    console.log(response);
                    resetBranchForm()

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





        $(document).on('click', '.itemlistContainerBranch', function() {



            const itemId = $(this).data('id');
            branchID = itemId
            const updateURL = '{{ url('admin/edit-branch') }}/' + branchID + '/edit';

            $.ajax({
                url: updateURL,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $("#driver_id").select2({
                        placeholder: "Select...",
                        allowClear: false,
                        width: '100%',
                        minimumResultsForSearch: 0
                    });
                    $('#branch_name').val(response.branch.name);
                    $('#branch_id').val(response.branch.id);
                    $('select[name="driver_id"]').val(response.branch.driver_id)
                        .trigger('change');


                    $('#branches-title').html('Edit Branch');
                    $('#save-branch-btn').html('Save Changes');

                    $('#delete-branch-btn').css('display', 'flex');
                    // $('.nav-pills a[href="#branches3"]').tab('show');
                },
                error: function(xhr) {
                    console.log('Error loading group details');
                }
            });
        });


        $(document).on('click', '#delete-branch-btn', function(e) {
            e.preventDefault();
            const deleteUrl = `{{ url('admin/delete-branch') }}/${branchID}`;

            if (confirm('Are you sure you want to delete this branch?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#delete-branch-btn').css('display', 'none');
                        resetBranchForm();

                    },
                    error: function(xhr) {
                        console.log('Error deleting zone');
                    }
                });
            }
        });









        // Please don't remove this
        const svgForMainContainer = {
            client: [{
                    svg: `<svg width="192px" height="192px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.2735 2.27405C4.09919 1.51137 5.23306 1.25 6.61988 1.25H9.66987L9.67988 1.25007L9.68996 1.25H14.3201H14.3599H17.3701C18.7569 1.25 19.8907 1.51137 20.7164 2.27405C21.5323 3.02762 21.9053 4.14206 22.093 5.49711C22.0942 5.50605 22.0953 5.51502 22.0962 5.524L22.3764 8.27572C22.477 9.28721 22.225 10.2262 21.7209 10.9801C21.7463 11.0554 21.7601 11.1361 21.7601 11.22V15.71C21.7601 18.0284 21.3026 19.8478 20.0832 21.0687C18.8637 22.2898 17.044 22.75 14.7201 22.75H9.33011C8.85863 22.75 8.41009 22.7311 7.98207 22.6865C7.16648 23.3601 6.12715 23.75 5 23.75C3.55017 23.75 2.25229 23.0955 1.3949 22.0714L1.3901 22.0656C1.38191 22.0572 1.37128 22.0461 1.36037 22.0341C1.34694 22.0192 1.3304 22.0001 1.31248 21.9768C1.1778 21.8177 1.04664 21.6392 0.933962 21.4412C0.496613 20.7269 0.25 19.8855 0.25 19C0.25 17.5079 0.936775 16.1655 2.02852 15.2967C2.11337 15.2271 2.20055 15.1612 2.29004 15.0993V11.22C2.29004 11.1572 2.29775 11.0962 2.31227 11.038C1.78003 10.2749 1.51024 9.31398 1.61355 8.27572L1.61373 8.274L1.89376 5.524C1.89467 5.51502 1.89575 5.50605 1.89699 5.49711C2.08461 4.14206 2.45768 3.02762 3.2735 2.27405ZM3.37628 16.1906C3.39204 16.1827 3.40747 16.1742 3.42257 16.1653C3.89138 15.9011 4.42996 15.75 5 15.75C5.81383 15.75 6.53997 16.0411 7.10724 16.5354C7.12106 16.5474 7.13531 16.559 7.14998 16.57C7.2152 16.6189 7.28866 16.6895 7.37503 16.7845L7.37713 16.7868C7.91943 17.3784 8.25 18.1507 8.25 19C8.25 19.6094 8.07947 20.1834 7.7816 20.6664L7.78152 20.6663L7.77509 20.6772C7.61789 20.9419 7.43823 21.1633 7.24363 21.3291C7.22524 21.3448 7.20764 21.3613 7.19087 21.3786C7.18296 21.3868 7.17524 21.3952 7.1677 21.4037C7.16233 21.4098 7.15706 21.4159 7.15189 21.4222L7.13918 21.4329L7.12828 21.4425C6.56449 21.9499 5.82269 22.25 5 22.25C4.01778 22.25 3.14182 21.8115 2.55912 21.1252C2.54059 21.1011 2.52345 21.0812 2.50962 21.0659C2.49872 21.0539 2.48809 21.0427 2.4799 21.0344L2.46947 21.0219C2.37353 20.91 2.29433 20.8002 2.2337 20.6924C2.22877 20.6836 2.22367 20.6749 2.2184 20.6664C1.92053 20.1834 1.75 19.6094 1.75 19C1.75 17.9745 2.221 17.0588 2.96618 16.4675L2.96626 16.4676L2.97678 16.459C3.10433 16.3539 3.23741 16.2648 3.37628 16.1906ZM9.1773 21.2493C9.54498 20.5781 9.75 19.8073 9.75 19C9.75 17.7499 9.261 16.6227 8.48392 15.7744C8.3755 15.6552 8.2372 15.5139 8.07311 15.3876C7.24317 14.6722 6.17689 14.25 5 14.25C4.58253 14.25 4.17677 14.3052 3.79004 14.4084V12.3143C4.34732 12.5933 4.98443 12.75 5.66987 12.75C6.94675 12.75 8.13774 12.1071 8.89611 11.1242C9.56555 12.1101 10.7015 12.75 12.03 12.75C13.344 12.75 14.4644 12.1248 15.1335 11.1593C15.8934 12.1208 17.0653 12.75 18.3301 12.75C19.0374 12.75 19.692 12.5833 20.2601 12.2878V15.71C20.2601 17.8815 19.8226 19.2071 19.0219 20.0087C18.2215 20.8102 16.8962 21.25 14.7201 21.25H9.33011C9.27865 21.25 9.22771 21.2498 9.1773 21.2493ZM3.38465 5.68964L3.10618 8.42424C2.95073 9.99091 4.09213 11.25 5.66987 11.25C6.93648 11.25 8.1009 10.2005 8.22336 8.93762L8.22355 8.9357L8.84113 2.75H6.61988C5.3867 2.75 4.71058 2.98862 4.29128 3.37592C3.86363 3.77094 3.55736 4.45304 3.38465 5.68964ZM13.6412 2.75L14.2738 9.08454L14.2745 9.09135C14.2785 9.12837 14.2831 9.16527 14.2882 9.20202C14.194 10.3745 13.2623 11.25 12.03 11.25C10.6417 11.25 9.63034 10.1404 9.76638 8.75347L10.3685 2.75H13.6412ZM15.7945 9.12037C16.0227 10.3067 17.132 11.25 18.3301 11.25C19.9047 11.25 21.0396 9.99477 20.8838 8.42501L20.6053 5.68964C20.4326 4.45304 20.1263 3.77094 19.6987 3.37592C19.2794 2.98862 18.6032 2.75 17.3701 2.75H15.1889L15.7764 8.60664C15.7935 8.78042 15.7993 8.95189 15.7945 9.12037ZM2.76001 18.98C2.76001 18.5658 3.0958 18.23 3.51001 18.23H4.25V17.52C4.25 17.1058 4.58579 16.77 5 16.77C5.41421 16.77 5.75 17.1058 5.75 17.52V18.23H6.48999C6.9042 18.23 7.23999 18.5658 7.23999 18.98C7.23999 19.3942 6.9042 19.73 6.48999 19.73H5.75V20.51C5.75 20.9242 5.41421 21.26 5 21.26C4.58579 21.26 4.25 20.9242 4.25 20.51V19.73H3.51001C3.0958 19.73 2.76001 19.3942 2.76001 18.98Z" fill="#585858"></path></svg>`
                },
                {
                    title: 'Clients'
                }
            ],
            group: [{
                    svg: `<svg width="192px" height="192px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.069 9.89563C18.206 9.37985 18.2791 8.83799 18.2791 8.27907C18.2791 4.81124 15.4678 2 12 2C8.53217 2 5.72093 4.81124 5.72093 8.27907C5.72093 8.83799 5.79396 9.37985 5.93099 9.89563C3.62636 10.8255 2 13.0833 2 15.7209C2 19.1888 4.81124 22 8.27907 22C9.67181 22 10.9586 21.5466 12 20.7792C13.0414 21.5466 14.3282 22 15.7209 22C19.1888 22 22 19.1888 22 15.7209C22 13.0833 20.3736 10.8255 18.069 9.89563ZM12 3.39535C9.3028 3.39535 7.11628 5.58187 7.11628 8.27907C7.11628 8.70844 7.17169 9.12486 7.27575 9.52159C7.4009 9.99873 7.59642 10.4474 7.85055 10.8558C8.39196 11.7258 9.19939 12.4131 10.1591 12.8039C10.6013 12.984 11.0758 13.1012 11.5715 13.1442C11.7127 13.1565 11.8556 13.1628 12 13.1628C12.1444 13.1628 12.2873 13.1565 12.4285 13.1442C12.9242 13.1012 13.3987 12.984 13.8409 12.8039C14.8006 12.4131 15.608 11.7258 16.1495 10.8558C16.4036 10.4474 16.5991 9.99873 16.7243 9.52159C16.8283 9.12486 16.8837 8.70844 16.8837 8.27907C16.8837 5.58187 14.6972 3.39535 12 3.39535ZM9.65192 14.1044C8.26732 13.5457 7.12754 12.5078 6.43818 11.1961C4.65343 11.9229 3.39535 13.675 3.39535 15.7209C3.39535 18.4181 5.58187 20.6047 8.27907 20.6047C9.26648 20.6047 10.1855 20.3116 10.9538 19.8078C11.3663 19.5318 11.5929 19.3298 12 18.8842C12.7252 18.032 13.1628 16.9276 13.1628 15.7209C13.1628 15.2916 13.1074 14.8751 13.0033 14.4784C12.6766 14.5309 12.3415 14.5581 12 14.5581C11.6585 14.5581 11.3234 14.5309 10.9967 14.4784C10.4501 14.3784 10.1522 14.302 9.65192 14.1044ZM13.0462 19.8078C13.9887 18.7094 14.5581 17.2817 14.5581 15.7209C14.5581 15.162 14.4851 14.6202 14.3481 14.1044C15.7327 13.5457 16.8725 12.5078 17.5618 11.1961C19.3466 11.9229 20.6047 13.675 20.6047 15.7209C20.6047 18.4181 18.4181 20.6047 15.7209 20.6047C14.7335 20.6047 13.8145 20.3116 13.0462 19.8078Z" fill="#585858"></path></svg>`
                },
                {
                    title: 'Groups'
                }
            ],
            zone: [{
                    svg: `<svg width="192px" height="192px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.54839 2.58897C8.97742 2.578 9.44954 2.65 9.85036 2.84663L9.85495 2.84888L15.1105 5.47167C15.234 5.53458 15.446 5.58272 15.7007 5.57529C15.9547 5.56787 16.1668 5.50756 16.289 5.43819L18.6371 4.09928C19.598 3.54874 20.6444 3.41154 21.4966 3.90638C22.3489 4.40123 22.75 5.37809 22.75 6.48997V16.22C22.75 16.7061 22.5807 17.1909 22.3491 17.5879C22.1173 17.9852 21.7806 18.3677 21.3672 18.6082L21.3628 18.6108L17.0328 21.0908L17.0308 21.0919C16.6399 21.3142 16.1701 21.41 15.7417 21.421C15.3127 21.4319 14.8405 21.3599 14.4397 21.1633L14.4341 21.1606L9.17959 18.5283C9.0561 18.4654 8.84406 18.4172 8.58943 18.4247C8.33535 18.4321 8.12329 18.4924 8.0011 18.5617L5.653 19.9007C4.69202 20.4512 3.64568 20.5884 2.79344 20.0936C1.9412 19.5987 1.54004 18.6218 1.54004 17.51V7.77997C1.54004 7.30032 1.70592 6.81843 1.93674 6.42162C2.16777 6.02443 2.5059 5.64015 2.92729 5.39916L7.25931 2.918C7.65018 2.69574 8.12002 2.59991 8.54839 2.58897ZM3.67279 6.70078L7.81006 4.33117V17C7.81006 17.0112 7.81031 17.0224 7.8108 17.0335C7.61775 17.0868 7.43023 17.1608 7.25853 17.2584L4.90853 18.5984L4.90708 18.5993C4.22825 18.9884 3.77918 18.9314 3.54664 18.7964C3.31388 18.6612 3.04004 18.2981 3.04004 17.51V7.77997C3.04004 7.62962 3.09916 7.40651 3.23334 7.17582C3.36719 6.94571 3.53448 6.77982 3.67279 6.70078ZM9.85802 17.1904C9.68517 17.1027 9.49948 17.0393 9.31006 16.9964V4.25339L14.4327 6.80981C14.6053 6.8974 14.7908 6.96067 14.98 7.00349V19.7563L9.86048 17.1917L9.85802 17.1904ZM16.48 19.6788L20.6129 17.3117L20.6146 17.3107C20.7509 17.2308 20.9183 17.0637 21.0535 16.8321C21.1894 16.599 21.25 16.3738 21.25 16.22V6.48997C21.25 5.70185 20.9762 5.33871 20.7434 5.20356C20.5109 5.06854 20.0618 5.01158 19.383 5.40066L19.3815 5.40149L17.0315 6.74149C16.8601 6.83901 16.6728 6.91297 16.48 6.96622V19.6788Z" fill="#585858"></path></svg>`
                },
                {
                    title: 'Zones'
                }
            ],
            branch: [{
                    svg: `<svg width="192px" height="192px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2.75C3.31421 2.75 2.75 3.31421 2.75 4V6C2.75 6.68579 3.31421 7.25 4 7.25H7C7.68579 7.25 8.25 6.68579 8.25 6V5V4C8.25 3.31421 7.68579 2.75 7 2.75H4ZM9.75 4V4.25H12.5H15.25V4.20001C15.25 3.12581 16.1258 2.25 17.2 2.25H20.8C21.8742 2.25 22.75 3.12581 22.75 4.20001V5.79999C22.75 6.87419 21.8742 7.75 20.8 7.75H17.2C16.1258 7.75 15.25 6.87419 15.25 5.79999V5.75H13.25V11.75H15.25V11.7C15.25 10.6258 16.1258 9.75 17.2 9.75H20.8C21.8742 9.75 22.75 10.6258 22.75 11.7V13.3C22.75 14.3742 21.8742 15.25 20.8 15.25H17.2C16.1258 15.25 15.25 14.3742 15.25 13.3V13.25H13.25V18C13.25 18.6858 13.8142 19.25 14.5 19.25H15.25V19.2C15.25 18.1258 16.1258 17.25 17.2 17.25H20.8C21.8742 17.25 22.75 18.1258 22.75 19.2V20.8C22.75 21.8742 21.8742 22.75 20.8 22.75H17.2C16.1258 22.75 15.25 21.8742 15.25 20.8V20.75H14.5C12.9858 20.75 11.75 19.5142 11.75 18V12.5V5.75H9.75V6C9.75 7.51421 8.51421 8.75 7 8.75H4C2.48579 8.75 1.25 7.51421 1.25 6V4C1.25 2.48579 2.48579 1.25 4 1.25H7C8.51421 1.25 9.75 2.48579 9.75 4ZM16.75 20V20.8C16.75 21.0458 16.9542 21.25 17.2 21.25H20.8C21.0458 21.25 21.25 21.0458 21.25 20.8V19.2C21.25 18.9542 21.0458 18.75 20.8 18.75H17.2C16.9542 18.75 16.75 18.9542 16.75 19.2V20ZM16.75 13.3V12.5V11.7C16.75 11.4542 16.9542 11.25 17.2 11.25H20.8C21.0458 11.25 21.25 11.4542 21.25 11.7V13.3C21.25 13.5458 21.0458 13.75 20.8 13.75H17.2C16.9542 13.75 16.75 13.5458 16.75 13.3ZM16.75 5.79999V5V4.20001C16.75 3.95421 16.9542 3.75 17.2 3.75H20.8C21.0458 3.75 21.25 3.95421 21.25 4.20001V5.79999C21.25 6.04579 21.0458 6.25 20.8 6.25H17.2C16.9542 6.25 16.75 6.04579 16.75 5.79999Z" fill="#585858"></path></svg>`
                },
                {
                    title: 'Branches'
                }
            ],

        }
        // Random Data can be deleted
        const tabData = {
            client: [{
                    id: 1,
                    name: "\"BK\" 2Gout Burger - Kitchen Park Jarir ",
                    description: "22110"
                },
                {
                    id: 2,
                    name: "\"BK\" 3roof",
                    description: "21833"
                },
            ],
            group: [{
                    id: 1,
                    name: "Fees 0",
                    description: "931"
                },
                {
                    id: 2,
                    name: "10 KM (1 SAR per KM Above)",
                    description: "191"
                },
            ],
            zone: [{
                    id: 1,
                    name: "15SAR",
                    description: "646"
                },
                {
                    id: 2,
                    name: "Zone_Test_1",
                    description: "61"
                },
            ],
            branch: [{
                id: 1,
                name: "New branch group",
                description: "357"
            }, ],
        };



        $('.tabContentContainer').on('scroll', function() {
            var $this = $(this);
            console.log(8787);
            // fetchGroupsData()
            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 1) {
                console.log(888);

                if (currentTab == 'client') {
                    console.log(66);

                    fetchClientData();
                }

                if (currentTab == 'group') {
                    console.log(66);

                    fetchGroupsData();
                }


                if (currentTab == 'zone') {
                    console.log(66);

                    fetchZonesData();
                }

                if (currentTab == 'branch') {
                    console.log(66);

                    fetchBranchesData();
                }



            }
        });



        function renderItems(tab) {
            $('#itemList').empty();
            PAGE_NUMBER = 1;
            let items = tabData[tab];
            let itemList = '';
            $(".nav-link").removeClass("active"); // Remove the 'active' class from all tabs
            $(`.nav-link[data-tab="${tab}"]`).addClass("active");

            switch (tab) {
                case 'client':
                    fetchClientData()
                    // console.log('jj', itemList);

                    break;
                case 'group':
                    itemList = fetchGroupsData();
                    break;
                case 'zone':
                    itemList = fetchZonesData();
                    break;
                case 'branch':
                    itemList = fetchBranchesData();
                    break;
                default:
                    itemList = '<p>No items available for this tab</p>';
                    break;
            }

            // $('#itemList').html(itemList);
        }


        function displayMainContent(tab) {
            const tabName = tab.charAt(0).toUpperCase() + tab.slice(1) + 's';
            console.log(svgForMainContainer[`${tab}`][1].title)
            console.log(tab)
            const addButton = `
                    <button class="flex items-center justify-center  gap-3 px-4 py-2 text-white rounded-md NewButton new-option" data-type="${tab}">+ New ${tabName.slice(0, -1)}</button>
                `;
            const welcomeMessage = `
                <div class="welcomeSection">
                    ${svgForMainContainer[`${tab}`][0].svg}
                    <h1>${svgForMainContainer[`${tab}`][1].title}</h1>
                    <p>
                    No ${tabName} have been created, yet!

                    </p>
                    ${addButton}
                </div>
                `;
            $('.main-container').html(welcomeMessage);
        }

        function displayItemDetails(item, tab) {
            const tabName = tab.charAt(0).toUpperCase() + tab.slice(1); // Capitalize the tab name
            let itemDetails = '';

            // Display different content based on the selected tab
            if (tab === 'client') {
                itemDetails = `
                       @include('admin.pages.clients.show')
                    `;
            } else if (tab === 'group') {
                itemDetails = `
                        @include('admin.pages.clients_group.add')
                    `;
            } else if (tab === 'zone') {
                itemDetails = `
                        @include('admin.pages.zones.add')
                    `;

            } else if (tab === 'branch') {
                itemDetails = `
                         @include('admin.pages.branches.add')
                    `;
            }


            $('.main-container').html(itemDetails);

            // initializeSelect2();


        }


        function initializeSelectModal(selector, dropdownParent, placeholder) {
            $(selector).select2({
                dropdownParent: $(dropdownParent),
                placeholder: placeholder,
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0,
            }).on('select2:select', function() {
                $(dropdownParent + ' .customSelectLegend').addClass('positioned');
            });
        }

        function initClientBranchesSelect() {
            $('#addNewBranchModal').on('shown.bs.modal', function() {
                initializeSelectModal(".select2insidemodal",
                    "#addNewBranchModal .modal-body .phoneNewBranch", "Code");
                initializeSelectModal(".branchGroup",
                    "#addNewBranchModal .modal-body .branchNameNewBranch", "Branch Group");
                initializeSelectModal(".driverGroup",
                    "#addNewBranchModal .modal-body .driverGroupNewBranch", "Driver Group");
                initializeSelectModal(".counryAddress",
                    "#addNewBranchModal .modal-body .countryNewBranch", "Country");
                initializeSelectModal(".cityAddress",
                    "#addNewBranchModal .modal-body .cityNewBranch", "City");
                initializeSelectModal(".areaAddress",
                    "#addNewBranchModal .modal-body .areaNewBranch", "Area");
                initializeSelectModal(".interId",
                    "#addNewBranchModal .modal-body .interIdNewBranch", "ID Type");
                initializeSelectModal(".dayBussinessHours",
                    "#addNewBranchModal .modal-body .dayHoursNewBranch", "Day");
                initializeSelectModal(".startBussinessHours",
                    "#addNewBranchModal .modal-body .startHoursNewBranch", "Start");
                initializeSelectModal(".endBussinessHours",
                    "#addNewBranchModal .modal-body .endHoursNewBranch", "End");


            });
        }

        function initClientUploadBranchesSelect() {
            $('#uploadBranchModal').on('shown.bs.modal', function() {
                initializeSelectModal(".counryAddress",
                    "#uploadBranchModal .modal-body .countryUploadBranch", "Country");
                initializeSelectModal(".cityAddress",
                    "#uploadBranchModal .modal-body .cityUploadBranch", "City");
                initializeSelectModal(".areaAddress",
                    "#uploadBranchModal .modal-body .areaUploadBranch", "Area");



                initializeSelectModal(".typeBranch",
                    "#uploadBranchModal .modal-body .typeUploadBranch", "Type");

                let typeSelect = $("#uploadBranchModal .modal-body .typeUploadBranch");

                if (typeSelect.length === 0) {
                    console.error("Type select element not found!");
                    return;
                }

                function toggleSheets() {
                    let selectedText = typeSelect.find("option:selected").text().trim();

                    if (selectedText === "Public") {
                        $(".publicSheet").removeClass("d-none");
                        $(".wasfetySheet").addClass("d-none");
                    } else if (selectedText === "Wasfety") {
                        $(".wasfetySheet").removeClass("d-none");
                        $(".publicSheet").addClass("d-none");
                    }
                }

                toggleSheets();

                typeSelect.on("change", function() {
                    toggleSheets();
                });



            });
        }

        function generateTimeOptions() {
            let options = '';
            for (let i = 0; i < 24; i++) {
                for (let j = 0; j < 60; j += 30) {
                    let time = `${String(i).padStart(2, '0')}:${String(j).padStart(2, '0')}`;
                    options += `<option value="${time}">${time}</option>`;
                }
            }
            return options;
        }

        function initClientBranchesRepeater() {
            $('.branchBussinessHoursContainer').empty();

            console.log(6345723);

            $('.branchBussinessHoursContainer').append(`

                    <span class="visibilty-hidden"></span>
                    <div class="branchBussinessHours">
                        <div class="modalSelectBox dayHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend">Day<span class="text-danger">*</span></label>
                            <select class="dayBussinessHours" name="business_hours[0][day]">
                                <option></option>
                                <option selected disabled>Day</option>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>
                        </div>
                        <div class="modalSelectBox startHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend">Start<span class="text-danger">*</span></label>
                            <select class="startBussinessHours" name="business_hours[0][start]">
                                <option></option>
                                ${generateTimeOptions()}
                            </select>
                        </div>
                        <div class="modalSelectBox endHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend">End<span class="text-danger">*</span></label>
                            <select class="endBussinessHours" name="business_hours[0][end]">
                                <option></option>
                                ${generateTimeOptions()}
                            </select>
                        </div>
                         <div class="action-btn d-flex justify-content-between align-items-center">
                                <button type="button" class="addBussinessHours">
                                    <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z"
                                            fill="#F46624"></path>
                                    </svg>
                                </button>
                                <button  type="button" class="removeBussinessHours">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z"
                                            fill="#949494"></path>
                                    </svg>
                                </button>
                            </div>
                    </div>
                `);

        }

        $(document).on('click', '#upload-client-branch', function() {

            $('#uploadBranchesForm')[0].reset();
            initClientUploadBranchesSelect();
            $('#branch-area2').prop('disabled', true);
        });

        $(document).on('click', '#create-client-branch', function() {
            console.log('hh', clientID, $("#client_id"));

            const fieldset = $('#custom_id_fieldset');

            fieldset.addClass('invisible');


            $('#client-branch-form')[0].reset();
            $('#exampleModalLabel').text('Add new branch');
            $("#branch_client_id").val(clientID);

            modalCLosed();
            var myModal = new bootstrap.Modal(document.getElementById('addNewBranchModal'));
            myModal.show();
            initClientBranchesSelect();
            initClientBranchesRepeater();

        });

        $(document).on('click', '.edit-client-branch', function() {

            var myModal = new bootstrap.Modal(document.getElementById('addNewBranchModal'));
            myModal.show();
            initClientBranchesSelect();
            initClientBranchesRepeater()
            var branchID = $(this).data('id')

            $.ajax({
                url: '{{ route('edit-client-branch') }}',
                type: 'GET',
                data: {
                    id: branchID
                },
                success: function(response) {
                    var branch = response.branch;
                    var user_branch = response.user;

                    if (branch) {


                        const fieldset = $('#custom_id_fieldset');
                        console.log(branch.pickup_id);

                        if (branch.pickup_id === 2) {
                            fieldset.removeClass('invisible');
                        } else {
                            fieldset.addClass('invisible');
                        }

                        $('#exampleModalLabel').text('Edit Branch');
                        $('#client-branch-form input[name="branch_name"]').val(branch.name);
                        $('#branch_client_id').val(clientID);
                        $('#client-branch-form input[name="branch_id"]').val(branch.id);
                        if (user_branch) {
                            $('#client-branch-form input[name="user_branch_id"]').val(
                                user_branch.id);
                            $('#client-branch-form input[name="branch_email"]').val(
                                user_branch.email);
                        }

                        $('#client-branch-form input[name="branch_phone"]').val(branch
                            .phone);
                        $('#client-branch-form select[name="client_group_id"]').val(branch
                                .client_group_id)
                            .trigger('change');
                        $('#client-branch-form select[name="driver_group_id"]').val(branch
                                .driver_group_id)
                            .trigger('change');
                        $('#client-branch-form input[name="lat"]').val(branch.lat);
                        $('#client-branch-form input[name="lng"]').val(branch.lng);
                        $('#client-branch-form select[name="country"]').val(branch.country)
                            .trigger('change');
                        $('#client-branch-form select[name="city_id"]').val(branch.city_id)
                            .trigger('change');
                        $('#client-branch-form select[name="area_id"]').val(branch.area_id)
                            .trigger('change');
                        $('#client-branch-form select[name="pickup_id"]').val(branch
                            .pickup_id).trigger('change');
                        $('#client-branch-form input[name="street"]').val(branch.street);
                        $('#client-branch-form input[name="custom_id"]').val(branch
                            .custom_id);
                        $('#client-branch-form input[name="landmark"]').val(branch
                            .landmark);
                        $('#client-branch-form input[name="building"]').val(branch
                            .building);
                        $('#client-branch-form input[name="floor"]').val(branch.floor);
                        $('#client-branch-form input[name="apartment"]').val(branch
                            .apartment);
                        $('#client-branch-form textarea[name="discription"]').val(branch
                            .discription);

                        updateMap(parseFloat(branch.lat), parseFloat(branch.lng));

                        console.log(branch.business_hours, branch.business_hours.length);
                        console.log(Object.keys(branch.business_hours).length);

                        if (branch.business_hours && Object.keys(branch.business_hours)
                            .length > 0) {
                            console.log(78787);

                            $('.branchBussinessHoursContainer .branchBussinessHours').not(
                                ':first').remove();

                            Object.values(branch.business_hours).forEach(function(hour,
                                index) {
                                if (index > 0) {

                                    $('.branchBussinessHoursContainer .branchBussinessHours:last .addBussinessHours')
                                        .click();
                                }

                                var row = $(
                                    '.branchBussinessHoursContainer .branchBussinessHours'
                                ).eq(
                                    index);


                                row.find(
                                        'select[name^="business_hours["][name$="[day]"]'
                                    ).val(hour.day)
                                    .trigger('change');
                                row.find(
                                    'select[name^="business_hours["][name$="[start]"]'
                                ).val(hour
                                    .start).trigger('change');
                                row.find(
                                        'select[name^="business_hours["][name$="[end]"]'
                                    ).val(hour.end)
                                    .trigger('change');
                            });
                        }


                    } else {
                        $('#exampleModalLabel').text('Add new branch');

                        $('#client-branch-form').trigger("reset");
                        $('#repeater .row').not(':first')
                            .remove();
                        $('#repeater .row:first select').val('').trigger(
                            'change');
                    }


                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });








        });


        $(document).on('click', '#create-client-user', function() {
            // Create a new Bootstrap Modal instance
            var myModal = new bootstrap.Modal(document.getElementById('addNewUserModal'));
            myModal.show(); // Show the modal

            // Initialize Select2 for static elements inside the modal
            $('#addNewUserModal').on('shown.bs.modal', function() {



                initializeSelectModal(".select2insidemodal",
                    "#addNewUserModal .modal-body .phoneNewBranch", "Code");


                initializeSelectModal(".userTemplate",
                    "#addNewUserModal .modal-body .userTemplateNewUser", "User template");


                initializeSelectModal(".grandAccess",
                    "#addNewUserModal .modal-body .grandAccessNewUser", "");
                initializeSelectModal(".clientBranch",
                    "#addNewUserModal .modal-body .clientBranchNewUser",
                    "Client");
                initializeSelectModal(".branchesBranch",
                    "#addNewUserModal .modal-body .branchesBranchNewUser",
                    "Branches");
                $(".clientsNewUserSelect").select2({
                    placeholder: "Clients",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })

                initializeSelectModal(".userTemplateExisting",
                    "#addNewUserModal .modal-body .userTemplateExistingUser",
                    "User");
                initializeSelectModal(".grandAccessExisting",
                    "#addNewUserModal .modal-body .grandAccessExistingUser", "");
                initializeSelectModal(".clientBranchExisting",
                    "#addNewUserModal .modal-body .clientBranchExistingUser",
                    "Client");
                initializeSelectModal(".branchesBranchExisting",
                    "#addNewUserModal .modal-body .branchesBranchExistingUser",
                    "Branches");
                $(".clientsExistingUserSelect").select2({
                    placeholder: "Clients",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })
                $("#branchesAccessSelect").select2({
                    placeholder: "Branches",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })
                $("#branchesAccessSelectExisitng").select2({
                    placeholder: "Branches",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })

            });
            $(".clientsUserSelect").on("select2:select", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });

            $(".clientsUserSelect").on("select2:opening", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });
        });

        $(document).on('select2:select select2:unselect', '.clientsUserSelect', function() {
            // Determine the target container based on the context
            var targetBox = $(this).closest('.dataContent').find('.selectedClientsBox');
            var selectedCount = $(this).val() ? $(this).val().length : 0;

            // Update the placeholder with the number of selected clients
            var placeholder = selectedCount > 0 ? selectedCount + " Clients" : "Clients";
            $(this).siblings('.select2').find('.select2-selection__rendered').attr('title',
                placeholder);
            $(this).siblings('.select2').find('.select2-selection__rendered').text(
                placeholder);

            // Update the selected clients list
            updateSelectedClients(targetBox);

            // Show or hide the selected clients box based on the selection count
            if (selectedCount === 0) {
                targetBox.hide(); // Hide the box if no clients are selected
                // $('.select2-selection__rendered').hide();
            } else {
                targetBox.show(); // Show the box if there are selected clients
                // $('.select2-selection__rendered').show();
            }
        });

        $(document).on('click',
            '.select2-container .select2-selection--multiple .select2-selection__rendered',
            function() {
                // Determine the target container and toggle it
                var targetBox = $(this).closest('.dataContent').find('.selectedClientsBox');
                targetBox.toggle();

                // Update the selected clients list
                updateSelectedClients(targetBox);
            });

        function updateSelectedClients(targetBox) {
            // Identify the related select element
            var selectElement = targetBox.closest('.dataContent').find('.clientsUserSelect');
            var selectedClients = selectElement.val();

            // Get the list container in the target box
            var listContainer = targetBox.find('.selectedClientsList');

            if (selectedClients && selectedClients.length > 0) {
                listContainer.empty();

                selectedClients.forEach(function(clientId) {
                    var clientName = selectElement.find("option[value='" + clientId + "']")
                        .text();
                    listContainer.append('<li>' + clientName + '</li>');
                });
            } else {
                listContainer.empty();
                targetBox.hide();
            }
        }


        function initializeSelect2() {
            $(".operator").select2({
                placeholder: "Select a Page...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });
            $("#calculation_method").select2({
                placeholder: "Select a Page...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });
            // $(".select2").select2({
            //     placeholder: "Select a Page...",
            //     allowClear: false,
            //     width: '100%',
            //     minimumResultsForSearch: 0
            // });


            $('.operator').on('select2:open', function() {

                $('.select2-search__field').attr('placeholder', 'Search...');
            });


            $(".branches .operator[multiple]").select2({
                placeholder: "Driver",
                minimumResultsForSearch: Infinity,
                allowClear: false,
                width: '100%',
                closeOnSelect: false,
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var checkbox = $(
                        '<input type="checkbox" style="margin-right: 8px;" />');
                    var dataMulti = $('<span class="select2MultiOption">' + data.text +
                        '</span>');
                    if (data.selected) {
                        checkbox.prop("checked", true);
                    }

                    var span = $('<span class="d-flex align-items-center">').append(
                        checkbox).append(
                        dataMulti);

                    return span;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });

            function updateSelectedCount() {
                var selectedCount = $(".branches .operator[multiple]").select2('data').length;
                if (selectedCount > 0) {
                    $('#selectDriverCount').text(selectedCount + " Drivers");
                } else {
                    $('#selectDriverCount').text("Drivers");
                }
            }

            $(".branches .operator[multiple]").on('change', function() {
                updateSelectedCount();
            });

            $(document).on("click", ".select2-results__option--selectable", function(e) {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop("checked", !checkbox.prop("checked"));
                if (checkbox.prop("checked")) {
                    $(this).addClass("select2-results__option--selected");
                } else {
                    $(this).removeClass("select2-results__option--selected");
                }
            });

            $(document).on("click", ".select2-results__option--selectable input[type='checkbox']",
                function(e) {
                    e.stopPropagation();
                });
            updateSelectedCount();



        }

        // toggle password
        $('#togglePassword').click(function() {
            // Toggle the input type between 'password' and 'text'
            var inputType = $('#passwordInput').attr('type') === 'password' ? 'text' :
                'password';
            $('#passwordInput').attr('type', inputType);

            $(this).find('path').css('fill', function() {
                return $(this).css('fill') === 'rgb(148, 148, 148)' ? 'orange' :
                    '#949494';
            });
        });

        // handle tabs in add user modal in clients new user
        $('#accessSelect').change(function() {
            var selectedValue = $(this).val();

            // Hide both data sections initially
            $('#clientsData').hide();
            $('#branchesData').hide();

            // Show data based on the selected value
            if (selectedValue === '1') { // Clients selected
                $('#clientsData').show();
            } else if (selectedValue === '2') { // Branches selected
                $('#branchesData').show();
            }
        });

        // Trigger the change event to display the content based on the initial selection new user
        $('#accessSelect').trigger('change');

        // handle tabs in add user modal in clients existing user
        $(document).on('change', '#accessSelectExisiting', function() {

            var selectedValue = $(this).val();

            // Hide both data sections initially
            $('#clientsDataExisting').hide();
            $('#branchesDataExisting').hide();

            // Show data based on the selected value
            if (selectedValue === '3') { // Clients selected
                $('#clientsDataExisting').show();
            } else if (selectedValue === '4') { // Branches selected
                $('#branchesDataExisting').show();
            }
        });

        // Trigger the change event to display the content based on the initial selection existing user
        $('#accessSelectExisiting').trigger('change');


        // Handle clicking tabs in side section
        $('.nav-link').click(function() {

            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            const selectedTab = $(this).data('tab');
            currentTab = selectedTab;
            displayMainContent(selectedTab);
            renderItems(selectedTab);
        });

        // Tabs in modal add new user (new user and exisiting user)
        $(document).on('click', '#uniqueTabGroup .btn[data-tab]', function() {
            const targetType = $(this).data('tab');
            if ($(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).is(':visible')) {
                $(this).removeClass('active');
                $(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).hide();
            } else {

                $('#uniqueTabGroup .btn').removeClass('active');
                $('#uniqueTabGroup .tab-pane').hide();


                $(this).addClass('active');
                $(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).show();
            }
        });




        // Handle clicking on an item card
        $(document).on('click', '.itemlistContainer', function() {
            const itemId = $(this).data('id');
            const itemType = $(this).data('type');
            const selectedItem = tabData[itemType].find(item => item.id === itemId);
            $('.itemlistContainer').css('border', '');
            $(this).css('border-color', '#1a1a1a');
            displayItemDetails(selectedItem, itemType);
        });

        $(document).on('click', '.itemlistContainerClient', function() {
            console.log($("#client_id"));
            console.log('test', $(this).data('is_active'));


            const itemId = $(this).data('id');
            clientID = itemId;
            $("#client_id").val(itemId)
            $('.itemlistContainer').css('border', '');
            $(this).css('border-color', '#1a1a1a');
            console.log($(this).data('client_country'), $(this).data('client_currency'), $(this).data(
                'account_number'));

            $('.main-container').html(`@include('admin.pages.clients.show')`);


            if ($(this).data('is_active') == 1) {
                console.log($('.client-active-toggle').length); // Should print "1" if found

                console.log('checked', $('.client-active-toggle'));

                $('#client_active_input').attr('checked', true);

            } else {
                $('.client-active-toggle').prop('checked', false).trigger('change');
            }




            $('#client_name, #client_name2').html($(this).data('name'));
            $('#integration_token').html($(this).data('integration_token'))
            $('#client_id').html(itemId);
            $('#total_orders_count').html($(this).data('total_orders_count'));
            $('#total_balance_client').html($(this).data('total_balance_client'));
            $('#client_country').val($(this).data('client_country'));
            $('#account_number').val($(this).data('account_number'));
            $('#client_city').val($(this).data('client_city'));
            $('#client_currency').val($(this).data('client_currency'));
            $('#client_parial_pay').val($(this).data('client_parial_pay'));
            $('#client_defualt_preperation_time').val($(this).data(
                'client_defualt_preperation_time'));
            $('#client_min_preperation_time').val($(this).data(
                'client_min_preperation_time'));
            $('#client_client_group').val($(this).data('client_client_group'));
            $('#client_operator_group').val($(this).data('client_operator_group'));
            $('#price_order_group').val($(this).data('price_order_group'));
            renderClientOrdersDataTable(itemId);
            renderClientBranchesDataTable(itemId);
            renderClientUsersDataTable(itemId);
            // initializeSelect2();
            // displayItemDetails(selectedItem, itemType);
        });




        function renderClientOrdersDataTable(id) {
            var ordersTable = $('#orders-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-orders') }}",
                    "type": "GET",
                    'data': {
                        'id': id
                    }
                },

                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "branch"
                    },
                    {
                        "data": "customer_name"
                    },
                    {
                        "data": "customer_area"
                    },

                    {
                        "data": "status"
                    },

                ],
                "pageLength": 5,
                "lengthChange": false,
                "searching": true,
            });
            $('.dt-input').attr('placeholder', 'Search here... ');

        }

        function renderClientBranchesDataTable(id) {
            if ($.fn.DataTable.isDataTable('#branches-table')) {
                $('#branches-table').DataTable().destroy();
            }

            var branchesTable = $('#branches-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-branches') }}",
                    "type": "GET",
                    "data": {
                        'id': id
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": null
                    }, // For status toggle
                    {
                        "data": null
                    },
                    {
                        "data": null
                    }
                ],
                "createdRow": function(row, data, dataIndex) {
                    console.log('hh');

                    let cardHtml = `

                    <div class="clientBranchCards">
                            <div class="clientBranchCard">
                                <div class="clientBranchCardInfo">
                                    <div>
                                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2.75C3.31421 2.75 2.75 3.31421 2.75 4V6C2.75 6.68579 3.31421 7.25 4 7.25H7C7.68579 7.25 8.25 6.68579 8.25 6V5V4C8.25 3.31421 7.68579 2.75 7 2.75H4ZM9.75 4V4.25H12.5H15.25V4.20001C15.25 3.12581 16.1258 2.25 17.2 2.25H20.8C21.8742 2.25 22.75 3.12581 22.75 4.20001V5.79999C22.75 6.87419 21.8742 7.75 20.8 7.75H17.2C16.1258 7.75 15.25 6.87419 15.25 5.79999V5.75H13.25V11.75H15.25V11.7C15.25 10.6258 16.1258 9.75 17.2 9.75H20.8C21.8742 9.75 22.75 10.6258 22.75 11.7V13.3C22.75 14.3742 21.8742 15.25 20.8 15.25H17.2C16.1258 15.25 15.25 14.3742 15.25 13.3V13.25H13.25V18C13.25 18.6858 13.8142 19.25 14.5 19.25H15.25V19.2C15.25 18.1258 16.1258 17.25 17.2 17.25H20.8C21.8742 17.25 22.75 18.1258 22.75 19.2V20.8C22.75 21.8742 21.8742 22.75 20.8 22.75H17.2C16.1258 22.75 15.25 21.8742 15.25 20.8V20.75H14.5C12.9858 20.75 11.75 19.5142 11.75 18V12.5V5.75H9.75V6C9.75 7.51421 8.51421 8.75 7 8.75H4C2.48579 8.75 1.25 7.51421 1.25 6V4C1.25 2.48579 2.48579 1.25 4 1.25H7C8.51421 1.25 9.75 2.48579 9.75 4ZM16.75 20V20.8C16.75 21.0458 16.9542 21.25 17.2 21.25H20.8C21.0458 21.25 21.25 21.0458 21.25 20.8V19.2C21.25 18.9542 21.0458 18.75 20.8 18.75H17.2C16.9542 18.75 16.75 18.9542 16.75 19.2V20ZM16.75 13.3V12.5V11.7C16.75 11.4542 16.9542 11.25 17.2 11.25H20.8C21.0458 11.25 21.25 11.4542 21.25 11.7V13.3C21.25 13.5458 21.0458 13.75 20.8 13.75H17.2C16.9542 13.75 16.75 13.5458 16.75 13.3ZM16.75 5.79999V5V4.20001C16.75 3.95421 16.9542 3.75 17.2 3.75H20.8C21.0458 3.75 21.25 3.95421 21.25 4.20001V5.79999C21.25 6.04579 21.0458 6.25 20.8 6.25H17.2C16.9542 6.25 16.75 6.04579 16.75 5.79999Z" fill="#585858"></path></svg>
                                    </div>
                                    <p class="branchName">${data.name} [${data.id}]</p>
                                    <p>${data.city}</p>
                                </div>
                                <div class="clientBranchCardActions">

                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="status_toggle_${data.model.id}">Status</label>
                                            <input class="form-check-input status-toggle  position-relative" data-id="${data.model.id}" type="checkbox" ${data.model.is_active ? 'checked' : ''} role="switch" id="status_toggle_${data.model.id}" name="c">
                                        </div>


                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="auto_dispatch_${data.model.id}"> Auto Dispatch</label>
                                            <input class="form-check-input position-relative auto-dispatch-toggle"
                                                ${data.model.auto_dispatch ? 'checked' : ''} id="auto_dispatch_${data.model.id}"
                                                data-id="${data.model.id}" type="checkbox" role="switch" name="c">
                                        </div>
                                        <button class="edit-client-branch"  data-bs-toggle="modal" data-id="${data.model.id}"
                                                            data-bs-target="#addNewBranchModal"> <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>
                                        </button>


                                </div>
                            </div>
                    </div>



                    `;
                    $(row).html(cardHtml);
                },
                "paging": true,
                "pageLength": 10,
                "lengthChange": false,
                "ordering": false,
                "searching": true,
                "info": false
            });
            $('.dt-input').attr('placeholder', 'Search here... ');
        }

        function renderClientUsersDataTable(id) {

            if ($.fn.DataTable.isDataTable('#users-table')) {
                $('#users-table').DataTable().destroy();
            }
            var usersTable = $('#users-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-users') }}",
                    "type": "GET",
                    'data': function(d) {
                        d.id = id;
                    },
                    "dataSrc": function(response) {
                        console.log('AJAX Response:', response);

                        // Update Select2 dropdown
                        $('#client-exist-user-form #user_id').empty();
                        $('#client-exist-user-form #user_id').append(
                            '<option value="" selected="selected" disabled>User</option>'
                        );

                        if (response.all_users) {
                            response.all_users.forEach(function(user) {
                                $('#client-exist-user-form #user_id').append(
                                    '<option value="' +
                                    user.id + '">' + user.name + '</option>'
                                );
                            });

                            $('#client-exist-user-form #user_id').select2({
                                width: '100%'
                            });
                        }

                        // Return only the data part to DataTables
                        return response.data;
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
                        "data": null
                    },
                    {
                        "data": null
                    }
                ],
                "createdRow": function(row, data, dataIndex) {
                    let cardHtml = `

                  <div class="clientBranchCards">
                        <div class="clientBranchCard">
                            <div class="clientBranchCardInfo">
                                <div>
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.1197 12.7796C12.0497 12.7696 11.9597 12.7696 11.8797 12.7796C10.1197 12.7196 8.71973 11.2796 8.71973 9.50955C8.71973 7.69955 10.1797 6.22955 11.9997 6.22955C13.8097 6.22955 15.2797 7.69955 15.2797 9.50955C15.2697 11.2796 13.8797 12.7196 12.1197 12.7796Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18.7398 19.3799C16.9598 21.0099 14.5998 21.9999 11.9998 21.9999C9.39976 21.9999 7.03977 21.0099 5.25977 19.3799C5.35977 18.4399 5.95977 17.5199 7.02977 16.7999C9.76977 14.9799 14.2498 14.9799 16.9698 16.7999C18.0398 17.5199 18.6398 18.4399 18.7398 19.3799Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 22.0005C17.5228 22.0005 22 17.5233 22 12.0005C22 6.47764 17.5228 2.00049 12 2.00049C6.47715 2.00049 2 6.47764 2 12.0005C2 17.5233 6.47715 22.0005 12 22.0005Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </div>
                                <p class="branchName">${data.name}</p>
                                <p>${data.email}</p>
                            </div>
                            <div class="clientBranchCardActions">




                                        <div class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                            <label class="form-check-label" for="status_toggle_${data.model.id}">Status</label>
                                            <input class="form-check-input position-relative status-toggle "
                                                ${data.model.status ? 'checked' : ''} id="status_toggle_${data.model.id}"
                                                data-id="${data.model.id}" type="checkbox" role="switch" name="c">
                                        </div>
                                <button class="edit-client-user" data-role = '${data.last_role}' data-user='${JSON.stringify(data)}'  data-bs-toggle="modal"
                                                            data-bs-target="#editUserModal">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 9.93958C10.5074 9.93958 9.5 10.9469 9.5 12.1896C9.5 13.4322 10.5074 14.4396 11.75 14.4396C12.9926 14.4396 14 13.4322 14 12.1896C14 10.9469 12.9926 9.93958 11.75 9.93958ZM8 12.1896C8 10.1185 9.67893 8.43958 11.75 8.43958C13.8211 8.43958 15.5 10.1185 15.5 12.1896C15.5 14.2606 13.8211 15.9396 11.75 15.9396C9.67893 15.9396 8 14.2606 8 12.1896Z" fill="#585858"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.35347 3.95976C9.0858 3.51381 8.52209 3.37509 8.10347 3.62414L8.09256 3.63063L6.36251 4.62054C5.81514 4.93332 5.6263 5.64247 5.9394 6.18438L5.93888 6.18348C6.47395 7.10662 6.61779 8.1184 6.13982 8.94781C5.66192 9.7771 4.71479 10.1596 3.65 10.1596C3.01678 10.1596 2.5 10.6812 2.5 11.3096V13.0696C2.5 13.6979 3.01678 14.2196 3.65 14.2196C4.71479 14.2196 5.66192 14.6021 6.13982 15.4314C6.61773 16.2607 6.47398 17.2723 5.93909 18.1953C5.62642 18.7372 5.81491 19.4457 6.3621 19.7584L8.10352 20.7549C8.52214 21.004 9.08577 20.8654 9.35345 20.4195L9.46093 20.2338C9.9958 19.311 10.802 18.6821 11.7587 18.6821C12.7162 18.6821 13.52 19.3115 14.05 20.2353L14.0507 20.2366L14.1565 20.4194C14.4242 20.8654 14.9879 21.0041 15.4065 20.755L15.4174 20.7485L17.1475 19.7586C17.6934 19.4467 17.8851 18.7474 17.5698 18.1934C17.0358 17.2709 16.8926 16.2601 17.3702 15.4314C17.8481 14.6021 18.7952 14.2196 19.86 14.2196C20.4932 14.2196 21.01 13.6979 21.01 13.0696V11.3096C21.01 10.6764 20.4884 10.1596 19.86 10.1596C18.7952 10.1596 17.8481 9.7771 17.3702 8.94781C16.8923 8.11856 17.036 7.10701 17.5708 6.18402C17.8836 5.64216 17.6951 4.93348 17.1479 4.62077L15.4065 3.62423C14.9879 3.37518 14.4242 3.51381 14.1565 3.95976L14.0491 4.14537C13.5142 5.06817 12.708 5.69709 11.7512 5.69709C10.7939 5.69709 9.99021 5.06783 9.46021 4.14412L9.45933 4.14258L9.35347 3.95976ZM7.34248 2.3315C8.50191 1.64614 9.97257 2.06661 10.6446 3.19612L10.6491 3.20378L10.7591 3.39381L10.7607 3.39659C11.1307 4.04205 11.5166 4.19709 11.7512 4.19709C11.987 4.19709 12.3759 4.04073 12.7509 3.39381L12.8654 3.19609C13.5374 2.06658 15.0081 1.64613 16.1675 2.33151L17.8921 3.3184C19.1647 4.04562 19.5963 5.6767 18.8694 6.93479L18.8689 6.93569C18.4939 7.58256 18.5528 7.99577 18.6698 8.19886C18.7869 8.40207 19.1148 8.65959 19.86 8.65959C21.3116 8.65959 22.51 9.84281 22.51 11.3096V13.0696C22.51 14.5212 21.3268 15.7196 19.86 15.7196C19.1148 15.7196 18.7869 15.9771 18.6698 16.1803C18.5528 16.3834 18.4939 16.7966 18.8689 17.4435L18.8712 17.4475C19.5944 18.7131 19.1657 20.3327 17.8925 21.0605L16.1674 22.0477C15.008 22.733 13.5374 22.3125 12.8654 21.1831L12.8609 21.1754L12.7509 20.9854L12.7493 20.9826C12.3793 20.3371 11.9934 20.1821 11.7587 20.1821C11.523 20.1821 11.1341 20.3384 10.7591 20.9854L10.6446 21.1831C9.97263 22.3126 8.50199 22.733 7.34257 22.0477L5.6179 21.0608C4.34558 20.3334 3.91378 18.7023 4.6406 17.4444L4.64112 17.4435C5.01605 16.7966 4.95721 16.3834 4.84018 16.1803C4.72308 15.9771 4.39521 15.7196 3.65 15.7196C2.18322 15.7196 1 14.5212 1 13.0696V11.3096C1 9.85794 2.18322 8.65959 3.65 8.65959C4.39521 8.65959 4.72308 8.40207 4.84018 8.19886C4.95721 7.99577 5.01605 7.58256 4.64112 6.93569L4.6406 6.93479C3.91378 5.67684 4.34518 4.04597 5.61749 3.31864L7.34248 2.3315Z" fill="#585858"></path></svg>
                                </button>

                            </div>
                        </div>
                    </div>



                    `;
                    $(row).html(cardHtml);
                },
                "paging": true,
                "pageLength": 3,
                "lengthChange": false,
                "ordering": false,
                "searching": true,
                "info": false
            });
            $('.dt-input').attr('placeholder', 'Search here... ');
        }



        $(document).on('change', '.client-active-toggle', function() {

            console.log(898);

            var clientId = clientID;
            var newStatus = $(this).is(':checked');
            var status = 0;
            if (newStatus) {
                status = 1;
            }
            console.log(status);


            $.ajax({
                url: '{{ route('changeClientActive') }}',
                method: 'get',
                data: {
                    id: clientId,
                    is_active: status
                },
                success: function(response) {
                    console.log('Status updated successfully.');
                    $('#itemList').empty();
                    PAGE_NUMBER = 1;
                    fetchClientData();
                },
                error: function(error) {
                    console.log('Failed to update status.');
                }
            });
        });




        $(document).on('change', '#branches-table .status-toggle', function() {

            console.log(898);

            var branchId = $(this).data('id');
            var newStatus = $(this).is(':checked');
            var status = 0;
            if (newStatus) {
                status = 1;
            }
            console.log(status);


            $.ajax({
                url: '{{ route('change-client-branch-status') }}',
                method: 'GET',
                data: {
                    id: branchId,
                    status: status
                },
                success: function(response) {
                    console.log('Status updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update status.');
                }
            });
        });

        $(document).on('change', '#branches-table .auto-dispatch-toggle', function() {

            var branchId = $(this).data('id');
            var newAutoDispatch = $(this).is(':checked');
            var auto_dispatch = 0;
            if (newAutoDispatch) {
                auto_dispatch = 1;
            }
            $.ajax({
                url: '{{ route('change-client-branch-auto-dispatch') }}',
                method: 'POST',
                data: {
                    id: branchId,
                    auto_dispatch: auto_dispatch
                },
                success: function(response) {
                    console.log('Auto dispatch updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update auto dispatch.');
                }
            });
        });



        $(document).on('change', '#users-table .status-toggle', function() {

            var newStatus = $(this).is(':checked');
            var status = 0;
            var id = $(this).data('id');
            if (newStatus) {
                status = 1;
            }

            $.ajax({
                url: '{{ route('change-client-user-status') }}',
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(response) {
                    console.log('Status updated successfully.');
                },
                error: function(error) {
                    console.log('Failed to update status.');
                }
            });
        });








        // Handle new option click
        $(document).on('click', '.new-option', function() {


            const type = $(this).data('type');

            displayForm(type);
            // Switch tab to the one related to the item
            $('.nav-link').removeClass('active');
            $(`[data-tab="${type}"]`).addClass('active');
            renderItems(type);
        });
        // Handle new option click
        $('.new-option').on('click', function() {
            console.log("type")
            const type = $(this).data('type');
            console.log(type)
            displayForm(type);
            // Switch tab to the one related to the item
            $('.nav-link').removeClass('active');
            $(`[data-tab="${type}"]`).addClass('active');
            renderItems(type);
        });

        // Display the form for adding a new item
        function displayForm(tab) {
            let formHtml = '';
            switch (tab) {
                case 'client':
                    formHtml = `
                   @include('admin.pages.clients.add')
                    `;
                    break;
                case 'group':
                    formHtml = `
                    @include('admin.pages.clients_group.add')
                    `;
                    break;
                case 'zone':

                    formHtml = `
                    @include('admin.pages.zones.add')
                    `;
                    break;
                case 'branch':
                    formHtml = `
                    @include('admin.pages.branches.add')
                    `;
                    break;
                default:
                    formHtml = `<p>Unknown tab selected</p>`;
                    break;
            }
            $('.main-container').html(formHtml);
            // initializeSelect2();

            initializeSelect2InForms();
        }

        function initializeSelect2InForms() {
            $("#driver_id").select2({
                placeholder: "Select...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });
            $(".operator").select2({
                placeholder: "Select...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });
            $("#service_type").select2({
                placeholder: "Select...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });

            $("#calculation_method").select2({
                placeholder: "Select a Page...",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });


            $('.operator').on('select2:open', function() {

                $('.select2-search__field').attr('placeholder', 'Search...');
            });
            $(".phoneNumberCreateClient").select2({
                placeholder: "Code",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            });
            $(".cityAddress").select2({
                placeholder: "City",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('select2:select', function() {
                $('.cityNewBranch .customSelectLegend').addClass('positioned');
            });
            $(".countryAddress").select2({
                placeholder: "Country",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('select2:select', function() {
                $('.countryNewBranch .customSelectLegend').addClass('positioned');
            });
            $(".currency").select2({
                placeholder: "Currency",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('select2:select', function() {
                $('.currencySelect .customSelectLegend').addClass('positioned');
            });
            $(".clientGroup").select2({
                placeholder: "Client group",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('select2:select', function() {
                $('.clientGroupCreateUser .customSelectLegend').addClass('positioned');
            });
            $(".driverGroup").select2({
                placeholder: "Driver Group",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('select2:select', function() {
                $('.driverGroupCreateUser .customSelectLegend').addClass('positioned');
            });



        }

        renderItems('client');
        displayMainContent('client');



        $(document).on("mouseenter", ".text-slide-wrapper", function() {
            console.log("hovered")
            const $textSlide = $(this).find(".text-slide");
            const textContent = $textSlide.text();
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

        let rowCount = 0;
        let rowCountInBranchModal = 0;

        // Function to add a new row in zones

        function addRow(rowData = {}) {
            rowCount++;
            const uniqueCityDropdownId = `cityDropdown-${rowCount}`;
            const uniqueAreaDropdownId = `areaDropdown-${rowCount}`;
            var citiesOptions = '';
            @foreach ($all_cities as $city)
                citiesOptions +=
                    `<option value="{{ $city->id }}" ${rowData.city_id == '{{ $city->id }}' ? 'selected' : ''}>{{ $city->name }}</option>`;
            @endforeach

            var areasOptions = '';
            @foreach ($all_areas as $area)
                areasOptions +=
                    `<option value="{{ $area->id }}" ${rowData.area_id == '{{ $area->id }}' ? 'selected' : ''}>{{ $area->name }}</option>`;
            @endforeach
            const newRow = `
                <div class=" dynamic-row">

                    <div class="custom-fieldset">
                        <label for="template-name" class="custom-legend">
                            City <span class="text-danger d-none">*</span>
                        </label>
                        <select class="operator city-select" name="city[]">
                            <option></option>
                           ${citiesOptions}
                        </select>
                    </div>
                    <div class="custom-fieldset">
                        <label for="template-name" class="custom-legend">
                            Area <span class="text-danger d-none">*</span>
                        </label>
                        <select class="operator " name="area[]" disabled>
                            <option value="" selected="selected" disabled>Area</option>
                             ${areasOptions}
                        </select>
                    </div>


                    <div class="action-btn d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-success add-btn">
                            <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z" fill="#F46624"></path></svg>
                        </button>
                        <button type="button" class="btn btn-outline-danger remove-btn">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z" fill="#949494"></path><path d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z" fill="#949494"></path><path d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z" fill="#949494"></path><path d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z" fill="#949494"></path></svg>
                        </button>
                    </div>
                </div>`;
            $('#dynamic-fields').append(newRow);
            initializeSelect2();
        }


        $(document).on('change', '.city-select', function() {
            var areaSelect = $(this).closest('.flex').find('.area-select');
            if ($(this).val()) {
                areaSelect.prop('disabled', false);
            } else {
                areaSelect.prop('disabled', true);
            }
        });
        $(document).on('change', '.city-select', function() {
            var cityId = $(this).val();
            var areaSelect = $(this).closest('.flex').find('.area-select');

            if ($(this).val()) {
                $.ajax({
                    url: '{{ route('city-areas') }}',
                    type: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        console.log(response); // Log the response to check the data
                        areaSelect.prop('disabled', false);
                        areaSelect.empty();
                        areaSelect.append(
                            '<option value="" selected="selected" disabled>Area</option>'
                        );

                        $.each(response, function(key, area) {

                            areaSelect.append('<option value="' + area.id + '">' +
                                area.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            } else {
                areaSelect.prop('disabled', true);
                areaSelect.empty();
                areaSelect.append('<option value="" selected="selected" disabled>Area</option>');
            }
        });



        $(document).on('click', '.add-btn', function() {

            addRow();
        });


        $(document).on('click', '.remove-btn', function() {
            updateDeleteButtons()
            if (isFirstRow === false) {
                $(this).closest('.dynamic-row').remove();
            }

        });



        $(document).on('click', '.addBussinessHours', function() {

            rowCountInBranchModal++;


            const newRow = `
        <div class="branchBussinessHours">
                    <div class="modalSelectBox dayHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Day<span
                                class="text-danger">*</span></label>
                        <select class="dayBussinessHours${rowCountInBranchModal}" name="business_hours[${rowCountInBranchModal}][day]" >
                            <option></option>
                            <option value="Sunday">Sunday</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>

                        </select>
                    </div>
                    <div class="modalSelectBox startHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Start<span
                                class="text-danger">*</span></label>
                        <select class="startBussinessHours${rowCountInBranchModal}"   name="business_hours[${rowCountInBranchModal}][start]" >
                            <option ></option>
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                for ($j = 0; $j < 60; $j += 30) {
                                    $time = sprintf('%02d:%02d', $i, $j);
                                    echo "<option value=\"$time\">$time</option>";
                                }
                            }
                            ?>


                        </select>
                    </div>
                    <div class="modalSelectBox endHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">End<span
                                class="text-danger">*</span></label>
                        <select class="endBussinessHours${rowCountInBranchModal}"   name="business_hours[${rowCountInBranchModal}][end]" >
                            <option></option>
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                for ($j = 0; $j < 60; $j += 30) {
                                    $time = sprintf('%02d:%02d', $i, $j);
                                    echo "<option value=\"$time\">$time</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="action-btn d-flex justify-content-between align-items-center">
                        <button type="button" class="addBussinessHours">
                            <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z"
                                    fill="#F46624"></path>
                            </svg>
                        </button>
                        <button type="button" class="removeBussinessHours">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z"
                                    fill="#949494"></path>
                                <path
                                    d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z"
                                    fill="#949494"></path>
                                <path
                                    d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z"
                                    fill="#949494"></path>
                                <path
                                    d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z"
                                    fill="#949494"></path>
                            </svg>
                        </button>
                    </div>

                </div>`;
            $('.branchBussinessHoursContainer').append(newRow); // Append the new row
            initializeSelectModal(".dayBussinessHours" + rowCountInBranchModal,
                `#addNewBranchModal .modal-body .dayHoursNewBranch${rowCountInBranchModal}`,
                "Day");
            initializeSelectModal(".startBussinessHours" + rowCountInBranchModal,
                "#addNewBranchModal .modal-body .startHoursNewBranch" +
                rowCountInBranchModal, "Start");
            initializeSelectModal(".endBussinessHours" + rowCountInBranchModal,
                "#addNewBranchModal .modal-body .endHoursNewBranch" +
                rowCountInBranchModal,
                "End");


        });


        // this is bussiness hours in add new branch modal remove row
        $(document).on('click', '.removeBussinessHours', function() {
            const branchRows = $('.branchBussinessHours'); // Select all rows
            if ($(this).closest('.branchBussinessHours')[0] !== branchRows.first()[0]) {
                // If the clicked row is not the first one, remove it
                $(this).closest('.branchBussinessHours').remove();
            }
        });

        $(document).on('keyup', '#searchCity', function() {
            var searchValue = $(this).val().toLowerCase();
            $('.dropdown-item').each(function() {
                var city = $(this).text().toLowerCase();
                if (city.indexOf(searchValue) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Dropdown item click functionality using event delegation
        $(document).on('click', '.dropdown-item', function() {
            var selectedCity = $(this).data('city');
            $('#selectedCity').text(selectedCity);
        });



        let cropper; // Declare cropper variable outside of the event listener

        // Open modal on file selection
        $(document).on("click", "#uploadButton", function(e) {
            e.stopPropagation();
            $("#fileInput").click();
        });

        $(document).on("change", "#fileInput", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                console.log("Selected file name:", file.name);
                reader.onload = function(e) {
                    $("#uploadedImage").attr("src", e.target.result);

                    // Show the modal (handled by Bootstrap)
                    $("#photoModal").modal("show");

                    // Initialize Cropper after the image is loaded in the modal
                    $("#photoModal").on("shown.bs.modal", function() {
                        // Destroy the previous cropper instance if it exists
                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper($("#uploadedImage")[0], {
                            aspectRatio: 1, // Maintain square aspect ratio
                            viewMode: 1,
                            dragMode: "move",
                            autoCropArea: 0.8,
                            cropBoxResizable: true, // Allow resizing of the crop box
                            cropBoxMovable: true, // Allow moving the crop box
                            toggleDragModeOnDblclick: false,
                            background: false,
                        });
                        $(".crop-container").css("background-color",
                            "transparent");
                    });
                };
                reader.readAsDataURL(file);
            }
        });


        // Optional: Reset cropper when the modal is closed
        $("#photoModal").on("hidden.bs.modal", function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        $(document).on("click", "#cropAndSaveButton", function() {
            if (cropper) {
                console.log("Saving cropped image...");

                // Get the cropped image as a canvas
                const croppedCanvas = cropper.getCroppedCanvas({
                    width: 300, // Set desired width
                    height: 300 // Set desired height
                });

                // Convert the canvas to a blob and create a URL
                croppedCanvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    console.log("Cropped image URL:", url);

                    // Create an image element to display the cropped image
                    const img = document.createElement("img");
                    img.src = url;
                    img.style.borderRadius = "50%"; // Make the image circular
                    img.style.maxWidth = "300px"; // Limit the width of the image
                    img.style.height = "auto"; // Keep the aspect ratio

                    // Append the image to the body or a specific container
                    $("#croppedImage").html(img);

                    // Optionally, you can append it to a specific element
                    // $("#someContainer").append(img);

                    // Close the modal
                    $("#photoModal").modal("hide");

                    // Destroy the cropper instance to free up resources
                    cropper.destroy();
                });
            } else {
                console.log("Cropper is not initialized.");
            }
        });


        // $('#order_search').on('input', function() {
        //     let inputVal = $(this).val().trim();

        //     if (inputVal.length > 0) {
        //         PAGE_NUMBER = 1;
        //         toggleDisplay()
        //     } else {
        //         console.log('begin');

        //         $('.clientsSearch').hide(); // Hide the search card
        //         $('.demondDriver').show();
        //          // Show the demondDriver
        //          PAGE_NUMBER = 1;
        //         toggleDisplay()
        //     }
        // });

        $('#order_search').trigger('input');


    });
</script>
@include('admin.pages.clients.scripts')
@include('admin.pages.calc_methods.scripts')

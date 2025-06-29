<style>
    .tooltip {
        background-color: transparent !important;
    }

    .tooltip .tooltip-inner {
        background-color: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        padding: 6.4px 9.6px;
        color: #fff;
        font-size: 11.2px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .tooltip .tooltip-arrow {
        display: none;
    }

    .customSidebar {
        background-color: #000;
        /* border-radius: 12.8px; */
        padding: 14.8px 12.8px;
        box-sizing: content-box;
        justify-content: center;
        align-items: center;

    }

    .customSidebar .nav-link {
        padding: 0;
        /* background-color: rgba(58, 58, 58, 0.5); */
        background-color: rgba(255, 255, 255, 0.13);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex !important;
        justify-content: center;
        align-items: center;
        color: #fff;
    }

    .customSidebar .nav-link svg {
        filter: grayscale(1);
    }

    .customSidebar .nav-pills {
        gap: 8.8px;
    }

    .customSidebar .tooltip-element {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    .dropdown-toggle::after {
        border: none;
        transform: rotate(-90deg);
        position: absolute;
        content: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTEuMnB4IiBoZWlnaHQ9IjUuNnB4IiB2aWV3Qm94PSIwIDAgMTQgNyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTEuMTkwMzcgMC40MzY0NjdDMS40ODMyNyAwLjE0MzU3NCAxLjk1ODE0IDAuMTQzNTc0IDIuMjUxMDMgMC40MzY0NjdMNi41OTc3IDQuNzgzMTNDNi44MTgxNCA1LjAwMzU3IDcuMTgzMjcgNS4wMDM1NyA3LjQwMzcxIDQuNzgzMTNMMTEuNzUwNCAwLjQzNjQ2N0MxMi4wNDMzIDAuMTQzNTc0IDEyLjUxODEgMC4xNDM1NzQgMTIuODExIDAuNDM2NDY3QzEzLjEwMzkgMC43MjkzNiAxMy4xMDM5IDEuMjA0MjMgMTIuODExIDEuNDk3MTNMOC40NjQzNyA1Ljg0Mzc5QzcuNjU4MTQgNi42NTAwMiA2LjM0MzI3IDYuNjUwMDIgNS41MzcwNCA1Ljg0Mzc5TDEuMTkwMzcgMS40OTcxM0MwLjg5NzQ4IDEuMjA0MjMgMC44OTc0OCAwLjcyOTM2IDEuMTkwMzcgMC40MzY0NjdaIiBmaWxsPSIjRkZGRkZGIj48L3BhdGg+PC9zdmc+");
        right: -16px;
    }

    .customSidebar .dropdown-menu.show {
        top: -45px !important;
        left: 80px !important;
        background-color: #585858;
        border-radius: 9.6px;
        padding: 5.4px;
        /* gap: 4.3px; */
        display: flex;
        flex-direction: column;
    }

    .customSidebar .dropdown-menu .dropdown-item {
        background: rgba(58, 58, 58, 0.5);
        color: #fff;
        font-size: 12.2px;
        font-weight: 600;
        padding: 9.6px;
        border-radius: 9.6px;
        margin-bottom: 3px;
    }

    .dropdown-menu .dropdown-item:hover {
        background: transparent;
    }

    .dropdown-menu.show.reportsSideMenu {
        bottom: -55px !important;
        top: unset !important;
        scrollbar-width: none;
        scrollbar-color: transparent;
        -ms-overflow-style: none;
    }

    #sidebar {
        transition: transform 0.3s ease;
    }

    #sidebar.collapse {
        transform: translateX(-120%);
        overflow: hidden;
    }

    #sidebar.collapse.show {
        transform: translateX(0%);
    }

    #sidebarToggle {
        display: none;
    }

    .dropdownNotifyHeader {
        font-size: 11.6px;
        font-weight: 600;
        color: #fff;
        margin: 10px 0;

    }

    .dropdownNotifyHeader span:last-child {
        background: #494949;
        padding: 1.6px 6.6px;
        border-radius: 50%;
    }

    .notifySideMenu {
        width: 350px;
    }

    .notifySideMenuContainer .dropdown-menu {
        max-height: 200px;
        overflow-y: scroll;
    }

    .notifySideMenuContainer .dropdown-menu::-webkit-scrollbar {
        width: 8px !important;
    }

    .notifySideMenuContainer .dropdown-menu::-webkit-scrollbar-thumb {
        background-color: #494949;
        border-radius: 0 10px 10px 0;
    }

    .notifySideMenuContainer .dropdown-menu::-webkit-scrollbar-track {
        background-color: #585858;
        border-radius: 0 10px 10px 0;
    }

    .dropdownNotifyHeader {
        position: sticky;
        top: 0;
        background-color: transparent;
        z-index: 1;
        transition: background-color 0.3s ease;
        top: -5px !important;
        padding: 5px;
    }


    .dropdownNotifyHeader {
        background-color: #585858;
    }

    .customSidebar .dropdown li {
        margin: .2rem 0;
    }

    .mobileNavbar {
        display: none;
    }

    .menuMobileItem {
        display: none;
    }

    .originalMiniIcons {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

    }

    @media (max-width:992px) {
        #sidebarToggle {
            display: block;
        }

        .mobileNavbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;

            width: 100%;
            z-index: 9999;
        }

        .menuMobileItem {
            display: inline-block;
        }

        .mobileNavbar .logoWrapper {
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .mobileNavbar .logoWrapper img {
            width: 100%;
            height: 100%;
        }

        body {
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            padding: 0 !important;
        }

        .customSidebar {
            width: 100% !important;
            top: 65px !important;
            border-radius: 0 !important;
            z-index: 9999 !important;
            height: auto !important;
            align-items: baseline !important;
            background: #0f275b !important;
            overflow-y: scroll !important;
            margin: 0 !important;
            justify-content: flex-start;
            position: absolute !important;
            padding: 0;

        }

        .dropdownNotifyHeader {
            margin: 0 !important;
        }

        .logoImage {
            display: none;
        }

        .customSidebar .nav-link {
            padding: 0;
            background-color: transparent !important;
            border-radius: 50%;
            width: 100% !important;
            height: auto;
            display: flex !important;
            justify-content: flex-start !important;
            align-items: center;
            color: #fff;
            gap: .5rem;
            font-size: 14px;
        }

        .logoImage {
            display: none !important;
        }

        .customSidebar .nav-pills {
            width: 100%;
            margin-bottom: 0 !important;
            gap: .3rem;
            padding: 14.8px 0 12.8px 12.8px;
        }

        .notifySideMenuContainer {
            border-top: none !important;
        }

        .customSidebar .tooltip-element {
            display: none !important;
        }

        .dropdown-toggle::after {
            transform: rotate(0deg);
            right: unset;
            position: relative;
        }

        .customSidebar .dropdown-menu.show {
            padding: 0 !important;
            top: -35px !important;
            left: 34px !important;
            background-color: transparent;
            border-radius: 9.6px;
            padding: 5.4px;
            display: flex;
            flex-direction: column;
            box-shadow: none !important;
            border: none;
            position: relative !important;
            transition: 0.5s;
        }

        .customSidebar .dropdown-menu .dropdown-item {
            background-color: transparent !important;
            color: #cbcbcb;
        }

        .customSidebar .dropdown-menu.show {
            top: 0 !important;
            position: relative !important;
            transform: none !important;
        }

        .ProfileSetting,
        .notifySideMenuContainer {
            display: none !important;
        }

        .mobileNavbar .ProfileSetting,
        .mobileNavbar .notifySideMenuContainer {
            display: flex !important;
        }

        .mobileNavbar .ProfileSetting .dropdown-toggle::after,
        .mobileNavbar .notifySideMenuContainer .dropdown-toggle::after {
            display: none !important;
        }

        .dropdown-menu.show.reportsSideMenu {
            bottom: unset !important;
            top: 21px !important;
            padding: 0;
            scrollbar-width: none;
            scrollbar-color: transparent;
            -ms-overflow-style: none;
        }

        .mobileMiniIcons .dropdown-menu.show.reportsSideMenu {
            bottom: unset !important;
            top: 13px !important;
            padding: 0;
            scrollbar-width: none;
            scrollbar-color: transparent;
            -ms-overflow-style: none;
        }

        .customSidebar li .dropdown-menu.show {
            bottom: unset !important;
            top: unset !important;
            padding: 0;
        }

        .mobileNavbar .notifySideMenuContainer {
            padding: 0 !important;
        }

        .customSidebar .dropdown-menu .dropdown-item {
            padding: 6.6px 9.6px;
        }

        .customSidebar .dropdown li {
            margin: 0;
        }

        .mobileNavbar .reportsSideMenu a {
            font-size: 12px;
            font-weight: 300;
            background-color: #0b285b;
            color: #fff;
        }

        .mobileNavbar .dropdownNotifyHeader {
            background-color: #0b285b !important;
        }

        .mobileMiniIcons .menuMobileItem {
            display: none;
        }

        .mobileMiniIcons svg {
            width: 20px;
            height: 20px;
            filter: grayScale(1);
        }

        .mobileMiniIcons .nav-link {
            padding: .5rem .5rem;
        }

        .mobileMiniIcons .notifyCount {
            top: 2px;
            color: #fff;
            font-size: 6px;
            left: 3px;

        }

        .dropdown-menu .dropdown-item:hover {
            background: #0b285b;
        }

    }

    @media (max-width: 380px) {
        .mobileNavbar .logoWrapper img {
            width: 80%;
            height: 100%;
        }

        .mobileNavbar .notifySideMenu {
            width: 321px;
        }

    }

    @media (max-height: 600px) and (min-width: 992px) {
        .customSidebar .nav-link {
            padding: 0;
            background-color: transparent !important;
            border-radius: 50%;
            width: 25px !important;
            height: 25px !important;
            display: flex !important;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .customSidebar .dropdown-menu.show {
            top: -30px !important;
        }

        .dropdown-menu.show.reportsSideMenu {
            bottom: -80px !important;
        }

    }


    @media (max-width: 992px) {
        .main-container {
            width: 100%;
            margin: 2rem 0;
        }

        .sideSectionContainer {
            display: block !important;
            overflow-y: scroll !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0 1rem;
        }

        .sideSectionContainer::webkit-scrollbar {
            display: none;
        }

        .sideSection {
            width: 100% !important;
        }

        .sideSectionMapContainer {
            overflow-y: scroll !important;
        }
    }

    /* Base styles for the menu button */
    .menu-icon {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 16px;
        height: 12px;
        cursor: pointer;
        position: relative;
        margin: 0;
    }

    .menu-icon .line {
        height: 1px;
        background-color: #fff;
        border-radius: 2px;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .menu-icon .top {
        transform-origin: top left;
    }

    .menu-icon .middle {
        opacity: 1;
    }

    .menu-icon .bottom {
        transform-origin: bottom left;
    }

    /* Checkbox checked state for animation */
    .menu-checkbox:checked+.menu-icon .top {
        transform: rotate(45deg) translate(0px, 0px);
    }

    .menu-checkbox:checked+.menu-icon .middle {
        opacity: 0;
    }

    .menu-checkbox:checked+.menu-icon .bottom {
        transform: rotate(-45deg) translate(0px, -1px);
    }

    .notifyCount {
        position: absolute;
        background: red;
        padding: 4px 6px;
        font-size: 8px;
        border-radius: 50%;
        line-height: normal;
        top: 11px;
        left: 0;
    }
</style>

<div class="mobileNavbar bg-sidebar">
    <!-- Sidebar toggle button -->
    <input type="checkbox" id="sidebarToggle" class="menu-checkbox" hidden>
    <label for="sidebarToggle" class="menu-icon">
        <span class="line top"></span>
        <span class="line middle"></span>
        <span class="line bottom"></span>
    </label>

    <div class="logoWrapper">
        <img src="//alshrouqdelivery.b-cdn.net/public/new/src/assets/images/logo.png" alt="" width="100"
            height="100" />
    </div>


    <div class="d-flex align-items-center mobileMiniIcons"></div>

</div>

<div class="d-flex flex-column flex-shrink-0 customSidebar rounded-4 bg-sidebar " id="sidebar"
    style="width: 4.5rem; height: 94vh;position: fixed;top: 6px; z-index:1060;">

    <a href="#" class="d-block link-dark text-decoration-none logoImage" data-bs-toggle="tooltip"
        data-bs-placement="right" style="width: 48.95px; height: 48.95px; border-radius: 50%; margin-bottom: 16px;"
        title="ALSHROUQ">
        <img src="//alshrouqdelivery.b-cdn.net/public/new/src/assets/images/logo ALSHROUQ DELIVERY white.png"
            alt="ALSHROUQ" width="100" height="100" style="width: 100%; height: 100%; border-radius: 50%;" />

    </a>
    {{-- @if (auth()->user()->user_role?->value == 1) --}}
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
        <li class="nav-item">
            @can('show_dashboard')
                {{-- <a href="{{ route('dashboard') }}" class="nav-link" aria-current="page" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="Dashboard">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.3276 2.002C16.676 1.97034 18.0786 2.31338 19.1908 3.09294L27.2376 8.73808L27.2385 8.73876C28.0454 9.30215 28.7246 10.1701 29.2005 11.083C29.6767 11.9963 30 13.0518 30 14.0392V23.5322C30 27.0831 27.1028 30 23.5405 30H8.45953C4.89569 30 2 27.0815 2 23.5192V13.8566C2 12.9302 2.29225 11.9274 2.72172 11.0509C3.15125 10.1744 3.76533 9.32767 4.49918 8.75594L11.5187 3.27919C12.5928 2.44273 13.9791 2.03366 15.3276 2.002Z"
                            fill="#f9f9f9"></path>
                        <path
                            d="M12.424 4.98846L5.956 10.0354C4.876 10.8765 4 12.667 4 14.0248V22.9291C4 25.7169 6.268 28 9.052 28H22.948C25.732 28 28 25.7169 28 22.9411V14.1931C28 12.7391 27.028 10.8765 25.84 10.0474L18.424 4.84426C16.744 3.66664 14.044 3.72673 12.424 4.98846Z"
                            fill="#F46624"></path>
                        <path
                            d="M19.3333 14.3325C18.781 14.3325 18.3333 14.7802 18.3333 15.3325C18.3333 15.8848 18.781 16.3325 19.3333 16.3325H19.5858L16.5554 19.3629L15.0987 17.1778C14.9325 16.9285 14.6633 16.7669 14.3652 16.7374C14.0671 16.7079 13.7714 16.8136 13.5596 17.0254L9.29289 21.2921C8.90237 21.6826 8.90237 22.3158 9.29289 22.7063C9.68342 23.0968 10.3166 23.0968 10.7071 22.7063L14.1112 19.3022L15.568 21.4872C15.7341 21.7365 16.0033 21.8981 16.3015 21.9277C16.5996 21.9572 16.8953 21.8515 17.1071 21.6396L20.9999 17.7468V17.9992C20.9999 18.5515 21.4476 18.9992 21.9999 18.9992C22.5522 18.9992 22.9999 18.5515 22.9999 17.9992V15.3453C23.0007 15.2835 22.9958 15.2216 22.9852 15.1605C22.9797 15.1291 22.9728 15.0982 22.9645 15.0679C22.9202 14.9059 22.8344 14.7527 22.7071 14.6254C22.6021 14.5204 22.4794 14.4436 22.3487 14.395C22.2401 14.3546 22.1226 14.3325 21.9999 14.3325H19.3333Z"
                            fill="white"></path>
                    </svg>
                </a> --}}

                <a href="{{ route('OrderDashboard.dashboard') }}" class="nav-link" aria-current="page"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.3276 2.002C16.676 1.97034 18.0786 2.31338 19.1908 3.09294L27.2376 8.73808L27.2385 8.73876C28.0454 9.30215 28.7246 10.1701 29.2005 11.083C29.6767 11.9963 30 13.0518 30 14.0392V23.5322C30 27.0831 27.1028 30 23.5405 30H8.45953C4.89569 30 2 27.0815 2 23.5192V13.8566C2 12.9302 2.29225 11.9274 2.72172 11.0509C3.15125 10.1744 3.76533 9.32767 4.49918 8.75594L11.5187 3.27919C12.5928 2.44273 13.9791 2.03366 15.3276 2.002Z"
                            fill="#f9f9f9"></path>
                        <path
                            d="M12.424 4.98846L5.956 10.0354C4.876 10.8765 4 12.667 4 14.0248V22.9291C4 25.7169 6.268 28 9.052 28H22.948C25.732 28 28 25.7169 28 22.9411V14.1931C28 12.7391 27.028 10.8765 25.84 10.0474L18.424 4.84426C16.744 3.66664 14.044 3.72673 12.424 4.98846Z"
                            fill="#F46624"></path>
                        <path
                            d="M19.3333 14.3325C18.781 14.3325 18.3333 14.7802 18.3333 15.3325C18.3333 15.8848 18.781 16.3325 19.3333 16.3325H19.5858L16.5554 19.3629L15.0987 17.1778C14.9325 16.9285 14.6633 16.7669 14.3652 16.7374C14.0671 16.7079 13.7714 16.8136 13.5596 17.0254L9.29289 21.2921C8.90237 21.6826 8.90237 22.3158 9.29289 22.7063C9.68342 23.0968 10.3166 23.0968 10.7071 22.7063L14.1112 19.3022L15.568 21.4872C15.7341 21.7365 16.0033 21.8981 16.3015 21.9277C16.5996 21.9572 16.8953 21.8515 17.1071 21.6396L20.9999 17.7468V17.9992C20.9999 18.5515 21.4476 18.9992 21.9999 18.9992C22.5522 18.9992 22.9999 18.5515 22.9999 17.9992V15.3453C23.0007 15.2835 22.9958 15.2216 22.9852 15.1605C22.9797 15.1291 22.9728 15.0982 22.9645 15.0679C22.9202 14.9059 22.8344 14.7527 22.7071 14.6254C22.6021 14.5204 22.4794 14.4436 22.3487 14.395C22.2401 14.3546 22.1226 14.3325 21.9999 14.3325H19.3333Z"
                            fill="white"></path>
                    </svg>
                </a>
            @endcan

        </li>
        <li>
            @can('basic_dispatcher_view')
                <a href="{{ route('index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Dispatcher">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22.666 13.3332H25.3327C27.9994 13.3332 29.3327 11.9998 29.3327 9.33317V6.6665C29.3327 3.99984 27.9994 2.6665 25.3327 2.6665H22.666C19.9993 2.6665 18.666 3.99984 18.666 6.6665V9.33317C18.666 11.9998 19.9993 13.3332 22.666 13.3332Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M6.66602 29.3332H9.33268C11.9993 29.3332 13.3327 27.9998 13.3327 25.3332V22.6665C13.3327 19.9998 11.9993 18.6665 9.33268 18.6665H6.66602C3.99935 18.6665 2.66602 19.9998 2.66602 22.6665V25.3332C2.66602 27.9998 3.99935 29.3332 6.66602 29.3332Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M7.99935 13.3332C10.9449 13.3332 13.3327 10.9454 13.3327 7.99984C13.3327 5.05432 10.9449 2.6665 7.99935 2.6665C5.05383 2.6665 2.66602 5.05432 2.66602 7.99984C2.66602 10.9454 5.05383 13.3332 7.99935 13.3332Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M23.9993 29.3332C26.9449 29.3332 29.3327 26.9454 29.3327 23.9998C29.3327 21.0543 26.9449 18.6665 23.9993 18.6665C21.0538 18.6665 18.666 21.0543 18.666 23.9998C18.666 26.9454 21.0538 29.3332 23.9993 29.3332Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </a>
            @endcan
        </li>
        <li class="dropdown">
            @can('control_drivers')
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                    <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Operators"></span>

                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.2134 14.4927C12.0801 14.4793 11.9201 14.4793 11.7734 14.4927C8.60008 14.386 6.08008 11.786 6.08008 8.58602C6.08008 5.31935 8.72008 2.66602 12.0001 2.66602C15.2667 2.66602 17.9201 5.31935 17.9201 8.58602C17.9067 11.786 15.3867 14.386 12.2134 14.4927Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M21.8799 5.33398C24.4665 5.33398 26.5465 7.42732 26.5465 10.0007C26.5465 12.5207 24.5465 14.574 22.0532 14.6673C21.9465 14.654 21.8265 14.654 21.7065 14.6673"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                            d="M5.54695 19.414C2.32029 21.574 2.32029 25.094 5.54695 27.2407C9.21362 29.694 15.227 29.694 18.8936 27.2407C22.1203 25.0807 22.1203 21.5607 18.8936 19.414C15.2403 16.974 9.22695 16.974 5.54695 19.414Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M24.4531 26.666C25.4131 26.466 26.3198 26.0793 27.0665 25.506C29.1465 23.946 29.1465 21.3727 27.0665 19.8127C26.3331 19.2527 25.4398 18.8793 24.4931 18.666"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="menuMobileItem">Operators</span>
                </a>

                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="{{ route('operators') }}">Operators</a></li>
                    <li><a class="dropdown-item" href="{{ route('reports.billings') }}">Operators Billings</a></li>


                </ul>
            @endcan

        </li>

        <li>
            @can('control_clients')
                <a href="{{ route('clientupdated') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Clients">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4.01343 14.96V20.9466C4.01343 26.9333 6.41343 29.3333 12.4001 29.3333H19.5868C25.5734 29.3333 27.9734 26.9333 27.9734 20.9466V14.96"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                            d="M16 16C18.44 16 20.24 14.0133 20 11.5733L19.12 2.66663H12.8933L12 11.5733C11.76 14.0133 13.56 16 16 16Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M24.4133 16C27.1066 16 29.08 13.8133 28.8133 11.1333L28.44 7.46663C27.96 3.99996 26.6267 2.66663 23.1333 2.66663H19.0667L20 12.0133C20.2267 14.2133 22.2133 16 24.4133 16Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M7.51984 16C9.71984 16 11.7065 14.2133 11.9198 12.0133L12.2132 9.06663L12.8532 2.66663H8.78651C5.29318 2.66663 3.95984 3.99996 3.47984 7.46663L3.11984 11.1333C2.85318 13.8133 4.82651 16 7.51984 16Z"
                            fill="#F46624" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M16.0001 22.6666C13.7734 22.6666 12.6667 23.7733 12.6667 26V29.3333H19.3334V26C19.3334 23.7733 18.2267 22.6666 16.0001 22.6666Z"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
            @endcan
        </li>

        <li>
            @can('control_users')
                <a href="{{ route('users') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Users">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M28.1066 11.4394V20.5594C28.1066 22.0527 27.3066 23.4394 26.0132 24.1994L18.0932 28.7727C16.7999 29.5194 15.1999 29.5194 13.8932 28.7727L5.97322 24.1994C4.67988 23.4527 3.87988 22.066 3.87988 20.5594V11.4394C3.87988 9.94606 4.67988 8.55934 5.97322 7.79934L13.8932 3.22602C15.1866 2.47935 16.7865 2.47935 18.0932 3.22602L26.0132 7.79934C27.3066 8.55934 28.1066 9.93273 28.1066 11.4394Z"
                            fill="#F46624"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.3932 4.09205C14.3932 4.09207 14.3932 4.09204 14.3932 4.09205L6.47676 8.66332C5.48757 9.24605 4.87988 10.3056 4.87988 11.4394V20.5593C4.87988 21.7108 5.49106 22.7664 6.4732 23.3334L14.3894 27.9045C14.39 27.9049 14.3907 27.9052 14.3913 27.9056C15.3917 28.4764 16.6114 28.4735 17.5932 27.9067C17.5932 27.9067 17.5931 27.9068 17.5932 27.9067L25.5066 23.3372C25.5076 23.3367 25.5086 23.3361 25.5096 23.3355C26.4988 22.7528 27.1066 21.6932 27.1066 20.5593V11.4394C27.1066 10.2941 26.5004 9.24695 25.5097 8.66334C25.5087 8.66273 25.5076 8.66211 25.5066 8.6615L17.5971 4.09426C17.5965 4.0939 17.5958 4.09355 17.5952 4.09319C16.5948 3.52238 15.3749 3.52531 14.3932 4.09205ZM13.3932 2.35998C14.9975 1.43379 16.9768 1.43634 18.5894 2.35777L18.5933 2.36002L26.5199 6.93715C28.114 7.87391 29.1066 9.5726 29.1066 11.4394V20.5593C29.1066 22.4111 28.1155 24.1239 26.5198 25.0616L26.5133 25.0654L18.5933 29.6387C16.989 30.5649 15.0096 30.5624 13.3971 29.641L13.3932 29.6387L5.47324 25.0654C5.47321 25.0654 5.47326 25.0655 5.47324 25.0654C3.86876 24.1391 2.87988 22.4212 2.87988 20.5593V11.4394C2.87988 9.58769 3.87093 7.87483 5.46658 6.93718L5.47314 6.93332L13.3932 2.35998Z"
                            fill="#f9f9f9"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M15.9992 9.45312C14.8357 9.45312 13.8926 10.3963 13.8926 11.5597C13.8926 12.7232 14.8358 13.6664 15.9992 13.6664C17.1627 13.6664 18.1059 12.7232 18.1059 11.5597C18.1059 10.3963 17.1627 9.45312 15.9992 9.45312ZM11.8926 11.5597C11.8926 9.29166 13.7312 7.45312 15.9992 7.45312C18.2673 7.45312 20.1059 9.29166 20.1059 11.5597C20.1059 13.8278 18.2673 15.6664 15.9992 15.6664C13.7312 15.6664 11.8926 13.8278 11.8926 11.5597Z"
                            fill="white"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.66602 22.2138C9.66602 19.0802 12.7011 16.8672 15.9993 16.8672C19.2976 16.8672 22.3327 19.0802 22.3327 22.2138C22.3327 22.7661 21.885 23.2138 21.3327 23.2138C20.7804 23.2138 20.3327 22.7661 20.3327 22.2138C20.3327 20.5474 18.5944 18.8672 15.9993 18.8672C13.4043 18.8672 11.666 20.5474 11.666 22.2138C11.666 22.7661 11.2183 23.2138 10.666 23.2138C10.1137 23.2138 9.66602 22.7661 9.66602 22.2138Z"
                            fill="white"></path>
                    </svg>
                </a>
            @endcan
        </li>
        <li>
            @can('view_vehicles')
                <a href="{{ route('vehicles') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Vehicles">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 39 37" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M23.1864 10.1602H16.1597C13.6797 10.1602 13.1197 11.4002 12.813 12.9335L11.6797 18.3335H27.6797L26.5464 12.9335C26.213 11.4002 25.6664 10.1602 23.1864 10.1602Z"
                            fill="#F46624"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12.9548 10.2743C13.6936 9.51538 14.7543 9.16016 16.1597 9.16016H23.1864C24.5928 9.16016 25.6497 9.51593 26.3879 10.2743C27.0877 10.9932 27.3524 11.9338 27.5236 12.7211L27.5251 12.7281L27.5251 12.7281L28.6584 18.1281C28.7203 18.4229 28.6459 18.7299 28.456 18.9638C28.2661 19.1977 27.9809 19.3335 27.6797 19.3335H11.6797C11.3784 19.3335 11.0932 19.1977 10.9034 18.9638C10.7135 18.7299 10.6391 18.4229 10.701 18.1281L11.8334 12.7327C11.9928 11.937 12.253 10.9953 12.9548 10.2743ZM14.3879 11.6693C14.1145 11.9503 13.9409 12.393 13.7936 13.1296L13.7918 13.1389L13.7917 13.1389L12.9114 17.3335H26.448L25.5685 13.1426C25.4065 12.3986 25.2313 11.9534 24.9548 11.6694C24.7164 11.4244 24.2599 11.1602 23.1864 11.1602H16.1597C15.0851 11.1602 14.6258 11.4249 14.3879 11.6693Z"
                            fill="#f9f9f9"></path>
                        <path
                            d="M29.6548 27.145C29.7615 28.3184 28.8281 29.3317 27.6281 29.3317H25.7481C24.6681 29.3317 24.5215 28.865 24.3215 28.305L24.1215 27.7051C23.8415 26.8917 23.6548 26.3317 22.2148 26.3317H17.0948C15.6548 26.3317 15.4415 26.9584 15.1881 27.7051L14.9881 28.305C14.8015 28.8783 14.6548 29.3317 13.5615 29.3317H11.6815C10.4815 29.3317 9.53481 28.3184 9.65481 27.145L10.2148 21.0517C10.3615 19.545 10.6415 18.3184 13.2681 18.3184H26.0281C28.6548 18.3184 28.9348 19.545 29.0815 21.0517L29.6548 27.145Z"
                            fill="#F46624"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M11.2106 21.1459C11.2105 21.1468 11.2105 21.1477 11.2104 21.1486L10.6499 27.2468C10.5931 27.8026 11.0457 28.3317 11.6817 28.3317H13.5617C13.7726 28.3317 13.8814 28.3096 13.9293 28.295C13.9306 28.2924 13.9319 28.2897 13.9333 28.2867C13.9605 28.2278 13.9867 28.1516 14.0375 27.9954L14.0397 27.9888L14.2414 27.3838C14.2475 27.3658 14.2538 27.3471 14.2604 27.3275C14.3723 26.9942 14.5596 26.4364 15.0105 26.011C15.5333 25.5178 16.2404 25.3317 17.0951 25.3317H22.2151C23.0431 25.3317 23.7518 25.4892 24.2819 25.9788C24.7329 26.3954 24.9241 26.9584 25.0434 27.3097C25.0517 27.334 25.0596 27.3574 25.0673 27.3796L25.0704 27.3888L25.2671 27.9787C25.3213 28.1305 25.3484 28.2058 25.3788 28.2693C25.3837 28.2795 25.3879 28.2876 25.3914 28.294C25.435 28.3085 25.5394 28.3317 25.7484 28.3317H27.6284C28.2526 28.3317 28.7109 27.8114 28.6593 27.2366C28.6592 27.2363 28.6592 27.2359 28.6592 27.2356L28.0864 21.1486C28.0864 21.148 28.0863 21.1475 28.0863 21.147C28.0103 20.3672 27.9112 19.9911 27.72 19.764C27.5781 19.5954 27.2102 19.3184 26.0284 19.3184H13.2684C12.0866 19.3184 11.7187 19.5954 11.5768 19.764C11.3857 19.991 11.2866 20.3669 11.2106 21.1459ZM25.3653 28.2826C25.3656 28.2824 25.3692 28.2839 25.3749 28.2878C25.3679 28.2848 25.365 28.2828 25.3653 28.2826ZM25.4028 28.313C25.4028 28.313 25.403 28.3133 25.4028 28.313V28.313ZM13.9608 28.2824C13.961 28.2826 13.9586 28.2842 13.9529 28.2866C13.9577 28.2835 13.9606 28.2823 13.9608 28.2824ZM10.0467 18.476C10.7381 17.6546 11.8235 17.3184 13.2684 17.3184H26.0284C27.4733 17.3184 28.5587 17.6546 29.2501 18.476C29.8921 19.2388 30.0064 20.2289 30.077 20.9548L30.0773 20.958L30.651 27.0545C30.812 28.826 29.4038 30.3317 27.6284 30.3317H25.7484C25.0751 30.3317 24.4723 30.1862 24.0166 29.7536C23.6442 29.4001 23.4827 28.9359 23.3996 28.6973C23.3925 28.6769 23.386 28.6582 23.38 28.6413L23.3731 28.6213L23.1746 28.026C23.1005 27.8109 23.0552 27.6821 23.0051 27.5769C22.9605 27.483 22.9331 27.4556 22.9249 27.448C22.9216 27.4449 22.8965 27.4196 22.802 27.3922C22.6969 27.3616 22.5159 27.3317 22.2151 27.3317H17.0951C16.5208 27.3317 16.3949 27.4542 16.3835 27.4653C16.3833 27.4655 16.3836 27.4651 16.3835 27.4653C16.3309 27.5149 16.2791 27.603 16.1362 28.0239C16.1359 28.0248 16.1357 28.0256 16.1354 28.0264L15.9382 28.6178C15.9337 28.6319 15.9288 28.647 15.9237 28.6632C15.8438 28.9132 15.6897 29.3954 15.3003 29.7617C14.8396 30.1951 14.2297 30.3317 13.5617 30.3317H11.6817C9.91958 30.3317 8.48013 28.8372 8.6597 27.0489L9.21976 20.9548C9.29042 20.2289 9.40467 19.2388 10.0467 18.476Z"
                            fill="#f9f9f9"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.66797 15.334C9.66797 14.7817 10.1157 14.334 10.668 14.334H11.668C12.2203 14.334 12.668 14.7817 12.668 15.334C12.668 15.8863 12.2203 16.334 11.668 16.334H10.668C10.1157 16.334 9.66797 15.8863 9.66797 15.334Z"
                            fill="#f9f9f9"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M26.668 15.334C26.668 14.7817 27.1157 14.334 27.668 14.334H28.668C29.2203 14.334 29.668 14.7817 29.668 15.334C29.668 15.8863 29.2203 16.334 28.668 16.334H27.668C27.1157 16.334 26.668 15.8863 26.668 15.334Z"
                            fill="#f9f9f9"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12.668 22.334C12.668 21.7817 13.1157 21.334 13.668 21.334H16.668C17.2203 21.334 17.668 21.7817 17.668 22.334C17.668 22.8863 17.2203 23.334 16.668 23.334H13.668C13.1157 23.334 12.668 22.8863 12.668 22.334Z"
                            fill="white"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M21.668 22.334C21.668 21.7817 22.1157 21.334 22.668 21.334H25.668C26.2203 21.334 26.668 21.7817 26.668 22.334C26.668 22.8863 26.2203 23.334 25.668 23.334H22.668C22.1157 23.334 21.668 22.8863 21.668 22.334Z"
                            fill="white"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M32.3241 8.03096C31.4923 6.77756 30.1588 5.98136 28.8121 6.00033C28.2599 6.00811 27.8185 6.46209 27.8263 7.01432C27.8341 7.56655 28.288 8.00791 28.8403 8.00013C29.3855 7.99245 30.1307 8.34132 30.6591 9.1391L30.6591 9.13912L30.6641 9.14656C31.2056 9.94834 31.241 10.7629 31.0278 11.2579C30.8093 11.7652 31.0433 12.3535 31.5506 12.572C32.0578 12.7905 32.6461 12.5564 32.8646 12.0492C33.3975 10.812 33.1672 9.28183 32.3241 8.03096Z"
                            fill="#f9f9f9"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M28.1949 2.54522C28.2617 1.99698 28.7603 1.60668 29.3085 1.67345C31.7065 1.96552 34.0872 3.4535 35.6588 5.80358C37.2287 8.1511 37.7044 10.9157 37.0577 13.2407C36.9097 13.7728 36.3584 14.0842 35.8263 13.9362C35.2942 13.7882 34.9829 13.2369 35.1309 12.7048C35.6041 11.0032 35.2798 8.83452 33.9963 6.91537C32.7146 4.99878 30.8287 3.87338 29.0667 3.65878C28.5185 3.59201 28.1282 3.09345 28.1949 2.54522Z"
                            fill="#f9f9f9"></path>
                    </svg>
                </a>
            @endcan
        </li>
        <li>
            @can('previous_orders_basic_view')
                <a href="{{ route('OrderDashboard') }}" class="nav-link" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="Orders">
                    <svg width="23.6px" height="23.6px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.422 20.618C10.178 21.54 11.056 22 12 22V12L2.638 7.073L2.598 7.14C2 8.154 2 9.417 2 11.942V12.059C2 14.583 2 15.846 2.597 16.86C3.195 17.875 4.271 18.44 6.422 19.569L8.422 20.618Z"
                            fill="#F46624"></path>
                        <path
                            d="M17.5772 4.432L15.5772 3.382C13.8222 2.461 12.9442 2 12.0002 2C11.0552 2 10.1782 2.46 8.42218 3.382L6.42218 4.432C4.31818 5.536 3.24218 6.1 2.63818 7.072L12 12L21.3622 7.073C20.7562 6.1 19.6822 5.536 17.5772 4.432Z"
                            fill="#F46624"></path>
                        <path
                            d="M21.403 7.14L21.3622 7.073L12 12V22C12.944 22 13.822 21.54 15.578 20.618L17.578 19.568C19.729 18.439 20.805 17.875 21.403 16.86C22 15.846 22 14.583 22 12.06V11.943C22 9.418 22 8.154 21.403 7.14Z"
                            fill="#F46624"></path>
                        <path
                            d="M6.32295 4.48395L6.42295 4.43195L7.91595 3.64795L17.016 8.65295L21.041 6.64195C21.1776 6.79662 21.2983 6.96262 21.403 7.13995C21.553 7.39395 21.665 7.66395 21.749 7.96495L17.75 9.96395V12.9999C17.75 13.1989 17.6709 13.3896 17.5303 13.5303C17.3896 13.6709 17.1989 13.7499 17 13.7499C16.801 13.7499 16.6103 13.6709 16.4696 13.5303C16.329 13.3896 16.25 13.1989 16.25 12.9999V10.7139L12.75 12.4639V21.9039C12.505 21.9675 12.253 21.9997 12 21.9999C11.752 21.9999 11.507 21.9679 11.25 21.9039V12.4639L2.25195 7.96395C2.33595 7.66395 2.44795 7.39395 2.59795 7.13995C2.70195 6.96262 2.82262 6.79662 2.95995 6.64195L12 11.1619L15.387 9.46895L6.32295 4.48395Z"
                            fill="white"></path>
                    </svg>
                </a>
            @endcan
        </li>
        <li class="dropdown">
            @can('view_export_reports')
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                    <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Reports"></span>

                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                            fill="#F46624"></path>
                        <path
                            d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M29.3334 13.3327H24C20 13.3327 18.6667 11.9993 18.6667 7.99935V2.66602L29.3334 13.3327Z"
                            fill="white" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M9.33331 17.334H17.3333" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M9.33331 22.666H14.6666" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </a>
                <ul class="dropdown-menu text-small shadow reportsSideMenu">
                    @can('orders_report')
                        <li><a class="dropdown-item" href="{{ route('reports') }}">Orders Reports</a></li>
                    @endcan
                    @can('operators_reports')
                        <li><a class="dropdown-item" href="{{ route('operators.operator-reports') }}">Operators Report</a> </li>
                        @endcan
                        @can('client_reports')
                        <li><a class="dropdown-item" href="{{ route('brand-reports') }}">Clients Reports</a> </li>
                        @endcan
                        @can('utr_reports')
                        <li><a class="dropdown-item" href="{{ route('reports.reportCitys') }}">UTR Reports</a> </li>
                        @endcan
                        @can('accounting_client_reports')
                        <li><a class="dropdown-item" href="{{ route('reports.clientReports') }}">Accounting Client Report</a> </li>
                        @endcan
                        @can('operators_acceptance_time_reports')
                        <li><a class="dropdown-item" href="{{ route('report.operatorassignReport') }}">Operators & Acceptance
                                Time</a> </li>
                        @endcan
                        {{-- @can('cities_sales_reports') --}}
                        {{-- <li><a class="dropdown-item" href="{{ route('report.citiesSalesreport') }}">Cities Sales Report</a> --}}
                        {{-- @endcan --}}
                        @can('dispatcher_assign_reports')
                        <li><a class="dropdown-item"
                                href="{{ route('report.dispatcherAssignReport') }}">{{ trans('dashboard.dispatcherAssignReport') }}</a> </li>
                        @endcan
                    @can('dispatcher_assign_reports')
                        <li><a class="dropdown-item"
                                href="{{ route('export.GetOrders') }}">Export Excel Report</a> </li>
                        @endcan
                </ul>
            @endcan
        </li>
        <li class="dropdown">
            @if (auth()->user()->id == 5606 || auth()->user()->id == 5798 || auth()->user()->id == 5799)
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                    <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Financial Reports"></span>

                    <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                            fill="#F46624"></path>
                        <path
                            d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                            stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                            d="M29.3334 13.3327H24C20 13.3327 18.6667 11.9993 18.6667 7.99935V2.66602L29.3334 13.3327Z"
                            fill="white" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M9.33331 17.334H17.3333" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path d="M9.33331 22.666H14.6666" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </a>
                <ul class="dropdown-menu text-small shadow reportsSideMenu">

                    @if (auth()->user()->id == 5606 || auth()->user()->id == 5798 || auth()->user()->id == 5799)
                        <li><a class="dropdown-item"
                                href="{{ route('report.clientsSalesreport') }}">{{ trans('dashboard.clientsSalesreport') }}</a>
                    @endif
                    @if (auth()->user()->id == 5606 || auth()->user()->id == 5798 || auth()->user()->id == 5799)
                        <li><a class="dropdown-item" href="{{ route('report.citiesSalesreport') }}">Cities Sales
                                Report</a>
                    @endif
        </li>
    </ul>
    @endif
    </li>


    <li>
        @can('view_integration')
            <a href="{{ route('integrations') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Intergrations">
                <svg version="1.1" height="23.6px" width="23.6px" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve"
                    fill="#ffffff">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <rect x="167.379" style="fill:#ffffff;" width="35.718" height="97.525"></rect>
                            <rect x="308.902" style="fill:#ffffff;" width="35.718" height="97.525"></rect>
                        </g>
                        <rect x="238.135" y="367.716" style="fill:#ffffff;" width="35.718" height="144.284">
                        </rect>
                        <rect x="255.076" y="367.716" style="fill:#ffffff;" width="18.782" height="144.284">
                        </rect>
                        <path style="fill:#fa6624;"
                            d="M255.999,385.575c-120.191,0-217.972-97.781-217.972-217.971V79.663h435.945v87.94 C473.971,287.793,376.19,385.575,255.999,385.575z">
                        </path>
                        <path style="fill:#fa6624;"
                            d="M255.999,385.575c0-116.082,0-305.911,0-305.911h217.972v87.94 C473.971,287.793,376.19,385.575,255.999,385.575z">
                        </path>
                    </g>
                </svg>
                <span class="menuMobileItem">Integrations</span>
            </a>
        @endcan
    </li>
    <li>
        @can('view_location')
            <a href="{{ route('locations') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Locations">
                <svg fill="#fff" height="23.6px" width="23.6px" version="1.1" id="Layer_1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="-51.2 -51.2 614.38 614.38" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <g>
                                <g>
                                    <path fill="#fa6624"
                                        d="M341.332,63.98c-35.285,0-64,28.715-64,64s28.715,64,64,64s64-28.715,64-64S376.617,63.98,341.332,63.98z">
                                    </path>
                                    <path
                                        d="M422.051,28.632C391.971,4.184,352.696-5.245,314.061,2.819c-50.795,10.56-90.581,52.267-98.987,103.744 c-0.405,2.56-0.597,5.141-0.853,7.723c-22.272-8.043-46.699-9.835-70.827-4.8C92.6,120.046,52.813,161.752,44.408,213.23 c-4.587,28.139-0.128,56.107,12.928,80.917l10.88,20.416c13.909,26.005,27.051,50.581,37.888,76.608l44.864,107.691 c3.328,7.936,11.093,13.12,19.691,13.12c8.619,0,16.384-5.184,19.691-13.12l48.597-116.693 c10.56-25.259,23.637-49.045,37.483-74.24l5.76-10.453l39.445,94.72c3.328,7.936,11.093,13.12,19.691,13.12 c8.619,0,16.384-5.184,19.691-13.12l48.619-116.672c10.539-25.301,23.616-49.067,37.483-74.261l6.592-12.011 c10.219-18.688,15.616-39.872,15.616-61.269C469.325,89.283,452.109,53.08,422.051,28.632z M170.659,298.648 c-35.285,0-64-28.715-64-64s28.715-64,64-64c35.307,0,64,28.715,64,64S205.965,298.648,170.659,298.648z M416.291,168.771 l-6.549,11.925c-13.803,25.067-28.075,50.987-39.488,78.4l-28.928,69.419l-25.152-60.416 c-5.824-13.931-12.224-27.179-17.6-37.867c-0.064-1.941-0.32-3.84-0.469-5.76c-0.149-1.877-0.256-3.755-0.469-5.611 c-0.363-2.859-0.896-5.675-1.429-8.491c-0.32-1.6-0.533-3.243-0.896-4.843c-0.939-3.968-2.048-7.872-3.349-11.712 c-0.448-1.365-1.003-2.688-1.493-4.011c-1.173-3.136-2.389-6.272-3.819-9.301c-0.811-1.771-1.792-3.456-2.709-5.184 c-1.152-2.197-2.283-4.395-3.563-6.528c-1.109-1.856-2.347-3.627-3.563-5.419c-1.259-1.899-2.539-3.776-3.904-5.589 c-1.365-1.813-2.837-3.563-4.288-5.312c-1.408-1.685-2.837-3.328-4.352-4.928c-1.6-1.728-3.264-3.413-4.971-5.056 c-0.725-0.683-1.365-1.451-2.112-2.133c-1.429-9.109-1.408-18.133,0.021-26.901c5.483-33.664,32.427-61.973,65.557-68.864 c26.304-5.461,52.053,0.619,72.363,17.131c20.032,16.32,31.531,40.448,31.531,66.261 C426.659,142.254,423.075,156.355,416.291,168.771z">
                                    </path>
                                    <path fill="#fa6624"
                                        d="M170.665,213.313c-11.776,0-21.333,9.579-21.333,21.333c0,11.755,9.557,21.333,21.333,21.333s21.333-9.579,21.333-21.333 C191.998,222.892,182.441,213.313,170.665,213.313z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
                <span class="menuMobileItem">Locations</span>
            </a>
        @endcan
    </li>
    <li>
        @can('view_location')
            <a href="{{ route('online_orders') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Online Orders">

                <svg fill="#fff" height="23.6px" width="23.6px" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 4h16l-1.5 9H5.5L4 4z" />
                    <circle cx="9" cy="20" r="2" />
                    <circle cx="15" cy="20" r="2" />
                    <path d="M5 6h14" />
                    <path d="M7 10h10" />
                </svg>

                <span class="menuMobileItem">Online Orders</span>
            </a>
        @endcan
    </li>

    <!-- Cancellation Reasons -->
    <li>
        @can('view_location')
            <a href="{{ route('cancelReasons') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Cancellation Reasons">

                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    height="23.6px" width="23.6px" x="0" y="0" viewBox="0 0 512 512"
                    style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                    <g>
                        <path
                            d="M256.002 503.671c136.785 0 247.671-110.886 247.671-247.672S392.786 8.329 256.002 8.329 8.33 119.215 8.33 256.001s110.886 247.67 247.672 247.67z"
                            style="" fill="#f46624" data-original="#ff6465" class="" opacity="1">
                        </path>
                        <path
                            d="M74.962 256.001c0-125.485 93.327-229.158 214.355-245.434a249.77 249.77 0 0 0-33.316-2.238C119.216 8.329 8.33 119.215 8.33 256.001s110.886 247.672 247.671 247.672c11.3 0 22.417-.772 33.316-2.238C168.289 485.159 74.962 381.486 74.962 256.001z"
                            style="opacity:0.1;enable-background:new ;" fill="#ffffff" opacity="1"
                            data-original="#000000" class=""></path>
                        <path
                            d="m311.525 256.001 65.206-65.206c4.74-4.74 4.74-12.425 0-17.163l-38.36-38.36c-4.74-4.74-12.425-4.74-17.164 0l-65.206 65.206-65.206-65.206c-4.74-4.74-12.425-4.74-17.163 0l-38.36 38.36c-4.74 4.74-4.74 12.425 0 17.163l65.206 65.206-65.206 65.206c-4.74 4.74-4.74 12.425 0 17.164l38.36 38.36c4.74 4.74 12.425 4.74 17.163 0l65.206-65.206 65.206 65.206c4.74 4.74 12.425 4.74 17.164 0l38.36-38.36c4.74-4.74 4.74-12.425 0-17.164l-65.206-65.206z"
                            style="" fill="#ffffff" data-original="#ffffff" class=""></path>
                        <path
                            d="M388.614 182.213a20.326 20.326 0 0 0-5.995-14.471l-38.36-38.36c-3.865-3.865-9.004-5.994-14.471-5.994s-10.605 2.129-14.471 5.994l-59.316 59.316-59.316-59.316a20.33 20.33 0 0 0-14.471-5.994 20.33 20.33 0 0 0-14.471 5.994l-38.36 38.36c-7.979 7.979-7.979 20.962 0 28.943l59.316 59.316-59.316 59.316c-7.979 7.979-7.979 20.962 0 28.943l38.36 38.36a20.33 20.33 0 0 0 14.471 5.993 20.334 20.334 0 0 0 14.471-5.993l59.316-59.316 59.316 59.316c3.865 3.865 9.004 5.993 14.471 5.993s10.605-2.129 14.471-5.993l38.36-38.36a20.328 20.328 0 0 0 5.995-14.471 20.326 20.326 0 0 0-5.995-14.471l-59.315-59.316 59.315-59.315a20.341 20.341 0 0 0 5.995-14.474zm-17.774 2.692-65.204 65.206a8.327 8.327 0 0 0 0 11.778l65.204 65.207a3.788 3.788 0 0 1 1.115 2.692c0 .589-.144 1.721-1.115 2.692l-38.36 38.36a3.788 3.788 0 0 1-2.692 1.115 3.787 3.787 0 0 1-2.692-1.115l-65.206-65.206a8.304 8.304 0 0 0-5.889-2.44 8.3 8.3 0 0 0-5.889 2.44l-65.206 65.206a3.788 3.788 0 0 1-2.692 1.115c-.59 0-1.722-.144-2.693-1.115l-38.36-38.36a3.813 3.813 0 0 1 0-5.385l65.206-65.206a8.327 8.327 0 0 0 0-11.778l-65.206-65.206a3.813 3.813 0 0 1 0-5.385l38.359-38.36c.971-.971 2.104-1.115 2.693-1.115s1.722.144 2.692 1.115l65.206 65.206a8.327 8.327 0 0 0 11.778 0l65.206-65.206a3.788 3.788 0 0 1 2.692-1.115c.589 0 1.722.144 2.692 1.115l38.36 38.36c.971.971 1.115 2.103 1.115 2.692s-.143 1.722-1.114 2.693z"
                            fill="#ffffff" opacity="1" data-original="#000000" class=""></path>
                        <path
                            d="M423.9 73.756a8.327 8.327 0 0 0 .086 11.778c46.016 45.349 71.358 105.89 71.358 170.466 0 63.931-24.896 124.035-70.102 169.241s-105.31 70.102-169.241 70.102c-35.385 0-69.471-7.555-101.311-22.455a8.329 8.329 0 0 0-7.061 15.087C181.695 503.917 218.156 512 255.999 512c68.381 0 132.668-26.629 181.019-74.982 48.352-48.352 74.98-112.64 74.98-181.019 0-69.072-27.106-133.825-76.323-182.331a8.324 8.324 0 0 0-11.775.088zM116.34 470.563a8.324 8.324 0 0 0 11.526-2.426 8.329 8.329 0 0 0-2.426-11.526C57.325 412.187 16.66 337.192 16.66 256c0-63.931 24.896-124.035 70.102-169.24 45.206-45.206 105.31-70.102 169.241-70.102 52.234 0 101.864 16.528 143.525 47.796a8.33 8.33 0 0 0 9.998-13.323C364.958 17.681 311.87 0 256.002 0c-68.38 0-132.668 26.629-181.019 74.98C26.63 123.333.001 187.62.001 255.999c0 86.842 43.492 167.052 116.339 214.564z"
                            fill="#ffffff" opacity="1" data-original="#000000" class=""></path>
                    </g>
                </svg>

                <span class="menuMobileItem">Cancellation Reasons</span>
            </a>
        @endcan
    </li>

    <li>
        @can('view_foodics_clients')
            <a href="{{ route('foodicsClients') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Foodics">
                <svg class="rounded-5" width="23.6px" height="23.6px" viewBox="0 0 400.000000 400.000000"
                    preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0.000000,400.000000) scale(0.100000,-0.100000)" fill="#fff"
                        stroke="none">
                        <path d="M0 2000 l0 -2000 2000 0 2000 0 0 2000 0 2000 -2000 0 -2000 0 0
                                -2000z m3048 883 l2 -263 -725 0 c-787 0 -751 2 -805 -55 -24 -26 -25 -31 -28
                                -203 -3 -172 -2 -178 20 -212 47 -70 47 -70 486 -70 l392 0 0 -260 0 -260
                                -418 0 c-384 0 -425 2 -496 20 -188 48 -353 180 -438 352 -55 112 -68 173 -75
                                353 -12 327 39 496 198 656 108 109 247 178 396 198 38 5 388 8 778 7 l710 -1
                                3 -262z m-1703 -1524 c58 -15 119 -68 140 -124 14 -34 16 -67 13 -149 -3 -97
                                -6 -108 -33 -147 -17 -23 -50 -53 -74 -66 -39 -20 -58 -23 -160 -23 -140 1
                                -187 19 -236 91 -27 40 -30 52 -33 147 -3 80 0 113 13 147 20 53 81 109 133
                                123 51 14 184 14 237 1z" />
                    </g>
                </svg>
                <span class="menuMobileItem">Foodics</span>
            </a>
        @endcan
    </li>

    </ul>
    {{-- @endif --}}
    {{-- @if (auth()->user()->user_role?->value == 2)
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="nav-item">
                @can('show_dashboard')
                    <a href="{{ route('dashboard') }}" class="nav-link" aria-current="page" data-bs-toggle="tooltip"
                        data-bs-placement="right" title="Dashboard">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.3276 2.002C16.676 1.97034 18.0786 2.31338 19.1908 3.09294L27.2376 8.73808L27.2385 8.73876C28.0454 9.30215 28.7246 10.1701 29.2005 11.083C29.6767 11.9963 30 13.0518 30 14.0392V23.5322C30 27.0831 27.1028 30 23.5405 30H8.45953C4.89569 30 2 27.0815 2 23.5192V13.8566C2 12.9302 2.29225 11.9274 2.72172 11.0509C3.15125 10.1744 3.76533 9.32767 4.49918 8.75594L11.5187 3.27919C12.5928 2.44273 13.9791 2.03366 15.3276 2.002Z"
                                fill="#f9f9f9"></path>
                            <path
                                d="M12.424 4.98846L5.956 10.0354C4.876 10.8765 4 12.667 4 14.0248V22.9291C4 25.7169 6.268 28 9.052 28H22.948C25.732 28 28 25.7169 28 22.9411V14.1931C28 12.7391 27.028 10.8765 25.84 10.0474L18.424 4.84426C16.744 3.66664 14.044 3.72673 12.424 4.98846Z"
                                fill="#F46624"></path>
                            <path
                                d="M19.3333 14.3325C18.781 14.3325 18.3333 14.7802 18.3333 15.3325C18.3333 15.8848 18.781 16.3325 19.3333 16.3325H19.5858L16.5554 19.3629L15.0987 17.1778C14.9325 16.9285 14.6633 16.7669 14.3652 16.7374C14.0671 16.7079 13.7714 16.8136 13.5596 17.0254L9.29289 21.2921C8.90237 21.6826 8.90237 22.3158 9.29289 22.7063C9.68342 23.0968 10.3166 23.0968 10.7071 22.7063L14.1112 19.3022L15.568 21.4872C15.7341 21.7365 16.0033 21.8981 16.3015 21.9277C16.5996 21.9572 16.8953 21.8515 17.1071 21.6396L20.9999 17.7468V17.9992C20.9999 18.5515 21.4476 18.9992 21.9999 18.9992C22.5522 18.9992 22.9999 18.5515 22.9999 17.9992V15.3453C23.0007 15.2835 22.9958 15.2216 22.9852 15.1605C22.9797 15.1291 22.9728 15.0982 22.9645 15.0679C22.9202 14.9059 22.8344 14.7527 22.7071 14.6254C22.6021 14.5204 22.4794 14.4436 22.3487 14.395C22.2401 14.3546 22.1226 14.3325 21.9999 14.3325H19.3333Z"
                                fill="white"></path>
                        </svg>
                    </a>
                @endcan
            </li>
            <li>
                @can('basic_dispatcher_view')
                    <a href="{{ route('index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Dispatcher">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.666 13.3332H25.3327C27.9994 13.3332 29.3327 11.9998 29.3327 9.33317V6.6665C29.3327 3.99984 27.9994 2.6665 25.3327 2.6665H22.666C19.9993 2.6665 18.666 3.99984 18.666 6.6665V9.33317C18.666 11.9998 19.9993 13.3332 22.666 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M6.66602 29.3332H9.33268C11.9993 29.3332 13.3327 27.9998 13.3327 25.3332V22.6665C13.3327 19.9998 11.9993 18.6665 9.33268 18.6665H6.66602C3.99935 18.6665 2.66602 19.9998 2.66602 22.6665V25.3332C2.66602 27.9998 3.99935 29.3332 6.66602 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M7.99935 13.3332C10.9449 13.3332 13.3327 10.9454 13.3327 7.99984C13.3327 5.05432 10.9449 2.6665 7.99935 2.6665C5.05383 2.6665 2.66602 5.05432 2.66602 7.99984C2.66602 10.9454 5.05383 13.3332 7.99935 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M23.9993 29.3332C26.9449 29.3332 29.3327 26.9454 29.3327 23.9998C29.3327 21.0543 26.9449 18.6665 23.9993 18.6665C21.0538 18.6665 18.666 21.0543 18.666 23.9998C18.666 26.9454 21.0538 29.3332 23.9993 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                @endcan
            </li>






            <li>
                @can('orders_basic_view')
                    <a href="{{ route('orders') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Orders">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.422 20.618C10.178 21.54 11.056 22 12 22V12L2.638 7.073L2.598 7.14C2 8.154 2 9.417 2 11.942V12.059C2 14.583 2 15.846 2.597 16.86C3.195 17.875 4.271 18.44 6.422 19.569L8.422 20.618Z"
                                fill="#F46624"></path>
                            <path
                                d="M17.5772 4.432L15.5772 3.382C13.8222 2.461 12.9442 2 12.0002 2C11.0552 2 10.1782 2.46 8.42218 3.382L6.42218 4.432C4.31818 5.536 3.24218 6.1 2.63818 7.072L12 12L21.3622 7.073C20.7562 6.1 19.6822 5.536 17.5772 4.432Z"
                                fill="#F46624"></path>
                            <path
                                d="M21.403 7.14L21.3622 7.073L12 12V22C12.944 22 13.822 21.54 15.578 20.618L17.578 19.568C19.729 18.439 20.805 17.875 21.403 16.86C22 15.846 22 14.583 22 12.06V11.943C22 9.418 22 8.154 21.403 7.14Z"
                                fill="#F46624"></path>
                            <path
                                d="M6.32295 4.48395L6.42295 4.43195L7.91595 3.64795L17.016 8.65295L21.041 6.64195C21.1776 6.79662 21.2983 6.96262 21.403 7.13995C21.553 7.39395 21.665 7.66395 21.749 7.96495L17.75 9.96395V12.9999C17.75 13.1989 17.6709 13.3896 17.5303 13.5303C17.3896 13.6709 17.1989 13.7499 17 13.7499C16.801 13.7499 16.6103 13.6709 16.4696 13.5303C16.329 13.3896 16.25 13.1989 16.25 12.9999V10.7139L12.75 12.4639V21.9039C12.505 21.9675 12.253 21.9997 12 21.9999C11.752 21.9999 11.507 21.9679 11.25 21.9039V12.4639L2.25195 7.96395C2.33595 7.66395 2.44795 7.39395 2.59795 7.13995C2.70195 6.96262 2.82262 6.79662 2.95995 6.64195L12 11.1619L15.387 9.46895L6.32295 4.48395Z"
                                fill="white"></path>
                        </svg>
                    </a>
                @endcan
            </li>


        </ul>
    @endif

    @if (auth()->user()->user_role?->value == 4)
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="nav-item">
                @can('show_dashboard')
                    <a href="{{ route('dashboard') }}" class="nav-link" aria-current="page" data-bs-toggle="tooltip"
                        data-bs-placement="right" title="Dashboard">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.3276 2.002C16.676 1.97034 18.0786 2.31338 19.1908 3.09294L27.2376 8.73808L27.2385 8.73876C28.0454 9.30215 28.7246 10.1701 29.2005 11.083C29.6767 11.9963 30 13.0518 30 14.0392V23.5322C30 27.0831 27.1028 30 23.5405 30H8.45953C4.89569 30 2 27.0815 2 23.5192V13.8566C2 12.9302 2.29225 11.9274 2.72172 11.0509C3.15125 10.1744 3.76533 9.32767 4.49918 8.75594L11.5187 3.27919C12.5928 2.44273 13.9791 2.03366 15.3276 2.002Z"
                                fill="#f9f9f9"></path>
                            <path
                                d="M12.424 4.98846L5.956 10.0354C4.876 10.8765 4 12.667 4 14.0248V22.9291C4 25.7169 6.268 28 9.052 28H22.948C25.732 28 28 25.7169 28 22.9411V14.1931C28 12.7391 27.028 10.8765 25.84 10.0474L18.424 4.84426C16.744 3.66664 14.044 3.72673 12.424 4.98846Z"
                                fill="#F46624"></path>
                            <path
                                d="M19.3333 14.3325C18.781 14.3325 18.3333 14.7802 18.3333 15.3325C18.3333 15.8848 18.781 16.3325 19.3333 16.3325H19.5858L16.5554 19.3629L15.0987 17.1778C14.9325 16.9285 14.6633 16.7669 14.3652 16.7374C14.0671 16.7079 13.7714 16.8136 13.5596 17.0254L9.29289 21.2921C8.90237 21.6826 8.90237 22.3158 9.29289 22.7063C9.68342 23.0968 10.3166 23.0968 10.7071 22.7063L14.1112 19.3022L15.568 21.4872C15.7341 21.7365 16.0033 21.8981 16.3015 21.9277C16.5996 21.9572 16.8953 21.8515 17.1071 21.6396L20.9999 17.7468V17.9992C20.9999 18.5515 21.4476 18.9992 21.9999 18.9992C22.5522 18.9992 22.9999 18.5515 22.9999 17.9992V15.3453C23.0007 15.2835 22.9958 15.2216 22.9852 15.1605C22.9797 15.1291 22.9728 15.0982 22.9645 15.0679C22.9202 14.9059 22.8344 14.7527 22.7071 14.6254C22.6021 14.5204 22.4794 14.4436 22.3487 14.395C22.2401 14.3546 22.1226 14.3325 21.9999 14.3325H19.3333Z"
                                fill="white"></path>
                        </svg>
                    </a>
                @endcan
            </li>
            <li>
                @can('basic_dispatcher_view')
                    <a href="{{ route('index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Dispatcher">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.666 13.3332H25.3327C27.9994 13.3332 29.3327 11.9998 29.3327 9.33317V6.6665C29.3327 3.99984 27.9994 2.6665 25.3327 2.6665H22.666C19.9993 2.6665 18.666 3.99984 18.666 6.6665V9.33317C18.666 11.9998 19.9993 13.3332 22.666 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M6.66602 29.3332H9.33268C11.9993 29.3332 13.3327 27.9998 13.3327 25.3332V22.6665C13.3327 19.9998 11.9993 18.6665 9.33268 18.6665H6.66602C3.99935 18.6665 2.66602 19.9998 2.66602 22.6665V25.3332C2.66602 27.9998 3.99935 29.3332 6.66602 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M7.99935 13.3332C10.9449 13.3332 13.3327 10.9454 13.3327 7.99984C13.3327 5.05432 10.9449 2.6665 7.99935 2.6665C5.05383 2.6665 2.66602 5.05432 2.66602 7.99984C2.66602 10.9454 5.05383 13.3332 7.99935 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M23.9993 29.3332C26.9449 29.3332 29.3327 26.9454 29.3327 23.9998C29.3327 21.0543 26.9449 18.6665 23.9993 18.6665C21.0538 18.6665 18.666 21.0543 18.666 23.9998C18.666 26.9454 21.0538 29.3332 23.9993 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                @endcan
            </li>






            <li>
                @can('orders_basic_view')
                    <a href="{{ route('orders') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Orders">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.422 20.618C10.178 21.54 11.056 22 12 22V12L2.638 7.073L2.598 7.14C2 8.154 2 9.417 2 11.942V12.059C2 14.583 2 15.846 2.597 16.86C3.195 17.875 4.271 18.44 6.422 19.569L8.422 20.618Z"
                                fill="#F46624"></path>
                            <path
                                d="M17.5772 4.432L15.5772 3.382C13.8222 2.461 12.9442 2 12.0002 2C11.0552 2 10.1782 2.46 8.42218 3.382L6.42218 4.432C4.31818 5.536 3.24218 6.1 2.63818 7.072L12 12L21.3622 7.073C20.7562 6.1 19.6822 5.536 17.5772 4.432Z"
                                fill="#F46624"></path>
                            <path
                                d="M21.403 7.14L21.3622 7.073L12 12V22C12.944 22 13.822 21.54 15.578 20.618L17.578 19.568C19.729 18.439 20.805 17.875 21.403 16.86C22 15.846 22 14.583 22 12.06V11.943C22 9.418 22 8.154 21.403 7.14Z"
                                fill="#F46624"></path>
                            <path
                                d="M6.32295 4.48395L6.42295 4.43195L7.91595 3.64795L17.016 8.65295L21.041 6.64195C21.1776 6.79662 21.2983 6.96262 21.403 7.13995C21.553 7.39395 21.665 7.66395 21.749 7.96495L17.75 9.96395V12.9999C17.75 13.1989 17.6709 13.3896 17.5303 13.5303C17.3896 13.6709 17.1989 13.7499 17 13.7499C16.801 13.7499 16.6103 13.6709 16.4696 13.5303C16.329 13.3896 16.25 13.1989 16.25 12.9999V10.7139L12.75 12.4639V21.9039C12.505 21.9675 12.253 21.9997 12 21.9999C11.752 21.9999 11.507 21.9679 11.25 21.9039V12.4639L2.25195 7.96395C2.33595 7.66395 2.44795 7.39395 2.59795 7.13995C2.70195 6.96262 2.82262 6.79662 2.95995 6.64195L12 11.1619L15.387 9.46895L6.32295 4.48395Z"
                                fill="white"></path>
                        </svg>
                    </a>
                @endcan
            </li>


            <li class="dropdown">
                @can('view_export_reports')
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                        <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Reports"></span>

                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                                fill="#F46624"></path>
                            <path
                                d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                                stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M29.3334 13.3327H24C20 13.3327 18.6667 11.9993 18.6667 7.99935V2.66602L29.3334 13.3327Z"
                                fill="white" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M9.33331 17.334H17.3333" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M9.33331 22.666H14.6666" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </a>
                    <ul class="dropdown-menu text-small shadow reportsSideMenu">
                        <li><a class="dropdown-item" href="{{ route('reports') }}">Orders Reports</a></li>
                        <li><a class="dropdown-item" href="{{ route('operators.operator-reports') }}">Operators
                                Report</a>
                        <li><a class="dropdown-item" href="{{ route('brand-reports') }}">Clients Reports</a>
                        </li>
                    </ul>
                @endcan
            </li>


        </ul>
    @endif

    @if (auth()->user()->user_role?->value == 5)
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">

            <li>
                @can('basic_dispatcher_view')
                    <a href="{{ route('index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Dispatcher">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.666 13.3332H25.3327C27.9994 13.3332 29.3327 11.9998 29.3327 9.33317V6.6665C29.3327 3.99984 27.9994 2.6665 25.3327 2.6665H22.666C19.9993 2.6665 18.666 3.99984 18.666 6.6665V9.33317C18.666 11.9998 19.9993 13.3332 22.666 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M6.66602 29.3332H9.33268C11.9993 29.3332 13.3327 27.9998 13.3327 25.3332V22.6665C13.3327 19.9998 11.9993 18.6665 9.33268 18.6665H6.66602C3.99935 18.6665 2.66602 19.9998 2.66602 22.6665V25.3332C2.66602 27.9998 3.99935 29.3332 6.66602 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M7.99935 13.3332C10.9449 13.3332 13.3327 10.9454 13.3327 7.99984C13.3327 5.05432 10.9449 2.6665 7.99935 2.6665C5.05383 2.6665 2.66602 5.05432 2.66602 7.99984C2.66602 10.9454 5.05383 13.3332 7.99935 13.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M23.9993 29.3332C26.9449 29.3332 29.3327 26.9454 29.3327 23.9998C29.3327 21.0543 26.9449 18.6665 23.9993 18.6665C21.0538 18.6665 18.666 21.0543 18.666 23.9998C18.666 26.9454 21.0538 29.3332 23.9993 29.3332Z"
                                fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                @endcan
            </li>






            <li>
                @can('orders_basic_view')
                    <a href="{{ route('orders') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Orders">
                        <svg width="23.6px" height="23.6px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.422 20.618C10.178 21.54 11.056 22 12 22V12L2.638 7.073L2.598 7.14C2 8.154 2 9.417 2 11.942V12.059C2 14.583 2 15.846 2.597 16.86C3.195 17.875 4.271 18.44 6.422 19.569L8.422 20.618Z"
                                fill="#F46624"></path>
                            <path
                                d="M17.5772 4.432L15.5772 3.382C13.8222 2.461 12.9442 2 12.0002 2C11.0552 2 10.1782 2.46 8.42218 3.382L6.42218 4.432C4.31818 5.536 3.24218 6.1 2.63818 7.072L12 12L21.3622 7.073C20.7562 6.1 19.6822 5.536 17.5772 4.432Z"
                                fill="#F46624"></path>
                            <path
                                d="M21.403 7.14L21.3622 7.073L12 12V22C12.944 22 13.822 21.54 15.578 20.618L17.578 19.568C19.729 18.439 20.805 17.875 21.403 16.86C22 15.846 22 14.583 22 12.06V11.943C22 9.418 22 8.154 21.403 7.14Z"
                                fill="#F46624"></path>
                            <path
                                d="M6.32295 4.48395L6.42295 4.43195L7.91595 3.64795L17.016 8.65295L21.041 6.64195C21.1776 6.79662 21.2983 6.96262 21.403 7.13995C21.553 7.39395 21.665 7.66395 21.749 7.96495L17.75 9.96395V12.9999C17.75 13.1989 17.6709 13.3896 17.5303 13.5303C17.3896 13.6709 17.1989 13.7499 17 13.7499C16.801 13.7499 16.6103 13.6709 16.4696 13.5303C16.329 13.3896 16.25 13.1989 16.25 12.9999V10.7139L12.75 12.4639V21.9039C12.505 21.9675 12.253 21.9997 12 21.9999C11.752 21.9999 11.507 21.9679 11.25 21.9039V12.4639L2.25195 7.96395C2.33595 7.66395 2.44795 7.39395 2.59795 7.13995C2.70195 6.96262 2.82262 6.79662 2.95995 6.64195L12 11.1619L15.387 9.46895L6.32295 4.48395Z"
                                fill="white"></path>
                        </svg>
                    </a>
                @endcan
            </li>


        </ul>
    @endif

    @if (auth()->user()->user_role?->value == 6)
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="dropdown">
                @can('view_export_reports')
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                        <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Reports"></span>

                        <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                                fill="#F46624"></path>
                            <path
                                d="M29.3334 13.3327V19.9993C29.3334 26.666 26.6667 29.3327 20 29.3327H12C5.33335 29.3327 2.66669 26.666 2.66669 19.9993V11.9993C2.66669 5.33268 5.33335 2.66602 12 2.66602H18.6667"
                                stroke="#f9f9f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M29.3334 13.3327H24C20 13.3327 18.6667 11.9993 18.6667 7.99935V2.66602L29.3334 13.3327Z"
                                fill="white" stroke="#f9f9f9" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M9.33331 17.334H17.3333" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M9.33331 22.666H14.6666" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </a>
                    <ul class="dropdown-menu text-small shadow reportsSideMenu">
                        <li><a class="dropdown-item" href="{{ route('reports') }}">Orders Reports</a></li>
                        <li><a class="dropdown-item" href="{{ route('operators.operator-reports') }}">Operators
                                Report</a>
                        <li><a class="dropdown-item" href="{{ route('brand-reports') }}">Clients Reports</a>
                        </li>
                    </ul>
                @endcan
            </li>
        </ul>
    @endif --}}


    <div class="originalMiniIcons">
        @if (auth()->user()->user_role == \App\Enum\UserRole::CLIENT)
            <div class="dropdown py-3 notifySideMenuContainer " style="border-top: 1px solid #585858">
                <a href="https://console.foodics.com/authorize?client_id=98eba077-21b0-4592-8774-4b340c679836&state=HHh1gNZsTUXfITL9JxMJEQezCoI2Faz2WFv6jTjC"
                    class="nav-link dropdown-toggle">
                    <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Foodics Connection"></span>

                    <svg id="logosandtypes_com" data-name="logosandtypes com" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 150 150">
                        <defs>
                            <style>
                                .cls-1 {
                                    fill: #450697;
                                }

                                .cls-2 {
                                    fill: none;
                                }
                            </style>
                        </defs>
                        <path class="cls-2" d="M0,0H150V150H0V0Z" />
                        <path id="Path_197" data-name="Path 197" class="cls-1"
                            d="M46.56,113.33h0c-3.46-3.86-9.74-3.21-14.4-3.21-6.23-.24-11.64,5.17-11.42,11.4,0,0,0,4.88,0,4.88-.24,6.22,5.18,11.64,11.4,11.39,4.66-.01,10.96,.67,14.42-3.2,3.57-3.23,3.36-8.67,3.25-13.08,.03-3.01-1.1-6.04-3.25-8.18" />
                        <path id="Path_200" data-name="Path 200" class="cls-1"
                            d="M49,50.25c0-4.38,3.55-7.93,7.93-7.92,0,0,76.15,0,76.15,0V14.33H57.58c-20.19,0-36.56,16.38-36.55,36.57v12.14c-.01,20.19,16.35,36.57,36.55,36.58,0,0,39.79,0,39.79,0v-27.99H56.92c-4.38,0-7.94-3.55-7.93-7.93,0,0,0-13.47,0-13.47Z" />
                    </svg>
                    <span class="menuMobileItem">Foodics Connection</span>
                </a>

            </div>
        @endif
        <div class="dropdown py-3 notifySideMenuContainer " style="border-top: 1px solid #585858;">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                data-bs-toggle="tooltip" data-bs-placement="right">
                <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Notifications"></span>

                <svg width="30.5px" height="30.5px" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C10.3431 22 9 20.6569 9 19H15C15 20.6569 13.6569 22 12 22Z" fill="#f9f9f9" />
                    <path
                        d="M18 15V10C18 6.68629 16.2091 4 14 4C13.4023 4 12.7741 4.15804 12.2071 4.44721C11.6401 4.73637 11.0607 5.13026 10.5 5.5C9.93934 5.86974 9.35995 6.26363 8.79289 6.55279C8.22585 6.84196 7.59769 7 7 7C5.79086 7 4 8.79086 4 10V15L2 17V18H22V17L20 15H18Z"
                        fill="#F46624" />
                </svg>
                <span class="notifyCount">{{ auth()->user()->unReaNotificationsCustom()->count() }}</span>
                <span class="menuMobileItem">Notifications</span>
            </a>
            <ul class="dropdown-menu text-small shadow reportsSideMenu notifySideMenu">
                <li class="dropdownNotifyHeader position-sticky top-0 d-flex justify-content-between">
                    <span>Notifications</span>
                    <span id="notifications-count">{{ auth()->user()->unReaNotificationsCustom()->count() }}</span>
                </li>
                <div id="notificationDropdown"></div>

            </ul>
        </div>

        <div class="dropdown ProfileSetting">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                data-bs-toggle="tooltip" data-bs-placement="right" title="Operators">
                <span class="tooltip-element" data-bs-toggle="tooltip" data-bs-placement="right"
                    title="Profile"></span>

                <svg width="23.6px" height="23.6px" viewBox="0 0 32 32" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16.0013 29.3327C23.3651 29.3327 29.3346 23.3631 29.3346 15.9993C29.3346 8.63555 23.3651 2.66602 16.0013 2.66602C8.63751 2.66602 2.66797 8.63555 2.66797 15.9993C2.66797 23.3631 8.63751 29.3327 16.0013 29.3327Z"
                        fill="#F46624" stroke="#f9f9f9" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                    </path>
                    <path
                        d="M16.1622 17.04C16.0689 17.0267 15.9489 17.0267 15.8422 17.04C13.4956 16.96 11.6289 15.04 11.6289 12.68C11.6289 10.2667 13.5756 8.30667 16.0022 8.30667C18.4156 8.30667 20.3756 10.2667 20.3756 12.68C20.3622 15.04 18.5089 16.96 16.1622 17.04Z"
                        fill="white" stroke="#f9f9f9" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                    </path>
                    <path
                        d="M24.989 25.84C22.6156 28.0133 19.469 29.3333 16.0023 29.3333C12.5356 29.3333 9.38896 28.0133 7.01562 25.84C7.14896 24.5867 7.94896 23.36 9.37562 22.4C13.029 19.9733 19.0023 19.9733 22.629 22.4C24.0556 23.36 24.8556 24.5867 24.989 25.84Z"
                        fill="white" stroke="#f9f9f9" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                    </path>
                </svg>
                <span class="menuMobileItem">Profile</span>
            </a>
            <ul class="dropdown-menu text-small shadow reportsSideMenu">
                @can('control_consolidated_orders_settings')
                    @if (auth()->user()->user_role?->value != 5)
                        <li><a class="dropdown-item" href="{{ route('settings') }}">System Setting</a></li>
                    @endif
                @endcan
                <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>

    </div>

</div>


<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
<script>
    function updateSidebar() {
        if ($(window).width() < 992) {
            $('#sidebar').addClass('collapse');
            $('#sidebar').removeClass('show');
        } else {
            $('#sidebar').removeClass('collapse'); // Remove collapse for larger screens
        }
    }

    $(document).ready(function() {
        updateSidebar(); // Check on page load

        // Check on window resize
        $(window).on('resize', function() {
            updateSidebar();
        });

        // Toggle sidebar visibility when the button is clicked
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('show'); // Toggle the "show" class
        });
    });
</script>

<script>
    $(document).ready(function() {
        function moveElements() {
            if ($(window).width() < 992) {
                if ($('.mobileMiniIcons').is(':empty')) {
                    $('.originalMiniIcons .notifySideMenuContainer').appendTo('.mobileMiniIcons');
                    $('.originalMiniIcons .ProfileSetting').appendTo('.mobileMiniIcons');
                    console.log("Here")
                }
            } else {
                if ($('.originalMiniIcons .notifySideMenuContainer').length === 0) {
                    $('.mobileMiniIcons .notifySideMenuContainer').appendTo('.originalMiniIcons');
                    $('.mobileMiniIcons .ProfileSetting').appendTo('.originalMiniIcons');
                }
            }
        }

        moveElements();

        $(window).resize(function() {
            moveElements();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.notifySideMenuContainer a').click(function() {
            $('#sidebarToggle').prop('checked', false);
            $('.customSidebar').removeClass('show');
        });
    });

    $(document).ready(function() {
        $('.ProfileSetting a').click(function() {
            $('#sidebarToggle').prop('checked', false);
            $('.customSidebar').removeClass('show');
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    #table_list {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    #table_list::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Alshrouq Express</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->first_name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 table-responsive p-0" id="table_list" style="height: 400px; overflow-y: auto;">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                @if (auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fa-solid fa-house"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('index') }}" class="nav-link">
                            <i class="fa-solid fa-bars"></i>
                            <p>
                                Dispatcher
                            </p>
                        </a>
                    </li>




                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Operators
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('operators') }}" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Operators</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('reports.billings')}}" class="nav-link">
                                    <i class="fa-solid fa-money-bill"></i>
                                    <p>Billings</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('operators.operator-reports')}}" class="nav-link">
                                    <i class="fa-solid fa-file"></i>
                                    <p>Operator Report</p>
                                </a>
                            </li>
                            


                            


                        </ul>
                    </li>






                    <li class="nav-item">
                        <a href="{{ route('clients') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Clients
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                User Management
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('vehicles') }}" class="nav-link">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>
                                Vehicles
                            </p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('integrations') }}" class="nav-link">
                            <i class="fa-solid fa-gears"></i>
                            <p>
                                Integrations
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('orders') }}" class="nav-link">
                            <i class="fa-solid fa-box"></i>
                            <p>
                                Orders
                            </p>
                        </a>
                    </li>

                   

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                Reports
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('reports') }}" class="nav-link">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <p>Orders</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('reports.driver-reports')}}" class="nav-link">
                                    <i class="fa-solid fa-users"></i>
                                    <p>Drivers</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{route('brand-reports')}}" class="nav-link">
                                    <i class="fa-solid fa-copyright"></i>
                                    <p>Brand</p>
                                </a>
                            </li>


                            


                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="{{ route('driver-reports') }}" class="nav-link">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <p>
                                Drivers' reports
                            </p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Profile
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa fa-cog"></i>
                                    <p>
                                        Settings
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('settings') }}" class="nav-link">
                                            <i class="far fa-circle  nav-icon"></i>
                                            <p>System Settings</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('locations') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Locations</p>
                                        </a>
                                    </li>


                                </ul>
                            </li>


                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                @endif


                @if (auth()->user()->hasRole('Client') || auth()->user()->hasRole('dispatcher'))
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fa-solid fa-house"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('index') }}" class="nav-link">
                            <i class="fa-solid fa-bars"></i>
                            <p>
                                Dispatcher
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('orders') }}" class="nav-link">
                            <i class="fa-solid fa-box"></i>
                            <p>
                                Orders
                            </p>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="{{ route('reports') }}" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                Reports
                            </p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Profile
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa fa-cog"></i>
                                    <p>
                                        Settings
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('settings') }}" class="nav-link">
                                            <i class="far fa-circle  nav-icon"></i>
                                            <p>System Settings</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('locations') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Locations</p>
                                        </a>
                                    </li>


                                </ul>
                            </li>


                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                @endif


            </ul>
            </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

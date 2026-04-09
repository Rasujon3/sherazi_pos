<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{URL::to('/dashboard')}}" class="brand-link">
        <img src="{{asset('back/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Booking Application</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('back/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="/" class="d-block">{{ Auth::user()->name ?? '' }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item {{ Request::is('dashboard') ? 'menu-open' : '' }}">
                    <a href="{{URL::to('/dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item {{ Request::is('services*') ? 'menu-open' : '' }}">
                    <a href="{{ route('services.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Services
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('services.create') }}" class="nav-link {{ request()->routeIs('services.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Service</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Service</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('durations*') ? 'menu-open' : '' }}">
                    <a href="{{ route('durations.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Durations
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('durations.create') }}" class="nav-link {{ request()->routeIs('durations.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Durations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('durations.index') }}" class="nav-link {{ request()->routeIs('durations.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Durations</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('branches*') ? 'menu-open' : '' }}">
                    <a href="{{ route('branches.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Branches
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('branches.create') }}" class="nav-link {{ request()->routeIs('branches.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Branches</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Branches</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('experiences*') ? 'menu-open' : '' }}">
                    <a href="{{ route('experiences.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Experiences
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('experiences.create') }}" class="nav-link {{ request()->routeIs('experiences.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Experience</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('experiences.index') }}" class="nav-link {{ request()->routeIs('experiences.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Experience</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('specialities*') ? 'menu-open' : '' }}">
                    <a href="{{ route('specialities.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Specialities
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('specialities.create') }}" class="nav-link {{ request()->routeIs('specialities.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Speciality</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('specialities.index') }}" class="nav-link {{ request()->routeIs('specialities.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Speciality</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('workingdays*') ? 'menu-open' : '' }}">
                    <a href="{{ route('workingdays.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Working Days
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('workingdays.create') }}" class="nav-link {{ request()->routeIs('workingdays.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Working Days</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('workingdays.index') }}" class="nav-link {{ request()->routeIs('workingdays.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Working Days</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('workingtimeranges*') ? 'menu-open' : '' }}">
                    <a href="{{ route('workingtimeranges.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Working Time Ranges
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('workingtimeranges.create') }}" class="nav-link {{ request()->routeIs('workingtimeranges.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Working Time Range</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('workingtimeranges.index') }}" class="nav-link {{ request()->routeIs('workingtimeranges.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Working Time Range</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('staffs*') ? 'menu-open' : '' }}">
                    <a href="{{ route('staffs.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Staffs
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('staffs.create') }}" class="nav-link {{ request()->routeIs('staffs.create') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('staffs.index') }}" class="nav-link {{ request()->routeIs('staffs.index') ? 'active_nav_menu' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Staff</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ Request::is('password-change') ? 'menu-open' : '' }}">
                    <a href="{{ route('password-change') }}" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Password Change
                        </p>
                    </a>
                </li>

                <li class="nav-header">EXAMPLES</li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

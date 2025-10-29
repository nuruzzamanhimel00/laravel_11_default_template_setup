<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- <li class="menu-title">{{__("Dashboard")}}</li> -->
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect {{ Route::is('dashboard') ? 'active' : '' }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span>{{__('Dashboard')}}</span>
                    </a>
                </li>

                <!-- <li class="menu-title">{{__("Main")}}</li> -->
                @canany(['List Admin','List Role'])
                <li class="{{ isActiveRoute(['roles', 'administrations']) }}">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ti-user"></i>
                        <span>{{__('Administration')}}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li class="{{ isActiveRoute('roles') }}">
                            <a href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                        </li>
                        <li class="{{ isActiveRoute('administrations') }}">
                            <a href="{{ route('administrations.index') }}">{{ __('System User') }}</a>
                        </li>
                    </ul>
                </li>
                @endcanany










                @can('List Agent')

                <li>
                    <a href="{{ route('agents.index') }}" class="waves-effect {{ Route::is('agents.index') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>{{__('Agents')}}</span>
                    </a>
                </li>
                @endcan

                {{-- @can('List User')

                <li>
                    <a href="{{ route('users.index') }}" class="waves-effect {{ Route::is('users.index') ? 'active' : '' }}">
                        <i class="far fa-user"></i>
                        <span>{{__('Home Users')}}</span>
                    </a>
                </li>
                @endcan --}}




                @can('Settings')
                <!-- <li class="menu-title">{{ __('Settings') }}</li> -->
                <li>
                    <a href="{{ route('settings.index') }}" class="waves-effect {{ Route::is('settings.index') ? 'active' : '' }}">
                        <i class="ti-settings"></i>
                        <span>{{__('Settings')}}</span>
                    </a>
                </li>
                @endcan


            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>

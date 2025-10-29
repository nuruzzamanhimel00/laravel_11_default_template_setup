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

                @can('List Warehouse')
                <li>
                    <a href="{{ route('warehouses.index') }}" class="waves-effect {{ Route::is('warehouses.index') ? 'active' : '' }}">
                        <i class="fas fa-industry"></i>

                        <span>{{__('Warehouse')}}</span>
                    </a>
                </li>
                @endcan

                @can('List Supplier')

                <li>
                    <a href="{{ route('suppliers.index') }}" class="waves-effect {{ Route::is('suppliers.index') ? 'active' : '' }}">
                        <i class="far fa-user-circle"></i>
                        <span>{{__('Suppliers')}}</span>
                    </a>
                </li>
                @endcan

                @canany(['List Product','List Category','List Brand'])
                <li class="{{ isActiveRoute(['products', 'categories','brands']) }}">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ti-package"></i>
                        <span>{{__('Products')}}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li class="{{ isActiveRoute('products') }}">
                            <a href="{{ route('products.index') }}">{{ __('Products') }}</a>
                        </li>
                        <li class="{{ isActiveRoute('categories') }}">
                            <a href="{{ route('categories.index') }}">{{ __('Product Category') }}</a>
                        </li>
                        <li class="{{ isActiveRoute('brands') }}">
                            <a href="{{ route('brands.index') }}">{{ __('Brands') }}</a>
                        </li>
                    </ul>
                </li>
                @endcanany

                @canany(['List Purchase','List Receive Purchase','List Return Purchase'])
                <li class="{{ Route::is('purchases.index')
                || Route::is('purchases.receive.index')
                || Route::is('purchases.return.index')
                ? 'mm-active' : '' }}">

                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-shopping-basket"></i>
                        <span>{{__('Purchases')}}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        @can('List Purchase')
                        <li class="{{ isActiveRoute('purchases') }}">
                            <a href="{{ route('purchases.index') }}">{{ __('Purchases') }}</a>
                        </li>
                        @endcan


                        @can('List Receive Purchase')
                        <li class=" {{ Route::is('purchases.receive.index') ? 'active' : '' }}">
                            <a href="{{ route('purchases.receive.index') }}">{{ __('Purchase Receive List') }}</a>
                        </li>
                        @endcan
                        @can('List Return Purchase')
                        <li class=" {{ Route::is('purchases.return.index') ? 'active' : '' }}">
                            <a href="{{ route('purchases.return.index') }}">{{ __('Purchase Return List') }}</a>
                        </li>
                        @endcan



                    </ul>
                </li>
                @endcanany

                @can('List Order')

                <li>
                    <a href="{{ route('orders.index') }}" class="waves-effect {{ Route::is('orders.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>{{__('Orders')}}</span>
                    </a>
                </li>
                @endcan


                @can('List Restaurant')

                <li>
                    <a href="{{ route('restaurants.index') }}" class="waves-effect {{ Route::is('restaurants.index') ? 'active' : '' }}">
                        <i class="far fa-building"></i>
                        <span>{{__('Restaurants')}}</span>
                    </a>
                </li>
                @endcan

                @can('List User')

                <li>
                    <a href="{{ route('users.index') }}" class="waves-effect {{ Route::is('users.index') ? 'active' : '' }}">
                        <i class="far fa-user"></i>
                        <span>{{__('Home Users')}}</span>
                    </a>
                </li>
                @endcan

                @can('List Delivery Man')

                <li>
                    <a href="{{ route('delivery-mans.index') }}" class="waves-effect {{ Route::is('delivery-mans.index') ? 'active' : '' }}">
                        <i class="mdi mdi-truck-delivery-outline"></i>
                        <span>{{__('Delivery Mans')}}</span>
                    </a>
                </li>
                @endcan

                @canany(['Sale Report','Purchase Report','Warehouse Stock Report'])
                <li class="{{ Route::is('orders.reports.index')
                || Route::is('purchases.reports.index')
                || Route::is('warehouse-stock.reports.index')
                ? 'mm-active' : '' }}">

                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-flag-checkered"></i>
                        <span>{{__('Reports')}}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        @can('Sale Report')
                        <li class="{{ isActiveRoute('orders.reports') }}">
                            <a href="{{ route('orders.reports.index') }}">{{ __('Orders Report') }}</a>
                        </li>
                        @endcan
                        @can('Purchase Report')
                        <li class="{{ isActiveRoute('purchases.reports') }}">
                            <a href="{{ route('purchases.reports.index') }}">{{ __('Purchases Report') }}</a>
                        </li>
                        @endcan
                        @can('Warehouse Stock Report')
                        <li class="{{ isActiveRoute('warehouse-stock.reports') }}">
                            <a href="{{ route('warehouse-stock.reports.index') }}">{{ __('Warehouse Stock Report') }}</a>
                        </li>
                        @endcan


                    </ul>
                </li>
                @endcanany

                @can('List Delivery Charge')

                <li>
                    <a href="{{ route('delivery-charges.index') }}" class="waves-effect {{ Route::is('delivery-charges.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>{{__('Delivery Charges')}}</span>
                    </a>
                </li>
                @endcan

                @can('List Promotion')

                <li>
                    <a href="{{ route('promotions.index') }}" class="waves-effect {{ Route::is('promotions.index') ? 'active' : '' }}">
                        <i class="fas fa-rainbow"></i>
                        <span>{{__('Promotions')}}</span>
                    </a>
                </li>
                @endcan
                @can('List Wishlist')

                <li>
                    <a href="{{ route('wish-lists.index') }}" class="waves-effect {{ Route::is('wish-lists.index') ? 'active' : '' }}">
                        <i class="mdi mdi-heart"></i>
                        <span>{{__('Wish Lists')}}</span>
                    </a>
                </li>
                @endcan


                {{-- @can('List Attribute')

                <li>
                    <a href="{{ route('attributes.index') }}" class="waves-effect {{ Route::is('attributes.index') ? 'active' : '' }}">
                        <i class="ti-settings"></i>
                        <span>{{__('Attributes')}}</span>
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

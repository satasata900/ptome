<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="{{ route('dashboard') }}"><img src="{{ asset('dashboard/img/logo.png') }}" alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini" href="{{ route('dashboard') }}"><img src="{{ asset('dashboard/img/icon.png') }}" alt="logo" /></a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc">
                <div class="profile-pic">
                    
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal">Welcome {{ auth()->user()->fullName }}</h5>
                    </div>
                </div>
                            
            </div>
        </li>
        <hr>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('dashboard') }}">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('contact_us') }}">
              <span class="menu-icon">
                <i class="mdi mdi-message"></i>
              </span>
              <span class="menu-title">Messages</span>

                <span class="badge badge-danger">{{ \App\Models\ContactUs::where('state','opened')->count() }}</span>
            </a>
        </li>

       
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('app_users') }}">
              <span class="menu-icon">
                <i class="mdi mdi-human-male-girl"></i>
              </span>
                <span class="menu-title">Users</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('wallets') }}">
              <span class="menu-icon">
                <i class="mdi mdi-wallet"></i>
              </span>
                <span class="menu-title">Wallets</span>
            </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('transaction') }}/?filter_by_date={{request('filter_by_date')}}">
              <span class="menu-icon">
                <i class="mdi mdi-wallet"></i>
              </span>
                <span class="menu-title">Transactions</span>
            </a>
        </li>


        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#USerServices" aria-expanded="false" aria-controls="USerServices">
              <span class="menu-icon">
                <i class="mdi mdi-security"></i>
              </span>
                <span class="menu-title">Users Services</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="USerServices">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('cities') }}">Cities</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('fields') }}">Fields</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('providers') }}">Providers</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('services') }}">Services</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('trader') }}">Traders</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('organization') }}">Organizations</a></li>
                </ul>
            </div>
        </li>



     
        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#Pages" aria-expanded="false" aria-controls="Pages">
              <span class="menu-icon">
                <i class="mdi mdi-security"></i>
              </span>
                <span class="menu-title">Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Pages">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('edit_page', 'terms-conditions') }}">Terms and conditions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('edit_page', 'about') }}"> About us </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('edit_page', 'privacy-policy') }}"> Privacy Policy </a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('branches') }}">
              <span class="menu-icon">
                <i class="mdi mdi-map-marker"></i>
              </span>
                <span class="menu-title">Branches</span>
            </a>
        </li>


        @role('system admin')
        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#system" aria-expanded="false" aria-controls="system">
              <span class="menu-icon">
                <i class="mdi mdi-security"></i>
              </span>
                <span class="menu-title">System</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="system">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('system_users') }}">Users</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('roles') }}"> Roles </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('permissions') }}"> Permissions </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('settings_edit') }}"> Settings </a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('admin_transactions') }}">
              <span class="menu-icon">
                <i class="mdi mdi-wallet"></i>
              </span>
                <span class="menu-title">Transfer Money</span>
            </a>
        </li>
        @endrole

       
    </ul>
</nav>
<!-- partial -->

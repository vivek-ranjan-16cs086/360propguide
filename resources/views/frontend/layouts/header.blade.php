<header id="navbar" class="{{ Request::is('/') ? '' : 'custom-nav' }} shadow-sm">

    <div>
        <div class="container customNav">

            <!-- end of menuBars -->
            <div class="logo">
                <a href="{{url('/')}}">
                    <img src="{{asset('frontend/360logo.png')}}" alt="360 PropGuide" width="250" height="100">
                </a>
            </div>
            <!-- end of logo -->
            <div class="menu d-flex align-items-center">
               <!-- <a href="{{url('contact')}}" class="contact-button btn d-none d-md-flex">
                    <i class="fas fa-phone"></i> CONTACT US
                </a> -->
                <div class="dropdown-wrapper mx-3">
                    @if (Auth::check() && Auth::user()->role_id == 2)
                    <a href="#" class="profile-button" id="userDropdown" tabindex="0">
                        @php
                        $frontendUser = Auth::user();
                        $initial = strtoupper(substr($frontendUser->name ?? 'User', 0, 1));
                        @endphp
                        <div class="custom-avatar">{{ $initial }}</div>
                        <i class="fa-solid fa-caret-down text-primary ms-2"></i>
                    </a>

                    <ul class="customDropdown">
                        <li>
                            <a class="dropdown-item dropdownLink" href="{{ route('list') }}">
                                <i class="fa-solid fa-list-ul pe-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdownLink" href="{{ route('profile') }}">
                                <i class="fa-solid fa-briefcase pe-2"></i> Profile
                            </a>
                        </li>
                        <li>
							<form id="logout-form" action="{{ route('frontend.logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
                            <a class="dropdown-item dropdownLink" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-arrow-right-from-bracket pe-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                    @else
                    <a href="{{ route('frontend.login') }}" class="btn customBtn rounded-3 p-2">
                        Post Property
                    </a>
                    @endif
                </div>


                <div class="menuBars  open">
                    <i class="fa-solid fa-bars-staggered"></i>

                </div>
            </div>

            <!-- end of menu -->
        </div>
        <!-- end of navbar -->
    </div>
    <!-- end of container -->
</header>
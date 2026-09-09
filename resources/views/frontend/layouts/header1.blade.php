<header id="navbar" class="{{ Request::is('/') ? '' : 'custom-nav' }} shadow-sm">
    <div>
        <div class="container customNav d-flex align-items-center justify-content-between">

            <!-- Left: Logo -->
            <div class="logo">
                <a class="head-tag" href="{{ url('/') }}">
                    <img src="{{ url('frontend/360logo.webp') }}" alt="360 PropGuide" width="250" height="100">
                </a>
            </div>

            <!-- Center: Nav links -->
            <ul class="nav-links d-none d-md-flex">
                <li class="nav-item">
                    <a class="head-tag tag {{ Request::is('projects*') ? 'active' : '' }}" href="{{ url('projects') }}">
                        <i class="fa-solid fa-building me-1"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="head-tag tag {{ Request::is('properties*') ? 'active' : '' }}" href="{{ url('properties') }}">
                        <i class="fa-solid fa-house me-1"></i> Properties
                    </a>
                </li>
                <li class="nav-item">
                    <a class="head-tag tag {{ Request::is('blogs*') ? 'active' : '' }}" href="{{ url('blogs') }}">
                        <i class="fa-solid fa-blog me-1"></i> Blogs
                    </a>
                </li>
                <li id="scrollSearch" class="{{ Request::is('/') ? 'hidden' : '' }}">
                    <a href="#" id="searchBtn">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </a>
                </li>
            </ul>

            <!-- Search bar -->
            <div id="searchDiv" class="searchDiv">
                <div class="container searchDiv-container">
                    <div class="searchboxs search-box w-100 position-relative">
                        <input id="searchInput" type="text" class="form-control keyword text-black" placeholder="Search for projects"
                            style="box-shadow: none;">
                        <ul class="project-results"></ul>
                        <button id="cancelSearch" class="btn btn-light close-btn ms-2">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Post property + profile/hamburger -->
            <div class="menu d-flex align-items-center">
                <div class="dropdown-wrapper mx-3">
                    @if (Auth::check() && Auth::user()->role_id == 2)
                        <a href="#" class="profile-button" id="userDropdown" tabindex="0" role="button" aria-haspopup="true"
                            aria-expanded="false" aria-controls="userDropdownMenu">
                            @php
                                $frontendUser = Auth::user();
                                $initial = strtoupper(substr($frontendUser->name ?? 'User', 0, 1));
                            @endphp
                            <div class="custom-avatar">{{ $initial }}</div>
                            <i class="fa-solid fa-caret-down text-primary ms-2"></i>
                        </a>

                        <ul id="userDropdownMenu" class="customDropdown" role="menu" aria-labelledby="userDropdown"
                            aria-hidden="true">
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
                                <form id="logout-form" action="{{ route('frontend.logout') }}" method="POST"
                                    style="display: none;">@csrf</form>
                                <a class="dropdown-item dropdownLink" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-arrow-right-from-bracket pe-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    @else
                        <a href="{{ route('frontend.login') }}" class="btn customBtn">Post Property</a>
                    @endif
                </div>

                <!-- Hamburger -->
                <div class="menuBars open">
                    <i class="fa-solid fa-bars-staggered"></i>
                </div>
            </div>
        </div>

        <!-- Mobile fixed bottom nav -->
        @php
            $isProjectDetail = Request::is('projects/*'); // checks if URL matches projects/slug
        @endphp

        @if($isProjectDetail)
            <div class="mobile-bottom-nav d-md-none fixed-bottom bg-light py-3 shadow-sm">
                <div class="row g-2 px-2">
                    <!-- Download Brochure Button -->
                    <div class="col-6" >
                        <button type="button" class="btn customBtn w-100 rounded-3 download-btn" data-bs-toggle="modal"
                            data-bs-target="#contactModalPopup">
                            <i class="fa-solid fa-download p-2"></i>Brochure
                        </button>
                    </div>

                    <!-- Call Button -->
					<div class="col-6">
    <button type="button"
        class="btn customBtn w-100 rounded-3 download-btn"
        onclick="window.location.href='tel:+919643020020'"
        style="background-color:#F05736; color:#fff; border:1px solid #F05736;">
        <i class="fa-solid fa-phone p-2"></i>Call
    </button>
</div>
                    
                </div>
            </div>

        @else
            <nav class="mobile-bottom-nav d-md-none" aria-label="Mobile Navigation">
                <ul class="mobile-nav-list">
                    <li><a href="{{ url('projects') }}" class="{{ Request::is('projects*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i><span>Projects</span></a></li>
                    <li><a href="{{ url('properties') }}" class="{{ Request::is('properties*') ? 'active' : '' }}"><i class="fa-solid fa-house"></i><span>Properties</span></a></li>
                    <li><a href="{{ url('blogs') }}" class="{{ Request::is('blogs*') ? 'active' : '' }}"><i class="fa-solid fa-blog"></i><span>Blogs</span></a></li>
                    <li><a href="#" id="searchBtnMobile"><i class="fa-solid fa-magnifying-glass"></i><span>Search</span></a></li>
                </ul>
            </nav>
        @endif

        <!-- Mobile vertical contact rail -->
        <div id="mobileContactRail" class="mobile-contact-rail d-md-none" aria-hidden="false">
            <button id="railToggle" class="rail-toggle" aria-label="Toggle contact rail">
                <i class="fa fa-chevron-right"></i>
            </button>
            <a aria-label="call link" class="rail-btn phone" href="tel:+919643020020" target="_blank">
                <i class="fa fa-phone"></i>
            </a>
            <a aria-label="whatsapp link" class="rail-btn whatsapp" href="https://wa.me/+919643020020" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        </div>
    </div>
</header>

@if (Request::is('/'))
<script>
    function handleNavbarScroll() {
        const mobileThreshold = 20;
        const desktopThreshold = 676;
        const isMobile = window.innerWidth <= 768;
        const threshold = isMobile ? mobileThreshold : desktopThreshold;

        const scrollSearch = document.getElementById('scrollSearch');
        const navbar = document.getElementById('navbar');
        const scrolled = window.scrollY > threshold;
        const mobileBottomNav = document.querySelector('.mobile-bottom-nav');

        if (scrollSearch) {
            if (scrolled) scrollSearch.classList.remove('hidden');
            else scrollSearch.classList.add('hidden');
        }

        if (navbar) {
            if (scrolled) navbar.classList.add('custom-nav');
            else navbar.classList.remove('custom-nav');
        }

        if (mobileBottomNav) {
            if (scrolled) mobileBottomNav.classList.add('nav-bottom-scrolled');
            else mobileBottomNav.classList.remove('nav-bottom-scrolled');
        }
    }

    window.addEventListener('scroll', handleNavbarScroll);
    window.addEventListener('resize', handleNavbarScroll);
    document.addEventListener('DOMContentLoaded', handleNavbarScroll);
</script>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchBtn = document.getElementById("searchBtn");
        const searchBtnMobile = document.getElementById("searchBtnMobile");
        const cancelSearch = document.getElementById("cancelSearch");
        const searchDiv = document.getElementById("searchDiv");
        const searchInput = document.getElementById("searchInput");
        const navLinks = document.querySelector(".nav-links");

        // ---------------- Open Search ----------------
        const openSearch = (e) => {
            e.preventDefault();
            searchDiv.classList.add("active");
            navLinks.classList.add("hide-links"); 


            document.body.classList.add("search-open"); 

            searchInput.focus({ preventScroll: true });
        };

        // ---------------- Close Search ----------------
        const closeSearch = () => {
            searchDiv.classList.remove("active");
            navLinks.classList.remove("hide-links");


            document.body.classList.remove("search-open");

            searchInput.value = "";
        };

        if (searchBtn) searchBtn.addEventListener("click", openSearch);
        if (searchBtnMobile) searchBtnMobile.addEventListener("click", openSearch);
        if (cancelSearch) cancelSearch.addEventListener("click", closeSearch);

        // prevent browser from scrolling while typing
        searchInput.addEventListener('keydown', (e) => {
            e.stopPropagation();      // stop scroll events bubbling
        });
        searchInput.addEventListener('input', (e) => { 
            e.stopPropagation();
        });

    });

    

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchBtn = document.getElementById("searchBtn");
    const searchBtnMobile = document.getElementById("searchBtnMobile");
    const cancelSearch = document.getElementById("cancelSearch");
    const searchDiv = document.getElementById("searchDiv");
    const searchInput = document.getElementById("searchInput");
    const navLinks = document.querySelector(".nav-links");
    const projectResults = document.querySelector(".project-results");

    // Open Search
    const openSearch = (e) => {
        e.preventDefault();
        e.stopPropagation();

        searchDiv.classList.add("active");
        if (navLinks) navLinks.classList.add("hide-links");

        document.body.classList.add("search-open");

        searchInput.focus({ preventScroll: true });
    };

    // Close Search
    const closeSearch = () => {
        searchDiv.classList.remove("active");
        if (navLinks) navLinks.classList.remove("hide-links");

        document.body.classList.remove("search-open");
        searchInput.value = "";
    };

    if (searchBtn) searchBtn.addEventListener("click", openSearch);
    if (searchBtnMobile) searchBtnMobile.addEventListener("click", openSearch);

    if (cancelSearch) {
        cancelSearch.addEventListener("click", function (e) {
            e.stopPropagation();
            closeSearch();
        });
    }

    // Search box ke andar click ho to close na ho
    searchDiv.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    // Results ke andar click ho to close na ho
    if (projectResults) {
        projectResults.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

    // Outside click
    document.addEventListener("click", function (e) {
        if (!searchDiv.classList.contains("active")) return;

        if (
            !searchDiv.contains(e.target) &&
            !(searchBtn && searchBtn.contains(e.target)) &&
            !(searchBtnMobile && searchBtnMobile.contains(e.target))
        ) {
            closeSearch();
        }
    });

    searchInput.addEventListener("keydown", (e) => e.stopPropagation());
    searchInput.addEventListener("input", (e) => e.stopPropagation());
});
</script>

<script>
    document.getElementById("railToggle").addEventListener("click", function() {
        const rail = document.getElementById("mobileContactRail");
        rail.classList.toggle("collapsed");
    });
</script> 

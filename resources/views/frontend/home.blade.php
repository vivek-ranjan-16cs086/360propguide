@extends('frontend.layouts.app')
@section('skipAos', true)
@section('title', "Real Estate Consultant in Noida | Buy Flats, Projects in Delhi NCR")
@section('description', "Trusted Real Estate Consultant in Noida & Delhi NCR. Buy, Sell or Invest in top residential & commercial projects. Expert guidance, home loans. Call Now!")
@section('keywords', "360 PropGuide, Real Estate Services, Delhi NCR Properties, Real Estate, real estate company, noida property, real estate company in noida, property greater noida, property in greater noida west, property in gr noida")
@section('priority')
@endSection
@section('canonical', url()->current())
@section('customCSS')
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "ItemList",
  "itemListElement": [
    {
      "@type": "SiteNavigationElement",
      "position": 1,
      "name": "About Us",
      "description": "Spaces crafted for a refined lifestyle.",
      "url": "https://www.360propguide.com/about-us"
    },
    {
      "@type": "SiteNavigationElement",
      "position": 2,
      "name": "Explore Project",
      "description": "Discover Real Estate Top Projects with 360 PropGuide",
      "url": "https://www.360propguide.com/projects"
    },
    {
      "@type": "SiteNavigationElement",
      "position": 3,
      "name": "360 Knowledge Base",
      "description": "Explore ideas for a better lifestyle.",
      "url": "https://www.360propguide.com/blogs"
    },
    {
      "@type": "SiteNavigationElement",
      "position": 4,
      "name": "Get in Touch",
      "description": "Connect to bring your dream home closer.",
      "url": "https://www.360propguide.com/contact"
    }
  ]
}
</script>
<link rel="stylesheet"
    href="{{ url('frontend/css/home.css') }}?v={{ filemtime(public_path('frontend/css/home.css')) }}">
@endSection

@section('content')
<div class="home-wrapper">

    <!-- =========================================================================
         1. HERO SECTION (original)
         ========================================================================= -->
    <section class="heroSection" id="heroSection">

        <div class="hero-video">
            <picture id="heroPoster">
                <source media="(max-width: 767px)" srcset="{{ url('frontend/360location-mobile-poster.webp') }}"
                    width="390" height="693">
                <img src="{{ url('frontend/360location-desktop-poster.webp') }}" loading="eager" fetchpriority="high"
                    width="1280" height="720" alt="360 PropGuide properties in Delhi NCR"
                    style="width:100%;height:100%;object-fit:cover;display:block;">
            </picture>
            <video id="myVideo" class="location-video" muted autoplay loop playsinline preload="none"
                data-mobile-src="{{ url('frontend/360location-mobile-web.mp4') }}"
                data-desktop-src="{{ url('frontend/360location-desktop-web.mp4') }}"
                aria-label="360 PropGuide property location video">
            </video>
        </div>

        <div class="hero-atmosphere" aria-hidden="true">
            <span class="hero-orb hero-orb--one"></span>
            <span class="hero-orb hero-orb--two"></span>
            <span class="hero-grid"></span>
        </div>

        <div class="hero-content">
            <div class="container">
                @php
                    $heroProjectCount = (int) ($pageData['projectCount'] ?? 0);
                    $heroPropertyCount = (int) ($pageData['propertyCount'] ?? 0);
                    $heroCountLabel = static function (int $count): string {
                        if ($count >= 1000) {
                            return rtrim(rtrim(number_format($count / 1000, 1, '.', ''), '0'), '.') . 'K+';
                        }
                        return $count . '+';
                    };
                @endphp
                <h1 class="hero-title">New projects to buy in <em>Delhi NCR</em></h1>
                <p class="hero-lead" id="heroLead"
                    data-lead-projects="{{ $heroCountLabel($heroProjectCount) }} verified projects and 100% RERA checked listings"
                    data-lead-properties="{{ $heroCountLabel($heroPropertyCount) }} listings added across Delhi NCR"
                    data-lead-commercial="Shops and commercial spaces across Delhi NCR">{{ $heroCountLabel($heroProjectCount) }} verified projects and 100% RERA checked listings</p>

                <div class="hero-search-panel">
                    <div class="hero-tabs" role="tablist" aria-label="Search type">
                        <button type="button" class="hero-tab is-active" role="tab" aria-selected="true" data-mode="projects">Projects</button>
                        <button type="button" class="hero-tab" role="tab" aria-selected="false" data-mode="properties">Buy</button>
                        <button type="button" class="hero-tab" role="tab" aria-selected="false" data-mode="commercial">Commercial</button>
                    </div>

                    <div class="hero-search searchboxs" data-search-mode="projects">
                        <div class="hero-search__city" id="heroCityPicker">
                            <button type="button" class="hero-city-trigger" id="heroCityTrigger"
                                aria-haspopup="listbox" aria-expanded="false" aria-controls="heroCityMenu">
                                <span class="hero-city-trigger__icon">
                                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                </span>
                                <span class="hero-city-trigger__copy">
                                    <span class="hero-city-trigger__label">Buy in</span>
                                    <span class="hero-city-trigger__value" id="heroCityValue">All cities</span>
                                </span>
                                <i class="fa-solid fa-chevron-down hero-city-trigger__chevron" aria-hidden="true"></i>
                            </button>

                            <div class="hero-city-menu" id="heroCityMenu" hidden>
                                <p class="hero-city-menu__head">Select city</p>
                                <ul class="hero-city-menu__list" role="listbox" aria-label="Cities">
                                    <li>
                                        <button type="button" class="hero-city-option is-selected" data-value="">
                                            <i class="fa-solid fa-globe" aria-hidden="true"></i>
                                            <span>All cities</span>
                                            <i class="fa-solid fa-check hero-city-option__check" aria-hidden="true"></i>
                                        </button>
                                    </li>
                                    @if(!empty($pageData['cities']) && count($pageData['cities']) > 0)
                                        @foreach($pageData['cities'] as $city)
                                            <li>
                                                <button type="button" class="hero-city-option" data-value="{{ $city }}">
                                                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                                    <span>{{ $city }}</span>
                                                    <i class="fa-solid fa-check hero-city-option__check" aria-hidden="true"></i>
                                                </button>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <select class="hero-search__native" name="location" id="heroLocation" tabindex="-1" aria-hidden="true">
                                <option value="">All cities</option>
                                @if(!empty($pageData['cities']) && count($pageData['cities']) > 0)
                                    @foreach($pageData['cities'] as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="hero-search__query">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <input type="text" id="keyword" name="keyword"
                                class="form-control keyword"
                                placeholder="Search for locality, landmark, project or builder"
                                autocomplete="off">
                            <ul class="project-results"></ul>
                        </div>

                        <select class="hero-search__native" name="bhkType" id="heroBhk" tabindex="-1" aria-hidden="true">
                            <option value=""></option>
                            <option value="Shops">Shops</option>
                        </select>

                        <button class="btn searchBtn hero-search__submit" type="button" id="keybutton">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <span>Search</span>
                        </button>
                    </div>
                </div>

                @php
                    $heroCities = !empty($pageData['cities']) ? collect($pageData['cities'])->take(6) : collect();
                @endphp
                @if($heroCities->isNotEmpty())
                    <div class="hero-cities">
                        <span class="hero-cities__label"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Popular Cities</span>
                        @foreach($heroCities as $city)
                            <a class="hero-city-pill" href="{{ route('projects', ['location' => [$city]]) }}" data-city="{{ $city }}">{{ $city }} <i class="fa-solid fa-chevron-right" aria-hidden="true"></i></a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <button type="button" class="hero-scroll" id="scrollDownBtn" aria-label="Scroll to next section">
            <span>Explore</span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </button>
    </section>
   

    <!-- =========================================================================
         2. TRUST & KEY METRICS BAR
         ========================================================================= -->
    <section class="trust-metrics-section">
        <div class="container">
            <div class="trust-metrics-card">
                <div class="metrics-grid">
                    <div class="metric-item">
                        <div class="metric-icon-wrap">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        @php
                            $startDate = \Carbon\Carbon::create(2018, 8, 11);
                            $years = $startDate->diffInYears(now());
                        @endphp

                        <div>
                            <div class="metric-number">
                                {{ $years }} <span class="metric-accent">+</span> Years
                            </div>
                            <p class="metric-label">Of Proven Trust</p>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon-wrap">
                            <i class="fa-solid fa-chart-area"></i>
                        </div>
                        <div>
                            <div class="metric-number">2.5 <span class="metric-accent">+</span> Mn. Sq. Ft.</div>
                            <p class="metric-label">Area Transacted</p>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                        <div>
                            <div class="metric-number">2,000<span class="metric-accent">+</span> Cr</div>
                            <p class="metric-label">Inventory Sold</p>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon-wrap">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>
                        <div>
                            <div class="metric-number">10,000<span class="metric-accent">+</span></div>
                            <p class="metric-label">Happy Families</p>
                        </div>
                    </div>
                </div>

                <div class="trust-badges-bar">
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-shield-halved"></i> 100% RERA Verified Projects
                    </div>
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-hand-holding-dollar"></i> Zero Brokerage on Fresh Bookings
                    </div>
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-star text-warning"></i> 4.9/5 Customer Satisfaction
                    </div>
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-handshake"></i> 50+ Top Tier NCR Builders
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. POPULAR PROJECTS CAROUSEL
         ========================================================================= -->
    <section class="propertties-section" id="propertiesSection">
        <div class="container">
            <div class="projects-section-card">
                <div
                    class="section-header-wrap d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="section-badge"><i class="fa-solid fa-fire"></i> Handpicked Projects</span>
                        <h2 class="section-title">Popular & Trending Projects</h2>
                        <p class="section-subtitle">Handpicked projects across Noida, Greater Noida West, Yamuna Expressway & Dehradun — RERA-verified, transparent pricing, trusted builders.</p>
                    </div>
                    <a href="{{ url('projects') }}" class="btn-theme-outline">
                        <span>Explore All Projects</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>

                <div class="swiper mySwiper2" aria-label="Popular projects carousel">
                    <div class="swiper-wrapper">
                        @if(!empty($pageData['projects']) && count($pageData['projects']) > 0)
                            @foreach($pageData['projects'] as $project)
                                @php
                                    $statusClean = strtolower(clean($project->project_status));
                                    $statusClass = 'status-construction';
                                    if (str_contains($statusClean, 'ready')) {
                                        $statusClass = 'status-ready';
                                    } elseif (str_contains($statusClean, 'launch')) {
                                        $statusClass = 'status-launch';
                                    }
                                    $isReraApproved = !empty($project->rera_no) && strtoupper(trim($project->rera_no)) !== 'N/A';
                                @endphp
                                <div class="swiper-slide">
                                    <a class="project-card-link" href="{{ route('projects.details', $project->slug) }}">
                                        <div class="itemSlider card project-card">
                                            <div class="status-badge {{ $statusClass }}">
                                                {{ clean($project->project_status) }}
                                            </div>
                                            <div class="project-card__image-wrap">
                                                <img src="{{ storageUrl($project->hero_images ?: $project->logo_image) }}"
                                                    alt="{{ $project->project_name }}" loading="lazy" decoding="async">
                                            </div>
                                            <div class="projectDescp">
                                                <h3 class="title">{{ $project->project_name }}</h3>
                                                <div class="projectDescBody">
                                                    <div class="projectDescBodyFirst">
                                                        <p>
                                                            <i class="fa-solid fa-location-dot"></i>
                                                            <span>{{ $project->location }}</span>
                                                        </p>
                                                        <p>
                                                            <i class="fa-solid fa-building-columns"></i>
                                                            <span>{{ $project->typology_string }}</span>
                                                        </p>
                                                        @if($isReraApproved)
                                                            <p class="project-rera-line">
                                                                <i class="fa-solid fa-circle-check text-emerald"></i>
                                                                <span class="rera-approved-text">RERA Approved</span>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="project-card__footer">
                                                    <p class="price">
                                                        <span class="price-currency">&#8377;</span>
                                                        @if(!empty($project->price))
                                                            @if(!empty($project->max_price) && $project->price != $project->max_price)
                                                                {{ formatPrice($project->price) }} -
                                                                {{ formatPrice($project->max_price) }}
                                                            @else
                                                                {{ formatPrice($project->price) }} Onwards
                                                            @endif
                                                        @endif
                                                    </p>
                                                    <span class="project-card__action">
                                                        <span>View Details</span>
                                                        <i class="fa-solid fa-arrow-right"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next d-none" aria-label="Next project"></div>
                    <div class="swiper-button-prev d-none" aria-label="Previous project"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. 360 FINANCIAL CALCULATORS & TOOLS
         ========================================================================= -->
    <section class="propertties-section pt-0">
        <div class="container">
            <div class="tools-section-card">
                <div class="section-header-wrap">
                    <span class="section-badge badge-blue"><i class="fa-solid fa-calculator"></i> Financial
                        Planning</span>
                    <h2 class="section-title">Smart Property Investment Tools</h2>
                    <p class="section-subtitle">Plan smarter with free EMI, budget & loan eligibility calculators — make confident,
					data-backed property decisions instantly.</p>
                </div>

                <div class="swiper mySwiper5">
                    <div class="swiper-wrapper">
                        <!-- Budget Calculator -->
                        <div class="swiper-slide">
                            <a class="tool-card-link" href="{{ route('budget-get') }}">
                                <div class="tool-card">
                                    <div class="tool-card__icon-box">
                                        <i class="fa-solid fa-wallet"></i>
                                    </div>
                                    <div class="tool-card__content">
                                        <h3>Budget Calculator</h3>
                                        <p>Check your ideal affordability range before shortlisting homes.</p>
                                        <span class="tool-card__cta">Calculate Now <i
                                                class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- EMI Calculator -->
                        <div class="swiper-slide">
                            <a class="tool-card-link" href="{{ route('emi-calculator') }}">
                                <div class="tool-card">
                                    <div class="tool-card__icon-box">
                                        <i class="fa-solid fa-calculator"></i>
                                    </div>
                                    <div class="tool-card__content">
                                        <h3>EMI Calculator</h3>
                                        <p>Estimate monthly loan repayments with custom tenures & rates.</p>
                                        <span class="tool-card__cta">Calculate Now <i
                                                class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Area Converter -->
                        <div class="swiper-slide">
                            <a class="tool-card-link" href="{{ route('area-calculator') }}">
                                <div class="tool-card">
                                    <div class="tool-card__icon-box">
                                        <i class="fa-solid fa-ruler-combined"></i>
                                    </div>
                                    <div class="tool-card__content">
                                        <h3>Area Converter</h3>
                                        <p>Convert Sq. Ft., Sq. Yards, Acres, Bigha, and Hectares easily.</p>
                                        <span class="tool-card__cta">Convert Units <i
                                                class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Loan Eligibility -->
                        <div class="swiper-slide">
                            <a class="tool-card-link" href="{{ route('loan-calulator') }}">
                                <div class="tool-card">
                                    <div class="tool-card__icon-box">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <div class="tool-card__content">
                                        <h3>Loan Eligibility</h3>
                                        <p>Discover your maximum borrowing capacity across top banks.</p>
                                        <span class="tool-card__cta">Check Eligibility <i
                                                class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Buy Ability -->
                        <div class="swiper-slide">
                            <a class="tool-card-link" href="{{ route('ability-get') }}">
                                <div class="tool-card">
                                    <div class="tool-card__icon-box">
                                        <i class="fa-solid fa-chart-pie"></i>
                                    </div>
                                    <div class="tool-card__content">
                                        <h3>Buy Ability</h3>
                                        <p>Find ideal configurations and localities matching your budget.</p>
                                        <span class="tool-card__cta">Explore Options <i
                                                class="fa-solid fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-button-next d-none" aria-label="Next"></div>
                    <div class="swiper-button-prev d-none" aria-label="Previous"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         5. POST PROPERTY BANNER
         ========================================================================= -->
    <section class="container post-property-section" id="postPropertySection">
        <div class="post-property-wrap">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="post-property-header">
                        <span class="section-badge"
                            style="background: rgba(255,255,255,0.15); color: #ffffff; border-color: rgba(255,255,255,0.25);">
                            <i class="fa-solid fa-bolt text-warning"></i> For Property Owners & Landlords
                        </span>
                        <h3>Want to Sell or Rent Your Property Faster?</h3>
                        <p>List your property on 360 PropGuide for free and connect directly with thousands of verified
                            home seekers and investors across Delhi NCR.</p>
                    </div>
                    <div class="post-property-features">
                        <span class="feature-chip"><i class="fa-solid fa-circle-check"></i> 100% Free Listing</span>
                        <span class="feature-chip"><i class="fa-solid fa-circle-check"></i> Direct Buyer Leads</span>
                        <span class="feature-chip"><i class="fa-solid fa-circle-check"></i> Zero Spam Guarantee</span>
                    </div>
                    <a href="{{ route('frontend.login') }}" class="btn-theme-primary">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Post Your Property Free</span>
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="post-property-steps-grid">
                        <div class="post-property-step-card">
                            <div class="step-badge">1</div>
                            <h4 class="step-card-title">Create Listing</h4>
                            <p class="step-card-desc">Add property type, locality, floor plan, and expected pricing in 2
                                minutes.</p>
                        </div>
                        <div class="post-property-step-card">
                            <div class="step-badge">2</div>
                            <h4 class="step-card-title">Add Photos</h4>
                            <p class="step-card-desc">High quality photos attract 5x more genuine buyer views and
                                responses.</p>
                        </div>
                        <div class="post-property-step-card">
                            <div class="step-badge">3</div>
                            <h4 class="step-card-title">Get Inquiries</h4>
                            <p class="step-card-desc">Receive verified phone & WhatsApp inquiries directly without
                                intermediaries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. VIDEO INSIGHTS & SHORTS
         ========================================================================= -->
    @if(!empty($youtubeVideo))
        <section class="propertties-section pt-0">
            <div class="container">
                <div class="video-section-card">
                    <div class="section-header-wrap">
                        <span class="section-badge"><i class="fa-brands fa-youtube"></i> Video Reviews</span>
                        <h2 class="section-title">360 Real Estate Insights</h2>
                        <p class="section-subtitle">Watch expert project walkthroughs, construction updates, and sector
                            investment analyses.</p>
                    </div>

                    <div class="swiper mySwiper3">
                        <div class="swiper-wrapper">
                            @foreach($youtubeVideo as $video)
                                <div class="swiper-slide">
                                    <a href="https://www.youtube.com/watch?v={{ $video->video_id }}" target="_blank"
                                        rel="noopener noreferrer" class="video-card-link">
                                        <div class="video-card">
                                            <div class="video-card__thumbnail">
                                                <img src="https://i.ytimg.com/vi/{{ $video->video_id }}/mqdefault.jpg"
                                                    class="video-card__img" alt="{{ $video->title }}" loading="lazy"
                                                    decoding="async">
                                                <div class="video-card__duration">{{ formatDuration($video->duration) }}</div>
                                                <div class="video-card__play"><i class="fa-solid fa-play"></i></div>
                                            </div>
                                            <div class="video-card__info">
                                                <h3>{{ $video->title }}</h3>
                                                <p class="video-card__meta">
                                                    <i class="fa-regular fa-eye"></i> {{ formatViews($video->views) }} views •
                                                    {{ formatYoutubeDate($video->published_time) }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next d-none" aria-label="Next project"></div>
                        <div class="swiper-button-prev d-none" aria-label="Previous project"></div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($youtubeShorts))
        <section class="propertties-section pt-0">
            <div class="container">
                <div class="video-section-card">
                    <div class="section-header-wrap">
                        <span class="section-badge badge-blue"><i class="fa-solid fa-bolt"></i> 60-Second Tips</span>
                        <h2 class="section-title">360 Shorts</h2>
                        <p class="section-subtitle">Bite-sized real estate advice, property highlights, and market updates
                            in 60 seconds.</p>
                    </div>

                    <div class="swiper mySwiper4">
                        <div class="swiper-wrapper">
                            @foreach($youtubeShorts as $short)
                                <div class="swiper-slide">
                                    <a href="https://www.youtube.com/shorts/{{ $short->video_id }}" target="_blank"
                                        rel="noopener noreferrer" class="video-card-link">
                                        <div class="video-card">
                                            <div class="video-card__thumbnail">
                                                <img src="{{ $short->thumbnail }}" class="video-card__img"
                                                    alt="{{ $short->title }}" loading="lazy" decoding="async">
                                                <span class="shorts-tag"><i class="fa-brands fa-youtube"></i> Shorts</span>
                                                <div class="video-card__duration">{{ formatDuration($short->duration) }}</div>
                                                <div class="video-card__play"><i class="fa-solid fa-play"></i></div>
                                            </div>
                                            <div class="video-card__info">
                                                <h3>{{ $short->title }}</h3>
                                                <p class="video-card__meta">
                                                    <i class="fa-regular fa-eye"></i> {{ formatViews($short->views) }} views
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next d-none" aria-label="Next project"></div>
                        <div class="swiper-button-prev d-none" aria-label="Previous project"></div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- =========================================================================
         7. WHY CHOOSE 360 PROPGUIDE (TRUST PILLARS)
         ========================================================================= -->
    <section class="why-choose-section">
        <div class="container">
            <div class="why-choose-card">
                <div class="section-header-wrap text-center max-w-700 mx-auto">
                    <span class="section-badge"><i class="fa-solid fa-shield-halved"></i> Why 360 PropGuide</span>
                    <h2 class="section-title">Why 360 PropGuide is Noida's Most Trusted Property Consultant</h2>
                    <p class="section-subtitle">100% RERA-verified listings, zero brokerage on bookings,
					and dedicated advisors focused on your budget — not commissions.</p>
                </div>

                <div class="pillars-grid">
                    <div class="pillar-item">
                        <div class="pillar-icon">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <h4 class="pillar-title">100% RERA Verified</h4>
                        <p class="pillar-desc">Every listing is thoroughly verified for RERA compliance, title clarity,
                            and builder track records before being featured.</p>
                    </div>

                    <div class="pillar-item">
                        <div class="pillar-icon">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <h4 class="pillar-title">Zero Brokerage on Launches</h4>
                        <p class="pillar-desc">Enjoy transparent, direct-from-developer pricing with absolutely zero
                            brokerage fees on new residential & commercial bookings.</p>
                    </div>

                    <div class="pillar-item">
                        <div class="pillar-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h4 class="pillar-title">Dedicated Property Advisor</h4>
                        <p class="pillar-desc">Receive unbiased, data-backed guidance tailored specifically to your
                            family's budget, lifestyle needs, and ROI targets.</p>
                    </div>

                    <div class="pillar-item">
                        <div class="pillar-icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h4 class="pillar-title">End-to-End Support</h4>
                        <p class="pillar-desc">From customized site visits and competitive home loan approvals to legal
                            registry and key handover, we guide you at every step.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         8. END-TO-END REAL ESTATE SERVICES
         ========================================================================= -->
    <section class="services-section" id="servicesSection">
        <div class="container">
            <div class="services-wrapper-card">
                <!-- Consumer Services -->
                <div class="section-header-wrap">
                    <span class="section-badge badge-blue"><i class="fa-solid fa-handshake"></i> Consumer
                        Solutions</span>
                    <h2 class="section-title">Complete Real Estate Services in Noida & Delhi NCR</h2>
                    <p class="section-subtitle">Property search, home loans, legal checks & interior design — your
					entire real estate journey, handled under one roof.</p>
                </div>

                <div class="consumer-services-grid">
                    <!-- 1. Consultancy -->
                    <div class="consumer-service-card">
                        <span class="service-step-num">01</span>
                        <div class="service-icon-box">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <h4 class="service-card-title">Real Estate Consultancy</h4>
                        <p class="service-card-desc">Data-backed advisory across Noida, Greater Noida, and Delhi NCR for
                            buying, selling, or leasing properties within your budget.</p>
                    </div>

                    <!-- 2. Home Loan Help -->
                    <div class="consumer-service-card">
                        <span class="service-step-num">02</span>
                        <div class="service-icon-box">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h4 class="service-card-title">Home Loan Assistance</h4>
                        <p class="service-card-desc">Secure competitive interest rates and seamless doorstep approvals
                            through our partnerships with leading banks across India.</p>
                    </div>

                    <!-- 3. Interior Design Collaboration -->
                    <div class="consumer-service-card">
                        <span class="service-step-num">03</span>
                        <div class="service-icon-box">
                            <i class="fa-solid fa-pen-ruler"></i>
                        </div>
                        <h4 class="service-card-title">Interior Collaborations</h4>
                        <p class="service-card-desc">Access vetted architects and interior designers to transform your
                            new apartment or villa into a functional, luxury home.</p>
                    </div>

                    <!-- 4. Legal Services -->
                    <div class="consumer-service-card">
                        <span class="service-step-num">04</span>
                        <div class="service-icon-box">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <h4 class="service-card-title">Legal & Documentation</h4>
                        <p class="service-card-desc">Complete builder agreement verification, title searches, and
                            registry assistance to keep your property investment safe.</p>
                    </div>

                    <!-- 5. Construction Services -->
                    <div class="consumer-service-card">
                        <span class="service-step-num">05</span>
                        <div class="service-icon-box">
                            <i class="fa-solid fa-person-digging"></i>
                        </div>
                        <h4 class="service-card-title">Turnkey Construction</h4>
                        <p class="service-card-desc">High-standard residential & commercial construction execution from
                            structural design to timely handover.</p>
                    </div>
                </div>

                <!-- Developer Services -->
                <div class="dev-services-heading">
                    <div class="section-header-wrap">
                        <span class="section-badge"><i class="fa-solid fa-city"></i> Developer Solutions</span>
                        <h2 class="section-title">Developer-Focused Services</h2>
                        <p class="section-subtitle">Strategic all-in-one sales, marketing, and planning management for
                            prime real estate projects.</p>
                    </div>

                    <div class="dev-services-grid">
                        <div class="dev-service-card">
                            <div class="dev-service-icon">
                                <i class="fa-solid fa-ruler-combined"></i>
                            </div>
                            <h4 class="dev-service-title">Project Planning</h4>
                            <p class="dev-service-desc">Strategic location viability, micro-market trends analysis, and
                                buyer persona mapping for new developments.</p>
                        </div>

                        <div class="dev-service-card">
                            <div class="dev-service-icon">
                                <i class="fa-solid fa-chart-simple"></i>
                            </div>
                            <h4 class="dev-service-title">Omnichannel Marketing</h4>
                            <p class="dev-service-desc">High-impact unified promotion across digital channels, social
                                media, and offline activations to drive high-intent leads.</p>
                        </div>

                        <div class="dev-service-card">
                            <div class="dev-service-icon">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h4 class="dev-service-title">Sales Management</h4>
                            <p class="dev-service-desc">End-to-end sales force management, CRM lead nurturing, and
                                fast-track closure support for high-volume inventory.</p>
                        </div>

                        <div class="dev-service-card">
                            <div class="dev-service-icon">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <h4 class="dev-service-title">Leasing Solutions</h4>
                            <p class="dev-service-desc">Institutional and retail tenant placement strategies designed to
                                achieve maximum occupancy and optimal rental yield.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         9. 360 KNOWLEDGE BASE (BLOGS)
         ========================================================================= -->
    @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)
        <section class="container blogSection" id="blogSection">
            <div class="blog-card-container">
                <div
                    class="section-header-wrap d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="section-badge"><i class="fa-solid fa-newspaper"></i> Market Insights</span>
                        <h2 class="section-title">Real Estate Market Insights & Investment Guide</h2>
                        <p class="section-subtitle">Expert insights on Noida price trends, Jewar Airport impact, 
						and Greater Noida West investment potential — updated regularly.</p>
                    </div>
                    <a href="{{ route('get.blogs') }}" class="btn-theme-outline">
                        <span>View All Articles</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>

                <div class="swiper mySwiperBlogs">
                    <div class="swiper-wrapper">
                        @foreach($pageData['blogs'] as $blog)
                            <div class="swiper-slide">
                                <a href="{{ route('blogs.details', $blog->slug) }}" class="project-card-link">
                                    <div class="blog-card">
                                        <div class="blog-card__img-wrap">
                                            <img src="{{ url('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}"
                                                loading="lazy" decoding="async">
                                        </div>
                                        <div class="projectDescp">
                                            <h3 class="title">{{ $blog->title }}</h3>
                                            <p class="blog-card__date">
                                                <i class="fa-solid fa-calendar-days text-secondary"></i>
                                                <span>{{ formatDate($blog->created_at) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next d-none" aria-label="Next project"></div>
                    <div class="swiper-button-prev d-none" aria-label="Previous project"></div>
                </div>
            </div>
        </section>
    @endif

    <!-- =========================================================================
         10. SOCIAL MEDIA UPDATES & COMMUNITY BUZZ
         ========================================================================= -->
    @php
        $feedItems = !empty($feeds['feed']['data']) ? $feeds['feed']['data'] : (!empty($feeds['data']) ? $feeds['data'] : []);
    @endphp

    @if(!empty($feedItems) && count($feedItems) > 0)
        <section class="container blogSection pt-0">
            <div class="blog-card-container">
                <div
                    class="section-header-wrap d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="section-badge badge-blue"><i class="fa-brands fa-facebook"></i> Social Updates</span>
                        <h2 class="section-title">Community Feeds & Social Updates</h2>
                        <p class="section-subtitle">Stay connected with on-ground construction progress, property showcases,
                            and community news.</p>
                    </div>
                    <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" class="btn-theme-outline">
                        <i class="fa-brands fa-facebook text-primary"></i>
                        <span>Follow on Facebook</span>
                    </a>
                </div>

                <div class="swiper mySwiperFeeds">
                    <div class="swiper-wrapper">
                        @foreach(array_slice($feedItems, 0, 8) as $feed)
                            @continue(empty($feed['full_picture']) && empty($feed['message']))
                            <div class="swiper-slide">
                                <div class="social-feed-card">
                                    <div class="social-feed-header">
                                        <div class="social-author">
                                            <img src="{{ url('frontend/360logo.webp') }}" class="social-avatar"
                                                alt="360 PropGuide">
                                            <div class="social-author-info">
                                                <h4 class="social-author-name">
                                                    360 PropGuide <i class="fa-solid fa-circle-check text-primary"></i>
                                                </h4>
                                                <span class="social-post-time">
                                                    <i class="fa-brands fa-facebook text-primary me-1"></i>
                                                    {{ !empty($feed['created_time']) ? date('d M Y', strtotime($feed['created_time'])) : 'Recent Post' }}
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ $feed['permalink_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer"
                                            class="social-ext-icon" title="View on Facebook">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>

                                    @if(!empty($feed['full_picture']))
                                        <a href="{{ $feed['permalink_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer"
                                            class="social-img-link">
                                            <div class="social-feed-img-wrap">
                                                <img src="{{ $feed['full_picture'] }}" alt="360 PropGuide Social Update"
                                                    loading="lazy" decoding="async" width="640" height="480"
                                                    onerror="this.onerror=null;this.src='{{ url('frontend/360logo.webp') }}';this.classList.add('is-fallback');">
                                            </div>
                                        </a>
                                    @endif

                                    @if(!empty($feed['message']))
                                        <div class="social-feed-body">
                                            <p class="social-feed-text">{{ \Illuminate\Support\Str::limit($feed['message'], 80) }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="social-feed-footer">
                                        <a href="{{ $feed['permalink_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer"
                                            class="social-view-link">
                                            <span>View on Facebook</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next d-none" aria-label="Next project"></div>
                    <div class="swiper-button-prev d-none" aria-label="Previous project"></div>
                </div>
            </div>
        </section>
    @endif

    <!-- =========================================================================
         11. INSTANT PROPERTY CONSULTATION CTA BANNER
         ========================================================================= -->
    <section class="container expert-consult-section">
        <div class="expert-consult-banner">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="section-badge"
                        style="background: rgba(255,255,255,0.15); color: #ffffff; border-color: rgba(255,255,255,0.25);">
                        <i class="fa-solid fa-headset text-warning"></i> Need Expert Advice?
                    </span>
                    <h3 class="consult-headline">Looking for the Best Property in Noida, Delhi NCR? Talk to Our Experts</h3>
                    <p class="consult-subtext">Confused which property suits your budget? Get free, unbiased advice and personalized project
					shortlists from our experts.</p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="consult-btn-group justify-content-lg-end">
                        <button type="button" class="btn btn-theme-primary" data-bs-toggle="modal"
                            data-bs-target="#contactModalPopup">
                            <i class="fa-solid fa-phone"></i>
                            <span>Request Free Callback</span>
                        </button>
                        <a href="https://wa.me/+919643020020" target="_blank" rel="noopener noreferrer"
                            class="btn-whatsapp-cta">
                            <i class="fa-brands fa-whatsapp fs-5"></i>
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endSection

@section('customJS')
<script defer src="{{ asset('frontend/js/home.js') }}?v={{ filemtime(public_path('frontend/js/home.js')) }}"></script>
@endSection
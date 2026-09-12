@extends('frontend.layouts.app')
@section('usesSweetAlert', true)
@section('og_image', storageUrl($projects->logo_image))
@section('title', $projects->seo_data['title'])
@section('keywords', $projects->seo_data['primary_keyword'] . ' ' . $projects->seo_data['secondary_keyword'])
@section('description', $projects->seo_data['meta_description'])
@section('canonical', url()->current())
@push('schema')
    @if (!empty($faqSchema))
        <script type="application/ld+json">{!! $faqSchema !!}</script>
    @endif
    <script type="application/ld+json">
                                                        {!! json_encode([
        "@context" => "https://schema.org",
        "@type" => "RealEstateListing",

        "name" => $projects->project_name,

        "title" => $projects->seo_data['title'] ?? $projects->project_name,

        "description" => strip_tags(
            $projects->seo_data['meta_description']
            ?? $projects->about_description
        ),

        "url" => url()->current(),

        "datePosted" => optional($projects->created_at)->format('Y-m-d'),

        "image" => storageUrl($projects->hero_images),

        "offers" => [
            "@type" => "AggregateOffer",
            "priceCurrency" => "INR",
            "lowPrice" => (string) $projects->price,
            "highPrice" => (string) ($projects->max_price ?: $projects->price),
            "availability" => "https://schema.org/InStock",
            "url" => url()->current()
        ],

        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $projects->location,
            "addressLocality" => $projects->location,
            "addressRegion" => "Uttar Pradesh",
            "addressCountry" => "IN"
        ],

        "identifier" => [
            "@type" => "PropertyValue",
            "name" => "RERA Registration",
            "value" => $projects->rera_no
        ],

        "brand" => [
            "@type" => "Brand",
            "name" => $projects->developer_name
        ],

        "seller" => [
            "@type" => "RealEstateAgent",
            "name" => "360PropGuide",
            "url" => "https://www.360propguide.com",
            "telephone" => "+91-9643020020"
        ]

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
                                                        </script>
@endpush


@section('customCSS')
    <link rel="stylesheet" href="{{ asset('frontend/css/details.css') }}" />
@endSection
@section('content')


    <!-- hero section -->
    <div class="project-HeroSection container pt-2">
        <div class="row hero-section" id="hero">
            <div class="col-md-6 d-none d-md-block p-1">
                <img class="w-100 h-100 object-fit-cover rounded-3 hero-main-img border border-1 border-light-subtle shadow-sm"
                    src="{{ storageUrl($projects->hero_images) }}" alt="{{ $projects->project_name }}" />
            </div>
            <div class="col-md-6 d-none d-md-block p-1">
                <div class="row h-100 g-2">
                    <div class="col-6 aspect2-1">
                        <img class="w-100 h-100 object-fit-cover rounded-3 hero-tile-img border border-1 border-light-subtle shadow-sm"
                            src="{{ storageUrl($projects->amenities_images) }}"
                            alt="{{ $projects->project_name }} - Amenities" />
                    </div>
                    <div class="col-6 aspect2-1">
                        <img class="w-100 h-100 object-fit-cover rounded-3 hero-tile-img border border-1 border-light-subtle shadow-sm"
                            src="{{ storageUrl($projects->feature_image) }}"
                            alt="{{ $projects->project_name }} - Features" />
                    </div>
                    <div class="col-6 aspect2-1">
                        <img class="w-100 h-100 object-fit-cover rounded-3 hero-tile-img border border-1 border-light-subtle shadow-sm"
                            src="{{ storageUrl($projects->logo_image) }}" alt="{{ $projects->project_name }} - Logo" />
                    </div>
                    <div class="col-6 aspect2-1">
                        <img class="w-100 h-100 object-fit-cover rounded-3 hero-tile-img border border-1 border-light-subtle shadow-sm"
                            src="{{ storageUrl($projects->developer_background_image) }}"
                            alt="{{ $projects->project_name }} - Developer Background" />
                    </div>
                </div>
            </div>
            <!-- Mobile View (Slider) -->
            <div class="col-12 d-block d-md-none">
                <div id="mobileProjectSlider"
                    class="carousel slide rounded-3 overflow-hidden border border-light-subtle shadow-sm"
                    data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="{{ storageUrl($projects->hero_images) }}"
                                alt="{{ $projects->project_name }}">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="{{ storageUrl($projects->amenities_images) }}"
                                alt="{{ $projects->project_name }}">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="{{ storageUrl($projects->feature_image) }}"
                                alt="{{ $projects->project_name }}">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100"src="{{ storageUrl($projects->logo_image) }}"
                                alt="{{ $projects->project_name }}">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="{{ storageUrl($projects->developer_background_image) }}"
                                alt="{{ $projects->project_name }}">
                        </div>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#mobileProjectSlider"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon custom-arrow" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mobileProjectSlider"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon custom-arrow" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Share Project Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h3 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Share Project</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex gap-4 justify-content-center align-items-center py-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('projects/' . $projects->slug)) }}"
                            target="__blank" class="text-primary">
                            <i class="fa-brands fa-facebook fs-1"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url('projects/' . $projects->slug)) }}"
                            target="__blank" class="text-primary">
                            <i class="fa-brands fa-linkedin fs-1"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode(url('projects/' . $projects->slug)) }}" target="__blank"
                            class="text-success">
                            <i class="fa-brands fa-square-whatsapp fs-1"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('projects/' . $projects->slug)) }}"
                            target="__blank" class="text-dark">
                            <i class="fa-brands fa-square-x-twitter fs-1"></i>
                        </a>
                        <a href="#"
                            onclick="copyToClipboard('{{ url('projects/' . $projects->slug) }}'); return false;"
                            title="Copy Link" class="text-secondary">
                            <i class="fa-solid fa-link fs-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="project-sticky-nav-wrapper" id="projectStickyNavWrapper">
        <nav class="project-sticky-navbar" aria-label="Project Sections Navigation">
            <div class="project-nav-container container">
                <ul class="project-nav-menu" id="navItemsContainer" role="tablist">
                    <div class="nav-underline-indicator" id="navUnderlineIndicator"></div>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container">
        <div class="row my-4 gx-lg-5 justify-content-between position-relative">
            <div class="col-lg-8 col-md-12 order-2 order-lg-1">
                <!-- Project Info Header Card -->
                <div
                    class="project-info-card card border border-light-subtle shadow-sm rounded-3 p-2 p-md-3 mb-4 bg-white">
                    <!-- Top Identity Section -->
                    <div class="mb-3">
                        <h1 class="h3 fw-bold text-dark mb-2">{{ $projects->project_name }}</h1>

                        <div class="d-flex align-items-center flex-wrap gap-3 text-secondary small mb-3">
                            @if (!empty($projects->developer_name))
                                <span>
                                    By <span class="fw-semibold text-primary">{{ $projects->developer_name }}</span>
                                </span>
                            @endif

                            @if (!empty($projects->location))
                                <span>
                                    <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $projects->location }}
                                </span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center flex-wrap gap-2">
                            @if (!empty($projects->project_status))
                                <span class="statusIcon">
                                    <i class="fa-solid fa-building"></i>
                                    {{ ucfirst(clean($projects->project_status)) }}
                                </span>
                            @endif

                            @if (!empty($projects->rera_no) && strtoupper(trim($projects->rera_no)) != 'N/A')
                                <span class="reraBadge">
                                    <i class="fa-solid fa-circle-check me-1"></i> RERA Approved
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Divider & Bottom Action Section -->
                    <div
                        class="pt-3 border-top border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                        @if ($projects->slug == 'prestige-bougainvillea-gardens')
                            <div>
                                <button type="button" class="btn customBtn orange rounded-3" data-bs-toggle="modal"
                                    data-bs-target="#contactModal">
                                    Price On Request
                                </button>
                            </div>
                        @else
                            <div>
                                <span class="text-muted small d-block">Starting Price</span>
                                <span class="fs-4 fw-bold text-dark mb-0">
                                    @if (!empty($projects->max_price) && $projects->price != $projects->max_price)
                                        ₹{{ formatPrice($projects->price) }} - ₹{{ formatPrice($projects->max_price) }}
                                    @else
                                        ₹{{ formatPrice($projects->price) }} Onwards
                                    @endif
                                </span>
                            </div>


                        @endif

                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a rel="noopener noreferrer" target="_blank" href="tel:+919643020020"
                                class="btn customBtn rounded-3 d-lg-none">Contact Sales</a>

                            <button type="button" class="btn customBtn rounded-3 px-4 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#contactModal">
                                Enquire Now
                            </button>

                            <button type="button" class="btn btn-outline-secondary rounded-3 px-3"
                                data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                                <i class="fa-solid fa-arrow-up-from-bracket me-1"></i><span>Share</span>
                            </button>
                        </div>
                    </div>
                    <div class="text-small fw-bold small d-block mt-2">
                        @if (!empty($projects->typology_string))
                            <span>
                                <i class="fa-solid fa-bed me-1 text-primary"></i>
                                {{ $projects->typology_string }}
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <!-- project overview -->
                    <div class="section project-section" id="overview">
                        <div class="project-details-card mb-4">
                            <h3 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }}<span
                                    class="text-primary"> Overview</span></h3>
                            <div class="row g-3 g-md-4">
                                <!-- Left Column -->
                                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                                    @if (!empty($projects->developer_name))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-user-tie"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Developer</span>
                                                <span
                                                    class="info-value d-block text-break">{{ $projects->developer_name }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($projects->property_size))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-ruler-combined"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Project Size</span>
                                                <span
                                                    class="info-value d-block text-break">{{ $projects->property_size }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($projects->typology_string))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-bed"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Configurations (BHK)</span>
                                                <span
                                                    class="info-value d-block text-break">{{ $projects->typology_string }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($projects->rera_no))
                                        <div class="project-info-row d-flex align-items-center position-relative">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-certificate"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">RERA Registration</span>
                                                <div class="info-value d-flex align-items-center flex-wrap gap-1 min-w-0">
                                                    <span class="rera-toggle rera-info text-break"
                                                        style="cursor:pointer;">
                                                        <i class="fa-solid fa-circle-info me-1 flex-shrink-0"></i>
                                                        {{ $projects->rera_no }}
                                                    </span>

                                                    <div class="rera-panel shadow" aria-hidden="true" role="dialog"
                                                        aria-label="RERA details">
                                                        <div class="rera-header">
                                                            <h3 class="fs-6 fw-bold mb-0 text-white">RERA Details</h3>
                                                            <span class="rera-close" role="button"
                                                                aria-label="Close">&times;</span>
                                                        </div>

                                                        <div class="rera-top-info">
                                                            <p>
                                                                <i class="fa fa-link"></i>
                                                                Source:
                                                                <a href="https://www.up-rera.in" target="_blank"
                                                                    rel="noopener noreferrer" class="normal-link">
                                                                    https://www.up-rera.in
                                                                </a>
                                                            </p>
                                                            <p>
                                                                <i class="fa fa-book"></i>
                                                                Please refer to the brochure for more bank details
                                                            </p>
                                                        </div>

                                                        <hr class="rera-divider">

                                                        <div class="rera-body">
                                                            @php
                                                                $rera_details = json_decode($projects->rera_data, true);
                                                            @endphp

                                                            @if (!empty($rera_details))
                                                                @foreach ($rera_details as $rera)
                                                                    <div class="rera-grid">
                                                                        <div class="rera-left">
                                                                            <p class="rera-label">REGISTERED</p>
                                                                            <p class="rera-phase">
                                                                                {{ !empty($rera['phase']) ? $rera['phase'] : '' }}
                                                                            </p>
                                                                            <p class="rera-no">
                                                                                {{ !empty($rera['rera_no']) ? $rera['rera_no'] : $projects->rera_no }}
                                                                            </p>
                                                                        </div>
                                                                        @if (!empty($rera['qr_image']))
                                                                            <div class="rera-right">
                                                                                <img src="{{ storageUrl($rera['qr_image']) }}"
                                                                                    alt="RERA QR Code">
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <hr class="rera-divider">
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right Column -->
                                <div class="col-12 col-md-6 d-flex flex-column gap-3">
                                    @if (!empty($projects->location))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-location-dot"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Location</span>
                                                <span
                                                    class="info-value d-block text-break">{{ $projects->location }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($projects->project_status))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-building"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0 text-capitalize">
                                                <span class="info-label d-block mb-1">Project Status</span>
                                                <span
                                                    class="info-value d-block text-break">{{ clean($projects->project_status) }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($projects->launch_date))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Launch Date</span>
                                                <span
                                                    class="info-value d-block text-break">{{ $projects->launch_date }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (request()->path() != 'projects/prestige-bougainvillea-gardens' && !empty($projects->price))
                                        <div class="project-info-row d-flex align-items-center">
                                            <div
                                                class="info-icon-box flex-shrink-0 d-flex align-items-center justify-content-center me-3">
                                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                            </div>
                                            <div class="info-text-box flex-grow-1 min-w-0">
                                                <span class="info-label d-block mb-1">Starting Price</span>
                                                <span class="info-value d-block text-break">
                                                    @if (!empty($projects->max_price) && $projects->price != $projects->max_price)
                                                        ₹{{ formatPrice($projects->price) }} -
                                                        ₹{{ formatPrice($projects->max_price) }}
                                                    @else
                                                        ₹{{ formatPrice($projects->price) }} Onwards*
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- summary -->
                    <div class="section project-section" id="summary">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">
                                {{ $projects->project_name }}<span class="text-primary"> About</span>
                            </h2>
                            <div class="key-insights-content">{!! $projects->about_description !!}</div>
                            @if (!empty($projects->youtube_links))
                                <div class="mt-3">
                                    <a href="{{ $projects->youtube_links }}" target="_blank" rel="noopener"
                                        class="youtube-link">
                                        <span class="youtube-icon"></span>
                                        Explore the Project in Action <i class="fab fa-youtube"
                                            style="color: #FF0000;"></i> Watch Now
                                        on YouTube!
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- key insights -->
                    <div class="section project-section" id="key">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-3 fw-bold text-dark">{{ $projects->project_name }}<span
                                    class="text-primary"> Key Insights</span></h2>
                            @if (!empty($projects->key_insights))
                                <div class="key-insights-content text-secondary">
                                    {!! $projects->key_insights !!}
                                </div>
                            @endif
                        </div>
                    </div>



                    <!-- price -->
                    <!--<div class="price section project-section" id="price">
             @if (!empty($projects->sqft_price))
    <div class="py-5">
       <div class="project-box mt-4" id="project_{{ $projects->id }}"
        data-floorplans='{{ $projects->floor_plans_data }}'
        data-sqftprice='{{ $projects->sqft_price }}'>
       </div>

       <div class="floorplan-tabs d-flex flex-wrap gap-2 mt-3" id="floor_tabs_{{ $projects->id }}"></div>

       <div class="row text-center mt-5">
        <div class="col-6 col-md-6 mb-3">
         <div class="price-box">
          <div class="label text-primary">BSP</div>
          <div class="value" id="bsp_value_{{ $projects->id }}">--</div>
         </div>
        </div>
        <div class="col-6 col-md-6 mb-3">
         <div class="price-box">
          <div class="label text-primary">Total Value</div>
          <div class="value" id="total_value_{{ $projects->id }}">--</div>
         </div>
        </div>
       </div>
       </div>
    @endif

            </div>-->
                    @if (request()->path() != 'projects/prestige-bougainvillea-gardens')
                        @php
                            $floorPlans = json_decode($projects->floor_plans_data, true) ?? [];
                            $sqftPrices = json_decode($projects->sqft_price, true) ?? [];

                            $priceValue = isset($sqftPrices[0]['value']) ? (float) $sqftPrices[0]['value'] : 0;

                            $firstPlan = $floorPlans[0] ?? null;
                            $superArea = isset($firstPlan['super_area']) ? (float) $firstPlan['super_area'] : 0;

                            $totalValue = $superArea * $priceValue;
                            $badgeColors = ['badge-blue', 'badge-purple', 'badge-green', 'badge-amber'];
                        @endphp

                        @if (!empty($floorPlans) || !empty($projects->sqft_price) || !empty($projects->price))
                            <div class="price section project-section" id="price">
                                <div class="project-details-card mb-4">
                                    <h3 class="h4 mb-4 fw-bold text-dark">
                                        {{ $projects->project_name }} <span class="text-primary">Pricing & Plans</span>
                                    </h3>

                                    {{-- Hidden data for JS --}}
                                    <div class="project-box" id="project_{{ $projects->id }}"
                                        data-floorplans='{{ $projects->floor_plans_data }}'
                                        data-sqftprice='{{ $projects->sqft_price }}'>
                                    </div>

                                    {{-- Desktop Table View --}}
                                    <div class="unit-table-container d-none d-md-block mb-4"
                                        style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <table class="unit-config-table mb-0 w-100">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 180px;">Unit Configuration</th>
                                                    <th style="min-width: 130px;">Area (sq.ft)</th>
                                                    <th style="min-width: 150px;">Starting Price</th>
                                                    <th style="min-width: 120px; text-align: right;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($floorPlans as $index => $plan)
                                                    @php
                                                        $planTitle = !empty($plan['title'])
                                                            ? $plan['title']
                                                            : 'Configuration ' . ($index + 1);
                                                        $planArea = !empty($plan['super_area'])
                                                            ? $plan['super_area'] . ' sq.ft'
                                                            : (!empty($plan['carpet_area'])
                                                                ? $plan['carpet_area'] . ' sq.ft'
                                                                : '—');

                                                        if (!empty($plan['price'])) {
                                                            $planPrice = $plan['price'];
                                                        } elseif ($priceValue && !empty($plan['super_area'])) {
                                                            $calcVal = ($plan['super_area'] * $priceValue) / 10000000;
                                                            $planPrice = '₹' . number_format($calcVal, 2) . ' Cr';
                                                        } elseif (!empty($projects->price)) {
                                                            $planPrice =
                                                                '₹' . formatPrice($projects->price) . ' Onwards*';
                                                        } else {
                                                            $planPrice = '—';
                                                        }

                                                        $badgeClass = $badgeColors[$index % count($badgeColors)];
                                                    @endphp
                                                    <tr class="floor-btn clickable-row {{ $index == 0 ? 'active' : '' }}"
                                                        data-plan-index="{{ $index }}"
                                                        data-super-area="{{ $plan['super_area'] ?? 0 }}"
                                                        data-plan-price="{{ $planPrice }}">
                                                        <td>
                                                            <span class="unit-type-badge {{ $badgeClass }}">
                                                                {{ $planTitle }}
                                                            </span>
                                                        </td>
                                                        <td class="fw-semibold text-dark">{{ $planArea }}</td>
                                                        <td class="price-text-bold text-primary">{{ $planPrice }}</td>
                                                        <td class="text-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold fs-13"
                                                                data-bs-toggle="modal" data-bs-target="#contactModal">
                                                                Get Quote
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Mobile Card View --}}
                                    <div class="mobile-unit-list d-md-none mb-4">
                                        @foreach ($floorPlans as $index => $plan)
                                            @php
                                                $planTitle = !empty($plan['title'])
                                                    ? $plan['title']
                                                    : 'Configuration ' . ($index + 1);
                                                $planArea = !empty($plan['super_area'])
                                                    ? $plan['super_area'] . ' sq.ft'
                                                    : (!empty($plan['carpet_area'])
                                                        ? $plan['carpet_area'] . ' sq.ft'
                                                        : '—');

                                                if (!empty($plan['price'])) {
                                                    $planPrice = $plan['price'];
                                                } elseif ($priceValue && !empty($plan['super_area'])) {
                                                    $calcVal = ($plan['super_area'] * $priceValue) / 10000000;
                                                    $planPrice = '₹' . number_format($calcVal, 2) . ' Cr';
                                                } elseif (!empty($projects->price)) {
                                                    $planPrice = '₹' . formatPrice($projects->price) . ' Onwards*';
                                                } else {
                                                    $planPrice = '—';
                                                }

                                                $badgeClass = $badgeColors[$index % count($badgeColors)];
                                            @endphp
                                            <div class="mobile-unit-card floor-btn clickable-row {{ $index == 0 ? 'active' : '' }}"
                                                data-plan-index="{{ $index }}"
                                                data-super-area="{{ $plan['super_area'] ?? 0 }}"
                                                data-plan-price="{{ $planPrice }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span
                                                        class="unit-type-badge {{ $badgeClass }}">{{ $planTitle }}</span>
                                                    <span class="price-text-bold text-primary">{{ $planPrice }}</span>
                                                </div>
                                                <div
                                                    class="card-row-item d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                                                    <span class="item-label text-muted fs-13">Area</span>
                                                    <span
                                                        class="item-value fw-semibold text-dark fs-13">{{ $planArea }}</span>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary w-100 rounded-2 py-2 fw-semibold fs-13"
                                                        data-bs-toggle="modal" data-bs-target="#contactModal">
                                                        Get Exact Quote
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Indicative Disclaimer --}}
                                    <p class="unit-disclaimer mb-4 fs-12 text-muted fst-italic">
                                        * Indicative prices. Final rates subject to floor, facing & payment plan. Contact
                                        sales for exact quote.
                                    </p>

                                    {{-- Integrated Pricing Highlights --}}
                                    <div class="stat-highlight-wrapper pt-3 border-top">
                                        <div class="stat-label mb-3 fw-bold text-uppercase fs-12 tracking-wide">
                                            <i class="fa-solid fa-chart-line me-1 text-primary"></i> Pricing Highlights
                                        </div>
                                        <div class="row text-center g-3">
                                            <div class="col-6">
                                                <div class="stat-highlight-box p-3 rounded-3 
                                                border bg-light">
                                                    <div class="stat-label mb-1 text-muted fs-12">BSP</div>
                                                    <div class="stat-value text-primary fw-bold fs-5"
                                                        id="bsp_value_{{ $projects->id }}">
                                                        @if ($priceValue)
                                                            ₹{{ number_format($priceValue, 0) }}/sq.ft
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
    <div class="stat-highlight-box p-3 rounded-3 border bg-light">
        <div class="stat-label mb-1 text-muted fs-12">
            Starting From
        </div>

        <div class="stat-value text-dark fw-bold fs-5"
            id="total_value_{{ $projects->id }}">

            @php
                $lowestFloorPlan = collect($floorPlans)
                    ->filter(function ($plan) use ($priceValue) {
                        return !empty($plan['super_area']) && $priceValue;
                    })
                    ->sortBy(function ($plan) use ($priceValue) {
                        return (float) $plan['super_area'] * (float) $priceValue;
                    })
                    ->first();

                $lowestFloorPlanPrice = null;

                if (
                    $lowestFloorPlan &&
                    !empty($lowestFloorPlan['super_area']) &&
                    $priceValue
                ) {
                    $lowestFloorPlanPrice =
                        (float) $lowestFloorPlan['super_area'] * (float) $priceValue;
                }
            @endphp

            @if ($lowestFloorPlanPrice)
                ₹{{ number_format($lowestFloorPlanPrice / 10000000, 2) }} Cr

            @elseif($priceValue && $superArea)
                ₹{{ number_format($totalValue / 10000000, 2) }} Cr

            @elseif(!empty($projects->price))
                ₹{{ formatPrice($projects->price) }} Onwards*

            @else
                --
            @endif

        </div>
    </div>
</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endif
                    <!-- location -->
                    <div class="section project-section" id="location">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Location</span></h2>
                            @if (!empty($projects->location_video))
                                <div class="mb-3 rounded-3 overflow-hidden">
                                    <video src="{{ storageUrl($projects->location_video) }}" class="w-100" autoplay
                                        muted controls></video>
                                </div>
                            @endif
                            @if (!empty($projects->location_description))
                                <div class="key-insights-content">
                                    {!! $projects->location_description !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (!empty($projects->site_plans_images))
                        <div class="section project-section" id="site-plan">
                            <div class="project-details-card mb-4">
                                <h3 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                        class="text-primary">Site Plan</span></h3>
                                <div class="siteplan-card shadow-sm">
                                    <div class="siteplan-img-wrapper">
                                        <img class="img-fluid siteplan-image d-block w-100"
                                            src="{{ storageUrl($projects->site_plans_images) }}"
                                            alt="{{ $projects->project_name }} Site Plan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Row for Sanctioned Map & Lease Deed -->
                    <div class="section project-section" id="legal-status">
                        <div class="project-details-card mb-4">
                            <h3 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Legal Status</span></h3>
                            <div class="row g-3 align-items-center">
                                <!-- Sanctioned Map -->
                                <div class="col-6 col-md-6">
                                    <button type="button" class="btn customBtn w-100 download-btn rounded-3 p-2"
                                        data-bs-toggle="modal" data-bs-target="#contactModal"
                                        data-project-id="{{ $projects->id }}" data-type="sanctioned_map">
                                        <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Sanctioned Map
                                    </button>
                                </div>
                                <!-- Lease Deed -->
                                <div class="col-6 col-md-6 text-md-end">
                                    <button type="button"
                                        class="btn customBtn w-100 w-md-auto download-btn rounded-3 p-2"
                                        data-bs-toggle="modal" data-bs-target="#contactModal"
                                        data-project-id="{{ $projects->id }}" data-type="lease_deed">
                                        <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Lease Deed
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- siteplan and floor plan -->
                    <div class="section project-section" id="floor">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Floor Plan</span></h2>
                            <div class="floorPlans">
                                <div thumbsSlider="" class="swiper mySwiper mb-3">
                                    <div class="swiper-wrapper">
                                        @php
                                            $floorPlans = json_decode($projects->floor_plans_data);
                                        @endphp
                                        @if (!empty($floorPlans) && count($floorPlans) > 0)
                                            @foreach ($floorPlans as $index => $floorPlan)
                                                <div class="swiper-slide">
                                                    <div class="floorSelect">
                                                        <div class="card text-center p-2 px-4 text-primary">
                                                            {{ $floorPlan->title }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff;"
                                    class="swiper floorSwiper mb-3">
                                    <div class="swiper-wrapper">
                                        @php
                                            $floorPlans = json_decode($projects->floor_plans_data, true);
                                        @endphp
                                        @if (!empty($floorPlans) && count($floorPlans) > 0)
                                            @foreach ($floorPlans as $index => $floorPlan)
                                                <div class="swiper-slide flex-column">
                                                    <img class="col-12 col-md-10 mx-auto"
                                                        alt="{{ $projects->project_name }}"
                                                        src="{{ storageUrl($floorPlan['image'] ?? ($floorPlan['feature_image'] ?? null)) }}" />
                                                    <div class="row m-0 w-100 align-items-center">
                                                        @if (@$floorPlan['super_area'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        Super area:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0 fw-bold">
                                                                            {{ $floorPlan['super_area'] }} sq. ft.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (@$floorPlan['carpet_area'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        Carpet area:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['carpet_area'] }}
                                                                            sq. ft.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (@$floorPlan['built_area'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        BuiltUp area:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['built_area'] }}
                                                                            sq. ft.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (@$floorPlan['balcony_area'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        Balcony area:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['balcony_area'] }}
                                                                            sq. ft.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (@$floorPlan['length'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        Length:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['length'] }} m</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (@$floorPlan['width'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">
                                                                        Width:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['width'] }} m</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (@$floorPlan['total_area'] != 0)
                                                            <div class="col-md-3 col-6">
                                                                <div class="">
                                                                    <p class="m-0 text-center py-2 fs-4 text-primary">Total
                                                                        area:</p>
                                                                    <div class="fw-bold text-black">
                                                                        <p class="m-0">{{ $floorPlan['total_area'] }}
                                                                            sq. yds</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section project-section" id="brochure">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Brochure & Price List</span></h2>
                            <div class="row g-3 align-items-center">
                                <div class="col-6 col-md-6">
                                    <button type="button" class="btn customBtn w-100 download-btn rounded-3 p-2"
                                        data-bs-toggle="modal" data-bs-target="#contactModal"
                                        data-project-id="{{ $projects->id }}" data-type="brochure">
                                        <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Brochure
                                    </button>
                                </div>
                                <div class="col-6 col-md-6 text-md-end">
                                    <button type="button"
                                        class="btn customBtn w-100 w-md-auto download-btn rounded-3 p-2"
                                        data-bs-toggle="modal" data-bs-target="#contactModal"
                                        data-project-id="{{ $projects->id }}" data-type="price_list">
                                        <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Price List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- possessions -->
                    <div class="possessions section project-section" id="possession">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Possessions</span></h2>
                            <div class="key-insights-content">{!! $projects->possession_description !!}</div>
                        </div>
                    </div>

                    <!-- amenities -->
                    <div class="amenities section project-section" id="amenities">
                        <div class="project-details-card mb-4">
                            <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                    class="text-primary">Amenities</span></h2>
                            <div class="row g-3">
                                @if (!empty($projects->amenitiesDetails) && count($projects->amenitiesDetails) > 0)
                                    @foreach ($projects->amenitiesDetails as $index => $amenity)
                                        <div class="icon col-md-2 col-4 ms-3">
                                            <img src="{{ storageUrl($amenity->image) }}"
                                                alt="{{ '360_propguide' . $amenity->name }}" />
                                            <span>{{ $amenity->name }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    @if (!empty($projects->site_plans_description))
                        <!-- price details -->
                        <div class="price section project-section" id="price-description">
                            <div class="project-details-card mb-4">
                                <h2 class="h4 mb-4 fw-bold text-dark">{{ $projects->project_name }} <span
                                        class="text-primary">Pricing Details</span></h2>
                                <div class="key-insights-content">{!! $projects->site_plans_description !!}</div>
                            </div>
                        </div>
                    @endif

                    <!-- dev backgrounds -->
                    <div class="possessions section project-section" id="developer">
                        <div class="project-details-card mb-4">
                            <h3 class="h4 mb-4 fw-bold text-dark">Developer <span class="text-primary">Background</span>
                            </h3>
                            <div class="row align-items-md-center mb-3">
                                @if (!empty($projects->developerDetails))
                                    @foreach ($projects->developerDetails as $developer)
                                        <div class="col-12 col-md-3 d-none">
                                            <img src="{{ storageUrl($developer->developer_logo) }}"
                                                class="col-6 mx-auto col-md-12 d-none"
                                                alt="{{ $projects->project_name }}" />
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <div class="text-center py-2 text-primary">
                                                Experience:
                                                <div class="fw-bold text-black">{{ $developer->developer_experience }} +
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <div class="text-center py-2 text-primary">
                                                Ongoing Projects:
                                                <div class="fw-bold text-black">{{ $developer->ongoing_project }} </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <div class="text-center py-2 text-primary">
                                                Completed Projects:
                                                <div class="fw-bold text-black">{{ $developer->completed_projects }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="key-insights-content">{!! $projects->developer_background_dscp !!}</div>
                        </div>
                    </div>

                    <div class="section mb-4 project-section" id="faq" itemscope
                        itemtype="https://schema.org/FAQPage">
                        <h3 class="h4 my-4 fw-bold text-dark">
                            Frequently Asked <span class="text-primary">Questions</span>
                        </h3>

                        @php
                            $faqs = json_decode($projects->faqs_data, true);
                        @endphp

                        @if (!empty($faqs) && count($faqs) > 0)
                            <div class="accordion custom-faq-accordion" id="projectFaqAccordion">
                                @foreach ($faqs as $index => $faq)
                                    @if (!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')))
                                        @php
                                            $isOpen = $index == 0;
                                        @endphp
                                        <div class="accordion-item faq-item-card mb-3 border rounded-3 overflow-hidden"
                                            itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <h3 class="accordion-header" id="faqHeading{{ $index }}">
                                                <button
                                                    class="accordion-button faq-btn px-4 py-3 fw-bold text-dark {{ $isOpen ? '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#faqCollapse{{ $index }}"
                                                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                                    aria-controls="faqCollapse{{ $index }}">
                                                    <span class="faq-question-text flex-grow-1 pe-3"
                                                        itemprop="name">{{ trim($faq['question']) }}</span>
                                                    <span class="faq-icon-toggle"></span>
                                                </button>
                                            </h3>
                                            <div id="faqCollapse{{ $index }}"
                                                class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}"
                                                aria-labelledby="faqHeading{{ $index }}"
                                                data-bs-parent="#projectFaqAccordion" itemscope itemprop="acceptedAnswer"
                                                itemtype="https://schema.org/Answer">
                                                <div class="accordion-body faq-answer-text px-4 pb-4 pt-0 text-secondary"
                                                    itemprop="text">
                                                    {!! $faq['answer'] !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="d-block d-lg-none ppc-form col-md-8 mx-auto col-lg-12">
                        <div class="row p-4">
                            <h3 class="h6 text-center mb-3 fw-bold">You can Count on us for Great Deals!</h3>
                            <div class="alert alert-success success-message d-none">
                                Your enquiry has been submitted successfully.
                            </div>
                            <form method="POST" class="popupForm" action="{{ route('contact-mail') }}">
                                @csrf
                                <input type="hidden" name="formName" value="popup" />
                                <div class="form-group input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control error commonerr" name="name"
                                        placeholder="Name" />
                                </div>
                                <span class="text-danger error-name"></span>
                                <div class="form-group input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" placeholder="Email Address*" name="email"
                                        class="form-control" />
                                </div>
                                <span class="text-danger error-email"></span>
                                <div class="form-group input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" name="mobile" placeholder="Mobile*"
                                        maxlength="10" pattern="[0-9]{10}" inputmode="numeric" />
                                </div>
                                <span class="text-danger error-mobile"></span>
                                <div class="form-group">
                                    <textarea type="text" placeholder="Message" name="message" class="form-control" row="5" col="1"></textarea>
                                </div>
                                <div class="g-recaptcha mb-3"
                                    data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                </div>
                                <span class="text-danger error-recaptcha"></span>
                                @if ($errors->has('recaptchaform5'))
                                    <div class="alert alert-danger">
                                        {{ $errors->first('recaptchaform5') }}
                                    </div>
                                @endif
                                <div class="w-full"> <button type="submit"
                                        class="btn orange text-white mb-2 w-100 submitButton">Submit</button> </div>
                                <div class="w-full d-flex justify-content-between gap-2">

                                    <!-- WhatsApp Button -->
                                    <a href="https://wa.me/919643020020?text={{ urlencode('I want brochure of ' . $projects->project_name) }}"
                                        target="_blank" id="whatsapp-btn-4"
                                        class="btn whatsapp text-white w-50 d-flex justify-content-center align-items-center">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>

                                    <a href="tel:919643020020"
                                        class="btn orange text-white w-50 d-flex justify-content-center align-items-center">
                                        <i class="fas fa-phone me-2"></i> Call
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-md-10 order-1 order-lg-2 mx-auto me-lg-0 ms-lg-auto" id="sidebar-wrapper">

                {{-- Desktop Quick Enquiry Form --}}
                <div class="d-none d-lg-block ppc-form col-md-8 mx-auto col-lg-12 mb-4" id="sidebar-enquiry-form">
                    <div class="p-4 pt-3">
                        <div class="text-center mb-3">
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 rounded-pill mb-2"
                                style="font-size: 0.75rem;">QUICK ENQUIRY</span>
                            <h3 class="h5 fw-bold text-dark mb-1">Get Best Pricing & Deals</h3>
                            <p class="text-muted small mb-0">Direct developer quote & instant callback</p>
                        </div>

                        <div class="alert alert-success success-message d-none">
                            Your enquiry has been submitted successfully.
                        </div>

                        <form method="POST" class="popupForm" action="{{ route('contact-mail') }}">
                            @csrf
                            <input type="hidden" name="formName" value="popup">

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control error commonerr name" name="name"
                                        placeholder="Full Name*" required />
                                </div>
                                <span class="text-danger error-name small"></span>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" placeholder="Email Address*" name="email"
                                        class="form-control email" required />
                                </div>
                                <span class="text-danger error-email small"></span>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control mobile" name="mobile"
                                        placeholder="Mobile Number*" minlength="10" maxlength="10" pattern="[0-9]{10}"
                                        inputmode="numeric" required />
                                </div>
                                <span class="text-danger error-mobile small"></span>
                            </div>

                            <div class="form-group">
                                <textarea placeholder="Message (Optional)" name="message" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="g-recaptcha mb-3 mt-2"
                                data-sitekey="{{ config('services.recaptcha.site_key') }}">
                            </div>
                            <span class="text-danger error-recaptcha small"></span>
                            @if ($errors->has('recaptchaform3'))
                                <div class="alert alert-danger p-2 small">
                                    {{ $errors->first('recaptchaform3') }}
                                </div>
                            @endif

                            <button type="submit" class="btn customBtn w-100 mb-3 py-2 fw-semibold submitButton">
                                <i class="fa-solid fa-paper-plane me-2"></i> Request Callback
                            </button>

                            <div class="d-flex justify-content-between gap-2">
                                <a href="https://wa.me/919643020020?text={{ urlencode('I want brochure of ' . $projects->project_name) }}"
                                    target="_blank" id="whatsapp-btn-3"
                                    class="btn whatsapp-btn text-white w-50 py-2 d-flex justify-content-center align-items-center">
                                    <i class="fab fa-whatsapp me-2 fs-6"></i> WhatsApp
                                </a>

                                <a href="tel:919643020020"
                                    class="btn call-btn text-white w-50 py-2 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-phone me-2"></i> Call Now
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 p-4 d-none d-lg-block mt-0 sticky-recommended-card"
                    id="sidebar-recommended-projects" style="border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h3 class="h5 mb-0 fw-bold text-dark">Recommended <span class="text-primary">Projects</span></h3>
                        <i class="fa-solid fa-fire text-danger opacity-75 fs-6"></i>
                    </div>

                    @foreach ($recommendedProjects as $recommended)
                        <a class="col-12 d-block text-decoration-none mb-3"
                            href="{{ route('projects.details', $recommended->slug) }}">
                            <div class="recommended-card-wrapper d-flex align-items-stretch">
                                <div class="col-5 img-box position-relative">
                                    <img alt="{{ $recommended->project_name }}" loading="lazy"
                                        class="h-100 w-100 object-fit-cover"
                                        src="{{ storageUrl($recommended->logo_image) }}" />
                                </div>
                                <div class="p-3 col-7 d-flex flex-column justify-content-center min-w-0">
                                    <h3 class="recommended-title text-dark fw-bold mb-1 text-truncate"
                                        style="font-size: 0.92rem;">
                                        {{ $recommended->project_name }}
                                    </h3>
                                    @if (!empty($recommended->typology))
                                        <div class="mb-1">
                                            <span class="badge bg-light text-secondary border fw-normal"
                                                style="font-size: 0.7rem;">
                                                {{ $recommended->typology }}
                                            </span>
                                        </div>
                                    @endif
                                    @if (!empty($recommended->location))
                                        <p class="text-muted small mb-1 text-truncate" style="font-size: 0.78rem;">
                                            <i
                                                class="fa-solid fa-location-dot me-1 text-primary"></i>{{ $recommended->location }}
                                        </p>
                                    @endif
                                    <p class="fw-bold text-primary mb-0" style="font-size: 0.9rem;">
                                        ₹{{ formatPrice($recommended->price) }} Onwards*
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
        <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog popupFormHome">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <img src="{{ asset('frontend/360logo.png') }}" alt="360propguide" class="w-50 mx-auto">

                        <button type="button" class="btn-close align-self-start ms-0 shadow-none"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <h3 class="h6 text-center mb-3 fw-bold">You can Count on us for Great Deals!</h3>
                    <div class="modal-body">

                        <form method="POST" id="popupFormDownload" action="{{ route('popup.download') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="project_id" value="{{ $projects->id }}">
                            <input type="hidden" name="download_type" id="downloadType">
                            <div class="mb-3">

                                <input type="text" class="form-control shadow-none" name="name"
                                    placeholder="Name*">
                                <span class="text-danger error-name"></span>
                            </div>
                            <div class="mb-3">

                                <input type="tel" class="form-control shadow-none" name="mobile"
                                    placeholder="Mobile*">
                                <span class="text-danger error-mobile"></span>
                            </div>
                            <div class="mb-3">

                                <input type="email" class="form-control shadow-none " name="email"
                                    placeholder="Email*">
                                <span class="text-danger error-email"></span>
                            </div>
                            <div class="mb-3">

                                <textarea name="message" class="form-control shadow-none" placeholder="Message"></textarea>

                            </div>
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                </div>
                                <span class="text-danger error-recaptcha"></span>
                            </div>
                            @if ($errors->has('recaptchaform2'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('recaptchaform2') }}
                                </div>
                            @endif
                            <div class="text-center pt-3 mb-3">
                                <button type="submit" class="btn customBtn w-100 submitButton">
                                    Submit
                                </button>
                            </div>

                        </form>
                        <div class="w-full d-flex justify-content-between gap-2">
                            <!-- Submit Button (left) -->

                            <a href="tel:+919643020020" class="btn customBtn orange text-white w-50"><i
                                    class="fas fa-phone me-2"></i>Call Us</a>

                            <!-- WhatsApp Button (right) -->
                            <a href="https://wa.me/919643020020?text={{ urlencode('I want brochure of ' . $projects->project_name) }}"
                                target="_blank"
                                class="whatsapp text-white w-50 d-flex justify-content-center align-items-center"
                                id="whatsapp-btn-2">
                                <i class="fab fa-whatsapp me-2"></i> WhatsApp
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endSection
@section('customJS')

    <script>
        $(document).on('click', '.download-btn', function() {
            let type = $(this).data('type');
            let projectId = $(this).data('project-id');

            $('#downloadType').val(type);
            $('input[name="project_id"]').val(projectId);
        });

        $(document).on("submit", "#popupFormDownload", function(e) {
            e.preventDefault();

            let form = $(this);
            form.find(".error-name, .error-email, .error-mobile, .error-recaptcha").text('');

            let name = (form.find('[name="name"]').val() || "").trim();
            let email = (form.find('[name="email"]').val() || "").trim();
            let mobile = (form.find('[name="mobile"]').val() || "").trim();
            let recaptcha = (form.find('[name="g-recaptcha-response"]').val() || "").trim();

            let isValid = true;

            if (name === "") {
                form.find(".error-name").text("Name is required.");
                isValid = false;
            }

            if (email === "") {
                form.find(".error-email").text("Email is required.");
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                form.find(".error-email").text("Invalid email format.");
                isValid = false;
            }

            if (mobile === "") {
                form.find(".error-mobile").text("Mobile number is required.");
                isValid = false;
            } else if (!/^\d{10}$/.test(mobile)) {
                form.find(".error-mobile").text("Enter a valid 10-digit mobile number.");
                isValid = false;
            }

            if (form.find('[name="g-recaptcha-response"]').length > 0 && recaptcha === "") {
                form.find(".error-recaptcha").text("Please validate Recaptcha");
                isValid = false;
            }

            if (!isValid) return;

            let submitBtn = form.find('.submitButton');
            submitBtn.prop('disabled', true).html('<div class="loader"></div>');

            $.ajax({
                url: "{{ route('popup.download') }}",
                type: "POST",
                data: form.serialize(),
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    submitBtn.prop("disabled", false).text("Submit");

                    // Redirect to thankyou page with session
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    }
                },
                error: function(xhr) {
                    submitBtn.prop("disabled", false).text("Submit");

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        form.find(".error-name").text(errors?.name ?? '');
                        form.find(".error-mobile").text(errors?.mobile ?? '');
                        form.find(".error-email").text(errors?.email ?? '');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong. Please try again later.',
                            confirmButtonColor: '#ff6600'
                        });
                    }
                }
            });
        });
    </script>

    <script>
        let swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: "auto",
            freeMode: true,
            watchSlidesProgress: true,
            centeredSlides: true,
        });
        let swiper2 = new Swiper(".floorSwiper", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
        swiper2.on("slideChange", function() {
            let activeIndex = swiper2.activeIndex;
            swiper.slideTo(activeIndex); // shift thumbs to keep active one visible
        });

        // Dynamic 99acres-Style Sticky Section Navigation with Butter-Smooth 60FPS Scroll Spy
        document.addEventListener('DOMContentLoaded', function() {
            const navContainer = document.getElementById('navItemsContainer');
            const navWrapper = document.getElementById('projectStickyNavWrapper');
            if (!navContainer) return;

            const sectionTitleMap = {
                'overview': 'Overview',
                'key': 'Key Insights',
                'summary': 'About',
                'price': 'Pricing & Plans',
                'location': 'Location',
                'floor': 'Floor Plans',
                'amenities': 'Amenities',
                'developer': 'Developer',
                'faq': 'FAQ'
            };

            // Scan page for primary project sections
            const sections = Array.from(document.querySelectorAll('.project-section[id]')).filter(sec => {
                return (sec.id in sectionTitleMap) && (sec.offsetWidth > 0 || sec.offsetHeight > 0 || sec
                    .getClientRects().length > 0);
            });

            if (sections.length === 0) {
                if (navWrapper) navWrapper.style.display = 'none';
                return;
            }

            // Clean & rebuild navigation items
            navContainer.querySelectorAll('.nav-menu-item').forEach(item => item.remove());

            let underline = document.getElementById('navUnderlineIndicator');
            if (!underline) {
                underline = document.createElement('div');
                underline.className = 'nav-underline-indicator';
                underline.id = 'navUnderlineIndicator';
                navContainer.appendChild(underline);
            }

            sections.forEach((sec, idx) => {
                const id = sec.id;
                const title = sectionTitleMap[id] || (sec.querySelector('h2, h3')?.textContent?.trim() ||
                    id);

                const li = document.createElement('li');
                li.className = 'nav-menu-item';

                const a = document.createElement('a');
                a.href = '#' + id;
                a.className = 'nav-menu-link' + (idx === 0 ? ' active' : '');
                a.setAttribute('data-target', id);
                a.setAttribute('role', 'tab');
                a.setAttribute('aria-selected', idx === 0 ? 'true' : 'false');
                a.textContent = title;

                li.appendChild(a);
                navContainer.appendChild(li);
            });

            const navLinks = navContainer.querySelectorAll('.nav-menu-link');

            // Pixel-perfect single underline positioning with requestAnimationFrame batching
            let rAFId = null;

            function moveUnderline(link) {
                if (!underline || !link) return;
                const containerRect = navContainer.getBoundingClientRect();
                const linkRect = link.getBoundingClientRect();

                const left = linkRect.left - containerRect.left + navContainer.scrollLeft;
                const width = linkRect.width;

                if (rAFId) cancelAnimationFrame(rAFId);
                rAFId = requestAnimationFrame(() => {
                    underline.style.transform = `translate3d(${left}px, 0, 0)`;
                    underline.style.width = `${width}px`;
                    underline.style.opacity = '1';
                });
            }

            // Helper to ensure navContainer remains firmly at scrollLeft 0
            function centerActiveTab(link) {
                if (!navContainer) return;
                navContainer.scrollLeft = 0;
            }

            // Desktop Mouse Drag-to-Scroll support
            let isDragDown = false;
            let dragStartX, dragScrollLeft;

            navContainer.addEventListener('mousedown', (e) => {
                isDragDown = true;
                dragStartX = e.pageX - navContainer.offsetLeft;
                dragScrollLeft = navContainer.scrollLeft;
            });

            navContainer.addEventListener('mouseleave', () => {
                isDragDown = false;
            });
            navContainer.addEventListener('mouseup', () => {
                isDragDown = false;
            });

            navContainer.addEventListener('mousemove', (e) => {
                if (!isDragDown) return;
                e.preventDefault();
                const x = e.pageX - navContainer.offsetLeft;
                const walk = (x - dragStartX) * 1.5;
                navContainer.scrollLeft = dragScrollLeft - walk;
            });

            // Initialize active underline position after DOM layout renders
            setTimeout(() => {
                const initialActive = navContainer.querySelector('.nav-menu-link.active');
                if (initialActive) moveUnderline(initialActive);
            }, 100);

            // Update underline position on resize or container scroll
            window.addEventListener('resize', function() {
                const active = navContainer.querySelector('.nav-menu-link.active');
                if (active) moveUnderline(active);
            }, {
                passive: true
            });

            navContainer.addEventListener('scroll', function() {
                const active = navContainer.querySelector('.nav-menu-link.active');
                if (active) moveUnderline(active);
            }, {
                passive: true
            });

            let isManualScrolling = false;
            let scrollTimer = null;
            let currentActiveId = null;

            // Instant Click Response & Immediate Page Scroll
            navContainer.addEventListener('click', function(e) {
                const link = e.target.closest('.nav-menu-link');
                if (!link) return;

                e.preventDefault();
                const targetId = link.getAttribute('data-target');
                const targetEl = document.getElementById(targetId);

                if (targetEl) {
                    isManualScrolling = true;
                    currentActiveId = targetId;

                    navLinks.forEach(l => {
                        if (l === link) {
                            l.classList.add('active');
                            l.setAttribute('aria-selected', 'true');
                            moveUnderline(l);
                            centerActiveTab(l);
                        } else {
                            l.classList.remove('active');
                            l.setAttribute('aria-selected', 'false');
                        }
                    });

                    const navbarHeight = document.getElementById('navbar')?.offsetHeight || 80;
                    const stickyNavHeight = navWrapper?.offsetHeight || 56;
                    const totalOffset = navbarHeight + stickyNavHeight + 12;

                    const targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset -
                        totalOffset;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => {
                        isManualScrolling = false;
                    }, 800);
                }
            });

            // Butter-Smooth 60FPS Scroll Spy
            let isScrollSpyTicking = false;

            function updateScrollSpy() {
                if (isManualScrolling) return;

                const navbarHeight = document.getElementById('navbar')?.offsetHeight || 80;
                const stickyNavHeight = navWrapper?.offsetHeight || 54;
                const totalHeaderOffset = navbarHeight + stickyNavHeight + 25;

                const scrollPosition = window.scrollY + totalHeaderOffset;

                let activeSection = sections[0];
                for (let i = 0; i < sections.length; i++) {
                    const sec = sections[i];
                    const secTop = sec.getBoundingClientRect().top + window.pageYOffset;
                    if (scrollPosition >= secTop - 5) {
                        activeSection = sec;
                    } else {
                        break;
                    }
                }

                if (activeSection && activeSection.id !== currentActiveId) {
                    currentActiveId = activeSection.id;

                    navLinks.forEach(link => {
                        if (link.getAttribute('data-target') === currentActiveId) {
                            link.classList.add('active');
                            link.setAttribute('aria-selected', 'true');
                            moveUnderline(link);
                            centerActiveTab(link);
                        } else {
                            link.classList.remove('active');
                            link.setAttribute('aria-selected', 'false');
                        }
                    });
                }
            }

            // 2-Stage Desktop Sidebar Sticky Handler:
            // Stage 1 (first half of the detail content): Quick Enquiry stays sticky.
            // Stage 2 (remaining content): Recommended Projects takes its place and stays sticky.
            // Keep the form height cached: measuring it after display:none returns 0 and
            // previously caused the two cards to alternate rapidly while scrolling.
            let sidebarStage = null;
            let enquiryFormHeight = 0;

            function measureEnquiryForm(enquiryForm) {
                const measuredHeight = enquiryForm.getBoundingClientRect().height || enquiryForm.scrollHeight;

                if (measuredHeight > 0) {
                    enquiryFormHeight = measuredHeight;
                }

                return enquiryFormHeight;
            }

            function setSidebarCardDisplay(element, display) {
                // Bootstrap's d-lg-block utility uses !important, so a normal
                // element.style.display assignment cannot reliably hide a card.
                element.style.setProperty('display', display, 'important');
            }

            function updateTwoStageSidebar() {
                const enquiryForm = document.getElementById('sidebar-enquiry-form');
                const recommendedCard = document.getElementById('sidebar-recommended-projects');
                const sidebarWrapper = document.getElementById('sidebar-wrapper');

                if (!enquiryForm || !recommendedCard || !sidebarWrapper) return;

                if (window.innerWidth < 992) {
                    enquiryForm.style.removeProperty('display');
                    enquiryForm.style.position = '';
                    enquiryForm.style.top = '';
                    enquiryForm.style.transform = '';
                    recommendedCard.style.removeProperty('display');
                    recommendedCard.style.position = '';
                    recommendedCard.style.top = '';
                    sidebarStage = null;
                    return;
                }

                // Measure before either card is hidden, and retain that height across
                // later scroll frames to make the switch point deterministic.
                measureEnquiryForm(enquiryForm);

                const boundarySection = document.getElementById('possession') ||
                    document.getElementById('floor') ||
                    document.getElementById('location') ||
                    document.getElementById('summary') ||
                    document.getElementById('overview');

                if (!boundarySection) return;

                const navbarHeight = document.getElementById('navbar')?.offsetHeight || 80;
                const stickyNavHeight = navWrapper?.offsetHeight || 54;
                const topMargin = navbarHeight + stickyNavHeight + 15;

                const scrollY = window.pageYOffset;
                const boundaryRect = boundarySection.getBoundingClientRect();
                const boundaryBottomDoc = boundaryRect.top + scrollY + boundarySection.offsetHeight;

                const enquiryHeight = enquiryFormHeight || 500;
                const stickyTriggerDoc = scrollY + topMargin;
                const maxEnquiryTopDoc = boundaryBottomDoc - enquiryHeight;
                const nextStage = stickyTriggerDoc < maxEnquiryTopDoc ? 'enquiry' : 'recommended';

                // Styles only change at the handoff point. Re-applying display and
                // sticky properties on every scroll frame creates visible jitter.
                if (sidebarStage === nextStage) return;

                sidebarStage = nextStage;

                if (nextStage === 'enquiry') {
                    // STAGE 1 (Overview -> Possession): Show Enquiry Form (Sticky), Hide Recommended Card
                    setSidebarCardDisplay(enquiryForm, 'block');
                    enquiryForm.style.position = 'sticky';
                    enquiryForm.style.top = `${topMargin}px`;
                    enquiryForm.style.zIndex = '12';

                    setSidebarCardDisplay(recommendedCard, 'none');
                } else {
                    // STAGE 2 (After Possession -> Bottom): Hide Enquiry Form, Show & Hold Recommended Card (Sticky)
                    setSidebarCardDisplay(enquiryForm, 'none');

                    setSidebarCardDisplay(recommendedCard, 'block');
                    recommendedCard.style.position = 'sticky';
                    recommendedCard.style.top = `${topMargin}px`;
                    recommendedCard.style.zIndex = '10';
                }
            }

            // Visibility toggle, Scroll Spy, and 2-Stage Sticky listener
            function handleScroll() {
                if (!isScrollSpyTicking) {
                    requestAnimationFrame(() => {
                        updateScrollSpy();
                        updateTwoStageSidebar();
                        if (navWrapper) {
                            const overviewEl = document.getElementById('overview');
                            if (overviewEl) {
                                const currentNavbarHeight = document.getElementById('navbar')
                                    ?.offsetHeight || 80;
                                const rect = overviewEl.getBoundingClientRect();
                                if (rect.top <= currentNavbarHeight + 160) {
                                    navWrapper.classList.add('is-visible');
                                } else {
                                    navWrapper.classList.remove('is-visible');
                                }
                            }
                        }
                        isScrollSpyTicking = false;
                    });
                    isScrollSpyTicking = true;
                }
            }

            window.addEventListener('resize', function() {
                const active = navContainer.querySelector('.nav-menu-link.active');
                if (active) moveUnderline(active);
                updateTwoStageSidebar();
            }, {
                passive: true
            });

            window.addEventListener('scroll', handleScroll, {
                passive: true
            });
            handleScroll();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const projectBox = document.querySelector('.project-box');
            if (!projectBox) return;

            const projectId = projectBox.id.split('_')[1];

            const sqftPriceData = JSON.parse(projectBox.dataset.sqftprice || '[]');
            const priceValue = parseFloat(sqftPriceData[0]?.value || 0);

            const bspEl = document.getElementById('bsp_value_' + projectId);
            const totalEl = document.getElementById('total_value_' + projectId);

            document.querySelectorAll('.floor-btn').forEach(btn => {

                btn.addEventListener('click', function() {

                    const planIndex = this.dataset.planIndex;

                    document.querySelectorAll('.floor-btn').forEach(b => b.classList.remove(
                        'active'));

                    if (planIndex !== undefined) {
                        document.querySelectorAll(`.floor-btn[data-plan-index="${planIndex}"]`)
                            .forEach(b => b.classList.add('active'));
                    } else {
                        this.classList.add('active');
                    }

                    const superArea = parseFloat(this.dataset.superArea || 0);
                    const planPriceAttr = this.dataset.planPrice;

                    if (bspEl && priceValue > 0) {
                        bspEl.textContent = `₹${priceValue.toLocaleString('en-IN')}/sq.ft`;
                    }

                    if (totalEl) {
                        if (planPriceAttr && planPriceAttr !== '—') {
                            totalEl.textContent = planPriceAttr;
                        } else if (superArea > 0 && priceValue > 0) {
                            const totalValue = superArea * priceValue;
                            totalEl.textContent = `₹ ${(totalValue / 10000000).toFixed(2)} Cr`;
                        }
                    }
                });

            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.rera-toggle').forEach(function(toggle) {
                const panel = toggle.closest('.position-relative') ? toggle.closest('.position-relative')
                    .querySelector('.rera-panel') : toggle.parentElement.querySelector('.rera-panel');
                if (!panel) return;
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (panel.classList.contains('pinned')) {
                        panel.classList.remove('active', 'pinned');
                    } else {
                        document.querySelectorAll('.rera-panel.active').forEach(function(p) {
                            if (p !== panel) {
                                p.classList.remove('active', 'pinned');
                            }
                        });
                        panel.classList.add('active', 'pinned');
                    }
                });
                toggle.addEventListener('mouseenter', function() {
                    if (!panel.classList.contains('pinned')) {
                        document.querySelectorAll('.rera-panel.active').forEach(function(p) {
                            if (p !== panel && !p.classList.contains('pinned')) {
                                p.classList.remove('active');
                            }
                        });
                        panel.classList.add('active');
                    }
                });

                toggle.addEventListener('mouseleave', function() {
                    if (!panel.classList.contains('pinned')) {
                        panel.classList.remove('active');
                    }
                });
            });
            document.addEventListener('click', function(e) {
                if (e.target.closest('.rera-close')) {
                    const p = e.target.closest('.rera-panel');
                    if (p) {
                        p.classList.remove('active', 'pinned');
                    }
                    return;
                }
                if (!e.target.closest('.rera-panel') && !e.target.closest('.rera-toggle')) {
                    document.querySelectorAll('.rera-panel.active').forEach(function(p) {
                        p.classList.remove('active', 'pinned');
                    });
                }
            });
        });
    </script>
@endSection

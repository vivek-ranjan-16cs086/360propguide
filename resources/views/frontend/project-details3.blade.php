@extends('frontend.layouts.app')
@section('usesSweetAlert', true)
@section('og_image', url('storage/' . $projects->logo_image))
@section('title', $projects->seo_data['title'])
@section('keywords', $projects->seo_data['primary_keyword'] . ' ' . $projects->seo_data['secondary_keyword'])
@section('description', $projects->seo_data['meta_description'])
@section('canonical', url()->current())
@push('schema')
    @if(!empty($faqSchema))
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

        "image" => url('storage/' . $projects->hero_images),

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
<link rel="stylesheet" href="{{asset('frontend/css/commercial.css')}}" />
@endSection
@section('content')


<!-- hero section -->
<div class="project-HeroSection container pt-5">
    <div class="row align-items-center">
        <div class="mb-3 col-md-6">
            <div>
                <h1 class="mb-2">{{ $projects->project_name }}</h1>

                <div class="d-flex align-items-center flex-wrap gap-2"> 

                    {{-- Project Status Badge --}}
                    @if(!empty($projects->project_status))
                        <span class="statusIcon">
                            <i class="fa-solid fa-building"></i>
                            {{ ucfirst(clean($projects->project_status)) }}
                        </span>
                    @endif

                    {{-- RERA Approved Badge --}}
                    @if(!empty($projects->rera_no) && strtoupper(trim($projects->rera_no)) != 'N/A')
                        <span class="reraBadge">
                            <i class="fa-solid fa-circle-check me-1"></i> RERA Approved
                        </span>
                    @endif

                </div>
            </div>
            <p class="mb-0 mt-2">
                By <span class="text-primary">{{$projects->developer_name}}</span>
            </p>

            <p class="text-secondary mb-0"><i class="fa-solid fa-location-dot me-2 ms-0 p-0"></i>{{$projects->location}}
            </p>
        </div>
        <div class="mb-3 mb-md-0 ms-auto col-md-auto">

            @if($projects->slug == 'prestige-bougainvillea-gardens')

                <button type="button" class="btn customBtn orange rounded-3 mb-5" data-bs-toggle="modal"
                    data-bs-target="#contactModal">
                    Price On Request
                </button>

            @else

                <p class="h3 text-lg-end text-sm-end mb-3">
                    @if(!empty($projects->max_price) && $projects->price != $projects->max_price)
                        ₹{{ formatPrice($projects->price) }} - ₹{{ formatPrice($projects->max_price) }}
                    @else
                        ₹{{ formatPrice($projects->price) }} Onwards
                    @endif
                </p>

            @endif


            <div class="d-flex  mb-3 mb-md-0 w-100 ms-0">
                <div>
                    <a rel="noopener noreferrer" target="_blank" href="tel:+919643020020"
                        class="btn customBtn rounded-3 d-lg-none">Contact Sales</a>
                </div>
                <div class="ms-3 ">
                    <button type="button" class="btn customBtn rounded-3" data-bs-toggle="modal"
                        data-bs-target="#contactModal">
                        Enquire Now
                    </button>
                </div>
                <div>

                    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                        <i class="mx-2 fa-solid fa-arrow-up-from-bracket"></i><span>Share</span>
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title fs-5 h1" id="exampleModalLabel">Share Project</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body d-flex gap-4 justify-content-center align-items-center">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('projects/' . $projects->slug)) }}"
                                        target="__blank">
                                        <i class="fa-brands fa-facebook fs-1"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url('projects/' . $projects->slug)) }}"
                                        target="__blank">
                                        <i class="fa-brands fa-linkedin fs-1"></i>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode(url('projects/' . $projects->slug)) }}"
                                        target="__blank">
                                        <i class="fa-brands fa-square-whatsapp fs-1"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('projects/' . $projects->slug)) }}"
                                        target="__blank">
                                        <i class="fa-brands fa-square-x-twitter fs-1"></i>
                                    </a>
                                    <a href="#"
                                        onclick="copyToClipboard('{{ url('projects/' . $projects->slug) }}'); return false;"
                                        title="Copy Link">
                                        <i class="fa-solid fa-link fs-2"></i>
                                    </a>

                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row hero-section" id="hero">
        <div class="col-md-6 d-none d-md-block">
            <img class="w-100 h-100 object-fit-cover rounded-start-3" src="{{url('storage/' . $projects->hero_images)}}"
                alt="{{$projects->project_name}}" />
        </div>
        <div class="col-md-6">
            <div class="row h-100">
                <div class="col-6 mb-3 aspect2-1 d-none d-md-block">
                    <img class="w-100 h-100" src="{{url('storage/' . $projects->amenities_images)}}"
                        alt="{{$projects->project_name}}" />
                </div>
                <div class="col-6 mb-3 aspect2-1 d-none d-md-block">
                    <img class="w-100 h-100 rounded-3 rounded-start-0 rounded-bottom-0"
                        src="{{url('storage/' . $projects->feature_image)}}" alt="{{$projects->project_name}}" />
                </div>
                <div class="col-6 aspect2-1 d-none d-md-block">
                    <img class="w-100 h-100" src="{{url('storage/' . $projects->logo_image)}}"
                        alt="{{$projects->project_name}}" />
                </div>
                <div class="col-6 aspect2-1 d-none d-md-block">
                    <img class="w-100 h-100 rounded-3 rounded-start-0 rounded-top-0"
                        src="{{url('storage/' . $projects->developer_background_image)}}"
                        alt="{{$projects->project_name}}" />
                </div>
            </div>
        </div>
        <!-- Mobile View (Slider) -->
        <div class="col-12 d-block d-md-none">
            <div id="mobileProjectSlider" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="{{url('storage/' . $projects->hero_images)}}"
                            alt="{{$projects->project_name}}">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="{{url('storage/' . $projects->amenities_images)}}"
                            alt="{{$projects->project_name}}">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="{{url('storage/' . $projects->feature_image)}}"
                            alt="{{$projects->project_name}}">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="{{url('storage/' . $projects->logo_image)}}"
                            alt="{{$projects->project_name}}">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="{{url('storage/' . $projects->developer_background_image)}}"
                            alt="{{$projects->project_name}}">
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
</div>
<div class="container">
    <div class="row my-4 justify-content-between">
        <div class="col-lg-7 col-md-12 order-2 order-lg-1">
            <div>
                <!-- summary -->
                <div class="section project-section" id="summary">
                    <h2 class="h4 mb-4 text-primary fw-bold ">
                        About {{$projects->project_name}}
                    </h2>
                    <p>{!!$projects->about_description!!}</p>
                    @if (!empty($projects->youtube_links))
                        <a href="{{ $projects->youtube_links }}" target="_blank" rel="noopener" class="youtube-link">
                            <span class="youtube-icon"></span>
                            Explore the Project in Action <i class="fab fa-youtube" style="color: #FF0000;"></i> Watch Now
                            on YouTube!
                        </a>
                    @endif

                </div>
                <!-- key insights -->
                <div class="section project-section" id="key">
                    <h2 class="h4 my-4 text-primary fw-bold">Key Insights</h2>
                    <p class="mb-3">{!!$projects->key_insights!!}</p>
                    <div class="row">
                        <div class="col-6 col-md-5 mb-2 position-relative">
                            <div class="d-inline-block w-100">
                                <span class="text-primary rera-toggle rera-info" style="cursor:pointer;">
                                    <i class="fa fa-info-circle px-2"></i> RERA INFO:
                                </span>
                                <span class="ms-1">{{ $projects->rera_no }}</span>

                                <div class="rera-panel shadow" aria-hidden="true" role="dialog"
                                    aria-label="RERA details">
                                    <div class="rera-header">
                                        <h4>RERA Details</h4>
                                        <span class="rera-close" role="button" aria-label="Close">&times;</span>
                                    </div>

                                    <div class="rera-top-info">
                                        <p>
                                            <i class="fa fa-link"></i>
                                            Source:
                                            <a href="https://www.up-rera.in" target="_blank" rel="noopener noreferrer"
                                                class="normal-link">
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

                                        @if(!empty($rera_details))
                                            @foreach($rera_details as $rera)
                                                <div class="rera-grid">
                                                    <div class="rera-left">
                                                        <p class="rera-label">REGISTERED</p>
                                                        <p class="rera-phase">{{ !empty($rera['phase']) ? $rera['phase'] : '' }}
                                                        </p>
                                                        <p class="rera-no">
                                                            {{ !empty($rera['rera_no']) ? $rera['rera_no'] : $projects->rera_no }}
                                                        </p>
                                                    </div>
                                                    @if(!empty($rera['qr_image']))
                                                        <div class="rera-right">
                                                            <img src="{{ url('storage/' . $rera['qr_image']) }}" alt="RERA QR Code">
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

                        @if(!empty($projects->launch_date))
                            <div class="col-6 col-md-5 mb-2">
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                                <span>
                                    <span class="text-primary fw-bold">Launch Date:</span>
                                    {{ $projects->launch_date }}
                                </span>
                            </div>
                        @endif

                        <div class="col-6 col-md-5 mb-2">
                            <i class="fa-solid fa-helmet-safety me-2 text-primary"></i><span><span
                                    class="text-primary fw-bold">Developer: </span>{{$projects->developer_name}}</span>
                        </div>
                        <div class="col-6 col-md-5 mb-2">
                            <i class="fa-solid fa-chart-area me-2 text-primary"></i><span><span
                                    class="text-primary fw-bold">Property
                                    Size: </span>{{$projects->property_size}}</span>
                        </div>
                        <div class="col-6 col-md-5 mb-2">
                            <i class="fa-solid fa-house-chimney me-2 text-primary"></i><span><span
                                    class="text-primary fw-bold">Typology: </span>{{$projects->typology_string }}</span>
                        </div>
                        <div class="col-6 col-md-5 mb-2">
                            <i class="fa-solid fa-location-dot me-2 text-primary"></i><span><span
                                    class="text-primary fw-bold">Location: </span>{{$projects->location}}</span>
                        </div>
                        <div class="col-6 col-md-5 mb-2 text-capitalize">
                            <i class="fa-solid fa-building me-2 text-primary"></i><span><span
                                    class="text-primary fw-bold">Project
                                    Status: </span>{{clean($projects->project_status)}}</span>
                        </div>
                        <!-- <div class="col-6 col-md-5 mb-2">
              <i class="fa-solid fa-sack-dollar me-2 text-primary"></i><span><span class="text-primary fw-bold">Price:
                </span>{{formatPrice($projects->price)}} Onwards*</span>
            </div>-->
                        @if(request()->path() != 'projects/prestige-bougainvillea-gardens')
                            <div class="col-6 col-md-5 mb-2">
                                <i class="fa-solid fa-sack-dollar me-2 text-primary"></i>
                                <span>
                                    <span class="text-primary fw-bold">Price:</span>
                                    {{ formatPrice($projects->price) }} Onwards*
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- price -->
                <!--<div class="price section project-section" id="price">
         @if(!empty($projects->sqft_price))
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
                @if(request()->path() != 'projects/prestige-bougainvillea-gardens')
				<div class="price section project-section" id="price">
					@if(!empty($projects->sqft_price))

						@php
							$floorPlans = json_decode($projects->floor_plans_data, true) ?? [];
							$sqftPrices = json_decode($projects->sqft_price, true) ?? [];

							$priceValue = isset($sqftPrices[0]['value']) ? (float)$sqftPrices[0]['value'] : 0;

							$firstPlan = $floorPlans[0] ?? null;
							$superArea = isset($firstPlan['super_area']) ? (float)$firstPlan['super_area'] : 0;

							$totalValue = $superArea * $priceValue;
						@endphp

						<div>

							{{-- Hidden data for JS --}}
							<div class="project-box mt-4"
								id="project_{{ $projects->id }}"
								data-floorplans='{{ $projects->floor_plans_data }}'
								data-sqftprice='{{ $projects->sqft_price }}'>
							</div>

							{{-- Floor Plan Tabs --}}
							<div class="floorplan-tabs d-flex flex-wrap gap-2 mt-3" id="floor_tabs_{{ $projects->id }}">
								@foreach($floorPlans as $index => $plan)
									<button
										type="button"
										class="floor-btn {{ $index == 0 ? 'active' : '' }}"
										data-super-area="{{ $plan['super_area'] ?? 0 }}">
										{{ $plan['title'] ?? 'Configuration '.($index + 1) }}
									</button>
								@endforeach
							</div>

							{{-- Price Section --}}
							<div class="row text-center mt-5">
								<div class="col-6 col-md-6 mb-3">
									<div class="price-box">
										<div class="label text-primary">BSP</div>
										<div class="value" id="bsp_value_{{ $projects->id }}">
											@if($priceValue)
												₹{{ number_format($priceValue, 0) }}/sq.ft
											@else
												--
											@endif
										</div>
									</div>
								</div>

								<div class="col-6 col-md-6 mb-3">
									<div class="price-box">
										<div class="label text-primary">Total Value</div>
										<div class="value" id="total_value_{{ $projects->id }}">
											@if($priceValue && $superArea)
												₹
												{{ number_format($totalValue / 10000000, 2) }} Cr
											@else
												--
											@endif
										</div>
									</div>
								</div>
							</div>

						</div>

					@endif
				</div>
			@endif
                <!-- location -->
                <div class="section project-section" id="location">
                    <h2 class="h4 my-4 text-primary fw-bold">{{ $projects->project_name }} Location</h2>
                    @if(!empty($projects->location_video))
                        <video src="{{ url('storage/' . $projects->location_video) }}" class="w-100" autoplay muted
                            controls></video>
                    @endif
                    <p class="my-3">{!!@$projects->location_description !!}
                    </p>

                </div>

                @if(!empty($projects->site_plans_images))
                    <section class="siteplan-section py-5">
                        <div class="container">
                            <h3 class="section-title mb-4">{{ $projects->project_name }} Site Plan</h3>

                            <div class="siteplan-card shadow-sm">
                                <div class="siteplan-img-wrapper">
                                    <img class="img-fluid siteplan-image d-block w-100"
                                        src="{{ url('storage/' . $projects->site_plans_images) }}"
                                        alt="{{ $projects->project_name }} Site Plan">
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                <!-- New Row for Sanctioned Map & Lease Deed -->
                <div class="h4 my-4 text-primary fw-bold heading">{{ $projects->project_name }} Legal Status</div>
                <div class="row g-3 align-items-center mb-3">

                    <!-- Sanctioned Map -->
                    <div class="col-6 col-md-6 ">
                        <button type="button" class="btn customBtn w-100 download-btn rounded-3 p-2"
                            data-bs-toggle="modal" data-bs-target="#contactModal" data-project-id="{{ $projects->id }}"
                            data-type="sanctioned_map">
                            <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Sanctioned Map
                        </button>
                    </div>

                    <!-- Lease Deed -->
                    <div class="col-6 col-md-6 text-md-end">
                        <button type="button" class="btn customBtn w-100 w-md-auto download-btn rounded-3 p-2"
                            data-bs-toggle="modal" data-bs-target="#contactModal" data-project-id="{{ $projects->id }}"
                            data-type="lease_deed">
                            <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Lease Deed
                        </button>
                    </div>

                </div>
                <!-- siteplan and floor plan -->
                <div class="section project-section" id="floor">
                    <h2 class="h4 my-4 text-primary fw-bold">{{ $projects->project_name }} Floor Plan </h2>
                    <div class="floorPlans">
                        <div thumbsSlider="" class="swiper mySwiper mb-3">
                            <div class="swiper-wrapper">
                                @php
                                    $floorPlans = json_decode($projects->floor_plans_data);
                                @endphp
                                @if(!empty($floorPlans) && count($floorPlans) > 0)
                                    @foreach($floorPlans as $index => $floorPlan)
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
                        <div style="
              --swiper-navigation-color: #fff;
              --swiper-pagination-color: #fff;
            " class="swiper  floorSwiper mb-3">
                            <div class="swiper-wrapper">
                                @php
                                    $floorPlans = json_decode($projects->floor_plans_data, true);
                                @endphp
                                @if(!empty($floorPlans) && count($floorPlans) > 0)
                                    @foreach($floorPlans as $index => $floorPlan)
                                        <div class="swiper-slide flex-column">
                                            <img class="col-12 col-md-10 mx-auto" alt="{{$projects->project_name}}"
                                                src="{{ url('storage/' . $floorPlan['image']) }}" />
                                            <div class="row m-0 w-100 align-items-center">
                                                @if(@$floorPlan['super_area'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary"> Super area:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0 fw-bold">{{ $floorPlan['super_area'] }} sq. ft.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(@$floorPlan['carpet_area'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">Carpet area:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['carpet_area'] }} sq. ft.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(@$floorPlan['built_area'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">BuiltUp area:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['built_area'] }} sq. ft.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(@$floorPlan['balcony_area'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">Balcony area:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['balcony_area'] }} sq. ft.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(@$floorPlan['length'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">Length:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['length'] }} m</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(@$floorPlan['width'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">Width:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['width'] }} m</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(@$floorPlan['total_area'] != 0)
                                                    <div class="col-md-3 col-6">
                                                        <div class="">
                                                            <p class="m-0 text-center py-2 fs-4 text-primary">Total area:</p>
                                                            <div class="fw-bold text-black">
                                                                <p class="m-0">{{ $floorPlan['total_area'] }} sq. yds</p>
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


                <div class="h4 my-4 text-primary fw-bold heading">{{ $projects->project_name }} Brochure And Price List</div>
                <div class="row g-3 align-items-center mb-3">

                    <!-- Left button -->
                    <div class="col-6 col-md-6">
                        <button type="button" class="btn customBtn w-100 download-btn rounded-3 p-2"
                            data-bs-toggle="modal" data-bs-target="#contactModal" data-project-id="{{ $projects->id }}"
                            data-type="brochure">
                            <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Brochure
                        </button>
                    </div>

                    <!-- Right button -->
                    <div class="col-6 col-md-6 text-md-end">
                        <button type="button" class="btn customBtn w-100 w-md-auto download-btn rounded-3 p-2"
                            data-bs-toggle="modal" data-bs-target="#contactModal" data-project-id="{{ $projects->id }}"
                            data-type="price_list">
                            <i class="fa-solid fa-download me-2 d-none d-md-inline"></i>Download Price List
                        </button>
                    </div>

                </div>
                <!-- possessions -->
                <div class="possessions section project-section" id="possession">
                    <h2 class="h4 my-4 text-primary fw-bold">{{ $projects->project_name }} Possessions </h2>
                    <div class="mb-3">{!!$projects->possession_description!!}</div>

                </div>
                <!-- amenities -->
                <div class="amenities section project-section" id="amenities">
                    <h2 class="h4 my-4 text-primary fw-bold">{{ $projects->project_name }} Amenities </h2>
                    <div class="row">
                        @if(!empty($projects->amenitiesDetails) && count($projects->amenitiesDetails) > 0)
                            @foreach($projects->amenitiesDetails as $index => $amenity)
                                <div class="icon col-md-2 col-4 ms-3">
                                    <img src="{{url('storage/' . $amenity->image)}}"
                                        alt="{{'360_propguide' . $amenity->name}}" />
                                    <span>{{$amenity->name}}</span>
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>
                <!-- price -->
                <div class="price section project-section" id="price">
                    <h2 class="h4 my-4 text-primary fw-bold">{{ $projects->project_name }} Pricing </h2>
                    {!!$projects->site_plans_description!!}


                </div>

                <!-- dev backgrounds -->
                <div class="possessions section project-section" id="developer">
                    <h3 class="h4 my-4 text-primary fw-bold">Developer Background</h3>
                    <div class="row align-items-md-center mb-3">
                        @if (!empty($projects->developerDetails))
                            @foreach ($projects->developerDetails as $developer)
                                <div class="col-12 col-md-3">
                                    <img src="{{url('storage/' . $developer->developer_logo)}}"
                                        class="col-6 mx-auto col-md-12 d-none" alt="{{$projects->project_name}}" />
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="text-center py-2 text-primary">
                                        Experience:
                                        <div class="fw-bold text-black">{{$developer->developer_experience}} +</div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="text-center py-2 text-primary">
                                        Ongoing Projects:
                                        <div class="fw-bold text-black">{{$developer->ongoing_project}} </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="text-center py-2 text-primary">
                                        Completed Projects:
                                        <div class="fw-bold text-black">{{$developer->completed_projects}} </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    {!!$projects->developer_background_dscp!!}
                </div>

                <div class="section mb-3 project-section" id="faq">
                    <h3 class="h4 my-4 text-primary fw-bold">
                        Frequently Asked Questions
                    </h3>
                    <div class="faq">
                        @php
                            $faqs = json_decode($projects->faqs_data, true);
                        @endphp
                        @if(!empty($faqs) && count($faqs) > 0)
                            @foreach($faqs as $index => $faq)
                                <div class="question d-flex">
                                    <div class="number fs-5 d-flex align-items-center justify-content-center me-3">
                                        Q
                                    </div>
                                    <div class="">
                                        <h3 class="key-Title fw-bold fs-6">
                                            {{$faq['question']}}
                                        </h3>
                                        <div>
                                            <p class="fs-14 m-0">{{$faq['answer'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="d-block d-lg-none ppc-form col-md-8 mx-auto col-lg-12">
                    <div class="row p-4">
                        <h5 class="text-center mb-3">You can Count on us for Great Deals!</h5>
                        <div class="alert alert-success success-message d-none">
                            Your enquiry has been submitted successfully.
                        </div>
                        <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
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
                                <input type="email" placeholder="Email Address*" name="email" class="form-control" />
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
                                <textarea type="text" placeholder="Message" name="message" class="form-control" row="5"
                                    col="1"></textarea>
                            </div>
                            <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}">
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

            {{-- Sidebar sticky wrapper — everything inside sticks together --}}
            <div>

                {{-- Desktop Enquiry Form --}}
                <div class="d-none d-lg-block ppc-form col-md-8 mx-auto col-lg-12 mb-3">
                    <div class="row p-4">
                        <h5 class="text-center mb-3">You can Count on us for Great Deals!</h5>
                        <div class="alert alert-success success-message d-none">
                            Your enquiry has been submitted successfully.
                        </div>
                        <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
                            @csrf
                            <input type="hidden" name="formName" value="popup">
                            <div class="form-group input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" class="form-control error commonerr name" name="name"
                                    placeholder="Name" />
                            </div>
                            <span class="text-danger error-name"></span>
                            <div class="form-group input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" placeholder="Email Address*" name="email"
                                    class="form-control email" />
                            </div>
                            <span class="text-danger error-email"></span>
                            <div class="form-group input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control mobile" name="mobile" placeholder="Mobile*"
                                    minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" />
                            </div>
                            <span class="text-danger error-mobile"></span>
                            <div class="form-group">
                                <textarea type="text" placeholder="Message" name="message" class="form-control" row="5"
                                    col="1"></textarea>
                            </div>
                            <div class="g-recaptcha mb-3 mt-3"
                                data-sitekey="{{ config('services.recaptcha.site_key') }}">
                            </div>
                            <span class="text-danger error-recaptcha"></span>
                            @if ($errors->has('recaptchaform3'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('recaptchaform3') }}
                                </div>
                            @endif
                            <div class="w-full"> <button type="submit"
                                    class="btn orange text-white w-100 mb-2 submitButton">Submit</button> </div>
                            <div class="w-full d-flex justify-content-between gap-2">
                                <a href="https://wa.me/919643020020?text={{ urlencode('I want brochure of ' . $projects->project_name) }}"
                                    target="_blank" id="whatsapp-btn-3"
                                    class="btn whatsapp text-white w-50 d-flex justify-content-center align-items-center"><i
                                        class="fab fa-whatsapp me-2"></i> WhatsApp
                                </a>
                                <a href="tel:919643020020"
                                    class="btn orange text-white w-50 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-phone me-2"></i> Call
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Desktop TOC Nav Card --}}
                <div class="card shadow-lg bg-white py-3 mb-3 TOC d-none d-md-block">
                    <div class="h4 text-primary fw-bold px-4 py-1">Key Details</div>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="summary">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            1
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">SUMMARY</h6>
                            <p class="fs-14 mb-0">Quick Overview of What Awaits You.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="key">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            2
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">KEY INSIGHTS</h6>
                            <p class="fs-14 mb-0">Important Highlights at a Glance.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="location">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            3
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">LOCATION</h6>
                            <p class="fs-14 mb-0">The Perfect Spot to Call Home.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="floor">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            4
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">FLOOR PLAN</h6>
                            <p class="fs-14 mb-0">Visualize Your Dream Space.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="possession">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            5
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">POSSESSION</h6>
                            <p class="fs-14 mb-0">Timelines to Turn Your Dreams Into Reality.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="amenities">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            6
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">AMENITIES</h6>
                            <p class="fs-14 mb-0">Indulge in Luxurious Comforts.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="price">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            7
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">PRICE LIST</h6>
                            <p class="fs-14 mb-0">Transparent Pricing for Your Investment.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="developer">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            8
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">DEVELOPER BACKGROUND</h6>
                            <p class="fs-14 mb-0">Trusted Legacy Behind the Project.</p>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center px-4 py-2" data-target="faq">
                        <div class="number fs-1 d-flex align-items-center justify-content-center pe-3">
                            9
                        </div>
                        <div class="">
                            <h6 class="key-Title mb-0 fw-bold">FAQS</h6>
                            <p class="fs-14 mb-0">Your Questions Answered.</p>
                        </div>
                    </a>
                </div>

                <div class="card shadow-lg bg-white p-3 d-none d-lg-block mt-3">

                    <div class="h4 text-primary fw-bold">Recommended</div>
                    @foreach($recommendedProjects as $recommended)
                        <a class="col-12 d-block mb-3" href="{{route('projects.details', $recommended->slug) }}">
                            <div>
                                <div class="d-flex border Recommended-card imgHover">
                                    <div class="col-5 col-md-5 img-container position-relative">
                                        <img alt="{{$recommended->project_name }}" loading="lazy" decoding="async"
                                            data-nimg="fill" class="h-100 object-fit-cover rounded-start-1" sizes="100vw"
                                            src="{{ url('storage/' . $recommended->logo_image) }}" style="
                                            position: absolute;
                                            height: 100%;
                                            width: 100%;
                                            inset: 0px;
                                            color: transparent;
                                          " />
                                    </div>
                                    <div class="p-3 font-sm col-7">
                                        <div class="project-heading">
                                            <p class="recommended-title mb-2">{{ $recommended->project_name }}</p>
                                            <div class="text-secondary fs-7"></div>
                                        </div>
                                        <div class="project-location">
                                            <p class="text-secondary fs-7 mb-0">{{ $recommended->typology}}</p>
                                            <p class="text-secondary recommended-location fs-7 mb-0">
                                                {{ $recommended->location}}
                                            </p>
                                        </div>
                                        <p class="project-price mb-0">₹ {{ formatPrice($recommended->price) }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach

                </div>

            </div>{{-- /project-right --}}
        </div>
    </div>

    <!-- Mobile TOC Navigation -->
    <div class="mobile-toc d-md-none">
        <div class="toc-items">
            <a href="javascript:void(0)" class="toc-link toc-summary" data-target="summary">Summary</a>
            <a href="javascript:void(0)" class="toc-link toc-key" data-target="key">Key Insights</a>
            <a href="javascript:void(0)" class="toc-link toc-location" data-target="location">Location</a>
            <a href="javascript:void(0)" class="toc-link toc-floor" data-target="floor">Floor Plan</a>
            <a href="javascript:void(0)" class="toc-link toc-possession" data-target="possession">Possessions</a>
            <a href="javascript:void(0)" class="toc-link toc-amenities" data-target="amenities">Amenities</a>
            <a href="javascript:void(0)" class="toc-link toc-price" data-target="price">Pricing</a>
            <a href="javascript:void(0)" class="toc-link toc-developer" data-target="developer">Developer Background</a>
            <a href="javascript:void(0)" class="toc-link toc-faq" data-target="faq">FAQs</a>
        </div>
    </div>
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog popupFormHome">
            <div class="modal-content p-3">
                <div class="modal-header border-0">
                    <img src="{{asset('frontend/360logo.png')}}" alt="360propguide" class="w-50 mx-auto">

                    <button type="button" class="btn-close align-self-start ms-0 shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <h5 class="text-center">You can Count on us for Great Deals!</h5>
                <div class="modal-body">

                    <form method="POST" id="popupFormDownload" action="{{ route('popup.download') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="project_id" value="{{ $projects->id }}">
                        <input type="hidden" name="download_type" id="downloadType">
                        <div class="mb-3">

                            <input type="text" class="form-control shadow-none" name="name" placeholder="Name*">
                            <span class="text-danger error-name"></span>
                        </div>
                        <div class="mb-3">

                            <input type="tel" class="form-control shadow-none" name="mobile" placeholder="Mobile*">
                            <span class="text-danger error-mobile"></span>
                        </div>
                        <div class="mb-3">

                            <input type="email" class="form-control shadow-none " name="email" placeholder="Email*">
                            <span class="text-danger error-email"></span>
                        </div>
                        <div class="mb-3">

                            <textarea name="message" class="form-control shadow-none" placeholder="Message"></textarea>

                        </div>
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
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

    $(document).on('click', '.download-btn', function () {
        let type = $(this).data('type');
        let projectId = $(this).data('project-id');

        $('#downloadType').val(type);
        $('input[name="project_id"]').val(projectId);
    });

    $(document).on("submit", "#popupFormDownload", function (e) {
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
            success: function (response) {
                submitBtn.prop("disabled", false).text("Submit");

                // Redirect to thankyou page with session
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function (xhr) {
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
    swiper2.on("slideChange", function () {
        let activeIndex = swiper2.activeIndex;
        swiper.slideTo(activeIndex); // shift thumbs to keep active one visible
    });

    document.querySelectorAll('.TOC a[data-target]').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            const targetId = link.getAttribute('data-target'); // Get the target section ID
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.section');
        const links = document.querySelectorAll('.TOC a[data-target], .mobile-toc a[data-target]');

        const observer = new IntersectionObserver(
            (entries) => {
                const visibleSections = entries
                    .filter((entry) => entry.isIntersecting && entry.intersectionRatio >= 0.8)
                    .sort((a, b) => a.target.getBoundingClientRect().top - b.target.getBoundingClientRect().top);

                if (visibleSections.length) {
                    const topSection = visibleSections[0].target;
                    const targetId = topSection.id;

                    // Remove active from all
                    links.forEach((link) => link.classList.remove('active'));

                    // Add active on current link
                    const activeLinks = document.querySelectorAll(`.TOC a[data-target="${targetId}"], .mobile-toc a[data-target="${targetId}"]`);
                    activeLinks.forEach((link) => {
                        link.classList.add('active');

                        //  Mobile TOC auto-scroll
                        if (link.closest('.mobile-toc')) {
                            link.scrollIntoView({
                                behavior: "smooth",
                                block: "nearest",
                                inline: "center"
                            });
                        }
                    });
                }
            },
            {
                root: null,
                threshold: [0.8],
            }
        );

        sections.forEach((section) => observer.observe(section));
    });
    document.querySelectorAll('.toc-link').forEach(link => {
        link.addEventListener('click', function () {
            let targetId = this.getAttribute('data-target');
            let targetEl = document.getElementById(targetId);

            if (targetEl) {
                targetEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

    const projectBox = document.querySelector('.project-box');
    if (!projectBox) return;

    const projectId = projectBox.id.split('_')[1];

    const sqftPriceData = JSON.parse(projectBox.dataset.sqftprice || '[]');
    const priceValue = parseFloat(sqftPriceData[0]?.value || 0);

    const bspEl = document.getElementById('bsp_value_' + projectId);
    const totalEl = document.getElementById('total_value_' + projectId);

    document.querySelectorAll('#floor_tabs_' + projectId + ' .floor-btn').forEach(btn => {

        btn.addEventListener('click', function () {

            document.querySelectorAll('#floor_tabs_' + projectId + ' .floor-btn')
                .forEach(b => b.classList.remove('active'));

            this.classList.add('active');

            const superArea = parseFloat(this.dataset.superArea || 0);
            const totalValue = superArea * priceValue;

            bspEl.textContent = `₹${priceValue.toLocaleString('en-IN')}/sq.ft`;
            totalEl.textContent = `₹ ${Number(totalValue / 10000000).toFixed(2)} Cr`;
        });

    });

});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.rera-toggle').forEach(function (toggle) {
            const panel = toggle.closest('.position-relative') ? toggle.closest('.position-relative').querySelector('.rera-panel') : toggle.parentElement.querySelector('.rera-panel');
            if (!panel) return;
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                if (panel.classList.contains('pinned')) {
                    panel.classList.remove('active', 'pinned');
                } else {
                    document.querySelectorAll('.rera-panel.active').forEach(function (p) {
                        if (p !== panel) {
                            p.classList.remove('active', 'pinned');
                        }
                    });
                    panel.classList.add('active', 'pinned');
                }
            });
            toggle.addEventListener('mouseenter', function () {
                if (!panel.classList.contains('pinned')) {
                    document.querySelectorAll('.rera-panel.active').forEach(function (p) {
                        if (p !== panel && !p.classList.contains('pinned')) {
                            p.classList.remove('active');
                        }
                    });
                    panel.classList.add('active');
                }
            });

            toggle.addEventListener('mouseleave', function () {
                if (!panel.classList.contains('pinned')) {
                    panel.classList.remove('active');
                }
            });
        });
        document.addEventListener('click', function (e) {
            if (e.target.closest('.rera-close')) {
                const p = e.target.closest('.rera-panel');
                if (p) {
                    p.classList.remove('active', 'pinned');
                }
                return;
            }
            if (!e.target.closest('.rera-panel') && !e.target.closest('.rera-toggle')) {
                document.querySelectorAll('.rera-panel.active').forEach(function (p) {
                    p.classList.remove('active', 'pinned');
                });
            }
        });
    });
</script>
@endSection

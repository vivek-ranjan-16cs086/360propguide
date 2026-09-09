@extends('frontend.layouts.app')
@php
    $seoData = is_array($property->seo_data)
        ? $property->seo_data
        : json_decode($property->seo_data ?? '{}', true);

    $gallery = is_array($property->galleries)
        ? $property->galleries
        : json_decode($property->galleries ?? '[]', true);

    $propertyDescription = '';

    if (!empty($seoData['meta_description'])) {
        $propertyDescription = $seoData['meta_description'];
    } elseif (is_array($property->property_details)) {
        $propertyDescription = \Illuminate\Support\Str::limit(
            strip_tags(implode(' ', $property->property_details)),
            200
        );
    }
@endphp
@section('title', $property->seo_data['meta_title'])
@section('description', $property->seo_data['meta_description'])
@section('canonical', url()->current())
@section('og_image', url('storage/' . ($gallery[0] ?? 'frontend/favicon.jpg')))


@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/project-detail.css')}}" />
<script type="application/ld+json">
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "RealEstateListing",

    "name" => $property->title,

    "url" => route('property.details', $property->slug),

    "description" => $propertyDescription,

    "image" => 
         url('storage/' . $gallery[0])
        ,

    "datePosted" => optional($property->created_at)->format('Y-m-d'),
	"dateModified" => optional($property->updated_at)->format('Y-m-d'),

    "offers" => [
        "@type" => "Offer",
        "price" => (string) $property->total_price,
        "priceCurrency" => "INR",
        "availability" => "https://schema.org/InStock",
        "businessFunction" => strtolower($property->listing_type ?? '') === 'rent'
            ? "https://purl.org/goodrelations/v1#LeaseOut"
            : "https://purl.org/goodrelations/v1#Sell"
    ],

    "address" => [
        "@type" => "PostalAddress",
        "addressLocality" => $property->city,
        "addressRegion" => "Uttar Pradesh",
        "addressCountry" => "IN"
    ],

    "floorSize" => [
        "@type" => "QuantitativeValue",
        "value" => $property->area,
        "unitCode" => strtoupper($property->area_unit ?? '') === 'SQFT'
            ? 'FTK'
            : ($property->area_unit ?? 'FTK')
    ],

    "numberOfRooms" => (int) preg_replace('/[^0-9]/', '', $property->configuration ?? ''),

    "additionalProperty" => [
        [
            "@type" => "PropertyValue",
            "name" => "furnishingStatus",
            "value" => ucwords(str_replace('_', ' ', $property->furnishing_types ?? ''))
        ],
        [
            "@type" => "PropertyValue",
            "name" => "propertyType",
            "value" => ucwords($property->property_type ?? '')
        ],
        [
            "@type" => "PropertyValue",
            "name" => "constructionStatus",
            "value" => ucwords(str_replace('_', ' ', $property->construction_status ?? ''))
        ],
        [
            "@type" => "PropertyValue",
            "name" => "listingType",
            "value" => ucwords($property->listing_type ?? '')
        ]
    ],

    "seller" => [
        "@type" => "RealEstateAgent",
        "name" => "360 PropGuide",
        "telephone" => "+919643020020",
        "url" => "https://www.360propguide.com"
    ]

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endSection 
@section('content')
<!-- hero section -->
<div class="project-HeroSection container pt-5"> 
    <div class="row mb-4">
        <div class="mb-3 col-md-6">
            <h1 class="h3">{{$property->title}}</h1>
            <p class="mb-0">
                By <span class="text-primary">{{$property->project->developer_name}}</span>
            </p>
            <p class="text-secondary mb-0">{{$property->project->location}}</p>
        </div>
        
        <div class="mb-md-0 ms-auto col-md-auto text-lg-end text-sm-end">
            <p class="h3 ">₹{{ formatPrice($property->total_price) }}</p>
            <p class="text-secondary">₹{{ formatPrice($property->price_per_sqft) }} / {{ $property->area_unit }}</p>
			
			<div class="d-flex  mb-3 mb-md-0 w-100 ms-0">
		
		<div>
          <a rel="noopener noreferrer" target="_blank" href="tel:+919643020020" class="btn customBtn rounded-3">Contact Sales</a>
        </div>
        <div class="ms-3 d-lg-none">
          <button type="button" class="btn customBtn rounded-3" data-bs-toggle="modal" data-bs-target="#contactModal">
            Enquire Now
          </button>
        </div>
		
            <div class="dropdown-wrapper ps-3 mt-2">
                <a href="#" class="profile-button" id="userDropdown" tabindex="0">
                    <i class="mx-2 fa-solid fa-arrow-up-from-bracket"></i><span>Share</span>

                </a>
                <ul class="customDropdown">
                    <li>
                        <a class="dropdown-item dropdownLink" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('properties/' . $property->slug)) }}"
                            target="__blank">
                            <i class="fa-brands fa-facebook fs-3 pe-3"></i>Facebook
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item dropdownLink" href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url('properties/' . $property->slug)) }}"
                            target="__blank">
                            <i class="fa-brands fa-linkedin fs-3 pe-3"></i>LinkedIn
                        </a>
                    </li>
                    <li><a class="dropdown-item dropdownLink" href="https://wa.me/?text={{ urlencode(url('properties/' . $property->slug)) }}" target="__blank">
                            <i class="fa-brands fa-square-whatsapp fs-3 pe-3"></i>WhatsApp
                        </a>
                    </li>
                    <li><a class="dropdown-item dropdownLink" href="https://twitter.com/intent/tweet?url={{ urlencode(url('properties/' . $property->slug)) }}"
                            target="__blank">
                            <i class="fa-brands fa-square-x-twitter fs-3 pe-3"></i>Twitter
                        </a>
                    </li>
                    <li><a class="dropdown-item dropdownLink" href="#" onclick="copyToClipboard('{{ url('properties/' . $property->slug) }}'); return false;"
                            title="Copy Link">
                            <i class="fa-solid fa-link fs-5 pe-3"></i>Copy Link
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        </div>
    </div>

		{{-- Desktop Grid (md and up) --}}
	<div class="row d-none d-md-flex">
		@foreach($finalImages as $index => $image)
			@if ($index === 0)
				<div class="col-md-6 aspect2-1 mb-3">
					<img class="w-100 h-100 object-fit-cover rounded-start-3" 
						 src="{{ url('storage/' . $image) }}" 
						 alt="{{ $property->project_name }}" />
				</div>
				<div class="col-md-6">
					<div class="row h-100">
			@elseif($index > 0)
				<div class="col-6 mb-3 aspect2-1">
					<img class="w-100 h-100 
						@if ($index == 2) rounded-3 rounded-start-0 rounded-bottom-0 
						@elseif ($index == 4) rounded-3 rounded-start-0 rounded-top-0 
						@endif"
						src="{{ url('storage/' . $image) }}" 
						alt="{{ $property->project_name }}" />
				</div>
			@endif
		@endforeach
					</div>
				</div>
	</div>

	<!-- slider -->
	<div id="propertyCarousel{{ $property->id }}" class="carousel slide d-md-none" data-bs-ride="carousel">
		<div class="carousel-inner">
			@foreach($finalImages as $index => $image)
				<div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
					<img src="{{ url('storage/' . $image) }}" 
						 class="d-block w-100 rounded-3" 
						 alt="{{ $property->project_name }}">
				</div>
			@endforeach
		</div>
		<button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel{{ $property->id }}" data-bs-slide="prev">
			<span class="carousel-control-prev-icon custom-arrow"></span>
		</button>
		<button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel{{ $property->id }}" data-bs-slide="next">
			<span class="carousel-control-next-icon custom-arrow"></span> 
		</button>
	</div>


</div>
<div class="container">
    <div class="row my-4 justify-content-between">
        <div class="col-lg-7 col-md-12 order-2 order-lg-1">
            <div>
                <!-- summary -->
                <div class="section" id="summary">
                    <p class="h4 mb-4 text-primary fw-bold ">
                        Property Overview
                    </p>
                    <div class="row py-3 py-lg-0 h-100 align-content-around">

                        {{-- Project Name --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Project Name</p>
                                </div>
                                <div>
                                    <a href="{{ url('projects', $property->project->slug) }}" target="_blank" class="text-primary text-decoration-underline">
                                        {{ $property->project->project_name ?? 'N/A' }}</a>
                                </div>
                            </div>
                        </div>

                          <!-- Brokerage -->
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Brokerage</p>
                                </div>
                                <div>
                                    <p class="">No Charge</p>
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="col-6 text-capitalize">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Price</p>
                                </div>
                                <div>
                                    <p class="">₹{{ formatPrice($property->total_price ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Avg Price -->
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Avg Price</p>
                                </div>
                                <div>
                                    <p class="">₹{{ formatPrice($property->price_per_sqft ?? 0) }} / {{ $property->area_unit ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                          <!-- Configuration -->
                        <div class="col-6 text-capitalize">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Configuration</p>
                                </div>
                                <div>
                                    <p class="text-uppercase">{{ clean($property->configuration ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </div>

                          <!-- Bathrooms -->
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Bathrooms</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['bathroom'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Parking --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Parking</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['parking'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Balcony --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Balcony</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['balcony'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Construction Status --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Construction Status</p>
                                </div>
                                <div>
                                    <p class="">{{ ucfirst(str_replace('_', ' ', $property->construction_status ?? 'N/A')) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Furnishing --}}
                        <div class="col-6 text-capitalize">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Furnishing</p>
                                </div>
                                <div>
                                    <p class="">{{ str_replace('_', ' ', $property->furnishing_types ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- floor --}}
                        <div class="col-6 text-capitalize">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Floor</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['floor_no'] ?? 'N/A' }} out of {{ $property->advanced_details['total_floors'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- facing --}}
                        <div class="col-6 text-capitalize">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Facing</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['facing'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Age of Property --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Age of Property</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->advanced_details['property_age'] ?? 'N/A' }} Years Old</p>
                                </div>
                            </div>
                        </div>

                        {{-- Area --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Area</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->area ?? 'N/A' }} {{ $property->area_unit ?? '' }}</p>
                                </div>
                            </div>
                        </div>


                        {{-- Added --}}
                        <div class="col-6">
                            <div class="">
                                <div class="d-flex">
                                    <p class="text-secondary mb-1">Added</p>
                                </div>
                                <div>
                                    <p class="">{{ $property->created_at->diffForHumans() ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
				<!-- About Property -->
                <div class="about-listing section">
                    <div class="h4 my-4 text-primary fw-bold">About this Property</div>
                    
                    <p>{{ $property->advanced_details['description'] ?? 'N/A' }}</p>
                </div> 
                <!-- location -->
                @if(!empty($property->project->location_video))
				<div class="section" id="location">
					<div class="h4 my-4 text-primary fw-bold">Location</div>

					<video
						src="{{ 'https://360propguide.com/storage/'.$property->project->location_video }}"
						class="w-100"
						autoplay
						muted
						controls>
					</video>

					@if(!empty($property->location_description))
						<p class="my-3">{!! $property->location_description !!}</p>
					@endif
				</div>
				@endif

                <!-- amenities -->
                <div class="amenities section" id="amenities">
                    <div class="h4 my-4 text-primary fw-bold">Amenities</div>
                    <div class="row">
                        @if(!empty($property->amenitiesDetails) && count($property->amenitiesDetails)>0)
                        @foreach($property->amenitiesDetails as $index => $amenity)
                        <div class="icon col-md-2 col-4">
                            <img src="{{'https://360propguide.com/storage/'.$amenity->image}}" alt="{{'360_propguide'.$amenity->name}}" />
                            <span>{{$amenity->name}}</span>
                        </div>
                        @endforeach
                        @endif

                    </div>
                </div>
                <!-- dev backgrounds -->
                <div class="possessions section" id="developer">
                    <div class="h4 my-4 text-primary fw-bold">About Developer</div>
                    <div class="row align-items-md-center mb-3">
                        @if (!empty($property->developerDetails))
                        @foreach ($property->developerDetails as $developer)
                        <div class="col-12 col-md-3">
                            <img src="{{'https://360propguide.com/storage/'.$developer->developer_logo}}" class="col-6 mx-auto col-md-12 d-block"
                                alt="{{$property->project_name}}" />
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
                                <div class="fw-bold text-black">{{$developer->ongoing_project}} +</div>
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
                    {!!$property->project->developer_background_dscp!!}
                </div>
                <div class="d-block d-lg-none ppc-form col-md-8 mx-auto col-lg-12">
                    <div class="row p-4">
                       @php
                        $initial = strtoupper(substr($user->name, 0, 1));
                    @endphp
					<p class="h4">Contact Seller</p>
					<div class='d-flex mb-2'>
                        <div class="custom-avatar bg-white text-primary me-3">{{ $initial }}</div>
						<div>
						<p class="text-capitalize mb-0">{{$user->name}}</p>
						<p class="mb-0">{{substr($user->phone_number, 0, 4) . str_repeat('X', 6)}}</p>
						</div>
					</div>
					<p>Please share your contact</p>
                        <form method="POST" action="{{route('contact-mail')}}" class="popupForm">
                            @csrf
                            <input type="hidden" name="formName" value="popup" />
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" class="form-control error commonerr" name="name" placeholder="Name" />
                            </div>
							<div class="error-name text-danger"></div>
                            <div class="mt-3 input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" placeholder="Email Address*" id="email" name="email" class="form-control"
                                     />
                            </div>
							<div class="error-email text-danger"></div>
                            <div class="mt-3 input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
								<input type="tel" name="mobile" maxlength="10" class="form-control only-numeric" pattern="\d{10}" placeholder="Mobile*" >
                            </div>
							<div class="error-mobile text-danger"></div>
                            <div class="mt-3 form-group">
                                <textarea type="text" placeholder="Message" id="message" name="message" class="form-control" row="5"
                                    col="1"></textarea>
                            </div>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
							<div class="error-recaptcha text-danger"></div>
                            @if ($errors->has('recaptchaform3'))
                            <div class="alert alert-danger">
                                {{ $errors->first('recaptchaform3') }}
                            </div>
                            @endif
                            <div class="w-full mt-3">
                                <button type="submit" class="btn orange text-white w-100">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-10 order-1 order-lg-2 mx-auto me-lg-0 ms-lg-auto project-right">
            <div class="d-none d-lg-block ppc-form col-md-8 mx-auto col-lg-12">
                <div class="row p-4">
                    @php
                        $initial = strtoupper(substr($user->name, 0, 1));
                    @endphp
					<p class="h4">Contact Seller</p>
					<div class='d-flex mb-2'>
                        <div class="custom-avatar bg-white text-primary me-3">{{ $initial }}</div>
						<div>
						<p class="text-capitalize mb-0">{{$user->name}}</p>
						<p class="mb-0">{{substr($user->phone_number, 0, 4) . str_repeat('X', 6)}}</p>
						</div>
					</div>
					<p>Please share your contact</p>
                    <form method="POST" action="{{route('contact-mail')}}" class="popupForm">  
                        @csrf
                        <input type="hidden" name="formName" value="popup" />
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" class="form-control error commonerr" name="name" placeholder="Name" />
                        </div>
						<div class="error-name text-danger"></div>
                        <div class="mt-3 input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" placeholder="Email Address*" name="email" class="form-control"  />
                        </div>
						<div class="error-email text-danger"></div>
                        <div class="mt-3 input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="tel" max="9999999999" name="mobile" maxlength="10" class="form-control only-numeric" pattern="\d{10}" placeholder="Mobile*" >
                        </div>
						<div class="error-mobile text-danger"></div>
                        <div class="mt-3 form-group">
                            <textarea type="text" placeholder="Message" name="message" class="form-control" row="5"
                                col="1"></textarea>
                        </div>
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
						<div class="error-recaptcha text-danger"></div>
                        @if ($errors->has('recaptchaform4'))
                        <div class="alert alert-danger">
                            {{ $errors->first('recaptchaform4') }}
                        </div>
                        @endif
                        <div class="w-full mt-3">
                            <button type="submit" class="btn orange text-white w-100">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-lg bg-white p-3 d-none d-lg-block mt-3">
                <div class="h4 text-primary fw-bold">Recommended</div> 
                @foreach($recommendedProjects as $recommended)
                <a class="col-12 d-block mb-3" href="{{ route('projects.details', $recommended->slug) }}">
                    <div>
                        <div class="d-flex border Recommended-card imgHover">
                            <div class="col-5 col-md-5 img-container position-relative">
                                <img alt="{{$recommended->project_name }}" loading="lazy" decoding="async" data-nimg="fill"
                                    class="h-100 object-fit-cover rounded-start-1" sizes="100vw"
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
        </div>
    </div>
</div>
@endSection

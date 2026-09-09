@extends('frontend.layouts.app')
@section('title', "Max Estates Sector 105 | Ultra Luxury Project in noida")
@section('description',"Max Estates Sector 105 Noida is new project. The project offers Luxury residential apartments and prime commercial spaces with world-class amenities.")
@section('keywords', "Max Estates, Max Estates 105, Max Estates Sector 105 Noida, Max Estates Sector 105 Noida Project, Max Project Sector 105 Noida, Max Estates In Sector 105 Noida, Max Sector 105 in Noida, Max Estates price list, Max Estates Brochure")

@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/static/max.css')}}">
@endsection
@section('content')
<!-- HERO SECTION -->
<section class="hero-section">
    <div class="overlay"></div>
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <!-- LEFT CONTENT -->
            <div class="col-lg-7 text-white hero-content">
                <div class="hero-lines">
                    <h1 class="fw-bold">Max Estate 105</h1>

                    <p class="locationss">
                        <i class="bi bi-geo-alt-fill"></i>
                        At Sector 105, Noida.
                    </p>

                    <span class="badge luxury-badge mb-3">
                        Signature 4 BHK Residences & Limited Penthouses
                    </span>

                    <ul class="features list-unstyled mt-4">
                        <li>Low-Density Luxury Living</li>
                        <li> Limited-Time Launch Advantages</li>
                        <li>Prime Location i.e. Sector 105</li>
                    </ul>

                    <div class="price-box mt-4">
                        <span>Start At :</span>
                        <strong>₹ 11 Cr*</strong>
                        <small>Onwards</small>
                    </div>
                </div>
            </div>

            <!-- RIGHT FORM -->
            <div class="col-lg-5 d-none d-md-block">
                <div class="contact-card">
                    <div class="contact-header">
                        Contact With Us!
                    </div>

                    <p class="contact-text">
                        Please fill out the form below, our expert will get back to you soon.
                    </p>

                    <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
					    @csrf
						<input type="hidden" name="formName" value="popup">
                        <div class="mb-3">
                            <input type="text" class="form-control shadow-none" placeholder="Name" name="name">
							<span class="text-danger error-name"></span>
                        </div>

                        <div class="mb-3">
                            <input type="email" class="form-control shadow-none" placeholder="Email " name="email">
							<span class="text-danger error-email"></span>
                        </div>
						<div class="mb-3">
                            <input type="tel"
                                class="form-control shadow-none"
                                name="mobile"
                                placeholder="Mobile">
                            <span class="text-danger error-mobile"></span>
                        </div>
                          <!--<div class="mb-3">
                            <textarea name="message"
                                class="form-control shadow-none"
                                placeholder="Message"></textarea>
                        </div>-->
                        <div class="g-recaptcha mb-3 mt-3" data-sitekey="{{ config('services.recaptcha.site_key') }}">
				
						</div>
						<span class="text-danger error-recaptcha"></span>
							@if ($errors->has('recaptchaform1'))
								<div class="alert alert-danger">
									{{ $errors->first('recaptchaform1') }}
								</div>
							@endif
                        <button type="submit" class="btn btn-callback w-100 submitButton">
                            GET CALL BACK
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
<section>
    <div class="col-sm-12 d-block d-md-none">
        <div class="contact-card-mobile">
            <h5 class="contact-header-mobile">
                Contact With Us!
            </h5>

            <p class="contact-text-mobile">
                Please fill out the form below, our expert will get back to you soon.
            </p>

            <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
			   @csrf
				<input type="hidden" name="formName" value="popup">
                <div class="mb-3">
                    <input type="text" class="form-control mobile-input" placeholder="Name" name="name">
					<span class="text-danger error-name"></span>
                </div>

                <div class="mb-3">
                    <input type="email" class="form-control mobile-input" placeholder="Email" name="email">
					<span class="text-danger error-email"></span>
                </div>

                <div class="mb-3">
                    <input type="tel" class="form-control mobile-input" placeholder="Mobile" name="mobile">
					<span class="text-danger error-mobile"></span>
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control mobile-input" placeholder="Message" name="message">
                </div>
				<div class="g-recaptcha mb-3 mt-3" data-sitekey="{{ config('services.recaptcha.site_key') }}">
				
						</div>
						<span class="text-danger error-recaptcha"></span>
							@if ($errors->has('recaptchaform1'))
								<div class="alert alert-danger">
									{{ $errors->first('recaptchaform1') }}
								</div>
							@endif
                <button type="submit" class="btn btn-callback w-100 submitButton">
                    GET CALL BACK
                </button>
            </form>
        </div>
    </div>
</section>
<!-- Introduction section -->
<section class="estate-intro">
    <div class="container">
        <div class="row align-items-center">

            <!-- LEFT CONTENT -->
            <div class="col-lg-6">
                <h2 class="estate-intro__title">
                    A New Address of Distinction — Max Estate 105
                </h2>

                <p class="estate-intro__text">
                    Max Estates Sector 105 is a superior mixed-use project located in Noida,
                    and it blends high-end residential and luxurious retail living in one masterpiece
                    development. The proposed project is exclusive, with ultra-luxury 4 BHK apartments
                    and penthouses with high-end shopping areas and food courts.
                </p>

                <p class="estate-intro__text">
                    The development will be located on a large land size of 10 acres
                    of land and will feature modern architecture with an 80 percent open
                    space layout, which will allow plenty of green space and serenity.
                    The project will have the best amenities and flexible payment options,
                    such as swimming pools, clubhouses, and landscaped gardens.
                </p>

                <button type="button" class="estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                    Download Brochure
                </button>
            </div>

            <!-- RIGHT IMAGES -->
            <div class="col-lg-6">
                <div class="estate-gallery">
                    <div class="estate-gallery__left">
                        <div class="estate-shot">
                            <img src="{{asset('frontend/Static-images/Gallery/122.jpg') }}" alt="">
                        </div>

                        <div class="estate-shot">
                            <img src="{{ asset('frontend/Static-images/Gallery/123.jpg') }}" alt="">
                        </div>
                    </div>

                    <div class="estate-gallery__right">
                        <div class="estate-shot estate-shot--tall">
                            <img src="{{ asset('frontend/Static-images/Gallery/124.jpg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>

<!-- priceList section -->

<section class="price-section">
    <div class="container">
        <h2 class="price-title text-uppercase">Price List</h2>

        <div class="price-grid">
            <div class="price-card">
                <h3 class="price-card__type">4 BHK</h3>

                <div class="price-row">
                    <span>Size</span>
                    <span>On request.</span>
                </div>

                <div class="price-row">
                    <span>Price</span>
                    <span>₹ On Request</span>
                </div>
                <button type="button" class="price-btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                    View Details
                </button>
            </div>

            <div class="price-card">
                <h3 class="price-card__type">Penthouse</h3>

                <div class="price-row">
                    <span>Size</span>
                    <span>On request.</span>
                </div>

                <div class="price-row">
                    <span>Price</span>
                    <span>₹ On Request</span>
                </div>

               <button type="button" class="price-btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                    View Details
                </button>
            </div>
            </div>
        </div>
    </div>
</section>

<!-- Amenities section -->
<section class="amenities-section">
    <div class="container">
        <div class="row">

            <!-- LEFT CONTENT -->
            <div class="col-lg-5">
                <h2 class="amenities-title">Elevated Amenities for Refined Living</h2>

                <p class="amenities-text">
                    Max Estate 105 is thoughtfully designed to offer a balanced, elevated living
                    experience, where wellness, recreation, and social engagement come together
                    seamlessly. Residents can unwind by the swimming pool, maintain an
                    active routine in the fully equipped fitness center, or rejuvenate in
                    dedicated yoga and meditation zones. The project also features an elegant
                    clubhouse, open-air Amphitheatre, and versatile multipurpose spaces for private gatherings
                    and community events. Inside the residences, expansive living and bedroom layouts are planned to maximize natural light, openness, and understated elegance.

                </p>

                <button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                    Know more
                </button>
            </div>

            <!-- RIGHT ICON GRID -->
            <div class="col-lg-7">
                <div class="amenities-grid">
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/badminton-court.png') }}" alt="">
                        <span>BADMINTON COURT</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_8_Clubhouse.png') }}" alt="">
                        <span>PARKING</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_9_parking.png') }}" alt="">
                        <span>JOGGING TRACK</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_10_Kids-play-area.png') }}" alt="">
                        <span>CLUBHOUSE</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_11_Pool.png') }}" alt="">
                        <span>KID’S PLAY AREA</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_12_jogging.png') }}" alt="">
                        <span>YOGA LAWN</span>
                    </div>
                    <div class="amenity-item">
                        <img src="{{ asset('frontend/Static-images/icon/imgi_13_yoga-lawn.png') }}" alt="">
                        <span>SWIMMING POOL</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Introduction section -->
<section class="estate-intro">
    <div class="container">
        <div class="row align-items-center">

            <!-- LEFT CONTENT -->
            <div class="col-lg-6">
                <h2 class="estate-intro__title">
                   Site Plan — Max Estate 105
                </h2>
                <p class="estate-intro__text">
                    The site plan of Max Estates 105 is built around a clear philosophy of low-density, planning-led luxury, where space, movement, and privacy are prioritized over unit maximization. The development features just two residential towers, strategically placed at a significant distance from each other to ensure openness, natural light, and unobstructed views. A key highlight is the car-free podium level, enabled through a lower-ground vehicular drop-off system, allowing residents to experience a safe, walkable, and disturbance-free environment at the surface level.
                </p>

                <button type="button" class="estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                    Download Brochure
                </button>
            </div>

            <!-- RIGHT IMAGES -->

            <div class="col-12 col-lg-6">
                <div class="highlight-image">
                    <img src="{{ asset('frontend/Static-images/max-floorplan/siteplan.jpeg') }}" class="img-fluid" alt="siteplan">
                </div>
            </div>


        </div>
    </div>
</section>

<section class="floor-plan py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="floor-title">FLOOR PLAN</h2>
                <p class="floor-subtitle">Elevating Properties: Our Spotlight Features</p>
            </div>
        </div>

        <div class="row g-0">

            <div class="col-lg-4 col-md-6 mb-2">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/4 BHK 3754.jpg.jpeg')}}" class="img-fluid" alt="Master Plan">
                    <div class="floor-overlay">
                        <!--<h4>4 BHK 3754</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-2">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/4 BHK FL.jpg.jpeg')}}" class="img-fluid" alt="4 BHK">
                    <div class="floor-overlay">
                        <!--<h4>4 BHK</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mx-md-auto">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/Duplex 6339.jpg.jpeg')}}" class="img-fluid" alt="Penthouse">
                    <div class="floor-overlay">
                        <!--<h4>Duplex 6339</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->
                    </div>
                </div>
            </div>

        </div>
        
        <div class="row g-0">

            <div class="col-lg-4 col-md-6 mb-2">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/Skyvilla 9821.jpg.jpeg')}}" class="img-fluid" alt="Master Plan">
                    <div class="floor-overlay">
                        <!--<h4>Skyvilla 9821</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-2">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/Townhouse 7008.jpg.jpeg')}}" class="img-fluid" alt="4 BHK">
                    <div class="floor-overlay">
                        <!--<h4>Townhouse 7008</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mx-md-auto">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/max-floorplan/Townhouse 7800.jpg.jpeg')}}" class="img-fluid" alt="Penthouse">
                    <div class="floor-overlay">
                        <!--<h4>Townhouse 7800</h4>-->
                        <!--<button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">-->
                        <!--    View deatils-->
                        <!--</button>-->
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<section class="schedule">
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-12 px-0">
                <div class="image-box">
                    <img src="{{asset('frontend/Static-images/imgi_30_visit.jpg')}}" alt="Schedule Visit">

                    <div class="text-overlay">
                        <h1 class="text-uppercase">Schedule A Visit</h1>
                        <hr class="horizontal">
                        <p>Max Estates 105 in Noida</p>
                        <hr class="horizontal">
                        <button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                            Know more
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="highlight py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="highlight-title">HIGHLIGHTS</h2>
            </div>
        </div>
        <div class="row align-items-center g-4">


            <div class="col-12 col-lg-6">
                <div class="highlight-text">
                    <p> Prime Central Noida Location – Sector 105</p>
                    <p>Seamless Connectivity to Noida Expressway</p>
                    <p>Large Consolidated Serene Green Landscapes</p>
                    <p>Spacious Luxury Residences with Refined Design</p>
                    <p> Institutional-Grade Development by Max Estates</p>
                </div>
            </div>


            <div class="col-12 col-lg-6">
                <div class="highlight-image">
                    <img src="{{asset('frontend/Static-images/imgi_6_highlight-img.jpg')}}" class="img-fluid" alt="Highlights">
                </div>
            </div>

        </div>

    </div>
</section>


<section class="location py-5">
    <div class="container location-box">


        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="location-title text-uppercase">Location Advantage</h2>
                <p class="location-subtitle">Prime Location Boosts Connectivity</p>
            </div>
        </div>


        <div class="row align-items-center g-4">


            <div class="col-12 col-lg-5">
                <div class="location-list">
                    <p> 2 Minutes from Noida–Greater Noida Expressway</p>
                    <p>8 Minutes to DND Delhi-Noida-Direct Flyway</p>
                    <p>12 Minutes to Film City, Sector 16A (Noida)</p>
                    <p>20 Minutes to Akshardham & Central Delhi</p>
                    <p>Approx. 30 Minutes to Noida International Airport</p>
                </div>
            </div>


            <div class="col-12 col-lg-7">
                <div class="location-map">
                    <iframe src="https://www.google.com/maps?q=Sector%20105%20Noida&output=embed" allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            </div>

        </div>

    </div>
</section>

<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content fp-bs-modal">

            <button type="button"
                class="btn-close fp-bs-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>

            <div class="row g-0">

                <!-- LEFT IMAGE -->
                <div class="col-lg-6 d-none d-lg-block fp-bs-left">
                    <img src="{{asset('frontend/Static-images/imgi_6_highlight-img.jpg')}}"
                        alt="Max Estates 105">
                </div>

                <!-- RIGHT FORM -->
                <div class="col-lg-6 fp-bs-right">

                    <h3>Max Estates 105</h3>
                    <p class="location-text">
                        <i class="fa fa-map-marker"></i> At Sector 105, Noida
                    </p>

                    <form method="POST" class="popupForm" action="{{route('contact-mail')}}">
                        @csrf
                        <input type="hidden" name="formName" value="popup">

                        <div class="mb-3">
                            <input type="text"
                                class="form-control shadow-none"
                                name="name"
                                placeholder="Name*">
                            <span class="text-danger error-name"></span>
                        </div>

                        <div class="mb-3">
                            <input type="tel"
                                class="form-control shadow-none"
                                name="mobile"
                                placeholder="Mobile*">
                            <span class="text-danger error-mobile"></span>
                        </div>

                        <div class="mb-3">
                            <input type="email"
                                class="form-control shadow-none"
                                name="email"
                                placeholder="Email*">
                            <span class="text-danger error-email"></span>
                        </div>

                        <div class="mb-3">
                            <textarea name="message"
                                class="form-control shadow-none"
                                placeholder="Message"></textarea>
                        </div>
                        <div class="g-recaptcha mb-3 mt-3" data-sitekey="{{ config('services.recaptcha.site_key') }}">
				
				</div>
				<span class="text-danger error-recaptcha"></span>
				    @if ($errors->has('recaptchaform1'))
                        <div class="alert alert-danger">
                            {{ $errors->first('recaptchaform1') }}
                        </div>
                    @endif
                        <button type="submit"
                            class="btn fp-bs-submit w-100 submitButton">
                            Submit
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection



@section('customJs')
@endsection

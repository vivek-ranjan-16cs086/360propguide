@extends('frontend.layouts.app')
@section('title', "Experion Sector 151 offer Luxury living Apartments in Noida")
@section('description',"Experion Sector 151 Noida – Luxury residential Flats with modern amenities, green spaces, and excellent connectivity to Noida Expressway, and Delhi NCR.")
@section('keywords', "Experion 151, Experion sector 151 Noida, experion sector 151 in Noida, experion sector 151 Location, Experion sector 151 Project.")

@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/static/experion.css')}}">
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
                    <h1 class="fw-bold">Experion 151</h1>

                    <p class="locationss">
                        <i class="bi bi-geo-alt-fill"></i>
                        At Sector 151, Noida.
                    </p>

                    <span class="badge luxury-badge mb-3">
                        Premium 3 & 4 BHK Luxury Residences
                    </span>

                    <ul class="features list-unstyled mt-4">
                        <li> Low-Density Living </li>
                        <li>Expansive Open Spaces</li>
                        <li> Early-Phase Investment Opportunity</li>
                    </ul>

                    <div class="price-box mt-4">
                        <span>Start At :</span>
                        <strong>₹ 3.36 Cr*</strong>
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
                    A New Benchmark in Contemporary Living — Experion 151
                </h2>

                <p class="estate-intro__text">
                    Experion 151 is a thoughtfully planned luxury residential development located in Sector 151,
                    Noida, offering premium 3 and 4 BHK residences designed for modern urban lifestyles.
                    Developed by Experion Developers, the project focuses on creating a balanced living environment
                    that blends refined architecture, functional layouts, and generous open spaces.

                </p>

                <p class="estate-intro__text">
                    The development emphasizes low-density planning with wide internal roads,
                    landscaped green zones, and well-defined recreational areas, ensuring privacy and serenity for residents.
                    Each residence is designed with spacious interiors, large balconies, and optimal natural light,
                    catering to families seeking long-term comfort and quality living.
                    Experion 151 reflects a modern residential address that prioritizes lifestyle, space, and
                    thoughtful design over congestion.

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
                            <img src="{{asset('frontend/Static-images/Experion/experioni/117.png') }}" alt="">
                        </div>

                        <div class="estate-shot">
                            <img src="{{ asset('frontend/Static-images/Experion/experioni/118.png') }}" alt="">
                        </div>
                    </div>

                    <div class="estate-gallery__right">
                        <div class="estate-shot estate-shot--tall">
                            <img src="{{ asset('frontend/Static-images/Experion/experioni/119.png') }}" alt="">
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
                <h3 class="price-card__type">3 BHK</h3>

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
                    Experion 151 is designed to support a well-rounded lifestyle where leisure, wellness, and
                    community living coexist effortlessly. Residents have access to a modern clubhouse that serves
                    as a social and recreational hub, along with fitness and wellness spaces tailored for daily routines.
                    Landscaped gardens and open green areas provide calm outdoor spaces for relaxation, while
                    dedicated zones for children and families encourage active living.
                    The project integrates indoor and outdoor amenities seamlessly, ensuring
                    that residents can enjoy both private comfort within their homes and shared community spaces outside.
                    The overall planning focuses on enhancing everyday living rather than just offering amenities as add-ons.

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
                    <img src="{{asset('frontend/Static-images/Screenshot .PNG')}}" class="img-fluid" alt="Master Plan">
                    <div class="floor-overlay">
                        <h4>3 BHK</h4>
                        <button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                            View deatils
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-2">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/Screenshot .PNG')}}" class="img-fluid" alt="4 BHK">
                    <div class="floor-overlay">
                        <h4>4 BHK</h4>
                        <button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                            View deatils
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mx-md-auto">
                <div class="floor-card">
                    <img src="{{asset('frontend/Static-images/Screenshot .PNG')}}" class="img-fluid" alt="Penthouse">
                    <div class="floor-overlay">
                        <h4>Master PLAN</h4>
                        <button type="button" class="btn btn-light estate-intro__btn" data-bs-toggle="modal" data-bs-target="#contactModal">
                            View deatils
                        </button>
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
                        <p>Experion 151 in Noida</p>
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
                    <p> Prime Residential Location – Sector 151, Noida</p>
                    <p> Low-Density Development with Ample Open Areas</p>
                    <p>Premium 3 & 4 BHK Residences</p>
                    <p>Modern Architecture with Functional Design</p>
                    <p> Landscaped Greens and Community-Centric Planning</p>
                </div>
            </div>


            <div class="col-12 col-lg-6">
                <div class="highlight-image">
                    <img src="{{asset('frontend/Static-images/Experion/experioni/121.png')}}" class="img-fluid" alt="Highlights">
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
                    <p>Direct Access to Noida–Greater Noida Expressway</p>
                    <p>Close Proximity to Sector 148 Metro Station</p>
                    <p> Smooth Connectivity to South Delhi and Central Noida</p>
                    <p>Well-Connected to Business Hubs and IT Corridors</p>
                    <p>Convenient Access to Educational Institutions & Healthcare</p>
                </div>
            </div>


            <div class="col-12 col-lg-7">
                <div class="location-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d7016.106385941405!2d77.45749694328356!3d28.447812853064164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sexperion%20151%20noida!5e0!3m2!1sen!2sin!4v1768027926090!5m2!1sen!2sin"> </iframe>
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
                        alt="Max Estate 105">
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

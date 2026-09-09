@extends('frontend.layouts.app')
@section('title', "Get in Touch with 360 PropGuide – Expert Real Estate Help")
@section('description',"Reach out to 360 PropGuide for clear communication and expert advice on your real estate needs. We're here to help!")
@section('keywords', "")

@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/contact.css')}}">
@endSection
@section('content')
<div class="hero-bg text-center d-flex align-items-center justify-content-center">
    <h1 class="text-white">Contact Us</h1>
</div>

<div class="container mb-5 mt-5 bg-white">
    <div class="row py-3 justify-content-center">
        <div class="col-md-6">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d112095.11211970053!2d77.31144933906253!3d28.600609180281722!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cf100329ae605%3A0x57b379e3d9d56c8e!2s360%20PropGuide%20LLP!5e0!3m2!1sen!2sin!4v1738826918232!5m2!1sen!2sin" class="w-100 h-100"
                 style="border:0; min-height:300px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="col-md-5 shadow-lg p-5 ms-md-4 form-wrapper">
            <h4 class="mb-3">Have Questions? We're here to help!</h4>  
			<div class="alert alert-success success-message d-none">
				Your enquiry has been submitted successfully.
			</div> 
            <form class="contact-form ps-auto popupForm" method="POST" action="{{route('contact-mail')}}">
			@csrf
                  <input type="hidden" name="formName" value="popup">
                <div class="form-group input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" class="form-control error commonerr name" name="name" placeholder="Name" />
					
                </div>
				<span class="text-danger error-name mt-0"></span>
                <div class="form-group input-group mt-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" placeholder="Email Address*" id="email1" name="email" class="form-control email"
                        />
						
                </div>
				<span class="text-danger error-email"></span>
                <div class="form-group input-group mt-3">
                    <span class="input-group-text">
                        <i class="fa-solid fa-phone"></i>
                    </span>
                    <input type="tel" class="form-control mobile" name="mobile" placeholder="Mobile*" maxlength="10" pattern="[0-9]{10}" inputmode="numeric"
                        />
						
                </div>
				<span class="text-danger error-mobile"></span>
                <div class="form-group mt-3">
                    <textarea type="text" placeholder="Message" id="message1" name="message" class="form-control" row="5"
                        col="1"></textarea>
                </div>
				<div class="g-recaptcha mb-3 mt-3" data-sitekey="{{ config('services.recaptcha.site_key') }}">
				
				</div>
				<span class="text-danger error-recaptcha"></span>
				    @if ($errors->has('recaptchaform1'))
                        <div class="alert alert-danger">
                            {{ $errors->first('recaptchaform1') }}
                        </div>
                    @endif
                <div class="w-full">
                    <button type="submit" class="btn w-100 orange text-white submitButton">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endSection
@extends('frontend.layouts.app')

@section('title', "Buy Ability Calculator | Check Your Property Buying Ability")
@section('description', "Explore Buy Ability Calculator to check your property buying ability instantly. Estimate budget, loan eligibility, EMI capacity, and plan smarter real estate decisions.")
@section('canonical', url()->current())
@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/tools/ability.css')}}">
@endsection
@section('content')
<section class="ability py-5">
    <div class="container bg-white p-4 rounded shadow-sm">
        <div class="row justify-content-center">
            <div class="bord col-md-10 col-lg-8">

                <div class="text-center mb-3">
                    <h2>Find properties that fit your budget</h2>
                </div>

                <div class="subline-text text-center mb-4">
                    <p>
                        At 360 Propguide, we believe your dream home shouldn’t stay just a dream.
                        Whether you’re looking for affordable plots, modern apartments, or
                        premium investments — explore properties that perfectly match your budget
                        and lifestyle. Browse our exclusive listings to find the right property at
                        the right price. From compact city apartments to spacious family homes, we
                        have options tailored for every buyer. Let’s make your real estate journey
                        easy and smart.
                    </p>
                </div>

                <div class="text-center">
                    <a href="{{ route('projects') }}" class="btn customBtn px-4 rounded-2 p-2">
                        Explore Projects
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>


@endsection
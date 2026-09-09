@extends('frontend.layouts.app')

@section('title', 'add property ')

@section('content')
<div class="container py-5">


    <div class="row justify-content-center">

        @include('frontend.properties.steps.partials.sidebar')

        {{-- Step Content Area --}}
        <div class="col-md-7 col-lg-8">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="card p-4 p-lg-5  border-0 shadow-lg border-secondary-subtle multiStepForm">



                <h3 class="mb-4">Add Basic Details</h3>

                <form method="POST" action="{{route('postproperty.create.save')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class=" mb-4">
                            <p class="text-secondary">Property Type</p>
                            <!-- Radio button for apartment -->
                            <input type="radio" class="btn-check" name="property_type" value="apartment" id="apartment" autocomplete="off">
                            <label class="radio-btn square me-3" for="apartment"><i class="fa-solid fa-building d-block text-center fs-4 mb-2 text-lightgrey"></i>Apartment</label>
                            <!-- Radio button for plot -->
                            <input type="radio" class="btn-check" name="property_type" value="plots" id="plot" autocomplete="off">
                            <label class="radio-btn square" for="plot"><i class="fa-solid fa-house-chimney d-block text-center fs-4 mb-2 text-lightgrey"></i>Plot</label>
                            @error('property_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" mb-4">
                            <p class="text-secondary">Listing Type</p>
                            <!-- Radio button for apartment -->
                            <input type="radio" class="btn-check" name="listing_type" value="sale" id="sale" autocomplete="off">
                            <label class="radio-btn me-3 px-4 py-2" for="sale">Sale</label>
                            <!-- Radio button for plot -->
							  <span id="rent-option">
                            <input type="radio" class="btn-check" name="listing_type" value="rent" id="rent" autocomplete="off">
                            <label class="radio-btn px-4 py-2" for="rent">Rent</label>
                            </span>
                            @error('listing_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-4">
                            <p class="text-secondary">City</p>

                            <div class="form-floating">

                                <select class="form-select shadow-none input @error('cities') is-invalid @enderror"
                                    name="cities" required>
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
									<option value="{{ ucwords(strtolower(trim($city))) }}"
										{{ old('cities') == ucwords(strtolower(trim($city))) ? 'selected' : '' }}>

										{{ ucwords(strtolower(trim($city))) }}
									</option>
									@endforeach
                                </select>
                                <label for="cities">City</label>
                            </div>
                            @error('cities')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn customBtn">Proceed</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const apartmentRadio = document.getElementById("apartment");
        const plotRadio = document.getElementById("plot");
        const rentOption = document.getElementById("rent-option");

        function toggleRentVisibility() {
            if (plotRadio.checked) {
                rentOption.style.display = "none";
            } else {
                rentOption.style.display = "inline-block"; // or "block" based on your layout
            }
        }

        // Run on load
        toggleRentVisibility();

        // Add event listeners
        apartmentRadio.addEventListener("change", toggleRentVisibility);
        plotRadio.addEventListener("change", toggleRentVisibility);
    });
</script>

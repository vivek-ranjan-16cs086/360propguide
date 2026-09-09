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

            <h4 class="mb-4">Step 5: Review Your Property </h4>


            {{-- Basic Information --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-semibold">Basic Information</div>
                <div class="card-body">
                    <p><strong>Project:</strong> {{ optional($property->project)->project_name ?? '-' }}</p>
                    <p><strong>City:</strong> {{ $property->city }}</p>
                    <p><strong>Property Type:</strong> {{ ucfirst($property->property_type) }}</p>
                    <p><strong>Listing Type:</strong> {{ ucfirst($property->listing_type) }}</p>
                    <p><strong>Configuration:</strong> {{ ucfirst($property->configuration) }}</p>
                    <p><strong>Construction Status:</strong> {{ ucfirst($property->construction_status) }}</p>
                    <p><strong>Total Price:</strong> ₹{{ number_format($property->total_price) }}</p>  
                    <p><strong>Area:</strong> {{ $property->area }} {{ $property->area_unit }}</p>
                    <p><strong>Price per unit:</strong> ₹{{ round($property->total_price / $property->area) }} / {{ $property->area_unit }}</p>
                    <p><strong>Furnishing:</strong> {{ ucfirst($property->furnishing_types) }}</p>
                </div>
            </div>


            {{-- advanced details --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-semibold">Advanced Details</div>
                <div class="card-body">
                    @foreach($property->advanced_details ?? [] as $key => $value)
                    <p><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                    @endforeach
                </div>
            </div>

            {{-- Amenities --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-semibold">Amenities</div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($amenities as $amenity) 
                        <div class="w-fit d-flex align-items-center mb-2">
                            <img src="{{ 'https://www.360propguide.com/storage/' . $amenity->image }}" alt="{{ $amenity->name }}" class="me-2" width="40" height="40">
                            <span>{{ $amenity->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{-- Gallery --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-semibold">Gallery</div>
                <div class="card-body">
                    <div class="row">
                        @foreach($property->galleries ?? [] as $image)
                        <div class="col-md-3 mb-3">
                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded shadow-sm border">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{-- Submit --}}
            <form action="{{ route('postproperty.edit.submit', $property->property_uid) }}" method="POST" class="text-end mt-4">
                @csrf
                <button type="button" class="btn btn-secondary px-4 me-2 me-mb-3" onclick="history.back()">Back</button>
                <button type="submit" class="btn customBtn px-4">Submit for Review</button>
            </form>

        </div>
    </div>
</div>
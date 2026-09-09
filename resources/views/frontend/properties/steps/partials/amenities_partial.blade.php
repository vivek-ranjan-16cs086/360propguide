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
            <div class="px-md-4">
                <h4 class="mb-4">Step 3: Amenities</h4>

                <form id="stepForm" action="{{ route('postproperty.edit.amenities.save', $property->property_uid) }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-4">
                        <p class="text-secondary">Select Amenities</p>
                        @foreach($amenities as $amenity)
                        @php
                        $selectedAmenities = old('amenities', $property->amenities ?? []);
                        $isChecked = in_array((string) $amenity->id, $selectedAmenities); // compare as string
                        @endphp

                        <input data-label="Amenities" class="btn-check" type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                            id="amenity_{{ $amenity->id }}" {{ $isChecked ? 'checked' : '' }} required>
                        <label class="radio-btn me-3 mb-3" for="amenity_{{ $amenity->id }}">
                            <img src="{{ 'https://www.360propguide.com/storage/' . $amenity->image }}" class="me-2 amenityImg" alt="{{ $amenity->name }}" />
                            {{ $amenity->name }}
                        </label>
                        @endforeach
						<div class="invalid-feedback d-block mb-3"></div>
                        @error('amenities')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-secondary px-4 me-2 me-mb-3" onclick="history.back()">Back</button>
                    <button type="submit" class="btn customBtn px-4">Next</button>

                </form>

            </div>
        </div>
    </div>
</div>
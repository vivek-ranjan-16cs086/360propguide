@foreach($properties as $property)
<div class="property-card mb-3"
     data-type="{{ $property->property_type }}"
     data-bhk="{{ $property->configuration }}"
     data-price="{{ $property->total_price }}"
     data-location="{{ $property->city }}">

    <!-- Property Images -->
    <div class="property-image">
    <div class="slider-container">
        <div id="propertyCarousel{{ $property->id }}"   
             class="carousel slide slider" 
             data-bs-ride="carousel" 
             data-bs-interval="2000">
             
            <div class="carousel-inner">
                @if(!empty($property->galleries) && count($property->galleries) > 0)
                    @foreach($property->galleries as $key => $gallery)
                        <div class="carousel-item slide {{ $key === 0 ? 'active' : '' }}">
                            <img src="{{ url('storage/'.$gallery) }}" 
                                 
                                 alt="{{ $property->title }}">
                        </div>
                    @endforeach
                @else
                    <div class="carousel-item slide active">
                        <img src="{{ asset('images/no-image.jpg') }}" 
                             
                             alt="No Image">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- Property Details -->
    <div class="property-details">
        <a href="{{ route('property.details', $property->slug) }}"> 
            <h2 class="property-title m-0">{{ $property->title }}
			</h2>
        </a>

        <div class="location">
            <i class="fa-solid fa-location-dot"></i>
            {{ $property->city }}
        </div>

        @php
			function formatText($text) {
				return ucwords(str_replace('_', ' ', $text));
			}
		@endphp

		<div class="info-blocks">
			@if($property->furnishing_types)
				<p class="info-item m-0">
					<i class="fa-solid fa-couch"></i> {{ formatText($property->furnishing_types) }}
				</p>
			@endif

			@if($property->configuration)
				<p class="info-item m-0">
					<i class="fa-solid fa-building"></i> {{ formatText($property->configuration) }}
				</p>
			@endif

			@if($property->listing_type)
				<p class="info-item m-0">
					<i class="fa-solid fa-hand-holding-dollar"></i> {{ formatText($property->listing_type) }}
				</p>
			@endif
		</div>

        <div class="location-details">
		    <p class="m-0">
            <i class="fa-solid fa-location-arrow"></i>
            {{ $property->construction_status}}
			</p>
        </div>
    </div>

    <!-- Price Section -->
    <div class="price-section">
        <div class="price-info">
            <div><p class="price m-0">{{ formatPrice($property->total_price) }}</p></div>
            <div class="price-details">
                {{ $property->listing_type === 'rent' ? 'per month' : 'total price' }}
            </div>
            @if($property->price_details)
                <div class="other-charges">+ See other charges</div>
            @endif
        </div>
        <div class="button-group">
            <a href="tel:+919643020020" class="btn contact-owner rounded">
				<i class="fa-solid fa-phone"></i> Contact
			</a>

            <button class="btn check-availability rounded" data-bs-toggle="modal" data-bs-target="#contactModalPopup">
                <i class="fa-solid fa-calendar-check"></i> Availability
            </button>
        </div>
    </div>
</div>
@endforeach
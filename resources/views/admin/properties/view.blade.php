@extends('admin.app')
@section('title', $title)
@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
@endsection

@section('content')
{{-- Basic Information --}}
{!! $breadcrumbHtml !!}
<div class="card mb-4 shadow-sm">
    <div class="card-header fw-semibold">Basic Information</div>
    <div class="card-body">
        <p><strong>Project:</strong> {{ optional($property->project)->project_name ?? '-' }}</p>
        <p><strong>City:</strong> {{ $property->city }}</p>
        <p><strong>Property Type:</strong> {{ ucfirst($property->property_type) }}</p>
        <p><strong>Listing Type:</strong> {{ ucfirst($property->listing_type) }}</p>
        <p><strong>Configuration:</strong> {{ formatToString($property->configuration) }}</p>
        <p><strong>Construction Status:</strong> {{ ucfirst($property->construction_status) }}</p>
        <p><strong>Total Price:</strong> ₹{{ formatPrice($property->total_price) }}</p>
        <p><strong>Area:</strong> {{ $property->area }} {{ $property->area_unit }}</p>
        <p><strong>Price per unit:</strong> ₹{{ round($property->total_price / $property->area) }} / {{ $property->area_unit }}</p>
        <p><strong>Furnishing:</strong> {{ formatToString($property->furnishing_types) }}</p>
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

{{-- Action Buttons --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body d-flex justify-content-end gap-2">
        <a href="{{ route('properties.approve', $property->id) }}"
           class="btn btn-success mr-2">
            Accept
        </a>
        <a href="{{ route('properties.reject', $property->id) }}"
           class="btn btn-danger mr-2">
            Reject
        </a>
        <a href="{{ route('properties.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>
</div>

@endsection

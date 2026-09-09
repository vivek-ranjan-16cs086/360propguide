@extends('frontend.layouts.app')

@section('content')
@if (session('success'))
<div class="position-relative">
    <div class="toast-container top-0 end-0 p-3 overflow-hidden">
        <div class="toast-show toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
@endif


<div class="container py-5">
    <div class="row">
        <div class="col-lg-3 col-md-5 d-none d-md-block">
            <div class="property-guide">
                <div class="p-4 bg-light rounded shadow-sm">
                    <h4 class="mb-3">Why List with 360PropGuide?</h4>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">✔️ Zero brokerage</li>
                        <li class="mb-2">✔️ Maximum Visibility</li>
                        <li class="mb-2">✔️ Real-Time Leads</li>
						<li class="mb-2">✔️ Dedicated Support</li>
                        <li class="mb-2">✔️ Smart Dashboard</li>
                    </ul>
                </div>
                <div class="p-4 bg-light rounded shadow-sm">
                    <p>360PropGuide is a client-centered real estate consultant based on transparency and a customer-first approach. We have objective advice, trusted listings, and full support: every property choice is educated, seamless, and safe. This makes 30+ lakh people who rely on us to make confident and clear decisions during real estate matters.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Listed Property ({{$properties->count()}})</h3>
                <a href="{{ route('postproperty.create') }}" class="btn customBtn float-end"><i class="fa-solid fa-plus me-2"></i>Add Property</a>
            </div>
            @if($properties->count())
            <div class="row g-4">
                @foreach($properties as $property)
                @php
                $data = is_array($property->data) ? $property->data : json_decode($property->data, true);
                @endphp
                <div class="col-lg-6">
                    <div class="card shadow-lg border-light-subtle h-100">
                        <div class="card-header border-light-subtle bg-white d-flex justify-content-between">
                            <span class="text-primary fw-semibold">ID: {{ $property->property_uid }}</span>
                            <span>
                                <a href="{{ route('postproperty.edit.property_details', $property->property_uid) }}">
                                    <i class="fa-solid fa-pencil text-primary"></i>
                                </a>
                                <div class="dropdown-wrapper ps-3">
                                    <a href="#" class="profile-button" id="userDropdown" tabindex="0">
                                        <i class="fa-solid fa-ellipsis-vertical "></i>

                                    </a>
                                    <ul class="customDropdown">
                                        <li>
                                            <a href="#" class="dropdown-item dropdownLink delete"
                                                onclick="event.preventDefault(); document.getElementById('delete-form-{{ $property->id }}').submit();">
                                                <i class="fa-solid fa-trash pe-2"></i> Delete
                                            </a>

                                            <form id="delete-form-{{ $property->id }}" action="{{ route('property.destroy', $property->property_uid) }}" method="POST" class="d-none">
                                                @csrf
                                            </form>

                                        </li>

                                    </ul>
                                </div>

                            </span>
                        </div>
                        <div class="card-body row p-0">
                            <div class="col-5">
                               @if (!empty($property->galleries[0]))
                                    <img
                                        src="{{ url('storage/' . $property->galleries[0]) }}"
                                        class="img-fluid h-100 rounded-bottom-left-2"
                                        alt="{{ $property->project_name ?? 'Property Image' }}"
                                    >
                                @else
                                    <div class="d-flex justify-content-center align-items-center bg-secondary-subtle h-100 rounded-bottom-left-2">
                                        <img src="{{ url('frontend/real-estate.png') }}" alt="Fallback Image" class="opacity-50 w-50">
                                    </div>
                                @endif

                            </div>
                            <div class="col-7 py-2 py-md-3 pe-4 ps-0">
                                <div class="card-title mb-md-3">
                                    <span class="fw-bold h5">₹{{ formatPrice($property->total_price) }}</span>
                                    @php
                                    $status = $property->status;
                                    $badgeClass = 'bg-secondary'; // default
                                    $badgeText = ucfirst($status); // default text

                                    switch ($status) {
                                    case 'active':
                                    $badgeClass = 'bg-success';
                                    break;
                                    case 'draft':
                                    $badgeClass = 'bg-warning text-dark';
                                    break;
                                    case 'under_review':
                                    $badgeClass = 'bg-info text-dark';
                                    $badgeText = 'Under Review';
                                    break;
                                    case 'rejected':
                                    $badgeClass = 'bg-danger';
                                    break;
                                    }
                                    @endphp

                                    <span class="badge {{ $badgeClass }} float-end">{{ $badgeText }}</span>
                                </div>
                                <div class="h5 mb-0 mb-md-2">{{ $property->title ?? 'Untitled Property' }} <a href="{{ route('property.details', $property->slug??'#') }}"><i class="fa-solid fa-arrow-up-right-from-square text-orange ms-3"></i></a></div>

                                <p class="mb-1">
                                    {{$property->city}}
                                </p>
                                <p class="mb-2 text-muted">{{ $property->area }} {{ $property->area_unit }} @php
                                    $pricePerUnit = $property->area > 0 ? round($property->total_price / $property->area, 2) : 0;
                                    @endphp

                                    <span class="ms-3">
                                        ₹{{ number_format($pricePerUnit) }} per {{ $property->area_unit ?? 'unit' }}
                                    </span>
                                <p class="mb-0">
                                    <small class="text-muted">Updated: {{ $property->updated_at->diffForHumans() }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted">You have no saved properties yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // Forces user to stay on dashboard
};
</script>
@endsection
@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{ url('assets/customs/css/career.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {!! $breadcrumbHtml !!}

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="contentCard projects">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('locations.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ base64_encode($location->id) }}">

                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="parent_id">Parent Location</label>
                                    <select name="parent_id" class="form-control input select2">
                                        <option value="">None (this is a city / main location)</option>
                                        @foreach ($parentLocations as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Leave empty for a city. Choose a city to make this a sublocation.</small>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Location Name</label>
                                    <input type="text" name="city" class="form-control"
                                        value="{{ old('city', $location->city) }}" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Country</label>
                                    <input type="text" name="country" class="form-control"
                                        value="{{ old('country', $location->country) }}" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>State</label>
                                    <input type="text" name="state" class="form-control"
                                        value="{{ old('state', $location->state) }}">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" class="form-control"
                                        value="{{ old('latitude', $location->latitude) }}">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" class="form-control"
                                        value="{{ old('longitude', $location->longitude) }}">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ old('status', (string) (int) $location->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', (string) (int) $location->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label>Image</label>
                                        <input type="file" class="form-control" name="image"
                                            onchange="previewImage(this, '#previewLocationImage')">
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{ $location->image ? asset('storage/' . $location->image) : url('assets/images/NA.webp') }}"
                                            alt="photo" id="previewLocationImage">
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Update Location</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

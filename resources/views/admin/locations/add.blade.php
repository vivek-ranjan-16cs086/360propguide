@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
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
                        <form method="POST" action="{{route('locations.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="parent_id">Parent Location</label>
                                    <select name="parent_id" class="form-control input select2">
                                        <option value="">None (this is a city / main location)</option>
                                        @foreach ($parentLocations as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Leave empty for a city. Choose a city to create a sublocation.</small>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="city">Location Name</label>
                                    <input class="form-control input @error('city') is-invalid @enderror"
                                        type="text" placeholder="e.g. Noida or Sector 150"
                                        value="{{ old('city') }}" name="city" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="country">Country</label>
                                    <input class="form-control input" type="text"
                                        value="{{ old('country', 'India') }}" name="country" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="state">State</label>
                                    <input class="form-control input" type="text"
                                        placeholder="e.g. Uttar Pradesh"
                                        value="{{ old('state') }}" name="state">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="latitude">Latitude</label>
                                    <input class="form-control input" type="text"
                                        value="{{ old('latitude') }}" name="latitude">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="longitude">Longitude</label>
                                    <input class="form-control input" type="text"
                                        value="{{ old('longitude') }}" name="longitude">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control input" required>
                                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control" name="image"
                                            onchange="previewImage(this, '#previewLocationImage')" id="image">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewLocationImage">
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Save Location</button>
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

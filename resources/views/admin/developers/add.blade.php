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
                        <form method="POST" action="{{route('developers.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Developer Name -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="developer_name">Developer Name</label>
                                    <input class="form-control input @error('developer_name') is-invalid @enderror "
                                        type="text" placeholder="Enter Developer Name"
                                        value="{{old('developer_name')}}" name="developer_name" required>
                                </div>

                                <!-- Developer Experience -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="developer_experience">Developer Experience (Years)</label>
                                    <input class="form-control input @error('developer_experience') is-invalid @enderror "
                                        type="text" placeholder="Enter Developer Experience"
                                        value="{{old('developer_experience')}}" name="developer_experience" required>
                                </div>

                                <!-- Ongoing Projects -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="ongoing">Ongoing Projects</label>
                                    <input class="form-control input @error('ongoing') is-invalid @enderror "
                                        type="number" placeholder="Enter Ongoing Projects Count"
                                        value="{{old('ongoing')}}" name="ongoing_project" required>
                                </div>

                                <!-- Completed Projects -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="completed">Completed Projects</label>
                                    <input class="form-control input @error('completed') is-invalid @enderror "
                                        type="number" placeholder="Enter Completed Projects Count"
                                        value="{{old('completed')}}" name="completed_projects" required>
                                </div>

                                <!-- Status -->
                                {{-- <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control input select2" required>
                                        <option value="" disabled selected> Select Status </option>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}> Active </option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}> Inactive </option>
                                    </select>
                                </div> --}}

                                <!-- Developer Background Image -->
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="developer_background_image">Developer Background Image:</label>
                                        <input type="file"
                                            class="form-control @error('developer_background_image') is-invalid @enderror"
                                            name="developer_logo"
                                            onchange="previewImage(this, '#previewDevImage')"
                                            placeholder="Project Developer Background Image"
                                            id="developer_background_image" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewDevImage">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Save Developer</button>
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
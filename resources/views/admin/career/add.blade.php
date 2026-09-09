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
            <div class="contentCard projects">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{route('career.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Position</label>
                                    <input class="form-control input @error('position') is-invalid @enderror " type="text"
                                        placeholder="Enter Position" value="{{@old('position')}}" name="position" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="experience">Experience</label>
                                    <input class="form-control input @error('experience') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Experience"
                                        value="{{@old('experience')}}" name="experience" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="open_positions">Open Position</label>
                                    <input
                                        class="form-control input @error('open_positions') is-invalid @enderror "
                                        type="text" placeholder="Enter Open Position"
                                        value="{{@old('open_positions')}}" name="open_positions"
                                        required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="location">Location</label>
                                    <input
                                        class="form-control input @error('location') is-invalid @enderror "
                                        type="text" placeholder="Enter Location"
                                        value="{{@old('location')}}" name="location" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="posted_date">Posted Date</label>
                                    <input
                                        class="form-control input @error('date_posted') is-invalid @enderror "
                                        type="date" placeholder="Enter Posted Date"
                                        value="{{@old('date_posted')}}" name="date_posted" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">

                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Job Description:</label>
                                        <textarea name="job_description" class="summernote" id="summernote">
										{{@old('job_description')}}
										</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Company Logo:</label>
                                        <input type="file"
                                            class="form-control @error('company_logo') is-invalid @enderror"
                                            name="company_logo" onchange="previewImage(this, '#previewCompanyLogo')"
                                            placeholder="Project Company Logo" id="company_logo" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewCompanyLogo">
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Upload Job</button>
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

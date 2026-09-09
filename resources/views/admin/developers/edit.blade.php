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

                        <form method="POST" action="{{ route('developers.update', $developer->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <input type="hidden" name="id" value="{{ base64_encode($developer->id) }}">

                                <!-- Developer Name -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Developer Name</label>
                                    <input
                                        type="text"
                                        name="developer_name"
                                        class="form-control"
                                        value="{{ old('developer_name', $developer->developer_name) }}">
                                </div>

                                <!-- Experience -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Developer Experience</label>
                                    <input
                                        type="text"
                                        name="developer_experience"
                                        class="form-control"
                                        value="{{ old('developer_experience', $developer->developer_experience) }}">
                                </div>

                                <!-- Ongoing -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Ongoing Projects</label>
                                    <input
                                        type="number"
                                        name="ongoing_project"
                                        class="form-control"
                                        value="{{ old('ongoing_project', $developer->ongoing_project) }}">
                                </div>

                                <!-- Completed -->
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label>Completed Projects</label>
                                    <input
                                        type="number"
                                        name="completed_projects"
                                        class="form-control"
                                        value="{{ old('completed_projects', $developer->completed_projects) }}">
                                </div>

                                <!-- Image -->
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">

                                    <div class="form-group fileInput mr-2">
                                        <label>Developer Background Image</label>

                                        <input
                                            type="file"
                                            name="developer_logo"
                                            class="form-control"
                                            onchange="previewImage(this,'#previewDevImage')">
                                    </div>

                                    <div class="fileInput">
                                        <img
                                            src="{{ asset($developer->developer_logo) }}"
                                            id="previewDevImage"
                                            width="120">
                                    </div>

                                </div>

                                <div class="col-12">
                                    <button class="btn btn-primary">
                                        Update Developer
                                    </button>
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
@extends('frontend.layouts.app')

@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/career-detail.css')}}">
@endSection
@section('content')
<!-- Carrer-listing section -->

<div class="container-fluid">
    <div class="row top-section align-items-center justify-content-center p-5">
        <h5 class="text-center">360 PropGuide | Full Time</h5>
        <h1 class="mt-3 text-center mt-md-5">{{@$careers->position}}</h1>
        <div class="  mt-3 mt-md-5 justify-content-center d-flex  gap-3 flex-wrap">
            <button class="btn customBtn">REFER A FRIEND</button>
            <a href="#apply-form">
                <button class="btn customBtn">I'M INTRESTED</button>
            </a>
        </div>
    </div>
    <p class="text-center">Share it to</p>
    <div class="icons-container d-flex text-center justify-content-center align-items-center gap-3">
        <i class="fa-brands fa-facebook"></i>
        <i class="fa-brands fa-x-twitter"></i>
        <i class="fa-brands fa-linkedin"></i>
        <i class="fa-brands fa-whatsapp"></i>
    </div>
</div>
</div>
<div class="container mt-5 career-detail">
    <div class="row">
        <div class="col-md-6">
            <span>
                <span class="subhead">Job Listing</span> > Job Details
            </span>
            <h2 class="mt-3">Job Description</h2>
            {!!@$careers->job_description!!}
        </div>
        <div class="col-md-6 my-5 ms-auto" id="apply-form">
            <form action="" class="apply-form d-flex flex-column gap-3">
                <label for="name">Name<span class="text-danger">*</span></label>
                <input type="text" id="name" class="form-control" />
                <label for="email">Email<span class="text-danger">*</span></label>
                <input type="text" id="email" class="form-control" />
                <label for="number">Phone Number <span class="text-danger">*</span></label>
                <input type="text" id="number" class="form-control" />
                <label for="name">Upload Resume <span class="text-danger">*</span></label>
                <input type="file" id="name" class="form-control" />
                <div>

                    <button class="btn customBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endSection

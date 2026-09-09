@extends('frontend.layouts.app')
@section('title', "360 PropGuide Careers Real Estate Job Vacancies")
@section('description',"Find job vacancy postings near you real estate jobs, property sector roles, real estate career opportunities and consultant jobs at 360 PropGuide")
@section('keywords', " job vacancy, job postings near me, 360 PropGuide careers, real estate jobs, property sector roles, join our team, real estate career opportunities, real estate job, real estate consultant, jobs in real estate industry")
@section('canonical', url()->current())

@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/career.css')}}">
@endSection
@section('content')
<!-- Carrer-listing section -->

    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <h1 class="text-center">
                We're more than just a workplace. We're a family.
            </h1>
            <p class="text-center mt-5">
                We know that finding a meaningful and rewarding career can be a long
                journey. Our goal is to make that process easy for you and to create a
                work environment that's enriching—one that you'll look forward to
                every day.
            </p>
            <div class="text-center">
                <a href="#"><button class="btn customBtn">View Open Roles</button></a>
            </div>
            <div class="my-5">
                <img src="{{asset('frontend/career-banner.jpg')}}" alt="360 PropGuide" class="w-100 object-fit-cover" />
            </div>
            <h2 class="mt-5 text-center">Some opportunites for you to explore</h2>
        </div>
        <div class="row mt-5 justify-content-center " id="job-cards">
            @if(!empty($pageData['careers']) && count($pageData['careers']) > 0)
                @foreach($pageData['careers'] as $career)
                    <div class="col-lg-4 col-md-6 p-3">
                        <a href="/" style="text-decoration:none;">
                            <div class="card px-3 py-4 shadow-lg">
                                <h4 class="card-head">{{$career->position}}</h4>
                                <p>
                                    {!!$career->job_description!!}
                                </p>
                                <div class="d-flex align-items-center justify-content-between">

                                    <p class="text-success">Full Time</p>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </a>
						
                    </div>
                @endforeach
            @endif
			<div class="fw-bold text-center">
             <p>We have no job openings right now.</p>
           </div>
        </div>
    </div>
@endSection

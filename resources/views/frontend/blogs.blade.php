@extends('frontend.layouts.app')    

@section('title', "Real Estate Blog | Noida & Delhi NCR Property Insights | 360 PropGuide")
@section('description',"Stay ahead in Delhi NCR real estate with expert blogs on Noida, Greater Noida West, Ghaziabad & Yamuna Expressway. Property tips, market trends & investment guides by 360 PropGuide.")
@section('keywords', "real estate insights, real estate market trends, buying property, property investment")
@section('canonical', url()->current())

@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/css/blogs.css')}}">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Shakti Singh",
  "url": "https://www.360propguide.com/blogs",
  "image": "https://www.360propguide.com/frontend/user.jpeg",
  "sameAs": [
    "https://www.linkedin.com/in/shakti1977/"
  ],
  "jobTitle": "Real Estate Experts",
  "worksFor": {
    "@type": "Organization",
    "name": "360PropGuide"
  },
  "alumniOf": {
    "@type": "CollegeOrUniversity",
    "name": "Delhi University"
  },
  "knowsAbout": [
    "Real Estate",
    "Real Estate Advisor",
    "Real Estate Experts"
  ]
}
</script>
@endSection

@section('content')

<div class="blogs-container">
    <div class="container mt-5 ">
        <div class="row">

            <div class="col-lg-8">

                <!-- Top Blog -->
                @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)
                @php
                $latestBlog = $pageData['blogs'][0];
                @endphp

                <div class="row top-blog">
                    <img src="{{url('storage/' . $latestBlog->featured_image)}}"
                        alt="blog" class="w-75 mx-auto">
                </div>
<a href="{{ route('blogs.details', $latestBlog->slug) }}" style="text-decoration:none;">
                <div class="row pt-5 top-blog-content mx-auto shadow-lg">
                    <p class="author">Shakti Singh</p>
                    <h5>{{$latestBlog->title}}</h5>
                    <p>{{ $latestBlog->short_description }}</p>
                    <div>
                        <a href="{{ route('blogs.details', $latestBlog->slug) }}" style="text-decoration:none;">
                            <button class="btn customBtn ms-auto d-block">Read More</button>
                        </a>
                    </div>
                </div>
				</a>
                @endif
                <!-- Top Blog End -->

                <!-- Blog Listing -->
                <div id="blog-listing" class="d-flex flex-column gap-4 blog-listing mb-5">

                    @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)

                    @foreach($pageData['blogs'] as $index => $blog)

                    @if($index == 0)
                    @continue
                    @endif

                    <a href="{{ route('blogs.details', $blog->slug) }}" style="text-decoration:none;">
                        <div class="row shadow-lg">
                            <div class="col-md-4 ps-md-0">
                                <img src="{{ url('storage/' . $blog->featured_image) }}" class="w-100 h-100"
                                    alt="{{ $blog->title }}">
                            </div>
                            <div class="col-md-8 d-flex justify-content-center flex-column">
                                <p class="author ">Shakti Singh</p>
                                <h5>{{$blog->title}}</h5>
                                <p>{{ $blog->short_description }}</p>
                                <button class="btn customBtn w-fit mb-3">
                                    Read More
                                </button>
                            </div>
                        </div>
                    </a>

                    @endforeach
                    @endif

                </div>

                <!-- Pagination
                <div id="pagination-links" class="mt-4">
                    {{ $pageData['blogs']->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>-->
				
				<!-- Pagination -->
					<div id="pagination-links" class="mt-4">

					@if ($pageData['blogs']->hasPages())
					<ul class="pagination justify-content-center">

						@php
							$current = $pageData['blogs']->currentPage();
							$last = $pageData['blogs']->lastPage();
						@endphp

						{{-- First Page --}}
						<li class="page-item {{ $current == 1 ? 'active' : '' }}">
							<a class="page-link" href="{{ $pageData['blogs']->url(1) }}">1</a>
						</li>

						{{-- Left dots --}}
						@if ($current > 3)
							<li class="page-item disabled"><span class="page-link">...</span></li>
						@endif

						{{-- Middle Pages (current -1, current, current +1) --}}
						@for ($i = max(2, $current - 1); $i <= min($last - 1, $current + 1); $i++)
							<li class="page-item {{ $current == $i ? 'active' : '' }}">
								<a class="page-link" href="{{ $pageData['blogs']->url($i) }}">{{ $i }}</a>
							</li>
						@endfor

						{{-- Right dots --}}
						@if ($current < $last - 2)
							<li class="page-item disabled"><span class="page-link">...</span></li>
						@endif

						{{-- Last Page --}}
						@if ($last > 1)
							<li class="page-item {{ $current == $last ? 'active' : '' }}">
								<a class="page-link" href="{{ $pageData['blogs']->url($last) }}">{{ $last }}</a>
							</li>
						@endif

					</ul>
					@endif

					</div>


            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-sm-10 mx-auto ps-lg-5 blog-sticky mb-5">

                <div class="search-blogs mb-3 px-3 py-4 shadow-lg rounded-2">
                    <h2 class="d-flex align-items-center mb-3 h4">
                        <div class="search-icon me-3">
                            <i class="fas fa-search text-white d-flex align-items-center"></i>
                        </div>
                        Search Blogs
                    </h2>
                    <div class="border-bottom mb-3"></div>

                    <form method="GET" action="{{ route('get.blogs') }}" class="input-group">
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Type Your Search Here" 
                            value="{{ request('search') }}">
                        <button type="submit" class="input-group-text">
                            <i class="fas fa-search input-icon"></i>
                        </button>
                    </form>
                </div>

                <h1 class="h4">Recent Blogs</h1>

                @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)
                @foreach($pageData['blogs'] as $blog)

                <a class="row my-4" href="{{route('blogs.details', $blog->slug) }}">
                    <div class="col-5 mb-2">
                        <img src="{{url('storage/' . $blog->featured_image)}}"
                            alt="{{$blog->title}}" class="w-100 object-fit-cover">
                    </div>
                    <div class="col-7">
                        <p>{{ $blog->title }}.</p>
                    </div>
                </a>

                @endforeach
                @endif

                <div class="subscribe mt-5 px-3 py-4 shadow-lg rounded-2">
                    <div class="d-flex align-items-center">
                        <div class="search-icon me-3">
                            <i class="fa-solid fa-envelope text-white"></i>
                        </div>
                        <div>
                            <div class="fs-4">Subscribe</div>
                            <p class="fs-14 m-0">Stay updated with everything real estate!</p>
                        </div>
                    </div>

                    <div class="border-bottom mb-md-4 mb-2"></div>

                    <p class="fs-5">Subscribe to our newsletter</p>

                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter email address">
                        <i class="fa-solid fa-envelope input-group-text"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection


@section('customJS')

<script>
$(document).ready(function () {

    $(document).on('click', '#pagination-links a', function (e) {
        e.preventDefault();

        let url = $(this).attr('href');

        $.ajax({
            url: url,
            type: "GET",

            beforeSend: function () {
                $('#blog-listing').html('<div class="text-center py-5">Loading...</div>');
            },

            success: function (response) {

                let newBlogs = $(response).find('#blog-listing').html();
                let newPagination = $(response).find('#pagination-links').html();

                $('#blog-listing').html(newBlogs);
                $('#pagination-links').html(newPagination);

                $('html, body').animate({
                    scrollTop: $(".blogs-container").offset().top
                }, 500);
            },

            error: function () {
                alert('Something went wrong!');
            }
        });

    });

});
</script>
@endsection
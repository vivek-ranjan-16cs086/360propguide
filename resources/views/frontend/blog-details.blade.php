@extends('frontend.layouts.app')
 
@section('title', $blogs->seo_data['secondary_keyword'])
@section('keywords', $blogs->seo_data['primary_keyword'])
@section('description', $blogs->seo_data['meta_description'])

@section('canonical', url()->current())
@section('og_image', url('storage/' . $blogs->featured_image))

@push('schema')
  @if(!empty($faqSchema))
    <script type="application/ld+json">{!! $faqSchema !!}</script>
  @endif
<script type="application/ld+json">
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "BlogPosting",
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => url()->current(),
    ],
    "headline" => $blogs->title,
    "description" => Str::limit(strip_tags($blogs->short_description), 160),
    "image" => url('storage/' . $blogs->featured_image),
    "author" => [
        "@type" => "Organization",
        "name" => "360propguide"
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => "360propguide",
        "logo" => [
            "@type" => "ImageObject",
            "url" => url('frontend/360logo.png')
        ]
    ],
    "datePublished" => $blogs->created_at->toDateString(),
    "dateModified" => $blogs->updated_at->toDateString(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('customCSS')
<link rel="stylesheet" href="{{asset('frontend/css/blogs.css')}}">
@endsection
@section('content')
<!-- herosec -->
@php
    $wordCount = str_word_count(strip_tags($blogs->description));
    $readingTime = max(1, ceil($wordCount / 100));
@endphp
<div class="customHerosec py-5 blog-content">
    <div class="container position-relative">
        <div class="row ">
            <div class="col-lg-7">
                <div>
                   
                    <h1 class="text-light mt-4 fw-bold h1">
					{{$blogs->title}} </h1>
                </div>
                <div class="d-flex justify-content-between mt-4 text-light "> 
                    <div class="d-flex gap-5">
                        <div class="d-flex  gap-2">
                            <div><i class="fa-solid fa-calendar-days"></i></div>
                            <div class="customFontHeroSec">{{formatDate($blogs->created_at)}}</div>
                        </div>
                        <div class="d-flex  gap-2">
						   <div><i class="fa-solid fa-clock"></i></div>
                           <span>{{ $readingTime }} min read</span> 
                        </div>
                        <div class="social-share">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('blogs/' . $blogs->slug)) }}" target="_blank"> 
                                    <i class="fa-brands fa-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('blogs/' . $blogs->slug)) }}&text={{ urlencode($blogs->seo_data['secondary_keyword']) }}" target="_blank">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url('blogs/' . $blogs->slug)) }}&title={{ urlencode($blogs->seo_data['secondary_keyword']) }}" target="_blank">
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode(url('blogs/' . $blogs->slug)) }}" target="_blank">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div> 
<div class="container rounded-3 blog-content">
    <div class="row"> 
        <div class="col-lg-8 col-12 customPosition">
            <img src="{{url('storage/' . $blogs->featured_image)}}" width="100%" alt="360propguide" class="rounded-3" loading="lazy">

            <div class="d-flex flex-column gap-2 mt-3">				
                <div id="toc">
				<p class="fw-bold h5">Table of content:</p>
				<ul id="tocList" class="mb-0"></ul>
                    <ul><li class="pointer toc-level-h3" onclick="scrollToHeading('faq')">Frequently Asked Questions</li></ul>
				</div>
            </div>
            <div  id="content">{!!$blogs->description!!}</div>
			<div class="section mb-3" id="faq" itemscope itemtype="https://schema.org/FAQPage">
			  <h3 class="h4 my-4 text-primary fw-bold">
				Frequently Asked Questions
			  </h3>
			  <div class="faq">
				@php
				$faqs = json_decode($blogs->faqs_data, true);  
				@endphp
				@if(!empty($faqs) && count($faqs) > 0)
				@foreach($faqs as $index => $faq)
				@if(!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')))
				<div class="question d-flex mb-3" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
				  <div class="number fs-5 d-flex align-items-center justify-content-center me-3">
					Q
				  </div>
				  <div class="">
					<h3 class="key-Title fw-bold fs-6" itemprop="name">
					  {{ trim($faq['question']) }}
					</h3>
					<div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
					  <div class="fs-14 m-0" itemprop="text">{!! $faq['answer'] !!}</div>
					</div>
				  </div>
				</div>
				@endif
				@endforeach
				@endif
			  </div>
			</div>
			<!-- ✅ AUTHOR CARD START -->
			<div class="author-box mt-4">
				<div class="author-wrapper">
					
					<div class="author-img">
						<img src="{{ url('frontend/user.jpeg') }}" alt="Author" loading="lazy">
					</div>

					<div class="author-content">
						<h5 class="author-name">Shakti Singh</h5>
						<span class="author-role">Real Estate Expert</span>

						<p class="author-bio">
							Shakti Singh is a real estate expert with strong experience in residential and commercial properties. 
							He shares market insights, investment tips, and latest trends to help buyers make smart decisions.
						</p>

						<div class="author-links">
							<a href="#"><i class="fa-brands fa-linkedin"></i></a>
							<a href="#"><i class="fa-brands fa-twitter"></i></a>
							<a href="#"><i class="fa-brands fa-instagram"></i></a>
						</div>
					</div>

				</div>
			</div>
			<!-- ✅ AUTHOR CARD END -->
        </div>
		
         <div class="col-lg-4 d-none d-lg-block mt-3 h-fit recommended">
            <div class="card shadow-md bg-white py-4 px-3 d-none d-lg-block mt-3">
                <h4>Recent Blogs</h4>
				@foreach($recommendedBlogs as $recommended)
            <a class="row my-3" href="{{route('blogs.details', $recommended->slug) }}">
              <div class="col-5 mb-2">
                        <img src="{{url('storage/' . $recommended->featured_image)}}" alt="{{$recommended->title}}"
                                        class="w-100 object-fit-cover">
                    </div>
                    <div class="col-7">
                      <p>{{ $recommended->title }}.</p>
                    </div>   
            </a>
			@endforeach
            </div>

            <div class="card shadow-md bg-white p-3 d-none d-lg-block mt-3 mb-5">
                <div class="h4 text-primary fw-bold">Recommended</div>
                @foreach($recommendedProjects as $recommended)
                    <a class="col-12 d-block mb-3" href="{{route('projects.details', $recommended->slug) }}">
                        <div>
                            <div class="d-flex border Recommended-card imgHover">
                                <div class="col-5 col-md-5 img-container position-relative">
                                    <img alt="{{$recommended->project_name }}" loading="lazy" decoding="async"
                                        data-nimg="fill" class="h-100 object-fit-cover rounded-start-1" sizes="100vw"
                                        src="{{ url('storage/' . $recommended->logo_image) }}" style="
                            position: absolute;
                            height: 100%;
                            width: 100%;
                            inset: 0px;
                            color: transparent;
                          " />
                                </div>
                                <div class="p-3 font-sm col-7">
                                    <div class="project-heading">
                                        <div class="recommended-title mb-2">{{ $recommended->project_name }}</div>
                                        <div class="text-secondary fs-7"></div>
                                    </div>
                                    <div class="project-location mb-2">
                                        <div class="text-secondary fs-7">{{ $recommended->typology}}</div>
                                        <div class="text-secondary recommended-location fs-7">
                                            {{ $recommended->location}}
                                        </div>
                                    </div>
                                    <div class="project-price">₹ {{ formatPrice($recommended->price) }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
        </div>
    </div>
</div>
<!-- sec2 end -->

@endSection
@section('customJS')
<script>
  document.addEventListener("DOMContentLoaded", function() {
            const contentElement = document.getElementById("content");
            const tocElement = document.getElementById("tocList");
            if (!contentElement || !tocElement) return;

            // Delay to ensure dynamic content is rendered (if necessary)
            setTimeout(() => {
                // Query headings within the content
                const headings = Array.from(
                    contentElement.querySelectorAll("h1, h2, h3, h4, h5, h6")
                );

                // Map headings to TOC items and render in the TOC element
                const tocItems = headings.map(heading => {
                    // Generate unique IDs for headings
                    const id = heading.textContent.trim().toLowerCase().replace(/\s+/g, "-").replace(/[^\w-]/g, "");
                    heading.id = id; // Set the ID for the heading

                    return {
                        text: heading.innerText,
                        level: heading.tagName,
                        id: id,
                    };
                });

                // Build the TOC as an unordered list
                tocElement.innerHTML += tocItems.map(item => 
                    `<li class="pointer toc-level-${item.level.toLowerCase()}" onclick="scrollToHeading('${item.id}')">${item.text}</li>`
                ).join("");
            }, 500); // Delay to allow content to render fully
        });

        // Function to scroll to the heading
        function scrollToHeading(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }
</script>
@endSection
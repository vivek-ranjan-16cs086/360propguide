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
                <div id="blog-listing" class="d-flex flex-column gap-4 blog-listing mb-5"
                    data-endpoint="{{ route('get.blogs') }}"
                    data-search="{{ $search ?? '' }}"
                    data-current-page="{{ $pageData['blogs']->currentPage() }}"
                    data-last-page="{{ $pageData['blogs']->lastPage() }}">

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

                <div id="blogScrollSentinel" aria-hidden="true"></div>
                <div id="blogAjaxLoader" class="text-center py-4" role="status" aria-live="polite" hidden>
                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                    <span id="blogLoadingMessage">Loading more blogs...</span>
                    <button type="button" id="blogLoadRetry" class="btn btn-link" hidden>Try again</button>
                </div>
                @if($pageData['blogs']->hasMorePages())
                <noscript><a href="{{ $pageData['blogs']->nextPageUrl() }}">Load more blogs</a></noscript>
                @endif


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
                        <input type="text" name="search" class="form-control"
                            placeholder="Type Your Search Here" value="{{ request('search') }}">
                        <button type="submit" class="input-group-text">
                            <i class="fas fa-search input-icon"></i>
                        </button>
                    </form>
                </div>

                <h1 class="h4">Recent Blogs</h1>
                @if(!empty($pageData['blogs']) && count($pageData['blogs']) > 0)
                    @foreach($pageData['blogs'] as $blog)
                    <a class="row my-4" href="{{ route('blogs.details', $blog->slug) }}">
                        <div class="col-5 mb-2">
                            <img src="{{ url('storage/' . $blog->featured_image) }}"
                                alt="{{ $blog->title }}" class="w-100 object-fit-cover">
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
(function () {
    var listing = document.getElementById('blog-listing');
    if (!listing) return;

    var sentinel = document.getElementById('blogScrollSentinel');
    var loader = document.getElementById('blogAjaxLoader');
    var loadingMessage = document.getElementById('blogLoadingMessage');
    var spinner = loader.querySelector('.spinner-border');
    var retryButton = document.getElementById('blogLoadRetry');
    var currentPage = Number(listing.dataset.currentPage) || 1;
    var lastPage = Number(listing.dataset.lastPage) || 1;
    var loading = false;
    var scrollCheckQueued = false;

    function updateFooter() {
        var shouldHideFooter = loading || currentPage < lastPage;
        document.querySelectorAll('.commonLinks, body > footer').forEach(function (element) {
            element.hidden = shouldHideFooter;
        });
    }

    function appendBlog(blog) {
        var link = document.createElement('a');
        link.href = blog.url;
        link.style.textDecoration = 'none';

        var row = document.createElement('div');
        row.className = 'row shadow-lg';

        var imageColumn = document.createElement('div');
        imageColumn.className = 'col-md-4 ps-md-0';
        var image = document.createElement('img');
        image.src = blog.image_url || '';
        image.alt = blog.title || 'Blog';
        image.className = 'w-100 h-100';
        image.loading = 'lazy';
        imageColumn.appendChild(image);

        var contentColumn = document.createElement('div');
        contentColumn.className = 'col-md-8 d-flex justify-content-center flex-column';
        var author = document.createElement('p');
        author.className = 'author';
        author.textContent = 'Shakti Singh';
        var title = document.createElement('h5');
        title.textContent = blog.title || '';
        var description = document.createElement('p');
        description.textContent = blog.short_description || '';
        var readMore = document.createElement('span');
        readMore.className = 'btn customBtn w-fit mb-3';
        readMore.textContent = 'Read More';

        contentColumn.append(author, title, description, readMore);
        row.append(imageColumn, contentColumn);
        link.appendChild(row);
        listing.appendChild(link);
    }

    function scheduleScrollCheck() {
        if (scrollCheckQueued) return;
        scrollCheckQueued = true;
        window.requestAnimationFrame(checkScroll);
    }

    function checkScroll() {
        scrollCheckQueued = false;
        if (loading || currentPage >= lastPage) return;
        if (sentinel.getBoundingClientRect().top <= window.innerHeight * 1.5) {
            loadNextPage();
        }
    }

    function loadNextPage() {
        if (loading || currentPage >= lastPage) return;

        loading = true;
        loader.hidden = false;
        loadingMessage.hidden = false;
        loadingMessage.textContent = 'Loading more blogs...';
        spinner.hidden = false;
        retryButton.hidden = true;
        updateFooter();
        var shouldScheduleNext = false;

        var nextPage = currentPage + 1;
        var url = new URL(listing.dataset.endpoint, window.location.href);
        url.searchParams.set('page', nextPage);
        if (listing.dataset.search) url.searchParams.set('search', listing.dataset.search);

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok || !payload || payload.status !== true) {
                        throw new Error(payload && payload.message ? payload.message : 'Unable to load blogs.');
                    }
                    return payload;
                });
            })
            .then(function (payload) {
                var data = payload.data || {};
                if (!Array.isArray(data.items) || !data.pagination) {
                    throw new Error('Invalid blog response.');
                }

                data.items.forEach(appendBlog);
                currentPage = Number(data.pagination.current_page) || nextPage;
                lastPage = Number(data.pagination.last_page) || lastPage;
                listing.dataset.currentPage = currentPage;
                listing.dataset.lastPage = lastPage;
                loader.hidden = true;
                shouldScheduleNext = true;
            })
            .catch(function () {
                loadingMessage.textContent = 'Unable to load blogs.';
                spinner.hidden = true;
                retryButton.hidden = false;
            })
            .finally(function () {
                loading = false;
                updateFooter();
                if (shouldScheduleNext && currentPage < lastPage) scheduleScrollCheck();
            });
    }

    window.addEventListener('scroll', scheduleScrollCheck, {passive: true});
    window.addEventListener('resize', scheduleScrollCheck, {passive: true});
    retryButton.addEventListener('click', loadNextPage);
    updateFooter();
    scheduleScrollCheck();
})();
</script>
@endsection
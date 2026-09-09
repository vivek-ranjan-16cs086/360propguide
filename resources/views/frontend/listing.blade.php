@extends('frontend.layouts.app')

@push('schema')
    {!! @$schema !!}
@endpush

@section('title', isset($title) && $title ? $title : '360 PropGuide | Explore Top Real Estate Projects in NCR')

@section('description', isset($description) && $description ? $description : 'Browse 360 PropGuide projects in Noida, Greater Noida & Delhi NCR. Get details on price, design, amenities & availability for your dream property.')

@section('keywords', (isset($keywords) && $keywords) ? $keywords : 'real estate projects, property listings, property pricing')

@section('canonical', isset($canonical) && $canonical ? $canonical : url()->current())

@section('customCSS')
<link rel="stylesheet" href="{{ asset('frontend/css/listing.css') }}?v={{ @filemtime(public_path('frontend/css/listing.css')) ?: time() }}">
@endsection

@section('content')
@php
    $selected = $selected ?? ['location' => [], 'locality' => [], 'type' => [], 'possession' => [], 'developer' => [], 'q' => '', 'sort' => 'newest', 'min_price' => null, 'max_price' => null];
    $hasActiveFilters = !empty($selected['location']) || !empty($selected['locality']) || !empty($selected['type']) || !empty($selected['possession']) || !empty($selected['developer']) || $selected['q'] !== '' || $selected['min_price'] !== null || $selected['max_price'] !== null;
    $queryBase = [
        'location' => $selected['location'],
        'locality' => $selected['locality'],
        'type' => $selected['type'],
        'possession' => $selected['possession'],
        'developer' => $selected['developer'],
    ];
    if ($selected['q'] !== '') {
        $queryBase['q'] = $selected['q'];
    }
    if (($selected['sort'] ?? 'newest') !== 'newest') {
        $queryBase['sort'] = $selected['sort'];
    }
    if ($selected['min_price'] !== null) {
        $queryBase['min_price'] = $selected['min_price'];
    }
    if ($selected['max_price'] !== null) {
        $queryBase['max_price'] = $selected['max_price'];
    }
    $removeFilterUrl = function ($key, $value = null) use ($queryBase) {
        $query = $queryBase;
        if ($value === null) {
            unset($query[$key]);
        } else {
            $query[$key] = array_values(array_filter($query[$key] ?? [], fn ($item) => (string) $item !== (string) $value));
            if (empty($query[$key])) {
                unset($query[$key]);
            }
        }
        return route('projects', $query);
    };
    $pageHeading = $dynamicTitle ?? ($name ?? 'Projects in Delhi NCR');
    $resultCount = isset($projects) ? $projects->total() : 0;
@endphp

<section class="projects-page">
    <form method="GET" action="{{ route('projects') }}" id="projectsFilterForm" class="container">
        <header class="projects-header">
            <div class="projects-header__top">
                <div class="projects-header__title-group">
                    <h1>{{ $pageHeading }}</h1>
                    <p class="projects-header__count">
                        {{ $resultCount }} {{ \Illuminate\Support\Str::plural('project', $resultCount) }} found
                    </p>
                </div>
                <div class="projects-header__actions">
                    <div class="project-search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <input type="search" name="q" value="{{ $selected['q'] }}" class="form-control" placeholder="Search projects" autocomplete="off">
                        @if($selected['q'] !== '')
                            <a href="{{ $removeFilterUrl('q') }}" class="project-search__clear" aria-label="Clear search"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                    </div>
                    <button type="button" class="project-filter-trigger d-lg-none" data-open-filters aria-controls="projectFilters">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i> Filters
                    </button>
                    <label class="project-sort">
                        <span>Sort</span>
                        <select name="sort" class="form-select" aria-label="Sort projects">
                            <option value="newest" {{ ($selected['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ ($selected['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ ($selected['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </label>
                </div>
            </div>
        </header>

        <div class="projects-content__layout">
            <aside class="filters-sidebar" id="projectFilters">
                <div class="filters-sidebar__mobile-head d-lg-none">
                    <h2 id="projectFiltersLabel">Filters</h2>
                    <button type="button" class="filters-sidebar__close" data-close-filters aria-label="Close filters">&times;</button>
                </div>
                @include('frontend.partials._project-filters')
            </aside>

            <div class="projects-results">
                @if($hasActiveFilters)
                    <div class="projects-active-filters">
                        <span class="projects-active-filters__label">Active</span>
                        <div class="projects-active-filters__list">
                            @foreach ($selected['location'] as $item)
                                <a class="active-filter-chip" href="{{ $removeFilterUrl('location', $item) }}">{{ $item }} <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                            @endforeach
                            @foreach ($selected['locality'] as $item)
                                <a class="active-filter-chip" href="{{ $removeFilterUrl('locality', $item) }}">{{ $item }} <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                            @endforeach
                            @foreach ($selected['type'] as $item)
                                <a class="active-filter-chip" href="{{ $removeFilterUrl('type', $item) }}">{{ $item }} <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                            @endforeach
                            @foreach ($selected['possession'] as $item)
                                <a class="active-filter-chip" href="{{ $removeFilterUrl('possession', $item) }}">{{ ucwords(str_replace('_', ' ', $item)) }} <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                            @endforeach
                            @foreach ($developers as $developer)
                                @if(in_array((string) $developer->id, array_map('strval', $selected['developer']), true))
                                    <a class="active-filter-chip" href="{{ $removeFilterUrl('developer', $developer->id) }}">{{ $developer->developer_name }} <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                                @endif
                            @endforeach
                            @if($selected['q'] !== '')
                                <a class="active-filter-chip" href="{{ $removeFilterUrl('q') }}">“{{ $selected['q'] }}” <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                            @endif
                            @if($selected['min_price'] !== null || $selected['max_price'] !== null)
                                @php
                                    $budgetQuery = $queryBase;
                                    unset($budgetQuery['min_price'], $budgetQuery['max_price']);
                                @endphp
                                <a class="active-filter-chip" href="{{ route('projects', $budgetQuery) }}">
                                    Budget
                                    @if($selected['min_price'] !== null) ₹{{ formatPrice($selected['min_price']) }} @endif
                                    @if($selected['max_price'] !== null) – ₹{{ formatPrice($selected['max_price']) }} @endif
                                    <i class="fa-solid fa-xmark remove-active-tag"></i>
                                </a>
                            @endif
                        </div>
                        <a class="projects-active-filters__clear" href="{{ route('projects') }}">Clear all</a>
                    </div>
                @endif

                @if(!empty($links_description))
                    <details class="projects-description">
                        <summary>About these projects</summary>
                        <div class="projects-description__body">{!! $links_description !!}</div>
                    </details>
                @endif

                <div class="projects-grid">
                    @include('frontend.partials._project-list', ['projects' => $projects])
                </div>

                @if(isset($projects) && $projects->lastPage() > 1)
                    <nav id="pagination-links" aria-label="Projects pagination">
                        <ul class="pagination">
                            <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}">
                                @if($projects->onFirstPage())
                                    <span class="page-link">Previous</span>
                                @else
                                    <a class="page-link" href="{{ $projects->previousPageUrl() }}">Previous</a>
                                @endif
                            </li>
                            @foreach ($projects->getUrlRange(max(1, $projects->currentPage() - 2), min($projects->lastPage(), $projects->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $projects->currentPage() === $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item {{ $projects->currentPage() === $projects->lastPage() ? 'disabled' : '' }}">
                                @if($projects->currentPage() === $projects->lastPage())
                                    <span class="page-link">Next</span>
                                @else
                                    <a class="page-link" href="{{ $projects->nextPageUrl() }}">Next</a>
                                @endif
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </form>
</section>
@endsection

@section('customJS')
<script>
(function () {
    var form = document.getElementById('projectsFilterForm');
    if (!form) return;

    function selectedCities() {
        return Array.prototype.map.call(form.querySelectorAll('input[name="location[]"]:checked'), function (input) {
            return input.value.toLowerCase();
        });
    }

    function syncLocalities() {
        var cities = selectedCities();
        form.querySelectorAll('#localityList [data-city]').forEach(function (row) {
            var city = (row.getAttribute('data-city') || '').toLowerCase();
            var matchesCity = !cities.length || !city || cities.indexOf(city) !== -1;
            if (!matchesCity) {
                row.hidden = true;
                var checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
            } else if (!row.hasAttribute('data-search-hidden')) {
                row.hidden = false;
            }
        });
    }

    form.addEventListener('change', function (event) {
        if (event.target.matches('input[name="location[]"]')) {
            syncLocalities();
        }
        if (event.target.matches('input[type="checkbox"], select[name="sort"]')) {
            form.requestSubmit();
        }
    });

    form.addEventListener('submit', function () {
        form.querySelectorAll('input[name="q"], input[name="min_price"], input[name="max_price"]').forEach(function (input) {
            if (!input.value) input.disabled = true;
        });
        form.querySelectorAll('#localityList [data-city]').forEach(function (row) {
            if (row.hidden) {
                var checkbox = row.querySelector('input');
                if (checkbox) checkbox.disabled = true;
            }
        });
    });

    form.querySelectorAll('[data-filter-search]').forEach(function (input) {
        input.addEventListener('input', function () {
            var query = input.value.trim().toLowerCase();
            var list = document.getElementById(input.getAttribute('data-filter-search'));
            if (!list) return;
            list.querySelectorAll('[data-filter-label]').forEach(function (row) {
                var label = row.getAttribute('data-filter-label') || '';
                var matchesQuery = !query || label.indexOf(query) !== -1;
                if (matchesQuery) {
                    row.removeAttribute('data-search-hidden');
                } else {
                    row.setAttribute('data-search-hidden', '1');
                }
                if (list.id === 'localityList') {
                    var cities = selectedCities();
                    var city = (row.getAttribute('data-city') || '').toLowerCase();
                    var matchesCity = !cities.length || !city || cities.indexOf(city) !== -1;
                    row.hidden = !matchesCity || !matchesQuery;
                } else {
                    row.hidden = !matchesQuery;
                }
            });
        });
    });

    form.querySelectorAll('[data-toggle-more]').forEach(function (button) {
        button.addEventListener('click', function () {
            var list = document.getElementById(button.getAttribute('data-toggle-more'));
            if (!list) return;
            list.classList.toggle('is-expanded');
            button.textContent = list.classList.contains('is-expanded') ? 'Show less' : 'Show more';
        });
    });

    syncLocalities();

    var openFilters = form.querySelector('[data-open-filters]');
    var closeFilters = form.querySelector('[data-close-filters]');
    function setFiltersOpen(open) {
        document.body.classList.toggle('project-filters-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
    }
    if (openFilters) {
        openFilters.addEventListener('click', function () { setFiltersOpen(true); });
    }
    if (closeFilters) {
        closeFilters.addEventListener('click', function () { setFiltersOpen(false); });
    }
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setFiltersOpen(false);
    });
    document.addEventListener('click', function (event) {
        if (!document.body.classList.contains('project-filters-open')) return;
        if (event.target.closest('#projectFilters') || event.target.closest('[data-open-filters]')) return;
        setFiltersOpen(false);
    });
})();
</script>
@endsection

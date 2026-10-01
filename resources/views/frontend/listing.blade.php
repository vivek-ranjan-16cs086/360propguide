@extends('frontend.layouts.app')

@push('schema')
{!! @$schema !!}
@endpush

@section('title', isset($title) && $title ? $title : '360 PropGuide | Explore Top Real Estate Projects in NCR')

@section('description', isset($description) && $description ? $description : 'Browse 360 PropGuide projects in Noida,
Greater Noida & Delhi NCR. Get details on price, design, amenities & availability for your dream property.')

@section('keywords', (isset($keywords) && $keywords) ? $keywords : 'real estate projects, property listings, property
pricing')

@section('canonical', isset($canonical) && $canonical ? $canonical : url()->current())

@section('customCSS')
<link rel="stylesheet"
    href="{{ asset('frontend/css/listing.css') }}?v={{ @filemtime(public_path('frontend/css/listing.css')) ?: time() }}">
@endsection

@section('content')
@php
$selected = $selected ?? ['location' => [], 'locality' => [], 'type' => [], 'possession' => [], 'developer' => [], 'q'
=> '', 'sort' => 'newest', 'min_price' => null, 'max_price' => null];
$hasActiveFilters = !empty($selected['location']) || !empty($selected['locality']) || !empty($selected['type']) ||
!empty($selected['possession']) || !empty($selected['developer']) || $selected['q'] !== '' || $selected['min_price'] !==
null || $selected['max_price'] !== null;
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
    <form method="GET" action="{{ route('projects') }}" data-filter-endpoint="{{ route('filters') }}" id="projectsFilterForm" class="container">
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
                        <input type="search" name="q" value="{{ $selected['q'] }}" class="form-control"
                            placeholder="Search projects" autocomplete="off">
                        <a href="{{ route('projects') }}" class="project-search__clear" data-clear-project-search
                            aria-label="Clear search" {{ $selected['q'] === '' ? 'hidden' : '' }}><i
                                class="fa-solid fa-xmark"></i></a>
                    </div>
                    <button type="button" class="project-filter-trigger d-lg-none" data-open-filters
                        aria-controls="projectFilters">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i> Filters
                    </button>
                    <label class="project-sort">
                        <span>Sort</span>
                        <select name="sort" class="form-select" aria-label="Sort projects">
                            <option value="newest" {{ ($selected['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>
                                Newest</option>
                            <option value="price_asc" {{ ($selected['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>
                                Price: Low to High</option>
                            <option value="price_desc"
                                {{ ($selected['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low
                            </option>
                        </select>
                    </label>
                </div>
            </div>
        </header>

        <div class="projects-content__layout">
            <aside class="filters-sidebar" id="projectFilters">
                <div class="filters-sidebar__mobile-head d-lg-none">
                    <h2 id="projectFiltersLabel">Filters</h2>
                    <button type="button" class="filters-sidebar__close" data-close-filters
                        aria-label="Close filters">&times;</button>
                </div>
                @include('frontend.partials._project-filters')
            </aside>

            <div class="projects-results">
                @if($hasActiveFilters)
                <div class="projects-active-filters">
                    <span class="projects-active-filters__label">Active</span>
                    <div class="projects-active-filters__list">
                        @foreach ($selected['location'] as $item)
                        <a class="active-filter-chip" href="{{ $removeFilterUrl('location', $item) }}">{{ $item }} <i
                                class="fa-solid fa-xmark remove-active-tag"></i></a>
                        @endforeach
                        @foreach ($selected['locality'] as $item)
                        <a class="active-filter-chip" href="{{ $removeFilterUrl('locality', $item) }}">{{ $item }} <i
                                class="fa-solid fa-xmark remove-active-tag"></i></a>
                        @endforeach
                        @foreach ($selected['type'] as $item)
                        <a class="active-filter-chip" href="{{ $removeFilterUrl('type', $item) }}">{{ $item }} <i
                                class="fa-solid fa-xmark remove-active-tag"></i></a>
                        @endforeach
                        @foreach ($selected['possession'] as $item)
                        <a class="active-filter-chip"
                            href="{{ $removeFilterUrl('possession', $item) }}">{{ ucwords(str_replace('_', ' ', $item)) }}
                            <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                        @endforeach
                        @foreach ($developers as $developer)
                        @if(in_array((string) $developer->id, array_map('strval', $selected['developer']), true))
                        <a class="active-filter-chip"
                            href="{{ $removeFilterUrl('developer', $developer->id) }}">{{ $developer->developer_name }}
                            <i class="fa-solid fa-xmark remove-active-tag"></i></a>
                        @endif
                        @endforeach
                        @if($selected['q'] !== '')
                        <a class="active-filter-chip" href="{{ $removeFilterUrl('q') }}">“{{ $selected['q'] }}” <i
                                class="fa-solid fa-xmark remove-active-tag"></i></a>
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
     <summary>
         <span class="projects-description__title">About these projects</span>
          <div class="projects-description__preview"> {{ Str::limit(strip_tags($links_description), 110) }} </div>
         </summary>
           <div class="projects-description__body"> {!! $links_description !!} 

          </div>
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
                        @foreach ($projects->getUrlRange(max(1, $projects->currentPage() - 2),
                        min($projects->lastPage(), $projects->currentPage() + 2)) as $page => $url)
                        <li class="page-item {{ $projects->currentPage() === $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                        @endforeach
                        <li
                            class="page-item {{ $projects->currentPage() === $projects->lastPage() ? 'disabled' : '' }}">
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
    (function() {
        var form = document.getElementById('projectsFilterForm');
        if (!form) return;
        var results = form.querySelector('.projects-results');
        var count = form.querySelector('.projects-header__count');
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        var activeRequest = null;

        function filterState(page) {
            var data = new FormData(form);

            return {
                location: data.getAll('location[]'),
                locality: data.getAll('locality[]'),
                type: data.getAll('type[]'),
                possession: data.getAll('possession[]'),
                developer: data.getAll('developer[]'),
                q: (data.get('q') || '').trim(),
                sort: data.get('sort') || 'newest',
                min_price: data.get('min_price') || null,
                max_price: data.get('max_price') || null,
                page: page || 1
            };
        }

        function setLoading(loading) {
            results.setAttribute('aria-busy', loading ? 'true' : 'false');
            results.style.opacity = loading ? '0.6' : '';
            results.style.pointerEvents = loading ? 'none' : '';
        }

        function showFilterError() {
            var error = results.querySelector('#projectsFilterError');
            if (!error) {
                error = document.createElement('div');
                error.id = 'projectsFilterError';
                error.className = 'alert alert-danger';
                error.setAttribute('role', 'alert');
                results.prepend(error);
            }
            error.hidden = false;
            error.textContent = 'Unable to update projects. Please try again.';
        }

        function applyProjectFilters(page) {
            if (activeRequest) activeRequest.abort();
            var request = new AbortController();
            activeRequest = request;
            setLoading(true);

            fetch(form.dataset.filterEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(filterState(page)),
                signal: request.signal
            })
                .then(function(response) {
                    if (!response.ok) throw new Error('Filter request failed');
                    return response.text();
                })
                .then(function(html) {
                    if (activeRequest !== request) return;

                    var page = new DOMParser().parseFromString(html, 'text/html');
                    var nextResults = page.querySelector('.projects-results');
                    var nextCount = page.querySelector('.projects-header__count');
                    if (!nextResults || !nextCount) throw new Error('Invalid filter response');

                    results.innerHTML = nextResults.innerHTML;
                    count.textContent = nextCount.textContent;

                    var clearSearch = form.querySelector('[data-clear-project-search]');
                    if (clearSearch) clearSearch.hidden = !form.querySelector('input[name="q"]').value.trim();
                })
                .catch(function(error) {
                    if (error.name !== 'AbortError') showFilterError();
                })
                .finally(function() {
                    if (activeRequest !== request) return;
                    activeRequest = null;
                    setLoading(false);
                });
        }

        function clearFilterControls() {
            form.querySelectorAll('input[type="checkbox"]').forEach(function(input) {
                input.checked = false;
            });
            form.querySelectorAll('input[name="q"], input[name="min_price"], input[name="max_price"]').forEach(function(input) {
                input.value = '';
            });
            form.querySelector('select[name="sort"]').value = 'newest';
            syncLocalities();
        }

        function useFilterUrl(url) {
            var params = new URL(url, document.baseURI).searchParams;
            clearFilterControls();

            ['location', 'locality', 'type', 'possession', 'developer'].forEach(function(name) {
                var values = [];
                params.forEach(function(value, key) {
                    if (key === name || key === name + '[]' || key.indexOf(name + '[') === 0) values.push(value);
                });
                values.forEach(function(value) {
                    var input = Array.prototype.find.call(form.querySelectorAll('[name="' + name + '[]"]'), function(item) {
                        return item.value === value;
                    });
                    if (input) input.checked = true;
                });
            });

            ['q', 'min_price', 'max_price'].forEach(function(name) {
                var input = form.querySelector('[name="' + name + '"]');
                if (input) input.value = params.get(name) || '';
            });
            form.querySelector('select[name="sort"]').value = params.get('sort') || 'newest';
            syncLocalities();
            applyProjectFilters(Number(params.get('page')) || 1);
        }

        function selectedCities() {
            return Array.prototype.map.call(form.querySelectorAll('input[name="location[]"]:checked'), function(input) {
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
            applyProjectFilters(1);
        }
    });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            applyProjectFilters(1);
        });

        form.addEventListener('click', function(event) {
            var pagination = event.target.closest('[data-project-page], #pagination-links a');
            if (pagination) {
                event.preventDefault();
                var page = Number(pagination.dataset.projectPage || new URL(pagination.href).searchParams.get('page')) || 1;
                applyProjectFilters(page);
                return;
            }

            var chip = event.target.closest('.active-filter-chip');
            if (chip) {
                event.preventDefault();
                if (chip.dataset.filterKey) {
                    var key = chip.dataset.filterKey;
                    if (key === 'q') {
                        form.querySelector('input[name="q"]').value = '';
                    } else if (key === 'budget') {
                        form.querySelector('input[name="min_price"]').value = '';
                        form.querySelector('input[name="max_price"]').value = '';
                    } else {
                        form.querySelectorAll('[name="' + key + '[]"]').forEach(function(input) {
                            if (input.value === chip.dataset.filterValue) input.checked = false;
                        });
                        if (key === 'location') syncLocalities();
                    }
                    applyProjectFilters(1);
                } else {
                    useFilterUrl(chip.href);
                }
                return;
            }

            if (event.target.closest('[data-clear-project-filters], .filter-header__reset, .project-empty-state__reset')) {
                event.preventDefault();
                clearFilterControls();
                applyProjectFilters(1);
                return;
            }

            if (event.target.closest('[data-clear-project-search], .project-search__clear')) {
                event.preventDefault();
                form.querySelector('input[name="q"]').value = '';
                applyProjectFilters(1);
            }
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
            openFilters.addEventListener('click', function() {
                setFiltersOpen(true);
            });
        }
        if (closeFilters) {
            closeFilters.addEventListener('click', function() {
                setFiltersOpen(false);
            });
        }
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') setFiltersOpen(false);
        });
        document.addEventListener('click', function(event) {
            if (!document.body.classList.contains('project-filters-open')) return;
            if (event.target.closest('#projectFilters') || event.target.closest('[data-open-filters]')) return;
            setFiltersOpen(false);
        });
    })();
</script>
@endsection

@extends('frontend.layouts.app')

@section('title', isset($title) && $title ? $title : 'Properties for Sale in Noida & Greater Noida | 360 PropGuide')

@section('description', isset($description) && $description ? $description : 'Browse 100+ verified properties in Noida, Greater Noida West & Ghaziabad. 2, 3 & 4 BHK apartments for sale. Talk to experts — call +91 9643-020-020.')

@section('keywords', isset($keywords) && $keywords ? $keywords : 'properties, flats, apartments, real estate')

@section('canonical', url()->current())

@section('customCSS')
<link rel="stylesheet" href="{{url('frontend/libraries/nouislider.min.css')}}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('frontend/css/listing.css') }}?v={{ @filemtime(public_path('frontend/css/listing.css')) ?: time() }}">
<link rel="stylesheet" href="{{url('frontend/css/properties-listing.css')}}">
@endSection

@section('content')
<section>
    <div class="container my-5 border-bottom pb-2"> 
        <div class="row">
            <div class="col-sm-12">
                <div class="row align-items-center justify-content-between">
                    <div class="d-flex w-fit">
                        <h3 class="text-decoration-none text-dark customFont me-2 fw-medium h5 d-none d-md-block">Sort
                            By:</h3>

                        <!-- Dropdown for smaller screens -->
                        <div class="d-md-none h-100">
                            <div id="sort-form">
                                @csrf
                                <input type="hidden" name="sort" value="sort" />
                                <select name="filter" id="filter" class="form-select shadow-none">
                                    <option>Sort</option>
                                    <option value="LowToHigh">Price -- Low to High</option>
                                    <option value="HighToLow">Price -- High to Low</option>
                                    <option value="NewestFirst">Newest First</option>
                                </select>
                            </div>
                        </div>

                        <!-- Links for larger screens -->
                        <ul class="d-md-flex gap-3 d-none list-unstyled">
                            <li class="sort-link text-decoration-none text-muted" data-filter="LowToHigh">
                                <span class="customFont">Price -- Low to High</span>
                            </li>
                            <li class="sort-link text-decoration-none  text-muted" data-filter="HighToLow">
                                <span class="customFont">Price -- High to Low</span>  
                            </li>
                            <li class="sort-link text-decoration-none  text-muted" data-filter="NewestFirst">
                                <span class="customFont">Newest First</span>
                            </li>
                        </ul>
                    </div>

                    <div class="input-group w-fit d-flex align-items-center mb-3">
                        <div id="search-form" class="d-flex w-100">
                            <input type="text" id="search-input" name="search" placeholder="Search Here"
                                class="form-control shadow-none" />
                        </div>
                    </div>

                    <div class="d-flex w-fit d-lg-none d-sm-block">
                        <button class="iconFilter" id="filterToggle"> <i class="fa fa-filter"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container">
    <div class="row">
        <!-- Filters Section -->
        <div class="col-lg-3 mb-5 d-none d-lg-block">
            <aside class="filters-sidebar" id="propertyFilters">
                <div id="desktopFilterContent">
                    @include('frontend.partials._property-filters')
                </div>
            </aside>
        </div>

        <!-- Property Listings Section -->
        <div class="col-lg-9">
            <div class="listings-section">
                <!-- Results Info -->
                <div class="results-bar">
					<div class="results-left">
						<div class="results-count" id="results-count">
							{{ isset($totalResults) ? $totalResults : ($properties->total() ?? 0) }} Results
						</div>
						<div class="results-divider"></div>
						<h1 class="results-title" id="results-title">
							{{ isset($dynamicTitle) ? $dynamicTitle : 'All Properties' }}
						</h1>
					</div>
				</div>

                <div class="collapseContainer mt-3">
                    <div class="collapseWrapper" id="collapseWrapper">
                        <span class="collapseText" id="collapseText">
                            <?php
                                if (isset($links_description)) {
                                    echo "<p>" . $links_description . '<span class="showicon" style="display:none;"><i class="fa-solid fa-angle-up icon "></i></span></p>';
                                }
                                ?>
                        </span>
                        <span class="hideicon">
                            <i class="fa-solid fa-angle-down icon"></i>
                        </span>
                    </div>
                </div>

                <!-- Property List -->
                <div id="property-list">
				@if(isset($properties) && $properties->count() > 0)
                        @foreach($properties as $property)
                            <div class="property-card mb-3"
                                 data-type="{{ $property->property_type }}"
                                 data-bhk="{{ $property->configuration }}"
                                 data-price="{{ $property->total_price }}"
                                 data-location="{{ $property->city }}">

                                <!-- Property Images -->
                                <div class="property-image">
                                    <div class="slider-container">
                                        <div id="propertyCarousel{{ $property->id }}"
                                             class="carousel slide slider"
                                             data-bs-ride="carousel"
                                             data-bs-interval="2000">

                                            <div class="carousel-inner">
                                                @if($property->galleries && count($property->galleries) > 0)
                                                    @foreach($property->galleries as $index => $gallery)
                                                        <div class="carousel-item slide {{ $index === 0 ? 'active' : '' }}">
                                                            <img src="/storage/{{ $gallery }}" alt="{{ $property->title }}">
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="carousel-item slide active">
                                                        <img src="/images/no-image.jpg" alt="No Image">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Property Details -->
                                <div class="property-details">
                                    <a href="{{ route('property.details', $property->slug) }}">
                                        <h2 class="m-0 property-title">{{ $property->title }}</h2>
                                    </a>

                                    <div class="location"><p>
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ $property->city }}
                                    </p></div>

                                    <div class="info-blocks">
                                        @if($property->furnishing_types)
                                            <p class="info-item m-0"><i class="fa-solid fa-couch"></i> {{ str_replace('_', ' ', ucfirst($property->furnishing_types)) }}</p>
                                        @endif

                                        @if($property->configuration)
                                            <p class="info-item m-0"><i class="fa-solid fa-building"></i> {{ str_replace('_', ' ', ucfirst($property->configuration)) }}</p>
                                        @endif

                                        @if($property->listing_type)
                                            <p class="info-item m-0"><i class="fa-solid fa-hand-holding-dollar"></i> {{ str_replace('_', ' ', ucfirst($property->listing_type)) }}</p>
                                        @endif
                                    </div>

                                    <div class="location-details">
                                        <p class="m-0">
                                            <i class="fa-solid fa-location-arrow"></i>
                                            {{ str_replace('_', ' ', ucfirst($property->construction_status)) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Price Section -->
                                <div class="price-section">
                                    <div class="price-info">
                                        <div><p class="price m-0">{{ formatPrice($property->total_price) }}</p></div>
                                        <div class="price-details">
                                            {{ strtolower($property->listing_type) === 'rent' ? 'per month' : 'total price' }}
                                        </div>
                                        @if($property->price_details)
                                            <div class="other-charges">+ See other charges</div>
                                        @endif
                                    </div>
                                    <div class="button-group">
                                        <a href="tel:+919643020020" class="btn contact-owner rounded">
                                            <i class="fa-solid fa-phone"></i> Contact
                                        </a>

                                        <button class="btn check-availability rounded" data-bs-toggle="modal" data-bs-target="#contactModalPopup">
                                            <i class="fa-solid fa-calendar-check"></i> Availability
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <h4 class="text-center">No properties found</h4>
                    @endif
				</div>

                <!-- Pagination -->
                <nav>
                     <ul class="pagination justify-content-center mt-3">
                        @if(isset($properties) && $properties->lastPage() > 1)
                            @php
                                $currentPage = $properties->currentPage();
                                $lastPage = $properties->lastPage();
                            @endphp
                            <li class="page-item {{ $properties->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" data-page="{{ $currentPage > 1 ? $currentPage - 1 : 1 }}">Previous</a>
                            </li>

                            @if($lastPage <= 5)
                                @for($page = 1; $page <= $lastPage; $page++)
                                    <li class="page-item {{ $currentPage === $page ? 'active' : '' }}">
                                        <a class="page-link" data-page="{{ $page }}">{{ $page }}</a>
                                    </li>
                                @endfor
                            @else
                                @if($currentPage > 3)
                                    <li class="page-item"><a class="page-link" data-page="1">1</a></li>
                                    @if($currentPage > 4)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                @endif

                                @php
                                    $start = max(1, $currentPage - 1);
                                    $end = min($lastPage, $currentPage + 1);
                                @endphp
                                @for($page = $start; $page <= $end; $page++)
                                    <li class="page-item {{ $currentPage === $page ? 'active' : '' }}">
                                        <a class="page-link" data-page="{{ $page }}">{{ $page }}</a>
                                    </li>
                                @endfor

                                @if($currentPage < $lastPage - 2)
                                    @if($currentPage < $lastPage - 3)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                    <li class="page-item"><a class="page-link" data-page="{{ $lastPage }}">{{ $lastPage }}</a></li>
                                @endif
                            @endif

                            <li class="page-item {{ $currentPage === $lastPage ? 'disabled' : '' }}">
                                <a class="page-link" data-page="{{ $currentPage < $lastPage ? $currentPage + 1 : $lastPage }}">Next</a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilter">
    <div class="offcanvas-header d-flex justify-content-between align-items-center">
        <h5 class="offcanvas-title">Filters</h5>
        <button type="button" class="btn p-0 border-0 bg-transparent fw-medium" data-bs-dismiss="offcanvas">
            Cancel
        </button>
    </div>
    <div class="offcanvas-body" id="mobileFilterContent">
        <!-- filter form will go here dynamically -->
    </div>
    <!-- Offcanvas Footer -->
    <div class="offcanvas-footer border-top p-3 d-flex justify-content-end">
        <button type="button" class="btn p-2 rounded border-0 fw-medium" data-bs-dismiss="offcanvas">
            Show All Results
        </button>
    </div>
</div>
@endSection

@section('customJS')
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterForm = document.querySelector("#filter-form");
            const mobileContainer = document.querySelector("#mobileFilterContent");
            const desktopContainer = document.querySelector("#desktopFilterContent");

            function moveFilters() {
                if (!filterForm || !mobileContainer || !desktopContainer) {
                    return;
                }
                if (window.innerWidth < 992) {
                    if (!mobileContainer.contains(filterForm)) {
                        mobileContainer.appendChild(filterForm);
                    }
                } else if (!desktopContainer.contains(filterForm)) {
                    desktopContainer.appendChild(filterForm);
                }
                if (window.jQuery && $("#slider-range").data("ui-slider")) {
                    $("#slider-range").slider("refresh");
                }
            }

            // run once on load
            moveFilters();

            // run on resize
            window.addEventListener("resize", moveFilters);

            // show offcanvas when filter button clicked
            document.getElementById("filterToggle")?.addEventListener("click", function () {
                let filterOffcanvas = new bootstrap.Offcanvas(document.getElementById("mobileFilter"));
                filterOffcanvas.show();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Server-provided initial filters (when visiting a custom property slug)
            var serverInitialFilters = {!! isset($initialFilters) ? json_encode($initialFilters) : 'null' !!};

            if (serverInitialFilters) {
                // Pre-check configuration filters
                if (serverInitialFilters.configuration) {
                    serverInitialFilters.configuration.forEach(function (c) {
                        $(".configuration-filter[value='" + c + "']").prop('checked', true);
                    });
                    filtersData['configuration'] = serverInitialFilters.configuration;
                }
                // Pre-check location filters
                if (serverInitialFilters.location) {
                    serverInitialFilters.location.forEach(function (l) {
                        // $(".location-filter[value='" + l + "']").prop('checked', true);
						$(".location-filter").filter(function () {
							return $(this).val().toLowerCase().trim() === l.toLowerCase().trim();
						}).prop('checked', true);
                    });
                    filtersData['location'] = serverInitialFilters.location;
                }

                // Pre-check propertyType filters (e.g., apartments for flats links)
                if (serverInitialFilters.propertyType) {
                    serverInitialFilters.propertyType.forEach(function (pt) {
                        $(".property-filter[value='" + pt + "']").prop('checked', true);
                    });
                    filtersData['propertyType'] = serverInitialFilters.propertyType;
                }

                filtersData['pageId'] = 1;
                applyFilters(filtersData);
            } else {
                // First, apply any query string filters (e.g., redirected with params)
                applyQueryParams();

                // Then ensure UI-driven updates are applied (these will re-run applyFilters)
                updateListingType(false);
                updatePropertyType(false);
                updateConfiguration(false);
                updateLocation(false);
            }
        });

        var filtersData = {};

        // Listing Type
        function updateListingType(apply = true) {
            const checkedValues = [];
            $('#listingTypeFilter .property-filter:checked').each(function () {
                checkedValues.push($(this).val());
            });
            filtersData['listingType'] = checkedValues;
            filtersData['pageId'] = 1;
            if (apply) {
                applyFilters(filtersData);
            }
        }

        // Property Type
        function updatePropertyType(apply = true) {
            const checkedValues = [];
            $('#propertyTypeFilter .property-filter:checked').each(function () {
                checkedValues.push($(this).val());
            });
            filtersData['propertyType'] = checkedValues;
            filtersData['pageId'] = 1;
            if (apply) {
                applyFilters(filtersData);
            }
        }

        // Location
        function updateLocation(apply = true) {
            const checkedValues = [];
            $('#locationFilter .property-filter:checked').each(function () {
                checkedValues.push($(this).val());
            });
            filtersData['location'] = checkedValues;
            filtersData['pageId'] = 1;
            if (apply) {
                applyFilters(filtersData);
            }
        }

        // Configuration / BHK
        function updateConfiguration(apply = true) {
            const checkedValues = [];
            $('#configurationFilter .property-filter:checked').each(function () {
                checkedValues.push($(this).val());
            });
            filtersData['configuration'] = checkedValues;
            filtersData['pageId'] = 1;
            if (apply) {
                applyFilters(filtersData);
            }
        }
		
		// Construction Status
		function updateConstructionStatus(apply = true) {
			const checkedValues = [];

			$('#constructionStatusFilter .property-filter:checked').each(function () {
				checkedValues.push($(this).val());
			});

			filtersData['constructionStatus'] = checkedValues;
			filtersData['pageId'] = 1;

			if (apply) {
				applyFilters(filtersData);
			}
		}

		// Furnishing Type
		function updateFurnishingType(apply = true) {
			const checkedValues = [];

			$('#furnishingTypeFilter .property-filter:checked').each(function () {
				checkedValues.push($(this).val());
			});

			filtersData['furnishingType'] = checkedValues;
			filtersData['pageId'] = 1;

			if (apply) {
				applyFilters(filtersData);
			}
		}

        // Read query params and pre-check filters. This runs once on load.
        function applyQueryParams() {
            const params = new URLSearchParams(window.location.search);
            // configuration (could be single or multiple comma-separated)
            if (params.has('configuration')) {
                const conf = params.get('configuration');
                const confs = conf.includes(',') ? conf.split(',') : [conf];
                confs.forEach(function (c) {
                    // check matching checkbox
                    $(".configuration-filter[value='" + c + "']").prop('checked', true);
                });
                filtersData['configuration'] = confs;
            }

            // location (supports location, location[], and comma-separated values)
            const locs = params.getAll('location[]')
                .concat(params.getAll('location'))
                .flatMap(function (value) { return String(value).split(','); })
                .map(function (value) { return value.trim(); })
                .filter(Boolean);
            if (locs.length > 0) {
                locs.forEach(function (l) {
                    $(".location-filter").filter(function () {
                        return $(this).val().toLowerCase().trim() === l.toLowerCase().trim();
                    }).prop('checked', true);
                });
                filtersData['location'] = locs;
            }

            // mapping for listingType & propertyType (optional)
            if (params.has('listingType')) {
                const lt = params.get('listingType');
                $("#" + lt.toLowerCase()).prop('checked', true);
                filtersData['listingType'] = [lt];
            }

            if (params.has('propertyType')) {
                const pt = params.get('propertyType');
                $(".property-filter[value='" + pt + "']").prop('checked', true);
                filtersData['propertyType'] = [pt];
            }

            if (params.has('keyword') || params.has('q') || params.has('search')) {
                const kw = (params.get('keyword') || params.get('q') || params.get('search') || '').trim();
                if (kw) {
                    $("#search-input").val(kw);
                    filtersData['search'] = kw;
                }
            }

            // if any filters were set from query, apply them
            if (Object.keys(filtersData).length > 0) {
                filtersData['pageId'] = 1;
                applyFilters(filtersData);
            }
        }

        // Budget Slider
        function formatPrice(value) {
            if (value >= 10000000) {
                return "₹" + (value / 10000000).toFixed(2).replace(/\.00$/, "") + " Cr";
            } else if (value >= 100000) {
                return "₹" + (value / 100000).toFixed(2).replace(/\.00$/, "") + " Lakh";
            }
            return "₹" + value.toLocaleString("en-IN");
        }

        var sliderMin = {{ (int) ($minPrice ?? 0) }};
        var sliderMax = {{ (int) ($maxPrice ?? 0) }};
        if (sliderMax <= sliderMin) {
            sliderMax = sliderMin + 100000;
        }

        $("#slider-range").slider({
            range: true,
            min: sliderMin,
            max: sliderMax,
            values: [sliderMin, sliderMax],
            slide: function (event, ui) {
                $("#amount-min").text(formatPrice(ui.values[0]));
                $("#amount-max").text(formatPrice(ui.values[1]));
                filtersData['budget'] = { 'min': ui.values[0], 'max': ui.values[1] };
            },
            stop: function (event, ui) {
                filtersData['budget'] = { 'min': ui.values[0], 'max': ui.values[1] };
                filtersData['pageId'] = 1;
                applyFilters(filtersData);
            }
        });

        // Set default values when page loads
        $("#amount-min").text(formatPrice($("#slider-range").slider("values", 0)));
        $("#amount-max").text(formatPrice($("#slider-range").slider("values", 1)));

        function formatText(text) {
            if (!text) return "";
            return text
                .replace(/_/g, " ")                 // replace underscores with spaces
                .replace(/\b\w/g, c => c.toUpperCase()); // capitalize each word
        }
        // Sort Handling
        $('.sort-link').on('click', function () {
            $('.sort-link').removeClass('fw-bold').addClass('text-muted');
            $(this).addClass('fw-bold').removeClass('text-muted');
            filtersData['sorting'] = $(this).attr('data-filter');
            filtersData['pageId'] = 1;
            applyFilters(filtersData);
        });

        // Pagination
        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            filtersData['pageId'] = $(this).data('page');
            applyFilters(filtersData);
        });

        $(document).on('input', '[data-filter-search]', function () {
            const query = $(this).val().trim().toLowerCase();
            const list = document.getElementById($(this).attr('data-filter-search'));
            if (!list) {
                return;
            }
            list.querySelectorAll('[data-filter-label]').forEach(function (row) {
                const label = row.getAttribute('data-filter-label') || '';
                row.hidden = query !== '' && label.indexOf(query) === -1;
            });
        });

        $(document).on('click', '[data-toggle-more]', function () {
            const list = document.getElementById($(this).attr('data-toggle-more'));
            if (!list) {
                return;
            }
            list.classList.toggle('is-expanded');
            $(this).text(list.classList.contains('is-expanded') ? 'Show less' : 'Show more');
        });

        $(document).on('click', '#resetFilters', function (e) {
            e.preventDefault();
            $('#filter-form input[type="checkbox"]').prop('checked', false);
            if ($("#slider-range").data("ui-slider")) {
                const min = $("#slider-range").slider("option", "min");
                const max = $("#slider-range").slider("option", "max");
                $("#slider-range").slider("values", [min, max]);
                $("#amount-min").text(formatPrice(min));
                $("#amount-max").text(formatPrice(max));
            }
            filtersData = { pageId: 1 };
            applyFilters(filtersData);
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, '', '{{ url("properties") }}');
            }
        });

        var propertySearchTimer = null;
        $("#search-input").on("keyup change", function () {
            const searchValue = $(this).val().trim();
            filtersData['pageId'] = 1;
            if (searchValue !== '') {
                filtersData['search'] = searchValue;
            } else {
                delete filtersData['search'];
            }
            clearTimeout(propertySearchTimer);
            propertySearchTimer = setTimeout(function () {
                applyFilters(filtersData);
            }, 400);
        });


        // AJAX Request
        function applyFilters(filtersData) {
            var pageNumber = (filtersData && filtersData['pageId'] !== undefined) ? filtersData['pageId'] : 1;
            $('#property-list').html('<div class="text-center"><h4>Loading....</h4></div>');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                url: "{{ route('property.filters', [], false) }}?page=" + pageNumber,
                method: "POST",
                data: JSON.stringify({ 'filters': filtersData }),
                success: function (response) {
                    $('#property-list').html('');
                    $('.pagination').html('');

                    const items = (response && response.data && Array.isArray(response.data.data))
                        ? response.data.data
                        : [];

                    if (!response || response.status === false || items.length === 0) {
                        $('#property-list').html('<h4 class="text-center">No properties found</h4>');
                        $('#results-count').text('0 Results');
                        $('#results-title').text((response && response.dynamicTitle) ? response.dynamicTitle : 'All Properties');
                        return;
                    }

                    let html = '';
                    items.forEach(function (property) {
                            const galleries = Array.isArray(property.galleries) ? property.galleries : [];
                            html += `
    <div class="property-card mb-3"
         data-type="${property.property_type || ''}"
         data-bhk="${property.configuration || ''}"
         data-price="${property.total_price || ''}"
         data-location="${property.city || ''}">

        <!-- Property Images -->
    <div class="property-image">
        <div class="slider-container">
            <div id="propertyCarousel${property.id}"
                 class="carousel slide slider"
                 data-bs-ride="carousel"
                 data-bs-interval="2000">

                <div class="carousel-inner">
                    ${galleries.length > 0
                                    ? galleries.map((gallery, key) => `
                            <div class="carousel-item slide ${key === 0 ? 'active' : ''}">
                                <img src="/storage/${gallery}" alt="${property.title || 'Property'}">
                            </div>
                        `).join('')
                                    : `
                            <div class="carousel-item slide active">
                                <img src="/images/no-image.jpg" alt="No Image">
                            </div>
                        `
                                }
                </div>
            </div>
        </div>
    </div>

        <!-- Property Details -->
        <div class="property-details">
            <a href="{{url('properties')}}/${property.slug}">
                <h2 class="m-0 property-title">${property.title}</h2> 
            </a>

            <div class="location"><p>
                <i class="fa-solid fa-location-dot"></i>
                ${property.city}
                </p>
            </div>

           <div class="info-blocks">
                ${property.furnishing_types
                                    ? `<p class="info-item m-0"><i class="fa-solid fa-couch"></i> ${formatText(property.furnishing_types)}</p>`
                                    : ''}

                ${property.configuration
                                    ? `<p class="info-item m-0"><i class="fa-solid fa-building"></i> ${formatText(property.configuration)}</p>`
                                    : ''}

                ${property.listing_type
                                    ? `<p class="info-item m-0"><i class="fa-solid fa-hand-holding-dollar"></i>${formatText(property.listing_type)}</p>`
                                    : ''}
            </div>

            <div class="location-details">
                <p class="m-0">
                <i class="fa-solid fa-location-arrow"></i>
                ${formatText(property.construction_status)}
                </p>
            </div>
        </div>

        <!-- Price Section -->
        <div class="price-section">
            <div class="price-info">
                <div><p class="price m-0">${formatPrice(property.total_price).toLocaleString()}</p></div>
                <div class="price-details">
                    ${property.listing_type === 'rent' ? 'per month' : 'total price'}
                </div>
                ${property.price_details
                                    ? `<div class="other-charges">+ See other charges</div>`
                                    : ''}
            </div>
            <div class="button-group">
                <a href="tel:+919643020020" class="btn contact-owner rounded">
                    <i class="fa-solid fa-phone"></i> Contact
                </a>

                <button class="btn check-availability rounded" data-bs-toggle="modal" data-bs-target="#contactModalPopup">
                    <i class="fa-solid fa-calendar-check"></i> Availability
                </button>
            </div>
        </div>
    </div>`;
                        });

                        $('#property-list').append(html);


                        // Results count
                        $('#results-count').text((response.totalResults || items.length) + ' Results');
                        $('#results-title').text(response.dynamicTitle || 'All Properties');


                        // ===== Pagination =====
                        let lastPage = (response.pagination && response.pagination.last_page) ? response.pagination.last_page : 1;
                        let currentPage = (response.pagination && response.pagination.current_page) ? response.pagination.current_page : 1;
                        let pagination = '';

                        // Previous Button
                        let prevClass = (currentPage > 1) ? '' : 'disabled';
                        let prevPage = (currentPage > 1) ? currentPage - 1 : 1;
                        pagination += `<li class="page-item ${prevClass}">
                                     <a class="page-link" data-page="${prevPage}">Previous</a>
                                   </li>`;

                        if (lastPage <= 5) {
                            // Show all pages if lastPage <= 5
                            for (let i = 1; i <= lastPage; i++) {
                                let activeClass = (i === currentPage) ? 'active' : '';
                                pagination += `<li class="page-item ${activeClass}">
                                             <a class="page-link" data-page="${i}">${i}</a>
                                           </li>`;
                            }
                        } else {
                            // Show first page and ellipsis if needed
                            if (currentPage > 3) {
                                pagination += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
                                if (currentPage > 4) {
                                    pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                }
                            }

                            // Show current -1, current, current +1
                            let start = Math.max(1, currentPage - 1);
                            let end = Math.min(lastPage, currentPage + 1);

                            for (let i = start; i <= end; i++) {
                                let activeClass = (i === currentPage) ? 'active' : '';
                                pagination += `<li class="page-item ${activeClass}">
                                             <a class="page-link" data-page="${i}">${i}</a>
                                           </li>`;
                            }

                            // Show last page and ellipsis if needed
                            if (currentPage < lastPage - 2) {
                                if (currentPage < lastPage - 3) {
                                    pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                }
                                pagination += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
                            }
                        }

                        // Next Button
                        let nextClass = (currentPage < lastPage) ? '' : 'disabled';
                        let nextPage = (currentPage < lastPage) ? currentPage + 1 : lastPage;
                        pagination += `<li class="page-item ${nextClass}">
                                     <a class="page-link" data-page="${nextPage}">Next</a>
                                   </li>`;

                        $('.pagination').html(pagination);
                },
                error: function () {
                    $('#property-list').html('<h4 class="text-center">No properties found</h4>');
                    $('#results-count').text('0 Results');
                    $('#results-title').text('All Properties');
                    $('.pagination').html('');
                }
            });
        }

    </script>
    <script>
        const toggleBox = document.querySelector('.hideicon');
        const wrapper = document.getElementById('collapseWrapper');

        toggleBox.addEventListener('click', () => {
            document.querySelector(".hideicon").style.display = "none"
			document.querySelector(".showicon").style.display = "inline"
            wrapper.classList.add('expanded');

        });
        document.querySelector(".showicon").addEventListener("click", function () {
            wrapper.classList.remove('expanded');
            document.querySelector(".hideicon").style.display = "block"
			document.querySelector(".showicon").style.display = "none"
        });
</script>
@endsection

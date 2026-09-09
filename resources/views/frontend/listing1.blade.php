
@extends('frontend.layouts.app')

@push('schema')
    {!! @$schema !!}
@endpush


@section('title', isset($title) && $title ? $title  : '360 PropGuide | Explore Top Real Estate Projects in NCR')

@section('description', isset($description) && $description ? $description : 'Browse 360 PropGuide projects in Noida, Greater Noida & Delhi NCR. Get details on price, design, amenities & availability for your dream property.')

@section('keywords', (isset($keywords) && $keywords) ? $keywords : 'real estate projects, property listings, property pricing')

@section('canonical', isset($canonical) && $canonical ? $canonical : url()->current())

@section('customCSS')

<link rel="stylesheet" href="{{url('frontend/libraries/nouislider.min.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/listing.css')}}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

@endSection
@section('content')

<section>
    <div class="container my-4 border-bottom pb-2">
        <div class="row">
            <div class="col-sm-12">
                <div class="row align-items-center justify-content-between">
                    <div class="d-flex w-fit">
                        <h2 class="text-decoration-none text-dark customFont me-2 fw-medium h5 d-none d-md-block">Sort By:</h2>

                        <!-- Dropdown for smaller screens -->
                        <div class="d-md-none h-100">
                            <div id="sort-form">
                                @csrf
                                <input type="hidden" name="sort" value="sort" />
                                <select name="filters[sorting]" id="filter" class="form-select shadow-none">
									<option value="">Sort</option>
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
                        <div id="search-form" class="d-flex w-100 d-none d-md-block">

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
<section>
    <div class="container">
        <div class="row">

            <!-- Sidebar Filters (visible only on desktop) -->
            <div class="col-lg-3 mb-5 d-none d-lg-block">
			  <div id="desktopFilterContent">

                <div class="filter-box recommended h-fit" id="filter-form">
                    @csrf
                    <input type="hidden" name="filters" value="filters" />

                    <div class="filter-header">
                        <span>Filters</span>
                        <a id="resetFilters">Reset All</a>
                    </div>

                    <div id="resetAmount">
                        <div class="d-flex justify-content-between" id="amount"></div>
                        <div id="slider-range"></div>
                    </div>

                    <!-- Possession -->
                    <div class="Possession py-3" id="resetPossession">
                        <div class="filter-section-title mb-3">Possession</div>
                        <div class="filter-checkbox">
                            <input onchange="updatePossession()" type="checkbox" class="possession-filter"
                                name="possession[1]" value="New Launch" id="new-launch"
                                {{ (isset($filters['project_status']) && $filters['project_status']=='new_launch') ? 'checked' : '' }}>
                            <label for="new-launch">New Launch</label>
                        </div>
                        <div class="filter-checkbox">
                            <input onchange="updatePossession()" type="checkbox" class="possession-filter"
                                name="possession[2]" value="Under Construction" id="under-construction"
                                {{ (isset($filters['project_status']) && $filters['project_status']=='under_construction') ? 'checked' : '' }}>
                            <label for="under-construction">Under Construction</label>
                        </div>
                        <div class="filter-checkbox">
                            <input onchange="updatePossession()" type="checkbox" class="possession-filter"
                                name="possession[3]" value="Ready to Move" id="ready-move"
                                {{ (isset($filters['project_status']) && $filters['project_status']=='ready_to_move') ? 'checked' : '' }}>
                            <label for="ready-move">Ready To Move</label>
                        </div>
                        <div class="filter-checkbox">
                            <input onchange="updatePossession()" type="checkbox" class="possession-filter"
                                name="possession[4]" value="within_a_year" id="within-year">
                            <label for="within-year">Possession Within A Year</label>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div class="property-type bhk py-3" id="resetProperty">
                        <div class="filter-section-title mb-3"> Property Type</div>
                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="1bhk"
                            value="1 BHK" name="propertyType[1]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='1 BHK') ? 'checked' : '' }}>
                        <label for="1bhk" class="property-btn">1 BHK</label>

                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="2bhk"
                            value="2 BHK" name="propertyType[2]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='2 BHK') ? 'checked' : '' }}>
                        <label for="2bhk" class="property-btn">2 BHK</label>

                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="3bhk"
                            value="3 BHK" name="propertyType[3]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='3 BHK') ? 'checked' : '' }}>
                        <label for="3bhk" class="property-btn">3 BHK</label>

                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="4bhk"
                            value="4 BHK" name="propertyType[4]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='4 BHK') ? 'checked' : '' }}>
                        <label for="4bhk" class="property-btn">4 BHK</label>

                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="5bhk"
                            value="5 BHK" name="propertyType[5]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='5 BHK') ? 'checked' : '' }}>
                        <label for="5bhk" class="property-btn">5 BHK</label>
						<input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="6bhk"
                            value="6 BHK" name="propertyType[9]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='6 BHK') ? 'checked' : '' }}>
                        <label for="6bhk" class="property-btn">6 BHK</label>

                        <input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="plots"
                            value="Plots" name="propertyType[6]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='Plots') ? 'checked' : '' }}>
                        <label for="plots" class="property-btn mt-2">Plots</label>
						<input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="shops"
                            value="Shops" name="propertyType[7]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='Shops') ? 'checked' : '' }}>
                        <label for="shops" class="property-btn mt-2">Shops</label>
						<input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="office"
                            value="Office" name="propertyType[9]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='Office') ? 'checked' : '' }}>
                        <label for="office" class="property-btn mt-2">Office Space</label>
						<input onchange="updatePropertyType()" type="checkbox" class="property-filter" id="studio"
                            value="Studio Apartments" name="propertyType[8]"
                            {{ (isset($filters['typologyToRender']) && $filters['typologyToRender']=='Studio Apartments') ? 'checked' : '' }}>
                        <label for="studio" class="property-btn mt-2">Studio Apartments</label>
                    </div>

                    <!-- Location -->
                    <div class="location py-3 property-type location" id="resetLocation">
                        <div class="filter-section-title mb-3">Location</div>
                        @foreach ($locations as $index => $location)
                            @php
                                $isChecked = isset($filters['location']) && in_array($location, (array) $filters['location']);
                                if (!$isChecked && isset($filters['city'])) {
                                    $isChecked = strtolower($filters['city']) === strtolower($location);
                                }
                            @endphp
                            <input onchange="updateLocation(); handleCityChange()" type="checkbox" class="property-filter"
                                id="location-{{ $index }}" value="{{ $location }}" name="location[{{ $index }}]"
                                {{ $isChecked ? 'checked' : '' }}>
                            <label for="location-{{ $index }}" class="property-btn mt-2">{{ $location }}</label>
                        @endforeach
                    </div>

					<!-- Locality -->
                    <div class="py-3 property-type locality" id="resetLocality">
						<div class="filter-section-title mb-3">Localities</div>

						@foreach ($locality as $index => $localities)
							@php
								$isChecked = isset($filters['locality']) && in_array($localities, (array) $filters['locality']);
							@endphp

							<input type="checkbox"
							   onchange="updateLocality()"
							   class="property-filter locality-item {{ $index >= 5 ? 'd-none extra-location' : '' }}"
							   id="localities-{{ $index }}"
							   value="{{ $localities }}"
							   name="locality[{{ $index }}]"
							   data-city="{{ $localityCityMap[$localities] ?? '' }}"
							   data-index="{{ $index }}"
							   {{ $isChecked ? 'checked' : '' }}>

							<label for="localities-{{ $index }}"
							       class="property-btn mt-2 {{ $index >= 5 ? 'd-none extra-location' : '' }}">
								{{ $localities }}
							</label>
						@endforeach

						@if(count($locality) > 5)
							<button type="button" class="btn btn-link p-0 mt-2" id="toggleLocality">Show More</button>
						@endif
					</div>

					<div class="developer py-3 property-type developer" id="resetDeveloper">
						<div class="filter-section-title mb-3">Developers</div>

						@foreach ($developers as $index => $developer)
							@php
								$isChecked = isset($filters['developer']) && in_array($developer, (array) $filters['developer']);
							@endphp

							<input onchange="updateDeveloper()"
							   type="checkbox"
							   class="property-filter {{ $index >= 5 ? 'd-none extra-developer' : '' }}"
							   id="developer-{{ $index }}"
							   value="{{ $developer->id }}"
							   name="developer[{{ $index }}]"
							   {{ $isChecked ? 'checked' : '' }}>

						<label for="developer-{{ $index }}"
							   class="property-btn mt-2 {{ $index >= 5 ? 'd-none extra-developer' : '' }}">
							   {{ $developer->developer_name }}
						</label>

						@endforeach

						@if(count($developers) > 5)
							<button type="button" class="btn btn-link p-0 mt-2" id="toggleDevelopers">Show More</button>
						@endif
					</div>

                </div>
				</div>
            </div>

            <!-- Projects List -->
            <div class="col-lg-9">
                @if (isset($projects['message']))
                    <div class="mb-5 fw-bold text-center">
                        {{ $projects['message'] }}
                    </div>
                @else
                    <?php if (!empty($dynamicTitle)) : ?>
						<div style="display:flex; align-items:center; gap:10px;">
							<p class="results-count fs-6 mb-0 fw-bold" >
								<?php echo $totalResultsText; ?> 
							</p>
							<h1 class="fs-6 fw-bold" id="resultsHeading" style="margin:0;">
								<?php echo $dynamicTitle; ?>
							</h1>
						</div>
					<?php endif; ?>

                    <div class="collapseContainer mt-3">
                        <div class="collapseWrapper" id="collapseWrapper">
                            <span class="collapseText" id="collapseText">
                                <?php
                                if(isset($links_description)){
                                    echo "<p>".$links_description.'<span class="showicon" style="display:none;"><i class="fa-solid fa-angle-up icon "></i></span></p>';
                                }
                                ?>
                            </span>
                            <span class="hideicon">
                                <i class="fa-solid fa-angle-down icon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="row defaultSpace" id="project-list">       
						@if(isset($projects) && $projects->count() > 0)
							@foreach($projects as $project)
								<div class="col-sm-6 col-md-4 col-lg-4 pb-4">
									<a href="{{ route('projects.details', $project) }}" style="text-decoration:none;">
										<div class="pb-4 customborder position-relative projectCard border">
											<div class="statusIcon text-capitalize">{{ $project->project_status }}</div>
											<div>
												<img src="{{ url('storage/'.$project->logo_image) }}" class="w-100" alt="{{ $project->project_name }} 360 PropGuide">
											</div>
											<h3 class="ms-3 mt-3 h5">{{ $project->project_name }}</h3>
											<div class="ms-3 customFontColour">
												<i class="fa-solid fa-house"></i>
												<div><p class="m-0">{{ $project->typology }}</p></div>
											</div>
											<div class="ms-3 customFontColour"><i class="fa-solid fa-location-dot"></i>
												<div class="limit1"><p class="m-0">{{ $project->location }}</p></div>
											</div>
											<h5 class="ms-3">
												<i class="fa-solid fa-indian-rupee-sign"></i>
												₹{{ formatPrice($project->price) }}{{ !empty($project->max_price) ? ' - ₹'.formatPrice($project->max_price) : '' }}
											</h5>
										</div>
									</a>
								</div>
							@endforeach
						@else
							<div class="defaultSpace text-center"><h4>No data Available</h4></div>
						@endif
                    </div>
                @endif

                <div id="pagination-links">
                    <ul class="pagination">
                        @if(isset($projects) && $projects->lastPage() > 1)
                            @php
                                $currentPage = $projects->currentPage();
                                $lastPage = $projects->lastPage();
                                $startPage = max(1, $currentPage - 1);
                                $endPage = min($lastPage, $currentPage + 1);
                            @endphp
                            <li class="page-item {{ $projects->onFirstPage() ? 'disabled' : '' }}"><a class="page-link">Previous</a></li>

                            @if($lastPage <= 5)
                                @for($page = 1; $page <= $lastPage; $page++)
                                    <li class="page-item {{ $currentPage === $page ? 'active' : '' }}"><a class="page-link" data-page="{{ $page }}">{{ $page }}</a></li>
                                @endfor
                            @else
                                @if($currentPage > 3)
                                    <li class="page-item"><a class="page-link" data-page="1">1</a></li>
                                    @if($currentPage > 4)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                @endif

                                @for($page = $startPage; $page <= $endPage; $page++)
                                    <li class="page-item {{ $currentPage === $page ? 'active' : '' }}"><a class="page-link" data-page="{{ $page }}">{{ $page }}</a></li>
                                @endfor

                                @if($currentPage < $lastPage - 2)
                                    @if($currentPage < $lastPage - 3)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                    <li class="page-item"><a class="page-link" data-page="{{ $lastPage }}">{{ $lastPage }}</a></li>
                                @endif
                            @endif

                            <li class="page-item {{ $currentPage === $lastPage ? 'disabled' : '' }}"><a class="page-link">Next</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


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
	  <button type="button" class="btn p-2 rounded border-0 orange text-white fw-medium" data-bs-dismiss="offcanvas">
	  Show All Projects
	</button>
	</div>
</div>

@endSection 

@section('customJS')
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    $(document).ready(function () {
            updatePossession(false)
            updatePropertyType(false)
            updateLocation(false)
            updateDeveloper(false)
            updateLocality(false)

    // Apply luxury filter on load
    if (isLuxury) {
        applyFilters(filtersData);
    }
	})

    var filtersData = {};

	let urlPath = window.location.pathname.toLowerCase();
	let isLuxury = urlPath.includes('luxury');

	// default values from blade
	let defaultMin = {{ $minPrice }};
	let defaultMax = {{ $maxPrice }};

	// override if luxury
	if (isLuxury) {
		defaultMin = 30000000; // 3 Cr
		filtersData['budget'] = { min: defaultMin, max: defaultMax };
	}

    // Initialize filtersData with existing Possession (if any)
       if (typeof window.updatePossession !== 'function') {
            function updatePossession(apply = true) {
                const checkedValues = [];
                const checkboxes = document.querySelectorAll('.Possession .possession-filter:checked');

                checkboxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });
                filtersData['possession'] = checkedValues;
                filtersData['pageId'] = 1; // Reset page to 1
                if (apply) {
                    applyFilters(filtersData);
                }
            }
        }

        if (typeof window.updateLocality !== 'function') {
            function updateLocality(apply = true) {
                const checkedValues = [];
                const checkboxes = document.querySelectorAll('#resetLocality .property-filter:checked');

                checkboxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });

                filtersData['locality'] = checkedValues;
                filtersData['pageId'] = 1;
                if (apply) {
                    applyFilters(filtersData);
                }
            }
        }


        if (typeof window.updatePropertyType !== 'function') {
            function updatePropertyType(apply = true) {
                const checkedValues = [];
                const checkboxes = document.querySelectorAll('.bhk .property-filter:checked');

                checkboxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });
                filtersData['propertyType'] = checkedValues;
                filtersData['pageId'] = 1; // Reset page to 1
                if (apply) {
                    applyFilters(filtersData);
                }
            }
        }

        if (typeof window.updateLocation !== 'function') {
            function updateLocation(apply = true) {
                const checkedValues = [];
                const checkboxes = document.querySelectorAll('#resetLocation .property-filter:checked');

                checkboxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });

                filtersData['location'] = checkedValues;
                filtersData['pageId'] = 1; // Reset page to 1
                if (apply) {
                    applyFilters(filtersData);
                }
            }
        }

        if (typeof window.updateDeveloper !== 'function') {
            function updateDeveloper(apply = true) {
                const checkedValues = [];
                const checkboxes = document.querySelectorAll('.developer .property-filter:checked');

                checkboxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });

                filtersData['developer'] = checkedValues;
                filtersData['pageId'] = 1; // Reset page to 1
                if (apply) {
                    applyFilters(filtersData);
                }
            }
        }


    $("#slider-range").slider({
        range: true,
        min: {{ $minPrice }},
        max: {{ $maxPrice }},
        values: [defaultMin, defaultMax],
        slide: function (event, ui) {
            $("#amount").html(
                "<div class='my-3'>" + formatPrice(ui.values[0]) + "</div> <div class='my-3'>-</div> <div class='m-3'>" + formatPrice(ui.values[1]) + "</div>"
            );
            filtersData['budget'] = { 'min': ui.values[0], 'max': ui.values[1] };
        },
        stop: function (event, ui) {
            filtersData['budget'] = { 'min': ui.values[0], 'max': ui.values[1] };
            filtersData['pageId'] = 1; // Reset page to 1
            applyFilters(filtersData);
        }
    });

    $("#amount").html("<div class='my-3'>" + formatPrice(defaultMin) + "</div> <div class='my-3'>-</div><div class='my-3'>" + formatPrice(defaultMax) + "</div>");

    $('.sort-link').on('click', function () {
        $('.sort-link').removeClass('fw-bold').addClass('text-muted');
        $(this).addClass('fw-bold').removeClass('text-muted');
        filtersData['sorting'] = $(this).attr('data-filter');
        filtersData['pageId'] = 1; // Reset page to 1
        applyFilters(filtersData);
    })

    $(document).on('click', '.page-link', function () {
        filtersData['pageId'] = $(this).text();
        var currentPage = $(".page-item.active");
        var currentPageId = parseInt(currentPage.find('a').data('page'));
        if ($(this).text() === 'Next') {
            var nextPageId = currentPageId + 1;
            filtersData['pageId'] = nextPageId;
        } else if ($(this).text() === 'Previous') {
            var PrevPageId = currentPageId - 1;
            filtersData['pageId'] = PrevPageId;
        }
        applyFilters(filtersData);
    })

    $("#search-input").on("keyup change keydown ", function () {
        const searchValue = $(this).val().trim().replace(/[^a-zA-Z0-9\s]/g, '');
        filtersData['pageId'] = 1; //  Reset page to 1
        searchValue !== ''
            ? (filtersData['search_params'] = searchValue, applyFilters(filtersData))
            : applyFilters();
    });

    // AJAX HIT FOR THE Filters
    if (typeof window.applyFilters !== 'function') {
        function applyFilters(filtersData) {

            var pageNumber = (filtersData && filtersData['pageId'] !== undefined) ? filtersData['pageId'] : 1;
            //$('#project-list').html('<div class="defaultSpace text-center"><h4>Loading....</h4></div>');

            setTimeout(function () {
                console.log(filtersData)
				var getParams = '';
                if(window.location.search.substr(1)){
                    getParams = getParams + '&'+ window.location.search.substr(1);
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    url: "{{route('filters')}}?page=" + pageNumber+getParams,
                    method: "POST",
                    data: JSON.stringify({
                        'filters': filtersData
                    }),
                    success: function (response) {
                        if (response.status) {
							if (response.dynamicTitle) {
								$('.results-count').text(response.totalResultsText+ ' |');
								$('#resultsHeading').text(response.dynamicTitle);
							}

                            $('.pagination').html('');
                            $('#project-list').html('');
                            var html = '';
                            response.data.data.forEach(function (project) {
                                html += `<div class="col-sm-6 col-md-4 col-lg-4  pb-4">
                                    <a href="{{url('projects')}}/${project.slug}" style="text-decoration:none;">
                                        <div class="pb-4 customborder position-relative projectCard border">

										<div class="badgeWrapper">

											<div class="statusIcon text-capitalize">
												${project.project_status}
											</div>

											${
												project.rera_no &&
												project.rera_no !== 'N/A' &&
												project.rera_no !== 'n/a'
												? `
													<div class="reraApprovedBadge">
														<i class="fa-solid fa-circle-check"></i>
														RERA
													</div>
												`
												: ''
											}

										</div>
                                            <div>
                                                <img src="${project.logo_image}" class="w-100"
                                                    alt="${project.project_name} 360 PropGuide">
                                            </div>
                                            <h3 class="ms-3 mt-3 h5">${project.project_name}</h3>
                                            <div class=" ms-3 customFontColour">
                                                <i class="fa-solid fa-house"></i>
                                                <div><p class="m-0"> ${project.typology}</p></div>
                                            </div>
                                            <div class="ms-3 customFontColour"><i class="fa-solid fa-location-dot"></i>
                                                <div class="limit1"><p class="m-0">${project.location}</p></div>
                                            </div>
                                            <h5 class="ms-3">
												<i class="fa-solid fa-indian-rupee-sign"></i>
												${formatPrice(project.price)}${project.max_price ? ` - ${formatPrice(project.max_price)}` : ''}
											</h5>

                                        </div>
                                    </a>
                                </div>`;
                            });
                            var lastPage = response.pagination.last_page;
                            var currentPage = response.pagination.current_page;
                            var pagination = '';

                            var prevClass = (currentPage > 1) ? '' : 'disabled';
                            pagination += `<li class="page-item ${prevClass}"><a class="page-link">Previous</a></li>`;

                            if (lastPage <= 5) {
                                for (let i = 1; i <= lastPage; i++) {
                                    let activeClass = (i === currentPage) ? 'active' : '';
                                    pagination += `<li class="page-item ${activeClass}"><a class="page-link" data-page="${i}">${i}</a></li>`;
                                }
                            } else {
                                if (currentPage > 3) {
                                    pagination += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
                                    if (currentPage > 4) {
                                        pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                    }
                                }

                                let start = Math.max(1, currentPage - 1);
                                let end = Math.min(lastPage, currentPage + 1);

                                for (let i = start; i <= end; i++) {
                                    let activeClass = (i === currentPage) ? 'active' : '';
                                    pagination += `<li class="page-item ${activeClass}"><a class="page-link" data-page="${i}">${i}</a></li>`;
                                }

                                if (currentPage < lastPage - 2) {
                                    if (currentPage < lastPage - 3) {
                                        pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                    }
                                    pagination += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
                                }
                            }

                            var nextClass = (currentPage < lastPage) ? '' : 'disabled';
                            pagination += `<li class="page-item ${nextClass}"><a class="page-link">Next</a></li>`;

                            if (response.data.data.length > 0) {
                                $('.pagination').append(pagination);
                                $('#project-list').append(html)
                            } else {
                                $('#project-list').html('<div class="defaultSpace text-center"><h4>No data Available</h4></div>');
                                $('.pagination').html('');
                            }
                        } else {

                        }
                    },
                    processData: false,
                    contentType: false,
                    cache: false,
                    error: function (data) {
                    }
                });
            }, 100)
        }
    }
	$(document).on("change", "#filter", function () {
        let selectedValue = $(this).val();
        if (selectedValue && selectedValue !== "Sort") {
            filtersData['sorting'] = selectedValue;
            filtersData['pageId'] = 1;
            applyFilters(filtersData);
        }
    });
</script>
<script>
$('#resetFilters').on('click', function () {
    // 1. Uncheck all filter checkboxes
    $('.possession-filter').prop('checked', false);
    $('.property-filter').prop('checked', false);

    // 2. Clear search input
    $('#search-input').val('');

    // 3. Reset price slider to default min-max
    $("#slider-range").slider("values", [{{ $minPrice }}, {{ $maxPrice }}]);
    $("#amount").html("<div class='my-3'>" + formatPrice({{ $minPrice }}) + "</div> <div class='my-3'>-</div><div class='my-3'>" + formatPrice({{ $maxPrice }}) + "</div>");

    // 4. Reset filtersData and page
    filtersData = {};
    filtersData['pageId'] = 1;

    // 5. Apply filters (empty means show all)
    applyFilters(filtersData);
});


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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector("#filter-form");
    const mobileContainer = document.querySelector("#mobileFilterContent");
    const desktopContainer = document.querySelector("#desktopFilterContent");

    function moveFilters() {
        if (window.innerWidth < 768) {
            // move into offcanvas for mobile
            if (!mobileContainer.contains(filterForm)) {
                mobileContainer.appendChild(filterForm);
            }
        } else {
            // move back to desktop for larger screens
            if (!desktopContainer.contains(filterForm)) {
                desktopContainer.appendChild(filterForm);
            }
        }
    }

    // run once on load
    moveFilters();

    // run on resize
    window.addEventListener("resize", moveFilters);

    // show offcanvas when filter button clicked
    document.getElementById("filterToggle").addEventListener("click", function () {
        let filterOffcanvas = new bootstrap.Offcanvas(document.getElementById("mobileFilter"));
        filterOffcanvas.show();
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleDevelopers");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            const hiddenItems = document.querySelectorAll(".extra-developer");
            hiddenItems.forEach(item => item.classList.toggle("d-none"));

            toggleBtn.innerText = toggleBtn.innerText === "Show More" ? "Show Less" : "Show More";
        });
    }
});
</script>
<script>
let localityLimit = 5;

function handleCityChange() {
    localityLimit = 5;
    applyLocalityFilter();
}

function applyLocalityFilter(showAll = false) {
    const selectedCities = Array.from(
        document.querySelectorAll('input[name^="location"]:checked')
    ).map(el => el.value.toLowerCase());

    const allLocalities = document.querySelectorAll('.locality-item');
    let visibleCount = 0;

    allLocalities.forEach(item => {
        const localityCity = item.dataset.city?.toLowerCase();
        const matchCity =
            selectedCities.length === 0 || selectedCities.includes(localityCity);

        if (matchCity && (showAll || visibleCount < localityLimit)) {
            item.classList.remove('d-none');
            item.nextElementSibling?.classList.remove('d-none');
            visibleCount++;
        } else if (matchCity && showAll) {
            item.classList.remove('d-none');
            item.nextElementSibling?.classList.remove('d-none');
        } else {
            item.classList.add('d-none');
            item.nextElementSibling?.classList.add('d-none');
            if (!item.checked) {
                item.checked = false;
            }
        }
    });

    const totalMatched = Array.from(allLocalities).filter(item => {
        const city = item.dataset.city?.toLowerCase();
        return selectedCities.length === 0 || selectedCities.includes(city);
    }).length;

    const toggleBtn = document.getElementById('toggleLocality');
    if (toggleBtn) {
        toggleBtn.style.display = totalMatched > localityLimit ? 'inline-block' : 'none';
        toggleBtn.innerText = showAll ? 'Show Less' : 'Show More';
        toggleBtn.onclick = () => applyLocalityFilter(!showAll);
    }
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Directly run filter (no pre-selection from URL)
    if (typeof updateLocality === 'function') {
        updateLocality();
    }

    applyLocalityFilter();
});
</script>
@endsection


@if(!empty($projects) && count($projects) > 0)
@foreach($projects as $project)
<div class="col-sm-6 col-md-4 col-lg-4  pb-4">
    <a href="{{ route('projects.details', $project) }}" style="text-decoration:none;">
        <div class="pb-4 customborder border">
            <div>
                <img src="{{url( $project->logo_image)}}" class="w-100" alt="{{$project->project_name}} 360 PropGuide">
            </div>
            <h3 class="ms-3 mt-3 h5">{{$project->project_name}}</h3>
            @php
            $typologies = json_decode($project->typology, true);
            @endphp

            <div class="ms-3 customFontColour">
                <i class="fa-solid fa-house"></i> 
                <div><p class="m-0">{{ is_array($typologies) ? implode(', ', $typologies) : 'N/A' }}</p></div>
            </div>
            <div class="ms-3 customFontColour"><i class="fa-solid fa-location-dot"></i>
                <div class="limit1"><p class="m-0">{{$project->location}}</p></div>
            </div>
            <h5 class="ms-3">
                <i class="fa-solid fa-indian-rupee-sign"></i>
                ${formatPrice(project.price)}${project.max_price ? ` - ${formatPrice(project.max_price)}` : ''}
            </h5>
        </div>
    </a>
</div>
@endforeach
@endif
@section('customJS1')
<script>
	$(document).ready(function(){
		applyFilters();
	})

	var filtersData = {}; 

	// Initialize filtersData with existing Possession (if any)
	if (typeof window.updatePossession !== 'function') {
		function updatePossession() {
			const checkedValues = [];
			const checkboxes = document.querySelectorAll('.Possession .possession-filter:checked');
			
			checkboxes.forEach((checkbox) => {
				checkedValues.push(checkbox.value);
			});
			filtersData['possession'] = checkedValues;
			applyFilters(filtersData);
		}
	}

	// SLIDER RANGE
	$("#slider-range").slider({
		range: true,
		min: {{$minPrice}},
		max: {{$maxPrice}},
		values: [{{$minPrice}}, {{$maxPrice}}],
		slide: function (event, ui) {
			$("#amount").html("<div class='my-3'>" + formatPrice(ui.values[0]) + "</div> <div class='my-3'>-</div> <div class='m-3'>" + formatPrice(ui.values[1]) + "</div>");
			filtersData['budget'] = {'min': ui.values[0], 'max': ui.values[1]};
			applyFilters(filtersData);
		}
	});
	$("#amount").html("<div class='my-3'>" + formatPrice({{$minPrice}}) + "</div> <div class='my-3'>-</div><div class='my-3'>" + formatPrice({{$maxPrice}}) + "</div>");
	
	$('.sort-link').on('click', function(){
		$('.sort-link').removeClass('fw-bold').addClass('text-muted');
		$(this).addClass('fw-bold').removeClass('text-muted');
		filtersData['sorting'] = $(this).attr('data-filter');
		applyFilters(filtersData);
	})
	
	$(document).on('click', '.page-link', function(){
		filtersData['pageId'] = $(this).text();
		applyFilters(filtersData);
	})
	
	$("#search-input").on("keyup change keydown ", function() {
		const searchValue = $(this).val().trim().replace(/[^a-zA-Z0-9\s]/g, '');
		searchValue !== '' 
        ? (filtersData['search_params'] = searchValue, applyFilters(filtersData)) 
        : applyFilters(); 
	});

	// AJAX HIT FOR THE Filters
	if (typeof window.applyFilters !== 'function') {
		function applyFilters(filtersData) {
			var pageNumber = (filtersData && filtersData['pageId'] !== undefined) ? filtersData['pageId'] : 1;
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: "{{route('filters')}}?page=" + pageNumber,
				method: "POST",
				data: {
					'filters': filtersData
				},
				success: function(response) {
					
					if(response.status){
						$('.pagination').html('');
						$('#project-list').html('');
						var html = '';
						response.data.data.forEach(function(project){
							html += `<div class="col-sm-6 col-md-4 col-lg-4  pb-4">
								<a href="{{url('projects')}}/${project.slug}" style="text-decoration:none;">
									<div class="pb-4 customborder border">
										<div>
											<img src="${project.hero_images}" class="w-100"
												alt="${project.project_name} 360 PropGuide">
										</div>
										<div class="ms-3 mt-3 h5">${project.project_name}</div>
										<div class=" ms-3 customFontColour">
											<i class="fa-solid fa-house"></i>
											<div> ${project.typology}</div>
										</div>
										<div class="ms-3 customFontColour"><i class="fa-solid fa-location-dot"></i>
											<div class="limit1">${project.location}</div>
										</div>
										<h5 class="ms-3"> <i class="fa-solid fa-indian-rupee-sign"></i>
											${formatPrice(project.price)}
											Onwards*</h5>
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
							// Simple loop if total pages ≤ 5
							for (let i = 1; i <= lastPage; i++) {
								let activeClass = (i === currentPage) ? 'active' : '';
								pagination += `<li class="page-item ${activeClass}"><a class="page-link" data-page="${i}">${i}</a></li>`;
							}
						} else {
							// First page always
							if (currentPage > 3) {
								pagination += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
								if (currentPage > 4) {
									pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
								}
							}

							// Middle pages
							let start = Math.max(1, currentPage - 1);
							let end = Math.min(lastPage, currentPage + 1);

							for (let i = start; i <= end; i++) {
								let activeClass = (i === currentPage) ? 'active' : '';
								pagination += `<li class="page-item ${activeClass}"><a class="page-link" data-page="${i}">${i}</a></li>`;
							}

							// Last page always
							if (currentPage < lastPage - 2) {
								if (currentPage < lastPage - 3) {
									pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
								}
								pagination += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
							}
						}

						var nextClass = (currentPage < lastPage) ? '' : 'disabled';
						pagination += `<li class="page-item ${nextClass}"><a class="page-link">Next</a></li>`;

						if(response.data.data.length>0){
							$('.pagination').append(pagination);
							$('#project-list').append(html)	
						}else{
							$('#project-list').html('<div class="defaultSpace text-center"><h4>No data Available</h4></div>');
							$('.pagination').html('');
						}
					}else{
						
					}
				},
				error: function(data) {
				}
			});
		}
	}
 
</script> 
@endsection
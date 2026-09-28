@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {!! $breadcrumbHtml !!}
			@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error) 
							<li>{{ $error }}</li>  
						@endforeach
					</ul>
				</div>
			@endif
            <div class="contentCard projects">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="myForm" action="{{route('projects.update')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{base64_encode(@$projects->id)}}">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Project Name</label>
                                    <input class="form-control input @error('project_name') is-invalid @enderror "
                                        type="text" placeholder="Enter Project Name"
                                        value="{{old('project_name') ? old('project_name') : @$projects->project_name}}"
                                        name="project_name" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Rera Number</label>
                                    <input class="form-control input @error('rera_no') is-invalid @enderror "
                                        type="text" placeholder="Enter Rera Number"
                                        value="{{old('rera_no') ? old('rera_no') : @$projects->rera_no}}"
                                        name="rera_no">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Launch Date</label>
                                    <input class="form-control input @error('launch_date') is-invalid @enderror "
                                        type="text" placeholder="Enter Launch Date"
                                        value="{{old('launch_date') ? old('launch_date') : @$projects->launch_date}}"
                                        name="launch_date">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="developer_name">Developer Name</label>
                                    <input class="form-control input @error('developer_name') is-invalid @enderror "
                                        type="text" placeholder="Enter Developer Name"
                                        value="{{old('developer_name') ? old('developer_name') : @$projects->developer_name}}"
                                        name="developer_name">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="property_size">Property Size <span class="text-danger">(Ex. 2
                                            Acres*)</span></label>
                                    <input class="form-control input @error('property_size') is-invalid @enderror "
                                        type="text" placeholder="Enter Property Size"
                                        value="{{old('property_size') ? old('property_size') : @$projects->property_size}}"
                                        name="property_size">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="typology">Available BHK Types</label>
                                    @php
										$bhkTypes = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK', '6 BHK', 'Plots', 'Shops', 'Studio Apartments', 'Office Space'];
										$selectedTypologies = is_array(@$projects->typology) ? @$projects->typology : json_decode(@$projects->typology, true) ?? [];
									@endphp

										<select name="typology[]" class="form-control input select2" multiple>
											@foreach($bhkTypes as $type)
												<option value="{{ $type }}" @if(in_array($type, $selectedTypologies)) selected @endif>
													{{ $type }} 
												</option>
											@endforeach
										</select>

                                    @error('typology')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                 <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="name">Project Status</label>
                                    <select placeholder="Select Project Status" name="project_status"
                                        class="form-control input select2 ">
                                        <option value="" disabled {{ @old('project_status', @$projects->project_status) == '' ?
                                            'selected' : '' }}> Select Status </option>
                                        
                                        <option value="ready_to_move" {{ @old('project_status', @$projects->project_status) ==
                                            'ready_to_move' ? 'selected' : '' }}>Ready To Move</option>
                                        <option value="under_construction" {{ @old('project_status', @$projects->project_status) ==
                                            'under_construction' ? 'selected' : '' }}>Under Construction</option>
                                        <option value="completed" {{ @old('project_status', @$projects->project_status) == 'completed' ?
                                            'selected' : '' }}>Completed</option>
                                        <option value="new_launch" {{ @old('project_status', @$projects->project_status) == 'new_launch'
                                            ?
                                            'selected' : '' }}>New Launch</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="location_id">City</label>
                                    <select name="location_id" id="location_id" class="form-control input select2">
                                        <option value="">Select City</option>
                                        @foreach (($parentLocations ?? []) as $parentLocation)
                                            <option value="{{ $parentLocation->id }}"
                                                data-name="{{ $parentLocation->city }}"
                                                {{ (string) old('location_id', $projects->location_id) === (string) $parentLocation->id ? 'selected' : '' }}>
                                                {{ $parentLocation->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="cities" id="cities" value="{{ old('cities', $projects->cities) }}">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="sublocation_id">Project Location (Sublocation)</label>
                                    <select name="sublocation_id" id="sublocation_id" class="form-control input select2">
                                        <option value="">Select Sublocation</option>
                                        @foreach (($sublocations ?? []) as $sublocation)
                                            <option value="{{ $sublocation->id }}"
                                                data-name="{{ $sublocation->city }}"
                                                {{ (string) old('sublocation_id', $projects->sublocation_id) === (string) $sublocation->id ? 'selected' : '' }}>
                                                {{ $sublocation->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="location" id="location" value="{{ old('location', $projects->location) }}">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="price">Minimum Price</label>
                                    <input class="form-control input @error('price') is-invalid @enderror " type="number"
                                        placeholder="Enter Minimum Price"
                                        value="{{old('price') ? old('price') : @$projects->price}}" name="price" pattern="[0-9]*">
                                </div>
								<div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="max_price">Maximum Price</label>
                                    <input class="form-control input @error('max_price') is-invalid @enderror " type="number"
                                        placeholder="Enter Maximum Price"
                                        value="{{old('max_price') ? old('max_price') : @$projects->max_price}}" name="max_price" pattern="[0-9]*">
                                </div> 
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="price">YouTube Link</label>
                                    <input class="form-control input @error('youtube_links') is-invalid @enderror "
                                        type="text" placeholder="Enter YouTube Link"
                                        value="{{old('youtube_links') ? old('youtube_links') : @$projects->youtube_links}}"
                                        name="youtube_links">
                                </div>
                                

                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">About Description:</label>
                                        <textarea name="about_description" class="summernote" id="summernote">
                                        {{old('about_description') ? old('about_description') : @$projects->about_description}}
                                        </textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Key Insights Description:</label>
                                        <textarea name="key_insights" class="summernote" id="summernote">
                                        {{old('key_insights') ? old('key_insights') : @$projects->key_insights}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Location Description:</label>
                                        <textarea name="location_description" class="summernote" id="summernote">
                                        {{old('location_description') ? old('location_description') : @$projects->location_description}}
                                        </textarea>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Floor Plan Description:</label>
                                        <textarea name="floor_plans_description" class="summernote" id="summernote">
                                        {{old('floor_plans_description') ? old('floor_plans_description') : @$projects->floor_plans_description}}
                                        </textarea>
                                    </div>
                                </div> -->
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Developer Background Description:</label>
                                        <textarea name="developer_background_dscp" class="summernote" id="summernote">
                                        {{@old('developer_background_dscp') ? @old('developer_background_dscp') : @$projects->developer_background_dscp}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Pricing Description:</label>
                                        <textarea name="site_plans_description" class="summernote" id="summernote">
                                        {{old('site_plans_description') ? old('site_plans_description') : @$projects->site_plans_description}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Possession Description:</label>
                                        <textarea name="possession_description" class="summernote" id="summernote">
                                        {{old('possession_description') ? old('possession_description') : @$projects->possession_description}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">

                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Choose Aminities
                                            </div>
                                            <div class="text-success alert-success p-2" data-toggle="modal"
                                                data-target="#aminityModal" style="cursor:pointer">
                                                <i class="fa fa-plus-square"></i>
                                                Add New
                                            </div>
                                        </div>
                                    @php
                                        $selectedAmenities = json_decode($projects->amenities_description ?? '[]', true);
                                        $selectedAmenities = is_array($selectedAmenities) ? $selectedAmenities : [];
                                    @endphp
									
                                    <select id="amenities" name="amenities[]" class="form-control select2" multiple>
                                        @if(!empty($aminityLists) && count($aminityLists) > 0)
                                        @foreach($aminityLists as $amenity)
                                        <option value="{{ $amenity->id }}"
                                            data-image="{{ url('storage/' . $amenity->image) }}" {{ in_array($amenity->id, $selectedAmenities) ? 'selected' : '' }}>
                                            {{ $amenity->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Choose Developer
                                            </div>
                                            <div class="text-success alert-success p-2" data-toggle="modal"
                                                data-target="#developerModal" style="cursor:pointer">
                                                <i class="fa fa-plus-square"></i>
                                                Add New
                                            </div>
                                        </div>
										
										
										
										{{-- 
                                        <div class="p-2 d-flex justify-content-between">
                                            <span class="col-2 p-0 font-weight-bold">
                                                Developer
                                            </span>
                                            <span class="col-2 p-0 font-weight-bold">Experience</span>
                                            <span class="col-2 p-0 font-weight-bold">Ongoing </span>
                                            <span class="col-2 p-0 font-weight-bold">Completed </span>
                                            <span class="col-2 p-0 font-weight-bold">logo</span>
                                            <span class="col-2 p-0 font-weight-bold">Status</span>
                                        </div>
                                        @if(!empty($developerDetails) && count($developerDetails) > 0)
                                        @foreach($developerDetails as $developerDetail)
                                        <div class="p-2 d-flex justify-content-between">
                                            <span class="col-2 p-0">
                                                <input {{ in_array($developerDetail->id, json_decode($projects->floor_plans_description ?? '[]')) ? 'checked' : '' }}
                                                    type="radio" name="details[{{$developerDetail->id}}]" id="">
                                                {{$developerDetail->developer_name}}
                                            </span>
                                            <span class="col-2 p-0">{{$developerDetail->developer_experience}}</span>
                                            <span class="col-2 p-0">{{$developerDetail->ongoing_project}}</span>
                                            <span class="col-2 p-0">{{$developerDetail->completed_projects}}</span>
                                            <span class="col-2 p-0">
                                                <img src="{{url('storage/' . $developerDetail->developer_logo)}}"
                                                    style="width:40px" />
                                            </span>
                                        </div>
                                        @endforeach
                                        @endif ---}}
										
										@php
											$selectedDetails = json_decode($projects->floor_plans_description, true) ?? [];
										@endphp

										<select name="details[]" class="form-control select2" multiple>
											<option value="">Choose Developer</option>
											@if(!empty($developerDetails))
												@foreach($developerDetails as $developer)
													<option value="{{ $developer->id }}"
														{{ in_array($developer->id, $selectedDetails) ? 'selected' : '' }}>
														{{ $developer->developer_name }}
													</option>
												@endforeach
											@endif
										</select>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Hero Images:</label>
                                        <input type="file"
                                            class="form-control @error('hero_images') is-invalid @enderror"
                                            name="hero_images" onchange="previewImage(this, '#previewHeroImages')"
                                            placeholder="Project Hero Images" id="hero_images">
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->hero_images)}}" alt="photo"
                                            id="previewHeroImages">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Amenities Images:</label>
                                        <input type="file"
                                            class="form-control @error('amenities_images') is-invalid @enderror"
                                            name="amenities_images"
                                            onchange="previewImage(this, '#previewAmenitiesImages')"
                                            placeholder="Project Amenities Images" id="amenities_images" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->amenities_images)}}" alt="photo"
                                            id="previewAmenitiesImages">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Site Plan Images:</label>
                                        <input type="file"
                                            class="form-control @error('site_plans_images') is-invalid @enderror"
                                            name="site_plans_images"
                                            onchange="previewImage(this, '#previewSitePlanImages')"
                                            placeholder="Project Site Plan Images" id="site_plans_images" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->site_plans_images)}}" alt="photo"
                                            id="previewSitePlanImages">
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Location Video:</label>
                                        <input type="file"
                                            class="form-control @error('location_video') is-invalid @enderror"
                                            name="location_video" onchange="previewImage(this, '#previewLocationVideo')"
                                            placeholder="Project Location Video" id="location_video" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->location_video)}}" alt="photo"
                                            id="previewLocationVideo">
                                    </div>
                                </div>
                                
								
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Developer Background Image:</label>
                                        <input type="file"
                                            class="form-control @error('developer_background_image') is-invalid @enderror"
                                            name="developer_background_image"
                                            onchange="previewImage(this, '#previewDevImage')"
                                            placeholder="Project Developer Background Image"
                                            id="developer_background_image" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->developer_background_image)}}"
                                            alt="photo" id="previewDevImage">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Logo Image:</label>
                                        <input type="file"
                                            class="form-control @error('logo_image') is-invalid @enderror"
                                            name="logo_image" onchange="previewImage(this, '#previewLogoImage')"
                                            placeholder="Project Logo Image" id="logo_image">
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->logo_image)}}" alt="photo"
                                            id="previewLogoImage">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Feature Image:</label>
                                        <input type="file"
                                            class="form-control @error('feature_image') is-invalid @enderror"
                                            name="feature_image" onchange="previewImage(this, '#previewFeatureImage')"
                                            placeholder="Project feature Image" id="feature_image">
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->feature_image)}}" alt="photo"
                                            id="previewFeatureImage">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Brochure:</label>
                                        <input type="file"
                                            class="form-control @error('floor_plans_images') is-invalid @enderror"
                                            name="floor_plans_images"
                                            onchange="previewImage(this, '#previewFloorPlanImages')"
                                            placeholder="Project Brochure" id="floor_plans_images" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->floor_plans_images)}}" alt="photo"
                                            id="previewFloorPlanImages">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Price List:</label>
                                        <input type="file"
                                            class="form-control @error('price_list') is-invalid @enderror"
                                            name="price_list" onchange="previewImage(this, '#previewPriceList')"
                                            placeholder="Project Price List" id="price_list" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->price_list)}}" alt="photo"
                                            id="previewPriceList">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Sanctioned Map:</label>
                                        <input type="file"
                                            class="form-control @error('sanctioned_map') is-invalid @enderror"
                                            name="sanctioned_map" onchange="previewImage(this, '#previewSanctionedMap')"
                                            placeholder="Project Price List" id="sanctioned_map" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->sanctioned_map)}}" alt="photo"
                                            id="previewSanctionedMap">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Lease Deed:</label>
                                        <input type="file"
                                            class="form-control @error('lease_deed') is-invalid @enderror"
                                            name="lease_deed" onchange="previewImage(this, '#previewLeaseDeed')"
                                            placeholder="Project Price List" id="lease_deed" multiple>
                                    </div>
                                    <div class="fileInput">
                                        <img src="{{url('storage/' . @$projects->lease_deed)}}" alt="photo"
                                            id="previewLeaseDeed">
                                    </div>
                                </div>
								
                             <div class="col-12 mt-5">

    <div class="row">

        {{-- Property Type --}}
        <div class="col-md-4">

            <h5>Property Type</h5>

            <select name="project_type"
                id="project_type"
                class="form-control mt-3">

                <option value="residential"
                    {{ old('project_type', $projects->project_type ?? 'residential') === 'residential' ? 'selected' : '' }}>
                    Residential
                </option>

                <option value="commercial"
                    {{ old('project_type', $projects->project_type ?? '') === 'commercial' ? 'selected' : '' }}>
                    Commercial
                </option>

                <option value="mixed"
                    {{ old('project_type', $projects->project_type ?? '') === 'mixed' ? 'selected' : '' }}>
                    Mixed
                </option>

            </select>

        </div>

    </div>
    <div class="row  align-items-end mt-3">

    {{-- Residential / BHK Type - LEFT --}}
    <div id="residentialFloorSectionWrapper"
        class="col-md-10">
    </div>

    {{-- Add Floor Plan - RIGHT --}}
    <div id="floorPlanButtonWrapper"
        class="col-md-2 text-right">
    </div>

</div>


    {{-- Floor Plans --}}
    <div id="floorPlansWrapper"
        class="col-12 ">

    </div>

</div>
                                <div class="col-12 mb-3">
                                    <h5>
                                        SEO Section
                                    </h5>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Title</label>
                                    <input class="form-control input @error('seo_data[title]') is-invalid @enderror "
                                        type="text" placeholder="Enter Title"
                                        value="{{@old('seo_data[title]') ? @old('seo_data[title]') : @$projects->seo_data['title']}}"
                                        name="seo_data[title]">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="primary_keyword">Primary Keyword</label>
                                    <input
                                        class="form-control input @error('seo_data[primary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Primary Keyword"
                                        value="{{@old('seo_data[primary_keyword]') ? @old('seo_data[primary_keyword]') : @$projects->seo_data['primary_keyword']}}"
                                        name="seo_data[primary_keyword]">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="secondary_keyword">Secondary Keyword</label>
                                    <input
                                        class="form-control input @error('seo_data[secondary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Secondary Keyword"
                                        value="{{@old('seo_data[secondary_keyword]') ? @old('seo_data[secondary_keyword]') : @$projects->seo_data['secondary_keyword']}}"
                                        name="seo_data[secondary_keyword]">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="meta_description">Meta Description</label>
                                    <input
                                        class="form-control input @error('seo_data[meta_description]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Meta Description"
                                        value="{{@old('seo_data[meta_description]') ? @old('seo_data[meta_description]') : @$projects->seo_data['meta_description']}}"
                                        name="seo_data[meta_description]">
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="canonical_link">Canonical Link</label>
                                    <input
                                        class="form-control input @error('seo_data[canonical_link]') is-invalid @enderror "
                                        type="text" placeholder="Enter Canonical Link"
                                        value="{{@old('seo_data[canonical_link]') ? @old('seo_data[canonical_link]') : @$projects->seo_data['canonical_link']}}"
                                        name="seo_data[canonical_link]">
                                </div>
                                <div class="col-12 mt-5">
                                    <h5>
                                        FAQs Section
                                    </h5>
                                </div>
                                <div class="row p-3 faqs" style="width:100%">
                                    @if(
                                    !empty(json_decode($projects->faqs_data)) &&
                                    count(json_decode($projects->faqs_data)) > 0
                                    )
                                    @foreach(json_decode($projects->faqs_data) as $index => $faqData)

                                    <div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-6">
                                                <label for="question">Question:</label>
                                                <input
                                                    value="{{(@old('question')) ? (@old('question')) : (@$faqData->question)}}"
                                                    name="faqs_data[{{@$index}}][question]" type="text"
                                                    class="form-control">
                                            </div>
                                            <div class="col-6">
                                                <label for="answer">Answer:</label>
                                                <input
                                                    value="{{(@old('answer')) ? (@old('answer')) : (@$faqData->answer)}}"
                                                    name="faqs_data[{{@$index}}][answer]" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        @if($index == 0)
                                        <span class="bg-warning plusBtnFAQ">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        @else
                                        <span class="bg-danger minusBtnFAQ">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        @endif
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-6">
                                                <label for="question">Question:</label>
                                                <input name="faqs_data[0][question]" type="text" class="form-control">
                                            </div>
                                            <div class="col-6">
                                                <label for="answer">Answer:</label>
                                                <input name="faqs_data[0][answer]" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <span class="bg-warning plusBtnFAQ">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                    @endif

                                </div>
								<div class="col-12">
                                    <h5>
                                        Pricing Section
                                    </h5>
                                </div>
								<div class="row p-3 sqftPrice" style="width:100%">
									@php
										
										$sqftPrices = old('sqft_price') ?? json_decode($projects->sqft_price ?? '[]', true);

										
										if (empty($sqftPrices)) {
											$sqftPrices = [['applied_from' => '', 'value' => '']];
										}
									@endphp

									@foreach($sqftPrices as $key => $price)
										<div class="col-sm-6 col-lg-6 mt-2 sqftPriceData" style="padding:0 30px">
											<div class="row p-2" style="background:#1b577733">
												<div class="col-6">
													<label for="applied_from">Applied From:</label>
													<input 
														value="{{ $price['applied_from'] ?? '' }}"
														name="sqft_price[{{$key}}][applied_from]" 
														type="date" 
														class="form-control" 
														>
												</div>
												<div class="col-6">
													<label for="value">Price per Sqft:</label>
													<input 
														value="{{ $price['value'] ?? '' }}"
														name="sqft_price[{{$key}}][value]" 
														type="text" 
														class="form-control" 
														placeholder="Enter price per sqft"
														>
												</div>
											</div>

											{{-- Show plus button only for the first item --}}
											@if ($loop->first)
												<span class="bg-warning px-2 py-1 sqftPricePlusBtn">
													<i class="fa fa-plus"></i>
												</span>
											@else
												<span class="bg-danger px-2 py-1 sqftPriceMinusBtn">
													<i class="fa fa-minus"></i>
												</span>
											@endif
										</div>
									@endforeach
								</div>
                                <div class="col-12 mt-5">
                                    <h5>
                                        Rera Data Section
                                    </h5>
                                </div>
                                <div class="row p-3 rera" style="width:100%">
                                    @if(
                                    !empty(json_decode($projects->rera_data)) &&
                                    count(json_decode($projects->rera_data)) > 0
                                    )
                                    @foreach(json_decode($projects->rera_data) as $index => $reraData)

                                    <div class="col-sm-12 col-lg-12 mt-2 reraData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-4">
                                                <label for="phase">Phase:</label>
                                                <input
                                                    value="{{(@old('phase')) ? (@old('phase')) : (@$reraData->phase)}}"
                                                    name="rera_data[{{@$index}}][phase]" type="text"
                                                    class="form-control">
                                            </div>
                                            <div class="col-4">
                                                <label for="rera_no">Rera Number:</label>
                                                <input
                                                    value="{{(@old('rera_no')) ? (@old('rera_no')) : (@$reraData->rera_no)}}"
                                                    name="rera_data[{{@$index}}][rera_no]" type="text"
                                                    class="form-control">
                                            </div>
											<div class="col-4">
                                                <label for="qr_image">QR Image:</label>
                                              
												<input name="rera_data[{{@$index}}][qr_image]" type="file" class="form-control">	
                                            </div>
                                        </div>
                                        @if($index == 0)
                                        <span class="bg-warning plusBtnRera">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        @else
                                        <span class="bg-danger minusBtnRera">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        @endif
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-sm-12 col-lg-12 mt-2 reraData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-4">
                                                <label for="phase">Phase:</label>
                                                <input name="rera_data[0][phase]" type="text" class="form-control"
                                                    >
                                            </div>
                                            <div class="col-4">
                                                <label for="rera_no">Rera Number:</label>
                                                <input name="rera_data[0][rera_no]" type="text" class="form-control"
                                                    >
                                            </div>
											<div class="col-4">
                                                <label for="qr_image">QR Image:</label>
                                                <input name="rera_data[0][qr_image]" type="file"
                                                    class="form-control" >
                                            </div>
                                        </div>
                                        <span class="bg-warning plusBtnRera">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                    @endif

                                </div>
                                <div class="col-12 mt-3">
                                    <button id="storeBtn"  type="submit" class="btn btn-primary">Upload Project</button>   
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="aminityModal" tabindex="-1" aria-labelledby="aminityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="aminityModalLabel">Add Aminities</h1>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    <div class="d-flex justify-content-between">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group text-right">
                            <input type="file" name="image" id="">
                        </div>
                    </div>
                    <button type="submit" id="aminityBtn" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Developer Modal -->
<div class="modal fade" id="developerModal" tabindex="-1" aria-labelledby="developerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="developerModalLabel">Add Developer Details</h1>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    <div class="d-flex justify-content-between">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="developer_name">Developer Name:</label>
                                    <input type="text" name="developer_name" class="form-control">
                                </div>
                                <div class="col-sm-6">
                                    <label for="developer_name">Developer Experience:</label>
                                    <input type="text" name="developer_experience" class="form-control">
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <label for="developer_name">Ongoing Project:</label>
                                    <input type="text" name="ongoing_project" class="form-control">
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <label for="developer_name">Completed Projects:</label>
                                    <input type="text" name="completed_projects" class="form-control">
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <label for="developer_name">Developer Logo:</label>
                                    <input type="file" name="developer_logo" id="" class="form-control">
                                </div>
                                <div class="col-sm-6">

                                </div>

                            </div>
                        </div>

                    </div>
                    <button type="submit" id="developerBtn" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    function syncLocationHiddenFields() {
        const cityOption = $('#location_id option:selected');
        const subOption = $('#sublocation_id option:selected');
        $('#cities').val(cityOption.data('name') || '');
        $('#location').val(subOption.data('name') || cityOption.data('name') || '');
    }

    function loadSublocations(parentId, selectedId) {
        const $sub = $('#sublocation_id');
        $sub.find('option:not(:first)').remove();
        if (!parentId) {
            syncLocationHiddenFields();
            return;
        }
        $.get("{{ url('7439/locations/children') }}/" + parentId, function (items) {
            items.forEach(function (item) {
                const selected = String(selectedId) === String(item.id) ? 'selected' : '';
                $sub.append('<option value="' + item.id + '" data-name="' + item.city + '" ' + selected + '>' + item.city + '</option>');
            });
            syncLocationHiddenFields();
        });
    }

    $(document).on('change select2:select', '#location_id', function () {
        loadSublocations($(this).val(), null);
        syncLocationHiddenFields();
    });
    $(document).on('change select2:select', '#sublocation_id', syncLocationHiddenFields);
    $('form').on('submit', syncLocationHiddenFields);

 let selectedAreaType = @json(old('area_type', $projects->area_type ?? 'apartment'));
function generateFloorPlanFields(type, index) {

    let html = '';

    if (type === 'apartment') {

        html = `
        <div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans"
             style="padding:0 30px">

            <div class="row pt-5 pb-5"
                 style="background:#1b577733">

                <div class="col">
                    <label>Title:</label>

                    <input
                        name="floor_plans[${index}][title]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Super Area:</label>

                    <input
                        name="floor_plans[${index}][super_area]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Carpet Area:</label>

                    <input
                        name="floor_plans[${index}][carpet_area]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Builtup Area:</label>

                    <input
                        name="floor_plans[${index}][built_area]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Balcony Area:</label>

                    <input
                        name="floor_plans[${index}][balcony_area]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Image:</label>

                    <input
                        name="floor_plans[${index}][feature_image]"
                        type="file"
                        class="form-control">
                </div>

            </div>

            <span class="bg-danger minusBtn">
                <i class="fa fa-minus"></i>
            </span>

        </div>`;
    }

    else if (type === 'plots') {

        html = `
        <div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans"
             style="padding:0 30px">

            <div class="row pt-5 pb-5"
                 style="background:#1b577733">

                <div class="col">
                    <label>Title:</label>

                    <input
                        name="floor_plans[${index}][title]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Length:</label>

                    <input
                        name="floor_plans[${index}][length]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Width:</label>

                    <input
                        name="floor_plans[${index}][width]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Total Area:</label>

                    <input
                        name="floor_plans[${index}][total_area]"
                        type="text"
                        class="form-control"
                        required>
                </div>

                <div class="col-2">
                    <label>Image:</label>

                    <input
                        name="floor_plans[${index}][feature_image]"
                        type="file"
                        class="form-control">
                </div>

            </div>

            <span class="bg-danger minusBtn">
                <i class="fa fa-minus"></i>
            </span>

        </div>`;
    }

    return html;
}


function generateCommercialFloorFields(index, data = {}) {

    return `
    <div class="col-sm-12 col-lg-12 mt-2 commercialFloorPlan"
         style="padding:0 30px">

        <div class="row pt-4 pb-4"
             style="background:#1b577733">

            <div class="col-md-3">
                <label>Floor:</label>

                <input
                    name="floor_plans[${index}][floor]"
                    type="text"
                    class="form-control"
                    value="${data.floor ?? ''}"
                    placeholder="e.g. Lower Ground"
                    required>
            </div>


            <div class="col-md-3">
                <label>Floor Sub Label:</label>

                <input
                    name="floor_plans[${index}][floor_sub_label]"
                    type="text"
                    class="form-control"
                    value="${data.floor_sub_label ?? ''}"
                    placeholder="e.g. Basement 1">
            </div>


            <div class="col-md-3">
                <label>Format & Position Title:</label>

                <input
                    name="floor_plans[${index}][format_position_title]"
                    type="text"
                    class="form-control"
                    value="${data.format_position_title ?? ''}"
                    placeholder="e.g. Retail & anchor spaces"
                    required>
            </div>


            <div class="col-md-3">
                <label>Rate / Sq Ft:</label>

                <input
                    name="floor_plans[${index}][rate_sq_ft]"
                    type="text"
                    class="form-control"
                    value="${data.rate_sq_ft ?? ''}"
                    placeholder="e.g. 18000"
                    required>
            </div>


            <div class="col-md-8 mt-3">
                <label>Format & Position Description:</label>

                <textarea
                    name="floor_plans[${index}][format_position_description]"
                    class="form-control"
                    placeholder="Position / layout notes">${data.format_position_description ?? ''}</textarea>
            </div>


            <div class="col-md-4 mt-3">
                <label>Image (optional):</label>

                <input
                    name="floor_plans[${index}][feature_image]"
                    type="file"
                    class="form-control">
            </div>

        </div>


        <span class="bg-danger commercialMinusBtn"
              style="cursor:pointer; padding:5px 10px;">

            <i class="fa fa-minus"></i>

        </span>

    </div>
    `;
}

function loadExistingFloorPlans() {

    const projectType =
        $('#project_type').val();

    const floorPlans =
        @json(json_decode($projects->floor_plans_data ?? '[]'));

    $('#residentialFloorSectionWrapper').html('');
    $('#floorPlanButtonWrapper').html('');
    $('#floorPlansWrapper').html('');



    // =========================
    // COMMERCIAL
    // =========================

    if (projectType === 'commercial') {

        $('#residentialFloorSectionWrapper').html(`
            <div id="commercialFloorSection"
                 class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Commercial Floor Plans
                </h5>

            </div>
        `);


        $('#floorPlanButtonWrapper').html(`
            <h5>&nbsp;</h5>

            <button type="button"
                class="btn btn-success commercialPlusBtn">

                <i class="fa fa-plus"></i>
                Add Floor Plan

            </button>
        `);


        $('#floorPlansWrapper').html(`
            <div id="commercialFloors"
                 class="row mb-3">
            </div>
        `);


        if (floorPlans.length > 0) {

            floorPlans.forEach(function(plan, index) {

                $('#commercialFloors').append(
                    generateCommercialFloorFields(
                        index,
                        plan
                    )
                );

            });

        } else {

            $('#commercialFloors').append(
                generateCommercialFloorFields(0)
            );
        }


        return;
    }


    // =========================
    // RESIDENTIAL / MIXED
    // =========================

    $('#residentialFloorSectionWrapper').html(`
        <div id="residentialFloorSection">

            <h5>BHK Plans</h5>

            <select
                name="area_type"
                id="area_type"
                class="form-control mt-3">

                <option value="apartment">
                    Apartments
                </option>

                <option value="plots">
                    Plots
                </option>

            </select>

        </div>
    `);


    $('#floorPlanButtonWrapper').html(`
        <h5>&nbsp;</h5>

        <button type="button"
            class="btn btn-success plusBtn">

            <i class="fa fa-plus"></i>
            Add Floor Plan

        </button>
    `);


    $('#floorPlansWrapper').html(`
        <div id="residentialFloors"
             class="floors row">
        </div>
    `);


    // Existing area type determine karo
    selectedAreaType = @json(old('area_type', $projects->area_type ?? 'apartment'));

$('#area_type').val(selectedAreaType);

    if (floorPlans.length > 0) {

        floorPlans.forEach(function(plan, index) {

            $('#residentialFloors').append(
                generateFloorPlanFields(
                    selectedAreaType,
                    index
                )
            );

            const $item =
                $('#residentialFloors .pdfloorPlans').last();

            Object.keys(plan).forEach(function(key) {

                if (key === 'feature_image') {
                    return;
                }

                $item
                    .find(`[name="floor_plans[${index}][${key}]"]`)
                    .val(plan[key] ?? '');
            });

        });

    } else {

        $('#residentialFloors').append(
            generateFloorPlanFields(
                selectedAreaType,
                0
            )
        );
    }
}

$(document).on('change', '#area_type', function () {

    selectedAreaType = $(this).val();

    $('#residentialFloors').html(
        generateFloorPlanFields(
            selectedAreaType,
            0
        )
    );

});$(document).on('change', '#project_type', function () {

    loadExistingFloorPlans();

});
$(document).on('click', '.plusBtn', function () {

    const currentIndex =
        $('#residentialFloors .pdfloorPlans').length;

    $('#residentialFloors').append(
        generateFloorPlanFields(
            selectedAreaType,
            currentIndex
        )
    );

});
$(document).on('click', '.commercialPlusBtn', function () {

    const currentIndex =
        $('#commercialFloors .commercialFloorPlan').length;

    $('#commercialFloors').append(
        generateCommercialFloorFields(currentIndex)
    );

});$(document).on('click', '.minusBtn', function () {

    $(this)
        .closest('.pdfloorPlans')
        .remove();

});$(document).on('click', '.commercialMinusBtn', function () {

    $(this)
        .closest('.commercialFloorPlan')
        .remove();

});$(document).ready(function () {

    loadExistingFloorPlans();

});

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    //for delete the images
    $(document).on('click', '.deleteButton', function() {
        let id = $(this).attr('dataId');

        let url = "{{ url('/7439/frontend-pages/projects/edit') }}";

        var actionUrl = `${url}${id}`;
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: actionUrl,
                    type: "GET",
                    success: function(data) {
                        if (data.status) {
                            Swal.fire({
                                title: postTitle,
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);

                            });
                        } else {
                            Swal.fire(
                                postTitle,
                                data.msg,
                                'error'
                            )
                        }
                    }
                }); //ajax ends here
            } else {
                Swal.fire(
                    'Cancelled!',
                    'Your data is safe.',
                    'error'
                );
            }
        })
    })
</script>
<script>
    //function to re-index the FAQs index
    function reIndexFaqsData() {
        $('.faqs .faqData').each(function(index) {
            // Update index in input names
            $(this).find('[name^="faqs_data"]').each(function() {
                let newName = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }
    // plus button click event for the Faq data
    $('.plusBtnFAQ').click(function() {
        let currentIndex = $('.faqs .faqData').length; // Get the current number of floor plans

        let html = `<div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                        <div class="row pt-5 pb-5" style="background:#1b577733">

                            <div class="col-6">
                                <label for="question">Question:</label>
                                <input name="faqs_data[${currentIndex}][question]" type="text" class="form-control" >
                            </div>
                            <div class="col-6">
                                <label for="answer">Answer:</label>
                                <input name="faqs_data[${currentIndex}][answer]" type="text" class="form-control" >
                            </div>
                        </div>
                        <span class="bg-danger minusBtnFAQ">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>`;
        $('.faqs').append(html);
        reIndexFaqsData();
    });
    $(document).on('click', '.minusBtnFAQ', function() {
        $(this).closest('.faqData').remove();
        reIndexFaqsData();
    });

    $('#openModalBtn').click(function() {
        $('#myModal').modal('show');
    });
    $('#aminityBtn').on('click', function(e) {
        e.preventDefault(); // Prevent the default button behavior

        // Get the form element
        var form = $(this).closest('.modal-body').find('form')[0]; // Select the form inside the modal body

        // Create a FormData object from the form
        var formData = new FormData(form);
        $.ajax({
            url: '{{route("aminity-list.store")}}', // Replace with your route
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                form.reset();
                $('#response').html('<p>' + response.message + '</p>'); // Handle success response
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                for (var key in errors) {
                    errorMessage += errors[key].join('<br>') + '<br>';
                }
                $('#response').html('<p style="color:red;">' + errorMessage + '</p>'); // Handle errors
            }
        });
    });
    $('#developerBtn').on('click', function(e) {
        e.preventDefault(); // Prevent the default button behavior

        // Get the form element
        var form = $(this).closest('.modal-body').find('form')[0]; // Select the form inside the modal body

        // Create a FormData object from the form
        var formData = new FormData(form);
        $.ajax({
            url: '{{route("developer-details.store")}}', // Replace with your route
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                form.reset();
                $('#response').html('<p>' + response.message + '</p>'); // Handle success response
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                for (var key in errors) {
                    errorMessage += errors[key].join('<br>') + '<br>';
                }
                $('#response').html('<p style="color:red;">' + errorMessage + '</p>'); // Handle errors
            }
        });
    });
</script>

<script>
$(document).ready(function () {

    // Function to reindex sqft_price fields
    function reIndexSqftPriceData() {
        $('.sqftPrice .sqftPriceData').each(function (index) {
            $(this).find('[name^="sqft_price"]').each(function () {
                let field = $(this).attr('name').split('[').pop().split(']')[0];
                $(this).attr('name', `sqft_price[${index}][${field}]`);
            });
        });
    }

    // Plus button click (Add new row)
    $(document).on('click', '.sqftPricePlusBtn', function () {
        let currentIndex = $('.sqftPrice .sqftPriceData').length;

        let html = `
            <div class="col-sm-6 col-lg-6 mt-2 sqftPriceData" style="padding:0 30px">
                <div class="row p-2" style="background:#1b577733">
                    <div class="col-6">
                        <label for="applied_from">Applied From:</label>
                        <input name="sqft_price[${currentIndex}][applied_from]" type="date" class="form-control" >
                    </div>
                    <div class="col-6">
                        <label for="value">Price per Sqft:</label>
                        <input name="sqft_price[${currentIndex}][value]" type="text" class="form-control" placeholder="Enter price per sqft" >
                    </div>
                </div>
                <span class="bg-danger px-2 py-1 sqftPriceMinusBtn">
                    <i class="fa fa-minus"></i>
                </span>
            </div>
        `;

        $('.sqftPrice').append(html);
        reIndexSqftPriceData();
    });

    // Minus button click (Remove row)
    $(document).on('click', '.sqftPriceMinusBtn', function () {
        $(this).closest('.sqftPriceData').remove();
        reIndexSqftPriceData();
    });

    // Ensure correct index order on page load (important for edit page)
    reIndexSqftPriceData();
});
</script>
<script>
    //function to re-index the rera index
    function reIndexReraData() {
        $('.rera .reraData').each(function (index) {
            // Update index in input names
            $(this).find('[name^="rera_data"]').each(function () {
                let newName = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }
    // plus button click event for the Faq data
    $('.plusBtnRera').click(function () {
        let currentIndex = $('.rera .reraData').length; // Get the current number of floor plans

        let html = `<div class="col-sm-12 col-lg-12 mt-2 reraData" style="padding:0 30px">
                        <div class="row pt-5 pb-5" style="background:#1b577733">

                            <div class="col-4">
                                <label for="phase">Phase:</label>
                                <input name="rera_data[${currentIndex}][phase]" type="text" class="form-control">
                            </div>
                            <div class="col-4">
                                <label for="rera_no">Rera Number:</label>
                                <input name="rera_data[${currentIndex}][rera_no]" type="text" class="form-control">
                            </div>
							<div class="col-4">
								<label for="qr_image">QR Image:</label>
								<input name="rera_data[${currentIndex}][qr_image]" type="file" class="form-control">
							</div>
                        </div>
                        <span class="bg-danger minusBtnRera">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>`;
        $('.rera').append(html);
        reIndexReraData();
    });
    $(document).on('click', '.minusBtnRera', function () {
        $(this).closest('.reraData').remove();
        reIndexReraData();
    });
</script>
@endsection
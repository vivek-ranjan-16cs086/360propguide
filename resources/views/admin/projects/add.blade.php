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
                        <form method="POST" id="myForm" action="{{route('projects.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
							   <div class="col-12 mb-4">
                                    <label for="docImportInput">Import Project Content (.doc/.docx)</label>
                                    <input type="file" class="form-control" id="docImportInput" accept=".doc,.docx">
                                    <small class="text-muted d-block mt-1">This only fills matching fields. It does not submit the form.</small>
                                    <div id="docImportStatus" class="mt-2 text-muted"></div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Project Name</label>
                                    <input class="form-control input @error('project_name') is-invalid @enderror "
                                        type="text" placeholder="Enter Project Name" value="{{@old('project_name')}}"
                                        name="project_name" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Rera Number</label>
                                    <input class="form-control input @error('rera_no') is-invalid @enderror "
                                        type="text" placeholder="Enter Rera Number" value="{{@old('rera_no')}}"
                                        name="rera_no" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Launch Date</label>
                                    <input class="form-control input @error('launch_date') is-invalid @enderror "
                                        type="text" placeholder="Enter Launch Date" value="{{@old('launch_date')}}"
                                        name="launch_date" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="developer_name">Developer Name</label>
                                    <input class="form-control input @error('developer_name') is-invalid @enderror "
                                        type="text" placeholder="Enter Developer Name"
                                        value="{{@old('developer_name')}}" name="developer_name" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="property_size">Property Size <span class="text-danger">(Ex. 2
                                            Acres*)</span></label>
                                    <input class="form-control input @error('property_size') is-invalid @enderror "
                                        type="text" placeholder="Enter Property Size" value="{{@old('property_size')}}"
                                        name="property_size" required>
                                </div>
                                
								<div class="col-sm-12 col-lg-6 mb-4">
                                   <label for="typology">Available BHK Types</label>
                                    <select placeholder="Select Available BHK Types" name="typology[]"
                                            class="form-control input select2 required" multiple>
											<option value="" disabled> Available BHK Types </option>
                                            <option value="1 BHK">1 BHK</option>
                                            <option value="2 BHK">2 BHK</option>
                                            <option value="3 BHK">3 BHK</option>
                                            <option value="4 BHK">4 BHK</option>
                                            <option value="5 BHK">5 BHK</option>
                                            <option value="6 BHK">6 BHK</option>
											<option value="Plots">Plots</option>
											<option value="shops">Shops</option>
											<option value="shops">Office Space</option>
                                            <option value="studio">Studio Apartments</option> 											
                                        </select>
                                     @error('typology')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                </div>
                                
								<div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="name">Project Status</label>
                                    <select placeholder="Select Project Status" name="project_status"
                                        class="form-control input select2 required">
                                        <option value="" disabled> Select Status </option>
                                       
                                        <option value="ready_to_move"> Ready To Move </option>
                                        <option value="under_construction"> Under Construction </option>
                                        <option value="completed"> Completed </option>
                                        <option value="new_launch"> New Launch </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="location">Project Location</label>
                                    <input class="form-control input @error('location') is-invalid @enderror "
                                        type="text" placeholder="Enter Project Location" value="{{@old('location')}}"
                                        name="location" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="location">City</label>
                                    <input class="form-control input @error('cities') is-invalid @enderror " type="text"
                                        placeholder="Enter Project City" value="{{@old('cities')}}" name="cities"
                                        required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="price">Minimum Price</label>
                                    <input class="form-control input @error('price') is-invalid @enderror " type="number"
                                        placeholder="Enter Minimum Price" value="{{@old('price')}}" name="price"
                                        required pattern="[0-9]*">
                                </div>
								<div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="max_price">Maximum Price</label>
                                    <input class="form-control input @error('max_price') is-invalid @enderror " type="number"
                                        placeholder="Enter Maximum Price" value="{{@old('max_price')}}" name="max_price"
                                        required pattern="[0-9]*">
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="price">YouTube Link</label>
                                    <input class="form-control input @error('youtube_links') is-invalid @enderror "
                                        type="text" placeholder="Enter YouTube Link" value="{{@old('youtube_links')}}"
                                        name="youtube_links">
                                </div>
                                
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">About Description:</label>
                                        <textarea name="about_description" class="summernote" id="summernote">
                                        {{@old('about_description')}}
                                        </textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Key Insights Description:</label>
                                        <textarea name="key_insights" class="summernote" id="summernote">
                                        {{@old('key_insights')}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Location Description:</label>
                                        <textarea name="location_description" class="summernote" id="summernote">
                                        {{@old('location_description')}}
                                        </textarea>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Floor Plan Description:</label>
                                        <textarea name="floor_plans_description" class="summernote" id="summernote">
                                        {{@old('floor_plans_description')}}
                                        </textarea>
                                    </div>
                                </div> -->
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Developer Background Description:</label>
                                        <textarea name="developer_background_dscp" class="summernote" id="summernote">
                                        {{@old('developer_background_dscp')}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Pricing Description:</label>
                                        <textarea name="site_plans_description" class="summernote" id="summernote">
                                        {{@old('site_plans_description')}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <label for="description">Possession Description:</label>
                                        <textarea name="possession_description" class="summernote" id="summernote">
                                        {{@old('possession_description')}}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <!-- <label for="description">Amenities Description:</label>
                                        <textarea name="amenities_description" class="summernote" id="summernote">
                                        {{@old('amenities_description')}}
                                        </textarea> -->
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
                                           <!--@if(!empty($aminityLists) && count($aminityLists) > 0)
                                        @foreach($aminityLists as $aminity)
                                        <div class="p-2 d-flex justify-content-between">
                                            <span>
                                                <input type="checkbox" name="aminities[{{$aminity->id}}]" id="">
                                                {{$aminity->name}}
                                            </span>
                                            <span>
                                                <img src="{{url('storage/'.$aminity->image)}}" style="width:40px" />
                                            </span>
                                        </div>
                                        @endforeach
                                        @endif-->
										<select id="amenities" name="amenities[]" class="form-control select2" multiple>
                                        @if(!empty($aminityLists) && count($aminityLists) > 0)
                                        @foreach($aminityLists as $amenity)
                                        <option value="{{ $amenity->id }}"
                                            data-image="{{ url('storage/' . $amenity->image) }}" {{ in_array($amenity->
                                            id, old('amenities', [])) ? 'selected' : '' }}>
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
                                           <!--<div class="p-2 d-flex justify-content-between">
                                            <span class="col-2 p-0 font-weight-bold">
                                                Developer
                                            </span>
                                            <span class="col-2 p-0 font-weight-bold">Experience</span>
                                            <span class="col-2 p-0 font-weight-bold">Ongoing </span>
                                            <span class="col-2 p-0 font-weight-bold">Completed </span>
                                            <span class="col-2 p-0 font-weight-bold">logo</span>
                                        </div>
                                        @if(!empty($developerDetails) && count($developerDetails) > 0)
                                        @foreach($developerDetails as $developerDetail)
                                        <div class="p-2 d-flex justify-content-between">
                                            <span class="col-2 p-0">
                                                <input type="radio" name="details[{{$developerDetail->id}}]" id="">
                                                {{$developerDetail->developer_name}}
                                            </span>
                                            <span class="col-2 p-0">{{$developerDetail->developer_experience}}</span>
                                            <span class="col-2 p-0">{{$developerDetail->ongoing_project}}</span>
                                            <span class="col-2 p-0">{{$developerDetail->completed_projects}}</span>
                                            <span class="col-2 p-0">
                                                <img src="{{url('storage/'.$developerDetail->developer_logo)}}"
                                                    style="width:40px" />
                                            </span>
                                        </div>
                                        @endforeach
                                        @endif-->
									<select placeholder="Choose Developer" name="details[]"
                                        class="form-control input select2 required">
                                        @if(!empty($developerDetails) && count($developerDetails) > 0)
                                        @foreach($developerDetails as $key => $developer)
                                        <option value="{{$developer->id}}">{{$developer->developer_name}} </option>
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
                                            placeholder="Project Hero Images" id="hero_images" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewHeroImages">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Amenities Images:</label>
                                        <input type="file"
                                            class="form-control @error('amenities_images') is-invalid @enderror"
                                            name="amenities_images"
                                            onchange="previewImage(this, '#previewAmenitiesImages')"
                                            placeholder="Project Amenities Images" id="amenities_images" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewAmenitiesImages">
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
                                            id="developer_background_image" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewDevImage">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Logo Image:</label>
                                        <input type="file"
                                            class="form-control @error('logo_image') is-invalid @enderror"
                                            name="logo_image" onchange="previewImage(this, '#previewLogoImage')"
                                            placeholder="Project Logo Image" id="logo_image" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewLogoImage">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Feature Image:</label>
                                        <input type="file"
                                            class="form-control @error('feature_image') is-invalid @enderror"
                                            name="feature_image" onchange="previewImage(this, '#previewFeatureImage')"
                                            placeholder="Project feature Image" id="feature_image" required>
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewFeatureImage">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Site Plan Images:</label>
                                        <input type="file"
                                            class="form-control @error('site_plans_images') is-invalid @enderror"
                                            name="site_plans_images"
                                            onchange="previewImage(this, '#previewSitePlanImages')"
                                            placeholder="Project Site Plan Images" id="site_plans_images">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewSitePlanImages">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Brochure:</label>
                                        <input type="file"
                                            class="form-control @error('floor_plans_images') is-invalid @enderror"
                                            name="floor_plans_images"
                                            onchange="previewImage(this, '#previewFloorPlanImages')"
                                            placeholder="Project Brochure" id="floor_plans_images">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewFloorPlanImages">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Location Video:</label>
                                        <input type="file"
                                            class="form-control @error('location_video') is-invalid @enderror"
                                            name="location_video" onchange="previewImage(this, '#previewLocationVideo')"
                                            placeholder="Project Location Video" id="location_video">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewLocationVideo">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Price List:</label>
                                        <input type="file"
                                            class="form-control @error('price_list') is-invalid @enderror"
                                            name="price_list" onchange="previewImage(this, '#previewPriceList')"
                                            placeholder="Project Price List" id="price_list">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewPriceList">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Sanctioned Map:</label>
                                        <input type="file"
                                            class="form-control @error('sanctioned_map') is-invalid @enderror"
                                            name="sanctioned_map"
                                            onchange="previewImage(this, '#previewSanctionedMap')"
                                            placeholder="Project sanctioned map" id="sanctioned_map">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewSanctionedMap">
                                    </div>
                                </div>
								
								<div class="col-sm-12 col-lg-6 d-flex mb-4">
                                    <div class="form-group fileInput mr-2">
                                        <label for="thumbnail">Lease Deed:</label>
                                        <input type="file"
                                            class="form-control @error('lease_deed') is-invalid @enderror"
                                            name="lease_deed"
                                            onchange="previewImage(this, '#previewLeaseDeed')"
                                            placeholder="Project Brochure" id="lease_deed">
                                    </div>
                                    <div class="fileInput">
                                        <img src="/../../assets/images/NA.webp" alt="photo" id="previewLeaseDeed">
                                    </div>
                                </div>

                                <div class="col-sm-12 col-lg-6">

                                </div>
                                <div class="col-12">
                                    <h5>
                                        BHK Plans
                                    </h5>
                                    @php
                                    $selectedType = old('project_type', 'apartment'); // apartment type
                                    @endphp
									<div class="row">
                                    <select name="project_type" id="project_type" class="col-6 custom-select mb-3" >
										<option value="apartment" {{ old('project_type', 'apartment') === 'apartment' ? 'selected' : '' }}>
											Apartments
										</option>
										<option value="plots" {{ old('project_type') === 'plots' ? 'selected' : '' }}>
											Plots
										</option>
									</select>
									
									<button type="button" class="btn btn-success plusBtn ml-auto">
										<i class="fa fa-plus"></i> Add Floor Plan
									</button>
									
									</div>
                                </div>

                                <div class="floors row p-3">
                                    @if(is_array(old('floor_plans')) && count(old('floor_plans')) > 0)
                                    @foreach(old('floor_plans',[]) as $key => $floorPlan)
                                    <div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-2">
                                                <label>Title:</label>
                                                <input value="{{ $floorPlan['title'] }}"
                                                    name="floor_plans[{{ $key }}][title]" type="text"
                                                    class="form-control" required>
                                            </div>

                                            @if($selectedType === 'apartment')
                                            <div class="col-2">
                                                <label>Super Area:</label>
                                                <input value="{{ $floorPlan['super_area'] }}"
                                                    name="floor_plans[{{ $key }}][super_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-2">
                                                <label>Carpet Area:</label>
                                                <input value="{{ $floorPlan['carpet_area'] }}"
                                                    name="floor_plans[{{ $key }}][carpet_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                           
                                            <div class="col-2">
                                                <label>Balcony Area:</label>
                                                <input value="{{ $floorPlan['balcony_area'] }}"
                                                    name="floor_plans[{{ $key }}][balcony_area]" type="text"
                                                    class="form-control" required>
                                            </div>
											
											 <div class="col-2">
                                                <label>Builtup Area:</label>
                                                <input value="{{ $floorPlan['built_area'] }}"
                                                    name="floor_plans[{{ $key }}][built_area]" type="text"
                                                    class="form-control" required>
                                            </div>
											
                                            @else
                                            <div class="col-2">
                                                <label>Length:</label>
                                                <input value="{{ $floorPlan['length'] }}"
                                                    name="floor_plans[{{ $key }}][length]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-2">
                                                <label>Width:</label>
                                                <input value="{{ $floorPlan['width'] }}"
                                                    name="floor_plans[{{ $key }}][width]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-2">
                                                <label>Total Area:</label>
                                                <input value="{{ $floorPlan['total_area'] }}"
                                                    name="floor_plans[{{ $key }}][total_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            @endif

                                            <div class="col-2">
                                                <label>Image:</label>
                                                <input name="floor_plans[{{ $key }}][feature_image]" type="file"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else


                                    <div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-2">
                                                <label for="title">Title:</label>
                                                <input name="floor_plans[0][title]" type="text" class="form-control"
                                                    required>
                                            </div>
                                            <div class="col-2">
                                                <label for="super_area">Super Area:</label>
                                                <input name="floor_plans[0][super_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-2">
                                                <label for="carpet_area">Carpet Area:</label>
                                                <input name="floor_plans[0][carpet_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            
                                            <div class="col-2">
                                                <label for="balcony_area">Balcony Area:</label>
                                                <input name="floor_plans[0][balcony_area]" type="text"
                                                    class="form-control" required>
                                            </div>
											<div class="col-2">
                                                <label for="built_area">Builtup Area:</label>
                                                <input name="floor_plans[0][built_area]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-2">
                                                <label for="feature_image">Image:</label>
                                                <input name="floor_plans[0][feature_image]" type="file"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-12 mb-3">
                                    <h5>
                                        SEO Section
                                    </h5>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="title">Title</label>
                                    <input class="form-control input @error('seo_data[title]') is-invalid @enderror "
                                        type="text" placeholder="Enter Title" value="{{@old('seo_data')['title']}}"
                                        name="seo_data[title]" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="primary_keyword">Primary Keyword</label>
                                    <input
                                        class="form-control input @error('seo_data[primary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Primary Keyword"
                                        value="{{@old('seo_data')['primary_keyword']}}" name="seo_data[primary_keyword]"
                                        required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="secondary_keyword">Secondary Keyword</label>
                                    <input
                                        class="form-control input @error('seo_data[secondary_keyword]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Secondary Keyword"
                                        value="{{@old('seo_data')['secondary_keyword']}}"
                                        name="seo_data[secondary_keyword]" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="meta_description">Meta Description</label>
                                    <input
                                        class="form-control input @error('seo_data[meta_description]') is-invalid @enderror "
                                        type="text" placeholder="Enter Blog Meta Description"
                                        value="{{@old('seo_data')['meta_description']}}"
                                        name="seo_data[meta_description]" required>
                                </div>

                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="canonical_link">Canonical Link</label>
                                    <input
                                        class="form-control input @error('seo_data[canonical_link]') is-invalid @enderror "
                                        type="text" placeholder="Enter Canonical Link"
                                        value="{{@old('seo_data')['canonical_link']}}" name="seo_data[canonical_link]"
                                        required>
                                </div>
                                <div class="col-12">
                                    <h5>
                                        FAQs Section
                                    </h5>
                                </div>
                                <div class="row p-3 faqs" style="width:100%">
                                    @if(!empty(@old('faqs_data')) && count(@old('faqs_data')) > 0)
                                    @foreach(@old('faqs_data') as $key => $faqsData)
                                    <div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-6">
                                                <label for="question">Question:</label>
                                                <input value="{{$faqsData['question']}}"
                                                    name="faqs_data[{{$key}}][question]" type="text"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-6">
                                                <label for="answer">Answer:</label>
                                                <input value="{{$faqsData['answer']}}"
                                                    name="faqs_data[{{$key}}][answer]" type="text" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-6">
                                                <label for="question">Question:</label>
                                                <input name="faqs_data[0][question]" type="text" class="form-control"
                                                    required>
                                            </div>
                                            <div class="col-6">
                                                <label for="answer">Answer:</label>
                                                <input name="faqs_data[0][answer]" type="text" class="form-control"
                                                    required>
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
										$oldSqftPrices = old('sqft_price') ?? [['applied_from' => '', 'value' => '']];
									@endphp
									@if(!empty($oldSqftPrices) && count($oldSqftPrices) > 0)
										@foreach($oldSqftPrices as $key => $price)
											<div class="col-sm-6 col-lg-6 mt-2 sqftPriceData" style="padding:0 30px">
												<div class="row p-2" style="background:#1b577733">
													<div class="col-6">
														<label for="applied_from">Applied From:</label>
														<input 
															value="{{ $price['applied_from'] ?? '' }}"
															name="sqft_price[{{$key}}][applied_from]" 
															type="date" 
															class="form-control" 
															required>
													</div>
													<div class="col-6">
														<label for="value">Price per Sqft:</label>
														<input 
															value="{{ $price['value'] ?? '' }}"
															name="sqft_price[{{$key}}][value]" 
															type="text" 
															class="form-control" 
															placeholder="Enter price per sqft"
															required>
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
									@endif
								</div>
                                <div class="col-12">
                                    <h5>
                                        Rera Data Section
                                    </h5>
                                </div>
                                <div class="row p-3 rera" style="width:100%">
                                    @if(!empty(@old('rera_data')) && count(@old('rera_data')) > 0)
                                    @foreach(@old('rera_data') as $key => $reraData)
                                    <div class="col-sm-12 col-lg-12 mt-2 reraData" style="padding:0 30px">
                                        <div class="row pt-5 pb-5" style="background:#1b577733">
                                            <div class="col-4">
                                                <label for="phase">Phase:</label>
                                                <input value="{{$reraData['phase']}}"
                                                    name="rera_data[{{$key}}][phase]" type="text"
                                                    class="form-control" >
                                            </div>
                                            <div class="col-4">
                                                <label for="rera_no">Rera Number:</label>
                                                <input value="{{$reraData['rera_no']}}"
                                                    name="rera_data[{{$key}}][rera_no]" type="text" class="form-control"
                                                    >
                                            </div>
											
											<div class="col-4">
                                                <label>QR Image:</label>
                                              
												<input value="{{$reraData['qr_image']}}"
                                                    name="rera_data[{{$key}}][qr_image]" type="file" class="form-control"
                                                    >	
                                            </div>
											
                                        </div>
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
								
								
                                <button id="storeBtn"  type="submit" class="btn btn-primary">Upload Project</button>
                            </div>
                        </form> 
                    </div>

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
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
let selectedAreaType = $('#project_type').val();

// Function to generate floor plan fields based on type and index
function generateFloorPlanFields(type, index) {
    let html = '';

    if (type === 'apartment') {
        html = `<div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans" style="padding:0 30px">
            <div class="row pt-5 pb-5" style="background:#1b577733">
                <div class="col">
                    <label for="title">Title:</label>
                    <input name="floor_plans[${index}][title]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="super_area">Super Area:</label>
                    <input name="floor_plans[${index}][super_area]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="carpet_area">Carpet Area:</label>
                    <input name="floor_plans[${index}][carpet_area]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="built_area">Builtup Area:</label>
                    <input name="floor_plans[${index}][built_area]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="balcony_area">Balcony Area:</label>
                    <input name="floor_plans[${index}][balcony_area]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="feature_image">Image:</label>
                    <input name="floor_plans[${index}][feature_image]" type="file" class="form-control" required>
                </div>
            </div>
            <span class="bg-danger minusBtn">
                <i class="fa fa-minus"></i>
            </span>
        </div>`;
    } else if (type === 'plots') {
        html = `<div class="col-sm-12 col-lg-12 mt-2 pdfloorPlans" style="padding:0 30px">
            <div class="row pt-5 pb-5" style="background:#1b577733">
                <div class="col">
                    <label for="title">Title:</label>
                    <input name="floor_plans[${index}][title]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="length">Length:</label>
                    <input name="floor_plans[${index}][length]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="width">Width:</label>
                    <input name="floor_plans[${index}][width]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="total_area">Total Area:</label>
                    <input name="floor_plans[${index}][total_area]" type="text" class="form-control" required>
                </div>
                <div class="col-2">
                    <label for="feature_image">Image:</label>
                    <input name="floor_plans[${index}][feature_image]" type="file" class="form-control" required>
                </div>
            </div>
            <span class="bg-danger minusBtn">
                <i class="fa fa-minus"></i>
            </span>
        </div>`;
    }

    return html;
}

// Function to reindex names properly
function reIndexFloorPlans() {
    $('.pdfloorPlans').each(function (i) {
        $(this).find('input').each(function () {
            const name = $(this).attr('name');
            if (name) {
                const updatedName = name.replace(/\[\d+\]/, `[${i}]`);
                $(this).attr('name', updatedName);
            }
        });
    });
}

// Initial rendering based on selected option
function renderInitialFloorPlan() {
    $('.floors').html(generateFloorPlanFields(selectedAreaType, 0));
}

$(document).ready(function () {
    // renderInitialFloorPlan();

    // Handle change of type dropdown
    $('#project_type').on('change', function () {
        selectedAreaType = $(this).val();
        renderInitialFloorPlan();
    });

    // Add new floor plan
    $('.plusBtn').click(function () {
        const currentIndex = $('.floors .pdfloorPlans').length;
        $('.floors').append(generateFloorPlanFields(selectedAreaType, currentIndex));
        reIndexFloorPlans();
    });

    // Remove floor plan
    $(document).on('click', '.minusBtn', function () {
        $(this).closest('.pdfloorPlans').remove();
        reIndexFloorPlans();
    });
});

</script>
<script>
    //function to re-index the FAQs index
    function reIndexFaqsData() {
        $('.faqs .faqData').each(function (index) {
            // Update index in input names
            $(this).find('[name^="faqs_data"]').each(function () {
                let newName = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }
    // plus button click event for the Faq data
    $('.plusBtnFAQ').click(function () {
        let currentIndex = $('.faqs .faqData').length; // Get the current number of floor plans

        let html = `<div class="col-sm-12 col-lg-12 mt-2 faqData" style="padding:0 30px">
                        <div class="row pt-5 pb-5" style="background:#1b577733">

                            <div class="col-6">
                                <label for="question">Question:</label>
                                <input name="faqs_data[${currentIndex}][question]" type="text" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label for="answer">Answer:</label>
                                <input name="faqs_data[${currentIndex}][answer]" type="text" class="form-control" required>
                            </div>
                        </div>
                        <span class="bg-danger minusBtnFAQ">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>`;
        $('.faqs').append(html);
        reIndexFaqsData();
    });
    $(document).on('click', '.minusBtnFAQ', function () {
        $(this).closest('.faqData').remove();
        reIndexFaqsData();
    });

    $('#openModalBtn').click(function () {
        $('#myModal').modal('show');
    });

    $('#aminityBtn').on('click', function (e) {
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
            success: function (response) {
                form.reset();
                $('#response').html('<p>' + response.message + '</p>'); // Handle success response
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors; 
                var errorMessage = ''; 
                for (var key in errors) {
                    errorMessage += errors[key].join('<br>') + '<br>';
                }
                $('#response').html('<p style="color:red;">' + errorMessage + '</p>'); // Handle errors
            }
        });
    });
    $('#developerBtn').on('click', function (e) {
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
            success: function (response) {
                form.reset();
                $('#response').html('<p>' + response.message + '</p>'); // Handle success response
            },
            error: function (xhr) {
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
<script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
<script>
$(document).ready(function () {

    // Function to reindex all sqft price rows
    function reIndexSqftPriceData() {
        $('.sqftPrice .sqftPriceData').each(function (index) {
            $(this).find('[name^="sqft_price"]').each(function () {
                let name = $(this).attr('name');
                // Replace the index number inside sqft_price[0]
                let updatedName = name.replace(/sqft_price\[\d+\]/, `sqft_price[${index}]`);
                $(this).attr('name', updatedName);
            });
        });
    }

    // Plus button click event for adding a new sqft price row
    $(document).on('click', '.sqftPricePlusBtn', function () {
        let currentIndex = $('.sqftPrice .sqftPriceData').length;

        let html = `
            <div class="col-sm-6 col-lg-6 mt-2 sqftPriceData" style="padding:0 30px">
                <div class="row p-2" style="background:#1b577733">
                    <div class="col-6">
                        <label>Applied From:</label>
                        <input name="sqft_price[${currentIndex}][applied_from]" type="date" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label>Price per Sqft:</label>
                        <input name="sqft_price[${currentIndex}][value]" type="text" class="form-control" placeholder="Enter price per sqft" required>
                    </div>
                </div>
                <span class="bg-danger sqftPriceMinusBtn" style="cursor:pointer">
                    <i class="fa fa-minus"></i>
                </span>
            </div>
        `;

        $('.sqftPrice').append(html);
        reIndexSqftPriceData(); // ensure all names are sequential
    });

    // Minus button click event for removing a row
    $(document).on('click', '.sqftPriceMinusBtn', function () {
        $(this).closest('.sqftPriceData').remove();
        reIndexSqftPriceData(); // reindex after deletion
    });

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
                                <input name="rera_data[${currentIndex}][phase]" type="text" class="form-control" >
                            </div>
                            <div class="col-4">
                                <label for="rera_no">Rera Number:</label>
                                <input name="rera_data[${currentIndex}][rera_no]" type="text" class="form-control" >
                            </div>
							<div class="col-4">
								<label for="qr_image">Image:</label>
								<input name="rera_data[${currentIndex}][qr_image]" type="file" class="form-control" >
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
<script>
    (function () {
        const importInput = document.querySelector('#docImportInput');
        const statusBox = document.querySelector('#docImportStatus');

        if (!importInput || !statusBox) {
            return;
        }

        const headingMap = {
            project_name: ['project name'],
            keywords: ['keywords'],
            seo_title: ['seo title'],
            meta_description: ['meta description'],
            hero_section: ['hero section'],
            launch_date: ['launch date'],
            property_size: ['property size'],
            project_status: ['project status'],
            city: ['city'],
            location: ['location'],
            rera: ['rera', 'rera phase 1 / phase 2', 'rera phase 1/phase 2', 'rera (phase 1 / phase 2)', 'rera phase 1', 'rera phase 2'],
            developer: ['developer'],
            bhk_types: ['bhk types', 'available bhk types'],
            project_price: ['project price', 'price range'],
            floor_plans: ['floor plans', 'bhk plans', 'bhk plan'],
            amenities: ['amenities'],
            faqs: ['faqs', 'faq']
        };

        const normalizedHeadingLookup = {};
        Object.keys(headingMap).forEach(function (key) {
            headingMap[key].forEach(function (variant) {
                normalizedHeadingLookup[normalizeHeading(variant)] = key;
            });
        });

        importInput.addEventListener('change', async function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                return;
            }

            setStatus('Parsing document...', 'text-info');
            importInput.disabled = true;

            try {
                const arrayBuffer = await file.arrayBuffer();
                const result = await mammoth.extractRawText({ arrayBuffer: arrayBuffer });
                const rawText = cleanText(result.value || '');

                if (!rawText) {
                    throw new Error('No readable text found in the selected document.');
                }

                const sections = parseSections(rawText);
                fillFormFromSections(sections);
                setStatus('Document parsed successfully. Review the filled fields before submitting.', 'text-success');
            } catch (error) {
                setStatus(error && error.message ? error.message : 'Failed to parse the document.', 'text-danger');
            } finally {
                importInput.disabled = false;
            }
        });

        function setStatus(message, cssClass) {
            statusBox.className = 'mt-2 ' + cssClass;
            statusBox.textContent = message;
        }

        function normalizeHeading(value) {
            return String(value || '')
                .toLowerCase()
                .replace(/[*_`#]/g, ' ')
                .replace(/\(.*?\)/g, ' ')
                .replace(/[^a-z0-9]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function cleanLine(line) {
            return String(line || '')
                .replace(/[\u2022\u25CF\u25A0]/g, ' ')
                .replace(/\*{2,}/g, ' ')
                .replace(/^[\s\-:;,.|]+|[\s\-:;,.|]+$/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function cleanText(value) {
            return String(value || '')
                .replace(/\r/g, '\n')
                .replace(/\n{3,}/g, '\n\n')
                .replace(/[ \t]+/g, ' ')
                .replace(/\*{2,}/g, ' ')
                .trim();
        }

        function detectHeading(line) {
            const cleaned = cleanLine(line);
            if (!cleaned) {
                return null;
            }

            const colonIndex = cleaned.indexOf(':');
            const headingCandidate = colonIndex > -1 ? cleaned.slice(0, colonIndex) : cleaned;
            const normalized = normalizeHeading(headingCandidate);

            if (normalizedHeadingLookup[normalized]) {
                return {
                    key: normalizedHeadingLookup[normalized],
                    inlineValue: colonIndex > -1 ? cleanLine(cleaned.slice(colonIndex + 1)) : ''
                };
            }

            return null;
        }

        function parseSections(rawText) {
            const lines = rawText.split('\n');
            const sections = {};
            let currentKey = null;

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                const heading = detectHeading(line);

                if (heading) {
                    currentKey = heading.key;
                    if (!sections[currentKey]) {
                        sections[currentKey] = '';
                    }
                    if (heading.inlineValue) {
                        sections[currentKey] = sections[currentKey]
                            ? (sections[currentKey] + '\n' + heading.inlineValue)
                            : heading.inlineValue;
                    }
                    continue;
                }

                if (!currentKey) {
                    continue;
                }

                const cleaned = cleanLine(line);
                if (!cleaned) {
                    continue;
                }

                sections[currentKey] = sections[currentKey]
                    ? (sections[currentKey] + '\n' + cleaned)
                    : cleaned;
            }

            Object.keys(sections).forEach(function (key) {
                sections[key] = cleanText(sections[key]);
            });

            return sections;
        }

        function applySummernoteValue(textarea, value, attempt) {
            const maxAttempts = 8;
            const currentAttempt = attempt || 0;

            textarea.value = value;

            if (!window.jQuery || !jQuery.fn || !jQuery.fn.summernote) {
                return;
            }

            const $textarea = jQuery(textarea);
            if ($textarea.next('.note-editor').length > 0) {
                $textarea.summernote('code', value);
                $textarea.trigger('change');
                return;
            }

            if (currentAttempt < maxAttempts) {
                setTimeout(function () {
                    applySummernoteValue(textarea, value, currentAttempt + 1);
                }, 120);
            }
        }

        function setInputValue(selector, value) {
            const el = document.querySelector(selector);
            if (!el || value === null || value === undefined) {
                return;
            }

            const finalValue = cleanText(value);

            if (el.tagName === 'TEXTAREA') {
                applySummernoteValue(el, finalValue);
                return;
            }

            if (el.type === 'checkbox') {
                el.checked = Boolean(finalValue);
                return;
            }

            el.value = finalValue;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function setSelectByValue(selector, value) {
            const select = document.querySelector(selector);
            if (!select || !value) {
                return;
            }

            select.value = value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            if (window.jQuery && jQuery(select).hasClass('select2')) {
                jQuery(select).trigger('change.select2');
            }
        }

        function setMultiSelectByTokens(selector, tokens) {
            const select = document.querySelector(selector);
            if (!select || !Array.isArray(tokens) || tokens.length === 0) {
                return;
            }

            const normalizedTokens = tokens.map(function (token) {
                return normalizeHeading(token);
            });

            Array.from(select.options).forEach(function (option) {
                const optionToken = normalizeHeading(option.textContent || option.value);
                option.selected = normalizedTokens.includes(optionToken);
            });

            select.dispatchEvent(new Event('change', { bubbles: true }));
            if (window.jQuery && jQuery(select).hasClass('select2')) {
                jQuery(select).trigger('change.select2');
            }
        }

        function slugify(value) {
            return String(value || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        function parseProjectStatus(value) {
            const normalized = normalizeHeading(value);
            if (!normalized) {
                return '';
            }
            if (normalized.includes('ready')) return 'ready_to_move';
            if (normalized.includes('under')) return 'under_construction';
            if (normalized.includes('completed') || normalized.includes('complete')) return 'completed';
            if (normalized.includes('new') || normalized.includes('launch')) return 'new_launch';
            return '';
        }

        function extractPriceNumbers(value) {
            const source = String(value || '').toLowerCase();
            if (!source) {
                return { min: '', max: '' };
            }

            const matches = source.match(/(?:inr|rs\.?|\u20b9)?\s*\d+(?:[.,]\d+)?\s*(?:cr|crore|lakh|lac|k)?/g) || [];
            const numbers = matches
                .map(function (item) { return toAbsoluteNumber(item); })
                .filter(function (num) { return Number.isFinite(num) && num > 0; });

            if (numbers.length === 0) {
                return { min: '', max: '' };
            }

            if (numbers.length === 1) {
                return { min: String(Math.round(numbers[0])), max: '' };
            }

            numbers.sort(function (a, b) { return a - b; });
            return {
                min: String(Math.round(numbers[0])),
                max: String(Math.round(numbers[numbers.length - 1]))
            };
        }

        function toAbsoluteNumber(raw) {
            let token = String(raw || '').toLowerCase().replace(/,/g, '').trim();
            const baseMatch = token.match(/\d+(?:\.\d+)?/);
            if (!baseMatch) return NaN;
            const base = parseFloat(baseMatch[0]);

            if (token.includes('crore') || token.includes('cr')) {
                return base * 10000000;
            }
            if (token.includes('lakh') || token.includes('lac')) {
                return base * 100000;
            }
            if (token.match(/\bk\b/)) {
                return base * 1000;
            }
            return base;
        }

        function parseCommaList(value) {
            return String(value || '')
                .split(/[,|\n]+/)
                .map(function (item) { return cleanLine(item); })
                .filter(Boolean);
        }

        function parseBhkTypes(value) {
            const list = parseCommaList(value);
            const results = [];
            list.forEach(function (item) {
                const lowered = item.toLowerCase();
                const bhkMatch = lowered.match(/(\d+)\s*bhk/);
                if (bhkMatch) {
                    const bhk = bhkMatch[1] + ' BHK';
                    if (!results.includes(bhk)) {
                        results.push(bhk);
                    }
                    return;
                }
                if (lowered.includes('plot') && !results.includes('Plots')) {
                    results.push('Plots');
                }
            });
            return results;
        }

        function parseFloorPlans(value) {
            const text = String(value || '').trim();
            if (!text) {
                return [];
            }

            const chunks = text
                .split(/\n\s*\n+/)
                .map(function (chunk) { return cleanText(chunk); })
                .filter(Boolean);

            const plans = [];

            chunks.forEach(function (chunk) {
                const normalized = normalizeHeading(chunk);
                const isPlot = normalized.includes('plot') || /length|width|total\s*area/i.test(chunk);
                const plan = {
                    type: isPlot ? 'plots' : 'apartment',
                    title: '',
                    super_area: '',
                    carpet_area: '',
                    built_area: '',
                    balcony_area: '',
                    length: '',
                    width: '',
                    total_area: ''
                };

                const lines = chunk.split('\n').map(cleanLine).filter(Boolean);
                const titleMatch =
                    chunk.match(/(?:title|plan|unit|typology)\s*[:\-]\s*([^\n]+)/i) ||
                    chunk.match(/(\d+\s*bhk[^\n,;|]*)/i);

                if (titleMatch) {
                    plan.title = cleanLine(titleMatch[1] || titleMatch[0]);
                } else if (lines.length > 0) {
                    plan.title = cleanLine(lines[0]);
                }

                function extractValue(regex) {
                    const match = chunk.match(regex);
                    return match ? cleanLine(match[1]) : '';
                }

                plan.super_area = extractValue(/super\s*area\s*[:\-]\s*([^\n,;|]+)/i);
                plan.carpet_area = extractValue(/carpet\s*area\s*[:\-]\s*([^\n,;|]+)/i);
                plan.built_area = extractValue(/built(?:up)?\s*area\s*[:\-]\s*([^\n,;|]+)/i);
                plan.balcony_area = extractValue(/balcony\s*area\s*[:\-]\s*([^\n,;|]+)/i);
                plan.length = extractValue(/length\s*[:\-]\s*([^\n,;|]+)/i);
                plan.width = extractValue(/width\s*[:\-]\s*([^\n,;|]+)/i);
                plan.total_area = extractValue(/total\s*area\s*[:\-]\s*([^\n,;|]+)/i);

                const hasData =
                    plan.title ||
                    plan.super_area ||
                    plan.carpet_area ||
                    plan.built_area ||
                    plan.balcony_area ||
                    plan.length ||
                    plan.width ||
                    plan.total_area;

                if (hasData) {
                    plans.push(plan);
                }
            });

            return plans;
        }

        function setFloorInputValue(row, field, value) {
            if (!row || !value) {
                return;
            }
            const input = row.querySelector('input[name*="[' + field + ']"]');
            if (input) {
                input.value = cleanText(value);
            }
        }

        function fillFloorPlans(plans) {
            if (!Array.isArray(plans) || plans.length === 0) {
                return;
            }

            const plotsCount = plans.filter(function (plan) { return plan.type === 'plots'; }).length;
            const targetType = plotsCount > plans.length / 2 ? 'plots' : 'apartment';
            const typeSelect = document.querySelector('#project_type');
            const addBtn = document.querySelector('.plusBtn');

            if (!typeSelect || !addBtn) {
                return;
            }

            setSelectByValue('#project_type', targetType);
            if (typeof renderInitialFloorPlan === 'function') {
                renderInitialFloorPlan();
            }

            let rowCount = document.querySelectorAll('.floors .pdfloorPlans').length;
            while (rowCount < plans.length) {
                addBtn.click();
                rowCount++;
            }

            while (rowCount > plans.length) {
                const minusButtons = document.querySelectorAll('.floors .pdfloorPlans .minusBtn');
                if (!minusButtons.length) {
                    break;
                }
                minusButtons[minusButtons.length - 1].click();
                rowCount--;
            }

            const rows = document.querySelectorAll('.floors .pdfloorPlans');
            plans.forEach(function (plan, index) {
                const row = rows[index];
                if (!row) {
                    return;
                }

                const defaultTitle = targetType === 'plots' ? ('Plot ' + (index + 1)) : ('Plan ' + (index + 1));
                setFloorInputValue(row, 'title', plan.title || defaultTitle);

                if (targetType === 'plots') {
                    setFloorInputValue(row, 'length', plan.length);
                    setFloorInputValue(row, 'width', plan.width);
                    setFloorInputValue(row, 'total_area', plan.total_area);
                } else {
                    setFloorInputValue(row, 'super_area', plan.super_area);
                    setFloorInputValue(row, 'carpet_area', plan.carpet_area);
                    setFloorInputValue(row, 'built_area', plan.built_area);
                    setFloorInputValue(row, 'balcony_area', plan.balcony_area);
                }
            });
        }

        function extractReraValue(value) {
            const source = String(value || '');
            const phase1 = source.match(/phase\s*1\s*[:\-]?\s*([^\n|]+)/i);
            const phase2 = source.match(/phase\s*2\s*[:\-]?\s*([^\n|]+)/i);

            if (phase1 || phase2) {
                const parts = [];
                if (phase1 && phase1[1]) parts.push('Phase 1: ' + cleanLine(phase1[1]));
                if (phase2 && phase2[1]) parts.push('Phase 2: ' + cleanLine(phase2[1]));
                return parts.join(' | ');
            }

            return cleanText(source);
        }

        function parseFaqPairs(value) {
            const text = String(value || '').trim();
            if (!text) {
                return [];
            }

            const pairs = [];
            const qaRegex = /q(?:uestion)?\s*[:\-]\s*(.+?)\s*a(?:nswer)?\s*[:\-]\s*(.+?)(?=(?:\n\s*q(?:uestion)?\s*[:\-])|$)/gis;
            let match;
            while ((match = qaRegex.exec(text)) !== null) {
                const question = cleanText(match[1]);
                const answer = cleanText(match[2]);
                if (question && answer) {
                    pairs.push({ question: question, answer: answer });
                }
            }

            if (pairs.length > 0) {
                return pairs;
            }

            const lines = text.split('\n').map(cleanLine).filter(Boolean);
            let pendingQuestion = '';
            lines.forEach(function (line) {
                if (!pendingQuestion && line.endsWith('?')) {
                    pendingQuestion = line;
                    return;
                }
                if (pendingQuestion) {
                    pairs.push({ question: pendingQuestion, answer: line });
                    pendingQuestion = '';
                }
            });

            return pairs;
        }

        function ensureFaqRows(count) {
            const existingRows = document.querySelectorAll('.faqs .faqData');
            const addBtn = document.querySelector('.plusBtnFAQ');
            const need = Math.max(1, count);

            for (let i = existingRows.length; i < need; i++) {
                if (addBtn) {
                    addBtn.click();
                }
            }
        }

        function fillFaqs(pairs) {
            if (!Array.isArray(pairs) || pairs.length === 0) {
                return;
            }

            ensureFaqRows(pairs.length);
            const rows = document.querySelectorAll('.faqs .faqData');

            pairs.forEach(function (pair, index) {
                const questionInput = rows[index] ? rows[index].querySelector('[name^="faqs_data"][name$="[question]"]') : null;
                const answerInput = rows[index] ? rows[index].querySelector('[name^="faqs_data"][name$="[answer]"]') : null;
                if (questionInput) questionInput.value = cleanText(pair.question);
                if (answerInput) answerInput.value = cleanText(pair.answer);
            });
        }

        function fillFormFromSections(sections) {
            const projectName = sections.project_name || '';
            const keywords = sections.keywords || '';
            const seoTitle = sections.seo_title || '';
            const metaDescription = sections.meta_description || '';
            const launchDate = sections.launch_date || '';
            const propertySize = sections.property_size || '';
            const projectStatus = sections.project_status || '';
            const city = sections.city || '';
            const location = sections.location || '';
            const rera = sections.rera || '';
            const developer = sections.developer || '';
            const bhkTypes = sections.bhk_types || '';
            const projectPrice = sections.project_price || '';
            const heroSection = sections.hero_section || '';
            const floorPlans = sections.floor_plans || '';
            const amenities = sections.amenities || '';
            const faqs = sections.faqs || '';

            if (projectName) {
                setInputValue('input[name="project_name"]', projectName);
                const generatedSlug = slugify(projectName);
                if (generatedSlug) {
                    setInputValue('input[name="slug"]', generatedSlug);
                }
            }

            if (keywords) {
                const parsedKeywords = parseCommaList(keywords);
                if (parsedKeywords.length > 0) {
                    setInputValue('input[name="seo_data[primary_keyword]"]', parsedKeywords[0]);
                }
                if (parsedKeywords.length > 1) {
                    setInputValue('input[name="seo_data[secondary_keyword]"]', parsedKeywords.slice(1).join(', '));
                }
            }

            if (seoTitle) setInputValue('input[name="seo_data[title]"]', seoTitle);
            if (metaDescription) setInputValue('input[name="seo_data[meta_description]"]', metaDescription);
            if (launchDate) setInputValue('input[name="launch_date"]', launchDate);
            if (propertySize) setInputValue('input[name="property_size"]', propertySize);
            if (city) setInputValue('input[name="cities"]', city);
            if (location) setInputValue('input[name="location"]', location);
            if (developer) setInputValue('input[name="developer_name"]', developer);
            if (rera) setInputValue('input[name="rera_no"]', extractReraValue(rera));

           

            const statusValue = parseProjectStatus(projectStatus);
            if (statusValue) {
                setSelectByValue('select[name="project_status"]', statusValue);
            }

            const bhkList = parseBhkTypes(bhkTypes);
            if (bhkList.length > 0) {
                setMultiSelectByTokens('select[name="typology[]"]', bhkList);
            }

            const amenityTokens = parseCommaList(amenities);
            if (amenityTokens.length > 0) {
                setMultiSelectByTokens('#amenities', amenityTokens);
            }

            if (projectPrice) {
                const range = extractPriceNumbers(projectPrice);
                if (range.min) setInputValue('input[name="price"]', range.min);
                if (range.max) setInputValue('input[name="max_price"]', range.max);
            }

            const floorPlanList = parseFloorPlans(floorPlans);
            if (floorPlanList.length > 0) {
                fillFloorPlans(floorPlanList);
            }

            const faqPairs = parseFaqPairs(faqs);
            if (faqPairs.length > 0) {
                fillFaqs(faqPairs);
            }
        }
    })();
</script>
@endsection
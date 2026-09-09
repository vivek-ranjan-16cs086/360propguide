<div class="row justify-content-center">

    @include('frontend.properties.steps.partials.sidebar')

    {{-- Step Content Area --}}
    <div class="col-md-7 col-lg-8">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="card p-4 p-lg-5  border-0 shadow-lg border-secondary-subtle multiStepForm">
            <div class="px-md-4">
                <h4 class="mb-4">Step 1: Basic Details</h4>
                <div id="formErrors" class="text-danger mb-3"></div>

                <form id="stepForm" action="{{ route('postproperty.edit.property_details.save', $property->property_uid) }}" method="POST" novalidate>  
                    @csrf
                    <div class="row">
                        <div class="">
                            <div class="position-relative mb-4">
                                <div class="form-floating">
                                    <input data-label="Project Name" type="text" class="form-control shadow-none border-0 border-bottom rounded-0" required
                                        id="project_search" name="project_search"
                                        value="{{ old('project_search', $projectName ?? '') }}"
                                        placeholder="Enter project name"
										autocomplete="off">
                                    <label for="project_search">Building/Project/Society</label>
                                </div>

                                <input class="form-control" data-label="Project" type="hidden" required name="project_id" id="project_id"
                                    value="{{ old('project_id', $property->project_id ?? '') }}">
                                <div class="invalid-feedback"></div>

                                <div id="project_suggestions" class="list-group position-absolute w-100 shadow-lg"
                                    style="z-index: 1000; display: none;"></div>
                            </div>

                        </div>
                        @php
                        $configurations = [
                            ['value' => '1_bhk', 'label' => '1 BHK'],
                            ['value' => '2_bhk', 'label' => '2 BHK'],
                            ['value' => '3_bhk', 'label' => '3 BHK'],
                            ['value' => '4_bhk', 'label' => '4 BHK'],
                            ['value' => '5_bhk', 'label' => '5 BHK'],
							['value' => '6_bhk', 'label' => '6 BHK'],
							['value' => 'shops', 'label' => 'Shops'],
							['value' => 'studio', 'label' => 'Studio Apartments'], 
                            ['value' => 'plot', 'label' => 'PLOT'],
                        ];
                        
                        if (isset($property)) {
                            if ($property->property_type === 'plots') {
                                // Show only "plot"
                                $configurations = array_filter($configurations, fn($item) => $item['value'] === 'plot');
                            } else {
                                // Show everything except "plot"
                                $configurations = array_filter($configurations, fn($item) => $item['value'] !== 'plot');
                            }
                        }
                        @endphp


                        <div class="mb-4">
                            <p>Configuration Type</p>
                            @foreach ($configurations as $config)
                            <input type="radio" data-label="Configuration Type" class="btn-check" name="configuration" value="{{ $config['value'] }}" @if(isset($data['configuration']) && $data['configuration']===$config['value']) checked @endif
                                id="{{ $config['value'] }}" autocomplete="off" required>
                            <label class="radio-btn me-2 me-mb-3 mb-3 px-4 py-2" for="{{ $config['value'] }}">
                                {{ $config['label'] }}
                            </label>
                            @endforeach
                            @error('configuration_in_bhk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-8 mb-4">
                            <div class="form-floating mb-3">
                                <input type="number" min="0" max="9999" maxlength="4" data-label="Total Area" name="area" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" id="PropertyArea" placeholder="name@example.com" value="{{ $data['area'] ?? '' }}" required>
                                <label class="text-muted" for="PropertyArea">Total Area</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-4 mb-4">
                            @php
                            $areaUnits = [
                            'feet' => 'Sq. Ft.',
                            'yard' => 'Sq. Yards',
                            'meter' => 'Sq. Meter',
                            ];

                            // Default unit selection logic
                            $defaultUnit = $property->property_type === 'plots' ? 'yard' : 'feet';
                            $selectedUnit = old('area_unit', $data['area_unit'] ?? $defaultUnit);
                            @endphp

                            <div class="form-floating">
                                <select name="area_unit" id="AreaUnit" class="form-select shadow-none border-0 border-bottom rounded-0">
                                    @foreach ($areaUnits as $value => $label)
                                    <option value="{{ $value }}" @selected($selectedUnit===$value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <label for="AreaUnit" class="text-muted">Area Unit</label>
                                <div class="invalid-feedback"></div>
                            </div>

                        </div>
						@if(isset($property->listing_type) && $property->listing_type === 'sale')
                        <div class="col-sm-12 col-lg-12 mb-4">
                            <div class="form-floating">
                                <input type="number" min='0' data-label="Price" name="total_price" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" id="PropertyPrice" placeholder="name@example.com" value="{{ $data['total_price'] ?? '' }}" maxlength="10" 
								max="9999999999" required>
                                <small id="formattedPrice" class="text-muted"></small>
                                <label class="text-muted" for="PropertyArea">Total Price</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
						@elseif(isset($property->listing_type) && $property->listing_type === 'rent')
						 <div class="col-sm-12 col-lg-12 mb-4">
                            <div class="form-floating">
                                <input type="number" min='0' data-label="Price" name="total_price" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" id="PropertyPrice" placeholder="name@example.com" value="{{ $data['total_price'] ?? '' }}" maxlength="10" 
								max="9999999999" required>
                                <small id="formattedPrice" class="text-muted"></small>
                                <label class="text-muted" for="PropertyArea">Monthly Rent</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
						@endif
						
                        @if (!isset($property) || (isset($property) && $property->property_type !== 'plots'))
                        <div class="mb-4">
							<p>Construction Status</p>

							<input type="radio" data-label="Construction Status" class="btn-check" name="construction_status" value="under_construction" id="under_construction" autocomplete="off"
								@checked(isset($data['construction_status']) && $data['construction_status']==='under_construction' )>

							<label class="radio-btn me-2 mb-2 px-4 py-2" for="under_construction">Under Construction</label>

							<input type="radio" data-label="Construction Status" class="btn-check" name="construction_status" value="completed" id="completed" autocomplete="off"
								@checked(isset($data['construction_status']) && $data['construction_status']==='completed' )>

							<label class="radio-btn me-2 mb-3 px-4 py-2" for="completed">Completed</label>
							<div id="possessionDateWrapper" class="mb-4">
								<p>Possession Date</p>
								<input type="date" name="property_details[possession_date]" id="possession_date" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="form-control shadow-none border-0 border-bottom rounded-0" value="{{ @$data->property_details['possession_date'] ?? '' }}">
								<div class="invalid-feedback"></div>
							</div>
							<div class="invalid-feedback"></div>
						</div>

                        @php
                            $furnishings = [
                                ['value' => 'unfurnished', 'label' => 'UnFurnished'],
                                ['value' => 'semi_furnished', 'label' => 'Semi-Furnished'],
                                ['value' => 'fully_furnished', 'label' => 'Fully Furnished'], 
                            ];
                        @endphp
                        
                            <div class="mb-4">
                                <p>Furnishing Type</p>
                                @foreach ($furnishings as $furnishing)
                                    <input type="radio"
                                        data-label="Furnishing Type" 
                                        class="btn-check"
                                        name="furnishing_types"
                                        value="{{ $furnishing['value'] }}"
                                        id="{{ $furnishing['value'] }}"
                                        autocomplete="off"
                                        @if (isset($data['furnishing_types']) && $data['furnishing_types'] === $furnishing['value']) checked @endif
                                    >
                                    <label class="radio-btn me-2 me-md-3 mb-2 px-4 py-2" for="{{ $furnishing['value'] }}">
                                        {{ $furnishing['label'] }}
                                    </label>
                                @endforeach
                                    <div class="invalid-feedback"></div>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn customBtn px-4">Next</button>
                </form>
            </div>
        </div>
    </div>
</div>


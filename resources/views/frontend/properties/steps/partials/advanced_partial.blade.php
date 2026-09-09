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
                <h4 class="mb-4">Step 2: Advanced Details</h4>

                <form id="stepForm" action="{{ route('postproperty.edit.advanced_details.save', $property->property_uid) }}" method="POST" novalidate>
                    @csrf
                    <div class="row">

                        <div class="form-floating mb-3">
                            <input data-label="Property Age" min="0" max="99" maxlength="2" type="number" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" value="{{ $data['property_age'] ?? '' }}"
                                name="advanced_details[property_age]" id="PropertyAge" placeholder="Age of Property" required>
                            <label class="text-muted" for="PropertyAge">Age of Property (Years)</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        @if(isset($property->property_type) && $property->property_type === 'apartment')
                        <div class="mb-4">
                            <p>Bathroom</p>
                            @for ($i = 1; $i <= 6; $i++)
                                <input type="radio" data-label="Bathroom" class="btn-check" name="advanced_details[bathroom]" value="{{ $i }}" id="{{ $i }}bath" autocomplete="off"
                                @if(isset($data['bathroom']) && $data['bathroom']==$i) checked @endif required>
                                <label class="radio-btn me-2 me-mb-3 mb-3 px-4 py-2" for="{{ $i }}bath">{{ $i }}</label>
                                @endfor
                                <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-4">
                            <p>Balcony</p>
                            @for ($i = 0; $i <= 6; $i++)
                                <input type="radio" data-label="Balcony" class="btn-check" name="advanced_details[balcony]" value="{{ $i }}" id="{{ $i }}balcony" autocomplete="off" @if(isset($data['balcony']) && $data['balcony']==$i) checked @endif required>
                                <label class="radio-btn me-2 me-mb-3 mb-3 px-4 py-2" for="{{ $i }}balcony">{{ $i }}</label>
                                @endfor
                                <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-4">
                            <p>Parking</p>
                            @for ($i = 0; $i <= 4; $i++)
                                @php $value=$i @endphp
                                <input type="radio" data-label="Parking" class="btn-check" name="advanced_details[parking]" value="{{ $value }}" id="{{ $value }}parking" autocomplete="off" @if(isset($data['parking']) && $data['parking']==$value) checked @endif required>
                                <label class="radio-btn me-2 me-mb-3 mb-3 px-4 py-2" for="{{ $value }}parking">{{ $value }}</label>
                                @endfor
                                <div class="invalid-feedback"></div>
                        </div>

                        <!--<div class="form-floating mb-4">
                            <input type="text" class="form-control shadow-none border-0 border-bottom rounded-0" value="{{ $data['flat_no'] ?? '' }}" data-label="Flat No"
                                name="advanced_details[flat_no]" id="FlatNo" placeholder="Flat No" required>
                            <label class="text-muted" for="FlatNo">Flat No</label>
                            <div class="invalid-feedback"></div>
                        </div>-->
						<div class="form-floating mb-4">
                            <input type="number" min="0" max="99" maxlength="2" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" value="{{ $data['total_floors'] ?? '' }}" data-label="Total Floors"
                                name="advanced_details[total_floors]" id="TotalFloors" placeholder="Total Floors" required>
                            <label class="text-muted" for="TotalFloors">Total Floors</label>
                            <div class="invalid-feedback"></div> 
                        </div>
                        <div class="form-floating mb-4">
                            <input type="number" min="0" max="99" maxlength="2" class="form-control shadow-none border-0 border-bottom rounded-0 only-numeric" value="{{ $data['floor_no'] ?? '' }}" data-label="Floor No."
                                name="advanced_details[floor_no]" id="FloorNo" placeholder="Floor No." required>
                            <label class="text-muted" for="FloorNo">Floor No.</label>   
                            <div class="invalid-feedback"></div>
                        </div>
                        @elseif(isset($property->property_type) && $property->property_type === 'plots')
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control shadow-none border-0 border-bottom rounded-0" value="{{ $data['plot_no'] ?? '' }}" data-label="Plot No"
                                name="advanced_details[plot_no]" id="PlotNo" placeholder="Plot No" required>
                            <label class="text-muted" for="PlotNo">Plot No</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mb-4 col-6">
                            <input type="text" class="form-control shadow-none border-0 border-bottom rounded-0" value="{{ $data['length'] ?? '' }}" data-label="Plot Length"
                                name="advanced_details[length]" id="PlotLength" placeholder="Plot Length" required>
                            <label class="text-muted" for="PlotLength">Plot Length</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mb-4 col-6">
                            <input type="text" class="form-control shadow-none border-0 border-bottom rounded-0" value="{{ $data['width'] ?? '' }}" data-label="Plot Width"
                                name="advanced_details[width]" id="PlotWidth" placeholder="Plot Width" required>
                            <label class="text-muted" for="PlotWidth">Plot Width</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        @endif
                        @php
                        $facings = ['North','East','West','South','North - East','North - West','South - East','South - West'];
                        @endphp
                        <div class="mb-4">
                            <p>Facing</p>
                            @foreach ($facings as $facing)
                            <input type="radio" class="btn-check" name="advanced_details[facing]" value="{{ $facing }}" id="{{ Str::slug($facing, '_') }}" autocomplete="off" data-label="Facing"
                                @if(isset($data['facing']) && $data['facing']===$facing) checked @endif required>
                            <label class="radio-btn me-2 me-mb-3 mb-3 px-4 py-2" for="{{ Str::slug($facing, '_') }}">{{ $facing }}</label>
                            @endforeach
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-floating mb-4">
                            <textarea oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"  rows="1" name="advanced_details[description]" class="form-control shadow-none border-0 border-bottom rounded-0 overflow-hidden" data-label="Project Name"
                                placeholder="Property Description" rows="4" id="Description" required>{{ $data['description'] ?? '' }}</textarea>
                            <label for="Description">Property Description</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary px-4 me-2 me-mb-3" onclick="history.back()">Back</button>
                    <button type="submit" class="btn customBtn px-4">Next</button>
                </form>

            </div>
        </div>
    </div>
</div>
<script>
    const totalFloorsInput = document.getElementById('TotalFloors');
const floorNoInput = document.getElementById('FloorNo');

function validateFloor() {
    const total = parseInt(totalFloorsInput.value);
    const floor = parseInt(floorNoInput.value);

    if (!isNaN(total)) {
        floorNoInput.setAttribute('max', total); // restrict max input
    }

    if (!isNaN(floor) && !isNaN(total) && floor > total) {
        floorNoInput.setCustomValidity(`Floor No. can't be more than ${total}`);
    } else {
        floorNoInput.setCustomValidity('');
    }
}

totalFloorsInput.addEventListener('input', validateFloor);
floorNoInput.addEventListener('input', validateFloor);

</script>



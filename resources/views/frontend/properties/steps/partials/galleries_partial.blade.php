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
        <div class="card p-4 p-lg-5 border-0 shadow-lg border-secondary-subtle multiStepForm">
            <div class="px-md-4">
                <h4 class="mb-4">Step 5: Galleries</h4>

                <form id="stepForm" action="{{ route('postproperty.edit.galleries.save', $property->property_uid) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="d-flex flex-wrap gap-4 justify-content-between">
                        <!-- Upload from Computer -->
                        <div id="drop-area" class="border-dashed rounded-4 p-4 text-center flex-fill position-relative border-primary"
                            style="min-width:300px; border: 2px dashed #ccc;">
                            <i class="fa-solid fa-cloud-arrow-up fs-3"></i>
                            <p class="text-muted mb-2">Drag and drop here</p>
                            <p class="text-muted mb-2">or</p>
                            <label class="btn btn-outline-primary px-4">
                                Browse Files
                                <input type="file" name="galleries[]" id="galleries" accept="image/*" multiple hidden> 
                            </label>
                        </div>
                    </div>

                    @if (!empty($data))
                    <div class="d-flex flex-wrap gap-3 mt-4" id="existing-images">
                        @foreach ($data as $img)
                        <div class="position-relative" style="width: 120px; height: 120px;">
                            <img src="{{ asset('storage/' . $img) }}" class="w-100 h-100 object-fit-contain border rounded" />
                            <span class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1 small imageX remove-existing"
                                data-path="{{ $img }}" data-property-id="{{ $property->id }}"
                                style="cursor:pointer; z-index:2; transform: translate(50%, -50%);">&times;</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Preview Images -->
                    <div id="preview" class="d-flex flex-wrap gap-3 mt-4"></div>

                    <button type="button" class="btn btn-secondary px-4 me-2 me-mb-3" onclick="history.back()">Back</button>
                    <button type="submit" class="btn customBtn px-4">Next</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Delete existing images --}} 
   <!--<script>
    document.querySelectorAll('.remove-existing').forEach(btn => {
        btn.addEventListener('click', function () {
            const imagePath = this.dataset.path;

            if (confirm('Are you sure you want to delete this image?')) {
                fetch("{{ route('postproperty.image.json.delete') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            image: imagePath,
                            property_id: '{{ $property->id }}',
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.parentElement.remove();
                        } else {
                            alert('Failed to delete image.');
                        }
                    });
            }
        });
    });
</script>
-->



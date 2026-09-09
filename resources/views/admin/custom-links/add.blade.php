@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css">
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
						<form action="{{ route('custom-links.store') }}" method="POST">
							@csrf

							<!-- Title -->
							<div class="mb-3">
								<label for="title" class="form-label">Title </label>
								<input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
								@error('title')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>

							<!-- Slug -->
							<div class="mb-3">
								<label for="slug" class="form-label">Slug</label>
								<input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" required>
								@error('slug')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
							
							<!-- canonical -->
							<div class="mb-3">
								<label for="canonical" class="form-label">Canonical</label>
								<input type="text" name="canonical" id="canonical" class="form-control" value="{{ old('canonical') }}" required>
								@error('canonical')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>


							<!-- keywords -->
							<div class="mb-3">
								<label for="keywords" class="form-label">Keywords</label>
								<input type="text" name="keywords" id="keywords" class="form-control" value="{{ old('keywords') }}" required>
								@error('keywords')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>

							<!-- meta Description -->
							<div class="mb-3">
								<label for="description" class="form-label">Meta Description</label>
								<textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
								@error('description')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
							
							<!-- Description for links -->
							<div class="mb-3">
								<label for="links_description" class="form-label">Links Description</label>
								<textarea name="links_description" class="summernote">{{ old('links_description') }}</textarea>

								@error('links_description')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>

							<!-- Is Active -->
							<div class="mb-3 form-check">
								<input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
								<label for="is_active" class="form-check-label">Active</label>
								@error('is_active')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>

							<!-- Submit -->
							<button type="submit" class="btn btn-primary">Create Link</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('customJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js"></script>

<script>
(function () {
    if ($('.summernote').length && $('.summernote').next('.note-editor').length === 0) {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }
})();
</script>
@endsection
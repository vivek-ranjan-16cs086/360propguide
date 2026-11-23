@extends('admin.app')
@section('title', $title)

@section('content')
<div class="container-fluid">
    {!! $breadcrumbHtml !!}
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card contentCard"><div class="card-body">
        <form method="POST" action="{{ isset($redirect) ? route('redirects.update', $redirect) : route('redirects.store') }}">
            @csrf
            @if (isset($redirect)) @method('PUT') @endif
            <div class="form-group">
                <label for="source_path">Old path</label>
                <input class="form-control" id="source_path" name="source_path" value="{{ old('source_path', $redirect->source_path ?? '') }}" placeholder="/old-page" required>
                <small class="form-text text-muted">Enter the path beginning with /, without the domain or query string.</small>
            </div>
            <div class="form-group">
                <label for="destination_url">Send visitors to</label>
                <input class="form-control" id="destination_url" name="destination_url" value="{{ old('destination_url', $redirect->destination_url ?? '') }}" placeholder="/new-page or https://example.com/page" required>
            </div>
            <div class="form-group">
                <label for="status_code">Redirect type</label>
                <select class="form-control" id="status_code" name="status_code" required>
                    @foreach ([301 => '301 — Permanent', 302 => '302 — Temporary', 307 => '307 — Temporary (keep method)', 308 => '308 — Permanent (keep method)'] as $code => $label)
                        <option value="{{ $code }}" @selected((int) old('status_code', $redirect->status_code ?? 301) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $redirect->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <button class="btn btn-primary" type="submit">{{ isset($redirect) ? 'Save Redirect' : 'Create Redirect' }}</button>
            <a class="btn btn-secondary" href="{{ route('redirects.index') }}">Cancel</a>
        </form>
    </div></div>
</div>
@endsection

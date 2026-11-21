@extends('admin.app')
@section('title', $title)

@section('content')
<div class="container-fluid">
    {!! $breadcrumbHtml !!}
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <div class="card contentCard"><div class="card-body">
        <a class="btn btn-primary float-right mb-3" href="{{ route('redirects.create') }}"><i class="fa fa-plus"></i> Add Redirect</a>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>Source path</th><th>Destination</th><th>Code</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse ($redirects as $redirect)
                    <tr>
                        <td>{{ $redirect->source_path }}</td><td>{{ $redirect->destination_url }}</td><td>{{ $redirect->status_code }}</td>
                        <td>{{ $redirect->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-info" href="{{ route('redirects.edit', $redirect) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('redirects.destroy', $redirect) }}" onsubmit="return confirm('Delete this redirect?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No redirects have been added.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $redirects->links() }}
    </div></div>
</div>
@endsection

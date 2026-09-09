@extends('frontend.layouts.app')

@section('title', 'My Profile')


@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">My Profile</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" type="text" class="form-control"
                                   value="{{ old('name', $user->name) }}">
                        </div>

                        <!-- Phone (required) -->
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input name="phone_number" type="number" class="form-control"
                                   value="{{ old('phone', $user->phone_number) }}" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control"
                                   value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn customBtn" type="submit">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection


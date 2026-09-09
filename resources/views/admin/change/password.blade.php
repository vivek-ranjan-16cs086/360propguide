@extends('admin.app')

@section('title', $title)

@section('customCss')
<link rel="stylesheet" href="{{ url('assets/customs/css/career.css') }}">

<style>
    .password-card {
    max-width: 600px;
    margin: 30px auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.password-card .card-header {
    padding: 18px 24px;
    background: #fff;
    border-bottom: 1px solid #e9ecef;
}

.password-card .card-header h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #222;
}

.password-card .card-body {
    padding: 25px 24px 28px;
}

.password-card .mb-3 {
    margin-bottom: 20px !important;
}

.password-card .form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.password-card .form-control {
    width: 100%;
    height: 44px;
    padding: 10px 13px;
    border: 1px solid #d8dde3;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    background: #fff;
    box-shadow: none;
    transition: all 0.2s ease;
}

.password-card .form-control::placeholder {
    color: #9aa0a6;
}

.password-card .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    outline: none;
}

.password-btn {
    min-width: 150px;
    padding: 10px 22px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.password-btn:hover {
    transform: translateY(-1px);
}

.password-btn:active {
    transform: translateY(0);
}

@media (max-width: 767px) {
    .password-card {
        margin: 20px 10px;
    }

    .password-card .card-body {
        padding: 20px;
    }

    .password-card .card-header {
        padding: 16px 20px;
    }

    .password-card .card-header h4 {
        font-size: 18px;
    }

    .password-btn {
        width: 100%;
    }
}
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-md-12 mt-4">

         

          

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="contentCard projects">

                <div class="card password-card">

                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>

                    <div class="card-body">
                     <form action="{{ route('password') }}" method="POST">
             @csrf

    {{-- Old Password --}}
    <div class="mb-3">
        <label for="old_password" class="form-label">
            Old Password
        </label>

        <input
            type="password"
            name="old_password"
            id="old_password"
            class="form-control"
            placeholder="Enter old password"
            required
        >

        @error('old_password')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- New Password --}}
    <div class="mb-3">
        <label for="new_password" class="form-label">
            New Password
        </label>

        <input
            type="password"
            name="new_password"
            id="new_password"
            class="form-control"
            placeholder="Enter new password"
            required
        >

        @error('new_password')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- Confirm Password --}}
    <div class="mb-3">
        <label for="new_password_confirmation" class="form-label">
            Confirm New Password
        </label>

        <input
            type="password"
            name="new_password_confirmation"
            id="new_password_confirmation"
            class="form-control"
            placeholder="Confirm new password"
            required
        >

        @error('new_password_confirmation')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>



    <div class="mt-4">
        <button type="submit" class="btn btn-primary password-btn">
            Change Password
        </button>
    </div>

</form>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
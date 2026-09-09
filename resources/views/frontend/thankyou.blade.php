@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center py-4 py-md-5">
        <div class="col-md-6 py-4 py-md-5">
            <div class="card my-5">
                <div class="card-body p-3 p-md-5 shadow-lg text-center">
                    <i class="fa-solid fa-circle-check display-3 mb-4 text-primary"></i>

                    <div class="h3">
                        @php
                            $form = session('form');
                        @endphp

                        @if(in_array($form, ['form1', 'form2', 'form3', 'form4']))
                            <p>Thank you for sharing your details! Our representative will reach out to you soon to discuss your requirements!</p>
                        @elseif($form === 'popup')
                            @if(session('download_file'))
                                <p>Thank you for sharing your details! Our representative will reach out to you soon to discuss your requirements!</p>

                                <script>
                                    window.addEventListener('DOMContentLoaded', () => {
                                        let a = document.createElement('a');
                                        a.href = "{{ asset('storage/' . session('download_file')) }}";
                                        a.download = "";
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                    });
                                </script>
                            @elseif(session('message'))
                                <p>{{ session('message') }}</p>
                            @else
                                <p>Thank you for sharing your details! Our representative will reach out to you soon to discuss your requirements!</p>
                            @endif
                        @else
                            <p>Thank you for sharing your details! Our representative will reach out to you soon to discuss your requirements!</p>
                        @endif
                    </div>

                    <p>We'll get in touch with you shortly.</p>
                    <a class="btn customBtn px-3 shadow-lg" href="{{ url('/') }}">Go to Home Page</a>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    session()->forget(['form', 'download_file', 'message']);
@endphp

@endsection

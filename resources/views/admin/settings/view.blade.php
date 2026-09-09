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
            <div class="contentCard projects">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{route('credentials.store')}}" enctype="multipart/form-data">
                            @csrf
                            <h4>YouTube Credentials Section</h4>
                            <div class="row">
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="api_key">API Key</label>
                                    <input class="form-control input @error('api_key') is-invalid @enderror " type="text"
                                        placeholder="Enter API Key" value="{{@$credentials->api_key}}" name="api_key" required>
                                </div>
                                <div class="col-sm-12 col-lg-6 mb-4">
                                    <label for="channel_id">Channel ID</label>
                                    <input class="form-control input @error('channel_id') is-invalid @enderror "
                                        type="text" placeholder="Enter Channel ID" value="{{@$credentials->channel_id}}"
                                         name="channel_id" required>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Save Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

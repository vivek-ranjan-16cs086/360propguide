@extends('admin.app')
@section('title', $title)

@section('content')

<div class="container-fluid">
	<div class="row">
		{!! $breadcrumbHtml !!}
		<div class="card contentCard">
			<div class="card-body">
				<form method="POST" action="{{route('queries.reply')}}">
						@csrf
						<input name="id" value="{{request()->route('id')}}" type="hidden">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="source">Name</label>
									<input type="text" class="form-control input @error('name') is-invalid @enderror" name="name" placeholder="Name" value="{{$query->name}}">
								</div>
								<div class="form-group">
									<label for="source">Mobile</label>
									<input type="text" class="form-control input @error('mobile') is-invalid @enderror" name="mobile" placeholder="Mobile No." value="{{$query->mobile}}">
								</div>
								<div class="form-group">
									<label for="email">Email:</label>
									<input type="text" class="form-control input @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{$query->email}}">
								</div>
								<div class="form-group">
									<label for="message">Message:</label>
									<textarea name="message" rows="5" id="" class="form-control input @error('message') is-invalid @enderror">
									 {{$query->message}}
									</textarea>
								</div><br>
							</div>
							<div class="col-md-6">
								 <div class="form-group">
									<label for="message">Reply:</label>
									<textarea name="reply" rows="5" id="" class="form-control input @error('reply') is-invalid @enderror">

									</textarea>
								</div>
							</div>
							<div class="col-12">
							<button type="submit" class="btn btn-primary">Reply Now</button>
							</div>
						</div>
                    </form>
			</div>
		</div>
	</div>
</div>
@endsection
@section('customJs')

@endsection








<div class="col-12 page_title d-flex justify-content-between">
	<h2>{{$title}}</h2>
	<div>
		<ul class="default-breadcrumb">
			@if(!empty($breadcrumbs) && count($breadcrumbs)>0)
				@foreach($breadcrumbs as $key => $data)
					<li class="crumb {{($key === 'javascript:void(0);') ? 'active' :'inactiveBreadcrumb'}}">
						<div class="link">
							<a href="{{($key !== 'javascript:void(0);') ? route($key) : $key}}">{{$data}}</a>
						</div>
					</li>
				@endforeach
			@endif
		</ul>
	</div>
</div> 

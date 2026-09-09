@extends('admin.app')
@section('title', $title)
@section('customCss')
<link rel="stylesheet" href="{{url('assets/customs/css/career.css')}}">
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		{!! $breadcrumbHtml !!}
		<div class="card contentCard">
			<div class="card-body table-responsive">
				<div class="row mb-3">
					<div class="col-md-4">
						<input type="text" id="date_range" class="form-control" placeholder="Select date range">
					</div>
				</div>
				<table class="table table-striped table-bordered table_data dataex-html5-export dataTable no-footer" id="table_data" role="grid">
					<a class="btn btn-primary pt-2 float-right" href="{{route('locations.add')}}">
						<span class="fa fa-plus"></span> Add New
					</a>
					<thead>
						<tr>
							<th style="min-width:40px">SI No.</th>
							<th>Name</th>
							<th>Type</th>
							<th>Parent Location</th>
							<th>State</th>
							<th>Status</th>
							<th>Uploaded At</th>
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('customJs')
<script>
    $(document).ready(function(){
       loadAjaxList();
	   let from_date = '';
        let to_date = '';

		$('#date_range').daterangepicker({
			locale: {
				format: 'YYYY-MM-DD'
			},
			opens: 'left',
			showDropdowns: true,
			alwaysShowCalendars: true,
			singleDatePicker: false,
			autoUpdateInput: false,
			linkedCalendars: false,
		}, function(start, end) {
			from_date = start.format('YYYY-MM-DD');
			to_date = end.format('YYYY-MM-DD');
			$('#date_range').val(from_date + ' to ' + to_date);
			loadAjaxList(from_date, to_date);
		});
    });
    function loadAjaxList(from_date = '', to_date = ''){
         $("#table_data").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            sortable: true,
            pagination: true,
            dom: 'lBfrtip',
            lengthMenu: [
                [ 10, 25, 50, 100, 500],
                [ '10 rows', '25 rows', '50 rows', '100 rows', '500 rows' ]
            ],
            buttons: [
                'pageLength',
                'colvis'
            ],
            ajax: {
				url: "{{ route('locations.ajax-list') }}",
				type: 'GET',
				data: function(d) {
					d.from_date = from_date;
					d.to_date = to_date;
				}
            },
            columns: [
                { data: 'id' },
                { data: 'city' },
                { data: 'type' },
                { data: 'parent' },
                { data: 'state' },
                { data: 'status' },
				{ data: 'uploaded_at' },
                { data: 'action' }
            ],
            "language":{
                "search":"<i class='fa fa-search'></i>",
                "searchPlaceholder":" Search locations...",
            }
        });
    }
</script>
@endsection

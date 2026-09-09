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
				<!--begin: Datatable -->
				<div class="row mb-3">
					<div class="col-md-4">
						<input type="text" id="date_range" class="form-control" placeholder="Select date range">
					</div>
				</div>
				<table class="table table-striped table-bordered table_data dataex-html5-export dataTable no-footer" id="table_data" role="grid" aria-describedby="table_data_info">
					<a class="btn btn-primary pt-2 float-right" href="{{route('projects.add')}}" target="_blank">
						<span class="fa fa-plus"></span> Add New
					</a>
					<thead class="">
						<tr>
							<th>SI No.</th>
							<th>Project Name</th>
							<th style="min-width:200px;">Hero Images</th>
							<th style="min-width:200px">Status</th>
							<th style="min-width:200px">BSP Price</th>
							<th style="min-width:100px">Uploaded At</th>
							<th style="min-width:100px">Location</th>
							<th style="min-width:100px">Cities</th>
							<th style="min-width:80px">Action</th>
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
			singleCalendar: true,
			autoUpdateInput: false,
			linkedCalendars: false, // This is the key to force single month
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
            type:'get',
            serverSide: true,
            destroy: true,
            sortable: true,
            pagination: true,
            dom: 'lBfrtip',
            lengthMenu: [
                [ 10, 25, 50, 100, 500, 1000, 1500],
                [ '10 rows', '25 rows', '50 rows', '100 rows', '500 rows', '1000 rows', '1500 rows' ]
            ],
            buttons: [
                'pageLength'
            ],
            buttons: [
                {
                    extend: 'copyHtml5',
                    footer: true,
                    title: "{{env('APP_NAME')}}",
                    exportOptions: {
                        columns: [0,1,2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: "{{env('APP_NAME')}}",
                    exportOptions: {
                        columns: [0,1,2]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: "{{env('APP_NAME')}}",
                    exportOptions: {
                        columns: [0,1,2]
                    }
                },
                {
                    extend: 'print',
                    footer: true,
                    title: "{{env('APP_NAME')}}",
                    exportOptions: {
                        columns: [0,1,2]
                    }
                },
                {
                    extend: 'csv',
                    footer: true,
                    title: "{{env('APP_NAME')}}",
                    exportOptions: {
                        columns: [0,1,2]
                    }
                },
                'colvis'
            ],
            ajax: {
				url: "{{ route('projects.ajax-list') }}",
				type: 'GET',
				data: function(d) {
					d.from_date = from_date;
					d.to_date = to_date;
				}
            },
            columns: [
                { data: 'id' },
                { data: 'project_name' },
                { data: 'hero_images' },
                { data: 'status' },
				{ data: 'sqft_price' },
				{ data: 'uploaded_at' },
				{ data: 'location' },
				{ data: 'cities' },
                { data: 'action' }
            ],
            "language":{
                "search":"<i class='fa fa-search'></i>",
                "searchPlaceholder":" Search projects...",
            }
        });
    }
</script>
@endsection







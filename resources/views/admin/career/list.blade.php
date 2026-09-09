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
				<table class="table table-striped table-bordered table_data dataex-html5-export dataTable no-footer" id="table_data" role="grid" aria-describedby="table_data_info">
					<a class="btn btn-primary pt-2 float-right" href="{{route('career.add')}}">
						<span class="fa fa-plus"></span> Add New
					</a>
					<thead class="">
						<tr>
							<th>SI No.</th>
							<th>Position</th>
							<th style="min-width:200px;">Experience</th>
							<th style="min-width:200px">Status</th>
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
    });
    function loadAjaxList(){
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
            ajax: "{{route('career.ajax-list')}}",
            columns: [
                { data: 'id' },
                { data: 'position' },
                { data: 'experience' },
                { data: 'status' },
                { data: 'action' }
            ],
            "language":{
                "search":"<i class='fa fa-search'></i>",
                "searchPlaceholder":" Search Jobs...",
            }
        });
    }
</script>
@endsection

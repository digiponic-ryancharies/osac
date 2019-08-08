<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')

@section('styles')

@endsection

@section('content')

<div class="box">
	<div class="box-header">
		<div class="row">
			<div class="col-lg-10 col-md-4 col-sm-12">
				<div class="box-title">
					Tabel Insentif
				</div>
			</div>
			<div class="col-lg-2 col-md-4 col-sm-12 pull-right">		
				<div class="form-group">
					<div class="input-group date">
						<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control" id="month">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-lg-12">
				<table id='table' class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>Karyawan</th>
							<!-- <th>Bulan</th> -->
							<th>Insentif</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>

</div>

@endsection

@section('scripts')
<script>

	$(function () {

		var month = moment().format('MMM YYYY');		
		$("#month").val(month);
		$("#month").datepicker({
			format: "M yyyy",
			startView: "months", 
			minViewMode: "months"
		}).on('changeDate', function(data){			
			var _month =  moment(data.date).format('M');
			var _year = moment(data.date).format('YYYY');

			var _url = 'insentif-datatable?month='+_month+'&year='+_year;
			var _base_url = '{!! CRUDBooster::mainpath("' + _url + '") !!}';

			table.ajax.url(_base_url).load();			
		});	

		
		var table = $('#table').DataTable({
			processing: true,
			serverSide: true,
			ajax: '{!! CRUDBooster::mainpath("insentif-datatable") !!}',
			data: {
				month: moment().format('M'),
				year: moment().format('YYYY')
			},
			columns: [{
					data: 'DT_Row_Index',
					name: 'DT_Row_Index'
				},
				{
					data: 'nama_karyawan',
					name: 'nama_karyawan'
				},
				// {
				// 	data: '_bulan',
				// 	name: '_bulan'
				// },
				{
					data: '_insentif',
					name: '_insentif'
				}
			]
		});

	});
</script>
@endsection
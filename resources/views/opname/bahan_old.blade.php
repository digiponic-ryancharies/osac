<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')

@section('styles')

@endsection

@section('content')

<div class="panel panel-default">
	<div class="panel-heading">
		<strong><i class='fa fa-file-o'></i> Form Opname Stok</strong>
	</div>

	<div class="panel-body" style="padding:20px 0px 0px 0px">
		<form class='form-horizontal' method='post' id="form" action='opname'>
			{{ csrf_field() }}			
			<div class="box-body" id="parent-form-area">
				<div class='form-group header-group-0'>
					<label class='control-label col-sm-2'>
						Status
					</label>
					<div class="col-sm-10">
						<label class="radio-inline"><input type="radio" name="status" checked>Masuk</label>
						<label class="radio-inline"><input type="radio" name="status">Keluar</label>
					</div>
				</div>
				<div class='form-group header-group-0'>
					<label class='control-label col-sm-2'>Bahan</label>
					<div class="col-sm-10">
						<select name="id_bahan" id="id_bahan" class="form-control select2-ajax" required></select>						
					</div>
				</div>
				<div class='form-group header-group-0 '>
					<label class='control-label col-sm-2'>Qty</label>
					<div class="col-sm-10">
						<input type="number" name="stok" id="stok" class='form-control'>
					</div>
				</div>
				<div class='form-group header-group-0 '>
					<label class='control-label col-sm-2'>Satuan</label>
					<div class="col-sm-10">
						<input type="text" id="satuan" class='form-control' readonly>
					</div>
				</div>
				<div class='form-group header-group-0 '>
					<label class='control-label col-sm-2'>Qty</label>
					<div class="col-sm-10">
						<input type="number" name="qty" id="qty" class='form-control'>
					</div>
				</div>
				<div class='form-group header-group-0 '>
					<label class='control-label col-sm-2'>Keterangan</label>
					<div class="col-sm-10">
						<input type="text" name="keterangan" id="keterangan" class='form-control' required>
					</div>
				</div>
			</div><!-- /.box-body -->

			<div class="box-footer" style="background: #F5F5F5">

				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<a href='http://localhost/digiponic/osac/public/admin/tb_pelanggan' class='btn btn-default'><i
								class='fa fa-chevron-circle-left'></i> Kembali</a>

						<input type="submit" name="submit" value='Simpan &amp; Tambah Lagi' class='btn btn-success'>

						<input type="submit" name="submit" value='Simpan' class='btn btn-success'>

					</div>
				</div>


			</div><!-- /.box-footer-->

		</form>

	</div>
</div>

@endsection

@section('scripts')
<script>
	$(function () {

		var produk = $('.select2-ajax');
		produk.select2({
			ajax: {
				type: 'GET',
				dataType: 'JSON',
				url: '{{ CRUDBooster::apipath("bahan/search") }}',
				data: function (params) {
					var query = {
						kode: params.term,
					}
					return query;
				},
				processResults: function (data) {
					console.log(data);
					return {
						results: $.map(data, function (item) {
							return {
								text: item.kode + ' - ' + item.keterangan,
								id: item.id,
								object: item
							}
						})
					}
				}
			},
			minimumInputLength: 1,
			placeholder: 'Masukkan kode bahan',
		});

		produk.on('select2:select', function (e) {
			var obj = e.params.data.object;			
			$('#satuan').val(obj.satuan);
			$('#stok').val(obj.stok);
			$('#stok_akhir').val(obj.stok);
			$('.btn-simpan').prop('disabled', false);
		});

		$('#stok_masuk').keyup(function () {
			var awal = $('#stok').val();
			var x = $('#stok_keluar').val();
			var akhir = parseInt(awal) + parseInt($(this).val()) - parseInt(x);
			$('#stok_akhir').val(akhir);
		});

		$('#stok_keluar').keyup(function () {
			var awal = $('#stok').val();
			var x = $('#stok_masuk').val();
			var akhir = parseInt(awal) - parseInt($(this).val()) + parseInt(x);
			$('#stok_akhir').val(akhir);
		});

	});
</script>
@endsection
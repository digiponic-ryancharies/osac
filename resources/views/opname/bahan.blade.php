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
					Form
				</div>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-lg-12">
				<form id="form" action="opname" method="post">
					{{ csrf_field() }}
					<div class="form-group">
						<label for="kode">Kode Produk</label>
						{{-- <input type="text" name="kode" id="kode" class="form-control"> --}}
						<select name="id" id="id" class="form-control select2 select2-ajax"></select>
					</div>
					<div class="form-group">
						<label for="stok">Stok</label>
						<input type="text" name="stok" id="stok" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="keterangan">Satuan</label>
						<input type="text" id="keterangan" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="stok_masuk">Stok Masuk</label>
						<input type="number" name="stok_masuk" id="stok_masuk" class="form-control" value="0">
					</div>
					<div class="form-group">
						<label for="stok_keluar">Stok Keluar</label>
						<input type="number" name="stok_keluar" id="stok_keluar" class="form-control" value="0">
					</div>
					<div class="form-group">
						<label for="stok_akhir">Stok Akhir</label>
						<input type="number" name="stok_akhir" id="stok_akhir" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label for="stok">Keterangan</label>
						<input type="text" name="keterangan" id="keterangan" class="form-control">
					</div>					
					<div class="box-footer">
						<button type="reset" class="btn btn-danger">Reset</button>
						<button type="submit" class="btn btn-primary btn-simpan pull-right" disabled>Simpan</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>

@endsection

@section('scripts')
<script>

	$(function () {

		var produk = $('#id');
		produk.select2({					
			ajax: {
				type: 'GET',
				dataType: 'JSON',
				url: '{{ CRUDBooster::apipath("bahan/search") }}',				
				data: function(params){
					var query = {
						kode: params.term,
					}
					return query;
				},
				processResults: function(data){
					console.log(data);
					return {
						results: $.map(data, function(item){
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
			placeholder: 'Masukkan kode / barcode bahan',
		});		

		produk.on('select2:select', function(e){			
			var obj = e.params.data.object;
			$('#keterangan').val(obj.satuan);
			$('#stok').val(obj.stok);
			$('#stok_akhir').val(obj.stok);
			$('.btn-simpan').prop('disabled', false);
		});

		$('#stok_masuk').keyup(function(){
			var awal = $('#stok').val();
			var x = $('#stok_keluar').val();
			var akhir = parseInt(awal) + parseInt($(this).val()) - parseInt(x);
			$('#stok_akhir').val(akhir);
		});
		
		$('#stok_keluar').keyup(function(){
			var awal = $('#stok').val();
			var x = $('#stok_masuk').val();
			var akhir = parseInt(awal) - parseInt($(this).val()) + parseInt(x);
			$('#stok_akhir').val(akhir);
		});		

	});
</script>
@endsection
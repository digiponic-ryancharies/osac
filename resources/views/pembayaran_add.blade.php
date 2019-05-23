@extends("crudbooster::admin_template")
@section("content")
   <link rel='stylesheet' href='{{ asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css") }}'/>
   <script src='{{ asset("vendor/crudbooster/assets/select2/dist/js/select2.full.min.js") }}'></script>
   <script src='{{ url("/") }}/js/recta.js'></script>
   {{-- <script src="https://cdn.jsdelivr.net/npm/recta/dist/recta.js"></script> --}}
   <style type="text/css">
   	.select2-container--default .select2-selection--single {border-radius: 0px !important}
           .select2-container .select2-selection--single {height: 35px}
           .select2-container--default .select2-selection--multiple .select2-selection__choice {
             background-color: #3c8dbc !important;
             border-color: #367fa9 !important;
             color: #fff !important;
           }
           .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
             color: #fff !important;
           }
   </style>
   <script type="text/javascript">
      $(function() {

         var printer = new Recta('20112017', '1811');

         var faktur = '';
         var sumberair = '';
         var rekening = '';
         var nama = '';
         var meteranlalu = '';
         var meteransekarang = '';
         var volume = '';
         var denda = '';
         var total = '';

         $('.select2').select2();

         $('#btnCek').click(function() {
            rekening = $('#pelanggan').val();
            bulan = $('#bulan').val();
            $.ajax({
               url: '{{ url("/") }}/apipamdesa/api/tagihan/'+rekening+'/'+bulan,
               method: 'get',
               success: function(data){
                  console.log('Data: ', data);

                  faktur = data.faktur;
                  sumberair = data.sumberair;
                  rekening = data.rekening;
                  nama = data.nama;
                  meteranlalu = data.meteran_bl;
                  meteransekarang = data.meteran_bi;
                  volume = data.volume;
                  denda = data.totaldenda;
                  total = data.total;

                  $('#status').val(data.cek);
                  $('#debit_air').val(data.volume);
                  $('#denda').val(data.denda);
                  $('#bayar').val(data.bayar);
                  $('#btnSave').prop('disabled',data.status);
               }
            });
         });

         $('#form').submit(function(event) {
            bulan = $('#bulan option:selected').text();
            rekening = $('#pelanggan option:selected').data('rekening');
            pelanggan = $('#pelanggan option:selected').data('pelanggan');

            printer.open().then(function () {
               printer.align('left')
                  .raw('\n')
                  .raw('\n')
                  .text('STRUK PEMBAYARAN AIR DESA TAWANGSARI - GONDANGLEGI')
                  .raw('\n')
                  .text('{{ str_pad("FAKTUR",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + faktur).raw('\n')
                  .text('{{ str_pad("SUMBER AIR",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + sumberair)
                  .text('{{ str_pad("NO REKENING",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + rekening)
                  .text('{{ str_pad("NAMA",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + nama)
                  .text('{{ str_pad("PERIODE BLN",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + bulan + ' {{ date("Y") }}')
                  .text('{{ str_pad("METERAN LALU",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + meteranlalu)
                  .text('{{ str_pad("METERAN SEKARANG",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + meteransekarang)
                  .text('{{ str_pad("PENGGUNAAN AIR",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + volume + ' m3')
                  .text('{{ str_pad("DENDA",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + 'Rp. ' + denda)
                  .text('{{ str_pad("TOTAL TAGIHAN",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH) }}' + 'Rp. ' + total)
                  .text('{{ str_pad("ADMIN",20," ",STR_PAD_RIGHT).str_pad(":",5," ",STR_PAD_BOTH).CRUDBooster::myName() }}')
                  .raw('\n')
                  .align('left')
                  .align('center')
                  .raw('\n')
                  .text('TOKO BUMDES - TAWANG GROSSMART')
                  .text('MENYATAKAN STRUK INI SEBAGAI BUKTI PEMBAYARAN YANG SAH')
                  .text('MOHON DI SIMPAN DENGAN BAIK')
                  .raw('\n')
                  .text('Terima kasih atas kepercayaan Anda membayar melalui loket kami')
                  .raw('\n')
                  .raw('\n')
                  .align('left')
                  .text('TOKO BUMDES - TAWANG GROSSMART')
                  .text('Jalan Raya Ketawang Desa Ketawang')
                  .text('Kecamatan Gondanglegi, Kabupaten Malang')
                  .feed(4)
                  .print()
            });
         });

      });
   </script>
   <div class="panel panel-default">
      <div class="panel-heading">
         Tambah Pembayaran
      </div>
      <div class="panel-body">
         <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{ CRUDBooster::mainpath('add-save') }}'>
            {{ csrf_field() }}
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Pelanggan <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-10">
                  <select class="form-control select2" name="rekening" id="pelanggan" required>
                     <option value="">Silahkan pilih pelanggan</option>
                     @foreach ($pelanggan as $pel)
                        <option value="{{ $pel->id }}" data-rekening="{{ $pel->rekening }}" data-pelanggan="{{ $pel->nama }}">{{ $pel->rekening }} - {{ $pel->nama }}</option>
                     @endforeach
                  </select>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Bulan <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-8">
                  <select class="form-control select2" name="bulan" id="bulan" required>
                     <option value="">Silahkan pilih bulan</option>
                     @foreach ($bulan as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                     @endforeach
                  </select>
               </div>
               <div class="col-sm-2">
                  <button type="button" name="button" class="btn btn-danger" id="btnCek">Cek</button>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Status <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-10">
                  <input type='text' id="status" required readonly  maxlength=255 class='form-control'>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Debit Air <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-10">
                  <input class="form-control" type="text" name="debit_air" id="debit_air" readonly>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Total Denda <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-10">
                  <input class="form-control" type="number" id="denda" name="denda" readonly>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-2">
                  Total Bayar <span class="text-danger" title='This field is required'>*</span>
               </label>
               <div class="col-sm-10">
                  <input class="form-control" type="number" id="bayar" name="bayar" readonly>
               </div>
            </div>
            <div class="box-footer">
               <div class="form-group">
                  <label class="control-label col-sm-2"></label>
                  <div class="col-sm-10">
                    <input type="submit" name="submit" id="btnSave" value='Save' disabled class='btn btn-success'>
                    {{-- <input type="button" name="print" id="btnPrint" value='Print' class='btn btn-primary'> --}}
                  </div>
               </div>
            </div><!-- /.box-footer-->
         </form>

      </div>
   </div>


@endsection

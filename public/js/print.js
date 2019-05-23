$(function(){
   var printer = new Recta('20112017', '1811');

   var bulan = $('#bulan option:selected').text();
   var rekening = $('#pelanggan option:selected').data('rekening');
   var pelanggan = $('#pelanggan option:selected').data('pelanggan');

   $('.print').click(function(ev){
      var url = $(this).attr('href');
      ev.preventDefault();
      swal({
          title: 'Apakah anda yakin ?',
          text: 'Anda akan mencetak ulang struk pembayaran ini!',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ff0000',
          confirmButtonText: 'Ya',
          cancelButtonText: 'Tidak',
          closeOnConfirm: false
      }, function(){
            $.ajax({
               url: url,
               method: 'get',
               success: function(res){
                  var faktur            = res.faktur;
                  var sumberair         = res.sumberair;
                  var rekening          = res.rekening;
                  var nama              = res.nama;
                  var periode           = res.periode;
                  var meteranlalu       = res.meteran_bl;
                  var meteransekarang   = res.meteran_bi;
                  var penggunaanair     = res.penggunaanair;
                  var denda             = res.totaldenda;
                  var total             = res.total;
                  var admin             = res.admin;

                  printer.open().then(function () {
                     printer.align('left')
                        .raw('\n')
                        .raw('\n')
                        .text('STRUK PEMBAYARAN AIR DESA TAWANGSARI - GONDANGLEGI')
                        .raw('\n')
                        .text(faktur).raw('\n')
                        .text(sumberair)
                        .text(rekening)
                        .text(nama)
                        .text(periode)
                        .text(meteranlalu)
                        .text(meteransekarang)
                        .text(penggunaanair)
                        .text(denda)
                        .text(total)
                        .text(admin)
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
                  swal.close();
               }
            });
      });
   });
})

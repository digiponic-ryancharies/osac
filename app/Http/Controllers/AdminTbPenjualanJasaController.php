<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use Image;
	use DB;
	use CRUDBooster;

	use Mike42\Escpos\Printer;
	use Mike42\Escpos\EscposImage;
	use Mike42\Escpos\CapabilityProfile;
	use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

	class AdminTbPenjualanJasaController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "tb_penjualan_jasa";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Penjualan","name"=>"status_penjualan","join"=>"tb_general,keterangan"];
			$this->col[] = ["label"=>"Kode","name"=>"kode"];
			$this->col[] = ["label"=>"Tgl Pesan","name"=>"tanggal","callback"=>function($row){
				return date('d-m-y | H:i',strtotime($row->tanggal));
			}];
			$this->col[] = ["label"=>"Tgl Masuk","name"=>"tanggal_masuk","callback"=>function($row){
				if(empty($row->tanggal_masuk))
					$tgl = '-';
				else
					$tgl = date('d-m-y | H:i',strtotime($row->tanggal_masuk));
				return $tgl;
			}];
			// $this->col[] = ["label"=>"Tanggal","name"=>"tanggal","visible"=>false];
			// $this->col[] = ["label"=>"Tanggal","name"=>"tanggal_masuk","callback"=>function($row){
			// 	return "I: &emsp;".date('d F Y H:i',strtotime($row->tanggal))."</br> O: &emsp;".date('d F Y H:i',strtotime($row->tanggal_masuk));
			// }];
			$this->col[] = ["label"=>"Pelanggan","name"=>"nama_pelanggan"];
			// $this->col[] = ["label"=>"Merek Kendaraan","name"=>"merek_kendaraan"];
			$this->col[] = ["label"=>"Kendaraan","name"=>"nama_kendaraan"];
			$this->col[] = ["label"=>"Pembayaran","name"=>"status_pembayaran","join"=>"tb_general,keterangan"];
			$this->col[] = ["label"=>"Total","name"=>"total",'callback_php'=>'number_format($row->total,0,",",".")'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('tb_penjualan_jasa')->whereDate('created_at',date('Y-m-d'))->count('id') + 1;
			$kode = 'REG'.date('dmy').''.str_pad($kode,5,0,STR_PAD_LEFT);
			$date = date('Y-m-d H:i:s');

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'','name'=>'kode','type'=>'hidden','value'=>$kode];			
			$this->form[] = ['label'=>'','name'=>'tanggal','type'=>'hidden','value'=>$date];			
			$this->form[] = ['label'=>'','name'=>'tanggal_masuk','type'=>'hidden','value'=>$date];			
			$this->form[] = ['label'=>'Nomor Polisi','name'=>'nomor_polisi','type'=>'text','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Pelanggan','name'=>'id_pelanggan','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_pelanggan,nama'];						
			// $this->form[] = ['label'=>'Pelanggan','name'=>'id_pelanggan','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_pelanggan,nama','datatable_ajax'=>true];						
			$this->form[] = ['label'=>'Kendaraan','name'=>'id_kendaraan','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'tb_kendaraan,keterangan'];			
			// $this->form[] = ['label'=>'Kendaraan','name'=>'id_kendaraan','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'tb_kendaraan,keterangan','datatable_ajax'=>true];			
			$columns[] = ['label'=>'Jasa','name'=>'id_jasa','type'=>'datamodal','required'=>true,'datamodal_table'=>'tb_jasa','datamodal_columns'=>'keterangan','datamodal_columns_alias'=>'Jasa','datamodal_size'=>'large'];
			$columns[] = ['label'=>'Harga','name'=>'harga','type'=>'number','readonly'=>true,'required'=>true];
			// $columns[] = ['label'=>'Qty','name'=>'quantity','type'=>'number','required'=>true];
			// $columns[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','dataenum'=>'0|Nominal;1|Persen'];
			// $columns[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'number'];
			// $columns[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'number','required'=>true,'formula'=>"[quantity] * [harga]","readonly"=>true];
			$this->form[] = ['label'=>'Detail Jasa','name'=>'penjualan_jasa_detail','type'=>'child','columns'=>$columns,'table'=>'tb_penjualan_jasa_detail','foreign_key'=>'id_penjualan_jasa'];
			$this->form[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'number','width'=>'col-sm-10','readonly'=>true,'value'=>0];
			$this->form[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 14','value'=>35];
			$this->form[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10','value'=>0];
			$this->form[] = ['label'=>'Total','name'=>'total','type'=>'number','width'=>'col-sm-10','readonly'=>true,'value'=>0];
			$this->form[] = ['label'=>'Status','name'=>'status_pembayaran','validation'=>'required','type'=>'radio','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 11','value'=>25];
			$this->form[] = ['label'=>'Metode Pembayaran','name'=>'metode_pembayaran','validation'=>'','type'=>'radio','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 12'];
			$this->form[] = ['label'=>'Merchant','name'=>'id_merchant','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 13'];
			$this->form[] = ['label'=>'No Kartu','name'=>'nomor_kartu','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Kode Trace','name'=>'kode_trace','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Bayar','name'=>'bayar','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Kembalian','name'=>'kembalian','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10','readonly'=>true];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Kode","name"=>"kode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tanggal","name"=>"tanggal","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Pelanggan","name"=>"id_pelanggan","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"pelanggan,id"];
			//$this->form[] = ["label"=>"Nama Pelanggan","name"=>"nama_pelanggan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Merek Kendaraan","name"=>"id_merek_kendaraan","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"merek_kendaraan,id"];
			//$this->form[] = ["label"=>"Merek Kendaraan","name"=>"merek_kendaraan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Kendaraan","name"=>"id_kendaraan","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"kendaraan,id"];
			//$this->form[] = ["label"=>"Nama Kendaraan","name"=>"nama_kendaraan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Nomor Polisi","name"=>"nomor_polisi","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Subtotal","name"=>"subtotal","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Diskon Tipe","name"=>"diskon_tipe","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Diskon Nominal","name"=>"diskon_nominal","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Total","name"=>"total","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Total Durasi","name"=>"total_durasi","type"=>"time","required"=>TRUE,"validation"=>"required|date_format:H:i:s"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Deleted By","name"=>"deleted_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();
			$this->addaction[] = ['title'=>'Print Struk','icon'=>'fa fa-print','color'=>'danger print','url'=>CRUDBooster::mainpath('print-struk').'/[id]'];

	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();			


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          
			$this->table_row_color[] = ["condition"=>"[status_penjualan] == 28","color"=>"success"];
			$this->table_row_color[] = ["condition"=>"[status_pembayaran] == 25","color"=>"danger"];
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
			$_omset = DB::table('tb_penjualan_jasa')->whereDate('tanggal',date('Y-m-d'))->where('status_pembayaran',26);;
			if(!CRUDBooster::isSuperadmin()) $_omset->where('id_cabang', CRUDBooster::myCabangId());			
			$x = $_omset->sum('total');
			$omset_h = number_format($x,0,',','.');
			
			$_omset = DB::table('tb_penjualan_jasa')->whereMonth('tanggal',date('m'))->where('status_pembayaran',26);;
			if(!CRUDBooster::isSuperadmin()) $_omset->where('id_cabang', CRUDBooster::myCabangId());			
			$x = $_omset->sum('total');
			$omset_b = number_format($x,0,',','.');

	        $this->index_statistic = array();
	        $this->index_statistic[] = ['label'=>'OMSET HARI INI','count'=>$omset_h,'icon'=>'fa fa-money','color'=>'success'];
	        $this->index_statistic[] = ['label'=>'OMSET BULAN INI','count'=>$omset_b,'icon'=>'fa fa-money','color'=>'warning'];


	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
			$this->script_js = "

				$(function(){
					
					var _data = {
						id_jasa: '',
						id_jenis_kendaraan: ''
					};

					$('#detailjasaid_jasa button').prop('disabled', true);
					// $('#form-group-metode_pembayaran, #form-group-id_merchant, #form-group-nomor_kartu, #form-group-kode_trace, #form-group-bayar, #form-group-kembalian').hide();

					var _id = $('#id_kendaraan').val();					
					if(_id !== ''){
						$('#detailjasaid_jasa button').prop('disabled', false);
						_data.id_jenis_kendaraan = _id;
					}
						

					$('#id_kendaraan').change(function(){
						var id = $(this).val();
						_data.id_jenis_kendaraan = id;

						if(id === ''){
							$('#detailjasaid_jasa button').prop('disabled', true);
						}else{
							$('#detailjasaid_jasa button').prop('disabled', false);
						}
						resetFormdetailjasa();
					});

					$('input.input-id').change(function(){
						_data.id_jasa = $(this).val();

						$.ajax({
							url: '".CRUDBooster::apipath('jasa/hargaperkendaraan')."',
							method: 'GET',
							data: _data,
							success: function(res){
								console.log('Res: ', res);
								$('#detailjasaharga').val(res.harga);
							},
							error: function(err){
								console.log('Err: ', err);
							}
						});
					});
	
					setInterval(function() {
					
						var subtotal = 0;
						var total = 0;
						$('#table-detailjasa tbody .harga').each(function() {
							subtotal += parseInt($(this).text());
						})
						$('#subtotal').val(subtotal);
						
						var diskon = $('#diskon_nominal').val();
						// diskon = parseFloat(diskon.replace(/,/g,''));	
						
						var diskon_tipe = $('input[name=diskon_tipe]:checked').val();						
						if(diskon_tipe == 0){
							total = subtotal - diskon;
						}else{
							var diskon_ = (diskon / 100) * subtotal;
							total = subtotal - diskon_;
						}
						
						$('#total').val(total);

					}, 500);				

					// $('input[type=radio][name=status_pembayaran]').change(function(){
					// 	var value = $(this).val();
					// 	if(value == 25){
					// 		$('#form-group-metode_pembayaran').hide();
					// 		$('#form-group-id_merchant').hide();
					// 		$('#form-group-nomor_kartu').hide();
					// 		$('#form-group-kode_trace').hide();
					// 		$('#form-group-bayar').hide();				
					// 		$('#form-group-kembalian').hide();				
					// 		// $('input[type=radio][name=metode_pembayaran]').prop('disabled', true);
					// 		// $('#id_merchant').prop('disabled', true);						
					// 		// $('#nomor_kartu').prop('disabled', true);
					// 		// $('#kode_trace').prop('disabled', true);					
					// 		// $('#bayar').prop('disabled', true);					
					// 	}else{
					// 		// $('input[type=radio][name=metode_pembayaran]').prop('disabled', false);							
					// 		// $('#nomor_kartu').prop('disabled', false);
					// 		// $('#kode_trace').prop('disabled', false);
					// 		// $('#bayar').prop('disabled', false);	
					// 		$('#form-group-metode_pembayaran').show();
					// 		$('input[type=radio][name=metode_pembayaran]').attr('checked', true).trigger('click');
					// 	}
					// });

					// $('input[type=radio][name=metode_pembayaran]').change(function(){
					// 	var value = $(this).val();
					// 	if(value == 30){
					// 		// $('#id_merchant').prop('disabled', false);
					// 		// $('#nomor_kartu').prop('disabled', false);
					// 		// $('#kode_trace').prop('disabled', false);
					// 		$('#form-group-id_merchant, #form-group-nomor_kartu, #form-group-kode_trace').show();											
					// 		$('#form-group-bayar, #form-group-kembalian').hide();								
					// 	}else{
					// 		// $('#id_merchant').prop('disabled', true);
					// 		// $('#nomor_kartu').prop('disabled', true);
					// 		// $('#kode_trace').prop('disabled', true);
					// 		$('#form-group-id_merchant, #form-group-nomor_kartu, #form-group-kode_trace').hide();								
					// 		$('#form-group-bayar, #form-group-kembalian').show();								
					// 	}
					// });

					$('#bayar').keyup(function(){
						var bayar = $(this).val();
						var total = $('#total').val();
						var kembalian = parseInt(bayar) - parseInt(total);
						console.log(kembalian);
						$('#kembalian').val(kembalian);
					});

				});
			";


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
			$this->style_css = "
				table tbody td {
					text-align: center;
				}
			";
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
			//Your code here
			if(!CRUDBooster::isSuperadmin()){
				$id_cabang = CRUDBooster::myCabangId();
				$query->where('id_cabang', $id_cabang);
			}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
	        //Your code here
			// $kendaraan = CRUDBooster::first('tb_kendaraan', $postdata['id_kendaraan']);
			// $merek = CRUDBooster::first('tb_merek_kendaraan', $kendaraan->id_merek_kendaraan);			

			// // $postdata['status_pembayaran'] = 25;	// 25 | BELUM BAYAR; 26 | SUDAH BAYAR; TB GENERAL
			// $postdata['status_penjualan'] = 27;	// 27 | REGULER; 28 | BOOKING; TB GENERAL
			// $postdata['created_by'] = CRUDBooster::myName();
			// $postdata['id_cabang'] = CRUDBooster::myCabangId();
			// $postdata['nama_pelanggan'] = (empty($postdata['nama_pelanggan'])) ? 'Walk In Order' : $postdata['nama_pelanggan'];
			// $postdata['id_merek_kendaraan'] = $merek->id;
			// $postdata['merek_kendaraan'] = $merek->keterangan;
			// $postdata['nama_kendaraan'] = $kendaraan->keterangan;

			// if($postdata['metode_pembayaran'] == 30){
			// 	$postdata['status_pembayaran'] = 26;
			// }else{
			// 	$postdata['id_merchant'] = NULL;
			// 	$postdata['nomor_kartu'] = NULL;
			// 	$postdata['kode_trace'] = NULL;
			// }

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
			//Your code here
			// $jb_stok = [];
			// $jasa = CRUDBooster::first('tb_penjualan_jasa', $id);
			// $jasa_detail = DB::table('tb_penjualan_jasa_detail')
			// 					->where('id_penjualan_jasa', $id)
			// 					->get();

			// foreach($jasa_detail as $jd) {
			// 	$js = CRUDBooster::first('tb_jasa',$jd->id_jasa);				
			// 	$array = array(
			// 		'kode_penjualan_jasa'	=> $jasa->kode,
			// 		'nama_jasa'				=> $js->keterangan
			// 	);
			// 	DB::table('tb_penjualan_jasa_detail')->where('id',$jd->id)->update($array);

			// 	$jb = CRUDBooster::get('tb_jasa_bahan','id_jasa = '.$jd->id_jasa);
			// 	foreach ($jb as $value) {
			// 		$bahan = CRUDBooster::first('tb_bahan_jasa',$value->id_bahan_jasa);
			// 		array_push($jb_stok, array(
			// 			'id_produk'		=> $value->id_bahan_jasa,
			// 			'tanggal'		=> date('Y-m-d H:i:s'),
			// 			'stok_masuk'	=> 0,
			// 			'stok_keluar'	=> $value->quantity,
			// 			'keterangan'	=> 'Pengurangan stok dari penjualan '.$jasa->kode,
			// 			'created_at'	=> date('Y-m-d H:i:s'),
			// 			'created_by'	=> CRUDBooster::myName()
			// 		));					
			// 		DB::table('tb_bahan_jasa')->where('id', $value->id_bahan_jasa)->update(['stok' => $bahan->stok - $value->quantity]);
			// 	}
			// 	DB::table('tb_bahan_jasa_stok')->insert($jb_stok);
			// }		
			
			$date = date('Y-m-d');
			$time = date('H:i:s');
			
			$shift = DB::table('tb_jam_shift_kerja')->orderby('jam_masuk','ASC')->get();			

			foreach ($shift as $value) {
				if($time >= $value->jam_masuk && $time <= $value->jam_keluar){					
					$karyawan = DB::table('tb_karyawan as k')
									->join('tb_karyawan_shift as ks','ks.id_karyawan','=','k.id')
									->select('k.id','ks.id_jam_shift_kerja as id_shift')
									->where('k.insentif',1)
									->get();
					
					break;
				}
			}
			
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here
			$postdata['updated_by'] = CRUDBooster::myName();	

			if($postdata['status_pembayaran'] == 25){
				$postdata['metode_pembayaran'] = NULL;
				$postdata['id_merchant'] = NULL;
				$postdata['nomor_kartu'] = NULL;
				$postdata['kode_trace'] = NULL;
				$postdata['bayar'] = NULL;
				$postdata['kembalian'] = NULL;
			}else{
				if($postdata['metode_pembayaran'] == 30){
					$postdata['status_pembayaran'] = 26;
				}else{
					$postdata['id_merchant'] = NULL;
					$postdata['nomor_kartu'] = NULL;
					$postdata['kode_trace'] = NULL;
				}	
			}
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 
			$jasa = CRUDBooster::first('tb_penjualan_jasa', $id);
			$jasa_detail = DB::table('tb_penjualan_jasa_detail')
								->where('id_penjualan_jasa', $id)
								->get();

			foreach($jasa_detail as $jd) {
				$js = CRUDBooster::first('tb_jasa',$jd->id_jasa);
				
				$array = array(
					'kode_penjualan_jasa'	=> $jasa->kode,
					'nama_jasa'				=> $js->keterangan
				);

				DB::table('tb_penjualan_jasa_detail')->where('id',$jd->id)->update($array);
			}	
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here
			DB::table('tb_penjualan_jasa')->where('id',$id)->update([
				'deleted_by'	=> CRUDBooster::myName()
			]);
	    }



	    //By the way, you can still create your own method in here... :) 
		public function getPrintStruk($id = null)
		{
			$myip = Request::ip();
			$cabang = CRUDBooster::myCabang();
			$image = storage_path('app/'.$cabang->logo_struk);

			$logo = EscposImage::load($image, false);
			$printer_name = CRUDBooster::getSetting('printer');

			$pos = CRUDBooster::first('tb_penjualan_jasa', $id);			
			$metode = CRUDBooster::first('tb_general', $pos->metode_pembayaran);
			$posd = DB::table('tb_penjualan_jasa_detail')->where('id_penjualan_jasa', $pos->id)->get();

			try {
				$profile = CapabilityProfile::load("simple");
				$connector = new WindowsPrintConnector('smb://Guest@'.$myip.'/'.$printer_name);
				$printer = new Printer($connector);
				$printer->setJustification(Printer::JUSTIFY_CENTER);		 		
		 		$printer -> bitImage($logo);
				$printer -> text("\n");
				
				$tanggal = date('d F Y', strtotime($pos->tanggal));				
				$printer -> text(new format("Kode", $pos->kode));
				$printer -> text(new format("Tanggal", $tanggal));
				$printer -> text(new format("Pelanggan", $pos->nama_pelanggan));
				$printer -> text(new format("Kasir", $pos->created_by));
				$printer -> text("--------------------------------\n");
				$printer -> feed();

				$printer -> setJustification(Printer::JUSTIFY_LEFT);
				foreach ($posd as $value) {
					$printer -> text(new item($value->nama_jasa, $value->harga));
				}
				$printer -> feed();
				$printer -> text("--------------------------------\n");

				$printer -> setJustification(Printer::JUSTIFY_CENTER);
				$printer -> text(new item("Subtotal", $pos->subtotal));
				if($pos->diskon_tipe === 0){
					$diskon = $pos->diskon_nominal;
				}else{
					$diskon = $pos->subtotal * ($pos->diskon_nominal / 100);
				}				

				$printer -> text(new item("Diskon", $diskon));
				$printer -> text(new item("Grand Total", $pos->total));
				$printer -> text(new item($metode->keterangan, $pos->bayar));
				$printer -> text(new item("Kembalian", $pos->kembalian));
				$printer -> feed();

				$printer -> text("Terima kasih\n");
				$printer -> text("Kepuasan anda \n merupakan prestasi kami\n");

				$printer -> feed(3);			
		
				$printer -> cut();
				$printer -> close();

				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Struk penjualan berhasil di cetak !","info");
			} catch (Exception $e) {
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Gagal, Printer bermasalah !!!","danger");
			}			
					
		}

		// public function getPrintStruk($id = null)
		// {
		// 	$logo = EscposImage::load("logo_black.png", false);
		// 	$printer_name = CRUDBooster::getSetting('printer');

		// 	$pos = CRUDBooster::first('tb_penjualan_jasa', $id);
		// 	$posd = DB::table('tb_penjualan_jasa_detail')->where('id_penjualan_jasa', $pos->id)->get();

		// 	try {
		// 		$connector = new WindowsPrintConnector($printer_name);
		// 		$printer = new Printer($connector);
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		//  		// $printer -> bitImage($logo);
		// 		$printer -> text("\n");
		// 		$printer -> setTextSize(2, 2);
		// 		$printer -> text('BOOKING');
		// 		$printer -> text("\n\n");

		// 		$printer->qrCode("POSJS07081900006",Printer::QR_ECLEVEL_L, 8);
		// 		$printer -> text("\n");
		// 		$printer -> setTextSize(1,1);
		// 		$printer -> text("--- POSJS07081900006 ---");
		// 		$printer -> text("\n");
		// 		$tanggal = date('d F Y', strtotime($pos->tanggal));
		// 		$printer -> text($tanggal);
		// 		$printer -> text("\n");
		// 		$jam_pesan = date('H:i', strtotime($pos->tanggal));
		// 		$jam_masuk = date('H:i', strtotime($pos->tanggal_masuk));
		// 		$printer -> text(new format("Jam Pesan", $jam_pesan));
		// 		$printer -> text(new format("Jam Masuk", $jam_masuk));
		// 		$printer -> text(new format("NOPOL", "N 4759 GH"));				
		// 		$printer -> text(new format("Kendaraan", "DAIHATSU | AYLA"));				
		// 		$printer -> text("--------------------------------\n");

		// 		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		// 		foreach ($posd as $value) {
		// 			$printer -> text(new format("1 x ", $value->nama_jasa));
		// 		}
		// 		// foreach ($posd as $value) {
		// 		// 	$printer -> text(new format("1 x ", $value->nama_jasa));
		// 		// }
		// 		// foreach ($posd as $value) {
		// 		// 	$printer -> text(new format("1 x ", $value->nama_jasa));
		// 		// }
		// 		$printer -> feed();

		// 		// $printer -> setJustification(Printer::JUSTIFY_CENTER);
		// 		// $printer -> text(new item("Subtotal", $pos->subtotal));
		// 		// if($pos->diskon_tipe === 0){
		// 		// 	$diskon = $pos->diskon_nominal;
		// 		// }else{
		// 		// 	$diskon = $pos->subtotal * ($pos->diskon_nominal / 100);
		// 		// }
		// 		// $printer -> text(new item("Diskon", $diskon));
		// 		// $printer -> text(new item("Grand Total", $pos->total));
		// 		// $printer -> feed();

		// 		// $printer -> text("Terima kasih\n\n");
		// 		// $printer -> text("Kepuasan anda \n merupakan prestasi kami\n");

		// 		$printer -> feed();			
		
		// 		$printer -> cut();
		// 		$printer -> close();

		// 		CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Struk penjualan berhasil di cetak !","info");
		// 	} catch (Exception $e) {
		// 		CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Gagal, Printer bermasalah !!!","danger");
		// 	}			
					
		// }

	}
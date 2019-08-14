<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	use Mike42\Escpos\Printer;
	use Mike42\Escpos\EscposImage;
	use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

	class AdminTbPenjualanPosController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->table = "tb_penjualan_pos";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Tanggal","name"=>"tanggal","callback"=>function($row){
				return date('d-m-y | H:i',strtotime($row->tanggal));				
			}];
			$this->col[] = ["label"=>"Kode","name"=>"kode"];
			$this->col[] = ["label"=>"Nama Pelanggan","name"=>"nama_pelanggan"];
			$this->col[] = ["label"=>"Total","name"=>"total"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('tb_penjualan_pos')->whereDate('created_at',date('Y-m-d'))->count('id') + 1;
			$kode = 'POS'.date('dmy').''.str_pad($kode,5,0,STR_PAD_LEFT);

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode','name'=>'kode','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-4','readonly'=>'true','value'=>$kode];
			$this->form[] = ['label'=>'Nama Pelanggan','name'=>'nama_pelanggan','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-4','placeholder'=>'Cth: Deni','help'=>'*Isi dengan nama pelanggan'];

			$columns[] = ['label'=>'Produk','name'=>'id_produk','type'=>'datamodal','required'=>true,'datamodal_table'=>'tb_produk','datamodal_columns'=>'keterangan,harga','datamodal_columns_alias'=>'Produk,Harga','datamodal_select_to'=>'harga:harga','datamodal_where'=>'status = 1','datamodal_size'=>'large'];
			$columns[] = ['label'=>'Harga','name'=>'harga','type'=>'number','readonly'=>true,'required'=>true];
			$columns[] = ['label'=>'Qty','name'=>'quantity','type'=>'number','required'=>true];
			// $columns[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','dataenum'=>'0|Nominal;1|Persen'];
			// $columns[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'number'];
			$columns[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'number','required'=>true,'formula'=>"[quantity] * [harga]","readonly"=>true];
			$this->form[] = ['label'=>'Detail Penjualan','name'=>'penjualan_pos_detail','type'=>'child','columns'=>$columns,'table'=>'tb_penjualan_pos_detail','foreign_key'=>'id_penjualan_pos'];
			$this->form[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'money','width'=>'col-sm-4','readonly'=>true,'value'=>0];
			$this->form[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','width'=>'col-sm-4','dataenum'=>'0|Nominal;1|Persen','value'=>0,'inline'=> true];
			$this->form[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'money','validation'=>'integer|min:0','width'=>'col-sm-4','value'=>0];
			$this->form[] = ['label'=>'Total','name'=>'total','type'=>'money','width'=>'col-sm-4','readonly'=>true,'value'=>0];
			$this->form[] = ['label'=>'Metode Pembayaran','name'=>'metode_pembayaran','validation'=>'','type'=>'radio','width'=>'col-sm-4','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 12','value'=>29,'inline'=> true];
			$this->form[] = ['label'=>'Merchant','name'=>'id_merchant','type'=>'select2','width'=>'col-sm-4','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 13'];
			$this->form[] = ['label'=>'No Kartu','name'=>'nomor_kartu','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Kode Trace','name'=>'kode_trace','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Bayar','name'=>'bayar','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Kembalian','name'=>'kembalian','type'=>'money','validation'=>'integer|min:0','width'=>'col-sm-4','readonly'=>true];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Kode","name"=>"kode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tanggal","name"=>"tanggal","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Nama Pelanggan","name"=>"nama_pelanggan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Subtotal","name"=>"subtotal","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Diskon Tipe","name"=>"diskon_tipe","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Diskon Nominal","name"=>"diskon_nominal","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Total","name"=>"total","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
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

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
			$_omset = DB::table('tb_penjualan_pos')->whereDate('tanggal',date('Y-m-d'));
			if(!CRUDBooster::isSuperadmin()) $_omset->where('id_cabang', CRUDBooster::myCabangId());			
			$x = $_omset->sum('total');
			$omset_h = number_format($x,0,',','.');
			
			$_omset = DB::table('tb_penjualan_pos')->whereMonth('tanggal',date('m'));
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

				function numberFormat(number){
					return Number(number.replace(/[^0-9\,]+/g,''));
				}

				$(function(){

					$('#detailpenjualanharga').val(0);
					$('#detailpenjualanquantity').val(0);
					$('#detailpenjualansubtotal').val(0);
					var _stokPrd = 0;

					setInterval(function() {
						var _id = $('#detailpenjualanid_produk .input-id').val();
						var _url = '".CRUDBooster::apiPath('produk')."';
						
						if(_id != null && _id != ''){
							$.ajax({
								method: 'GET',
								url: _url,
								data: {id: _id},
								success: function(res){
									console.log(res);
									_stokPrd = res[0].stok;
								},
								error: function(err){
									console.log(err);
								}
							});
						}
					
						var subtotal = 0;
						var total = 0;
						$('#table-detailpenjualan tbody .subtotal').each(function() {
							subtotal += numberFormat($(this).text());
						})
						$('#subtotal').val(subtotal);
						
						var diskon = $('#diskon_nominal').val();
						diskon = parseFloat(diskon.replace(/,/g,''));	
						
						var diskon_tipe = $('input[name=diskon_tipe]:checked').val();						
						if(diskon_tipe == 0){
							total = subtotal - diskon;
						}else{
							var diskon_ = (diskon / 100) * subtotal;
							total = subtotal - diskon_;
						}
						
						$('#total').val(total);
						$('.inputMoney').priceFormat({'prefix':'','thousandsSeparator':'.','centsLimit':'0','clearOnEmpty':false});

					}, 1000);	

					$('input.input-id').change(function(){						
						$('#detailpenjualanquantity').val(1).trigger('change');
					});

					$('#detailpenjualanquantity').on('keydown keyup', function(e){
						if ($(this).val() > _stokPrd 
							&& e.keyCode !== 46 // keycode for delete
							&& e.keyCode !== 8 // keycode for backspace
						   ) {
						   e.preventDefault();
						   $(this).val(_stokPrd);
						}
					});

					$('#bayar').keyup(function(){
						var bayar = numberFormat($(this).val());						
						var total = numberFormat($('#total').val());						
						var kembalian = parseInt(bayar) - parseInt(total);		
						kembalian = (kembalian < 0) ? 0 : kembalian;
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
	        $this->style_css = NULL;
	        
	        
	        
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
			$postdata['tanggal'] = date('Y-m-d H:i:s');
			$postdata['nama_pelanggan'] = (empty($postdata['nama_pelanggan'])) ? 'Walk In Order' : $postdata['nama_pelanggan'];
			$postdata['created_by'] = CRUDBooster::myName();
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
			$pos = CRUDBooster::first('tb_penjualan_pos', $id);
			$pos_detail = DB::table('tb_penjualan_pos_detail')
								->where('id_penjualan_pos', $id)
								->get();

			foreach($pos_detail as $pd) {
				$produk = CRUDBooster::first('tb_produk',$pd->id_produk);
				
				$array = array(
					'kode_penjualan_pos'	=> $pos->kode,
					'nama_produk'			=> $produk->keterangan
				);

				$produk_stok = array(
					'tanggal'		=> $pos->tanggal,
					'id_produk'		=> $pd->id_produk,
					'stok_masuk'	=> 0,
					'stok_keluar'	=> $pd->quantity,
					'keterangan'	=> 'Pengurangan stok dari penjualan '.$pos->kode,
					'created_at'	=> $pos->tanggal,
					'created_by'	=> 'by Sistem'
				);

				DB::table('tb_penjualan_pos_detail')->where('id',$pd->id)->update($array);
				DB::table('tb_produk_stok')->insert($produk_stok);
				DB::table('tb_produk')->where('id',$pd->id_produk)->update(['stok'=> abs($produk->stok - $pd->quantity)]);
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

			$produk = [];
			$pos_detail = DB::table('tb_penjualan_pos_detail')->where('id_penjualan_pos', $id)->get();
			foreach ($pos_detail as $value) {
				$prd = CRUDBooster::first('tb_produk',$value->id_produk);
				array_push($produk, array(
					'tanggal'		=> date('Y-m-d H:i:s'),
					'id_produk'		=> $value->id_produk,
					'stok_masuk'	=> $value->quantity,
					'stok_keluar'	=> 0,
					'keterangan'	=> 'Perubahan transaksi '.$postdata['kode'].' oleh '.$postdata['updated_by'],
					'created_at'	=> date('Y-m-d H:i:s'),
					'created_by'	=> 'by Sistem'
				));

				DB::table('tb_produk')->where('id', $value->id_produk)->update(['stok'=> abs($prd->stok + $value->quantity)]);
			}
			DB::table('tb_produk_stok')->insert($produk);		
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
			$produk = [];			
			$upd_produk = [];

			$pos = CRUDBooster::first('tb_penjualan_pos',$id);
			$pos_detail = DB::table('tb_penjualan_pos_detail')->where('id_penjualan_pos', $id)->get();
			foreach ($pos_detail as $value) {
				$prd = CRUDBooster::first('tb_produk',$value->id_produk);

				$upd_produk = array(
					'kode_penjualan_pos'	=> $pos->kode,
					'nama_produk'			=> $prd->keterangan
				);

				array_push($produk, array(
					'tanggal'		=> date('Y-m-d H:i:s'),
					'id_produk'		=> $value->id_produk,
					'stok_masuk'	=> 0,
					'stok_keluar'	=> $value->quantity,
					'keterangan'	=> 'Perubahan transaksi '.$pos->kode.' oleh '.$pos->updated_by,
					'created_at'	=> date('Y-m-d H:i:s'),
					'created_by'	=> 'by Sistem'
				));
				
				DB::table('tb_penjualan_pos_detail')->where('id',$value->id)->update($upd_produk);
				DB::table('tb_produk')->where('id', $value->id_produk)->update(['stok'=> abs($prd->stok - $value->quantity)]);
			}			
			DB::table('tb_produk_stok')->insert($produk);
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
			DB::table('tb_penjualan_pos')->where('id',$id)->update([
				'deleted_by'	=> CRUDBooster::myName()
			]);
	    }



	    //By the way, you can still create your own method in here... :) 
		public function getPrintStruk($id = null)
		{
			$logo = EscposImage::load("logo_black.png", false);
			$printer_name = CRUDBooster::getSetting('printer');

			$pos = CRUDBooster::first('tb_penjualan_pos', $id);
			$metode = CRUDBooster::first('tb_general', $pos->metode_pembayaran);
			$posd = DB::table('tb_penjualan_pos_detail')->where('id_penjualan_pos', $pos->id)->get();

			try {
				$connector = new WindowsPrintConnector($printer_name);
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
					$printer -> text($value->nama_produk."\n");
					$printer -> text(new item($value->quantity.' x '.number_format($value->harga,0,',','.'), $value->subtotal));
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

	}

class item
{
	private $name;
	private $price;
	private $rupiah;
	public function __construct($name = '', $price = '', $rupiah = false)
	{
		$this->name = $name;
		$this->price = number_format($price,0,',','.');
		$this->rupiah = $rupiah;
	}
	
	public function __toString()
	{
		$rightCols = 8;
		$leftCols = 24;
		if ($this->rupiah) {
			$leftCols = $leftCols / 2 - $rightCols / 2;
		}
		$left = str_pad($this->name, $leftCols) ;
		$sign = ($this->rupiah ? 'Rp ' : '');
		$right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
		return "$left$right\n";
	}
}	

class format
{
	private $name;
	private $text;
	public function __construct($name = '', $text = '')
	{
		$this->name = $name;
		$this->text = $text;
	}
	
	public function __toString()
	{
		$rightCols = 20;
		$leftCols = 12;
		
		$left = str_pad($this->name, $leftCols) ;
		$right = str_pad($this->text, $rightCols, ' ', STR_PAD_LEFT);
		return "$left$right\n";
	}
}	
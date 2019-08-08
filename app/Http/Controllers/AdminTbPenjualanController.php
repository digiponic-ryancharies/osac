<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	use Mike42\Escpos\Printer;
	use Mike42\Escpos\EscposImage;
	use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

	class AdminTbPenjualanController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "orders";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Order Number","name"=>"order_number"];
			$this->col[] = ["label"=>"Order Date","name"=>"created_at","callback_php"=>'date("d-m-Y | H:i", strtotime($row->created_at))'];
			$this->col[] = ["label"=>"Tipe","name"=>"tipe",'join'=>'tb_general,keterangan'];
			$this->col[] = ["label"=>"Kendaraan","name"=>"kendaraan",'join'=>'tb_general,keterangan'];
			$this->col[] = ["label"=>"Grand Total","name"=>"grand_total","callback_php"=>'"Rp ".number_format($row->grand_total)'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('orders')->max('id') + 1;
			$kode = 'PNJ/'.date('dmy').'/'.str_pad($kode, 5, 0, STR_PAD_LEFT);

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode Penjualan','name'=>'order_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','readonly'=>true,'value'=>$kode];
			//$this->form[] = ['label'=>'Nomor Polisi','name'=>'police_number','type'=>'text','validation'=>'required','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Tipe','name'=>'kendaraan','type'=>'radio','validation'=>'required','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'kode_tipe = 9'];
			$this->form[] = ['label'=>'Kendaraan','name'=>'tipe','type'=>'radio','validation'=>'required','width'=>'col-sm-10', 'datatable'=>'tb_general,keterangan','datatable_where'=>'kode_tipe = 10'];
			$columns = [];
			$columns[] 		= ['label'=>'Jasa','name'=>'services_id','type'=>'datamodal','datamodal_table'=>'services','datamodal_columns'=>'name,price','datamodal_select_to'=>'price:services_price','required'=>true];
			$columns[] 		= ['label'=>'Price','name'=>'services_price','type'=>'text','readonly'=>true];
			$this->form[] 	= ['label'=>'Services Detail','name'=>'services_detail','columns'=>$columns,'type'=>'child','width'=>'col-sm-10','table'=>'orders_detail','foreign_key'=>'orders_id'];
			
			$this->form[] 	= ['label'=>'Total','name'=>'total','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10','readonly'=>'1'];
			$this->form[]	= ['label'=>'Tipe Diskon','name'=>'diskon_tipe','type'=>'radio','dataenum'=>'Nominal;Persen','value'=>'Nominal'];
			$this->form[] 	= ['label'=>'Discount','name'=>'discount','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10','value'=>0];
			$this->form[] 	= ['label'=>'Grand Total','name'=>'grand_total','type'=>'number','validation'=>'integer|min:0','width'=>'col-sm-10','readonly'=>'1'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Kode","name"=>"kode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Keterangan","name"=>"keterangan","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Tanggal","name"=>"tanggal","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Subtotal","name"=>"subtotal","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Pajak","name"=>"pajak","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Diskon Tipe","name"=>"diskon_tipe","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Diskon","name"=>"diskon","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Grand Total","name"=>"grand_total","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Users Id","name"=>"users_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"users,id"];
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
	        | @color 	   	= Default is primary. (primary, warning, succecss, info)
	        | @id 	   		= Id of action
	        | @title 	   	= Title of action
	        | @onclick 	   = OnClick JS of action
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        |
	        */
	        $this->addaction = array();
			$this->addaction[] = ['label'=>'','icon'=>'fa fa-print','color'=>'warning print','url'=>CRUDBooster::mainpath('set-print').'/[id]','showIf'=>"[status] > 1"];

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
	        $this->index_statistic = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add javascript at body
	        | ----------------------------------------------------------------------
	        | javascript code in the variable
	        | $this->script_js = "function() { ... }";
	        |
	        */
			$this->script_js = "
			$(function() {

				setInterval(function() {
					var total = 0;
					$('#table-servicesdetail tbody .services_price').each(function() {
						total += parseInt($(this).text());
					});
					$('#total').val(total);

					var grand_total = 0;
					grand_total += total;

					var discount = $('#discount').val().toString().length;
					if(discount < 3){
						grand_total -= (parseInt($('#discount').val()) / 100) * total;
					}else{
						grand_total -= parseInt($('#discount').val());
					} 

					$('#grand_total').val(grand_total);

				}, 500);

			})";



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
			$penjualan = DB::table('tb_penjualan')->where('id',$id)->first();
			$penjualan_detail = DB::table('tb_penjualan_detail')->where('id_penjualan',$id)->get();

			foreach($penjualan_detail as $pd) {
				$produk = DB::table('tb_produk')->where('id',$pd->id_produk)->first();
				$array = array(
					'kode_penjualan'	=> $penjualan->kode,
					'kode_produk'		=> $produk->kode,
					'nama_produk'		=> $produk->keterangan,
					'satuan'			=> $produk->satuan
				);
				$produk_stok = array(
					'tanggal'		=> $penjualan->tanggal,
					'kode_produk'	=> $pd->id_produk,
					'stok_masuk'	=> $pd->kuantitas,
					'stok_keluar'	=> 0,
					'keterangan'	=> 'Pengurangan stok dari penjualan '.$penjualan->kode
				);

				DB::table('tb_penjualan_detail')->where('id',$pd->id)->update($array);
				DB::table('tb_produk_stok')->insert($produk_stok);
				DB::table('tb_produk')->where('id',$pd->id_produk)->update(['stok'=> abs($produk->stok - $pd->kuantitas)]);
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

		}
		
		public function getSetPrint($id) {

			$logo = EscposImage::load("logo_black.png", false);

			try {
				$connector = new WindowsPrintConnector("GP-5830");
				$printer = new Printer($connector);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
		 		$printer -> bitImage($logo);
				$printer -> text("\n");
				
				$date = date('d F Y');
				$printer -> text($date);
				$printer -> text("\n\n");
				 
				$printer->setBarcodeHeight(49);
				$printer->setBarcodeWidth(2);
				$printer->selectPrintMode();
				$printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
				// $printer->barcode("{A00/008", Printer::BARCODE_CODE128);
				$printer->barcode("{APNJ210219/008", Printer::BARCODE_CODE128);
				$printer->feed(3);			
		
				$printer -> cut();
				$printer -> close();

				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"The Orders has been printed !","info");
			} catch (Exception $e) {
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Error, Print Failed !!!","danger");
			}			
			
			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"The Orders has been printed !","info");
		 }

		// public function getSetPrint($id) {
		// 	$order = DB::table('orders')->where('id',$id)->first();
		// 	$detail = DB::table('orders_detail')->where('orders_id',$id)->get();
			
		// 	$i = 0;
		// 	foreach ($detail as $d) {
		// 		$detail[$i]->services_name = DB::table('services')->where('id',$d->services_id)->value('name');
		// 		$i++;
		// 	}
		// 	$logo = EscposImage::load("logo_black.png", false);

		// 	try {
		// 		$connector = new WindowsPrintConnector("GP-5830");
		// 		$printer = new Printer($connector);
				
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer -> bitImage($logo);
		// 		 $printer -> text("\n");

		// 		// $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		// 		// $printer -> text("O S A C\n");
		// 		// $printer -> selectPrintMode();
		// 		// $printer -> text("(One Stop Auto Car)\n");
		// 		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		// 		$date 	= date('d M Y', strtotime($order->created_at));
		// 		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer -> text(new format($date, $order->police_number));
		// 		$printer -> text("--------------------------------");
		// 		$printer -> text($order->order_number."\n\n\n");

		// 		$printer -> setJustification(Printer::BARCODE_UPCA);
		// 		$printer -> text('123456');
		// 		// foreach ($detail as $d) {
		// 		// 	$printer -> text($d->services_name."\n");
		// 		// 	$printer -> text(new item("1 x ".$d->services_price,$d->services_price));
		// 		// }
		// 		// $printer -> text("--------------------------------");

		// 		// $printer -> text(new item("Subtotal",$order->total));
		// 		// $printer -> text(new item("Discount",$order->discount));
		// 		// $printer -> text(new item("Grand Total",$order->grand_total));
		// 		// $printer -> feed();
		
		// 		$printer -> cut();
		// 		$printer -> close();

		// 		CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"The Orders has been printed !","info");
		// 	} catch (Exception $e) {
		// 		CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Error, Print Failed !!!","danger");
		// 	}			
			
		// 	CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"The Orders has been printed !","info");
		//  }
	    //By the way, you can still create your own method in here... :)
	}
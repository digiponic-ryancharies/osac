<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

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
			$this->col[] = ["label"=>"Kode","name"=>"kode"];
			$this->col[] = ["label"=>"Tanggal","name"=>"tanggal"];
			$this->col[] = ["label"=>"Nama Pelanggan","name"=>"nama_pelanggan"];
			$this->col[] = ["label"=>"Total","name"=>"total"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('tb_penjualan_pos')->max('id') + 1;
			$kode = 'POS/'.date('dmy').'/'.str_pad($kode,5,0,STR_PAD_LEFT);

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode','name'=>'kode','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','readonly'=>'true','value'=>$kode];
			$this->form[] = ['label'=>'Nama Pelanggan','name'=>'nama_pelanggan','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10','placeholder'=>'Cth: Andi','help'=>'*silahkan ganti dengan nama pelanggan'];

			$columns[] = ['label'=>'Produk','name'=>'id_produk','type'=>'datamodal','required'=>true,'datamodal_table'=>'tb_produk','datamodal_columns'=>'keterangan,harga','datamodal_columns_alias'=>'Produk,Harga','datamodal_select_to'=>'harga:harga','datamodal_where'=>'status = 1','datamodal_size'=>'large'];
			$columns[] = ['label'=>'Harga','name'=>'harga','type'=>'number','readonly'=>true,'required'=>true];
			$columns[] = ['label'=>'Qty','name'=>'quantity','type'=>'number','required'=>true];
			// $columns[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','dataenum'=>'0|Nominal;1|Persen'];
			// $columns[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'number'];
			$columns[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'number','required'=>true,'formula'=>"[quantity] * [harga]","readonly"=>true];
			$this->form[] = ['label'=>'Detail Penjualan','name'=>'penjualan_pos_detail','type'=>'child','columns'=>$columns,'table'=>'tb_penjualan_pos_detail','foreign_key'=>'id_penjualan_pos'];

			$this->form[] = ['label'=>'Subtotal','name'=>'subtotal','type'=>'money','width'=>'col-sm-10','readonly'=>true,'value'=>0];
			$this->form[] = ['label'=>'Diskon Tipe','name'=>'diskon_tipe','type'=>'radio','width'=>'col-sm-10','dataenum'=>'0|Nominal;1|Persen','value'=>0];
			$this->form[] = ['label'=>'Nominal Diskon','name'=>'diskon_nominal','type'=>'money','validation'=>'integer|min:0','width'=>'col-sm-10','value'=>0];
			$this->form[] = ['label'=>'Total','name'=>'total','type'=>'money','width'=>'col-sm-10','readonly'=>true,'value'=>0];
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
			
				$(function(){
					setInterval(function() {

						// var harga = $('#detilpenjualanharga').val();
						
						// var diskon_produk = $('#detilpenjualandiskon').val();
						// var subtotal_produk = $('#detilpenjualansubtotal').val();
						// var grand_total_produk = 0;

						// $('#detilpenjualangrand_total').val(grand_total_produk);
					
						var subtotal = 0;
						var total = 0;
						$('#table-detailpenjualan tbody .subtotal').each(function() {
							subtotal += parseInt($(this).text());
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

						// var subtotal = 0;
						// subtotal += total;
						// $('#subtotal').val(subtotal); 
						
						// var pajak = $('#pajak').val();
						// var diskon_tipe = $('input[name=diskon_tipe]:checked').val();
						// var diskon_keseluruhan = $('#diskon').val();
						// var subtotal = 	$('#subtotal').val();
						// var grand_total_keseluruhan = 0;

						// if(diskon_tipe =='Nominal'){
						// 	grand_total_keseluruhan = subtotal - diskon_keseluruhan;
						// }else{
						// 	var diskon_keseluruhan_ = (diskon_keseluruhan/100) * subtotal;
						// 	grand_total_keseluruhan = subtotal - diskon_keseluruhan_;
							
						// }	
						// var pajak_ = (pajak/100) * subtotal;
						// grand_total_keseluruhan_pajak = grand_total_keseluruhan + pajak_;		
						// $('#grand_total').val(grand_total_keseluruhan_pajak);
				
						// var xx = ".CRUDBooster::getSetting('minimal_belanja').";
						// if(grand_total_keseluruhan_pajak > xx){
						// 	$('#ongkos_kirim').val('GRATIS');
						// }else{
						// 	$('#ongkos_kirim').val(temp);
						// }
					},500);	
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
			DB::table('tb_produk_stok')->where('id',$id)->update([
				'deleted_by'	=> CRUDBooster::myName()
			]);
	    }



	    //By the way, you can still create your own method in here... :) 


	}
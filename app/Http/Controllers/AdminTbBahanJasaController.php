<?php namespace App\Http\Controllers;

	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;

	class AdminTbBahanJasaController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "tb_bahan_jasa";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Kode","name"=>"kode"];
			$this->col[] = ["label"=>"Barcode","name"=>"barcode"];
			$this->col[] = ["label"=>"Keterangan","name"=>"keterangan"];
			$this->col[] = ["label"=>"Jenis","name"=>"id_jenis","join"=>"tb_general,keterangan"];
			$this->col[] = ["label"=>"Merek","name"=>"id_merek","join"=>"tb_general,keterangan"];
			// $this->col[] = ["label"=>"Satuan","name"=>"id_satuan","join"=>"tb_general,keterangan"];
			$this->col[] = ["label"=>"Stok","name"=>"stok"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('tb_bahan_jasa')->max('id') + 1;
			$kode = 'BHN'.str_pad($kode,5,0,STR_PAD_LEFT);

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode','name'=>'kode','type'=>'text','width'=>'col-sm-10','readonly'=>true,'value'=>$kode];
			$this->form[] = ['label'=>'Barcode','name'=>'barcode','type'=>'text','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Keterangan','name'=>'keterangan','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			// $this->form[] = ['label'=>'Jenis','name'=>'id_jenis','type'=>'select2','validation'=>'integer|required','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 3'];
			$this->form[] = ['label'=>'Satuan','name'=>'id_satuan','type'=>'select2','validation'=>'required','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 1'];			
			$this->form[] = ['label'=>'Merek','name'=>'id_merek','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 6'];
			$this->form[] = ['label'=>'Kategori','name'=>'id_kategori','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 15'];			
			// $this->form[] = ['label'=>'Harga','name'=>'harga','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Satuan','name'=>'id_satuan','type'=>'select','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 1'];
			$columns[] = ['label'=>'Pengali','name'=>'pengali','type'=>'number'];
			$this->form[] = ['label'=>'Satuan Bertingkat','name'=>'bahan_jasa_satuan','type'=>'child','columns'=>$columns,'table'=>'tb_bahan_jasa_satuan','foreign_key'=>'id_produk'];
			// $this->form[] = ['label'=>'Satuan Pemakaian','name'=>'id_satuan','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 1'];
			// $this->form[] = ['label'=>'Stok','name'=>'stok','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			// $this->form[] = ['label'=>'Status','name'=>'status','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			// $this->form[] = ['label'=>'Gambar','name'=>'gambar','type'=>'upload','validation'=>'required|image|max:3000','width'=>'col-sm-10','help'=>'Tipe file yang didukung: JPG, JPEG, PNG, GIF, BMP'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Kode","name"=>"kode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Keterangan","name"=>"keterangan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Jenis","name"=>"id_jenis","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"jenis,id"];
			//$this->form[] = ["label"=>"Merek","name"=>"id_merek","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"merek,id"];
			//$this->form[] = ["label"=>"Kategori","name"=>"id_kategori","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"kategori,id"];
			//$this->form[] = ["label"=>"Satuan","name"=>"id_satuan","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"satuan,id"];
			//$this->form[] = ["label"=>"Stok","name"=>"stok","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Harga","name"=>"harga","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Gambar","name"=>"gambar","type"=>"upload","required"=>TRUE,"validation"=>"required|image|max:3000","help"=>"Tipe file yang didukung: JPG, JPEG, PNG, GIF, BMP"];
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
			$this->sub_module[] = ['title'=>'Kartu Stok','path'=>'tb_bahan_jasa_stok','parent_columns'=>'kode,keterangan','foreign_key'=>'id_produk','button_color'=>'danger','button_icon'=>'fa fa-cubes'];

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
			$this->index_button[] = ['label'=>'Opname Stok','url'=>CRUDBooster::mainpath("opname"),"icon"=>"fa fa-file-o"];


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
	        $this->script_js = NULL;


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
			// dd($postdata);
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



	    //By the way, you can still create your own method in here... :) 
		public function getOpname()
		{
			$data = [];
			$data['url'] = CRUDBooster::apiPath();
			$data['page_title'] = 'Opname Stok Bahan Jasa';			
			$this->cbView('opname.bahan', $data);
		}

		public function postOpname(Request $request)
		{
			$param = $request->all();	

			DB::table('tb_bahan_jasa')->where('id', $param['id'])->update(['stok' => $param['stok_akhir']]);			
			DB::table('tb_bahan_jasa_stok')->insert([
				'id_produk'		=> $param['id'],
				'tanggal'		=> date('Y-m-d H:i:s'),
				'stok_masuk'	=> $param['stok_masuk'],
				'stok_keluar'	=> $param['stok_keluar'],
				'keterangan'	=> $param['keterangan'],
				'created_at'	=> date('Y-m-d H:i:s'),
				'created_by'	=> CRUDBooster::myName()
			]);

			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Stok ".$param['keterangan']." berhasil di simpan","primary");
		}

	}
<?php namespace App\Http\Controllers;

	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use DNS1D;
	use DNS2D;

	class AdminTbProdukController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "tb_produk";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];			
			$this->col[] = ["label"=>"Gambar","name"=>"gambar",'image'=>true];
			$this->col[] = ["label"=>"Kategori","name"=>"id_kategori","join"=>"tb_general,keterangan"];
			// $this->col[] = ["label"=>"Kode","name"=>"kode"];
			$this->col[] = ["label"=>"Keterangan","name"=>"keterangan"];			
			$this->col[] = ["label"=>"Stok","name"=>"stok"];
			$this->col[] = ["label"=>"Satuan","name"=>"id_satuan","join"=>"tb_general,keterangan"];
			$this->col[] = ["label"=>"Harga","name"=>"harga",'callback_php'=>'number_format($row->harga,0,",",".")'];
			$this->col[] = ["label"=>"Status","name"=>"status",'callback'=>function($row){
				$status = ($row->status == 1) ? 'DIJUAL' : 'TIDAK DIJUAL';
				return $status; 
			}];
			# END COLUMNS DO NOT REMOVE THIS LINE

			$kode = DB::table('tb_produk')->max('id') + 1;
			$kode = 'PRD/'.str_pad($kode,5,0,STR_PAD_LEFT);

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Kode','name'=>'kode','type'=>'hidden','validation'=>'required|min:1|max:255','width'=>'col-sm-10','readonly'=>true,'value'=>$kode];			
			$this->form[] = ['label'=>'Jenis','name'=>'id_jenis','type'=>'radio','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 3','value'=>5,'inline'=>true];
			$this->form[] = ['label'=>'Nama Produk','name'=>'keterangan','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','placeholder'=>'Silahkan beri nama produk'];
			// $this->form[] = ['label'=>'Jenis','name'=>'id_jenis','type'=>'select2','validation'=>'integer|required','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 3'];
			$this->form[] = ['label'=>'Merek','name'=>'id_merek','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 6'];
			$this->form[] = ['label'=>'Kategori','name'=>'id_kategori','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 2'];
			$this->form[] = ['label'=>'Satuan','name'=>'id_satuan','type'=>'select2','width'=>'col-sm-10','datatable'=>'tb_general,keterangan','datatable_where'=>'id_tipe = 1'];
			$this->form[] = ['label'=>'Harga','name'=>'harga','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10','value'=>0];
			$this->form[] = ['label'=>'Dijual','name'=>'status','type'=>'radio','width'=>'col-sm-10','dataenum'=>'1|Ya;0|Tidak','value'=>1,'inline'=>true];
			$this->form[] = ['label'=>'Gambar','name'=>'gambar','type'=>'upload','validation'=>'image|max:500','encrypt'=>true,'width'=>'col-sm-10','help'=>'*maksimal ukuran file 500KB'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Kode","name"=>"kode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Keterangan","name"=>"keterangan","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Kategori","name"=>"kategori","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Jenis","name"=>"jenis","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Satuan","name"=>"satuan","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Harga","name"=>"harga","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
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
			// $this->sub_module[] = ['label'=>'','path'=>'tb_produk_detail','parent_columns'=>'kode,keterangan','foreign_key'=>'kode_produk','button_color'=>'info','button_icon'=>'fa fa-bars','showIf'=>'[jenis] == 22'];
			$this->sub_module[] = ['title'=>'Kartu Stok','path'=>'tb_produk_stok','parent_columns'=>'kode,keterangan','foreign_key'=>'id_produk','button_color'=>'danger','button_icon'=>'fa fa-cubes'];

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
			$this->button_selected[] = ['label'=>'Print Barcode','icon'=>'fa fa-print','name'=>'print_barcode'];

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
			if($button_name == 'print_barcode') {
				$barcode = array();
				foreach ($id_selected as $value) {
					$produk = CRUDBooster::first('tb_produk',$value);					
					$barcode[] = $produk->kode;
					$text[] = $produk->keterangan;
				}								
				$data = [
					'count'		=> sizeof($barcode),
					'barcode'	=> $barcode,
					'text'		=> $text
				];
				CRUDBooster::redirect('print_barcode',$data);
			}
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
			$postdata['stok'] = ($postdata['id_jenis_jasa'] == 37) ? 1000000 : 0;			
			$postdata['created_by'] = CRUDBooster::myName();
			$postdata['id_kategori'] = (empty($postdata['id_kategori'])) ? 38 : $postdata['id_kategori'];
			$postdata['id_satuan'] = (empty($postdata['id_satuan'])) ? 39 : $postdata['id_satuan'];
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
			$stok = array(
				'id_produk'	=> $id,
				'tanggal'		=> date('Y-m-d H:i:d'),
				'stok_masuk'	=> 0,
				'stok_keluar'	=> 0,
				'keterangan'	=> 'Stok produk baru',
				'created_by'	=> 'by Sistem'
			);

			DB::table('tb_produk_stok')->insert($stok);
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
			$postdata['id_kategori'] = (empty($postdata['id_kategori'])) ? 38 : $postdata['id_kategori'];
			$postdata['id_satuan'] = (empty($postdata['id_satuan'])) ? 39 : $postdata['id_satuan'];
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
			$postdata['updated_by'] = CRUDBooster::myName();
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
			DB::table('tb_produk')->where('id',$id)->update([
				'deleted_by'	=> CRUDBooster::myName()
			]);
	    }



	    //By the way, you can still create your own method in here... :)
		public function getOpname()
		{
			$data = [];
			$data['url'] = CRUDBooster::apiPath();
			$data['page_title'] = 'Opname Stok';
			// $data['table'] = Datatables::of(DB::table('tb_insentif')->whereNull('deleted_at'))->make(true);
			$this->cbView('opname.view', $data);
		}

		public function postOpname(Request $request)
		{
			$param = $request->all();	

			DB::table('tb_produk')->where('id', $param['id'])->update(['stok' => $param['stok_akhir']]);			
			DB::table('tb_produk_stok')->insert([
				'id_produk'		=> $param['id'],
				'tanggal'		=> date('Y-m-d H:i:s'),
				'stok_masuk'	=> $param['stok_masuk'],
				'stok_keluar'	=> $param['stok_keluar'],
				'keterangan'	=> 'Penyesuaian stok | stok opname',
				'created_at'	=> date('Y-m-d H:i:s'),
				'created_by'	=> CRUDBooster::myName()
			]);

			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"Stok ".$param['keterangan']." berhasil di simpan","primary");
		}

	}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiProdukController extends Controller
{
    public function data(Request $request)
    {
        $path = CRUDBooster::publicPath();
        $param = $request->all();

        $query = DB::table('tb_produk as pd')
                    ->join('tb_general as jn','jn.id','=','pd.id_jenis')
                    ->join('tb_general as kt','kt.id','=','pd.id_kategori')
                    ->join('tb_general as st','st.id','=','pd.id_satuan')
                    ->select('pd.id','pd.kode','pd.keterangan','pd.stok','pd.harga','pd.gambar','jn.keterangan as jenis','kt.keterangan as kategori','st.keterangan as satuan')                    
                    ->where('pd.status',1)
                    ->whereNull('pd.deleted_at');
        
        if(!empty($param)){
            foreach ($param as $key => $value) {
                $where['pd.'.$key] = $value;
            }
            $query->where($where);
        }

        $json = $query->get();
        foreach ($json as $value) {
            if(!empty($value->gambar))
                $value->gambar = $path . $value->gambar;
            else
                $value->gambar = $path . 'logo.png';
        }
        return $json;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiBahanController extends Controller
{
    public function data(Request $request)
    {
        $path = CRUDBooster::publicPath();
        $param = $request->all();

        $query = DB::table('tb_bahan_jasa as pd')
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

    public function single(Request $request)
    {
        $param = $request->all();
        $query = DB::table('tb_bahan_jasa as bj')
                        ->join('tb_general as gn','gn.id','bj.id_satuan')
                        ->select('bj.*','gn.keterangan as satuan')
                        ->where('bj.id',$param['id'])
                        ->first();

        return response()->json($query);
        
    }

    public function search(Request $request)
    {
        $param = $request->all();
        if(empty($param)){
            return NULL;
        }else{
            $query = DB::table('tb_bahan_jasa as b')                          
                            ->join('tb_general as g','g.id','=','b.id_satuan')
                            ->select('b.id','b.kode','b.barcode','b.keterangan','b.stok','g.keterangan as satuan')
                            ->where([
                                ['b.kode','LIKE','%'.$param['kode'].'%'],                
                                ['b.deleted_at', NULL],                        
                            ])
                            ->orWhere('b.barcode','LIKE','%'.$param['kode'].'%')
                            ->get();                    

            return $query;
        }
    }
}

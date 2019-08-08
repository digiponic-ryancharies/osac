<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiJasaController extends Controller
{
    public function data(Request $request)
    {
        $path = CRUDBooster::publicPath();
        $param = $request->all();

        $query = DB::table('tb_jasa as js')
                    ->join('tb_general as jn','jn.id','=','js.id_jenis_jasa')
                    ->select('js.id','js.kode','js.keterangan','js.deskripsi','js.gambar','jn.keterangan as jenis_jasa')
                    ->whereNull('js.deleted_at');
        
        if(!empty($param)){
            foreach ($param as $key => $value) {
                $filter['js.'.$key] = $value;
            }            
            $query->where($filter);
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

    public function harga(Request $request)
    {
        $param = $request->all();
        $id_jasa = $param['id_jasa'];

        if(empty($id_jasa)){
            return 'input parameter id_jasa';
        }else {
            $query = DB::table('tb_harga_jasa as j')
                        ->join('tb_general as jn','jn.id','=','j.id_jenis_kendaraan')
                        ->select('j.id','jn.keterangan as jenis_kendaraan','j.harga')
                        ->where('j.id_jasa', $id_jasa)
                        ->get();     

            return $query;
        }    
    }

    public function hargaPerKendaraan(Request $request)
    {
        $param = $request->all();  

        $kendaraan = CRUDBooster::first('tb_kendaraan', $param['id_jenis_kendaraan']);  
        $param['id_jenis_kendaraan'] = $kendaraan->id_jenis_kendaraan;

        $query = DB::table('tb_harga_jasa')
                        ->where($param)
                        ->first();

        return response()->json($query, 200);
    }

    public function durasi(Request $request)
    {
        $param = $request->all();
        $id_jasa = $param['id_jasa'];

        if(empty($id_jasa)){
            return 'input parameter id_jasa';
        }else {
            $query = DB::table('tb_durasi_jasa as dj')
                        ->join('tb_general as jn','jn.id','=','dj.id_jenis_kendaraan')
                        ->select('dj.id','jn.keterangan as jenis_kendaraan','dj.durasi')
                        ->where('dj.id_jasa', $id_jasa)
                        ->get();
        }

        return $query;
    }
}

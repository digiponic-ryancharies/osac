<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiKendaraanController extends Controller
{
    public function data(Request $request)
    {
        $path = CRUDBooster::publicPath();
        $param = $request->all();

        $query = DB::table('tb_kendaraan as k')
                    ->join('tb_general as jk','jk.id','=','k.id_jenis_kendaraan')
                    ->join('tb_merek_kendaraan as mk','mk.id','=','k.id_merek_kendaraan')
                    ->select('k.id','k.kode','k.keterangan','k.gambar','k.id_jenis_kendaraan','jk.keterangan as jenis_kendaraan','mk.keterangan as merek_kendaraan')
                    ->whereNull('k.deleted_at');

        if(!empty($param)){
            foreach ($param as $key => $value) {
                $filter['k.'.$key] = $value;
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
}

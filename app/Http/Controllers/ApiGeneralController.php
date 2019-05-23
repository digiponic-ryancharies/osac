<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use CRUDBooster;

class ApiGeneralController extends Controller
{
    public function data(Request $request)
    {
        $path = CRUDBooster::publicPath();
        $param = $request->all();

        $query = DB::table('tb_general as gn')
                    ->select('gn.id','gn.kode','gn.keterangan','gn.gambar')
                    ->whereNull('gn.deleted_at');
        
        if(!empty($param)){
            foreach ($param as $key => $value) {
                $where['gn.'.$key] = $value;
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiMerekKendaraanController extends Controller
{
    public function data()
    {
        $path = CRUDBooster::publicPath();

        $query = DB::table('tb_merek_kendaraan as mk')
                    ->select('mk.id','mk.kode','mk.keterangan','mk.gambar')
                    ->whereNull('mk.deleted_at');

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

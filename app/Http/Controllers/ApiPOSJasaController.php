<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiPOSJasaController extends Controller
{
    public function data(Request $request)
    {
        /*
            {
                "id": ,
                "kode": "",
                "tanggal": "",
                "nama_pelanggan": ""
            }
        */

        $param = $request->all();

        $query = DB::table('tb_penjualan as pj');
        if (empty($param)) {
            $now = date('Y-m-d');
            $query->whereDate('pj.tanggal',$now);                        
        }else{
            foreach ($param as $key => $value) {
                $filter['pj.'.$key] = $value;
            }
            $query->where($filter);
        }

        $data = $query->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        /*
            {
                "id_pelanggan": "",
                "nama_pelanggan": "",
                "id_merek_kendaraan": "",
                "merek_kendaraan": "",
                "id_kendaraan": "",
                "nama_kendaraan": "",
                "nomor_polisi": "",
                "subtotal": "",
                "diskon_tipe": "",
                "diskon_nominal": "",
                "total": "",
                "total_durasi": "",
                "jasa_detail": [{
                    "id_jasa": "",
                    "nama_jasa" : "",
                    "harga": "",
                    "durasi": "",
                    "subtotal": ""
                }]
            }   
            
            V2
            {
                "id_merek_kendaraan": 1,
                "merek_kendaraan": "HONDA",
                "id_kendaraan": 1,
                "nama_kendaraan": "BRIO",
                "nomor_polisi": "N 4759 GH",
                "subtotal": 50000,
                "diskon_tipe": 0,
                "diskon_nominal": 0,
                "total": 50000,
                "jasa_detail": [{
                    "id_jasa": 1,
                    "nama_jasa" : "EVAPORATOR CLEANING",
                    "durasi": 30,
                    "harga": 50000,
                    "subtotal": 50000
                }]
            }            

        */
        $param = $request->json()->all();

        $pos = $param;
        $pos_detail = $pos['jasa_detail'];
        unset($pos['jasa_detail']);

        $kode = DB::table('tb_penjualan_jasa')->max('id') + 1;
        $kode = 'POSJS/'.date('dmy').'/'.str_pad($kode,5,0,STR_PAD_LEFT);
        $timestamp = date('Y-m-d H:i:s');

        $pos['kode'] = $kode;
        $pos['tanggal'] = $timestamp;
        $pos['created_at'] = $timestamp;
        $pos['created_by'] = 'by Sistem POSJS';

        $newIdPos = DB::table('tb_penjualan_jasa')->insertGetId($pos);    

        $produk_stok = array();

        $count = count($pos_detail);
        for ($i=0; $i < $count; $i++) { 
            $pos_detail[$i]['id_penjualan_jasa'] = $newIdPos;
            $pos_detail[$i]['kode_penjualan_jasa'] = $kode;
        }

        $stat_pos = DB::table('tb_penjualan_jasa_detail')->insert($pos_detail);

        if ($stat_pos) {
            $data = 'success';
            return response()->json($data, 200);
        }else{
            $data = 'failed';
            return response()->json($data, 201);
        }
    }    
}

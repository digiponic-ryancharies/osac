<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use CRUDBooster;
use DB;

class ApiPOSController extends Controller
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

        $query = DB::table('tb_penjualan_pos as pos');
        if (empty($param)) {
            $now = date('Y-m-d');
            $query->whereDate('pos.tanggal',$now);                        
        }else{
            foreach ($param as $key => $value) {
                $filter['pos.'.$key] = $value;
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
                "nama_pelanggan": "",
                "subtotal": "",
                "diskon_tipe": "",
                "diskon_nominal": "",
                "total": "",
                "pos_detail": [{
                    "id_produk": "",
                    "nama_produk" : "",
                    "quantity": "",
                    "harga": "",
                    "subtotal": ""
                }]
            }        
        */
        $param = $request->json()->all();

        $pos = $param;
        $pos_detail = $pos['pos_detail'];
        unset($pos['pos_detail']);

        $kode = DB::table('tb_penjualan_pos')->max('id') + 1;
        $kode = 'POS/'.date('dmy').'/'.str_pad($kode,5,0,STR_PAD_LEFT);
        $timestamp = date('Y-m-d H:i:s');

        $pos['kode'] = $kode;
        $pos['tanggal'] = $timestamp;
        $pos['created_at'] = $timestamp;
        $pos['created_by'] = 'by Sistem POS';

        $newIdPos = DB::table('tb_penjualan_pos')->insertGetId($pos);    

        $produk_stok = array();

        $count = count($pos_detail);
        for ($i=0; $i < $count; $i++) { 
            $pos_detail[$i]['id_penjualan_pos'] = $newIdPos;
            $pos_detail[$i]['kode_penjualan_pos'] = $kode;

            array_push($produk_stok, array(
                'tanggal'		=> $timestamp,
                'id_produk'		=> $pos_detail[$i]['id_produk'],
                'stok_masuk'	=> 0,
                'stok_keluar'	=> $pos_detail[$i]['quantity'],
                'keterangan'	=> 'Pengurangan stok dari penjualan '.$kode,
                'created_at'	=> $timestamp,
                'created_by'	=> 'by Sistem POS'                
            ));

            $produk = CRUDBooster::first('tb_produk',$pos_detail[$i]['id_produk']);
            $update_stok = $produk->stok - $pos_detail[$i]['quantity'];
            DB::table('tb_produk')->where('id', $pos_detail[$i]['id_produk'])->update(['stok' => $update_stok]);

        }

        $stat_pos = DB::table('tb_penjualan_pos_detail')->insert($pos_detail);
        $stat_stok = DB::table('tb_produk_stok')->insert($produk_stok);

        if ($stat_pos && $stat_stok) {
            $data = 'success';
            return response()->json($data, 200);
        }else{
            $data = 'failed';
            return response()->json($data, 201);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use CRUDBooster;

class ApiPelangganController extends Controller
{
    public function data(Request $request)
    {
        $param = $request->all();

        $query = DB::table('tb_pelanggan as p')
            ->select('p.id','p.kode','p.nama','p.email','p.telepon','p.telepon2');

        if (!empty($param)) {
            foreach ($param as $key => $value) {
                $filter['p.'.$key] = $value;
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
                "nama": "",
                "email": "",
                "password": "",
                "telepon": "",
                "telepon2": ""
            }            
        */
        $kode = DB::table('tb_pelanggan')->max('id') + 1;
        $kode = 'PLNGN/'.str_pad($kode,4,0,STR_PAD_LEFT);	

        $param = $request->all();
        $param['kode'] = $kode;
        $param['password'] = bcrypt($param['password']);
        $param['telepon'] = (empty($param['telepon'])) ? NULL : $param['telepon'];
        $param['created_at'] = date('Y-m-d H:i:s');
        $param['created_by'] = $param['nama'];

        $new = DB::table('tb_pelanggan')->insertGetId($param);
        if(!empty($new)){
            $status = 200;
            $response = DB::table('tb_pelanggan')->select('id','kode','nama','email','telepon','telepon2')->where('id',$new)->first();
            $response = json_decode(json_encode($response), true);
        }else{
            $status = 201;
            $response = 'Gagal menambahkan pelanggan';
        }

        return response()->json($response, $status);
    }

    public function kendaraan(Request $request)
    {
        $param = $request->all();
        $id_pelanggan = $param['id_pelanggan'];

        if (empty($id_pelanggan)) {
            return 'input parameter id_pelanggan';
        }else {
            $query = DB::table('tb_pelanggan_kendaraan as pk')
                        ->join('tb_merek_kendaraan as mk','mk.id','=','pk.id_merek_kendaraan')
                        ->join('tb_kendaraan as k','k.id','=','pk.id_kendaraan')
                        ->select('pk.id','mk.id as id_merek_kendaraan','mk.keterangan as merek_kendaraan','k.id as id_kendaraan','k.keterangan as nama_kendaraan','pk.nomor_polisi','pk.nomor_rangka','pk.tahun','pk.warna')
                        ->where('pk.id_pelanggan', $id_pelanggan);
        }
        
        $data = $query->get();
        return response()->json($data);
    }

    public function storeKendaraan(Request $request)
    {   
        /*
            {
                "id_pelanggan": "",
                "nama_pelanggan": "",
                "id_merek_kendaraan": "",
                "id_kendaraan": "",
                "nomor_polisi": "",
                "nomor_rangka": "",
                "tahun": "",
                "warna": ""
            }        
        */

        $param = $request->all();
        $param['created_at'] = date('Y-m-d H:i:s');
        $param['created_by'] = $param['nama_pelanggan'];
        unset($param['nama_pelanggan']);

        $new = DB::table('tb_pelanggan_kendaraan')->insertGetId($param);
        if(!empty($new)){
            $status = 200;
            $response = 'Berhasil menambahkan kendaraan';
        }else{
            $status = 201;
            $response = 'Gagal menambahkan kendaraan';
        }

        return response()->json($response, $status);
    }

    public function alamat(Request $request)
    {
        $param = $request->all();
        $id_pelanggan = $param['id_pelanggan'];

        if (empty($id_pelanggan)) {
            return 'input parameter id_pelanggan';
        }else {
            $query = DB::table('tb_pelanggan_alamat as pa')
                        ->join('tb_provinsi as p','p.id','=','pa.id_provinsi')
                        ->join('tb_kota as k','k.id','=','pa.id_kota')
                        ->join('tb_kecamatan as kc','kc.id','=','pa.id_kecamatan')
                        ->select('pa.id','pa.kode','pa.keterangan','p.id as id_provinsi','p.keterangan as nama_provinsi','k.id as id_kota','k.keterangan as nama_kota','kc.id as id_kecamatan','kc.keterangan as nama_kecamatan','pa.alamat_lengkap')
                        ->where('pa.id_pelanggan', $id_pelanggan);
        }
        
        $data = $query->get();
        return response()->json($data);
    }

    public function storeAlamat(Request $request)
    {   
        /*
            {
                "id_pelanggan": "",
                "nama_pelanggan": "",
                "keterangan": "",
                "id_provinsi": "",
                "id_kota": "",
                "id_kecamatan": "",
                "alamat_lengkap": "",
            }        
        */

        $kode = DB::table('tb_pelanggan_alamat')->max('id') + 1;
        $kode = 'ALMT/'.str_pad($kode,4,0,STR_PAD_LEFT);	

        $param = $request->all();
        $param['kode'] = $kode;
        $param['created_at'] = date('Y-m-d H:i:s');
        $param['created_by'] = $param['nama_pelanggan'];
        unset($param['nama_pelanggan']);

        $new = DB::table('tb_pelanggan_alamat')->insertGetId($param);
        if(!empty($new)){
            $status = 200;
            $response = 'Berhasil menambahkan alamat';
        }else{
            $status = 201;
            $response = 'Gagal menambahkan alamat';
        }

        return response()->json($response, $status);
    }

}

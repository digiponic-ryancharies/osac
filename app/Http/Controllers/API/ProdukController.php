<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public $successStatus = 200;

    public function all()
    {
        $path = url('/');
        //$param = $request->all();

        $query = DB::table('tb_produk as pd')
            ->join('tb_general as jn', 'jn.id', '=', 'pd.id_jenis')
            ->join('tb_general as m', 'm.id', '=', 'pd.id_merek')
            ->join('tb_general as kt', 'kt.id', '=', 'pd.id_kategori')
            ->join('tb_general as st', 'st.id', '=', 'pd.id_satuan')
            ->select('pd.id', 'pd.kode', 'pd.keterangan', 'pd.stok', 'pd.harga', 'pd.gambar', 'jn.keterangan as jenis', 'kt.keterangan as kategori', 'm.keterangan as merk', 'st.keterangan as satuan')
            ->where('pd.status', 1)
            ->whereNull('pd.deleted_at')
            ->get();

        foreach ($query as $value) {
            if ($value->gambar == null) {
                $value->gambar = $path . '/img/logo.png';
            } else {
                $value->gambar = $path . '/' . $value->gambar;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Produk', 'data' => $query], $this->successStatus);
    }

    public function category()
    {
        $query = DB::table('tb_general')
            ->select('id', 'keterangan')
            ->where('id_tipe', 2)
            ->whereNull('deleted_at')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Kategori Produk', 'data' => $query], $this->successStatus);
    }

    public function byCategory($catid)
    {
        $path = url('/');
        //$param = $request->all();

        if ($catid == 0){
            $query = DB::table('tb_produk as pd')
                ->join('tb_general as jn', 'jn.id', '=', 'pd.id_jenis')
                ->join('tb_general as m', 'm.id', '=', 'pd.id_merek')
                ->join('tb_general as kt', 'kt.id', '=', 'pd.id_kategori')
                ->join('tb_general as st', 'st.id', '=', 'pd.id_satuan')
                ->select('pd.id', 'pd.kode', 'pd.keterangan', 'pd.stok', 'pd.harga', 'pd.gambar', 'jn.keterangan as jenis', 'kt.keterangan as kategori', 'm.keterangan as merk', 'st.keterangan as satuan')
                ->where('pd.status', 1)
                ->whereNull('pd.deleted_at')
                ->get();
        } else {
            $query = DB::table('tb_produk as pd')
                ->join('tb_general as jn', 'jn.id', '=', 'pd.id_jenis')
                ->join('tb_general as m', 'm.id', '=', 'pd.id_merek')
                ->join('tb_general as kt', 'kt.id', '=', 'pd.id_kategori')
                ->join('tb_general as st', 'st.id', '=', 'pd.id_satuan')
                ->select('pd.id', 'pd.kode', 'pd.keterangan', 'pd.stok', 'pd.harga', 'pd.gambar', 'jn.keterangan as jenis', 'kt.keterangan as kategori', 'm.keterangan as merk', 'st.keterangan as satuan')
                ->where('pd.status', 1)
                ->where('pd.id_kategori', $catid)
                ->whereNull('pd.deleted_at')
                ->get();
        }

        foreach ($query as $value) {
            if ($value->gambar == null) {
                $value->gambar = $path . '/img/logo.png';
            } else {
                $value->gambar = $path . '/' . $value->gambar;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Produk', 'data' => $query], $this->successStatus);
    }
}

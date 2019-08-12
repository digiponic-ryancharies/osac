<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use CRUDBooster;

class JasaController extends Controller
{

    public $successStatus = 200;

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function jenisJasaList()
    {
        $data = DB::table('tb_general')
            ->select('id', 'keterangan')
            ->where('id_tipe', 4)
            ->orderBy('id')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Jenis Jasa', 'data' => $data], $this->successStatus);
    }

    public function jasaList($idJenisJasa)
    {
        $path = url('/');

        if ($idJenisJasa == 0) {
            $data = DB::table('tb_jasa as j')
                ->join('tb_general as jj', 'jj.id', '=', 'j.id_jenis_jasa')
                ->select('j.id', 'j.kode', 'j.keterangan as nama', 'jj.keterangan as jenis', 'j.gambar', 'j.deskripsi')
                ->orderBy('jj.keterangan')
                ->get();
        } else {
            $data = DB::table('tb_jasa as j')
                ->join('tb_general as jj', 'jj.id', '=', 'j.id_jenis_jasa')
                ->select('j.id', 'j.kode', 'j.keterangan as nama', 'jj.keterangan as jenis', 'j.gambar', 'j.deskripsi')
                ->where('jj.id', $idJenisJasa)
                ->orderBy('jj.keterangan')
                ->get();
        }

        // grouping array
        /*$result = array();
        foreach ($data as $element) {
            $result[] = (array)$element;
        }
        $result = collect($result)->groupBy('NAMA_JENIS_JASA');*/
        $result = [];
        foreach ($data as $value) {
            if ($value->gambar == null) {
                $value->gambar = $path . '/img/logo.png';
            } else {
                $value->gambar = $path . '/' . $value->gambar;
            }

            $result[] = $value;
        }
        return response()->json(['error' => false, 'msg' => 'Daftar Jasa', 'data' => $result], $this->successStatus);
    }

    public function hargaJasaList()
    {
        $data = DB::table('tb_harga_jasa as hj')
            ->join('tb_jasa as j', 'j.id', '=', 'hj.id_jasa')
            ->join('tb_general as jk', 'jk.id', '=', 'hj.id_jenis_kendaraan')
            ->select('hj.id', 'j.keterangan as nama', 'jk.keterangan as jenis_kendaraan', 'hj.harga')
            ->orderBy('j.id')
            ->orderBy('jk.id')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Harga Jasa', 'data' => $data], $this->successStatus);
    }

    public function durasiJasaList()
    {
        $data = DB::table('tb_durasi_jasa as dj')
            ->join('tb_jasa as j', 'j.id', '=', 'dj.id_jasa')
            ->join('tb_general as jk', 'jk.id', '=', 'dj.id_jenis_kendaraan')
            ->select('dj.id', 'j.keterangan as nama', 'jk.keterangan as jenis_kendaraan', 'dj.durasi')
            ->orderBy('j.id')
            ->orderBy('jk.id')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Durasi Jasa', 'data' => $data], $this->successStatus);
    }
}
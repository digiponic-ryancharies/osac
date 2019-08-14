<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;

class KendaraanController extends Controller
{

    public $successStatus = 200;

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function allByMerk($merkid)
    {
        $path = url('/');
        $data = DB::table('tb_kendaraan as k')
            ->join('tb_merek_kendaraan as mk', 'mk.id', '=', 'k.id_merek_kendaraan')
            ->join('tb_general as jk', 'jk.id', '=', 'k.id_jenis_kendaraan')
            ->select('k.id', 'k.kode', 'mk.keterangan as merk', 'jk.keterangan as jenis', 'k.keterangan', 'k.gambar')
            ->where('k.id_merek_kendaraan', $merkid)
            ->orderBy('k.kode')
            ->get();

        foreach ($data as $value) {
            if ($value->gambar == null) {
                $value->gambar = $path . '/img/logo.png';
            } else {
                $value->gambar = $path . '/' . $value->gambar;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Kendaraan', 'data' => $data], $this->successStatus);
    }

    public function merkList()
    {
        $path = url('/');
        $data = DB::table('tb_merek_kendaraan')
            ->select('id', 'kode', 'keterangan', 'gambar')
            ->get();

        foreach ($data as $value) {
            if ($value->gambar == null) {
                $value->gambar = $path . '/img/logo.png';
            } else {
                $value->gambar = $path . '/' . $value->gambar;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Merk Kendaraan', 'data' => $data], $this->successStatus);

    }

    public function vehicleType()
    {
        $data = DB::table('tb_general')
            ->select('id', 'keterangan')
            ->where('id_tipe', 8)
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Jenis Kendaraan', 'data' => $data], $this->successStatus);

    }

    public function tambah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merk'            => 'required',
            'jenis'           => 'required',
            'warna'           => 'required',
            //            'no_polisi'       => 'required|regex:/^([A-Za-z]{1,3})+(\s)+([0-9]{1,4})+(\s)+([A-Za-z]{0,3})$/i',
            'no_polisi'       => 'required',
            'no_rangka'       => 'max:17',
            'no_mesin'        => 'max:20',
            'tahun_kendaraan' => 'required|numeric',
            'id_pelanggan'    => 'required|numeric',
        ], [
            'required'       => ':attribute harus diisi.',
            'unique'         => ':attribute harus unique.',
            'digits_between' => ':attribute harus 10 atau 12 digits',
            'regex'          => ':attribute harus sesuai format [ N 123 MLG ]',
            'numeric'        => ':attribute harus diisi angka',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'msg' => $validator->errors()], 401);
        }

        $input = $request->all();
        DB::table('tb_pelanggan_kendaraan')->insert(
            [
                'id'                 => DB::table('tb_pelanggan_kendaraan')->max('id') + 1,
                'id_pelanggan'       => $input['id_pelanggan'],
                'id_merek_kendaraan' => $input['merk'],
                'id_kendaraan'       => $input['jenis'],
                'warna'              => $input['warna'],
                'tahun'              => $input['tahun_kendaraan'],
                'nomor_polisi'       => trim(strtoupper($input['no_polisi']), " "),
                'nomor_rangka'       => $input['no_rangka'],
                'nomor_mesin'        => $input['no_mesin'],
                'created_at'         => date("Y-m-d H:i:s"),
                'created_by'         => 'api'
            ]
        );

        return response()->json(['error' => false, 'msg' => 'Data Berhasil Ditambahkan', 'data' => null], $this->successStatus);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merk'            => 'required',
            'jenis'           => 'required',
            'warna'           => 'required',
            //            'no_polisi'       => 'required|regex:/^([A-Za-z]{1,3})+(\s)+([0-9]{1,4})+(\s)+([A-Za-z]{0,3})$/i',
            'no_polisi'       => 'required',
            'no_rangka'       => 'max:17',
            'no_mesin'        => 'max:20',
            'tahun_kendaraan' => 'required|numeric',
            'id_kendaraan'    => 'required|numeric',
        ], [
            'required'       => ':attribute harus diisi.',
            'unique'         => ':attribute harus unique.',
            'digits_between' => ':attribute harus 10 atau 12 digits',
            'regex'          => ':attribute harus sesuai format [ N 123 MLG ]',
            'numeric'        => ':attribute harus diisi angka',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'msg' => $validator->errors()], 401);
        }

        $input = $request->all();
        $data =
            [
                'id_merek_kendaraan' => $input['merk'],
                'id_kendaraan'       => $input['jenis'],
                'warna'              => $input['warna'],
                'tahun'              => $input['tahun_kendaraan'],
                'nomor_polisi'       => trim(strtoupper($input['no_polisi']), " "),
                'nomor_rangka'       => $input['no_rangka'],
                'nomor_mesin'        => $input['no_mesin'],
                'updated_at'         => date("Y-m-d H:i:s"),
                'updated_by'         => 'api'
            ];

        DB::table('tb_pelanggan_kendaraan')
            ->where('id', $input['id_kendaraan'])
            ->update($data);

        return response()->json(['error' => false, 'msg' => 'Data Berhasil Diubah', 'data' => null], $this->successStatus);
    }

    public function hapus($id_kendaraan)
    {
        DB::table('tb_pelanggan_kendaraan')->where('id', $id_kendaraan)->delete();

        return response()->json(['error' => false, 'msg' => 'Kendaraan Dihapus', 'data' => null], $this->successStatus);
    }

    public function kendaraanPelanggan($id_pelanggan)
    {
        $data = DB::table('tb_pelanggan_kendaraan as k')
            ->join('tb_merek_kendaraan as mk', 'mk.id', '=', 'k.id_merek_kendaraan')
            ->join('tb_kendaraan as jk', 'jk.id', '=', 'k.id_kendaraan')
            ->select('k.id', 'k.id_pelanggan', 'mk.keterangan as merk', 'jk.keterangan as tipe', 'k.warna', 'k.nomor_polisi', 'k.tahun', 'k.nomor_rangka', 'k.nomor_mesin', 'k.created_at')
            ->where('id_pelanggan', $id_pelanggan)
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Kendaraan Pelanggan', 'data' => $data], $this->successStatus);

    }

    public function detail($idKendaraan)
    {
        $data = DB::table('tb_pelanggan_kendaraan as k')
            ->join('tb_merek_kendaraan as mk', 'mk.id', '=', 'k.id_merek_kendaraan')
            ->join('tb_kendaraan as kd', 'kd.id', '=', 'k.id_kendaraan')
            ->select('k.id', 'k.id_pelanggan', 'mk.keterangan as merk', 'kd.keterangan as tipe', 'k.warna', 'k.nomor_polisi', 'k.tahun', 'k.nomor_rangka', 'k.nomor_mesin', 'k.created_at')
            ->where('k.id', $idKendaraan)
            ->first();

        return response()->json(['error' => false, 'msg' => 'Detail Kendaraan', 'data' => $data], $this->successStatus);
    }
}
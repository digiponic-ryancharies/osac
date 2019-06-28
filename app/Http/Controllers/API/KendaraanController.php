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

    public function all()
    {
        $path = url('/');
        $data = DB::table('tbl_kendaraan as k')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'k.ID_KENDARAAN')
            ->join('tbl_merk_kendaraan as mk', 'mk.ID_MERK', '=', 'k.ID_MERK_KENDARAAN')
            ->select('k.ID_KENDARAAN', 'jk.KETERANGAN_JENIS_KENDARAAN', 'mk.NAMA_MERK', 'k.KODE', 'k.KETERANGAN', 'k.GAMBAR')
            ->orderBy('k.KODE')
            ->get();

        foreach ($data as $value) {
            if ($value->GAMBAR == null) {
                $value->GAMBAR = $path . '/img/logo.png';
            } else {
                $value->GAMBAR = $path . '/' . $value->GAMBAR;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Kendaraan', 'data' => $data], $this->successStatus);
    }

    public function merkList()
    {
        $data = DB::table('tbl_merk_kendaraan')
            ->select('ID_MERK', 'KODE_MERK', 'NAMA_MERK')
            ->get();
//        $data = (array) $data;
        return response()->json(['error' => false, 'msg' => 'Daftar Merk Kendaraan', 'data' => $data], $this->successStatus);

    }

    public function vehicleType()
    {
        $data = DB::table('tbl_jenis_kendaraan')
            ->select('ID_JENIS_KENDARAAN', 'KODE_JENIS_KENDARAAN', 'KETERANGAN_JENIS_KENDARAAN')
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Jenis Kendaraan', 'data' => $data], $this->successStatus);

    }

    public function detail($idKendaraan)
    {
        $data = DB::table('tbl_kendaraan_pelanggan as k')
            ->join('tbl_merk_kendaraan as mk', 'mk.ID_MERK', '=', 'k.ID_MERK')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'k.ID_JENIS_KENDARAAN')
            ->select('k.ID_KENDARAAN', 'k.ID_PELANGGAN', 'mk.NAMA_MERK', 'jk.KETERANGAN_JENIS_KENDARAAN', 'k.NAMA_KENDARAAN', 'k.WARNA_KENDARAAN', 'k.NOPOL_KENDARAAN', 'k.TAHUN_KENDARAAN', 'k.NO_RANGKA_KENDARAAN', 'k.NO_MESIN_KENDARAAN', 'k.CREATED_AT')
            ->where('ID_KENDARAAN', $idKendaraan)
            ->first();

        return response()->json(['error' => false, 'msg' => 'Detail Kendaraan', 'data' => $data], $this->successStatus);

    }

    public function tambah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis'           => 'required',
            'merk'            => 'required',
            'nama_kendaraan'  => 'required|min:4',
            'warna'           => 'required',
            'no_polisi'       => 'required|regex:/^([A-Za-z]{1,3})+(\s)+([0-9]{1,4})+(\s)+([A-Za-z]{0,3})$/i',
            'no_rangka'       => 'max:17',
            'no_mesin'        => 'max:20',
            'tahun_kendaraan' => 'required|numeric',
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
        DB::table('tbl_kendaraan_pelanggan')->insert(
            [
                'ID_KENDARAAN'        => DB::table('tbl_kendaraan_pelanggan')->max('ID_KENDARAAN') + 1,
                'ID_PELANGGAN'        => $input['id_pelanggan'],
                'ID_JENIS_KENDARAAN'  => $input['jenis'],
                'ID_MERK'             => $input['merk'],
                'NAMA_KENDARAAN'      => $input['nama_kendaraan'],
                'WARNA_KENDARAAN'     => $input['warna'],
                'TAHUN_KENDARAAN'     => $input['tahun_kendaraan'],
                'NOPOL_KENDARAAN'     => strtoupper($input['no_polisi']),
                'NO_RANGKA_KENDARAAN' => $input['no_rangka'],
                'NO_MESIN_KENDARAAN'  => $input['no_mesin'],
                'CREATED_AT'          => date("Y-m-d H:i:s"),
                'CREATED_BY'          => 'api'
            ]
        );

        return response()->json(['error' => false, 'msg' => 'Data Berhasil Ditambahkan', 'data' => null], $this->successStatus);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis'           => 'required',
            'merk'            => 'required',
            'nama_kendaraan'  => 'required|min:4',
            'warna'           => 'required',
            'no_polisi'       => 'required|regex:/^([A-Za-z]{1,3})+(\s)+([0-9]{1,4})+(\s)+([A-Za-z]{0,3})$/i',
            'no_rangka'       => 'max:17',
            'no_mesin'        => 'max:20',
            'tahun_kendaraan' => 'required|numeric',
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
                'ID_JENIS_KENDARAAN'  => $input['jenis'],
                'ID_MERK'             => $input['merk'],
                'NAMA_KENDARAAN'      => $input['nama_kendaraan'],
                'WARNA_KENDARAAN'     => $input['warna'],
                'TAHUN_KENDARAAN'     => $input['tahun_kendaraan'],
                'NOPOL_KENDARAAN'     => strtoupper($input['no_polisi']),
                'NO_RANGKA_KENDARAAN' => $input['no_rangka'],
                'NO_MESIN_KENDARAAN'  => $input['no_mesin'],
                'UPDATED_AT'          => date("Y-m-d H:i:s"),
                'UPDATED_BY'          => 'api'
            ];

        DB::table('tbl_kendaraan_pelanggan')
            ->where('ID_KENDARAAN', $input['id_kendaraan'])
            ->update($data);

        return response()->json(['error' => false, 'msg' => 'Data Berhasil Ditambahkan', 'data' => null], $this->successStatus);
    }

    public function hapus($id_kendaraan)
    {
        DB::table('tbl_kendaraan_pelanggan')->where('ID_KENDARAAN', $id_kendaraan)->delete();

        return response()->json(['error' => false, 'msg' => 'Kendaraan Dihapus', 'data' => null], $this->successStatus);
    }

    public function kendaraanPelanggan($id_pelanggan)
    {
        $data = DB::table('tbl_kendaraan_pelanggan as k')
            ->join('tbl_merk_kendaraan as mk', 'mk.ID_MERK', '=', 'k.ID_MERK')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'k.ID_JENIS_KENDARAAN')
            ->select('k.ID_KENDARAAN', 'k.ID_PELANGGAN', 'mk.NAMA_MERK', 'jk.KETERANGAN_JENIS_KENDARAAN', 'k.NAMA_KENDARAAN', 'k.WARNA_KENDARAAN', 'k.NOPOL_KENDARAAN', 'k.TAHUN_KENDARAAN', 'k.NO_RANGKA_KENDARAAN', 'k.NO_MESIN_KENDARAAN', 'k.CREATED_AT')
            ->where('ID_PELANGGAN', $id_pelanggan)
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Kendaraan Pelanggan', 'data' => $data], $this->successStatus);

    }
}
<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{

    public $successStatus = 200;

    function randomize($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $customer = DB::table('tbl_pelanggan')
            ->select('ID_PELANGGAN', 'KODE_PELANGGAN', 'NAMA_PELANGGAN', 'EMAIL_PELANGGAN', 'PASSWORD_PELANGGAN', 'TELEPON_PELANGGAN', 'CREATED_AT')
            ->where('EMAIL_PELANGGAN', request('email'))
            ->first();
        $customer = (array)$customer;
        if (password_verify(request('password'), $customer['PASSWORD_PELANGGAN'])) {
            session()->put('id_pelanggan', $customer['ID_PELANGGAN']);
            return response()->json(['error' => false, 'msg' => 'Login berhasil', 'data' => $customer], $this->successStatus);
        } else {
            return response()->json(['error' => true, 'msg' => 'Username / Password salah', 'data' => 0], 401);
        }

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'                  => 'required|min:4',
            'email'                 => 'required|email|unique:tbl_pelanggan,EMAIL_PELANGGAN',
            'telepon'               => 'required|digits_between:10,12',
            'nama_kendaraan'        => 'required',
            'warna'                 => 'required',
            'no_polisi'             => 'required|regex:/^([A-Za-z]{1,3})+(\s)+([0-9]{1,4})+(\s)+([A-Za-z]{0,3})$/i',
            'no_rangka'             => 'max:17',
            'no_mesin'              => 'max:17',
            'tahun_kendaraan'       => 'required|numeric',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
        ], [
            'required'       => ':attribute harus diisi.',
            'unique'         => ':attribute harus unique.',
            'digits_between' => ':attribute harus 10 atau 12 digits',
            'regex'          => ':attribute harus sesuai format [ N 123 MLG ]',
            'confirmed'      => ':attribute tidak sesuai',
            'numeric'        => ':attribute harus diisi angka',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'msg' => $validator->errors()], 401);
        }

        $input = $request->all();

        $id = DB::table('tbl_pelanggan')->max('ID_PELANGGAN') + 1;
        $kode = $this->randomize();
        DB::table('tbl_pelanggan')->insert(
            [
                'ID_PELANGGAN'       => $id,
                'KODE_PELANGGAN'     => $kode,
                'NAMA_PELANGGAN'     => $input['nama'],
                'EMAIL_PELANGGAN'    => $input['email'],
                'PASSWORD_PELANGGAN' => bcrypt($input['password']),
                'TELEPON_PELANGGAN'  => $input['telepon'],
                'CREATED_AT'         => date("Y-m-d H:i:s"),
                'CREATED_BY'         => 'api'
            ]
        );

        DB::table('tbl_kendaraan_pelanggan')->insert(
            [
                'ID_KENDARAAN'        => DB::table('tbl_kendaraan_pelanggan')->max('ID_KENDARAAN') + 1,
                'ID_PELANGGAN'        => $id,
                'ID_MERK'             => $input['merk'],
                'ID_JENIS_KENDARAAN'  => $input['jenis'],
                'NAMA_KENDARAAN'      => $input['nama_kendaraan'],
                'WARNA_KENDARAAN'     => $input['warna'],
                'NOPOL_KENDARAAN'     => strtoupper($input['no_polisi']),
                'TAHUN_KENDARAAN'     => $input['tahun_kendaraan'],
                'NO_RANGKA_KENDARAAN' => $input['no_rangka'],
                'NO_MESIN_KENDARAAN'  => $input['no_mesin'],
                'CREATED_AT'          => date("Y-m-d H:i:s"),
                'CREATED_BY'          => 'api'
            ]
        );
        return response()->json(['error' => false, 'msg' => 'Pendaftaran Berhasil', 'data' => 0], $this->successStatus);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'                  => 'min:4',
            'telepon'               => 'digits_between:10,12',
            'password_lama'         => '',
            'password'              => 'confirmed',
            'password_confirmation' => '',
        ], [
            'required'       => ':attribute harus diisi.',
            'digits_between' => ':attribute harus 10 atau 12 digits',
            'confirmed'      => ':attribute tidak sesuai',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'msg' => $validator->errors()], 401);
        }

        $input = $request->all();
        $customer = DB::table('tbl_pelanggan')
            ->where('ID_PELANGGAN', $input['id_pelanggan'])
            ->first();
        $customer = (array)$customer;

        if (password_verify($input['password_lama'], $customer['PASSWORD_PELANGGAN'])) {
            $data = [
                'NAMA_PELANGGAN'     => $input['nama'],
                'PASSWORD_PELANGGAN' => bcrypt($input['password']),
                'TELEPON_PELANGGAN'  => $input['telepon'],
                'UPDATED_AT'         => date("Y-m-d H:i:s"),
                'UPDATED_BY'         => 'api'
            ];

            DB::table('tbl_pelanggan')
                ->where('ID_PELANGGAN', $input['id_pelanggan'])
                ->update($data);

            return response()->json(['error' => false, 'msg' => 'Profile Berhasil Diubah', 'data' => null], $this->successStatus);
        } else {
            return response()->json(['error' => true, 'msg' => 'Password lama salah', 'data' => null], 401);
        }
    }

    public function detail($id_pelanggan)
    {
        $data = DB::table('tbl_pelanggan')
            ->where('ID_PELANGGAN', $id_pelanggan)
            ->first();
        if ($data) {
            return response()->json(['error' => false, 'msg' => null, 'data' => $data], $this->successStatus);
        } else {
            return response()->json(['error' => true, 'msg' => 'Invalid ID', 'data' => 0], 401);
        }
    }

    public function logout()
    {
        session()->forget('id_pelanggan');
        return response()->json(['error' => false, 'msg' => 'logged out', 'data' => 0], $this->successStatus);
    }
}
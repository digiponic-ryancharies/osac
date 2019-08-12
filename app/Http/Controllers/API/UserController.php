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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $customer = DB::table('tb_pelanggan')
            ->select('id', 'kode', 'nama', 'email', 'password', 'telepon', 'created_at')
            ->where('email', request('email'))
            ->first();
        $customer = (array)$customer;
        if (password_verify(request('password'), $customer['password'])) {
            session()->put('id_pelanggan', $customer['id']);
            return response()->json(['error' => false, 'msg' => 'Login berhasil', 'data' => $customer], $this->successStatus);
        } else {
            return response()->json(['error' => true, 'msg' => 'Username / Password salah', 'data' => 0], 401);
        }

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'                  => 'required|min:4',
            'email'                 => 'required|email|unique:tb_pelanggan,email',
            'telepon'               => 'required|digits_between:10,12',
            'jenis'        => 'required',
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

        $id = DB::table('tb_pelanggan')->max('id') + 1;
        $kode = 'PLNGN/' . str_pad($id, 4, 0, STR_PAD_LEFT);
        DB::table('tb_pelanggan')->insert(
            [
                'id'         => $id,
                'kode'       => $kode,
                'nama'       => $input['nama'],
                'email'      => $input['email'],
                'password'   => bcrypt($input['password']),
                'telepon'    => $input['telepon'],
                'created_at' => date("Y-m-d H:i:s"),
                'created_by' => 'api'
            ]
        );

        DB::table('tb_pelanggan_kendaraan')->insert(
            [
                'id'                 => DB::table('tb_pelanggan_kendaraan')->max('id') + 1,
                'id_pelanggan'       => $id,
                'id_merek_kendaraan' => $input['merk'],
                'id_kendaraan'       => $input['jenis'],
                'warna'              => $input['warna'],
                'nomor_polisi'       => strtoupper($input['no_polisi']),
                'tahun'              => $input['tahun_kendaraan'],
                'nomor_rangka'       => $input['no_rangka'],
                'nomor_mesin'        => $input['no_mesin'],
                'created_at'         => date("Y-m-d H:i:s"),
                'created_by'         => 'api'
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
        $customer = DB::table('tb_pelanggan')
            ->where('id', $input['id_pelanggan'])
            ->first();
        $customer = (array)$customer;

        if (password_verify($input['password_lama'], $customer['password'])) {
            $data = [
                'nama'       => $input['nama'],
                'password'   => bcrypt($input['password']),
                'telepon'    => $input['telepon'],
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => 'api'
            ];

            DB::table('tb_pelanggan')
                ->where('id', $input['id_pelanggan'])
                ->update($data);

            return response()->json(['error' => false, 'msg' => 'Profile Berhasil Diubah', 'data' => null], $this->successStatus);
        } else {
            return response()->json(['error' => true, 'msg' => 'Password lama salah', 'data' => null], 401);
        }
    }

    public function detail($id_pelanggan)
    {
        $data = DB::table('tb_pelanggan')
            ->where('id', $id_pelanggan)
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
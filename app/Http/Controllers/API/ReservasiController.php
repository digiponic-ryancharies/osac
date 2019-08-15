<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use CRUDBooster;

class ReservasiController extends Controller
{

    public $successStatus = 200;

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    function convertDate($data, $format)
    {
        if ($data == '-' || $data == null || $data == '') {
            return "-";
        }

        if ($format == 'indo') {
            $dt = explode(" ", $data);
            $date = explode("-", $dt[0]);
            $bulan = ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
//        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            if (isset($dt[1])) {
                $time = explode(":", $dt[1]);
                $converted = $date[2] . " " . $bulan[(int)($date[1]) - 1] . " " . $date[0] . " - " . $time[0] . ":" . $time[1];
            } else {
                $converted = $date[2] . " " . $bulan[(int)($date[1]) - 1] . " " . $date[0];
            }

        } else if ($format == 'db') {
            // convert input format to YYYY-mm-dd
            $date = explode(" ", $data);
            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            if (strlen($date[1]) == 3) {
                $month = array_search($date[1], $bln) + 1;
            } else {
                $month = array_search($date[1], $bulan) + 1;
            }

            if ($month < 10) {
                $converted = $date[2] . '-0' . $month . '-' . $date[0];
            } else {
                $converted = $date[2] . '-' . $month . '-' . $date[0];
            }
        }

        return $converted;
    }

    public function cabangList()
    {
        $data = DB::table('tb_cabang')
            ->select('id', 'kode_cabang', 'nama_cabang', 'alamat', 'telepon')
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Cabang', 'data' => $data], $this->successStatus);
    }

    public function slotList($cabang, $tgl)
    {
        /*if ($tgl < date("Y-m-d")){
            return response()->json(['error' => false, 'msg' => 'Daftar Waktu Booking', 'data' => null], $this->successStatus);
        }*/

        // get time configuration from setting
        $getTime = DB::table('tb_cabang')
            ->where('id', $cabang)
            ->first();

        // get booked time
        $getServiceTime = DB::table('tb_penjualan_jasa')
            ->where('id_cabang', $cabang)
            ->where('tanggal', '>', $tgl . " 02:00:00")
            ->where('tanggal', '<', $tgl . " 22:00:00")
            ->select('tanggal', 'total_slot')
            ->get();

        $slots = [];
        $time = $getTime->jam_buka;
        $time2 = $getTime->interval_jasa;
        $secs = strtotime($time2) - strtotime("00:00");
        $close = $getTime->jam_tutup;
//        $break = ["12:00", "12:30", $close];
        $break = [$close];

        // add booked time to break
        foreach ($getServiceTime as $gs) {
            $temp = date("H:i", strtotime($gs->tanggal));
            $break[] = $temp;
            // if service time more than 1 slot then add finish time break
            if ($gs->total_slot > 1) {
                for ($i = 1; $i < $gs->total_slot; $i++) {
                    $temp = date("H:i", strtotime($temp) + $secs);
                    $break[] = $temp;
                }
            }
        }

        // populating slot
        if ($getTime->jam_buka > date("H:i") || $tgl > date("Y-m-d")) {
            $slots[] = $time;
        }
        while ($time < $close) {
            $time = date("H:i", strtotime($time) + $secs);
            if ($tgl > date("Y-m-d")) {
//                if (!in_array($time, $break)) {
                $slots[] = $time;
//                }
            } else {
//                if (!in_array($time, $break) && $time > date("H:i")) {
                if ($time > date("H:i")) {
                    $slots[] = $time;
                }
            }
        }

        $res = array_diff($slots, $break);
        $result = [];
        foreach ($res as $r) {
            $result[] = $r;
        }
        return response()->json(['error' => false, 'msg' => 'Daftar Waktu Booking', 'data' => $result], $this->successStatus);

    }

    public function reservasiPelanggan($idPelanggan, $status)
    {
        $booking = DB::table('tb_penjualan_jasa as pj')
            ->join('tb_cabang as cb', 'cb.id', '=', 'pj.id_cabang')
            ->join('tb_general as gn', 'gn.id', '=', 'pj.status_penjualan')
            ->where('id_pelanggan', $idPelanggan)
            ->where('pj.status_pembayaran', $status)
            ->select('pj.id', 'cb.nama_cabang', 'pj.kode', 'pj.tanggal', 'pj.tanggal_masuk', 'pj.nama_pelanggan', 'pj.merek_kendaraan', 'pj.nama_kendaraan', 'pj.nomor_polisi', 'pj.total', 'gn.keterangan as status')
            ->orderBy('pj.tanggal', 'desc')
            ->get();

        $result = [];
        foreach ($booking as $data) {
            $detailBooking = DB::table('tb_penjualan_jasa_detail')
                ->select('nama_jasa', 'harga')
                ->where('id_penjualan_jasa', $data->id)
                ->get();

            $data->tanggal = $this->convertDate($data->tanggal, 'indo');
            $data->tanggal_masuk = $this->convertDate($data->tanggal_masuk, 'indo');
            $data->detail_jasa = $detailBooking;
            $result[] = $data;
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Booking Pelanggan', 'data' => $result], $this->successStatus);
    }

    public function daftar(Request $request)
    {
        // validation setup
        $validator = Validator::make($request->all(), [
            'id_cabang'    => 'required',
            'tgl_booking'  => 'required',
            'jam_booking'  => 'required',
            'id_kendaraan' => 'required',
            'kode_jasa'    => 'required',
            'id_pelanggan' => 'required',
        ], [
            /*'required'       => 'The :attribute field is required.',
            'unique'         => 'The :attribute field must be unique.',
            'digits_between' => 'The :attribute field must have a length between 10 and 12 digits',*/
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'msg' => $validator->errors()], 401);
        }

        $input = $request->all();

        // selecting customer data
        $customer = DB::table('tb_pelanggan')
            ->where('id', $input['id_pelanggan'])
            ->first();
        $customer = (array)$customer;
        // selecting customer vehicle
        $vehicle = DB::table('tb_pelanggan_kendaraan as k')
            ->join('tb_merek_kendaraan as mk', 'mk.id', '=', 'k.id_merek_kendaraan')
            ->join('tb_kendaraan as kd', 'kd.id', '=', 'k.id_kendaraan')
            ->select('kd.id_jenis_kendaraan', 'k.id_merek_kendaraan', 'mk.keterangan as merk', 'k.id_kendaraan', 'kd.keterangan as tipe', 'k.nomor_polisi')
            ->where('k.id_pelanggan', $input['id_pelanggan'])
            ->where('k.id', $input['id_kendaraan'])
            ->first();
        $vehicle = (array)$vehicle;

        // looping data foreach jasa
        $id_booking = DB::table('tb_penjualan_jasa')->max('id') + 1;
        $kode = 'POSJS' . date('dmy') . '' . str_pad($id_booking, 5, 0, STR_PAD_LEFT);
        $id_book_detail = DB::table('tb_penjualan_jasa_detail')->max('id') + 1;
        $kode_jasa = explode("&", $input['kode_jasa']);
        $batch = [];
        $durasi = "00:00:00";
        $total = 0;
        for ($index = 0; $index < count($kode_jasa); $index++) {
            // selecting data from db
            $jasa = DB::table('tb_jasa as j')
                ->join('tb_durasi_jasa as dj', 'dj.id_jasa', '=', 'j.id')
                ->join('tb_harga_jasa as hj', 'hj.id_jasa', '=', 'j.id')
                ->select('j.id', 'j.kode', 'j.keterangan', 'dj.durasi', 'hj.harga')
                ->where('j.kode', $kode_jasa[$index])
                ->where('dj.id_jenis_kendaraan', $vehicle['id_jenis_kendaraan'])
                ->where('hj.id_jenis_kendaraan', $vehicle['id_jenis_kendaraan'])
                ->first();
            $jasa = (array)$jasa;

            // adding durasi
            $time2 = "00:" . $jasa['durasi'];
            $secs = strtotime($time2) - strtotime("00:00:00");
            $durasi = date("H:i", strtotime($durasi) + $secs);

            // adding price
            $total += $jasa['harga'];

            // put into array for batch
            $batch[] = [
                'id'                  => $id_book_detail + $index,
                'id_penjualan_jasa'   => $id_booking,
                'kode_penjualan_jasa' => $kode,
                'id_jasa'             => $jasa['id'],
                'nama_jasa'           => $jasa['keterangan'],
                'harga'               => $jasa['harga'],
                'durasi'              => $jasa['durasi'],
            ];
        }

        // hitung total slot
        $getTime = DB::table('tb_cabang')
            ->where('id', $input['id_cabang'])
            ->first();

        $pengerjaan = date("i", strtotime($durasi));
        $interval = date("i", strtotime($getTime->interval_jasa));

        DB::table('tb_penjualan_jasa')->insert(
            [
                'id'                 => $id_booking,
                'id_cabang'          => $input['id_cabang'],
                'kode'               => $kode,
                'tanggal'            => $input['tgl_booking'] . ' ' . $input['jam_booking'],
                //                'CATATAN_BOOKING'   => $input['catatan'],
                'id_pelanggan'       => $customer['id'],
                'nama_pelanggan'     => $customer['nama'],
                'id_merek_kendaraan' => $vehicle['id_merek_kendaraan'],
                'merek_kendaraan'    => $vehicle['merk'],
                'id_kendaraan'       => $vehicle['id_kendaraan'],
                'nama_kendaraan'     => $vehicle['tipe'],
                'nomor_polisi'       => $vehicle['nomor_polisi'],
                'subtotal'           => $total,
                'total'              => $total,
                'total_slot'         => ceil($pengerjaan / $interval),
                'status_penjualan'   => 28,
                'status_pembayaran'  => 25,
                'created_at'         => date("Y-m-d H:i:s"),
                'created_by'         => 'api'
            ]
        );

        DB::table('tb_penjualan_jasa_detail')->insert($batch);

        return response()->json(['error' => false, 'msg' => 'Reservasi Berhasil', 'data' => null], $this->successStatus);
    }

    public function detail($bookingcode)
    {
        $data = DB::table('tb_penjualan_jasa as pj')
            ->join('tb_cabang as cb', 'cb.id', '=', 'pj.id_cabang')
            ->join('tb_general as gn', 'gn.id', '=', 'pj.status_penjualan')
            ->where('pj.kode', $bookingcode)
            ->select('pj.id', 'cb.nama_cabang', 'pj.kode', 'pj.tanggal', 'pj.tanggal_masuk', 'pj.nama_pelanggan', 'pj.merek_kendaraan', 'pj.nama_kendaraan', 'pj.nomor_polisi', 'pj.total', 'gn.keterangan as status')
            ->first();

        $detailBooking = DB::table('tb_penjualan_jasa_detail')
            ->select('nama_jasa', 'harga')
            ->where('id_penjualan_jasa', $data->id)
            ->get();

        $checkin = date("Y-m-d H:i:s");
        if ($data->tanggal_masuk == null) {
            DB::table('tb_penjualan_jasa')
                ->where('id', $data->id)
                ->update([
                    'tanggal_masuk' => $checkin
                ]);
        }

        $data->tanggal = $this->convertDate($data->tanggal, 'indo');
        $data->tanggal_masuk = ($data->tanggal_masuk == null ? $this->convertDate($checkin, 'indo') : $this->convertDate($data->tanggal_masuk, 'indo'));
        $data->detail_jasa = $detailBooking;

        return response()->json(['error' => false, 'msg' => 'Detail Booking', 'data' => $data], $this->successStatus);
    }
}
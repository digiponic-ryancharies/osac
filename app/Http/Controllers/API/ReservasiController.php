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

    function randomize($length = 6)
    {
        $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

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

    public function jenisJasaList()
    {
        $data = DB::table('tbl_jenis_jasa')
            ->select('ID_JENIS_JASA', 'NAMA_JENIS_JASA')
            ->orderBy('ID_JENIS_JASA')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Jenis Jasa', 'data' => $data], $this->successStatus);
    }

    public function hargaJasaList()
    {
        $data = DB::table('tbl_harga_jasa as hj')
            ->join('tbl_jasa as j', 'j.ID_JASA', '=', 'hj.ID_JASA')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'hj.ID_JENIS_KENDARAAN')
            ->select('hj.ID_HARGA_JASA', 'j.NAMA_JASA', 'jk.KETERANGAN_JENIS_KENDARAAN', 'hj.NOMINAL_HARGA_JASA')
            ->orderBy('hj.ID_HARGA_JASA')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Harga Jasa', 'data' => $data], $this->successStatus);
    }

    public function durasiJasaList()
    {
        $data = DB::table('tbl_durasi_jasa as dj')
            ->join('tbl_jasa as j', 'j.ID_JASA', '=', 'dj.ID_JASA')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'dj.ID_JENIS_KENDARAAN')
            ->select('dj.ID_DURASI', 'j.NAMA_JASA', 'jk.KETERANGAN_JENIS_KENDARAAN', 'dj.NOMINAL_DURASI')
            ->orderBy('dj.ID_DURASI')
            ->get();

        return response()->json(['error' => false, 'msg' => 'Daftar Durasi Jasa', 'data' => $data], $this->successStatus);
    }

    public function jasaList($idJenisJasa)
    {
        $path = url('/');

        if ($idJenisJasa == 0) {
            $data = DB::table('tbl_jasa as j')
                ->join('tbl_jenis_jasa as jj', 'jj.ID_JENIS_JASA', '=', 'j.ID_JENIS_JASA')
                ->select('j.ID_JASA', 'j.KODE_JASA', 'j.NAMA_JASA', 'jj.ID_JENIS_JASA', 'j.GAMBAR', 'j.DESKRIPSI')
                ->orderBy('jj.NAMA_JENIS_JASA')
                ->get();
        } else {
            $data = DB::table('tbl_jasa as j')
                ->join('tbl_jenis_jasa as jj', 'jj.ID_JENIS_JASA', '=', 'j.ID_JENIS_JASA')
                ->select('j.ID_JASA', 'j.KODE_JASA', 'j.NAMA_JASA', 'jj.ID_JENIS_JASA', 'j.GAMBAR', 'j.DESKRIPSI')
                ->where('jj.ID_JENIS_JASA', $idJenisJasa)
                ->orderBy('jj.NAMA_JENIS_JASA')
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
            if ($value->GAMBAR == null) {
                $value->GAMBAR = $path . '/img/logo.png';
            } else {
                $value->GAMBAR = $path . '/' . $value->GAMBAR;
            }

            $result[] = $value;
        }
        return response()->json(['error' => false, 'msg' => 'Daftar Jasa', 'data' => $result], $this->successStatus);
    }

    public function cabangList()
    {
        $data = DB::table('tb_cabang')
            ->select('id', 'kode_cabang', 'nama_cabang', 'alamat', 'telfon')
            ->get();
        return response()->json(['error' => false, 'msg' => 'Daftar Cabang', 'data' => $data], $this->successStatus);
    }

    public function slotList($cabang, $tgl)
    {
        // get time configuration from setting
        $getTime = DB::table('tb_cabang')
            ->where('id', $cabang)
            ->first();
        /*$getInterval = DB::table('cms_settings')
            ->where('name', 'interval_jasa')
            ->first();*/

        // get booked time
        $getServiceTime = DB::table('tbl_booking_jasa')
            ->where('TANGGAL_BOOKING', '>', $tgl . " 02:00:00")
            ->where('TANGGAL_BOOKING', '<', $tgl . " 22:00:00")
            ->select('TANGGAL_BOOKING', 'TOTAL_SLOT')
            ->get();

        $slots = [];
        $time = $getTime->jam_buka;
//        $time2 = $getInterval->content;
        $time2 = $getTime->interval_jasa;
        $secs = strtotime($time2) - strtotime("00:00");
        $close = $getTime->jam_tutup;
        $break = ["12:00", "12:30", $close];

        // add booked time to break
        foreach ($getServiceTime as $gs) {
            $temp = date("H:i", strtotime($gs->TANGGAL_BOOKING));
            $break[] = $temp;
            // if service time more than 1 slot then add finish time break
            if ($gs->TOTAL_SLOT > 1) {
                for ($i = 1; $i < $gs->TOTAL_SLOT; $i++) {
                    $temp = date("H:i", strtotime($temp) + $secs);
                    $break[] = $temp;
                }
            }
        }

        // populating slot
        $slots[] = $time;
        while ($time < $close) {
            $time = date("H:i", strtotime($time) + $secs);
            if (!in_array($time, $break)) {
                $slots[] = $time;
            }
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Waktu Booking', 'data' => $slots], $this->successStatus);

    }

    public function reservasiPelanggan($emailPelanggan)
    {
        $booking = DB::table('tbl_booking_jasa as bj')
            ->join('tb_cabang as c', 'c.id', '=', 'bj.ID_CABANG')
            ->join('tb_general as g', 'g.id', '=', 'bj.STATUS_BOOKING')
            ->select('bj.ID_BOOKING', 'bj.KODE_BOOKING', 'c.nama_cabang', 'bj.TANGGAL_BOOKING', 'bj.CATATAN_BOOKING', 'bj.NOPOL_KENDARAAN', 'bj.NAMA_KENDARAAN', 'bj.TOTAL_DURASI', 'bj.TOTAL', 'g.keterangan')
            ->where('EMAIL_PELANGGAN', $emailPelanggan)
            ->orderBy('TANGGAL_BOOKING', 'desc')
            ->get();

        // looping query result to array
        $bookingArray = [];
        foreach ($booking as $v) {
            $bookingArray[] = (array)$v;
        }

        $result = [];
        // get booking detail for each data
        foreach ($bookingArray as $r) {
            $detailBooking = DB::table('tbl_booking_jasa_detail')
                ->where('ID_BOOKING', $r['ID_BOOKING'])
                ->get();
            $dBooking = [];
            $jasa = [];
            // make query result to array
            foreach ($detailBooking as $db) {
                $dBooking[] = (array)$db;
            }

            // add nama jasa to array
            foreach ($dBooking as $db) {
                $jasa[] = $db['NAMA_JASA'];
            }
            $r['TANGGAL_BOOKING'] = $this->convertDate($r['TANGGAL_BOOKING'], 'indo');
            $r['JASA'] = implode(", ", $jasa);
            // add fixed data to final array
            $result[] = $r;
        }

        return response()->json(['error' => false, 'msg' => 'Daftar Booking Pelanggan', 'data' => $result], $this->successStatus);
    }

    public function daftar(Request $request)
    {
        // validation setup
        $validator = Validator::make($request->all(), [
            'id_kendaraan' => 'required',
            'kode_jasa'    => 'required',
            'catatan'      => '',
            'id_cabang'    => 'required',
            'tgl_booking'  => 'required',
            'jam_booking'  => 'required',
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
        $customer = DB::table('tbl_pelanggan')
            ->where('ID_PELANGGAN', $input['id_pelanggan'])
            ->first();
        $customer = (array)$customer;
        // selecting customer vehicle
        $vehicle = DB::table('tbl_kendaraan as k')
            ->join('tbl_jenis_kendaraan as jk', 'jk.ID_JENIS_KENDARAAN', '=', 'k.ID_JENIS_KENDARAAN')
            ->select('k.NAMA_KENDARAAN', 'k.NOPOL_KENDARAAN', 'jk.KETERANGAN_JENIS_KENDARAAN')
            ->where('ID_PELANGGAN', $input['id_pelanggan'])
            ->where('ID_KENDARAAN', $input['id_kendaraan'])
            ->first();
        $vehicle = (array)$vehicle;

        // looping data foreach jasa
        $kode_jasa = explode("&", $input['kode_jasa']);
        $batch = [];
        $time = "00:00:00";
        $total = 0;
        $id_booking = DB::table('tbl_booking_jasa')->max('ID_BOOKING') + 1;
//        $kode =  'POS'.date('dmy').''.str_pad($id_booking,5,0,STR_PAD_LEFT);
        $kode = $this->randomize(6);
        $id_book_detail = DB::table('tbl_booking_jasa_detail')->max('ID_BOOKING_DETAIL') + 1;
        for ($index = 0; $index < count($kode_jasa); $index++) {
            // selecting data from db
            $jenisJasa = DB::table('tbl_jasa as j')
                ->join('tbl_durasi_jasa as dj', 'dj.ID_JASA', '=', 'j.ID_JASA')
                ->join('tbl_harga_jasa as hj', 'hj.ID_JASA', '=', 'j.ID_JASA')
                ->select('j.KODE_JASA', 'j.NAMA_JASA', 'dj.NOMINAL_DURASI', 'hj.NOMINAL_HARGA_JASA')
                ->where('j.KODE_JASA', $kode_jasa[$index])
                ->first();
            $jenisJasa = (array)$jenisJasa;

            // adding durasi
            $time2 = $jenisJasa['NOMINAL_DURASI'];
            $secs = strtotime($time2) - strtotime("00:00:00");
            $time = date("H:i:s", strtotime($time) + $secs);

            // adding price
            $total += $jenisJasa['NOMINAL_HARGA_JASA'];

            // put into array for batch
            $batch[] = [
                'ID_BOOKING_DETAIL' => $id_book_detail + $index,
                'ID_BOOKING'        => $id_booking,
                'KODE_JASA'         => $kode_jasa[$index],
                'NAMA_JASA'         => $jenisJasa['NAMA_JASA'],
                'HARGA_JASA'        => $jenisJasa['NOMINAL_HARGA_JASA'],
                'DURASI_JASA'       => $jenisJasa['NOMINAL_DURASI'],

            ];
        }

        DB::table('tbl_booking_jasa')->insert(
            [
                'ID_BOOKING'        => $id_booking,
                'KODE_BOOKING'      => $kode,
                'ID_CABANG'         => $input['id_cabang'],
                'TANGGAL_BOOKING'   => $input['tgl_booking'] . ' ' . $input['jam_booking'],
                'CATATAN_BOOKING'   => $input['catatan'],
                'JENIS_KENDARAAN'   => $vehicle['KETERANGAN_JENIS_KENDARAAN'],
                'NAMA_KENDARAAN'    => $vehicle['NAMA_KENDARAAN'],
                'NOPOL_KENDARAAN'   => $vehicle['NOPOL_KENDARAAN'],
                'NAMA_PELANGGAN'    => $customer['NAMA_PELANGGAN'],
                'EMAIL_PELANGGAN'   => $customer['EMAIL_PELANGGAN'],
                'TELEPON_PELANGGAN' => $customer['TELEPON_PELANGGAN'],
                'TOTAL'             => $total,
                'TOTAL_SLOT'        => count($kode_jasa),
                'STATUS_BOOKING'    => 18,
                'CREATED_AT'        => date("Y-m-d H:i:s"),
                'CREATED_BY'        => 'api'
            ]
        );

        DB::table('tbl_booking_jasa_detail')->insert($batch);

        return response()->json(['error' => false, 'msg' => 'Reservasi Berhasil', 'data' => null], $this->successStatus);
    }

    public function detail($bookingcode)
    {
        $data = DB::table('tbl_booking_jasa')
            ->where('KODE_BOOKING', $bookingcode)
            ->select('KODE_BOOKING', 'TOTAL')
            ->first();

        return response()->json(['error' => false, 'msg' => 'Detail Booking', 'data' => $data], $this->successStatus);

    }
}
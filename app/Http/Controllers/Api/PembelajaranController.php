<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ResponseBuilder;
use App\Http\Helper\SiakV2Matkul;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use App\Models\Pembelajaran;
use App\Models\Absensi;
use App\Models\Cpl_matkul;

class PembelajaranController extends Controller
{
    public function index(Request $request)
    {
        $filterField = $request->input('filter');
        $filterValue = $request->input('filterValue');

        $learning_done = $request->input('learning_done');


        $data = Pembelajaran::select([
            'id',
            'id_lecture',
            'nik_dosen',
            'id_matkul',
            'pertemuan',
            'kelas',
            'rps_dasar',
            'rps_pelaksanaan',
            'nidn_dosen_pengganti',
            'dosen_tamu',
            'npm_komti',
            'name_komti',
            'status_kelas',
            'use_face_recognition',
            'learning_done',
            'token',
            'created_at',
            'deleted_at'
        ])->with('dosen', 'dosenPengganti');

        // Dosen/Dosen_Ext cuma boleh lihat presensi miliknya sendiri -- filter nik_dosen
        // dari client sebelumnya dipercaya mentah2 tanpa cek pemilik, jadi dosen manapun
        // bisa comot data (termasuk token QR) dosen lain cukup ganti nilai filter. Admin
        // tetap bebas lintas dosen (dipakai rekap admin & form "ubah dosen pengampu").
        $tiasUser = $request->attributes->get('tias_user');
        $tiasRole = $tiasUser['role'] ?? null;
        $scopedNik = null;
        if (in_array($tiasRole, ['Dosen', 'Dosen_Ext'])) {
            if (!$tiasUser['nip']) {
                return response()->json(['status' => 403, 'message' => 'Akun ini belum punya NIP terdaftar.', 'data' => null], 403);
            }
            // trim() -- nip dari /profile/getDataPribadi kadang ada spasi nyasar, client (fe-ucl)
            // sendiri sudah trim() nip-nya sebelum kirim filter, jadi tanpa ini nilainya bisa
            // beda persis dgn yg dikirim client meski sumbernya sama dan query jadi kosong.
            $scopedNik = trim($tiasUser['nip']);
            $data->where('nik_dosen', $scopedNik);
        } elseif ($tiasRole !== 'Admin') {
            return response()->json(['status' => 403, 'message' => 'Akses ditolak.', 'data' => null], 403);
        }

        if ($filterField && $filterValue) {
            foreach ($filterField as $key => $value) {
                // nik_dosen sudah dipaksa dari identitas token di atas (utk Dosen/Dosen_Ext) --
                // lewati filter client supaya tidak dobel-AND dgn nilai yg berpotensi beda format.
                if ($scopedNik !== null && $value === 'nik_dosen') {
                    continue;
                }
                if ($filterField[$key] != null || $filterValue[$key] != null) {
                    $data->where($value, '=', $filterValue[$key]);
                }
            }
        }
        $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'id', $request->input('orderValue') ? $request->input('orderValue') : 'desc');

        if ($learning_done == 'true') {
            $data = $data->whereNotNull('learning_done');
        }

        if ($request->input('dataTable') == true) {
            // Nama matkul diambil dari SIAK v2 (be-siakad) lewat SiakV2Matkul (lihat catatan
            // di sana soal SIAK_V2_API_URL harus IP lokal, bukan domain publik). SEMUA kode
            // unik di-lookup SEKALI secara paralel (findMany/Http::pool) SEBELUM Datatables
            // jalan -- "Daftar Hadir" minta length=-1 (tanpa pagination, bisa ratusan baris
            // riwayat), jadi lookup satu-satu per baris di dalam addColumn bikin halaman
            // menggantung lama (pernah kejadian, lihat riwayat commit).
            $uniqueKodeMatkul = (clone $data)->pluck('id_matkul')->filter()->unique()->values()->all();
            $matkulCache = SiakV2Matkul::findMany($uniqueKodeMatkul);
            return $dummyTable = Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('matkul', function ($row) use ($matkulCache) {
                    return $matkulCache[$row->id_matkul] ?? null;
                })
                // ->addColumn('qr_code', function ($row) {
                //     return QrCode::generate(
                //         $row['token'],
                //     );
                // })
                ->make(true);
        } else {
            if ($request->input('searchData')) {
                $data->where(function ($query) use ($request) {
                    foreach (Pembelajaran::getTableColumns() as $value) {
                        $query->orwhere('pembelajaran.' . $value, 'LIKE',  "%" . $request->input('searchData') . "%");
                        $query->orwhere('pembelajaran.' . $value, 'LIKE',  "" . $request->input('searchData') . "%");
                        $query->orwhere('pembelajaran.' . $value, 'LIKE',  "%" . $request->input('searchData') . "");
                        $query->orwhere('pembelajaran.' . $value, 'LIKE',  $request->input('searchData'));
                    }
                });
            }
            $dummyAll = $data->get();
            return ResponseBuilder::success(200, "success", $dummyAll);
        }
    }

    public function cekPertemuan(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $result = [];
        $thisMonth = DATE('m');
        $thisYear = DATE('Y');
        $nextYear = date('Y', strtotime('+1 Year'));
        if (!$request->input('id_lecture') || !$request->input('nik_dosen') || !$request->input('id_matkul') || !$request->input('kelas')) {
            return ResponseBuilder::success(200, "Error, Lecture atau Dosen atau Matkul belum terisi", null);
        }

        // $prosesPertemuan = Pembelajaran::where(DB::raw('YEAR(created_at)'), '=', $thisYear);
        $prosesPertemuan = Pembelajaran::where('nik_dosen', $request->input('nik_dosen'))
            ->where('id_lecture', $request->input('id_lecture'))
            ->where('id_matkul', $request->input('id_matkul'))
            ->where('kelas', $request->input('kelas'));
        $prosesPertemuan = $prosesPertemuan->orderBy('id', 'desc');

        // return response()->json($prosesPertemuan->get(), 200);

        if ($prosesPertemuan->get()->toArray()) {

            $datePembelajaran = $prosesPertemuan->get()[0]['created_at'];
            $tglPenetapan = '' . $nextYear . '-02-01';
            $convertTgl1 = date('Y-m-d', strtotime($tglPenetapan));
            $convertTgl2 = date('Y-m-d', strtotime($datePembelajaran));
            if ($convertTgl2 < $convertTgl1) {
                // $countPertemuan = 1;
            } else {
                $countPertemuan = 1;
            }
        }

        $setPertemuan = $prosesPertemuan->pluck('pertemuan')->first();
        $countPertemuan = $setPertemuan + 1;

        if ($countPertemuan == 15) {
            $countPertemuan = 1;
        }

        // $getData = Http::get('https://cpl.ft.uika-bogor.ac.id/api/matkul', [ 
        //     'course_code' => $request->input('id_matkul')
        // ]);
        // $dataCpl = json_decode($getData->body(), true);
        $cplMatkul = Cpl_matkul::orderBy('id', 'DESC')
            ->where('course_code', $request->input('id_matkul'))
            ->with('pertemuanBelajar')->first();

        // return response()->json($cplMatkul, 200); 


        if ($cplMatkul) {
            $cplMatkul = collect($cplMatkul);
            if ($cplMatkul['pertemuan_belajar']) {
                $dummyEx = $cplMatkul;
                $finisCpl =  $cplMatkul['pertemuan_belajar']['p' . $countPertemuan . ''];
            } else {
                $dummyEx = null;
                $finisCpl = null;
            }
        } else {
            $dummyEx = null;
            $finisCpl = null;
        }

        return response()->json([
            "status" => 200,
            "message" => "Berhasil",
            "data" => array(
                "pertemuan_ke" => $countPertemuan,
                "rps_dasar" => $finisCpl,
                "rps_pelaksanaan" => $finisCpl,
                "pertemuan_cpl" => $dummyEx
            )
        ], 200);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $result = [];

        foreach (Pembelajaran::getTableColumns() as $key) {

            if ($request->file() != null && $request->file($key) != null) {

                $files = $request->file($key);
                $filename = basename($files->getClientOriginalName(), '.' . $files->getClientOriginalExtension());
                $files->move(storage_path('app/public/pelanggaran/photo/'), $filename . '.' . $files->getClientOriginalExtension());
                $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
            } elseif ($request->input($key) != null) {
                $result[$key] = $request->input($key);
            } else {
                // $result[$key] = $request->input($key) ? $request->input($key) : '-';
            }
        }

        // Identitas dosen WAJIB dari token terverifikasi (lihat VerifyTiasToken), BUKAN
        // dari body request -- sebelumnya nik_dosen dipercaya mentah2 dari client,
        // siapapun bisa generate sesi kelas atas nama dosen lain. Admin tetap boleh
        // generate atas nama dosen manapun (lihat field "Dosen Pengganti" di form).
        // id_lecture TETAP dari client (FK ke siak_lecture.id, jadwal yg dipilih dosen
        // dari dropdown roster -- bukan field identitas, jadi tidak dipaksa dari token).
        $tiasUser = $request->attributes->get('tias_user');
        $tiasRole = $tiasUser['role'] ?? null;
        if (in_array($tiasRole, ['Dosen', 'Dosen_Ext'])) {
            if (!$tiasUser['nip']) {
                return response()->json(['status' => 403, 'message' => 'Akun ini belum punya NIP terdaftar.', 'data' => null], 403);
            }
            // trim() -- lihat catatan serupa di index(), nip dari /profile/getDataPribadi
            // kadang ada spasi nyasar.
            $result['nik_dosen'] = trim($tiasUser['nip']);
        } elseif ($tiasRole !== 'Admin') {
            return response()->json(['status' => 403, 'message' => 'Hanya dosen atau admin yang boleh membuat sesi presensi.', 'data' => null], 403);
        }

        $thisYear = DATE('Y');
        if (!$result['id_lecture'] || !$result['nik_dosen'] || !$request->input('id_matkul') || !$request->input('kelas')) {
            return ResponseBuilder::success(200, "Error, Dosen atau Matkul belum terisi", null);
        }

        // $prosesPertemuan = Pembelajaran::where(DB::raw('YEAR(created_at)'), '=', $thisYear);
        // $prosesPertemuan = $prosesPertemuan->where('nik_dosen', $request->input('nik_dosen'))
        //                     ->where('id_matkul', $request->input('id_matkul'))
        //                     ->where('kelas', $request->input('kelas'));
        // $prosesPertemuan = $prosesPertemuan->orderBy('id', 'desc');
        // $prosesPertemuan = $prosesPertemuan->pluck('pertemuan')->first();
        // $prosesPertemuan = $prosesPertemuan + 1;
        // if($prosesPertemuan == 15){
        //     $prosesPertemuan = 1;
        // }

        // $result['pertemuan'] = $prosesPertemuan;

        $tokenExists = true;
        $token = null;

        while ($tokenExists) {
            $token = mt_rand(100000, 999999);
            $tokenExists = Pembelajaran::where('token', $token)->exists();
        }

        $result['token'] = $token;


        $dummy = Pembelajaran::create($result);
        if ($dummy) {
            $output = ResponseBuilder::success(200, "success", $dummy);
        } else {
            $output = ResponseBuilder::success(200, "error", $dummy);
        }
        return $output;
    }


    public function update($id, Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $data = Pembelajaran::find($id);
        if (!$data) {
            return ResponseBuilder::success(200, "error", '');
        }

        $result = [];
        foreach (Pembelajaran::getTableColumns() as $key) {

            if ($request->file() != null && $request->file($key) != null) {

                $files = $request->file($key);
                $filename = basename($files->getClientOriginalName(), '.' . $files->getClientOriginalExtension());
                $files->move(storage_path('app/public/pelanggaran/photo/'), $filename . '.' . $files->getClientOriginalExtension());
                $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
            } elseif ($request->input($key) != null) {
                $result[$key] = $request->input($key);
            } else {
                // $result[$key] = $request->input($key) ? $request->input($key) : '-';
            }
        }

        $data->fill($result);
        $data->save();
        return ResponseBuilder::success(200, "success", $result);
    }

    public function delete($id)
    {
        $data = Pembelajaran::find($id);
        if ($data) {
            $data->delete();

            $dataAbsen = Absensi::where('id_pembelajaran', $id)->delete();
            if ($dataAbsen) {
                return ResponseBuilder::success(200, "success", []);
            } else {
                return ResponseBuilder::success(200, "error, delete Absen", []);
            }
        } else {
            return ResponseBuilder::success(200, "error, delete Pembelajaran", []);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Pmb_Candidate;
use App\Models\Pmb_Desa;
use App\Models\Pmb_Provinsi;
use App\Models\Pmb_Registration;
use App\Models\Siak_Class;
use App\Models\Siak_Student;
use App\Models\Siak_Student_Snapshot;
use App\Models\Siak_Lecture;
use App\Models\Siak_Lecturer;
use App\Models\Pembelajaran;
use App\Models\Absensi;
use App\Models\Siak_Course;
use App\Models\Siak_Curriculum;



use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\Datatables\Datatables;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

// protected $connection = 'second_db';

class AkademikController extends Controller
{

  public function unique_key($array, $keyname)
  {

    $new_array = array();
    foreach ($array as $key => $value) {

      if (!isset($new_array[$value[$keyname]])) {
        $new_array[$value[$keyname]] = $value;
      }
    }
    $new_array = array_values($new_array);
    return $new_array;
  }

  public function dosenForMk(Request $request)
  {
    $thisMonth = DATE("n");
    $thisYear = DATE("Y");
    $previousyear = $thisYear - 1;

    $thnAkademik = '' . $previousyear . '/' . $thisYear . '';
    if ($thisMonth <= 6) {   //GENAP
      $stSemester = "GENAP";
    } else {    //GASAL
      $stSemester = "GASAL";
    }

    $get = Http::get('https://skpi.uika-bogor.ac.id/restApi/index.php', [
      'menu' => $request->menu ? $request->menu : 'matakuliah',
      'academic_year' => $request->academic_year ? $request->academic_year : $thnAkademik,
      'semester' => $request->semester ? $request->semester : $stSemester,
      'code' => $request->code
    ]);
    $dataGet = json_decode($get->body(), true);


    if ($dataGet['Status']['code'] == 200) {
      $sorts = collect($dataGet['Data'])->sortByDesc('curr_code');
      // $result = $this->unique_key($sorts, 'name');   

      $ress = response()->json([
        "Status" => $dataGet['Status'],
        "message" => "Berhasil",
        "semester" => $stSemester,
        "nik" => $dataGet['nik'],
        "dosen" => $dataGet['dosen'],
        "Total" => $dataGet['Total'],
        "SKS" => $dataGet['SKS'],
        "Data" => $sorts,
      ], 200);
    } else {
      $ress = $dataGet;
    }
    return $ress;
  }

  public function dosenMk(Request $request)
  {
    try {
      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');

      $menu = $request->input('menu');
      $academic_year = $request->input('academic_year');
      $semester = $request->input('semester');
      $code = $request->input('code');


      $dataDosenCodes = Siak_Lecturer::where('nik', $code)->pluck('code')->toArray();
      // return response()->json(
      //   array(
      //     "data" => $dataDosenCodes
      //   )
      // ); 


      $thisMonth = DATE("n");
      $thisYear = DATE("Y");
      $previousyear = $thisYear - 1;

      $thnAkademik = '' . $previousyear . '/' . $thisYear . '';
      if ($thisMonth <= 9) {   //GENAP
        $stSemester = "GENAP";
      } else {    //GASAL
        $stSemester = "GASAL";
      }

      $data = Siak_Lecture::select([
        'id',
        'academic_year',
        'semester',
        'department_code',
        'course_code',
        'curr_code',
        'lecturer_code',
        'class',
        'on_day',
        'from_time',
        'until_time',
        'classroom',
      ])
        ->with('matkul', 'lecturer')
        // ->where('curr_code', $cekNewKurikulum['curr_code']) 
        // ->where('siak_lecture.department_code', 'FT_TI') 
        ->orderByRaw("FIND_IN_SET(siak_lecture.on_day, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'), curr_code DESC, course_code ASC, from_time DESC, until_time DESC");
      // ->orderBy($request->input('orderField') ? $request->input('orderField') : 'course_code', $request->input('orderValue') ? $request->input('orderValue') : 'desc');


      if ($academic_year) {
        $data = $data->where('academic_year', $academic_year);
      } else {
        $data = $data->where('academic_year', $thnAkademik);
      }
      if ($semester) {
        $data = $data->where('semester', $semester);
      } else {
        $data = $data->where('semester', $stSemester);
      }
      if ($dataDosenCodes || $code) {
        $data = $data->where(function ($query) use ($dataDosenCodes, $code) {
          if ($dataDosenCodes) {
            $query->whereIn('lecturer_code', $dataDosenCodes);
          }
          if ($code) {
            $query->orWhere('lecturer_code', $code);
          }
        });
      }

      if ($filterField && $filterValue) {
        foreach ($filterField as $key => $value) {
          if ($filterField[$key] != null || $filterValue[$key] != null) {
            $data->where($value, '=', $filterValue[$key]);
          }
        }
      }

      if ($request->input('dataTable') == true) {
        return $dummyTable = Datatables::of($data)
          ->addIndexColumn()
          ->addColumn('name', function ($row) {
            return $row['matkul']['name'];
          })
          ->addColumn('credit', function ($row) {
            return $row['matkul']['credit'];
          })
          ->addColumn('dosen', function ($row) {
            return $row['lecturer']['name'];
          })
          ->make(true);
      } else {
        if ($request->input('searchData')) {
          $data->where(function ($query) use ($request) {
            foreach (Absensi::getTableColumns() as $value) {
              $query->orwhere('siak_lecture.' . $value, 'LIKE',  "%" . $request->input('searchData') . "%");
              $query->orwhere('siak_lecture.' . $value, 'LIKE',  "" . $request->input('searchData') . "%");
              $query->orwhere('siak_lecture.' . $value, 'LIKE',  "%" . $request->input('searchData') . "");
              $query->orwhere('siak_lecture.' . $value, 'LIKE',  $request->input('searchData'));
            }
          });
        }
        $dummyAll = $data->get()->toArray();
        // return response()->json(
        //   array(
        //     "data" => $dummyAll
        //   )
        // ); 

        $label = array();
        foreach ($dummyAll as $key) {
          $label[] = array(
            "id" => $key['id'],
            "course_code" => $key['course_code'],
            "curr_code" => $key['curr_code'],
            'academic_year' => $key['academic_year'],
            'semester' => $key['semester'],
            "name" => $key['matkul']['name'],
            "sks" => $key['matkul']['credit'],
            "class" => $key['class'],
            "day" => $key['on_day'],
            "from_time" => $key['from_time'],
            "until_time" => $key['until_time'],
            "class_room" => $key['classroom']
          );
        }

        $sks = collect($dummyAll)->sum('matkul.credit');
        return response()->json([
          "Status" => array(
            "success" => true,
            "code" => 200,
            "description" => "Request Valid"
          ),
          "nik" => $code,
          "dosen" => $dummyAll[0]['lecturer']['name'],
          "Total" => count($dummyAll),
          "SKS" => $sks,
          // "real" => $dummyAll ,
          "Data" => $label
        ]);
        // return ResponseBuilder::success(200, "success", $dummyAll);
      }
    } catch (\Exception $e) {
      // return ResponseBuilder::success(200, "error", null);
      return response()->json([
        "Status" => array(
          "success" => true,
          "code" => 400,
          "description" => "Request Invalid"
        ),
        "Data" => array()
      ], 400);
    }
  }

  public function listPertemuan(Request $request)
  {
    try {
      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');

      $menu = $request->input('menu');
      $academic_year = $request->input('academic_year');
      // $semester = $request->input('semester');
      $code = $request->input('code');

      $thisMonth = DATE("n");
      $thisYear = DATE("Y");
      $nextYear = date('Y', strtotime('+1 Year'));
      $from = date('' . $thisYear . '-02-01');
      $to = date('' . $nextYear . '-02-01');

      if ($thisMonth <= 9) {   //GENAP
        $stSemester = "GENAP";
        $previousyear = $thisYear - 1;
        $thnAkademik = '' . $previousyear . '/' . $thisYear . '';
      } else {    //GASAL
        $stSemester = "GASAL";
        $previousyear = $nextYear - 1;
        $thnAkademik = '' . $previousyear . '/' . $nextYear . '';
      }

      $data = Siak_Lecture::select([
        'id',
        'academic_year',
        'semester',
        'department_code',
        'course_code',
        'curr_code',
        'lecturer_code',
        'class',
        'on_day',
        'from_time',
        'until_time',
        'classroom'
        // 'created_at'
      ])
        ->with('pembelajaran', 'matkul', 'lecturer')
        // ->with([
        // 'pembelajaran' => function ($query) {
        // $query->select('id', 'nik_dosen','id_matkul', 'pertemuan', 'kelas', 'status_kelas', 'token');
        // $query->where('created_at', '=>', $from)->where('created_at', '<=', $to);
        // },
        // 'matkul', 'lecturer']) 
        // ->whereHas('pembelajaran', function($query) {
        //   $query->whereBetween('created_at', [$from, $to]);
        // })
        // ->whereBetween('pembelajaran.created_at', [$from, $to])
        ->orderByRaw("FIND_IN_SET(siak_lecture.on_day, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'), course_code ASC, from_time DESC, until_time DESC");
      // ->orderBy($request->input('orderField') ? $request->input('orderField') : 'course_code', $request->input('orderValue') ? $request->input('orderValue') : 'desc');


      if ($academic_year) {
        $data = $data->where('academic_year', $academic_year);
      } else {
        $data = $data->where('academic_year', $thnAkademik);
      }
      // if($semester){
      //   $data = $data->where('semester', $semester);
      // }else{
      //   $data = $data->where('semester', $stSemester);
      // } 
      if ($code) {
        $data = $data->where('lecturer_code', $code);
      } else {
        return ResponseBuilder::success(200, "error, code dosen harus di input", null);
      }

      if ($filterField && $filterValue) {
        foreach ($filterField as $key => $value) {
          if ($filterField[$key] != null || $filterValue[$key] != null) {
            $data->where($value, '=', $filterValue[$key]);
          }
        }
      }


      $dummy = $data->get()->toArray();

      $label = array();
      foreach ($dummy as $key) {

        $pertemuan = array();
        $pertemuan_statusKelas = array();
        // foreach ($key['pembelajaran'] as $val) {
        if (count($key['pembelajaran']) > 0) {                //TOTAL data dari pembelajara
          for ($i = 0; $i < 14; $i++) {
            if (!empty($key['pembelajaran'][$i])) {
              array_push($pertemuan, 1);
              if ($key['pembelajaran'][$i]['status_kelas'] == 1) {
                $stKelas = 'Online';
              } else if ($key['pembelajaran'][$i]['status_kelas'] == 2) {
                $stKelas = 'Hybrid';
              } else {
                $stKelas = 'Offline';
              }
              array_push($pertemuan_statusKelas, $stKelas);
            } else {
              array_push($pertemuan, 0);
              array_push($pertemuan_statusKelas, null);
            }
          }
          $countPersen = min((count($key['pembelajaran']) / 14) * 100, 100);
          $persen = round($countPersen, 2) . '%';
        } else {
          for ($i = 0; $i < 14; $i++) {
            array_push($pertemuan, 0);
            array_push($pertemuan_statusKelas, null);
          }
          $persen = "0%";
        }


        $hasilModif[] = array(
          "cek" => array(
            'id_lecture' => $key['id'],
            'total_belajar' => count($key['pembelajaran'])
          ),

          "academic_year" => $key['academic_year'],
          // "academic_year" => $thnAkademik, 
          "semester" => $key['semester'],
          "course_code" => $key['course_code'],
          "curr_code" => $key['curr_code'],
          "name_matkul" => $key['matkul']['name'],
          "sks" => $key['matkul']['credit'],
          "class" => $key['class'],
          "day" => $key['on_day'],
          "from_time" => $key['from_time'],
          "until_time" => $key['until_time'],
          "class_room" => $key['classroom'],
          "pertemuan" => $pertemuan,
          "pertemuan_statusKelas" => $pertemuan_statusKelas,
          "persentase" => $persen
        );
      }

      // return response()->json([
      //   "real" => $dummy,
      //   "data" => $hasilModif
      // ]);

      if ($request->input('dataTable') == true) {
        return $dummyTable = Datatables::of($hasilModif)
          ->addIndexColumn()
          ->make(true);
      } else {

        return ResponseBuilder::success(200, "success", $hasilModif);
      }
    } catch (\Exception $e) {
      return ResponseBuilder::success(200, "error", null);
    }
  }

  public function listDosenPertemuan(Request $request)
  {
    try {

      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');

      $departementCode = null;
      $academicYear = null;

      // Iterate over the filters to match the corresponding values
      if ($filterField && $filterValue) {
        foreach ($filterField as $index => $filter) {
          if ($filter == 'departement_code') {
            $departementCode = $filterValue[$index];
          }
          if ($filter == 'academic_year') {
            $academicYear = $filterValue[$index];
          }
        }
      }

      // Check if the values are available
      if (!$academicYear || !$departementCode) {
        return response()->json([
          "status" => 200,
          "data" => []
        ]);
      }

      $thisMonth = DATE("n");
      $thisYear = DATE("Y");
      $nextYear = date('Y', strtotime('+1 Year'));
      $from = date('' . $thisYear . '-02-01');
      $to = date('' . $nextYear . '-02-01');

      // Ambil tahun akademik dari request, jika tidak ada fallback ke perhitungan default
      $thnAkademik = $academicYear;

      if (!$thnAkademik) {
        if ($thisMonth <= 9) {   // GENAP
          $stSemester = "GENAP";
          $previousyear = $thisYear - 1;
          $thnAkademik = '' . $previousyear . '/' . $thisYear . '';
        } else {    // GASAL
          $stSemester = "GASAL";
          $previousyear = $nextYear - 1;
          $thnAkademik = '' . $previousyear . '/' . $nextYear . '';
        }
      }

      $dataDosen = Siak_Lecturer::select([
        'code',
        'faculty_code',
        'nik',
        'name',
        'status',
        'functional_title',
        'sex',
        'religion',
        'active'
      ])
        ->with([
          'dosen' => function ($el) {
            $el->select('id', 'nip', 'nidn');
          },
          'lecture' => function ($query) use ($thnAkademik, $departementCode) {
            $cekNewKurikulum = Siak_Curriculum::where('department_code', $departementCode)->orderBy('curr_code', 'DESC')->first();

            $query->select('id', 'academic_year', 'semester', 'department_code', 'course_code', 'curr_code', 'lecturer_code', 'class');

            $query->where('academic_year', $thnAkademik);
            $query->where('curr_code', $cekNewKurikulum['curr_code']);
            $query->orderByRaw("FIND_IN_SET(on_day, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'), course_code ASC, from_time DESC, until_time DESC");
            $query->with([
              'matkul' => function ($exx) {
                $exx->select('code', 'curr_code', 'name', 'credit', 'semester');
              },
              'pembelajaran' => function ($exs) {
                $exs->select('id', 'id_lecture', 'status_kelas');
              }
            ]);
          }
        ])
        ->whereNotNull('nik')
        ->where('nik', '!=', '')
        ->where('active', 'Y');

      // Order based on request parameters
      $dataDosen = $dataDosen->orderBy($request->input('orderField') ? $request->input('orderField') : 'code', $request->input('orderValue') ? $request->input('orderValue') : 'asc');

      // Apply filters
      // if ($filterField && $filterValue) {
      //     foreach ($filterField as $key => $value) {
      //         if ($filterField[$key] != null || $filterValue[$key] != null) {
      //             $dataDosen->where($value, '=', $filterValue[$key]);
      //         }
      //     }
      // } 

      $dummyDosen = $dataDosen->get()->toArray();

      $hasilModif = [];
      $uniqueLecturers = [];

      foreach ($dummyDosen as $key) {
        $hasilGenap_arrayCount = [];
        $hasilGasal_arrayCount = [];
        $ttl_matkulGenap = 0;
        $ttl_matkulGasal = 0;
        $nidn = $key['dosen'] != null ? $key['dosen']['nidn'] : null;

        $uniqueKey = $key['code'] . '_' . $key['nik'];  // Kombinasi 'code' dan 'nik' sebagai unique identifier

        if (!in_array($uniqueKey, $uniqueLecturers)) { // Cek jika kombinasi code_lecturer dan nik sudah ada
          $uniqueLecturers[] = $uniqueKey;

          if (count($key['lecture']) > 0) {
            foreach ($key['lecture'] as $val) {
              if ($val['semester'] == "GENAP") {
                $ttl_matkulGenap += 1;
                $hasilGenap_array = [];
                if (count($val['pembelajaran']) > 0) {
                  $countPersenGenap = (count($val['pembelajaran']) / 14) * 100;
                  array_push($hasilGenap_array, $countPersenGenap);
                  $hasilGenap_arrayCount[] = $hasilGenap_array;
                }
              } else {
                $ttl_matkulGasal += 1;
                $hasilGasal_array = [];
                if (count($val['pembelajaran']) > 0) {
                  $countPersenGasal = (count($val['pembelajaran']) / 14) * 100;
                  array_push($hasilGasal_array, $countPersenGasal);
                  $hasilGasal_arrayCount[] = $hasilGasal_array;
                }
              }
            }
          }

          $averageGenap = count($hasilGenap_arrayCount) > 0 ? array_sum(array_merge(...$hasilGenap_arrayCount)) / $ttl_matkulGenap : 0;
          $averageGasal = count($hasilGasal_arrayCount) > 0 ? array_sum(array_merge(...$hasilGasal_arrayCount)) / $ttl_matkulGasal : 0;

          if (count($key['lecture']) > 0) {
            $hasilModif[] = array(
              "code_lecturer" => $key['code'],
              "nik" => $key['nik'],
              "nidn" => $nidn,
              "name" => $key['name'],
              "ttl_matkulGenap" => $ttl_matkulGenap,
              "ttl_matkulGasal" => $ttl_matkulGasal,
              "persentase_genap" => round($averageGenap, 2) . '%',
              "persentase_gasal" => round($averageGasal, 2) . '%',
            );
          }
        }
      }

      // Return data for DataTable or JSON response
      if ($request->input('dataTable') == true) {
        return Datatables::of($hasilModif)
          ->addIndexColumn()
          ->make(true);
      } else {
        return response()->json([
          "status" => 200,
          "data" => $hasilModif
        ]);
      }
    } catch (\Exception $e) {
      Log::error('Error di listDosenPertemuan: ' . $e->getMessage());
      Log::error('Lokasi error: ' . $e->getFile() . ' di baris ' . $e->getLine());
      return response()->json([
        "status" => 500,
        "message" => $e->getMessage(),
      ], 500);
    }
  }


  public function listAbsenMatkul(Request $request)
  {
    // try {
    $id_matkul = $request->input('id_matkul');
    $kelas = $request->input('kelas');
    $curiculum = $request->input('curiculum');
    $cekNewKurikulum = $curiculum ?? 'TIF2021';


    if (!$id_matkul && !$kelas) {
      return ResponseBuilder::success(200, "error, id_matkul dan kelas harus di inputkan", null);
    }

    $thisYear = DATE('Y');
    $nextYear = date('Y', strtotime('+1 Year'));
    $from = date('' . $thisYear . '-02-01');
    $to = date('' . $nextYear . '-02-01');


    $dataAbsen = Absensi::select([
      'absensi_mhs.id',
      'absensi_mhs.id_pembelajaran',
      'absensi_mhs.npm',
      'absensi_mhs.status_absen',
      'absensi_mhs.coordinate_absen',
      'pembelajaran.status_kelas',
      'pembelajaran.pertemuan',
    ])->with([
      'mahasiswa' => function ($query) {
        $query->select('name', 'student_code');
      }
    ])
      ->join('pembelajaran', 'absensi_mhs.id_pembelajaran', '=', 'pembelajaran.id')
      ->orderBy('pembelajaran.pertemuan', 'asc')
      ->where('pembelajaran.id_matkul', $id_matkul)
      ->where('pembelajaran.kelas', $kelas)
      ->whereBetween('absensi_mhs.created_at', [$from, $to]);

    $dataAbsen = $dataAbsen->get()->toArray();


    $result = array();
    $groupRes = array();
    foreach ($dataAbsen as $val) {
      $nameMhs = $val['mahasiswa'] ? $val['mahasiswa']['name'] : $val['npm'];
      $groupRes[$nameMhs][] = $val;
      // $groupRes[$val['npm']][] = $val;
    }



    // $st_absen = [1, 0, 2, 1, 0, 1]; // Data kehadiran
    // $pertemuan_terlaksana = [2, 5, 7, 8, 9 ,10]; // Pertemuan yang terlaksana
    // $total_pertemuan = 14; // Total pertemuan

    // // Inisialisasi hasil array
    // $hasil_array = [];

    // // Iterasi untuk setiap pertemuan
    // for ($i = 1; $i <= $total_pertemuan; $i++) {
    //   // Periksa apakah pertemuan terlaksana
    //   if (in_array($i, $pertemuan_terlaksana)) {
    //       // Menentukan nilai kehadiran berdasarkan indeks pada array st_absen
    //       $status = "Tidak terlaksana";
    //       $index_pertemuan = array_search($i, $pertemuan_terlaksana);
    //       if (isset($st_absen[$index_pertemuan])) {
    //           switch ($st_absen[$index_pertemuan]) {
    //               case 0:
    //                   $status = "Alfa";
    //                   break;
    //               case 1:
    //                   $status = "Hadir";
    //                   break;
    //               case 2:
    //                   $status = "Hybrid";
    //                   break;
    //           }
    //       }
    //   } else {
    //       // Jika pertemuan tidak terlaksana
    //       $status = "Tidak terlaksana";
    //   }

    //   // Menambahkan status ke array hasil
    //   // $hasil_array["Pertemuan $i"] = $status;
    //   array_push($hasil_array, $status); 
    // }

    // return response()->json([
    //   "status" => 200,  
    //   // "real" => $groupRes, 
    //   "stAbsen" => $st_absen,
    //   "pertemuan_terlaksana" => $pertemuan_terlaksana,
    //   // "ceks" => 
    //   "data" => $hasil_array 
    // ]);





    $dummy = array();
    foreach ($groupRes as $key => $val) {

      $stAbsen = array();
      $temuDum = array();
      foreach ($val as $val2) {
        $npmData = $val2['npm'];
        array_push($stAbsen, $val2['status_absen']);
        // $pertemuanDumm[$val2['pertemuan']] = $val2['status_absen'];
        array_push($temuDum, intval($val2['pertemuan']));
      }

      $stAbsenDeal = array();
      $pertemuanDumm = array();
      $dummyT = array();



      // Inisialisasi hasil array
      // $stAbsen = [1,2,1];
      // $temuDum = [3,5,7];
      // $stAbsenDeal = [];

      for ($i = 1; $i <= 14; $i++) {
        // Periksa apakah pertemuan terlaksana
        if (in_array($i, $temuDum)) {
          // Menentukan nilai kehadiran berdasarkan indeks pada array stAbsen
          $status = null;
          $index_pertemuan = array_search($i, $temuDum);
          if (isset($stAbsen[$index_pertemuan])) {
            switch ($stAbsen[$index_pertemuan]) {
              case 0:
                $status = 0;
                break;
              case 1:
                $status = 1;
                break;
              case 2:
                $status = 2;
                break;
            }
          }
        } else {
          // Jika pertemuan tidak terlaksana
          $status = null;
        }

        // Menambahkan status ke array hasil 
        array_push($stAbsenDeal, $status);
      }



      $countPersen = min((count($val) / 14) * 100, 100);
      $persen = round($countPersen, 2) . '%';


      array_push($dummy, array(
        "name_mhs" => $key,
        "npm" => $npmData,

        // "stAbsen" => $stAbsen,
        // "temuDum" => $temuDum, 
        // "testing" => $dummyT,

        "status_absen" => $stAbsenDeal,
        "persentase" => $persen
      ));
    }


    $thisMonth = DATE("n");
    $thisYear = DATE("Y");
    $nextYear = date('Y', strtotime('+1 Year'));
    $from = date('' . $thisYear . '-02-01');
    $to = date('' . $nextYear . '-02-01');

    if ($thisMonth <= 9) {   //GENAP
      $stSemester = "GENAP";
      $previousyear = $thisYear - 1;
      $thnAkademik = '' . $previousyear . '/' . $thisYear . '';
    } else {    //GASAL
      $stSemester = "GASAL";
      $previousyear = $nextYear - 1;
      $thnAkademik = '' . $previousyear . '/' . $nextYear . '';
    }
    // return response()->json([
    //   "status" => 200,
    //   'data' => $thnAkademik
    // ]);


    $dataMatkul = Siak_Course::select([
      'siak_course.code',
      'siak_course.curr_code',
      'siak_course.name',
      'siak_course.credit',
      'siak_course.semester',
      // 'siak_lecture.academic_year',
      DB::raw(' "' . $thnAkademik . '" as academic_year'),
      'siak_lecture.class',
      'siak_lecture.on_day',
    ])
      ->join('siak_lecture', 'siak_course.code', '=', 'siak_lecture.course_code')
      ->where('siak_course.curr_code', $cekNewKurikulum)
      ->where('code', $id_matkul)
      ->where('siak_lecture.class', $kelas)
      ->first()->toArray();

    if ($request->input('dataTable') == true) {
      return $dummyTable = Datatables::of($dummy)
        ->addIndexColumn()
        // ->addColumn('tahun_akademik', function ($row) {
        //     return $thnAkademik;
        // })
        ->with([
          'matkul' => $dataMatkul,
        ])
        ->make(true);
    } else {
      return response()->json([
        "status" => 200,
        "message" => "success",
        "tahun_akademik" => $thnAkademik,
        "matkul" => $dataMatkul,
        "data" => $dummy
      ], 200);
      // return ResponseBuilder::success(200, "success", $dummy);
    }
    // } catch (\Exception $e) {
    //   return ResponseBuilder::success(200, "error", null); 
    // }
  }

  public function kelas(Request $request)
  {
    try {
      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');

      $data = Siak_Class::orderBy($request->input('orderField') ? $request->input('orderField') : 'name', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
      if ($filterField && $filterValue) {
        foreach ($filterField as $key => $value) {
          if ($filterField[$key] != null || $filterValue[$key] != null) {
            $data->where($value, '=', $filterValue[$key]);
          }
        }
      }
      if ($request->input('dataTable') == true) {
        return $dummyTable = Datatables::of($data)
          ->addIndexColumn()
          ->make(true);
      } else {
        if ($request->input('searchData')) {
          $data->where(function ($query) use ($request) {
            foreach (Absensi::getTableColumns() as $value) {
              $query->orwhere('siak_class.' . $value, 'LIKE',  "%" . $request->input('searchData') . "%");
              $query->orwhere('siak_class.' . $value, 'LIKE',  "" . $request->input('searchData') . "%");
              $query->orwhere('siak_class.' . $value, 'LIKE',  "%" . $request->input('searchData') . "");
              $query->orwhere('siak_class.' . $value, 'LIKE',  $request->input('searchData'));
            }
          });
        }
        $dummyAll = $data->get();
        return ResponseBuilder::success(200, "success", $dummyAll);
      }
    } catch (\Exception $e) {
      return ResponseBuilder::success(200, "error", null);
    }
  }

  public function dataMhs(Request $request)
  {
    try {
      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');
      $data = DB::connection('second_db')->table('pmb_registration as a')
        ->join('pmb_candidate as c', 'c.registration_no', '=', 'a.registration_no')
        // ->join('pmb_provinsi as d', 'd.id', '=', 'c.prov_code')
        // ->join('pmb_desa as e', 'e.id', '=', 'c.desa_code')
        // ->join('pmb_kabupaten as f', 'f.id', '=', 'c.kabkot_code')
        // ->join('pmb_kecamatan as h', 'h.id', '=', 'c.kec_code')
        ->join('siak_department as g', 'g.code', '=', 'a.department_code')
        ->select(
          'c.registration_no',
          'c.name as nama_mahasiswa',
          'c.student_code as npm',
          'c.sex',
          'g.name as prodi',
          // 'd.name as provinsi',
          // 'f.name as city',
          // 'h.name as kecamatan',
          // 'e.name as desa',
          'c.mobile_phone',
          'c.address'
        )
        ->where('c.student_code', '!=', '');
      // $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'c.student_code', $request->input('orderValue') ? $request>
      if ($filterField && $filterValue) {
        foreach ($filterField as $key => $value) {
          if ($filterField[$key] != null || $filterValue[$key] != null) {
            $data->where($value, '=', $filterValue[$key]);
          }
        }
      }
      $data->get();


      return Datatables::of($data)
        ->addIndexColumn()
        ->orderColumn('npm', function ($query, $order) {
          $query->orderBy('c.student_code', $order);
        })
        ->orderColumn('nama_mahasiswa', function ($query, $order) {
          $query->orderBy('c.name', $order);
        })
        ->orderColumn('mobile_phone', function ($query, $order) {
          $query->orderBy('c.mobile_phone', $order);
        })
        ->orderColumn('address', function ($query, $order) {
          $query->orderBy('c.address', $order);
        })

        ->filterColumn('npm', function ($query, $keyword) {
          $query->whereRaw("c.student_code like ?", ["%$keyword%"]);
        })
        ->filterColumn('nama_mahasiswa', function ($query, $keyword) {
          $query->whereRaw("c.name like ?", ["%$keyword%"]);
        })
        ->filterColumn('mobile_phone', function ($query, $keyword) {
          $query->whereRaw("c.mobile_phone like ?", ["%$keyword%"]);
        })
        ->filterColumn('address', function ($query, $keyword) {
          $query->whereRaw("c.address like ?", ["%$keyword%"]);
        })
        ->make(true);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }


  public function dataDosen(Request $request)
  {
    try {
      $filterField = $request->input('filter');
      $filterValue = $request->input('filterValue');
      $data = DB::connection('second_db')->table('pmb_registration as a')
        ->join('pmb_candidate as c', 'c.registration_no', '=', 'a.registration_no')
        // ->join('pmb_provinsi as d', 'd.id', '=', 'c.prov_code')
        // ->join('pmb_desa as e', 'e.id', '=', 'c.desa_code')
        // ->join('pmb_kabupaten as f', 'f.id', '=', 'c.kabkot_code')
        // ->join('pmb_kecamatan as h', 'h.id', '=', 'c.kec_code')
        ->join('siak_department as g', 'g.code', '=', 'a.department_code')
        ->select(
          'c.registration_no',
          'c.name as nama_mahasiswa',
          'c.student_code as npm',
          'c.sex',
          'g.name as prodi',
          'c.mobile_phone',
          'c.address'
        )
        ->where('c.student_code', '!=', '');
      // $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'c.student_code', $request->input('orderValue') ? $request>
      if ($filterField && $filterValue) {
        foreach ($filterField as $key => $value) {
          if ($filterField[$key] != null || $filterValue[$key] != null) {
            $data->where($value, '=', $filterValue[$key]);
          }
        }
      }
      $data->get();


      return Datatables::of($data)
        ->addIndexColumn()

        ->make(true);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }
}

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
use Symfony\Component\HttpFoundation\Response;

// protected $connection = 'second_db';

class AkademikController extends Controller
{ 

    public function unique_key($array,$keyname){

      $new_array = array();
      foreach($array as $key=>$value){
    
        if(!isset($new_array[$value[$keyname]])){
          $new_array[$value[$keyname]] = $value;
        }
    
      }
      $new_array = array_values($new_array);
      return $new_array;
    }

    public function dosenForMk(Request $request){
      $thisMonth = DATE("n");
      $thisYear = DATE("Y");
      $previousyear = $thisYear -1;

      $thnAkademik = ''.$previousyear.'/'.$thisYear.'';
      if($thisMonth <= 6){   //GENAP
        $stSemester = "GENAP";
      }else{    //GASAL
        $stSemester = "GASAL";
      } 

      $get = Http::get('https://skpi.uika-bogor.ac.id/restApi/index.php', [
        'menu' => $request->menu ? $request->menu : 'matakuliah',
        'academic_year' => $request->academic_year ? $request->academic_year : $thnAkademik,
        'semester' => $request->semester ? $request->semester : $stSemester,
        'code' => $request->code
      ]);
      $dataGet = json_decode($get->body(), true);

      
      if($dataGet['Status']['code'] == 200){   
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
      }else{
        $ress = $dataGet;
      }
      return $ress; 
    }

    public function dosenMk(Request $request){
      try {
        $filterField = $request->input('filter');
        $filterValue = $request->input('filterValue');

        $menu = $request->input('menu');
        $academic_year = $request->input('academic_year');
        $semester = $request->input('semester');
        $code = $request->input('code');

        $thisMonth = DATE("n");
        $thisYear = DATE("Y");
        $previousyear = $thisYear -1;

        $thnAkademik = ''.$previousyear.'/'.$thisYear.'';
        if($thisMonth <= 6){   //GENAP
          $stSemester = "GENAP";
        }else{    //GASAL
          $stSemester = "GASAL";
        } 

        $cekNewKurikulum = Siak_Curriculum::where('department_code', 'FT_TI')->orderBy('curr_code', 'DESC')->first(); 

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
        ->where('curr_code', $cekNewKurikulum['curr_code']) 
        ->where('siak_lecture.department_code', 'FT_TI') 
        ->orderByRaw("FIND_IN_SET(siak_lecture.on_day, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'), curr_code DESC, course_code ASC, from_time DESC, until_time DESC");
        // ->orderBy($request->input('orderField') ? $request->input('orderField') : 'course_code', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
        

        if($academic_year){
          $data = $data->where('academic_year', $academic_year);
        }else{
          $data = $data->where('academic_year', $thnAkademik);
        }
        if($semester){
          $data = $data->where('semester', $semester);
        }else{
          $data = $data->where('semester', $stSemester);
        } 
        if($code){
          $data = $data->where('lecturer_code', $code);
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
        }else{
          if($request->input('searchData')){
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

    public function listPertemuan(Request $request){
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
        $from = date(''.$thisYear.'-02-01');
        $to = date(''.$nextYear.'-02-01');

        $previousyear = $thisYear -1;

        $thnAkademik = ''.$previousyear.'/'.$thisYear.'';
        if($thisMonth <= 6){   //GENAP
          $stSemester = "GENAP";
        }else{    //GASAL
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
        ->with('pembelajaran', 'matkul', 'lecturer') 
        // ->with([
        // 'pembelajaran' => function ($query) {
          // $query->select('id', 'nik_dosen','id_matkul', 'pertemuan', 'kelas', 'status_kelas', 'token');
          // $query->where('created_at', '=>', $from)->where('created_at', '<=', $to);
        // },
        // 'matkul', 'lecturer']) 
        ->where('siak_lecture.department_code', 'FT_TI') 
        // ->whereHas('pembelajaran', function($query) {
        //   $query->whereBetween('created_at', [$from, $to]);
        // })
        // ->whereBetween('pembelajaran.created_at', [$from, $to])
        ->orderByRaw("FIND_IN_SET(siak_lecture.on_day, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'), course_code ASC, from_time DESC, until_time DESC");
        // ->orderBy($request->input('orderField') ? $request->input('orderField') : 'course_code', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
        

        if($academic_year){
          $data = $data->where('academic_year', $academic_year);
        }else{
          $data = $data->where('academic_year', $thnAkademik);
        }
        // if($semester){
        //   $data = $data->where('semester', $semester);
        // }else{
        //   $data = $data->where('semester', $stSemester);
        // } 
        if($code){
          $data = $data->where('lecturer_code', $code);
        }else{
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
          if(count($key['pembelajaran']) > 0){
            for ($i = 0; $i < 14; $i++) {
              if(!empty($key['pembelajaran'][$i])){ 
                array_push($pertemuan, 1);
                if($key['pembelajaran'][$i]['status_kelas'] == 1){
                  $stKelas = 'Online';
                }else if($key['pembelajaran'][$i]['status_kelas'] == 2){
                  $stKelas = 'Hybrid';
                }else{
                  $stKelas = 'Offline';
                }
                array_push($pertemuan_statusKelas, $stKelas);
              }else{
                array_push($pertemuan, 0);
                array_push($pertemuan_statusKelas, null);
              } 
            }
            $countPersen = (count($key['pembelajaran']) / 14) * 100;
            $persen = round($countPersen, 2). '%';
          } else {
            for ($i = 0; $i < 14; $i++) {
              array_push($pertemuan, 0);
              array_push($pertemuan_statusKelas, null);
            }
            $persen = "0%";
          }
        

          $hasilModif[] = array( 
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
        }else{ 
          
          return ResponseBuilder::success(200, "success", $hasilModif);
        }
      } catch (\Exception $e) {
        return ResponseBuilder::success(200, "error", null); 
      }
    }

    public function listAbsenMatkul(Request $request){
      // try {
        $id_matkul = $request->input('id_matkul');
        $kelas = $request->input('kelas');
        if(!$id_matkul && !$kelas){
          return ResponseBuilder::success(200, "error, id_matkul dan kelas harus di inputkan", null); 
        }

        $thisYear = DATE('Y'); 
        $nextYear = date('Y', strtotime('+1 Year'));
        $from = date(''.$thisYear.'-02-01');
        $to = date(''.$nextYear.'-02-01');

        
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
            $query->select('name','student_code');
          }
        ])
        ->join('pembelajaran', 'absensi_mhs.id_pembelajaran', '=', 'pembelajaran.id') 
        ->orderBy('pembelajaran.pertemuan', 'asc')
        ->where('pembelajaran.id_matkul', $id_matkul)
        ->where('pembelajaran.kelas', $kelas)
        ->whereBetween('absensi_mhs.created_at', [$from, $to]);  
        $dataAbsen = $dataAbsen->get()->toArray(); 


        // return response()->json([
        //   "status" => 200, 
        //   "data" => $dataAbsen 
        // ]);

        $result = array();
        $groupRes = array();
        foreach ($dataAbsen as $val) {
          $nameMhs = $val['mahasiswa'] ? $val['mahasiswa']['name'] : $val['npm'];
          $groupRes[$nameMhs][] = $val;
          // $groupRes[$val['npm']][] = $val;
        }
        
        

        $dummy = array();
        foreach ($groupRes as $key=>$val) {

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
          for ($i = 1; $i <= 14; $i++) {  
            $sumDums = $i - 1;
            // $pertemuanDumm['p'.$sumDums.''] = $i + 1; 
            
            $stAbsenDeal[] = in_array($i, $temuDum) ? 1 : null;
            // if(!empty($stAbsen[$i]) ){  
            //   array_push($stAbsenDeal, $stAbsen[$i]); 
            // }else{
            //   array_push($stAbsenDeal, null);
            // }

          }   
           
          $countPersen = (count($val) / 14) * 100;
          $persen = round($countPersen, 2). '%';
         
          array_push($dummy, array(
            "name_mhs" => $key,
            "npm" => $npmData,   

            // "stAbsen" => $stAbsen,
            // "temuDum" => $temuDum, 

            "status_absen" => $stAbsenDeal,
            "persentase" => $persen
          ));
        } 

        
        
        $cekNewKurikulum = Siak_Curriculum::where('department_code', 'FT_TI')->orderBy('curr_code', 'DESC')->first();
        // return response()->json([
        //   "status" => 200,
        //   'groupRes' => $cekNewKurikulum['curr_code']
        // ]);

        $dataMatkul = Siak_Course::select([
          'siak_course.code',
          'siak_course.curr_code',
          'siak_course.name',
          'siak_course.credit',
          'siak_course.semester',
          'siak_lecture.academic_year',
          'siak_lecture.class',
          'siak_lecture.on_day',
        ])
        ->join('siak_lecture', 'siak_course.code', '=', 'siak_lecture.course_code')
        ->where('siak_course.curr_code', $cekNewKurikulum['curr_code'])
        ->where('code', $id_matkul)
        ->where('siak_lecture.class', $kelas)
        ->first()->toArray(); 

        if ($request->input('dataTable') == true) {
            return $dummyTable = Datatables::of($dummy)
            ->addIndexColumn()  
            
            ->with([
              'matkul' => $dataMatkul,
            ])
            ->make(true);
        }else{  
            return response()->json([
              "status" => 200,
              "message" => "success",
              "matkul" => $dataMatkul,
              "data" => $dummy
            ], 200);
            // return ResponseBuilder::success(200, "success", $dummy);
        }
      // } catch (\Exception $e) {
      //   return ResponseBuilder::success(200, "error", null); 
      // }
    }

    public function kelas(Request $request){
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
        }else{
          if($request->input('searchData')){
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

    public function dataMhs(Request $request){
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


    public function dataDosen(Request $request){
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


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
                    $query->orwhere('absensi_mhs.' . $value, 'LIKE',  "%" . $request->input('searchData') . "%");
                    $query->orwhere('absensi_mhs.' . $value, 'LIKE',  "" . $request->input('searchData') . "%");
                    $query->orwhere('absensi_mhs.' . $value, 'LIKE',  "%" . $request->input('searchData') . "");
                    $query->orwhere('absensi_mhs.' . $value, 'LIKE',  $request->input('searchData'));
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


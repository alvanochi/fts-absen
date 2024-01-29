<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Pmb_Candidate;
use App\Models\Pmb_Desa;
use App\Models\Pmb_Provinsi;
use App\Models\Pmb_Registration;
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


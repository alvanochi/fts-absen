<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\DB; 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use App\Models\Meeting;

class MeetingController extends Controller
{ 
    public function index(Request $request)
    {
        $filterField = $request->input('filter');
        $filterValue = $request->input('filterValue');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $ids = $request->input("meetIds");

        $data = Meeting::select([
            'id',     
            'id_group_tias',
            'nm_pengundang', 
            'nm_kegiatan', 
            'tipe_kegiatan',
            'sub_tema',
            'ruangan', 
            'bukti_foto', 
            'narsum',
            'ket_narsum',
            'link_online',
            'pertemuan', 
            'tanggal', 
            'waktu', 
            'waktu_end', 
            'notulen',  
            'status_ruangan',  
            'token',
            'contact',
            'created_at',
            'deleted_at' 
        ]);
    
        if ($filterField && $filterValue) {
            foreach ($filterField as $key => $value) {
                if ($filterField[$key] != null || $filterValue[$key] != null) {
                    $data->where($value, '=', $filterValue[$key]);
                }
            }
        }
    
        if ($tanggal_mulai && $tanggal_selesai) {
            $data->whereDate('tanggal', '>=', $tanggal_mulai)
                 ->whereDate('tanggal', '<=', $tanggal_selesai);
        } else {
            if ($tanggal_mulai) {
                $data->whereDate('tanggal', '>=', $tanggal_mulai);
            }
        
            if ($tanggal_selesai) {
                $tanggal_selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $data->whereDate('tanggal', '<=', $tanggal_selesai);
            }
        }

        if ($ids) {
            $data->whereIn('id', $ids);
        }
        
    
        $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'id', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
    
        if ($request->input('dataTable') == true) {
            return $dummyTable = Datatables::of($data)
                ->addIndexColumn()  
                ->make(true);
        } else { 
            if ($request->input('searchData')) {
                $data->where(function ($query) use ($request) {
                    foreach (Meeting::getTableColumns() as $value) {
                        $query->orWhere('meeting.' . $value, 'LIKE', "%" . $request->input('searchData') . "%");
                    }
                });
            }
            $dummyAll = $data->get();
            return ResponseBuilder::success(200, "success", $dummyAll);
        }
    }
    
    
    

    public function find(Request $request)
    {
        if ($request->input() != null) {

            foreach (Meeting::getTableColumns() as $key) {
                if ($request->input($key)) {
                    $data[$key] =  $request->input($key);
                }
            }
            $dataUser =  Meeting::where($data)->get()->toArray(); 
            
            // $getTias = Http::get('https://api-tias.ti.ft.uika-bogor.ac.id/voting/group-users-all');
            // $dataGet = json_decode($getTias->body(), true);
            // return response()->json([
            //     "status" => 200,
            //     "message" => "Berhasil", 
            //     "data" => $dataUser
            // ], 200);
            
            if(count($dataUser) > 0){ 

                $getTias = Http::get('https://api-tias.ti.ft.uika-bogor.ac.id/voting/group-users-all',[
                    'filter[]' => 'id', 
                    'filterValue[]' => $dataUser[0]['id_group_tias']
                ]);
                $dataGet = json_decode($getTias->body(), true);
                // $collectGet = collect($dataGet)->where('id', $dataUser[0]['id_group_tias']); 

                // return response()->json([
                //     "status" => 200,
                //     "message" => "Berhasil", 
                //     "data" => $dataUser[0],
                //     "data_tias" => $collectGet
                // ], 200);

                if(count($dataGet) > 0){   
                    
                    return response()->json([
                        "status" => 200,
                        "message" => "Berhasil", 
                        "data" => $dataUser[0],
                        "data_tias" => $dataGet
                    ], 200);
                    
                }else{
                    return response()->json([
                        "status" => 400,
                        "message" => "GAGAL", 
                        "data" => null
                    ], 200);
                }
            }else{
                return response()->json([
                    "status" => 400,
                    "message" => "Tidak Ada Data", 
                    "data" => null
                ], 200);
            } 
        } else {
            return ResponseBuilder::success(404, "error", "");
        }
    } 

    public function cekPertemuan(Request $request)
    { 
        date_default_timezone_set('Asia/Jakarta'); 
        $result = [];  
        $thisMonth = DATE('m');
        $thisYear = DATE('Y'); 
        $nextYear = date('Y', strtotime('+1 Year'));
        if(!$request->input('nm_kegiatan') || !$request->input('ruangan')){
            return ResponseBuilder::success(200, "Error, Dosen atau Matkul belum terisi", null);
        } 

        $prosesPertemuan = Meeting::where(DB::raw('YEAR(created_at)'), '=', $thisYear);
        $prosesPertemuan = $prosesPertemuan->where('nm_kegiatan', $request->input('nm_kegiatan'))
                            ->where('ruangan', $request->input('ruangan'));
        $prosesPertemuan = $prosesPertemuan->orderBy('id', 'desc');

        if($prosesPertemuan->get()->toArray()){

            $datePembelajaran = $prosesPertemuan->get()[0]['created_at'];
            $tglPenetapan = ''.$nextYear.'-02-01'; 
            $convertTgl1 = date('Y-m-d', strtotime($tglPenetapan));
            $convertTgl2 = date('Y-m-d', strtotime($datePembelajaran));
            if ($convertTgl2 < $convertTgl1) {
                // $prosesPertemuan = 1;
            }else{
                $prosesPertemuan = 1;
            } 
        } 

        $prosesPertemuan = $prosesPertemuan->pluck('pertemuan')->first();
        $prosesPertemuan = $prosesPertemuan + 1;
        // $prosesPertemuan = $prosesPertemuan->get();

        if($prosesPertemuan == 15){
            $prosesPertemuan = 1;
        }
        
        
        $result['pertemuan'] = $prosesPertemuan;

        return response()->json([
            "status" => 200,
            "message" => "Berhasil", 
            "data" => array(  
                "pertemuan_ke" => $prosesPertemuan
            )
        ], 200);

        // return ResponseBuilder::success(200, "success", $prosesPertemuan); 
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta'); 
        $result = [];  
    
        foreach (Meeting::getTableColumns() as $key) {  
    
            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
                    $files->move(storage_path('app/public/meeting/photo/'), $filename . '.' . $files->getClientOriginalExtension());
                    $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
                     
            } elseif ($request->input($key) != null) { 
                $result[$key] = $request->input($key); 
            } else {
                // $result[$key] = $request->input($key) ? $request->input($key) : '-';
            }
                
        }    
    
        $thisYear = DATE('Y');
        if(!$request->input('nm_kegiatan') || !$request->input('ruangan')){
            return ResponseBuilder::success(200, "Error, Dosen atau Matkul belum terisi", null);
        } 
    
        $prosesPertemuan = Meeting::where(DB::raw('YEAR(created_at)'), '=', $thisYear);
        $prosesPertemuan = $prosesPertemuan->where('nm_kegiatan', $request->input('nm_kegiatan'))
                            ->where('ruangan', $request->input('ruangan'));
        $prosesPertemuan = $prosesPertemuan->orderBy('id', 'desc');
        $prosesPertemuan = $prosesPertemuan->pluck('pertemuan')->first();
        $prosesPertemuan = $prosesPertemuan + 1;
        if($prosesPertemuan == 15){
            $prosesPertemuan = 1;
        }
    
        $result['pertemuan'] = $prosesPertemuan;
    
        $tokenExists = true;
        $token = null;

        while ($tokenExists) {
            $token = mt_rand(100000, 999999); 
            $tokenExists = Meeting::where('token', $token)->exists(); 
        }

        $result['token'] = $token;

    
        $dummy = Meeting::create($result);
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
        $data = Meeting::find($id);
        if (!$data) {
            return ResponseBuilder::success(200, "error", '');
        } 

        $result = [];
        foreach (Meeting::getTableColumns() as $key) {  

            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
                    $files->move(storage_path('app/public/meeting/photo/'), $filename . '.' . $files->getClientOriginalExtension());
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
        $data = Meeting::find($id);
        if ($data) { 
            $data->delete();
            return ResponseBuilder::success(200, "success", []);
        }else{
            return ResponseBuilder::success(200, "error", []);
        } 
    }
}

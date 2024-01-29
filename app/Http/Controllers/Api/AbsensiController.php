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

use App\Models\Absensi;
use App\Models\Pembelajaran;

class AbsensiController extends Controller
{ 
    public function index(Request $request)
    {
        $filterField = $request->input('filter');
        $filterValue = $request->input('filterValue');

        $data = Absensi::select([
            'id',  
            'id_pembelajaran',
            'npm',
            'status_absen',  
            'deleted_at' 
        ])->with('mahasiswa', 'pembelajaran.dosen', 'pembelajaran.matkul');  
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
            $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'id', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
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
    } 

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta'); 
        $result = []; 
 
        foreach (Absensi::getTableColumns() as $key) {  

            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
                    $files->move(storage_path('app/public/pelanggaran/photo/'), $filename . '.' . $files->getClientOriginalExtension());
                    $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
                     
            } elseif ($request->input($key) != null) { 
                $result[$key] = $request->input($key); 
            } else {
                // $result[$key] = $request->input($key) ? $request->input($key) : '-';
            }
                
        }    
        
        $result['token'] = Str::random(40);

        $dummy = Absensi::create($result);
        if ($dummy) {
            $output = ResponseBuilder::success(200, "success", $dummy);
        } else {
            $output = ResponseBuilder::success(200, "error", $dummy);
        }
        return $output; 
       
    } 

    public function scanQr(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $token = $request->input('token');
        $coordinate = $request->input('coordinate');
        $npm = $request->input('npm');
        $st_absen = $request->input('status_absen');
        if(!$token || !$coordinate || !$npm){
            return ResponseBuilder::success(200, "failed, Validasi Kurang Lengkap", null); 
        }
        
        
        $data = Pembelajaran::where('token', $token)->first();
        if (isset($data)) { 
            $time = time();

            $timeCreate = strtotime($data->created_at);
            $date = strtotime('2 hours', $timeCreate);
            if ($time < $date) {   
                $cekAbsen = Absensi::where('id_pembelajaran', $data->id);
                $cekAbsen = $cekAbsen->where('npm', $npm)->first();

                // return ResponseBuilder::success(200, "failed", $cekAbsen); 
                if($cekAbsen){ 
                    return ResponseBuilder::success(200, "Anda sudah melakukan absen", null); 
                }else{
                    $absensi = []; 
                    $absensi['id_pembelajaran'] = $data->id;
                    $absensi['npm'] = $npm;
                    $absensi['status_absen'] = $st_absen ? $st_absen : 0;
                    $dummyAbsen = Absensi::create($absensi);
                    if ($dummyAbsen) {
                        $ress = ResponseBuilder::success(200, "success", $dummyAbsen);
                    } else {
                        $ress = ResponseBuilder::success(200, "error", $dummyAbsen);
                    }
                    return $ress;  
                }
            } else {  
                return ResponseBuilder::success(200, "QR telah melewati batas waktu", null);  
            }
        } else {
            return ResponseBuilder::success(200, "QR tidak di temukan", null);   
        }
    }

    public function showQr(Request $request)
    { 
        $token = $request->input('token');
        if(!$token){
            return ResponseBuilder::success(200, "failed, Validasi Kurang Lengkap", null); 
        }

        return QrCode::size(250)->generate(
            $token,
        );
    }

    public function downloadQr(Request $request)
    {
        $token = $request->input('token');
        if(!$token){
            return ResponseBuilder::success(200, "failed, Validasi Kurang Lengkap", null); 
        }
        return response()->streamDownload(
            function () {
                echo QrCode::size(250)
                    ->format('png')
                    ->generate($token);
            },
            'qr-code.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }

    public function update($id, Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta'); 
        $data = Absensi::find($id);
        if (!$data) {
            return ResponseBuilder::success(200, "error", '');
        } 

        $result = [];
        foreach (Absensi::getTableColumns() as $key) {  

            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
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
        $data = Absensi::find($id);
        if ($data) { 
            $data->delete();
            return ResponseBuilder::success(200, "success", []);
        }else{
            return ResponseBuilder::success(200, "error", []);
        } 
    }
}

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
            'upload_dok',
            'nilai',
            'status_absen',  
            'coordinate_absen',
            'created_at',
            'deleted_at' 
        ])
        // ->with('mahasiswa', 'pembelajaran.dosen', 'pembelajaran.matkul');  
        ->with([
            'mahasiswa' => function ($query) {
              $query->select('registration_no', 'name','student_code');
            },
            'pembelajaran' => function ($query) {
              $query->select('id', 'nik_dosen','id_matkul', 'pertemuan', 'kelas', 'status_kelas', 'token');
            },
            'pembelajaran.dosen' => function ($query) {
              $query->select('nip','nama', 'gelar_belakang');
            },
            'pembelajaran.matkul' => function ($query) {
              $query->select('code', 'curr_code','name', 'credit', 'semester');
            }
          ]);
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
        $npm = $request->input('npm'); 
        if(!$npm){
            return ResponseBuilder::success(200, "failed, Npm di masukan", null); 
        }
 
        foreach (Absensi::getTableColumns() as $key) {  

            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
                    $files->move(storage_path('app/public/tugas/'.$npm.'/'), $filename . '.' . $files->getClientOriginalExtension());
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

    public function distance($latitudeOne='', $longitudeOne='', $latitudeTwo='', $longitudeTwo='',$distanceUnit ='',$round=false,$decimalPoints='')
    {
        if (empty($decimalPoints)) 
        {
            $decimalPoints = '1';
        }
        if (empty($distanceUnit)) {
            $distanceUnit = 'KM';
        }
        $distanceUnit = strtolower($distanceUnit);
        $pointDifference = $longitudeOne - $longitudeTwo;
        $toSin = (sin(deg2rad($latitudeOne)) * sin(deg2rad($latitudeTwo))) + (cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * cos(deg2rad($pointDifference)));
        $toAcos = acos($toSin);
        $toRad2Deg = rad2deg($toAcos);

        $toMiles  =  $toRad2Deg * 60 * 1.1515;
        $toKilometers = $toMiles * 1.609344;
        $toNauticalMiles = $toMiles * 0.8684;
        $toMeters = $toKilometers * 1000;
        $toFeets = $toMiles * 5280;
        $toYards = $toFeets / 3;


              switch (strtoupper($distanceUnit)) 
              {
                  case 'ML'://miles
                         $toMiles  = ($round == true ? round($toMiles) : round($toMiles, $decimalPoints));
                         return $toMiles;
                      break;
                  case 'KM'://Kilometers
                        $toKilometers  = ($round == true ? round($toKilometers) : round($toKilometers, $decimalPoints));
                        return $toKilometers;
                      break;
                  case 'MT'://Meters
                        $toMeters  = ($round == true ? round($toMeters) : round($toMeters, $decimalPoints));
                        return $toMeters;
                      break;
                  case 'FT'://feets
                        $toFeets  = ($round == true ? round($toFeets) : round($toFeets, $decimalPoints));
                        return $toFeets;
                      break;
                  case 'YD'://yards
                        $toYards  = ($round == true ? round($toYards) : round($toYards, $decimalPoints));
                        return $toYards;
                      break;
                  case 'NM'://Nautical miles
                        $toNauticalMiles  = ($round == true ? round($toNauticalMiles) : round($toNauticalMiles, $decimalPoints));
                        return $toNauticalMiles;
                      break;
              }


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
        
        // FROM
        $coords = explode(",", $coordinate); 
        $lat1 = $coords[0];
        $lon1 = $coords[1];

        // TO UIKA
        $lat2 = "-6.559638321828133";
        $lon2 = "106.7933585877713";
        // $lat2 = "-6.56157168370266";
        // $lon2 = "106.79061952492778";

        $countRadius = $this->distance($lat1, $lon1, $lat2, $lon2, 'FT', true, 150);
        if($countRadius < 105)
        {
          $stPosisi = true;
        }
        else
        {
          $stPosisi = false;
        }
        // return response()->json([
        //     "status" => 200,
        //     "message" => "Berhasil",  
        //     "hasil" => $countRadius,
        //     "data" => $monitoredArea[0], 
        // ], 200); 

        $data = Pembelajaran::where('token', $token)->first();
        if (isset($data)) { 
            $time = time();

            $timeCreate = strtotime($data->created_at);
            $date = strtotime('3 hours', $timeCreate);
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
                    $absensi['coordinate_absen'] = $coordinate;
                    if($data->status_kelas == 0 && $stPosisi == false){       // Jika Kelas Offline dan Tidak Masuk radius 
                        return ResponseBuilder::success(200, "Anda diluar lokasi yang di tentukan", null); 
                    }else{
                        $dummyAbsen = Absensi::create($absensi);
                        if ($dummyAbsen) {
                            $ress = ResponseBuilder::success(200, "success", $dummyAbsen);
                        } else {
                            $ress = ResponseBuilder::success(200, "error", $dummyAbsen);
                        }
                        return $ress;  
                    }
                    
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
        $npm = $request->input('npm'); 
        if(!$npm){
            return ResponseBuilder::success(200, "failed, Npm di masukan", null); 
        }

        $data = Absensi::find($id);
        if (!$data) {
            return ResponseBuilder::success(200, "error", '');
        } 

        $result = [];
        foreach (Absensi::getTableColumns() as $key) {  

            if ($request->file() != null && $request->file($key) != null) {
                
                    $files = $request->file($key);
                    $filename = basename($files->getClientOriginalName(), '.'.$files->getClientOriginalExtension()); 
                    $files->move(storage_path('app/public/tugas/'.$npm.'/'), $filename . '.' . $files->getClientOriginalExtension());
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

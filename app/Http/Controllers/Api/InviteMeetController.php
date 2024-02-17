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

use App\Models\InviteMeet; 

class InviteMeetController extends Controller
{ 
    public function index(Request $request)
    {
        $filterField = $request->input('filter');
        $filterValue = $request->input('filterValue');

        $data = InviteMeet::select([
            'id',  
            'id_meeting',
            'npm',
            'nip_dosen',
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

        if ($request->input('dataTable') == true) {
            return $dummyTable = Datatables::of($data)
            ->addIndexColumn()  
            ->make(true);
        }else{
            $data = $data->orderBy($request->input('orderField') ? $request->input('orderField') : 'id', $request->input('orderValue') ? $request->input('orderValue') : 'desc');
            if($request->input('searchData')){
                $data->where(function ($query) use ($request) {
                    foreach (InviteMeet::getTableColumns() as $value) {
                        $query->orwhere('undangan_meeting.' . $value, 'LIKE',  "%" . $request->input('searchData') . "%");
                        $query->orwhere('undangan_meeting.' . $value, 'LIKE',  "" . $request->input('searchData') . "%");
                        $query->orwhere('undangan_meeting.' . $value, 'LIKE',  "%" . $request->input('searchData') . "");
                        $query->orwhere('undangan_meeting.' . $value, 'LIKE',  $request->input('searchData'));
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
 
        foreach (InviteMeet::getTableColumns() as $key) {  

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

        $dummy = InviteMeet::create($result);
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
        $npm = $request->input('npm'); 
        if(!$npm){
            return ResponseBuilder::success(200, "failed, Npm di masukan", null); 
        }

        $data = InviteMeet::find($id);
        if (!$data) {
            return ResponseBuilder::success(200, "error", '');
        } 

        $result = [];
        foreach (InviteMeet::getTableColumns() as $key) {  

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
        $data = InviteMeet::find($id);
        if ($data) { 
            $data->delete();
            return ResponseBuilder::success(200, "success", []);
        }else{
            return ResponseBuilder::success(200, "error", []);
        } 
    }
}

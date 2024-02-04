<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', 'Api\AuthController@authenticate');
Route::post('/register', 'Api\AuthController@register');

Route::get('/pembelajaran/cek-pertemuan', 'Api\PembelajaranController@cekPertemuan');  
Route::post('/pembelajaran/store', 'Api\PembelajaranController@store');  
Route::get('/pembelajaran', 'Api\PembelajaranController@index');  
Route::get('/pembelajaran/list-pertemuan', 'Api\PembelajaranController@listPertemuan');  
Route::post('/pembelajaran/update/{id}', 'Api\PembelajaranController@update');  
Route::delete('/pembelajaran/delete/{id}', 'Api\PembelajaranController@delete');  

Route::get('/data-mhs', 'Api\AkademikController@dataMhs'); 

Route::get('/absensi', 'Api\AbsensiController@index');   
Route::post('/absensi/store', 'Api\AbsensiController@store'); 

Route::get('/absensi/scanqr', 'Api\AbsensiController@scanQr');  
Route::post('/absensi/scan-qr', 'Api\AbsensiController@scanQr'); 

Route::get('/absensi/show-qr', 'Api\AbsensiController@showQr'); 
Route::get('/absensi/download-qr', 'Api\AbsensiController@downloadQr'); 
Route::post('/absensi/update/{id}', 'Api\AbsensiController@update');  
Route::delete('/absensi/delete/{id}', 'Api\AbsensiController@delete');  

Route::get('/dosen-for-mk', 'Api\AkademikController@dosenForMk');   
Route::get('/dosen-mk', 'Api\AkademikController@dosenMk');  
Route::get('/kelas', 'Api\AkademikController@kelas');   

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/logout', 'Api\AuthController@logout');
    Route::get('/get_user', 'Api\AuthController@get_user');  
    Route::get('/refresh', 'Api\AuthController@refresh');  


     
    

    
    
    
});

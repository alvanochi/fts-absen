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



// for Absensi mahasiswa — DIKUNCI verify.tias (butuh token tias-backend yg valid).
// Sebelumnya endpoint2 ini TANPA proteksi sama sekali & nik_dosen/npm cuma dipercaya
// dari body request (siapapun bisa generate sesi/absen atas nama orang lain tanpa
// login). Lihat app/Http/Middleware/VerifyTiasToken.php.
Route::group(['middleware' => ['verify.tias']], function () {
    Route::get('/pembelajaran/cek-pertemuan', 'Api\PembelajaranController@cekPertemuan');
    Route::post('/pembelajaran/store', 'Api\PembelajaranController@store');
    Route::get('/pembelajaran', 'Api\PembelajaranController@index');
    Route::post('/pembelajaran/update/{id}', 'Api\PembelajaranController@update');
    Route::delete('/pembelajaran/delete/{id}', 'Api\PembelajaranController@delete');

    Route::get('/absensi', 'Api\AbsensiController@index');
    Route::post('/absensi/store', 'Api\AbsensiController@store');
    Route::get('/absensi/scanqr', 'Api\AbsensiController@scanQr');
    Route::post('/absensi/scan-qr', 'Api\AbsensiController@scanQr');
    Route::get('/absensi/show-qr', 'Api\AbsensiController@showQr');
    Route::get('/absensi/download-qr', 'Api\AbsensiController@downloadQr');
    Route::post('/absensi/update/{id}', 'Api\AbsensiController@update');
    Route::delete('/absensi/delete/{id}', 'Api\AbsensiController@delete');

    Route::get('/pembelajaran/list-pertemuan', 'Api\AkademikController@listPertemuan');
    Route::get('/pembelajaran/list-dosen-pertemuan', 'Api\AkademikController@listDosenPertemuan');
    Route::get('/pembelajaran/list-absen', 'Api\AkademikController@listAbsenMatkul');
});
// for Absensi mahasiswa


// for absensi Jadwal meeting — DIKUNCI verify.tias juga (pola sama persis seperti
// presensi perkuliahan: sebelumnya tanpa proteksi sama sekali). Beda dari
// pembelajaran/absensi di atas, identitas di sini (nm_kegiatan/code/name_absen) tidak
// langsung memetakan ke 1 field NIP/NPM tunggal milik 1 role tertentu (dipakai lintas
// admin/dosen/mahasiswa/pegawai) -- jadi scope fix ini CUMA wajibkan login valid,
// BELUM membedah ulang model kepemilikan undangan/absen per endpoint-nya.
Route::group(['middleware' => ['verify.tias']], function () {
    Route::get('/meeting/cek-pertemuan', 'Api\MeetingController@cekPertemuan');
    Route::post('/meeting/store', 'Api\MeetingController@store');
    Route::get('/meeting', 'Api\MeetingController@index');
    Route::get('/meeting/find', 'Api\MeetingController@find');
    Route::post('/meeting/update/{id}', 'Api\MeetingController@update');
    Route::delete('/meeting/delete/{id}', 'Api\MeetingController@delete');

    Route::post('/generate-pamplet', 'Api\ImageController@generateImage');
    Route::post('/generate-certificate', 'Api\ImageController@generateCertificate');

    Route::get('/meeting-invite', 'Api\InviteMeetController@index');
    Route::post('/meeting-invite/store', 'Api\InviteMeetController@store');
    Route::post('/meeting-invite/update/{id}', 'Api\InviteMeetController@update');
    Route::delete('/meeting-invite/delete/{id}', 'Api\InviteMeetController@delete');

    Route::get('/absensi-meeting', 'Api\AbsensiMeetController@index');
    Route::post('/absensi-meeting/store', 'Api\AbsensiMeetController@store');
    Route::get('/absensi-meeting/scanqr', 'Api\AbsensiMeetController@scanQr');
    Route::post('/absensi-meeting/scan-qr', 'Api\AbsensiMeetController@scanQr');
    Route::get('/absensi-meeting/show-qr', 'Api\AbsensiMeetController@showQr');
    Route::get('/absensi-meeting/download-qr', 'Api\AbsensiMeetController@downloadQr');
    Route::post('/absensi-meeting/update/{id}', 'Api\AbsensiMeetController@update');
    Route::delete('/absensi-meeting/delete/{id}', 'Api\AbsensiMeetController@delete');
});
// for absensi Jadwal meeting



// Lookup data mahasiswa/dosen/kelas -- bukan presensi, tapi tetap data internal,
// jadi ikut dikunci verify.tias juga (konsisten, sebelumnya publik juga).
Route::group(['middleware' => ['verify.tias']], function () {
    Route::get('/data-mhs', 'Api\AkademikController@dataMhs');
    Route::get('/dosen-for-mk', 'Api\AkademikController@dosenMk');
    Route::get('/kelas', 'Api\AkademikController@kelas');
});

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/logout', 'Api\AuthController@logout');
    Route::get('/get_user', 'Api\AuthController@get_user');  
    Route::get('/refresh', 'Api\AuthController@refresh');   
    
});

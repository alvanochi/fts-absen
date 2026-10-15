<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Endpoint presensi (pembelajaran/absensi) di app ini sebelumnya TANPA proteksi
 * sama sekali -- nik_dosen/npm cuma dipercaya dari body request, siapapun bisa
 * generate sesi kelas atau absen atas nama orang lain tanpa login.
 *
 * Middleware ini memverifikasi token yang sama dipakai login di tias-backend
 * (fe-ucl kirim token itu di header utk SEMUA request, termasuk ke app ini --
 * cuma sebelumnya tidak pernah dicek) dengan cara tanya balik ke tias-backend
 * sendiri (GET /profile/getDataPribadi, endpoint yg sudah ada & sudah dipakai
 * fe-ucl). Identitas terverifikasi (nip/npm/role) ditaruh di
 * $request->attributes->get('tias_user') -- controller WAJIB pakai ini,
 * BUKAN nik_dosen/npm dari body, utk field yg menentukan identitas pemilik data.
 */
class VerifyTiasToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('token') ?? $request->query('token') ?? $request->input('token');

        if (!$token) {
            return response()->json([
                'status' => 401,
                'message' => 'Token tidak ditemukan. Silakan login ulang.',
                'data' => null,
            ], 401);
        }

        $baseUrl = rtrim(env('TIAS_BACKEND_API_URL', ''), '/');
        if (!$baseUrl) {
            Log::error('VerifyTiasToken: TIAS_BACKEND_API_URL belum diset di .env');
            return response()->json([
                'status' => 500,
                'message' => 'Konfigurasi server belum lengkap.',
                'data' => null,
            ], 500);
        }

        try {
            $response = Http::withHeaders(['token' => $token])
                ->timeout(8)
                ->get($baseUrl . '/profile/getDataPribadi');
        } catch (\Exception $e) {
            Log::error('VerifyTiasToken: gagal menghubungi tias-backend: ' . $e->getMessage());
            return response()->json([
                'status' => 502,
                'message' => 'Gagal memverifikasi sesi, coba lagi.',
                'data' => null,
            ], 502);
        }

        if (!$response->successful()) {
            return response()->json([
                'status' => 401,
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
                'data' => null,
            ], 401);
        }

        $data = $response->json('data') ?? [];

        $request->attributes->set('tias_user', [
            'user_id' => $data['user_id'] ?? null,
            'nip' => $data['nip'] ?? null,
            'npm' => $data['npm'] ?? null,
            'nidn' => $data['nidn'] ?? null,
            'role' => $data['role'] ?? null,
            'nama_lengkap' => $data['nama_lengkap'] ?? null,
            // department_code & email dipakai utk kunci scope rekap presensi per-prodi
            // admin (lihat AkademikController@listDosenPertemuan/listPertemuan).
            'department_code' => $data['department_code'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        return $next($request);
    }
}

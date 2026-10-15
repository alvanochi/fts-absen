<?php

namespace App\Http\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cari mata kuliah (nama/sks) by kode lewat API publik SIAK v2 (be-siakad) --
 * pengganti siak_course lokal (second_db/DB siakuika/SIAK v1) yg sudah tidak
 * disinkron sejak migrasi ke SIAK v2. Dipakai di AkademikController@listAbsenMatkul
 * dan PembelajaranController@index (kolom "matkul" utk tabel Daftar Hadir).
 */
class SiakV2Matkul
{
    public static function find(?string $kode): ?array
    {
        if (!$kode) {
            return null;
        }

        $baseUrl = rtrim(env('SIAK_V2_API_URL', ''), '/');
        if (!$baseUrl) {
            return null;
        }

        try {
            $response = Http::timeout(8)->get($baseUrl . '/public/mata-kuliah', [
                'search' => $kode,
            ]);
            if (!$response->successful()) {
                return null;
            }
            foreach (($response->json('data') ?? []) as $row) {
                if (($row['kode'] ?? null) === $kode) {
                    return [
                        'code' => $row['kode'],
                        'name' => $row['nama'] ?? null,
                        'credit' => $row['totalSks'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('SiakV2Matkul::find gagal ambil data dari SIAK v2: ' . $e->getMessage());
        }

        return null;
    }
}

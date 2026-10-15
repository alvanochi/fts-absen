<?php

namespace App\Http\Helper;

use Illuminate\Http\Client\Pool;
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

    /**
     * Versi batch dari find() -- panggil SIAK v2 SEKALI PARALEL per kode unik (Http::pool),
     * bukan satu-satu berurutan. Dipakai di tabel yg nampilin banyak baris sekaligus (mis.
     * Daftar Hadir dgn length=-1/tanpa pagination) -- lookup berurutan bikin halaman
     * menggantung kalau kode matkul unik-nya banyak.
     *
     * @return array<string, array|null> map kode => data matkul (atau null kalau tdk ketemu)
     */
    public static function findMany(array $kodeList): array
    {
        $kodeList = array_values(array_unique(array_filter($kodeList)));
        $result = array_fill_keys($kodeList, null);
        if (empty($kodeList)) {
            return $result;
        }

        $baseUrl = rtrim(env('SIAK_V2_API_URL', ''), '/');
        if (!$baseUrl) {
            return $result;
        }

        try {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($kode) => $pool->as($kode)->timeout(8)->get($baseUrl . '/public/mata-kuliah', ['search' => $kode]),
                $kodeList
            ));

            foreach ($kodeList as $kode) {
                $response = $responses[$kode] ?? null;
                if (!$response || !method_exists($response, 'successful') || !$response->successful()) {
                    continue;
                }
                foreach (($response->json('data') ?? []) as $row) {
                    if (($row['kode'] ?? null) === $kode) {
                        $result[$kode] = [
                            'code' => $row['kode'],
                            'name' => $row['nama'] ?? null,
                            'credit' => $row['totalSks'] ?? null,
                        ];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SiakV2Matkul::findMany gagal ambil data dari SIAK v2: ' . $e->getMessage());
        }

        return $result;
    }
}

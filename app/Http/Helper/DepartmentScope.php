<?php

namespace App\Http\Helper;

/**
 * Pola sama persis seperti middleware/authMiddleware.js (isSuperAdmin) dan
 * middleware/departmentScope.js (resolveDepartmentScope) di tias-backend --
 * superadmin (allowlist email, SUPERADMIN_EMAILS) bypass semua kunci prodi,
 * admin biasa WAJIB difilter ke department_code miliknya sendiri. Dipakai di
 * AkademikController@listDosenPertemuan/listPertemuan.
 */
class DepartmentScope
{
    public static function isSuperAdmin(?array $tiasUser): bool
    {
        if (!$tiasUser || empty($tiasUser['email'])) {
            return false;
        }
        $emails = array_filter(array_map('trim', explode(',', env('SUPERADMIN_EMAILS', ''))));
        $emails = array_map('strtolower', $emails);
        return in_array(strtolower($tiasUser['email']), $emails, true);
    }

    /**
     * ['allowed' => false] -- akun ini tidak boleh akses sama sekali (bukan admin,
     *   atau admin tapi belum terhubung ke prodi manapun).
     * ['allowed' => true, 'code' => null] -- superadmin, tidak perlu difilter.
     * ['allowed' => true, 'code' => 'FT_TI'] -- admin biasa, WAJIB difilter ke kode ini.
     */
    public static function resolve(?array $tiasUser): array
    {
        if (self::isSuperAdmin($tiasUser)) {
            return ['allowed' => true, 'code' => null];
        }
        if (!$tiasUser || ($tiasUser['role'] ?? null) !== 'Admin') {
            return ['allowed' => false, 'code' => null];
        }
        $code = $tiasUser['department_code'] ?? null;
        if (!$code) {
            return ['allowed' => false, 'code' => null];
        }
        return ['allowed' => true, 'code' => $code];
    }
}

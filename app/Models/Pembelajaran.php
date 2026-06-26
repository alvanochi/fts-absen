<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class Pembelajaran extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'pembelajaran';
    protected $fillable = [ 
        'id', 
        'id_lecture',
        'nik_dosen',
        'id_matkul',
        'pertemuan', 
        'kelas',
        'rps_dasar',
        'rps_pelaksanaan', 
        'nidn_dosen_pengganti',
        'dosen_tamu',
        'npm_komti',
        'name_komti',
        'status_kelas', 
        'learning_done',
        'qrcode',
        'token',
        'deleted_at' 
    ];
 

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('pembelajaran.deleted_at');
        });
    }

    public function dosen()
    {
        return $this->belongsTo(Simpeg_Pegawai::class, 'nik_dosen', 'nip');
    }

    public function dosenPengganti()
    {
        return $this->belongsTo(Simpeg_Pegawai::class, 'nidn_dosen_pengganti', 'nip');
    }

    public function matkul()
    {
        return $this->belongsTo(Siak_Course::class, 'id_matkul', 'code');
    }

    public function absen()
    {
        return $this->hasMany(Absensi::class,  'id_pembelajaran', 'id');
    }
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('pembelajaran');
    }  

    public function getIdMatkulAttribute($value)
    {
        return trim(strtoupper($value));
    }
}

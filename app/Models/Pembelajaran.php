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
        'nik_dosen',
        'id_matkul',
        'pertemuan', 
        'kelas',
        'status_kelas', 
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
}

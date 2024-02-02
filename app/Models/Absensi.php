<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class Absensi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'absensi_mhs';
    protected $fillable = [ 
        'id', 
        'id_pembelajaran',
        'npm',
        'status_absen',  
        'coordinate_absen',
        'deleted_at' 
    ];
 

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('absensi_mhs.deleted_at');
        });
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Pmb_Candidate::class, 'npm', 'student_code');
    }

    public function pembelajaran()
    {
        return $this->belongsTo(Pembelajaran::class, 'id_pembelajaran', 'id');
    }
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('absensi_mhs');
    }  
}

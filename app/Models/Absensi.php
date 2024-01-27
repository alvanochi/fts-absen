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
        'deleted_at' 
    ];
 

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('absensi_mhs.deleted_at');
        });
    }
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('absensi_mhs');
    }  
}

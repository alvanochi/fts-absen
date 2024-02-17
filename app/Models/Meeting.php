<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class Meeting extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'meeting';
    protected $fillable = [ 
        'id',   
        'nik_dosen', 

        'nm_pengundang', 
        'nm_kegiatan', 
        'ruangan', 
        'bukti_foto', 

        'pertemuan', 
        'notulen',  
        'status_ruangan',  
        'token',
        'deleted_at' 
    ];
 

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('meeting.deleted_at');
        });
    }
 
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('meeting');
    }  
}

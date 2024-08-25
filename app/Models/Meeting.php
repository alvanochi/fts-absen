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
        'id_group_tias',
        'nm_pengundang', 
        'nm_kegiatan', 
        'tipe_kegiatan',
        'sub_tema',
        'ruangan', 
        'bukti_foto', 
        'narsum',
        'ket_narsum',
        'link_online',
        'pertemuan', 
        'tanggal', 
        'waktu', 
        'waktu_end', 
        'notulen',  
        'status_ruangan',  
        'token',
        'contact',
        'created_at',
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

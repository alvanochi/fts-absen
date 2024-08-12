<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class AbsensiMeet extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'absensi_meeting';
    protected $fillable = [ 
        'id', 
        'id_meeting',
        'code',
        'name_absen',
        'status_absen',  
        'coordinate_absen',
        'deleted_at' 
    ];
 
    
    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'id_meeting', 'id');
    }

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('absensi_meeting.deleted_at');
        });
    } 
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('absensi_meeting');
    }  
}

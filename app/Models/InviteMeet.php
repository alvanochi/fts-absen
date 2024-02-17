<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class InviteMeet extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'undangan_meeting';
    protected $fillable = [ 
        'id',  
        'id_meeting',
        'npm',
        'nip_dosen',
        'created_at',
        'deleted_at' 
    ];
 

    public static function boot()
    {
        parent::boot();
  
        static::addGlobalScope(function ($query) {
            $query->whereNull('undangan_meeting.deleted_at');
        });
    }
 
    // public function pembelajaran()
    // {
    //     return $this->hasMany(Pembelajaran::class, 'id', 'id_pembelajaran');
    // }
    
  
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('undangan_meeting');
    }  
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpeg_Pegawai extends Model
{
    protected $connection = 'second_db';
    protected $table = 'simpeg_pegawai';
    protected $fillable = [ 
        'id', 
        'nip',
        'nama',
        'gelar_belakang',  
        "branch",
        "division",
        "title"
    ];

    public static function boot()
    {
        parent::boot();
   
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('simpeg_pegawai');
    }  

    public function getNipAttribute($value)
    {
        return trim($value);
    }
}

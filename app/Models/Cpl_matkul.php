<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpl_matkul extends Model
{
    protected $connection = 'three_db';
    protected $table = 'mataKuliah';
    // protected $fillable = [
    //     'id_bk',
    //     'kode_mk',
    //     'course_code',
    //     'name_mk',
    //     'std_nilai',
    //     'status_mk', 
    //     'deleted_at' 
    // ];

    public static function boot()
    {
        parent::boot();
   
        static::addGlobalScope(function ($query) {
            $query->whereNull('mataKuliah.deleted_at');
        });
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('mataKuliah');
    }  

    public function pertemuanBelajar()
    {
        return $this->belongsTo(Cpl_pertemuan::class, 'id', 'id_matkul');
    }
}

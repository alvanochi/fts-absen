<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Lecture extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_lecture'; 

    public static function boot()
    {
        parent::boot();
   
    }

    public function matkul()
    {
        return $this->belongsTo(Siak_Course::class, 'course_code', 'code');
    }

    public function lecturer()
    {
        return $this->belongsTo(Siak_Lecturer::class, 'lecturer_code', 'code');
    }

    public function pembelajaran()
    {
        return $this->hasMany(Pembelajaran::class,  'id_matkul', 'course_code');
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('siak_lecture');
    }  

}

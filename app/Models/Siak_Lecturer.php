<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Lecturer extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_lecturer'; 

    public static function boot()
    {
        parent::boot();
   
    }

    public function lecture()
    {
        return $this->hasMany(Siak_Lecture::class, 'lecturer_code', 'code');
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('siak_lecturer');
    }  

}

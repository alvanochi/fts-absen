<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Course extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_course'; 

    public static function boot()
    {
        parent::boot();
   
    } 

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('siak_course');
    }  

    public function getCodeAttribute($value)
    {
        return trim(strtoupper($value));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Curriculum extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_curriculum'; 

    public static function boot()
    {
        parent::boot();
   
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('siak_curriculum');
    }  

}

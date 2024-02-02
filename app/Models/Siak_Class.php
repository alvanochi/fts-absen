<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Class extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_class'; 

    public static function boot()
    {
        parent::boot();
   
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('siak_class');
    }  

}

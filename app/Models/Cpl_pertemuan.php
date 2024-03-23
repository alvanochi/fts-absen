<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpl_pertemuan extends Model
{
    protected $connection = 'three_db';
    protected $table = 'pembelajaran_matkul'; 

    public static function boot()
    {
        parent::boot();
   
        static::addGlobalScope(function ($query) {
            $query->whereNull('pembelajaran_matkul.deleted_at');
        });
    }

    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing('pembelajaran_matkul');
    }   
}

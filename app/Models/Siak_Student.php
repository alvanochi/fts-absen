<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siak_Student extends Model
{
    protected $connection = 'second_db';
    protected $table = 'siak_student';
}

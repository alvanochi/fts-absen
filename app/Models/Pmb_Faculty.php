<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pmb_Faculty extends Model
{
    protected $connection = 'second_db';
    use HasFactory;
}

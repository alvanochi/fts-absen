<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pmb_Candidate extends Model
{
    protected $connection = 'second_db';
    protected $table = 'pmb_candidate';
}

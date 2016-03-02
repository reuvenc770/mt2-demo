<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class UniqueProfile extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'UniqueProfile';
    protected $primaryKey = 'profile_id';
}

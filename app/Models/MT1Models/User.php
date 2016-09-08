<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'user';
    protected $primaryKey = 'user_id';

}
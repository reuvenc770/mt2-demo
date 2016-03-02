<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class ListProfile extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'list_profile';
    protected $primaryKey = 'profile_id';
}
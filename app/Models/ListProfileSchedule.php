<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileSchedule extends Model
{
    protected $guarded = ['id'];
    protected $connection = 'list_profile';
}

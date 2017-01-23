<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AWeberEmailActionsStorage extends Model
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppression extends Model
{
    CONST TYPE_UNSUB = 1;
    CONST TYPE_HARD_BOUNCE = 2;
    CONST TYPE_COMPLAINT = 3;
    protected $guarded = ['id'];
}

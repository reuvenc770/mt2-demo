<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEventLog extends Model
{
   CONST SUCCESS = 1;
   CONST FAILED = 2;
   CONST VALIDATION_FAILED = 3;
   CONST UNAUTHORIZED = 4;
   CONST ERROR = 5;
   protected $guarded = ['id'];
}

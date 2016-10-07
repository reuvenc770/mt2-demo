<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class Country extends Model implements IReport
{
    protected $guarded = ['id'];
    public $timestamps = false;
}
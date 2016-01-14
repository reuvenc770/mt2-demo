<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class BlueHornetReport extends Model implements IReport
{
    protected $guarded = ['id'];
}

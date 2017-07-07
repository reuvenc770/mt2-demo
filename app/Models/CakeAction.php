<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IReport;

class CakeAction extends Model implements IReport
{
    protected $connection = 'reporting_data';
    protected $guarded = ['id'];
}

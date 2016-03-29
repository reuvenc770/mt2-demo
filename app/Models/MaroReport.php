<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class MaroReport extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

}
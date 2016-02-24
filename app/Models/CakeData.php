<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class CakeData extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $table = 'cake_aggregated_data';
    protected $connection = "reporting_data";
}
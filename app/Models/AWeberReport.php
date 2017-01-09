<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class AWeberReport extends Model implements IReport
{
    CONST UNIQUE_OPENS = 1;
    CONST UNIQUE_CLICKS = 2;
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

}

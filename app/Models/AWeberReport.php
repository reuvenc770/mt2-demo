<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

class AWeberReport extends Model implements IReportMapper
{
    CONST UNIQUE_OPENS = 1;
    CONST UNIQUE_CLICKS = 2;
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "datetime";
    }

    public function getSubjectFieldName(){
        return "subject";
    }

}

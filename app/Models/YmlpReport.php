<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

class YmlpReport extends Model implements IReportMapper {
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "date";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}

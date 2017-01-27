<?php

namespace App\Models;

use App\Facades\DeployActionEntry;
use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;
use Log;
class StandardReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    //TODO  This should really be created during deploy process, but needs to be triggered now for dashboard
    public static function boot()
    {
        parent::boot();

        static::created(function($report)
        {
            DeployActionEntry::initDeployActions($report);
        });

    }

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function getDateFieldName(){
        return "datetime";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}

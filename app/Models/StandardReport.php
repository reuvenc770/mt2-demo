<?php

namespace App\Models;

use App\Facades\CampaignActionsEntry;
use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;
use Log;
class StandardReport extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    //TODO  This should really be created during deploy process, but needs to be triggered now for dashboard
    public static function boot()
    {
        parent::boot();

        static::created(function($report)
        {
            Log::info("i am here");
            CampaignActionsEntry::initCampaignActions($report);
        });

    }

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }





}

<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserTracking extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'advertiser_tracking';
    protected $primaryKey = 'tracking_id';

}
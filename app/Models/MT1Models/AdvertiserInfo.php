<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserInfo extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'advertiser_info';
}

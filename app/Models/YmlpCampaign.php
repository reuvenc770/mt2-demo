<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YmlpCampaign extends Model {
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreativeClickthroughRate extends Model
{
    protected $connection = 'mt2_reports';
    protected $fillable = ['creative_id', 'list_profile_id', 'deploy_id', 'opens', 'clicks'];
}

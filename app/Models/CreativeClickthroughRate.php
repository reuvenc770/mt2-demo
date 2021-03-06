<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeClickthroughRate extends Model
{
    protected $primaryKey = ['creative_id', 'list_profile_combine_id', 'deploy_id'];
    protected $connection = 'reporting_data';
    protected $fillable = ['creative_id', 'list_profile_combine_id', 'deploy_id', 'opens', 'clicks'];

    public function creative() {
        return $this->belongsTo('App\Models\Creative');
    }
}

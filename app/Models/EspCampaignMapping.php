<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspCampaignMapping extends Model
{
    protected $guarded = [];
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

}

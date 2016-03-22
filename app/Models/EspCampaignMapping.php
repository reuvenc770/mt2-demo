<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspCampaignMapping extends Model
{
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

}

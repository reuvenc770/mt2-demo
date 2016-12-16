<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class LiveEspAdvertiserJoin extends Model {
    protected $connection = 'legacy_data_sync';
    protected $table = 'EspAdvertiserJoin';
    protected $primaryKey = 'subAffiliateID';
    public $timestamps = false;
}

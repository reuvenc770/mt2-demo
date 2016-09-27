<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class EspAdvertiserJoin extends Model {
    protected $connection = 'mt1_data';
    protected $table = 'EspAdvertiserJoin';
    protected $primaryKey = 'subAffiliateID';
}

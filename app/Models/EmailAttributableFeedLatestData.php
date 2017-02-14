<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAttributableFeedLatestData extends Model
{
    protected $table = 'email_attributable_feed_latest_data';
    protected $guarded = [];

    const ATTRIBUTED = "ATTR";
    const PASSED_DUE_TO_RESPONDER = "POR";
    const PASSED_DUE_TO_ATTRIBUTION = "POA";
    const LOST_ATTRIBUTION = "MOA";
}

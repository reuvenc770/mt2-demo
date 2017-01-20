<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedAction extends Model
{
    protected $guarded = [];
    const DELIVERABLE = "Deliverable";
    const PASSED_DUE_TO_RESPONDER = "POR";
    const PASSED_DUE_TO_ATTRIBUTION = "POA";
    const LOST_ATTRIBUTION = "MOA";
    const OPENER = "Opener";
    const CLICKER = "Clicker";
    const CONVERTER = "Converter";
}

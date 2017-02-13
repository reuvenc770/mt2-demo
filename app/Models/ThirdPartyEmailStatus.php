<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyEmailStatus extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'email_id';

    const DELIVERABLE = "None";
    const OPENER = "Opener";
    const CLICKER = "Clicker";
    const CONVERTER = "Converter";
}

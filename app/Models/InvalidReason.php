<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvalidReason extends Model
{
    public $timestamps = false;
    const NO_EMAIL_ADDRESS = 1;
    const NO_IP = 2;
    const NO_CAPTURE_DATE = 3;
    const NO_PASSWORD = 4;
    const BAD_SOURCE_URL = 5;
    const BAD_IP_ADDRESS = 6;
    const BAD_DOMAIN = 7;
    const CANADA = 8;
    const EMAIL = 9;
    const OTHER_INVALIDATION = 10;
}
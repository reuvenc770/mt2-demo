<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class MailingTemplate extends Model
{
    use ModelCacheControl;
    CONST NORMAL_HTML = ["id" => 0, "name" => "Normal Html"];
    CONST HTML_LITE =  ["id" => 1, "name" => "HTML Lite (no images)"];
    CONST IMAGE_ONLY =  ["id" => 2, "name" => "Image Only"];
    CONST IMAGE_MAP =  ["id" => 3, "name" => "Image Map"];
    CONST NEWSLETTER =  ["id" => 4, "name" => "Newsletter"];
    CONST CLICK_BUTTON =  ["id" => 5, "name" => "Clickable Button"];

    protected $guarded = ['id'];
    protected $timestamps = false;

}
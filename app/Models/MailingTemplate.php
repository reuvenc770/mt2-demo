<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class MailingTemplate extends Model
{
    use ModelCacheControl;
    CONST NORMAL_HTML = ["id" => 1, "name" => "Normal Html"];
    CONST HTML_LITE =  ["id" => 2, "name" => "HTML Lite (no images)"];
    CONST IMAGE_ONLY =  ["id" => 3, "name" => "Image Only"];
    CONST IMAGE_MAP =  ["id" => 4, "name" => "Image Map"];
    CONST NEWSLETTER =  ["id" => 5, "name" => "Newsletter"];
    CONST CLICK_BUTTON =  ["id" => 6, "name" => "Clickable Button"];

    protected $guarded = ['id'];
    public $timestamps = false;

    public function espAccounts()
    {
        return $this->belongsToMany('App\Models\espAccount' , 'esp_account_mailing_templates');
    }
}
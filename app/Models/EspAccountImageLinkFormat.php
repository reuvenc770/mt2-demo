<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspAccountImageLinkFormat extends Model {
    protected $guarded = [];
    protected $primaryKey = 'esp_account_id';
    public $timestamps = false;

    public function espAccount() {
        return $this->belongsTo('App\Models\EspAccount');
    }
}

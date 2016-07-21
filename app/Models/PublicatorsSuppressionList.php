<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicatorsSuppressionList extends Model {
  
    public $timestamps = false;

    public function espAccount() {
        return $this->hasOne('App\Model\EspAccount', 'account_name');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicatorsSuppressionList extends Model {
  
    protected $guarded = ['id'];

    public function espAccount() {
        return $this->hasOne('App\Model\EspAccount', 'account_name');
    }
}
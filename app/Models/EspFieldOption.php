<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspFieldOption extends Model {
    
    protected $primaryKey = 'esp_id';
    protected $guarded = [];

    public function esp() {
        return $this->belongsTo('App\Models\Esp');
    }
}

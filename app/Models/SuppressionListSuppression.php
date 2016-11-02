<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionListSuppression extends Model {

    protected $connection = 'suppression';
    protected $guarded = [''];
    
    public function list() {
        return $this->belongsTo('App\Models\SuppressionList');
    }
}

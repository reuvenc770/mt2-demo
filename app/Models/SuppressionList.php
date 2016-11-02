<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionList extends Model {

    protected $connection = 'suppression';
    protected $guarded = [''];

    public function suppressions() {
        return $this->hasMany('App\Models\SuppressionListSuppressions');
    }
}

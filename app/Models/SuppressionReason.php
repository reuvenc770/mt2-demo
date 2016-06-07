<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionReason extends Model {

    protected $guarded = ['id'];
    public $timestamps = false;

    public function suppressions() {
        return $this->hasMany('App\Model\Suppression');
    }

    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

    /**
     * Scope a query to only include active reasons
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisplayable($query)
    {
        return $query->where('display',true);
    }
}

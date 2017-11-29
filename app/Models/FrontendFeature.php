<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontendFeature extends Model
{
    protected $guarded = [];

    public function pages() {
        return $this->hasMany('App\Models\Page');
    }

    public function permissions() {
        return $this->belongsToMany('App\Models\Permission', 'frontend_feature_permission_mappings', 'frontend_feature_id', 'permission_id');
    }
}

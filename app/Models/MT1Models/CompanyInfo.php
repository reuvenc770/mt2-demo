<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model {
    protected $connection = 'mt1mail';
    protected $table = 'company_info';

    public function offers() {
        return $this->hasMany('App\Models\MT1Models\AdvertiserInfo', 'company_id', 'company_id');
    }
}

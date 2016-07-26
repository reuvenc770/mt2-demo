<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserInfo extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'advertiser_info';

    public function actualAdvertiser() {
        return $this->belongsTo('App\Models\MT1Models\CompanyInfo', 'company_id', 'company_id');
    }
}

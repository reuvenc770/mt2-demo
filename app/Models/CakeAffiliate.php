<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeAffiliate extends Model
{
    protected $guarded = [ 'id' ];

    public function proxies () {
        return $this->hasMany( 'App\Models\Proxy' , 'cake_affiliate_id' , 'id' );
    }
}
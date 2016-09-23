<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPayoutType extends Model
{
    public function offers() {
        return $this->hasMany('App\Models\Offer');
    }
}

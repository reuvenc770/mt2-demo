<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferPayoutType
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayoutType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayoutType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayoutType whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayoutType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfferPayoutType extends Model
{
    public function offers() {
        return $this->hasMany('App\Models\Offer');
    }
}

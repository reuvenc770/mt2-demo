<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeRedirectDomain
 *
 * @property int $id
 * @property int $cake_affiliate_id
 * @property int $offer_payout_type_id
 * @property string $redirect_domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereCakeAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereOfferPayoutTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereRedirectDomain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeRedirectDomain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CakeRedirectDomain extends Model
{
    protected $guarded = ['id'];
}

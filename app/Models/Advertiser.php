<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Advertiser
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Advertiser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Advertiser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Advertiser whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Advertiser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Advertiser extends Model {
  
    protected $fillable = ['id', 'name'];

    public function offers() {
        return $this->hasMany('App\Models\Offer');
    }
}
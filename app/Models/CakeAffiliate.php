<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeAffiliate
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Proxy[] $proxies
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeAffiliate whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeAffiliate whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeAffiliate whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeAffiliate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CakeAffiliate extends Model
{
    protected $guarded = [ 'id' ];

    public function proxies () {
        return $this->hasMany( 'App\Models\Proxy' , 'cake_affiliate_id' , 'id' );
    }
}

<?php

namespace App\Models;

use App\Models\ModelTraits\Deletable;
use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Proxy
 *
 * @property int $id
 * @property string $name
 * @property string $ip_addresses
 * @property string $provider_name
 * @property string $esp_account_names
 * @property string $isp_names
 * @property string $notes
 * @property bool $status
 * @property string $dba_name
 * @property int $cake_affiliate_id
 * @property-read \App\Models\CakeAffiliate $cakeAffiliate
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy activeFirst()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereCakeAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereDbaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereEspAccountNames($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereIpAddresses($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereIspNames($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereProviderName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Proxy whereStatus($value)
 * @mixin \Eloquent
 */
class Proxy extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];
    public $timestamps = false;


    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }

    public function cakeAffiliate () {
        return $this->hasOne( 'App\Models\CakeAffiliate' , 'id' , 'cake_affiliate_id' );
    }
}

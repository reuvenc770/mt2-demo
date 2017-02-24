<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Domain
 *
 * @property int $id
 * @property string $domain_name
 * @property string $main_site
 * @property int $registrar_id
 * @property int $proxy_id
 * @property int $esp_account_id
 * @property string $created_at
 * @property string $expires_at
 * @property int $doing_business_as
 * @property bool $domain_type
 * @property bool $status
 * @property bool $live_a_record
 * @property-read \App\Models\Esp $esp
 * @property-read \App\Models\EspAccount $espAccount
 * @property-read \App\Models\Proxy $proxy
 * @property-read \App\Models\Registrar $registrar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereDoingBusinessAs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereDomainName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereDomainType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereExpiresAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereLiveARecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereMainSite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereProxyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereRegistrarId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Domain whereStatus($value)
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use ModelCacheControl;
    CONST MAILING_DOMAIN = 1;
    CONST CONTENT_DOMAIN = 2;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function espAccount(){
        return $this->hasOne('App\Models\EspAccount');
    }

    public function esp(){
        return $this->hasOne('App\Models\Esp');
    }
    public function proxy(){
        return $this->hasOne('App\Models\Proxy');
    }
    public function registrar(){
        return $this->hasOne('App\Models\Registrar');
    }

    public function contentDomainValidForEspAccount($espAccountId) {
        if ($this->esp_account_id === $espAccountId
            && $this->domain_type === self::CONTENT_DOMAIN
            && $this->status === 1
            && $this->live_a_record === 1) {

            return true;
        }

        return false;
    }

}

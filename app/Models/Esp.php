<?php

namespace App\Models;

use App\Models\ModelTraits\Deletable;
use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Esp
 *
 * @property int $id
 * @property string $name
 * @property string $nickname
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\EspCampaignMapping $accountMapping
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EspAccount[] $espAccounts
 * @property-read \App\Models\EspFieldOption $fieldOptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SuppressionReason[] $suppressionReasons
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Esp whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Esp whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Esp whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Esp whereNickname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Esp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Esp extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];

    public function espAccounts()
    {
        return $this->hasMany('App\Models\EspAccount');
    }

    public function accountMapping()
    {
        return $this->hasOne('App\Models\EspCampaignMapping');
    }

    public function suppressionReasons(){
        return $this->hasMany('App\Models\SuppressionReason');
    }

    public function fieldOptions() {
        return $this->hasOne('App\Models\EspFieldOption');
    }
}

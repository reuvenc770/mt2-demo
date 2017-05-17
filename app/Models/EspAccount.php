<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;
use App\Models\EspAccountCustomIdHistory;
use Carbon\Carbon;

use Storage;
/**
 * App\Models\EspAccount
 *
 * @property int $id
 * @property string $account_name
 * @property int $custom_id
 * @property string $key_1
 * @property string $key_2
 * @property int $esp_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property bool $enable_stats
 * @property bool $enable_suppression
 * @property-read \App\Models\OAuthTokens $OAuthTokens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AWeberReport[] $aweberReport
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlueHornetReport[] $blueHornetReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CampaignerReport[] $campaignerReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EspAccountCustomIdHistory[] $customIds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Domain[] $domains
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailDirectReport[] $emailDirectReport
 * @property-read \App\Models\Esp $esp
 * @property-read \App\Models\EspAccountImageLinkFormat $imageLinkFormat
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MailingTemplate[] $mailingTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MaroReport[] $maroReport
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DeployRecordRerun[] $rerunDeploys
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Suppression[] $suppressions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\YmlpReport[] $ymlpReport
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereAccountName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereCustomId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereEnableSuppression($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereEspId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereKey1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereKey2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspAccount extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

    public function blueHornetReports(){
        return $this->hasMany('App\Models\BlueHornetReport');
    }

    public function campaignerReports(){
        return $this->hasMany('App\Models\CampaignerReport');
    }

    public function emailDirectReport(){
        return $this->hasMany('App\Models\EmailDirectReport');
    }

    public function maroReport() {
        return $this->hasMany('App\Models\MaroReport');
    }

    public function aweberReport(){
        return $this->hasMany('App\Models\AWeberReport');
    }

    public function getResponseReport(){
        return $this->hasMany('App\Models\GetResponseReport');
    }

    public function ymlpReport() {
        return $this->hasMany('App\Models\YmlpReport');
    }

    public function OAuthTokens(){
        return $this->hasOne('App\Models\OAuthTokens');
    }

    public function getFirstKey()
    {
        return $this->attributes['key_1'];
    }

    public function getSecondKey()
    {
        return $this->attributes['key_2'];
    }

    public function suppressions()
    {
        return $this->hasMany('App\Models\Suppression');
    }

    public function rerunDeploys() {
        return $this->hasMany('App\Models\DeployRecordRerun');
    }

    public function mailingTemplate()
    {
        return $this->belongsToMany('App\Models\MailingTemplate');
    }

    public function domains(){
        return $this->hasMany('App\Models\Domain');
    }

    public function imageLinkFormat() {
        return $this->hasOne('App\Models\EspAccountImageLinkFormat');
    }

    public function customIds(){
        return $this->hasMany('App\Models\EspAccountCustomIdHistory');
    }

    public static function boot()
    {
        parent::boot();

        $callback = function( $espAccount ){
            if ($espAccount['custom_id'] != null){
                $customIdHistory = new EspAccountCustomIdHistory();
                $customIdHistory->custom_id = $espAccount->custom_id;
                $customIdHistory->esp_account_id = $espAccount->id;
                $customIdHistory->created_at = Carbon::now()->toDateTimeString();
                $customIdHistory->save();
            }
        };

        static::created( $callback );

        static::updated( $callback );
    }

}

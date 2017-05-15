<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Deploy
 *
 * @property int $id
 * @property string $deploy_name
 * @property string $send_date
 * @property int $esp_account_id
 * @property string $external_deploy_id
 * @property int $offer_id
 * @property int $creative_id
 * @property int $from_id
 * @property int $subject_id
 * @property int $template_id
 * @property int $mailing_domain_id
 * @property int $content_domain_id
 * @property int $list_profile_combine_id
 * @property int $cake_affiliate_id
 * @property bool $encrypt_cake
 * @property bool $fully_encrypt
 * @property string $url_format
 * @property string $notes
 * @property bool $deployment_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $user_id
 * @property bool $party
 * @property-read \App\Models\Domain $contentDomain
 * @property-read \App\Models\Creative $creative
 * @property-read \App\Models\EspAccount $espAccount
 * @property-read \App\Models\From $from
 * @property-read \App\Models\ListProfileCombine $listProfileCombine
 * @property-read \App\Models\Domain $mailingDomain
 * @property-read \App\Models\MailingTemplate $mailingTemplate
 * @property-read \App\Models\Offer $offer
 * @property-read \App\Models\StandardReport $standardReport
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereCakeAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereContentDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereDeployName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereDeploymentStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereEncryptCake($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereExternalDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereFromId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereFullyEncrypt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereListProfileCombineId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereMailingDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereParty($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereSendDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereSubjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereTemplateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereUrlFormat($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Deploy whereUserId($value)
 * @mixin \Eloquent
 */
class Deploy extends Model
{
    protected $guarded = [];
    protected $hidden = array('created_at', 'updated_at');
    const PENDING_PACKAGE_STATUS = 2;
    const VERIFIED_PACKAGE_STATUS = 3;
    const NO_PACKAGE_STATUS = 0;
    const CREATED_PACKAGE_STATUS = 1;
    use ModelCacheControl;

    public function listProfileCombine(){
        return $this->belongsTo('App\Models\ListProfileCombine');
    }

    public function espAccount(){
        return $this->belongsTo('App\Models\EspAccount');
    }

    public function offer(){
        return $this->belongsTo('App\Models\Offer');
    }

    // The next two are stored in the same table
    public function mailingDomain() {
        return $this->belongsTo('App\Models\Domain', 'mailing_domain_id');
    }

    public function contentDomain() {
        return $this->belongsTo('App\Models\Domain', 'content_domain_id');
    }

    public function mailingTemplate() {
        return $this->belongsTo('App\Models\MailingTemplate', 'template_id', 'id');
    }

    public function standardReport(){
        return $this->hasOne('App\Models\StandardReport', 'external_deploy_id');
    }

    public function creative() {
        return $this->belongsTo('App\Models\Creative');
    }

    public function from() {
        return $this->belongsTo('App\Models\From');
    }

    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Email
 *
 * @property int $id
 * @property string $email_address
 * @property int $email_domain_id
 * @property string $lower_case_md5
 * @property string $upper_case_md5
 * @property string $created_at
 * @property string $updated_at
 * @property-read \App\Models\AttributionRecordTruth $attributionTruths
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailAction[] $emailAction
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailCampaignStatistic[] $emailCampaignStatistic
 * @property-read \App\Models\EmailDomain $emailDomain
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailFeedInstance[] $emailFeedInstances
 * @property-read \App\Models\EmailFeedAssignment $feedAssignment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Suppression[] $suppressions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereEmailDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereLowerCaseMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Email whereUpperCaseMd5($value)
 * @mixin \Eloquent
 */
class Email extends Model {

    protected $guarded = ['id'];
    public $timestamps = false;

    public function emailFeedInstances() {
        return $this->hasMany('App\Models\EmailFeedInstance');
    }

    public function emailDomain() {
        return $this->belongsTo('App\Models\EmailDomain');
    }

    public function emailAction() {
        return $this->hasMany('App\Models\EmailAction');
    }

    public function emailCampaignStatistic() {
        return $this->hasMany('App\Models\EmailCampaignStatistic');
    }

    public function suppressions() {
        return $this->hasMany('App\Models\Suppression');
    }

    public function feedAssignment() {
        return $this->hasOne('App\Models\EmailFeedAssignment');
    }

    public function attributionTruths() {
        return $this->hasOne('App\Models\AttributionRecordTruth');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Suppression
 *
 * @property int $id
 * @property string $email_address
 * @property int $type_id
 * @property int $esp_account_id
 * @property int $esp_internal_id
 * @property string $date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $reason_id
 * @property-read \App\Models\Email $email
 * @property-read \App\Models\EspAccount $espAccount
 * @property-read \App\Models\StandardReport $standardReport
 * @property-read \App\Models\SuppressionReason $suppressionReason
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereReasonId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Suppression whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Suppression extends Model
{
    CONST TYPE_UNSUB = 1;
    CONST TYPE_HARD_BOUNCE = 2;
    CONST TYPE_COMPLAINT = 3;
    protected $guarded = ['id'];

    public function espAccount() {
        return $this->belongsto('App\Models\EspAccount');
    }
    public function suppressionReason(){
        return $this->hasOne('App\Models\SuppressionReason', 'id', 'reason_id');
    }
    public function standardReport(){
        return $this->belongsTo('App\Models\StandardReport', 'esp_internal_id', 'esp_internal_id');
    }
    public function email(){
        return $this->belongsTo('App\Models\Email');
    }


}

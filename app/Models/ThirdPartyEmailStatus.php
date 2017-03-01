<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ThirdPartyEmailStatus
 *
 * @property int $email_id
 * @property string $last_action_type
 * @property int $last_action_offer_id
 * @property string $last_action_datetime
 * @property int $last_action_esp_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereLastActionDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereLastActionEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereLastActionOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereLastActionType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ThirdPartyEmailStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ThirdPartyEmailStatus extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'email_id';

    const DELIVERABLE = "None";
    const OPENER = "Opener";
    const CLICKER = "Clicker";
    const CONVERTER = "Converter";
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AWeberEmailActionsStorage
 *
 * @property int $id
 * @property int $email_id
 * @property int $esp_account_id
 * @property int $esp_internal_id
 * @property int $deploy_id
 * @property bool $action_id
 * @property string $datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberEmailActionsStorage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AWeberEmailActionsStorage extends Model
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
}

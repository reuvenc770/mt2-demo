<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrphanEmail
 *
 * @property int $id
 * @property string $email_address
 * @property bool $missing_email_record
 * @property bool $missing_email_client_instance
 * @property int $esp_account_id
 * @property int $deploy_id
 * @property int $esp_internal_id
 * @property bool $action_id
 * @property string $datetime
 * @property int $adopt_attempts
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereAdoptAttempts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereMissingEmailClientInstance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereMissingEmailRecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrphanEmail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrphanEmail extends Model
{
}

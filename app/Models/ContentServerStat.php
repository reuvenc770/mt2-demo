<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContentServerStat
 *
 * @property int $id
 * @property int $email_id
 * @property int $deploy_id
 * @property int $action_id
 * @property string $datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContentServerStat extends Model
{
    //
}

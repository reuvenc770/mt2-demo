<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailIdHistory
 *
 * @property int $email_id
 * @property mixed $old_email_id_list
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailIdHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailIdHistory whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailIdHistory whereOldEmailIdList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailIdHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailIdHistory extends Model
{
    protected $primaryKey = 'email_id';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AWeberSubscriber
 *
 * @property int $id
 * @property string $email_address
 * @property int $internal_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberSubscriber whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberSubscriber whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberSubscriber whereInternalId($value)
 * @mixin \Eloquent
 */
class AWeberSubscriber extends Model
{
    protected $guarded = ['id'];
}

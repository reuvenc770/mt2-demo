<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FtpUser
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $host
 * @property string $directory
 * @property string $service
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereDirectory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereHost($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereService($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FtpUser whereUsername($value)
 * @mixin \Eloquent
 */
class FtpUser extends Model
{
    protected $guarded = ['id'];
}

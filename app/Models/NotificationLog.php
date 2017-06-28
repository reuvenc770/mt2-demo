<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $guarded = [ 'id' ];
    public $timestamps = false;
}

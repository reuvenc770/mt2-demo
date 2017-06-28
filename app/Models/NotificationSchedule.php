<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;

class NotificationSchedule extends Model
{
    use ModelCacheControl;

    protected $guarded = [ 'id' ];
}

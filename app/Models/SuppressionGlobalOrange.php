<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionGlobalOrange extends Model
{
    protected $guarded = [ '' ];
    protected $connection = 'suppression';
    protected $table = 'suppression_global_orange';
}

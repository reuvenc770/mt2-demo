<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceUrlCount extends Model
{
    protected $connection = "reporting_data";
    protected $guarded = [ '' ];

    public $timestamps = false;
}

<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeploySnapshot extends Model
{
    protected $connection = "reporting_data";
    protected $guarded = [];
}

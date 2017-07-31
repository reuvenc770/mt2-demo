<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpmReportingListProfile extends Model
{
    protected $connection = 'reporting_data';
    protected $table = 'cpm_reporting_listprofile';
    protected $guarded = [];
}

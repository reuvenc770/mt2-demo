<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpaReportingListProfile extends Model
{
    protected $connection = 'reporting_data';
    protected $table = 'cpa_reporting_list_profile';
    protected $guarded = [];
}

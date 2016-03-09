<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/8/16
 * Time: 3:57 PM
 */

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class GetResponseReport extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
}
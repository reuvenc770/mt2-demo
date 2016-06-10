<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:24 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IReport;

class BrontoReport extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

}

<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:24 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IReportMapper;

class BrontoReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "start";
    }

    public function getSubjectFieldName(){
        return "message_name";
    }
}

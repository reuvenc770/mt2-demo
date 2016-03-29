<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class StandardReport extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }
}

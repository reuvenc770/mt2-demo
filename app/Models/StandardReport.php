<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

class StandardReport extends Model implements IReport
{
    protected $guarded = ['id'];

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }
}

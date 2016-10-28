<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class VendorSuppList extends Model {
    protected $connection = 'mt1supp';
    protected $tableName = 'vendor_supp_list';
}
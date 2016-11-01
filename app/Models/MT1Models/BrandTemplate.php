<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class BrandTemplate extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'brand_template';
    protected $primaryKey = 'template_id';

}
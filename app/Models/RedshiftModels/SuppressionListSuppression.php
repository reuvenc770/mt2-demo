<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class SuppressionListSuppression extends Model {
    protected $connection = 'redshift';
    protected $guarded = [];
}
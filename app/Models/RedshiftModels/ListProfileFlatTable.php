<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class ListProfileFlatTable extends Model
{
    protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'list_profile_flat_table';
}
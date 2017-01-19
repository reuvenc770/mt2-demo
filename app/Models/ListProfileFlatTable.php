<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileFlatTable extends Model {
    protected $guarded = [];
    public $timstamps = false;
    protected $connection = 'list_profile';
    protected $table = 'list_profile_flat_table';
}

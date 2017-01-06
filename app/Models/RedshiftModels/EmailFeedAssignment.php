<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class EmailFeedAssignment extends Model
{
    protected $connection = 'redshift';
    protected $primaryKey = 'email_id';
    public $timestamps = false;
    protected $guarded = [];
}
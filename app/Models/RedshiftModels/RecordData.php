<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class RecordData extends Model
{
    protected $connection = 'redshift';
    protected $primaryKey = 'email_id';
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'record_data';
}
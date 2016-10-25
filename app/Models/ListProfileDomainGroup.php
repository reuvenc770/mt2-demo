<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileDomainGroup extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function domainGroup() {
        return $this->belongsTo('App\Models\EmailDomainGroup');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
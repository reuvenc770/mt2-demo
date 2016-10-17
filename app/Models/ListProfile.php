<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfile extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function clients() {
        return $this->belongsToMany('App\Models\Client', 'list_profile.list_profile_clients');
    }

    public function feeds() {
        return $this->belongsToMany('App\Models\Feed', 'list_profile.list_profile_feeds');
    }

    public function domainGroups() {
        return $this->belongsToMany('App\Models\DomainGroup', 'list_profile.list_profile_domain_groups');
    }

    public function offers() {
        return $this->belongsToMany('App\Models\Offer', 'list_profile.list_profile_offers');
    }

    public function verticals() {
        return $this->belongsToMany('App\Models\CakeVertical', 'list_profile.list_profile_verticals');
    }
}

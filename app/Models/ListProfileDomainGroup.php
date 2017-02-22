<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileDomainGroup
 *
 * @property int $list_profile_id
 * @property int $domain_group_id
 * @property-read \App\Models\DomainGroup $domainGroup
 * @property-read \App\Models\ListProfile $listProfile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileDomainGroup whereDomainGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileDomainGroup whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileDomainGroup extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function domainGroup() {
        return $this->belongsTo('App\Models\DomainGroup');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
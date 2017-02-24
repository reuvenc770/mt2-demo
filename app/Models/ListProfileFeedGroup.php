<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileFeedGroup
 *
 * @property int $list_profile_id
 * @property int $feed_group_id
 * @property-read \App\Models\FeedGroup $feedGroup
 * @property-read \App\Models\ListProfile $listProfile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFeedGroup whereFeedGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFeedGroup whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileFeedGroup extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function feedGroup() {
        return $this->belongsTo('App\Models\FeedGroup');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
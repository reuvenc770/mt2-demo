<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileFeed
 *
 * @property int $list_profile_id
 * @property int $feed_id
 * @property-read \App\Models\Feed $feed
 * @property-read \App\Models\ListProfile $listProfile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFeed whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFeed whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileFeed extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
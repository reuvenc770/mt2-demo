<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AWeberList
 *
 * @property int $id
 * @property int $internal_id
 * @property string $name
 * @property int $esp_account_id
 * @property int $total_subscribers
 * @property string $subscribers_collection_link
 * @property string $campaigns_collection_link
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereCampaignsCollectionLink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereSubscribersCollectionLink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereTotalSubscribers($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AWeberList extends Model
{
    protected $guarded = ['id'];
}

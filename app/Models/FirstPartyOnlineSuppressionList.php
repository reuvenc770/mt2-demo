<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FirstPartyOnlineSuppressionList
 *
 * @property int $feed_id
 * @property int $suppression_list_id
 * @property int $esp_account_id
 * @property string $target_list
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyOnlineSuppressionList whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyOnlineSuppressionList whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyOnlineSuppressionList whereSuppressionListId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyOnlineSuppressionList whereTargetList($value)
 * @mixin \Eloquent
 */
class FirstPartyOnlineSuppressionList extends Model
{
    protected $connection = 'suppression';
    public $timestamps = false;
}

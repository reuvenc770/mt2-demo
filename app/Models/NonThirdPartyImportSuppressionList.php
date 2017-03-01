<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NonThirdPartyImportSuppressionList
 *
 * @property int $feed_id
 * @property int $suppression_list_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NonThirdPartyImportSuppressionList whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NonThirdPartyImportSuppressionList whereSuppressionListId($value)
 * @mixin \Eloquent
 */
class NonThirdPartyImportSuppressionList extends Model {

    protected $guarded = [];
    protected $connection = 'suppression';
}

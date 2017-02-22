<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspDataExport
 *
 * @property int $feed_id
 * @property int $esp_account_id
 * @property string $target_list
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspDataExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspDataExport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspDataExport whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspDataExport whereTargetList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspDataExport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspDataExport extends Model
{
    protected $primaryKey = 'feed_id';
}

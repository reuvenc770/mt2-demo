<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProcessedFeedFile
 *
 * @property int $path
 * @property int $feed_id
 * @property int $line_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProcessedFeedFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProcessedFeedFile whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProcessedFeedFile whereLineCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProcessedFeedFile wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProcessedFeedFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcessedFeedFile extends Model
{
    protected $guarded = [ '' ];
    protected $primaryKey = 'path';
}

<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;

/**
 * App\Models\AttributionModel
 *
 * @property int $id
 * @property string $name
 * @property bool $live
 * @property bool $processing
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereLive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereProcessing($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionModel extends Model
{
    use ModelCacheControl;

    protected $connection = 'attribution';
}

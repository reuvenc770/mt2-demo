<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionAssignedRecord
 *
 * @mixin \Eloquent
 */
class AttributionAssignedRecord extends Model
{
    protected $connection = 'attribution';
}

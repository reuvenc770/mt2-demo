<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionListOwnerReport extends Model
{
    protected $guarded = ['id'];

    protected $connection = 'attribution';
}

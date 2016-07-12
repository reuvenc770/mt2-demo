<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;

class AttributionModel extends Model
{
    use ModelCacheControl;

    protected $connection = 'attribution';
}

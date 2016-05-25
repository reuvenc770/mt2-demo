<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class CategoryInfo extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'category_info';
}

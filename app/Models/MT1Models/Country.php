<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'Country';
}

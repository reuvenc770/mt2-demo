<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class SuppressListOrange extends Model
{
    protected $connection = 'mt1supp';
    protected $table = 'suppress_list_orange';
    protected $primaryKey = 'sid';
}

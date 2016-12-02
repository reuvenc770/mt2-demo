<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class MD5AdvertiserSuppressList extends Model {
    protected $connection = "mt1supp";
    protected $table = "MD5AdvertiserSuppressList";
}

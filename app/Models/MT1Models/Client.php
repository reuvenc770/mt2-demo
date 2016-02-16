<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:27 PM
 */

namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'user';
}
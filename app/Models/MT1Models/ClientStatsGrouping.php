<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:52 PM
 */

namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;

class ClientStatsGrouping extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'ClientStatsGrouping';
}
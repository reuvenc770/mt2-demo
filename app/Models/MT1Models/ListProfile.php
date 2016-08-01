<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/1/16
 * Time: 12:16 PM
 */

namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;
class ListProfile extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'list_profile';
    protected $primaryKey = 'profile_id';

    public function Deploys(){
        return $this->belongsToMany('App\Models\Deploys');
    }
}
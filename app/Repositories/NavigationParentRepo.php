<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/24/16
 * Time: 3:25 PM
 */

namespace App\Repositories;


use App\Models\NavigationParent;

class NavigationParentRepo
{
    private $navigationModel;

    public function __construct(NavigationParent $navigationParent)
    {
        $this->navigationModel = $navigationParent;
    }

    public function getAllSections(){
        return $this->navigationModel->orderBy('rank')->get();
    }


    public function updateRank($id, $rank){
        return $this->navigationModel->find($id)->update(['rank'=>$rank]);
    }

}
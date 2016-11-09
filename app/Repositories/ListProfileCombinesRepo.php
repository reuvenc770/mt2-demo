<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/7/16
 * Time: 3:27 PM
 */

namespace App\Repositories;


use App\Models\ListProfileCombine;

class ListProfileCombinesRepo
{
    private $model;

    public function __construct(ListProfileCombine $combine)
    {
        $this->model = $combine;
    }

    public function createRow($data){
        $this->model->create($data);
    }

    public function getAll(){
        return $this->model->with(['listProfiles' => function ($query) {
            $query->select('name');
        }])->get();
    }
    public function getAllNoneProfiles(){
        return $this->model->with(['listProfiles' => function ($query) {
            $query->select('name');
        }])->whereNull("list_profile_id")->get();
    }

    public function insertRow($row){
       return $this->model->create($row);
    }

    public function attachPivot($listProfileCombine, $id){
        return $listProfileCombine->listProfiles()->attach($id);
    }

    //TODO: Refactor Toggle list trait, or do we add this into pagination scope
    public function toggleRow($id, $direction){
        return $this->model->find($id)->update(['status'=> $direction]);
    }
    //TODO:: getModel is another method that only is used by pagination scope.. might be better off in a trait and overridden when it gets complex
    public function getModel(){
      return $this->model;
    }


    public function getRowWithListProfiles($id){
        return $this->model->with("listProfiles")->find($id);
    }

}
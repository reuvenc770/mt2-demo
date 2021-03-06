<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/7/16
 * Time: 3:27 PM
 */

namespace App\Repositories;


use App\Models\ListProfileCombine;

class ListProfileCombineRepo
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
    public function getListCombinesOnly(){
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

    public function getCombineHeader($listProfileCombineId){
        return $this->model
            ->select('columns')
            ->join('list_profile_list_profile_combine as lplpc',"lplpc.list_profile_combine_id", "=", "list_profile_combines.id" )
            ->join('list_profiles as lp',"lplpc.list_profile_id", "=", "lp.id")
            ->where('list_profile_combines.id',$listProfileCombineId)->get();
    }

    public function getIdFromName($name){
        $combine = $this->model->where('name',$name)->first();
        return $combine->id;
    }

    public function isEditable($id) {
        return $this->model->where('id' , $id)->whereNull("list_profile_id")->count() > 0;
    }

    public function updateCombine( $record) {
        $currentCombine = $this->model->find( $record['id']);

        $currentCombine->name =  $record['combineName'];
        $currentCombine->ftp_folder =  $record['ftpFolder'];
        $currentCombine->save();

        $currentCombine->listProfiles()->detach();

        foreach( $record['selectedProfiles'] as $profile ){
            $this->attachPivot( $currentCombine , $profile['id'] );
        }

    }

    public function getFirstPartyListCombines(){
        return $this->model->with(['listProfiles' =>function( $query ) {
            $query->select('name');
        }])->where('party', 1 )->get();
    }
}
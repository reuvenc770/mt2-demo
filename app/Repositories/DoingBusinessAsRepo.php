<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:54 PM
 */

namespace App\Repositories;


use App\Models\DoingBusinessAs;

class DoingBusinessAsRepo
{
    protected $doingBusinessAs;

    public function __construct(DoingBusinessAs $businessAs)
    {
        $this->doingBusinessAs = $businessAs;

    }
    public function insertRow($data){
      return $this->doingBusinessAs->create($data);
    }

    public function getAll(){
        return $this->doingBusinessAs->all();
    }
    public function getAllActive(){
        return $this->doingBusinessAs->where('status',1)->get();
    }

    public function fetch($id){
        return $this->doingBusinessAs->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->doingBusinessAs->find( $id )->update( [
            'dba_name' => $accountData[ 'dba_name' ] ,
            'state_id' => $accountData[ 'state_id' ] ,
        ] );
    }

    public function toggleRow($id, $direction){
        return $this->doingBusinessAs->find($id)->update(["status" => $direction]);
    }

    public function getModel(){
        return $this->doingBusinessAs->activeFirst();
    }
}
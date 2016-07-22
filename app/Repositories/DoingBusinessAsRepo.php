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
      return $this->doingBusinessAs->insert($data);
    }

    public function getAll(){
        return $this->doingBusinessAs->all();
    }

    public function fetch($id){
        return $this->doingBusinessAs->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->doingBusinessAs->where( 'id' , $id )->update( [
            'dba_name' => $accountData[ 'dba_name' ] ,
            'state_id' => $accountData[ 'state_id' ] ,
        ] );
    }
}
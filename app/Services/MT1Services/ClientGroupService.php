<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 1:04 PM
 */

namespace App\Services\MT1Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\MT1Repositories\ClientGroupRepo;

class ClientGroupService
{
    use PaginateList;
    protected $clientGroupRepo;

    public function __construct(ClientGroupRepo $clientGroupRepo)
    {
        $this->clientGroupRepo = $clientGroupRepo;
    }

    public function getModel () {
        return $this->clientGroupRepo->getModel(); 
    }

    public function getName ( $groupId ) {
        return $this->clientGroupRepo->getName( $groupId );
    }

    public function getAll () {
        return $this->clientGroupRepo->getAllClientGroups();
    }

    public function getAllNames(){
        return $this->clientGroupRepo->getAllClientGroups();
    }

    public function getClientGroup ( $groupId ) {
        return $this->clientGroupRepo->getClientGroup( $groupId );
    }

    public function getClientsForClientGroup($id){
        return $this->clientGroupRepo->getAllClientsForGroup($id);
    }

    public function search ( $query ) {
        return $this->clientGroupRepo->search( $query );
    }
}

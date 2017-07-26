<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\CpmDeploySnapshotRepo;

class CpmDeploySnapshotService {
    protected $repo;

    public function __construct ( CpmDeploySnapshotRepo $repo ) {
        $this->repo = $repo;
    }

    public function massInsert ( $recordList ) {
        return $this->repo->massInsert( $recordList );
    }

    public function toSqlFormat ( $record ) {
        return $this->repo->toSqlFormat( $record );
    }

    public function clearForDeploy ( $deployId ) {
        return $this->repo->clearForDeploy( $deployId );
    }

    public function getListProfileExportsFromDeploy ( $deployId ) {
        return $this->repo->getListProfileExportsFromDeploy( $deployId );
    }
}

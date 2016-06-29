<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionLevel;

class AttributionLevelRepo {
    protected $levels;

    public function __construct ( AttributionLevel $levels ) {
        $this->levels = $levels;
    }

    public function setLevel ( $clientId , $level ) {
        #insert or update given client and level.
    }

    public function getLevel ( $clientId ) {
        #returns level for the given client.
    }

    public function getAllLevels () {
        #returns all levels
    }

    public function toggleActiveStatus ( $clientId , $isActive ) {
        #sets active filed for the given client.
    }
}

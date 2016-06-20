<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\PageRepo;

class PageService {
    protected $pageRepo;

    public function __construct ( PageRepo $pageRepo ) {
        $this->pageRepo = $pageRepo;
    }

    public function getAllPages () {
        return $this->pageRepo->getAllPages();
    }

    public function getAllPageNames () {
        return $this->pageRepo->getAllPageNames();
    }
    
    public function getPageId ( $name ) {
        return $this->pageRepo->getPageId( $name );
    }
}

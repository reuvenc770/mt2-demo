<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Page;

class PageRepo {
    protected $page;

    public function __construct ( Page $page ) {
        $this->page = $page;
    }

    public function getAllPages () {
        return $this->page->get();
    }

    public function getAllPageNames () {
        return $this->page->select( 'name' )->get();
    }

    public function getPageId ( $name ) {
        return $this->page->where( 'name' , $name )->pluck( 'id' )->pop();
    }
}

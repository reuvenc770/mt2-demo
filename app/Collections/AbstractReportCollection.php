<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections;

use Illuminate\Support\Collection;

abstract class AbstractReportCollection extends Collection {
    protected $model;
    protected $query;

    public function config ( $query = [] ) {
        $this->query = $query;
    }

    public function recordCount () {
        return $this->processQuery()->count();
    }

    public function load ( $query = [] ) {
        parent::__construct( $this->getRecords() );
    }

    protected function processQuery () {
        $order = 'asc';
        if ( $this->query[ 'sort' ][ 'desc' ] ) {
            $order = 'desc';
        }

        return $this->model->whereBetween( 'date' , [ $this->query[ 'date' ][ 'start' ] , $this->query[ 'date' ][ 'end' ] ] )
                    ->orderBy( $this->query[ 'sort' ][ 'field' ] , $order );
    }

    protected function getRecords () {
        return $this->processQuery()->get()->toArray();
    }
}

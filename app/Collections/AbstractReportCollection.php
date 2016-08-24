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

    public function getRecordsAndTotals ( $options = [] ) {
        $records = $this->forPage( $options[ 'page' ] , $options[ 'chunkSize' ] );

        return [
            'records' => &$records ,
            'totals' => $this->sumTotals( $records , $this->totalFields )
        ];
    }

    abstract protected function getDateField ();

    protected function processQuery () {
        $order = 'asc';
        if ( $this->query[ 'sort' ][ 'desc' ] ) {
            $order = 'desc';
        }

        return $this->model->whereBetween( $this->getDateField() , [ $this->query[ 'date' ][ 'start' ] , $this->query[ 'date' ][ 'end' ] ] )
                    ->orderBy( $this->query[ 'sort' ][ 'field' ] , $order );
    }

    protected function getRecords () {
        return $this->processQuery()->get()->toArray();
    }

    protected function sumTotals ( &$records , $fields = [] ) {
        $totals = [];

        foreach ( $fields as $field ) {
            $totals[ $field ] = $records->pluck( $field )->sum();
        }

        return $totals;
    }
}

<?php

namespace App\Repositories\RepoInterfaces;

interface ICanonicalDataSource {
    public function compareSourcesWithField($tableName, $startPoint, $segmentEnd, $field);
    public function compareSources($tableName, $startPoint, $segmentEnd);
    public function maxId();
    public function nextNRows($startPoint, $count);
    public function lessThan($startPoint, $endPoint);
}
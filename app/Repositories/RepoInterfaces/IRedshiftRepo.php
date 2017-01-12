<?php

namespace App\Repositories\RepoInterfaces;

interface IRedshiftRepo {
    public function loadEntity($entity);
}
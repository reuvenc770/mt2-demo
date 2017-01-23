<?php

namespace App\Repositories\RepoInterfaces;

interface IRedshiftRepo {
    public function loadEntity($entity);
    public function clearAndReloadEntity($entity);
}
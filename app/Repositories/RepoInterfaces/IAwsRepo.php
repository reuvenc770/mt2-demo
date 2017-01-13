<?php

namespace App\Repositories\RepoInterfaces;

interface IAwsRepo {
    public function extractForS3Upload($stopPoint);
    public function mapForS3Upload($row);
}
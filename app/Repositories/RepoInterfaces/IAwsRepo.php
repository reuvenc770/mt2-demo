<?php

namespace App\Repositories\RepoInterfaces;
use App\Repositories\EtlPickupRepo;

interface IAwsRepo {
    public function extractForS3Upload(EtlPickupRepo $pickupRepo);
    public function mapForS3Upload($row);
}
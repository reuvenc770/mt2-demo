<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 12:33 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\UniqueProfileRepo;
use App\Services\ServiceTraits\PaginateList;

class UniqueProfileService
{
    use PaginateList;
    protected $profileRepo;

    public function __construct(UniqueProfileRepo $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function getAllProfiles () {
        return $this->profileRepo->getProfilesNameAndId();
    }

    public function getById ( $id ) {
        return $this->profileRepo->getProfileById( $id );
    }

    public function getIspsByProfileId ( $profileId ) {
        return $this->profileRepo->getIspsByProfileId( $profileId );
    }

    public function getSourcesByProfileId ( $profileId ) {
        return $this->profileRepo->getSourcesByProfileId( $profileId );
    }

    public function getSeedsByProfileId ( $profileId ) {
        return $this->profileRepo->getSeedsByProfileId( $profileId );
    }

    public function getZipsByProfileId ( $profileId ) {
        return $this->profileRepo->getZipsByProfileId( $profileId );
    }

    public function getModel () { return $this->profileRepo->getModel(); }
}

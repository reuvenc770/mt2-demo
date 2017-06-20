<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\NotificationScheduleRepo;
use App\Services\ServiceTraits\PaginateList;
use Storage;

class NotificationScheduleService {
    use PaginateList;

    protected $repo;

    public function __construct ( NotificationScheduleRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function getAllActiveNotifications ( $contentType ) {
        return $this->repo->getAllActiveNotifications( $contentType );
    }

    public function log ( $contentType , $content ) {
        return $this->repo->log( $contentType , $content );
    }

    public function hasLogs ( $contentType , $lookback ) {
        return $this->repo->hasLogs( $contentType , $lookback );
    }

    public function getLogs ( $contentType , $lookback ) {
        return $this->repo->getLogs( $contentType , $lookback );
    }

    public function getUnscheduledLogs () {
        return $this->repo->getUnscheduledLogs();
    }

    public function getContentKeys () {
        return $this->repo->getDistinctContentKeys();
    }

    public function getEmailTemplates () {
        $files = Storage::disk( 'views' )->files( 'emails' );

        $names = [];

        foreach ( $files as $fileName ) {
            $matches = [];
            preg_match( '/\w+\/(\w+).blade.php/' , $fileName , $matches );

            $names []= 'emails.' . $matches[1]; 
        }

        return $names;
    }

    public function getSlackTemplates () {
        $files = Storage::disk( 'views' )->files( 'slack' );

        $names = [];

        foreach ( $files as $fileName ) {
            $matches = [];
            preg_match( '/\w+\/(\w+).blade.php/' , $fileName , $matches );

            $names []= 'slack.' . $matches[1]; 
        }

        return $names;
    }

    public function updateOrCreate ( $fields ) {
        return $this->repo->updateOrCreate( $fields );
    }

    public function toggleStatus ( $id , $currentStatus ) {
        return $this->repo->toggleStatus( $id , $currentStatus );
    }
}

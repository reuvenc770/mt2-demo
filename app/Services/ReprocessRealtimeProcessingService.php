<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\Mt1RealtimeProcessingService;
use Maknz\Slack\Facades\Slack;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Models\ProcessedFeedFile;
use App\Repositories\RawFeedEmailRepo;
use Illuminate\Support\Facades\Redis;

class ReprocessRealtimeProcessingService extends Mt1RealtimeProcessingService {
    protected $serviceName = 'ReprocessRealtimeProcessingService';
    protected $slackChannel = '#cmp_hard_start_errors';

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        parent::__construct( $feedService , $systemService , $domainGroupService , $rawRepo );
    }

    public function setCreds ( $hostConfig , $portConfig , $userConfig , $publicKeyConfig , $privateKeyConfig ) {
        $this->hostConfig = $hostConfig;
        $this->portConfig = $portConfig;
        $this->userConfig = $userConfig;
        $this->publicKeyConfig = $publicKeyConfig;
        $this->privateKeyConfig = $privateKeyConfig;
    }

    public function setFile ( $filePath , $feedId , $party ) {
        $this->newFileList[] = [
            'path' => trim( $filePath ) ,
            'feedId' => $feedId ,
            'party' => $party 
        ];

        Redis::connection( 'cache' )->executeRaw( [ 'SETNX' , self::REDIS_LOCK_KEY_PREFIX . trim( $filePath ) , getmypid() ] );
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    public function loadNewFilePaths () {
        $this->connectToServer();
    }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) { 
            $this->systemService->initSshConnection(
                config( $this->hostConfig ),
                config( $this->portConfig ),
                config( $this->userConfig ),
                config( $this->publicKeyConfig ),
                config( $this->privateKeyConfig )
            );  
        }   
    }
}

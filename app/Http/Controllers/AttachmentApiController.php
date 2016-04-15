<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Flow\Request as FlowRequest;
use \Flow\Config;
use \Flow\File;
use Storage;
use Carbon\Carbon;
use Event;

class AttachmentApiController extends Controller
{
    const UPLOAD_BASE_DIR = '/files/uploads/';
    const CHUNKS_DIR = '\chunks';
    const PAGE_TYPE_REQUEST_KEY = 'fromPage';

    protected $flowRequest;
    protected $destinationFolder;
    protected $destination;

    protected $responseCode = 200;
    protected $response = [ 'uploadstatus' => 0 ];

    public function flow () {
        $this->setupFlow();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' && !$this->file->checkChunk() ) {
            $this->responseCode = 204; #no content
        } elseif ( $_SERVER['REQUEST_METHOD'] !== 'GET' && $this->file->validateChunk() ) {
            $this->file->saveChunk();
        } else {
            $this->responseCode = 400; #invalid chunk, retry
        }

        if ( $this->fileDownloadComplete() ) {
            $this->response[ 'uploadstatus' ] = 1;

            $eventPath = "App\\Events\\" . studly_case( $_REQUEST[ self::PAGE_TYPE_REQUEST_KEY ] . '_file_uploaded' );

            if ( class_exists( $eventPath ) ) {
                Event::fire( \App::make( $eventPath , [ $this->destination ] ) );
            }
        }

        return response()->json( $this->response , $this->responseCode );
    }

    protected function setupFlow () {
        $this->flowRequest = new FlowRequest();

        $this->setDestination();

        $this->prepareDirectory();

        $this->flowConfig = new \Flow\Config( [
            'tempDir' => storage_path() . '/app/files/uploads/chunks'
        ] );

        $this->file = new \Flow\File( $this->flowConfig , $this->flowRequest );
    }

    protected function setDestination () {
        $this->destinationFolder = self::UPLOAD_BASE_DIR . $_REQUEST[ self::PAGE_TYPE_REQUEST_KEY ] . '/' . Carbon::now( config( 'app.timezone' ) )->format( 'Ymd' ); 

        $this->destination = $this->destinationFolder . '/' . $_REQUEST[ 'flowFilename' ];
    }

    protected function prepareDirectory () {
        if ( !Storage::exists( self::UPLOAD_BASE_DIR . self::CHUNKS_DIR ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR . self::CHUNKS_DIR );
        }

        if ( !Storage::exists( self::UPLOAD_BASE_DIR ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR );
        }

        if ( !Storage::exists( self::UPLOAD_BASE_DIR . $_REQUEST[ self::PAGE_TYPE_REQUEST_KEY ] ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR . $_REQUEST[ self::PAGE_TYPE_REQUEST_KEY ] );
        }

        if ( !Storage::exists( $this->destinationFolder ) ) {
            Storage::makeDirectory( $this->destinationFolder );
        }
    }

    protected function fileDownloadComplete () {
        return (
            $this->responseCode === 200
            && $this->file->validateFile()
            && $this->file->save( storage_path() . '/app' . $this->destination )
        );
    }
}

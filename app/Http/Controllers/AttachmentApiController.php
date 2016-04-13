<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Flow\Request as FlowRequest;
use \Flow\Config;
use \Flow\File;
use Storage;
use Carbon\Carbon;

class AttachmentApiController extends Controller
{
    const UPLOAD_BASE_DIR = '/files/uploads/';

    public function flow () {
        $request = new FlowRequest();

        $destinationFolder = self::UPLOAD_BASE_DIR . 'bulksuppression/' . Carbon::now( 'America/New_York' )->format( 'Ymd' ); 
        $destination = $destinationFolder . '/' . $_REQUEST[ 'flowFilename' ];

        if ( !Storage::exists( self::UPLOAD_BASE_DIR . '/chunks' ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR . '/chunks' );
        }

        if ( !Storage::exists( self::UPLOAD_BASE_DIR ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR );
        }

        if ( !Storage::exists( self::UPLOAD_BASE_DIR . 'bulksuppression/' ) ) {
            Storage::makeDirectory( self::UPLOAD_BASE_DIR . 'bulksuppression/' );
        }

        if ( !Storage::exists( $destinationFolder ) ) {
            Storage::makeDirectory( $destinationFolder );
        }

        $config = new \Flow\Config( [
            'tempDir' => storage_path() . '/app/files/uploads/chunks'
        ] );

        $file = new \Flow\File( $config , $request );

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
            if ( !$file->checkChunk() ) {
                return response( '' , 204 );
            }
        } else {
            if ( $file->validateChunk() ) {
                $file->saveChunk();
            } else {
                // error, invalid chunk upload request, retry
                return response( '' , 400 );
            }
        }

        if ( $file->validateFile() && $file->save( storage_path() . '/app' . $destination ) ) {
            return response()->json( [ 'uploadstatus' => 1 ] , 200 );
        }

        return response( '' , 200 );
    }
}

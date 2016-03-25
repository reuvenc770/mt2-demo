<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Flow\Request as FlowRequest;
use \Flow\Config;
use \Flow\File;
use Carbon\Carbon;

class AttachmentApiController extends Controller
{
    public function flow () {
        $request = new FlowRequest();
        $baseDestination = storage_path() . '/files/uploads/';

        if ( !is_dir( $baseDestination . $_REQUEST[ 'filetype' ] ) ) {
            mkdir( $baseDestination . $_REQUEST[ 'filetype' ] );
        }

        $destination = $baseDestination . $_REQUEST[ 'filetype' ] . '/' . Carbon::now( 'America/New_York' )->format( 'Y-m-d-H-i-s' );
        $config = new \Flow\Config( [
            'tempDir' => storage_path() . '/files/chunks'
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
        if ( $file->validateFile() && $file->save( $destination ) ) {
            return response()->json( [ 'uploadstatus' => 1 ] , 200 );
        }

        return response( '' , 200 );
    }
}

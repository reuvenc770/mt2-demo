<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Laracasts\Flash\Flash;
use App\Http\Controllers\Controller;
use App\Services\Mt1ApiService;

class BulkSuppressionController extends Controller
{

    protected $api;
    const BULK_SUPPRESSION_API_ENDPOINT = 'bulk_suppressions';


    public function __construct(MT1ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view( 'pages.bulk-suppression' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Transfer locally-stored suppression uploads to MT1Bin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $failed = [];
        $dateFolder = date('Ymd');
        $path = storage_path() . "/app/files/uploads/bulksuppression/$dateFolder/";
        $files = scandir($path);

        $user = env('MT1_FILE_UPLOAD_USER', '');
        $host = env('MT1_FILE_UPLOAD_HOST', '');
        $pass = env('MT1_FILE_UPLOAD_PASS', '');
        $port = env('MT1_FILE_UPLOAD_PORT', '');
        $remoteDir = env('MT1_FILE_UPLOAD_DIRECTORY', '');
        $conn = ssh2_connect($host, $port);
        ssh2_auth_password($conn, $user, $pass);

        foreach ($files as $file) {
            if (!preg_match('/^\./', $file)) {
                $filename = $path . $file;
                $fs[]= $filename;
                $result = \ssh2_scp_send($conn, $filename, $remoteDir . $file); // returns a bool
                if (!$result) {
                    $failed[]= $file;
                }
            }

        }

        return $failed;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return response( $this->api->getJSON( self::BULK_SUPPRESSION_API_ENDPOINT,
         $request->all() ) );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

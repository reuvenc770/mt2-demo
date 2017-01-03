<?php

namespace App\Http\Controllers;

use App\Events\BulkSuppressionFileWasUploaded;
use App\Services\EmailRecordService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\BulkSuppressionRequest;
use Laracasts\Flash\Flash;
use App\Facades\Suppression;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class BulkSuppressionController extends Controller
{

    protected $api;
    protected $emailService;
    const BULK_SUPPRESSION_API_ENDPOINT = 'bulk_suppressions';


    public function __construct(MT1ApiService $api, EmailRecordService $recordService)
    {
        $this->api = $api;
        $this->emailService = $recordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('bootstrap.pages.bulk-suppression');
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $failed = [];
        $dateFolder = date('Ymd');
        $path = storage_path() . "/app/files/uploads/bulksuppression/$dateFolder/";
        $files = scandir($path);

        $user = config('ssh.servers.mt1_file_upload.username');
        $host = config('ssh.servers.mt1_file_upload.host');
        $pass = config('ssh.servers.mt1_file_upload.password');
        $port = config('ssh.servers.mt1_file_upload.port');
        $remoteDir = config('ssh.servers.mt1_file_upload.remote_dir');

        $conn = ssh2_connect($host, $port);
        \ssh2_auth_password($conn, $user, $pass);

        foreach ($files as $file) {
            if (!preg_match('/^\./', $file)) {
                $filename = $path . $file;
                $fs[] = $filename;
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(BulkSuppressionRequest $request)
    {
        $type = 'eid';
        $records = $request->input('emails');
        $reason = $request->input('suppressionReasonCode');
        if (preg_match("/@+/", $records)) $type = 'email';
        if(!empty($records)) {
            if ($type == "email") {
                foreach (explode(',', $records) as $record) {
                    Suppression::recordSuppressionByReason($record, Carbon::today()->toDateTimeString(), $reason);
                }
            } else {
                foreach (explode(',', $records) as $record) {
                    $email = $this->emailService->getEmailAddress($record);
                    Suppression::recordSuppressionByReason($email, Carbon::today()->toDateTimeString(), $reason);
                }
            }
        }
        if (!empty($reason)) {
            Event::fire(new BulkSuppressionFileWasUploaded($reason, $request->input('suppfile'), date('Ymd')));
        }
        return response($this->api->getJSON(self::BULK_SUPPRESSION_API_ENDPOINT,
            $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

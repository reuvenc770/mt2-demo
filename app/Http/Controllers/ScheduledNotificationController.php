<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\NotificationScheduleService;

class ScheduledNotificationController extends Controller
{
    protected $service;

    public function __construct ( NotificationScheduleService $service ) {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view( 'pages.notification_schedule' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode( $request->all()[ 'data' ] , true );

        return response()->json( $this->service->updateOrCreate( $data ) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        unset( $data[ 'status' ] );
        unset( $data[ 'created_at' ] );
        unset( $data[ 'updated_at' ] );

        return response()->json( $this->service->updateOrCreate( $data ) );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request , $id )
    {
        return response()->json( $this->service->toggleStatus( $id , $request->input( 'currentStatus' ) ) );
    }

    public function getUnscheduledLogs () {
        return response()->json( $this->service->getUnscheduledLogs() );
    }

    public function getEmailTemplates () {
        return response()->json( $this->service->getEmailTemplates() );
    }

    public function getSlackTemplates () {
        return response()->json( $this->service->getSlackTemplates() );
    }

    public function getContentKeys () {
        return response()->json( $this->service->getContentKeys() );
    }
}

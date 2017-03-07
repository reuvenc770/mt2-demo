<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\AttributionModelService;
use App\Services\AttributionFeedReportService;
use App\Http\Requests\AttributionModelRequest;

use Artisan;
use Cache;
use Sentinel;

class AttributionController extends Controller
{
    protected $service;
    protected $reportService;

    protected $reportCollection;

    public function __construct ( AttributionModelService $service , AttributionFeedReportService $reportService ) {
        $this->service = $service;
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function listAll () {
        return response()->view( "pages.attribution.attribution-index" );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( "pages.attribution.attribution-add" );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributionModelRequest $request)
    {
        return response()->json( [ $this->service->create( $request->input( 'name' ) , $request->input( 'levels' ) ) ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json( $this->service->get( $id ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( "pages.attribution.attribution-edit" );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributionModelRequest $request, $id)
    {
        $status = $this->service->updateModel( $id , $request->input( 'name' ) , $request->input( 'levels' ) );

        Cache::tags( 'AttributionModel' )->flush();

        return response()->json( [ 'status' => $status ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $modelId , $feedId )
    {
        $this->service->removeFeed( $modelId , $feedId );
    }

    public function levels ( $modelId ) {
        return response()->json( $this->service->levels( $modelId ) );
    }

    public function copyLevels ( Request $request ) {
        return response()->json( [ "status" =>
            $this->service->copyLevels(
                $request->input( 'currentModelId' ) ,
                $request->input( 'templateModelId' )
            )
        ] );
    }

    public function syncLevelsWithMT1 () {
        return response()->json( [ "status" => $this->service->syncLevelsWithMT1() ] );
    }

    public function setModelLive ( $modelId ) {
        $status = $this->service->setLive( $modelId );

        Cache::tags( 'AttributionModel' )->flush();

        return response()->json( [ "status" => $status ] );
    }

    public function runAttribution ( Request $request ) {
        if ( $request->input( 'modelId' ) > 0 ) {
            $userEmail = null;

            $currentUser = Sentinel::getUser();
            if ( !is_null( $currentUser ) ) {
                $userEmail = $currentUser->email;
            }

            Artisan::queue( 'attribution:commit' , [
                'type' => "model",
                '--modelId' => $request->input( 'modelId' ) ,
                '--userEmail' => $userEmail
            ] );

            $this->service->setProcessingFlag( $request->input( 'modelId' ) , true );

            Cache::tags( 'AttributionModel' )->flush();
        } else {
            Artisan::queue( 'attribution:commit' );
        }

        return response()->json( [ "status" => true ] );
    }

    public function getModelFeeds ( $modelId ) {
        return response()->json( $this->service->getModelFeeds( $modelId ) );
    }

    public function showProjection () {
        return response()->view( "pages.attribution.attribution-projection" , [ 'models' => $this->service->getNonliveModels() ] );
    }

    public function getReportData ( Request $request ) {
        return response()->json( $this->reportService->getReportData( $request ) );
    }
}

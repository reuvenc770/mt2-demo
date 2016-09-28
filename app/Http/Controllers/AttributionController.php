<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\AttributionModelService;
use App\Http\Requests\AttributionModelRequest;

use App\Collections\Attribution\ProjectionChartCollection;
use App\Collections\Attribution\ProjectionReportCollection;

use Artisan;
use Cache;

class AttributionController extends Controller
{
    protected $service;

    protected $chartCollection;
    protected $reportCollection;

    public function __construct (
        AttributionModelService $service ,
        ProjectionChartCollection $chartCollection ,
        ProjectionReportCollection $reportCollection
    ) {
        $this->service = $service; 
        $this->chartCollection = $chartCollection;
        $this->reportCollection = $reportCollection;
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
        return response()->view( 'pages.attribution.attribution-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.attribution.attribution-add' );
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
        return response()->view( 'pages.attribution.attribution-edit' );
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
    public function destroy($id)
    {
        //
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
            Artisan::queue( 'attribution:commit' , [ 
                'modelId' => $request->input( 'modelId' )
            ] );
        } else {
            Artisan::queue( 'attribution:commit' );
        }

        return response()->json( [ "status" => true ] );
    }

    public function getModelFeeds ( $modelId ) {
        return response()->json( $this->service->getModelFeeds( $modelId ) );
    }

    public function showProjection ( $modelId ) {
        return response()->view( 'pages.attribution.attribution-projection' , [ 'modelId' => $modelId ] );
    }

    public function getChartData ( $modelId ) {
        return $this->chartCollection->getChartData( $modelId );
    }

    public function getReportData ( $modelId ) {
        return $this->reportCollection->getReportData( $modelId );
    }
}

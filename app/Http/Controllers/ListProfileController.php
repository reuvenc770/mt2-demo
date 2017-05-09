<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Cache;
use AdrianMejias\States\States;

use App\Http\Requests;
use App\Http\Requests\SubmitListProfileRequest;
use App\Http\Requests\SubmitListCombineRequest;

use App\DataModels\CacheReportCard;
use App\Models\CakeVertical;

use App\Jobs\ExportSimpleCombineJob;
use App\Jobs\ListProfileBaseExportJob;

use App\Services\ListProfileService;
use App\Services\EntityCacheService;
use App\Services\DomainGroupService;
use App\Services\ListProfileCombineService;

class ListProfileController extends Controller
{
    use DispatchesJobs;

    protected $listProfile;
    protected $states;
    protected $ispService;
    protected $combineService;

    public function __construct (
        ListProfileService $listProfileService ,
        States $states ,
        DomainGroupService $ispService,
        ListProfileCombineService $combineService
    ) {
        $this->listProfile = $listProfileService;
        $this->states = $states;
        $this->ispService = $ispService;
        $this->combineService = $combineService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json( $this->listProfile->getAllListProfiles() );
    }

    public function listAll () {
        return response()->view( 'pages.listprofile.list-profile-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'pages.listprofile.list-profile-add' , $this->getFormFieldOptions() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitListProfileRequest $request)
    {
        $data = $request->all();

        $columns = [];
        foreach($data['selectedColumns'] as $tile) {
            $columns[] = $tile['header'];
        }

        $data['selectedColumns'] = $columns;

        $profileID = $this->listProfile->create( $data );

        if(isset($data['exportOptions']['interval']) && in_array("Immediately", $data['exportOptions']['interval'])) {
            $cacheTagName = null;
            $this->dispatch(new ListProfileBaseExportJob($profileID, $cacheTagName, str_random(16)));
        }

        Flash::success("List Profile was Successfully Created");

        return response()->json( [ 'status' => true ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view(
            'pages.listprofile.list-profile-edit' ,
            $this->getFormFieldOptions( $id )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubmitListProfileRequest $request, $id)
    {
        $data = $request->all();

        $columns = [];
        foreach($data['selectedColumns'] as $tile) {
            $columns[] = $tile['header'];
        }

        $data['selectedColumns'] = $columns;

        $this->listProfile->formUpdate( $id , $data );

        if(isset($data['exportOptions']['interval']) && in_array("Immediately", $data['exportOptions']['interval'])) {
            $cacheTagName = null;
            $this->dispatch(new ListProfileBaseExportJob($id, $cacheTagName, str_random(16)));
        }

        Flash::success("List Profile was Successfully Updated");

        return response()->json( [ 'status' => true ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->listProfile->tryToDelete( $id );
        $code = $response !== true ? 500 : 200;

        return response()->json( [ 'delete' => $response ] , $code );
    }

    //USES LIST PROFILE DB NOT MT1 UNIQUE PROFILE
    public function listActive(){
        return response()->json(
            $this->listProfile->getActiveListProfiles()
        );
    }

    protected function getFormFieldOptions ( $id = 0 , $addOptions = [] ) {
        if ( $id > 0 ) {
            $addOptions[ 'id' ] = $id;
            $addOptions[ 'prepop' ] = $this->listProfile->getFullProfileJson( $id );
        }

        $formFields = array_merge( [
            'feeds' => EntityCacheService::get( \App\Repositories\FeedRepo::class , 'array' ) ,
            'feedGroups' => EntityCacheService::get( \App\Repositories\FeedGroupRepo::class , 'array' ) ,
            'clients' => EntityCacheService::get( \App\Repositories\ClientRepo::class , 'array' ) ,
            'clientFeedMap' => EntityCacheService::get( \App\Repositories\ClientRepo::class , 'feedMap' ) ,
            'partyFeedMap' => EntityCacheService::get( \App\Repositories\FeedRepo::class , 'partyMap' ) ,
            'countryFeedMap' =>  EntityCacheService::get( \App\Repositories\FeedRepo::class , 'countryMap' ) ,
            'states' => $this->states->all() ,
            'isps' => $this->ispService->getAllActive() ,
            'categories' => CakeVertical::orderBy('name')->get() ,
        ] , $addOptions );

        return $formFields;
    }


    public function createListCombine( SubmitListCombineRequest $request){

        $insertData = [
            "name" => $request->input("combineName"),
            "ftp_folder" => $request->input("ftpFolder"),
            'party' => $request->input("combineParty"),
        ];
        $this->combineService->insertCombine($insertData, $request->input("selectedProfiles"));
    }

    public function getCombines(){
        return response()->json($this->combineService->getAll());
    }
    public function getListCombinesOnly(){
        return response()->json($this->combineService->getListCombinesOnly());
    }

    public function exportListCombine(Request $request){
        $combineId = $request->input("id");
        $ran = str_random(10);
        $reportCard = CacheReportCard::makeNewReportCard("Combine-{$combineId}-{$ran}");
        $this->dispatch(new ExportSimpleCombineJob($combineId, $reportCard, str_random(16)));
    }

    public function editListCombine( $id ) {

        if ( !$this->combineService->isEditable($id) ) {
            abort(404);
        }

        $combineData = $this->combineService->getCombineById($id);
        $listProfileIds = $combineData->listProfiles->pluck('id');
        return response()->view( 'pages.listprofile.list-combine-edit' , [
            'combineId' => $id ,
            'combineName' => $combineData->name ,
            'ftpFolder' => $combineData->ftp_folder ,
            'combineParty' => $combineData->party,
            'listProfileIds' => $listProfileIds
        ]);
    }

    public function updateListCombine( SubmitListCombineRequest $request ) {

        $this->combineService->updateCombine( $request->all() );

        Flash::success( 'List combine was successfully updated.' );

    }

    public function copy(Request $request){
        $id = $request->input('id');
        $newId = $this->listProfile->cloneProfile($id);

        Cache::tags("ListProfile")->flush();

        Flash::success( 'List profile was successfully copied.' );

        return response()->json( [ 'status' => true , 'id' => $newId ] );
    }

    public function getFirstPartyListCombines(){
        return response()->json( $this->combineService->getFirstPartyListCombines() );
    }
}

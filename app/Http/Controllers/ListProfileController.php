<?php

namespace App\Http\Controllers;

use App\Jobs\ListProfileCombineExportJob;
use App\Services\FeedGroupService;
use App\Services\ListProfileService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\MT1Services\CountryService;
use AdrianMejias\States\States;
use App\Services\DomainGroupService;
use App\Models\CakeVertical;
use App\Services\OfferService;
use App\Services\ClientService;
use App\Jobs\ListProfileBaseExportJob;
use App\Services\FeedService;
use App\Http\Requests\SubmitListProfileRequest;
use App\Http\Requests\SubmitListCombineRequest;
use Laracasts\Flash\Flash;
use App\Services\ListProfileCombineService;
use Cache;
use App\Services\EntityCacheService;

class ListProfileController extends Controller
{
    use DispatchesJobs;

    protected $listProfile;
    protected $states;
    protected $ispService;
    protected $offerService;
    protected $clientService;
    protected $feedService;
    protected $combineService;
    protected $feedGroupService;

    public function __construct (
        ListProfileService $listProfileService ,
        CountryService $mt1CountryService ,
        States $states ,
        DomainGroupService $ispService ,
        OfferService $offerService,
        ClientService $clientService,
        FeedService $feedService,
        FeedGroupService $feedGroupService,
        ListProfileCombineService $combineService
    ) {
        $this->listProfile = $listProfileService;
        $this->mt1CountryService = $mt1CountryService;
        $this->states = $states;
        $this->ispService = $ispService;
        $this->offerService = $offerService;
        $this->clientService = $clientService;
        $this->feedService = $feedService;
        $this->combineService = $combineService;
        $this->feedGroupService = $feedGroupService;
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
            $this->dispatch(new ListProfileBaseExportJob($profileID, str_random(16)));
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
            $this->dispatch(new ListProfileBaseExportJob($id, str_random(16)));
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
        $id = $request->input("id");
        $this->dispatch(new ListProfileCombineExportJob($id, str_random(16)));
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

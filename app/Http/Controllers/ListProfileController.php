<?php

namespace App\Http\Controllers;

use App\Services\ListProfileCombineService;
use App\Services\ListProfileService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\MT1Services\CountryService;
use AdrianMejias\States\States;
use App\Services\DomainGroupService;
use App\Models\CakeVertical;
use App\Services\OfferService;
use App\Services\ClientService;
use App\Services\FeedService;
use App\Http\Requests\SubmitListProfileRequest;
use Laracasts\Flash\Flash;

class ListProfileController extends Controller
{
    protected $listProfile;
    protected $states;
    protected $ispService;
    protected $offerService;
    protected $clientService;
    protected $feedService;
    protected $listProfileCombineService;

    public function __construct (
        ListProfileService $listProfileService ,
        CountryService $mt1CountryService ,
        States $states ,
        DomainGroupService $ispService ,
        OfferService $offerService,
        ClientService $clientService,
        FeedService $feedService,
        ListProfileCombineService $combineService
    ) {
        $this->listProfile = $listProfileService;
        $this->mt1CountryService = $mt1CountryService;
        $this->states = $states;
        $this->ispService = $ispService;
        $this->offerService = $offerService;
        $this->clientService = $clientService;
        $this->feedService = $feedService;
        $this->listProfileCombineService = $combineService;
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
        return response()->view( 'bootstrap.pages.listprofile.list-profile-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view( 'bootstrap.pages.listprofile.list-profile-add' , $this->getFormFieldOptions() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitListProfileRequest $request)
    {
        #Need to fire a job to run the list profile at this point if the user chooses immediately

        $this->listProfile->create( $request->all() );

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
            'bootstrap.pages.listprofile.list-profile-edit' ,
            $this->getFormFieldOptions( [ 'id' => $id , 'prepop' => $this->listProfile->getFullProfileJson( $id ) ] )
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
        #Need to fire a job to run the list profile at this point if the user chooses immediately

        $this->listProfile->formUpdate( $id , $request->all() );

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
    }

    //USES LIST PROFILE DB NOT MT1 UNIQUE PROFILE
    public function listActive(){
        return response()->json(
            $this->listProfile->getActiveListProfiles()
        );
    }

    protected function getFormFieldOptions ( $addOptions = [] ) {

        return array_merge( [
            'feeds' => $this->feedService->getAllFeedsArray() ,
            'clients' => $this->clientService->getAllClientsArray() ,
            'clientFeedMap' => $this->clientService->getClientFeedMap() ,
            'countries' => $this->mt1CountryService->getAll() ,
            'states' => $this->states->all() ,
            'isps' => $this->ispService->getAll() ,
            'categories' => CakeVertical::orderBy('name')->get() ,
        ] , $addOptions );
    }

    public function createListCombine(Request $request){

        $insertData = [
            "name" => $request->input("name"),
        ];
        $this->listProfileCombineService->insertCombine($insertData, $request->input("selectedProfiles"));
    }

    public function getCombines(){
        return response()->json($this->listProfileCombineService->getAll());
    }
}

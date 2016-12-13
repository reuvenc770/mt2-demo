<?php

namespace App\Http\Controllers;


use AdrianMejias\States\States;
use App\Services\DoingBusinessAsService;
use App\Services\EspApiAccountService;
use App\Services\DomainGroupService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;

class DoingBusinessAsController extends Controller
{
    protected $doingBusinessService;
    protected $espAccountService;
    protected $domainGroupService;
    protected $states;
    public function __construct(DoingBusinessAsService $doingBusinessService, EspApiAccountService $espAccountService , DomainGroupService $domainGroupService , States $states){
        $this->doingBusinessService = $doingBusinessService;
        $this->espAccountService = $espAccountService;
        $this->domainGroupService = $domainGroupService;
        $this->states = $states;
    }

    public function listAll()
    {
        return response()
            ->view("bootstrap.pages.dba.dba-index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dbas = $this->doingBusinessService->getAll();
        return response()->json($dbas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = States::all();
        $espAccounts = $this->espAccountService->getAllAccounts();
        $isps = $this->domainGroupService->getAllActive();
        return view("bootstrap.pages.dba.dba-add", [
            "states" => $states ,
            "espAccounts" => $espAccounts,
            "isps" => $isps
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddDBARequest $request)
    {
        Flash::success("DBA was Successfully Created");
        $request = $this->doingBusinessService->insertRow($request->all());
        return response()->json( [ 'status' => $request ] );
    }

    /**
     * Display the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to lookup.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->doingBusinessService->getDBA( $id ));

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( )
    {
        $states = States::all();
        $espAccounts = $this->espAccountService->getAllAccounts();
        $isps = $this->domainGroupService->getAllActive();
        return response()
            ->view( "bootstrap.pages.dba.dba-edit", [
            "states" => $states ,
            "espAccounts" => $espAccounts,
            "isps" => $isps
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response = $this->doingBusinessService->tryToDelete($id);
        $code = $response !== true ? 500 : 200;
        return response()->json( [ 'delete' => $response ],$code );

    }

    public function toggle(Request $request, $id){
        $this->doingBusinessService->toggleRow($id,$request->get("direction"));
    }
}

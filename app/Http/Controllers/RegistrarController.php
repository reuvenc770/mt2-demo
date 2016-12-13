<?php

namespace App\Http\Controllers;

use App\Services\RegistrarService;
use App\Services\DoingBusinessAsService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Log;
use Laracasts\Flash\Flash;
class RegistrarController extends Controller
{
    protected $registrarService;
    protected $dbaService;

    public function __construct(RegistrarService $registrarService , DoingBusinessAsService $doingBusinessAsService )
    {
        $this->registrarService = $registrarService;
        $this->dbaService = $doingBusinessAsService;
    }

    public function listAll()
    {
        return response()
            ->view("bootstrap.pages.registrar.registrar-index");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registrars = $this->registrarService->getAll();
        $return = array();
        foreach ($registrars as $registrar) {
            $return[] = array(
                $registrar->id,
                $registrar->name,
                $registrar->username

            );
        }
        return response()->json($return);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("bootstrap.pages.registrar.registrar-add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddRegistrarRequest $request)
    {
        Flash::success("Registrar was Successfully Created");
        $request = $this->registrarService->insertRow($request->all());
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
        return $this->registrarService->getRegistrar( $id );

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( )
    {
        return response()
            ->view( "bootstrap.pages.registrar.registrar-edit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditRegistrarRequest $request, $id)
    {
        $this->registrarService->updateAccount( $id , $request->toArray() );
        Flash::success("Registrar Account was Successfully Updated");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response = $this->registrarService->tryToDelete($id);
        $code = $response !== true ? 500 : 200;
        return response()->json( [ 'delete' => $response ],$code );

    }

    public function toggle(Request $request, $id){
        $this->registrarService->toggleRow($id,$request->get("direction"));
    }

    public function tryToDelete($id){
        $canBeDeleted =  $this->registrar->canBeDeleted($id);
        if($canBeDeleted === true){
            $this->registrar->delete($id);
            return true;
        } else{
            return $canBeDeleted;
        }
    }
}

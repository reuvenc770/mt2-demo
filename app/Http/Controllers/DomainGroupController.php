<?php

namespace App\Http\Controllers;

use App\Services\DomainGroupService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Laracasts\Flash\Flash;
class DomainGroupController extends Controller
{
    protected $domainGroupService;

    public function __construct(DomainGroupService $domainGroupService)
    {
        $this->domainGroupService = $domainGroupService;
    }

    public function listAll()
    {
        return response()
            ->view('pages.domaingroup.domaingroup-index');
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.domaingroup.domaingroup-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\DomainGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\DomainGroupRequest $request)
    {
        Flash::success("ISP Group was Successfully Created");
        $request = $this->domainGroupService->insertGroup($request->all());
        return response()->json( [ 'status' => $request ] );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json( $this->domainGroupService->getDomainGroupById( $id ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()
            ->view('pages.domaingroup.domaingroup-edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\DomainGroupRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\DomainGroupRequest $request, $id)
    {
        $this->domainGroupService->updateGroup( $id , $request->toArray() );
        Flash::success("ISP Group was Successfully Updated");
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

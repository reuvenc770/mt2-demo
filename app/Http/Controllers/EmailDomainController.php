<?php

namespace App\Http\Controllers;

use App\Services\DomainGroupService;
use App\Services\EmailDomainService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Laracasts\Flash\Flash;
class EmailDomainController extends Controller
{
    protected $emailDomainService;
    protected $domainGroupService;

    public function __construct(EmailDomainService $emailDomainService)
    {
        $this->emailDomainService = $emailDomainService;
    }

    public function listAll()
    {
        return response()
            ->view('pages.emaildomain.emaildomain-index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(DomainGroupService $domainGroupService)
    {
        $domainGroups = $domainGroupService->getAll();
        return view('pages.emaildomain.emaildomain-add' ,['domainGroups' => $domainGroups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\EmailDomainRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\EmailDomainRequest $request)
    {
        Flash::success("ISP Domain was Successfully Created");
        $request = $this->emailDomainService->insertDomain($request->all());
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
        return response()->json( $this->emailDomainService->getEmailDomainById( $id ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param DomainGroupService $domainGroupService
     * @return \Illuminate\Http\Response
     */
    public function edit($id,DomainGroupService $domainGroupService)
    {
        $domainGroups = $domainGroupService->getAll();
        return response()
            ->view('pages.emaildomain.emaildomain-edit', ['domainGroups' => $domainGroups]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\EmailDomainRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EmailDomainRequest $request, $id)
    {
        $this->emailDomainService->updateDomain( $id , $request->toArray() );
        Flash::success("ISP Domain was Successfully Updated");
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

<?php

namespace App\Http\Controllers;

use App\Services\EspApiAccountService;
use App\Services\MailingTemplateService;
use Illuminate\Http\Request;

use App\Http\Requests;

class MailingTemplateController extends Controller
{
    public $service;

    public function __construct(MailingTemplateService $mailingTemplateService )
    {
        $this->service = $mailingTemplateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return $this->service->getAllTemplates();
    }
    public function listAll()
    {
        return response()
            ->view('pages.mailingtemplate.mailingtemplate-index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return response()->view( 'pages.mailingtemplate.mailingtemplate-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddMailingTemplateForm $request)
    {
       $espIds = explode(',',$request->input("selectedEsps"));

        $insertData = [
                "template_name" => $request->input("name"),
                "template_type" => $request->input("templateType"),
                "template_html" => $request->input("html"),
                "template_text" => $request->input("text"),
        ];

        $this->service->insertTemplate($insertData, $espIds);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->service->retrieveTemplate($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return response()->view( 'pages.mailingtemplate.mailingtemplate-edit');
    }

    public function preview(Request $request, $id = null){

        if($request->has("html")){
            return response($request->input("html"));
        }
        $info = $this->service->retrieveTemplate($id);

        return response($info['template_html']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditMailingTemplateForm $request, $id)
    {
        $espIds = explode(',',$request->input("selectedEsps"));
        $insertData = [
            "template_name" => $request->input("name"),
            "template_type" => $request->input("templateType"),
            "template_html" => $request->input("html"),
            "template_text" => $request->input("text"),
        ];
        $this->service->updateTemplate($insertData, $id, $espIds);

        return response()->json(["success"=>true]);

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

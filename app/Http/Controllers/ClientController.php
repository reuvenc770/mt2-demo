<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\NavigationService;

class ClientController extends Controller
{
    protected $clients = [
        [ 1 , 'Acxiom' , 'Active' , 'Do Not Mail' , 402833 , 'feed.acxiom.com' , 'Acxiom' , 'Direct' , 'jdoe' , 'jdoe@acxiom.com' , '888-888-8888' , '1 Main St, NY, NY 10002' , 'US' ] ,
        [ 2 , 'Tactara' , 'Active' , 'Misc' , 402738 , 'feed.tactara.com' , 'Tactara' , 'Direct' , 'ldoe' , 'ldoe@tactara.com' , '777-777-7777' , '1 Side St, NY, NY 10009' , 'US' ] ,
        [ 3 , 'Popular Marketing' , 'Paused' , 'Misc' , 402928 , 'feed.popularm.net' , 'Popular Marketing' , 'Direct' , 'ohpie' , 'ohpie@popularm.com' , '666-666-6666' , '1 Left St, NY, NY 10029' , 'US' ]
    ];

    protected $nav = [
        'Reporting' => [
            'url' => '' , #route( 'reporting.dashboard' )
            'chiildren' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'reporting.dashboard' )
                'Revenue' => [ 'url' => '' ] , #route( 'reporting.revenue' )
                'Lead' => [ 'url' => '' ] , #route( 'reporting.lead' )
                'Feed' => [ 'url' => '' ] , #route( 'reporting.feed' )
                'Campaign' => [ 'url' => '' ] , #route( 'reporting.campaign' )
                'Manual Import' => [ 'url' => '' ] #route( 'reporting.import' )
            ]
        ] ,
        'Offer' => [
            'url' => '' , #route( 'offer.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'offer.dashboard' )
                'Search' => [ 'url' => '' ] , #route( 'offer.search' )
                'Vertical Management' => [
                    'url' => '' , #route( 'offer.vertical' )
                    'children' => [
                        'Vertical Categories' => [ 'url' => '' ] , #route( 'offer.vertical.category' )
                        'Vertical Category Groups' => [ 'url' => '' ] #route( 'offer.vertical.groups' )
                    ]
                ] 
            ]
        ] ,
        'Creative' => [
            'url' => '' , #route( 'creative.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'creative.dashboard' )
                'Search' => [ 'url' => '' ] , #route( 'creative.search' )
                'Approval' => [ 'url' => '' ] , #route( 'creative.approval' )
                'FTP Upload' => [ 'url' => '' ] #route( 'creative.upload' )
            ]
        ] ,
        'List' => [
            'url' => '' , #route( 'list.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'list.dashboard' )
                'Search' => [ 'url' => '' ] , #route( 'list.search' )
                'Attribution' => [ 'url' => '' ] , #route( 'list.attribution' )
                'Import' => [ 'url' => '' ] , #route( 'list.import' )
                'Groups' => [ 'url' => '' ] , #route( 'list.groups' )
                'Profiles' => [ 'url' => '' ] , #route( 'list.profiles' )
                'Suppression' => [ 'url' => '' ] , #route( 'list.suppression' )
            ]
        ] ,
        'ESP' => [
            'url' => '' , #route( 'esp.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'esp.dashboard' )
                'Search' => [ 'url' => '' ] , #route( 'esp.search' )
                'Profiles' => [ 'url' => '' ] , #route( 'esp.profiles' )
                'Accounts' => [ 'url' => '' ] #route( 'esp.accounts' )
            ]
        ] ,
        'Schedule' => [
            'url' => '' , #route( 'schedule.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route( 'schedule.dashboard' )
                'Search' => [ 'url' => '' ] , #route( 'schedule.search' )
                'Pachages' => [ 'url' => '' ] , #route( 'schedule.pakages' )
                'Templates' => [ 'url' => '' ]
            ]
        ] ,
        'Infrastructure' => [
            'url' => '' , #route( 'infrastructure.dashboard' )
            'children' => [
                'Dashboard' => [ 'url' => '' ] , #route(  )
            ]
        ]
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json( $this->clients );
    }

    /**
     *
     */
    public function list () {
        return response()->view( 'pages.mocks.client-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.mocks.client-edit' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

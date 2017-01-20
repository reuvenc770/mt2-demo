<?php

namespace App\Http\Controllers;

use App\Services\NavigationService;
use Illuminate\Http\Request;
use Cache;
use App\Http\Requests;

class NavigationController extends Controller
{
    protected $navigationService;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.navigation.navigation-index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $bool = $this->navigationService->updateNavigation($request->all());
        Cache::tags("navigation-bootstrap")->flush();
        return response()->json(["success" =>$bool]);
    }

    public function returnCurrentNavigation(){
        return response()->json($this->navigationService->getMenuTreeJson());
    }

    public function returnValidOrphanNavigation(){
        return response()->json($this->navigationService->getValidRoutesWithNoParent());
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

<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use App\Http\Requests\LoginFormRequest;
use Sentinel;
use Laracasts\Flash\Flash;

class SessionsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(LoginFormRequest $request)
    {
        $input = $request->only('email', 'password');
        try {
            if (Sentinel::authenticate($input, $request->has('remember'))) {
                Flash::success("Successfully logged in");
                return redirect()->intended('/home');
            }
            Flash::error("Invalid credentials provided");
            return redirect()->back()->withInput();
        } catch (NotActivatedException $e) {
            Flash::error("'User Not Activated.");
            return redirect()->back()->withInput();
        } catch (ThrottlingException $e) {
            Flash::error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id = null)
    {
        Sentinel::logout();
        return redirect()->route('home');
    }
}
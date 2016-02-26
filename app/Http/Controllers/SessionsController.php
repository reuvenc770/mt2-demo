<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use App\Http\Requests\LoginFormRequest;
use Sentinel;
use Laracasts\Flash\Flash;
use App\Facades\UserEventLog;
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
        $password = $request->get('password');
        $login = $request->get("login");
        $credentials = [
            'login' => $login,
        ];
        try {
            $user = Sentinel::findByCredentials($credentials);
            if($user->mt1_hash == md5($password)){  //old password checked out lets log them in and change their pw.
                Sentinel::login($user);
                Sentinel::update($user, array('password' => $password));
                $user->mt1_hash = "";
                $user->save();
                Flash::success("Successfully logged in");
                return redirect()->intended('/home');
            } else {
                 if (Sentinel::authenticate(array("login" => $login, "password" => $password), $request->has('remember'))) {
                    Flash::success("Successfully logged in");
                    return redirect()->intended('/home');
            }
            }
            UserEventLog::insertCustomRequest(0,str_replace(".","/",$request->route()->getAction()['as']),"Login Fail",\App\Models\UserEventLog::UNAUTHORIZED);
            Flash::error("Invalid credentials provided");
            return redirect()->back()->withInput();
        } catch (NotActivatedException $e) {
            UserEventLog::insertCustomRequest(0,str_replace(".","/",$request->route()->getAction()['as']),"Deactivated Login Attempt ",\App\Models\UserEventLog::UNAUTHORIZED);
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
        Flash::success("Successfully logged out");
        return redirect()->route('home');
    }
}
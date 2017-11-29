<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>  
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class DeleteUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Sentinel::getUser();
        $admin = Sentinel::findRoleByName('Admin');
        if ( $user->inRole( $admin ) ) {  //lets make sure its an admin
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}

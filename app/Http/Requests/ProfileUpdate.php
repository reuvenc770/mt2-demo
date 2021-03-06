<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;
use Sentinel;

class ProfileUpdate extends Request
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
        if ($user->inRole($admin)|| $user->getUserId() == $this->get('id')) {  //lets make sure its an admin or the same user
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
        return  array(
            'email' => 'required|email|unique:users,email,'.$this->get('id'),
            'username' => 'required|unique:users,username,'.$this->get('id'),
            'first_name' => 'required',
            'last_name' => 'required',
            'roles'      => 'required',
            'password' => 'hash:userPassword' ,
            'newpass' => 'different:password|confirmed'
        );

    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'password.hash' => 'Current password is incorrect.',
            'newpass.confirmed' => 'Password confirmation does not match.',
            'email.required'    => 'Email address is required.',
            'username.required' => 'A username is required.',
            'password.required' => 'Password is required.',
            'first_name.required'   => 'User\'s first name is required.',
            'last_name.required'    => 'User\'s last name is required.'
        ];
    }
}

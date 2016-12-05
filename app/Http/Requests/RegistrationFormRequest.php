<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;
class RegistrationFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'username' =>'required|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'roles'      => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'roles.required' => 'At least 1 role is required.',
            'email.required'    => 'Email address is required.',
            'password.required' => 'Password is required.',
            'username.required' => 'A username is required.',
            'first_name.required'   => 'User\'s first name is required.',
            'last_name.required'    => 'User\'s last name is required.'
        ];
    }
}
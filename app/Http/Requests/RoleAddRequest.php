<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;
use Sentinel;
class RoleAddRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("role.add")){
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
            'name' => 'required|unique:roles|',
            'permissions'      => 'required',
        ];
    }
    /**
     *
     */
    public function messages ()
    {
        return [
            'name.required' => 'Role name is required.' ,
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'At least 1 permission is required.'
        ];
    }
}
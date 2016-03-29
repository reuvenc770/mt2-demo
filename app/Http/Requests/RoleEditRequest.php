<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 3:00 PM
 */

namespace App\Http\Requests;
use App\Http\Requests\Request;
use Sentinel;

class RoleEditRequest extends Request
{
    public function authorize()
    {
        if(Sentinel::hasAccess("role.edit")){
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
            'name' => 'required',
            'slug' => 'required',
            'permissions'      => 'required',
        ];
    }
}
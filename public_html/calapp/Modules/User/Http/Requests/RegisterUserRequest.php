<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PasswordValidationRule;
use App\Rules\PhoneValidationRule;
/**
 * Class RegisterUserRequest
 *
 * The Validation Rules is Defined for Register User.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\User
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|min:8|max:20|regex:/^[a-z0-9_.-]+$/|unique:'.config('core.acl.users_table').',username,NULL,id,deleted_at,NULL',
            'firstname' => 'required|max:20',
            'lastname' => 'required|max:20',
            'email' => 'required|email|unique:'.config('core.acl.users_table').',email,NULL,id,deleted_at,NULL',
            'password' => [
                'required',
                'min:6',
                'max:12',
                'confirmed',
                new PasswordValidationRule
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

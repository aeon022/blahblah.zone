<?php

namespace Modules\User\Http\Requests;

use App\Rules\PasswordValidationRule;
use Illuminate\Foundation\Http\FormRequest;
/**
 * Class ChangePasswordRequest
 *
 * The Validation Rules is Defined for Change Password.
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
class ChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => [
                'required',
                'min:6',
                'max:12',
                'different:old_password',
                new PasswordValidationRule
            ],
            'password_confirmation' => 'required|same:password'
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

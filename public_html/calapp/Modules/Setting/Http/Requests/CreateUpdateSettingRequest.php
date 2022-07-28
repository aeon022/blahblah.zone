<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

use App\Rules\PhoneValidationRule;

/**
 * Class CreateUpdateSettingRequest
 *
 * The Validation Rules is Defined for Setting.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Setting
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateUpdateSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $form_for = $this->request->get('form_for');
        switch ($form_for) {
        case 'company_detail':
            return [
                    'company_name' => 'required|max:50',
                    'company_legal_name' => 'required|max:50',
                    'company_address' => 'required',
                    'company_zipcode' => 'required|digits_between:1,10|integer',
                    'company_phone' => ['nullable',new PhoneValidationRule],
                    'company_email' => 'nullable|email',
                    'company_website' => 'nullable|url'
                ];
                break;
        case 'email_setting':
            return [
                    'company_from_email' => 'nullable|email',
                    'smtp_protocol' => 'required',
                    'smtp_host' => 'required',
                    'smtp_user' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required'
                ];
                break;
        case 'system_setting':
            return [
                    'tables_pagination_limit' => 'nullable|integer'
                ];
                break;
        case 'system_update':
            return [
                    'update_url' => 'nullable|url'
                ];
                break;
        default:
            return [
                    //
                ];
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_zipcode.integer' => 'The zipcode must be an digits.',
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

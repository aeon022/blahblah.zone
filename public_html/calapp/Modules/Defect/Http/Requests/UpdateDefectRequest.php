<?php

namespace Modules\Defect\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateDefectRequest
 *
 * The Validation Rules is Defined for Defect.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Defect
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class UpdateDefectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->segment(3);
        return [
            'generated_id' => 'required|unique:'.
            config('core.acl.defects_table').',generated_id,'.$id,
            'defect_type' => 'required',
            'defect_name' => 'required|max:'.config('core.max_length'),
            'project_id' => 'required',
            'project_version' => 'required',
            'status' => 'required',
            'assigned_group_id' => 'required',
            'severity' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'generated_id.required' => 'The defect id field is required.',
            'project_id.required' => 'The project field is required.',
            'assigned_group_id.required' => 'Please select at least one group.'
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

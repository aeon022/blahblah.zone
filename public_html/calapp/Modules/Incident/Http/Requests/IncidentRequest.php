<?php

namespace Modules\Incident\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class IncidentRequest
 *
 * The Validation Rules is Defined for Incident.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Incident
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class IncidentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
        case 'POST':
            return [
                    'incident_name' => 'required|max:'.config('core.max_length'),
                    'generated_id' => 'required|unique:'.
                    config('core.acl.incidents_table'),
                    'incident_type' => 'required',
                    'status' => 'required',
                    'assigned_group_id' => 'required',
                    'priority' => 'required',
                ];

        case 'PUT':
        case 'PATCH':
            $id = $this->segment(3);
            return [
                    'incident_name' => 'required|max:'.config('core.max_length'),
                    'generated_id' => 'required|unique:'.
                    config('core.acl.incidents_table').',generated_id,'.$id,
                    'incident_type' => 'required',
                    'status' => 'required',
                    'assigned_group_id' => 'required',
                    'priority' => 'required',
                ];
            
        default:
            break;
        }
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'generated_id.required' => 'The incident id field is required.',
            'generated_id.unique' => 'The incident id field is not unique ,Please refresh the page',
            'project_id.required' => 'The project field is required.',
            'assigned_group_id.required' => 'Please select at least one group.',
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

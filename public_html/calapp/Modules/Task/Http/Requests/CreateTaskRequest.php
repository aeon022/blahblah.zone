<?php

namespace Modules\Task\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\EstimatedHours;

/**
 * Class CreateTaskRequest
 *
 * The Validation Rules is Defined for Task.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Task
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => [
                'required',
                'max:'.config('core.max_length'),
                Rule::unique(config('core.acl.task_table'))->where(
                    function ($query) {
                        return $query->where('project_id', $this->request->get('project_id'))
                            ->where('deleted_at', null);
                    }
                ),
            ],
            'project_id' => 'required',
            'project_version' => 'required',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after_or_equal:planned_start_date',
            'task_start_date' => 'required|date|after_or_equal:planned_start_date|before_or_equal:planned_end_date',
            'task_end_date' => 'required|date|after_or_equal:task_start_date|before_or_equal:planned_end_date',
            'status' => 'required',
            'priority' => 'required',
            'assign_to' => 'required',
            'generated_id' => 'required|unique:'. config('core.acl.task_table'),
            'estimated_hours' => ['nullable',new EstimatedHours],
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

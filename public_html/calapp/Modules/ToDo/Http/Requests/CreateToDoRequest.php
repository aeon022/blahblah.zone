<?php

namespace Modules\ToDo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\carbon;
/**
 * Class CreateToDoRequest
 *
 * The Validation Rules is Defined for Create ToDo.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\ToDo
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateToDoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today = Carbon::now()->subDays(1);
        return [
            'assigned' => 'required',
            'title' => 'required|min:3|max:100',
            'status' => 'required',
            'due_date' => 'required|date|after:' . $today,
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

<?php

namespace Modules\Task\Models\Attribute;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Trait TaskAttribute
 *
 * The Attribute is Defined for Task.
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
trait TaskAttribute
{
    /**
     * This mutator automatically set planned start date format.
     *
     * @param Date $value [Planned start date]
     *
     * @return Date
     */
    public function setPlannedStartDateAttribute($value)
    {
        $this->attributes['planned_start_date'] = (new Carbon($value))->format('Y-m-d H:i:s');
    }
}

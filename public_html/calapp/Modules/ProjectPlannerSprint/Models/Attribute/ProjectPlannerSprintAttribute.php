<?php

namespace Modules\ProjectPlannerSprint\Models\Attribute;

/**
 * Trait ProjectPlannerSprintAttribute
 *
 * The Attribute is Defined for Project Planner Sprint.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\ProjectPlannerSprint
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
trait ProjectPlannerSprintAttribute
{
    /**
     * This mutator automatically convert the first character of each word to uppercase. 
     *
     * @param String $value [Sprint name]
     *
     * @return String
     */
    public function setSprintNameAttribute($value)
    {
        $this->attributes['sprint_name'] = trim(ucwords($value));
    }
}

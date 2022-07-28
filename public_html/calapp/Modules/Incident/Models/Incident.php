<?php

namespace Modules\Incident\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Incident\Models\Relationship\IncidentRelationship;

/**
 * Class Incident
 *
 * The Model is Defined for Incident.
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
class Incident extends Model
{
    use SoftDeletes, IncidentRelationship;

    const MODULE_NAME = 'Incident';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'create_user_id',
        'generated_id',
        'project_id',
        'project_version',
        'incident_type' ,
        'incident_name',
        'description',
        'status',
        'assigned_group_id',
        'assign_to',
        'priority',
        'notes'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Instantiate a new model instance.
     *
     * @param Array $attributes [object]
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->table = config('core.acl.incidents_table');
    }
}

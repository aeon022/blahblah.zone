<?php

namespace Modules\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Task\Models\Attribute\TaskAttribute;
use Modules\Task\Models\Relationship\TaskRelationship;

use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Class Task
 *
 * The Model is Defined for Task.
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
class Task extends Model
{
    use SoftDeletes, TaskRelationship, TaskAttribute, SearchableTrait;

    const MODULE_NAME = 'Task';

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
        'name',
        'parent_task_id',
        'generated_id',
        'priority',
        'project_id',
        'project_version',
        'planned_start_date',
        'planned_end_date',
        'task_start_date',
        'task_end_date',
        'estimated_hours',
        'status',
        'progress',
        'created_by',
        'description',
        'assign_to',
        'notes',
        'order'
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable;

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

        $this->table = config('core.acl.task_table');
        $this->searchable = [
            'columns' => [
                config('core.acl.task_table').'.name' => 1,
                config('core.acl.task_table').'.generated_id' => 2,
                config('core.acl.task_table').'.task_start_date' => 3,
                config('core.acl.task_table').'.task_end_date' => 3,
                config('core.acl.task_table').'.estimated_hours' => 4,
                config('core.acl.projects_table').'.project_name' => 5,
                config('core.acl.projects_table').'.project_version' => 6,
                config('core.acl.task_table').'.progress' => 7,
                config('core.acl.task_table').'.status' => 8,
                config('core.acl.task_table').'.priority' => 9
            ],
            'joins' => [
                config('core.acl.projects_table') => [
                    config('core.acl.task_table').'.project_id',
                    config('core.acl.projects_table').'.id'
                ]
            ],
        ];
    }
}

<?php

namespace Modules\Defect\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Defect\Models\Relationship\DefectRelationship;

/**
 * Class Defect
 *
 * The Model is Defined for Defect.
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
class Defect extends Model
{
    use SoftDeletes, DefectRelationship;

    const MODULE_NAME = 'Defect';

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
        'defect_type' ,
        'defect_name',
        'description',
        'status',
        'assigned_group_id',
        'assign_member',
        'severity',
        'notes',
        'attachment',
        'attachment_hash'
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
        
        $this->table = config('core.acl.defects_table');
    }
}

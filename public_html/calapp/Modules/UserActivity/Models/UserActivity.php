<?php

namespace Modules\UserActivity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\UserActivity\Models\Relationship\UserActivityRelationship;
/**
 * Class UserActivity
 *
 * The Model is Defined for UserActivity.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\UserActivity
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class UserActivity extends Model
{
    use SoftDeletes, UserActivityRelationship;
    
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
        'user_id',
        'module',
        'module_field_id',
        'status',
        'action',
        'description',
        'ip_address'
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
        
        $this->table = config('core.acl.user_activities_table');
    }
}

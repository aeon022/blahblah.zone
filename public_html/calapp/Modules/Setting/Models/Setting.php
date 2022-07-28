<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Setting
 *
 * The Model is Defined for Setting.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Setting
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class Setting extends Model
{
    const MODULE_NAME = 'Setting';

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
        'company_name',
        'company_legal_name',
        'company_short_name',
        'contact_person',
        'company_address',
        'company_country',
        'company_city',
        'company_zipcode',
        'company_phone',
        'company_email',
        'company_website',
        'company_from_email',
        'post_mark',
        'smtp_protocol',
        'smtp_host',
        'smtp_user',
        'smtp_password',
        'smtp_port',
        'smtp_encryption',
        'sparkpost_secret',
        'mailgun_domain',
        'mailgun_secret',
        'mandrill_secret',
        'is_announcement_notification',
        'active_cronjob',
        'automatic_backup',
        'login_background',
        'company_logo',
        'company_sidebar_logo',
        'sidebar_background_images',
        'is_announcement_dashboard',
        'is_activities_dashboard',
        'is_todos_dashboard',
        'is_meetings_dashboard',
        'is_clients_dashboard',
        'is_projects_dashboard',
        'is_tasks_dashboard',
        'is_defects_dashboard',
        'is_incidents_dashboard',
        'default_language',
        'default_locale',
        'timezone',
        'tables_pagination_limit',
        'date_format',
        'purchase_key',
        'unique_coder',
        'update_url',
        'is_collapsed_menu',
        'theme_layout',
        'is_sidebar_background',
        'sidebar_bg_color',
        'sidebar_bg_image',
        'sidebar_transparent_bg_image',
        'sidebar_bg_custom_color',
        'sidebar_font_color',
        'sidebar_width'
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
     * Instantiate a new model instance.
     *
     * @param Array $attributes [object]
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->table = config('core.acl.users_settings_table');
    }
}

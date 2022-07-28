<?php

namespace Modules\Menu\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Modules\Menu\Models\Menu;
use Modules\User\Models\Role\Role;
use Modules\User\Models\Department\Department;
use Modules\User\Models\Department\DepartmentRoleMenu;
use Modules\User\Models\Department\DepartmentRoleUser;
use Modules\User\Models\User\User;

use DB;

/**
 * Class MenuDatabaseSeeder
 *
 * The Seeder is Defined for Menu.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Menu
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class MenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        DB::table(config('core.acl.menu_table'))->delete();

        // --
        // Menu table
        $data = array(
            array(
                'id'=> 1,
                'parent_menu_id' => 0,
                'module' => 'dashboard',
                'label' => 'dashboard',
                'text' => 'Dashboard',
                'link' => '/dashboard',
                'icon' => 'fa fa-dashboard',
                'order' => 1,
                'subscription' => 1,
                'status' => 1
            ),
            array('id'=> 2,
                'parent_menu_id' => 0,
                'module' => 'calendar',
                'label' => 'calendar',
                'text' => 'Calendar',
                'link' => '/calendar',
                'icon' => 'fa fa-calendar',
                'order' => 2,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 3,
                'parent_menu_id' => 0,
                'module' => 'admin',
                'label' => 'administration',
                'text' => 'Administration',
                'link' => '#',
                'icon' => 'fa fa-user-circle',
                'order' => 3,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 4,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'roles',
                'text' => 'Roles',
                'link' => '/roles',
                'icon' => 'fa fa-user-circle',
                'order' => 1,
                'subscription' => 1,
                'status' => 1
        ),
            array(
                'id'=> 5,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'departments',
                'text' => 'Departments',
                'link' => '/departments',
                'icon' => 'fa fa-user-circle',
                'order' => 2,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 6,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'users',
                'text' => 'Users',
                'link' => '/users',
                'icon' => 'fa fa-user-circle',
                'order' => 3,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 7,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'teams',
                'text' => 'Teams',
                'link' => '/teams',
                'icon' => 'fa fa-user-circle',
                'order' => 4,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 8,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'email templates',
                'text' => 'Email Templates',
                'link' => '/email-templates',
                'icon' => 'fa fa-envelope-o',
                'order' => 5,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 9,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'holidays',
                'text' => 'Holidays',
                'link' => '/holidays',
                'icon' => 'fa fa-calendar-plus-o',
                'order' => 6,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 10,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'meetings',
                'text' => 'Meetings',
                'link' => '/meetings',
                'icon' => 'fa fa-briefcase',
                'order' => 7,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 11,
                'parent_menu_id' => 3,
                'module' => 'admin',
                'label' => 'clients',
                'text' => 'Clients',
                'link' => '/clients',
                'icon' => 'fa fa-user-circle',
                'order' => 8,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 12,
                'parent_menu_id' => 0,
                'module' => 'utilities',
                'label' => 'todos',
                'text' => 'Todos',
                'link' => '/todos',
                'icon' => 'fa fa-list-ul',
                'order' => 4,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 13,
                'parent_menu_id' => 0,
                'module' => 'utilities',
                'label' => 'announcements',
                'text' => 'Announcements',
                'link' => '/announcements',
                'icon' => 'fa fa-announcements',
                'order' => 5,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 14,
                'parent_menu_id' => 0,
                'module' => 'utilities',
                'label' => 'mailbox',
                'text' => 'Mailbox',
                'link' => '/mailbox',
                'icon' => 'fa fa-envelope-o',
                'order' => 6,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 15,
                'parent_menu_id' => 0,
                'module' => 'filemanager',
                'label' => 'file manager',
                'text' => 'File Manager',
                'link' => '/file-browser',
                'icon' => 'fa fa-folder',
                'order' => 7,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 16,
                'parent_menu_id' => 0,
                'module' => 'setting',
                'label' => 'settings',
                'text' => 'Settings',
                'link' => '/settings',
                'icon' => 'fa fa-cogs',
                'order' => 8,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 17,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'company detail',
                'text' => 'Company Detail',
                'link' => '#',
                'icon' => 'fa fa-info-circle',
                'order' => 1,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 18,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'email settings',
                'text' => 'Email Settings',
                'link' => '#',
                'icon' => 'fa fa-envelope',
                'order' => 2,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 19,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'email Templates',
                'text' => 'Email Templates',
                'link' => '#',
                'icon' => 'fa fa-pencil-square',
                'order' => 3,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 20,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'email notifications',
                'text' => 'Email Notifications',
                'link' => '#',
                'icon' => 'fa fa-bell-o',
                'order' => 4,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 21,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'cronjob',
                'text' => 'Cronjob',
                'link' => '#',
                'icon' => 'fa fa-contao',
                'order' => 5,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 22,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'menu allocation',
                'text' => 'Menu Allocation',
                'link' => '#',
                'icon' => 'fa fa-language',
                'order' => 6,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 23,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'theme settings',
                'text' => 'Theme Settings',
                'link' => '#',
                'icon' => 'fa fa-compass',
                'order' => 7,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 24,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'dashboard settings',
                'text' => 'Dashboard Settings',
                'link' => '#',
                'icon' => 'fa fa-cog',
                'order' => 10,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 25,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'system settings',
                'text' => 'System Settings',
                'link' => '#',
                'icon' => 'fa fa-desktop',
                'order' => 11,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 26,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'system update',
                'text' => 'System Update',
                'link' => '#',
                'icon' => 'fa fa-repeat',
                'order' => 12,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 27,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'backup database',
                'text' => 'Backup Database',
                'link' => '#',
                'icon' => 'fa fa-database',
                'order' => 13,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 28,
                'parent_menu_id' => 16,
                'module' => 'settings',
                'label' => 'custom fields',
                'text' => 'Custom Fields',
                'link' => '#',
                'icon' => 'fa fa-list-alt',
                'order' => 14,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 41,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'project management',
                'text' => 'Project Management',
                'link' => '#',
                'icon' => 'fa fa-product-hunt',
                'order' => 9,
                'subscription' => 1,
                'status' => 0
            ),
            array(
                'id'=> 42,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'project planner',
                'text' => 'Project Planner',
                'link' => '/projects-planner',
                'icon' => 'fa fa-american-sign-language-interpreting',
                'order' => 10,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 43,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'projects',
                'text' => 'Projects',
                'link' => '/projects',
                'icon' => 'fa fa-product-hunt',
                'order' => 11,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 44,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'tasks',
                'text' => 'Tasks',
                'link' => '/tasks',
                'icon' => 'fa fa-tasks',
                'order' => 12,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 45,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'task board',
                'text' => 'Task Board',
                'link' => '/taskboard',
                'icon' => 'fa fa-clipboard',
                'order' => 13,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 46,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'defects',
                'text' => 'Defects',
                'link' => '/defects',
                'icon' => 'fa fa-bug',
                'order' => 14,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 47,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'incidents',
                'text' => 'Incidents',
                'link' => '/incidents',
                'icon' => 'fa fa-ticket',
                'order' => 15,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 48,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'release planner',
                'text' => 'Release Planner',
                'link' => '/release-planner',
                'icon' => 'fa fa-paper-plane-o',
                'order' => 16,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 49,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'knowledge base',
                'text' => 'Knowledge Base',
                'link' => '/knowledgebase',
                'icon' => 'fa fa-graduation-cap',
                'order' => 17,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 50,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'team boards',
                'text' => 'Team Boards',
                'link' => '/teamboard',
                'icon' => 'fa fa-universal-access',
                'order' => 18,
                'subscription' => 1,
                'status' => 1
            ),
            array(
                'id'=> 51,
                'parent_menu_id' => 0,
                'module' => 'pm',
                'label' => 'reports',
                'text' => 'Reports',
                'link' => '/reports',
                'icon' => 'fa fa-bar-chart',
                'order' => 19,
                'subscription' => 1,
                'status' => 1
            )
        );

        Menu::insert($data);
    }
}

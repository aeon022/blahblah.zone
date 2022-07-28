<?php

namespace Modules\User\Database\Seeders;

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
 * Class UserDatabaseSeeder
 *
 * The Migrations is Defined for User Database Seeder.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\User
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // --
        // Roles table.
        Role::whereIn('id', [1,2,3])->delete();
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'User has access to all system functionality.',
                'status' => 1
            ],
            [
                'id' => 2,
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff role.',
                'status' => 1
            ],
            [
                'id' => 3,
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Client role.',
                'status' => 1
            ]
        ];
        Role::insert($roles);

        // --
        // Departments table.
        $department = Department::find(1);
        if ($department) {
            $department->forceDelete();
        }
        $department = new Department();
        $department->id = 1;
        $department->name = 'Administration';
        $department->save();

        // --
        // Attach/Detach roles.
        $department->roles()->sync([1, 2, 3]);

        // --
        // Role department menu tables.
        $menus = Menu::get();
        if (!empty($menus)) {
            foreach ($menus as $key => $value) {
                $departmentRoleMenu = new DepartmentRoleMenu();
                $departmentRoleMenu->department_id = 1;
                $departmentRoleMenu->role_id = 1;
                $departmentRoleMenu->menu_id = $value->id;
                $departmentRoleMenu->view = $value->id;
                $departmentRoleMenu->created = $value->id;
                $departmentRoleMenu->edited = $value->id;
                $departmentRoleMenu->deleted = $value->id;
                $departmentRoleMenu->save();

                $departmentRoleMenu1 = new DepartmentRoleMenu();
                $departmentRoleMenu1->department_id = 1;
                $departmentRoleMenu1->role_id = 2;
                $departmentRoleMenu1->menu_id = $value->id;
                $departmentRoleMenu1->view = $value->id;
                $departmentRoleMenu1->created = 0;
                $departmentRoleMenu1->edited = 0;
                $departmentRoleMenu1->deleted = 0;
                $departmentRoleMenu1->save();

                $departmentRoleMenu2 = new DepartmentRoleMenu();
                $departmentRoleMenu2->department_id = 1;
                $departmentRoleMenu2->role_id = 3;
                $departmentRoleMenu2->menu_id = $value->id;
                $departmentRoleMenu2->view = $value->id;
                $departmentRoleMenu2->created = 0;
                $departmentRoleMenu2->edited = 0;
                $departmentRoleMenu2->deleted = 0;
                $departmentRoleMenu2->save();
            }
        }

        // --
        // User table.
        $user = User::find(1);
        if ($user) {
            $user->forceDelete();
        }
        $user = new User();
        $user->id = 1;
        $user->user_generated_id = "USR0001";
        $user->firstname = 'Vipul';
        $user->lastname = 'Patel';
        $user->username = 'vipspatel';
        $user->email = 'vipul@chetsapp.com';
        $user->password = 'Vipul@123!';
        $user->is_super_admin = 1;
        $user->is_active = 1;
        $user->email_verified = 1;
        $user->emp_id = 'EMP001';
        $user->permission = 'all';

        if ($user->save()) {
            $departmentRoleUsers1 = new DepartmentRoleUser();
            $departmentRoleUsers1->department_id = 1;
            $departmentRoleUsers1->role_id = 1;
            $departmentRoleUsers1->user_id = $user->id;
            $departmentRoleUsers1->save();

            $departmentRoleUsers2 = new DepartmentRoleUser();
            $departmentRoleUsers2->department_id = 1;
            $departmentRoleUsers2->role_id = 2;
            $departmentRoleUsers2->user_id = $user->id;
            $departmentRoleUsers2->save();
        }
    }
}

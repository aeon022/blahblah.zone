<?php

namespace Modules\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Modules\Installer\Helpers\InstalledFileManager;
use Modules\Setting\Models\Setting;
use Modules\User\Models\Department\DepartmentRoleUser;
use Modules\User\Models\User\User;
use Modules\User\Repositories\UserRepository;
use Validator;

/**
 * Class SitesController
 *
 * Sites create super admin user.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Installer
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @author    Another Author <another@example.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class SitesController extends Controller
{
    protected $userRepo;

    /**
     * Create a new SitesController instance.
     *
     * @param UserRepository $userRepo [Object]
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Display site form.
     *
     * @return view
     */
    public function getSiteForm()
    {
        \View::addLocation(base_path().'/Modules/Installer/Views');
        \View::addNamespace('theme', base_path().'/Modules/Installer/Views');
        return \View::make('theme::sites');
    }

    /**
     * Save sites info.
     *
     * @param InstalledFileManager $fileManager [Object]
     * @param Request              $request     [Object]
     *
     * @return Response
     */
    public function saveSites(InstalledFileManager $fileManager, Request $request)
    {
        $configs = getConfigs();
        $input = $request->all();
        $rules = $configs['sites']['form']['rules'];
        $validator = Validator::make($request->all(), $rules);

        // --
        // Check validations
        if ($validator->fails()) {
            $errors = $validator->errors();

            \View::addLocation(base_path().'/Modules/Installer/Views');
            \View::addNamespace('theme', base_path().'/Modules/Installer/Views');
            return view('theme::sites', compact('errors'));
        }

        $user = User::where('email', $input['email'])
            ->first();
        if (!empty($user)) {
            $finalStatusMessage = $fileManager->update();
            return redirect('/');
        } else {
            $user = new User();
            $user->emp_id = 'EMP000001';
            $user->username = $input['username'];
            $user->firstname = ucwords($input['firstname']);
            $user->lastname = ucwords($input['lastname']);
            $user->email = $input['email'];
            $user->password = $input['password'];
            $user->is_super_admin = 1;
            $user->is_active = 1;
            $user->email_verified = 1;
            $user->permission = 'all';
            $user->user_generated_id = $this->userRepo->getUserGeneratedId();

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
                
                // --
                // Save settings
                $setting = Setting::first();
                if (!$setting) {
                    $setting = new Setting;
                }
                $setting->id = 1;
                $setting->company_name = env('APP_NAME');
                $setting->company_from_email = env('MAIL_FROM_ADDRESS');
                $setting->smtp_protocol = env('MAIL_DRIVER');
                $setting->smtp_host = env('MAIL_HOST');
                $setting->smtp_user = env('MAIL_USERNAME');
                $setting->smtp_password = env('MAIL_PASSWORD');
                $setting->smtp_port = env('MAIL_PORT');
                $setting->smtp_encryption = env('MAIL_ENCRYPTION');
                $setting->active_cronjob = true;
                $setting->tables_pagination_limit = 10;
                $setting->date_format = 'YYYY-MM-DD';
                $setting->save();

                $finalStatusMessage = $fileManager->update();
                return redirect('/');
            }
        }
    }

    /**
     * Set env variable.
     *
     * @param Array $input [Input array]
     *
     * @return Boolean
     */
    private function setEnvVariables($input)
    {
        $envPath = base_path('.env');

        // --
        // Set env variables
        if (file_exists($envPath)) {
            // --
            // App name
            if (is_bool(env('APP_NAME'))) {
                $old = env('APP_NAME')? 'true' : 'false';
            } elseif (env('APP_NAME') === null) {
                $old = 'null';
            } else {
                $old = env('APP_NAME');
            }

            if (isset($input['site_name'])) {
                file_put_contents(
                    $envPath,
                    str_replace(
                        "APP_NAME=".$old,
                        "APP_NAME=". $input['site_name'],
                        file_get_contents($envPath)
                    )
                );
            }

            // --
            // App email
            if (is_bool(env('SITE_EMAIL'))) {
                $old = env('SITE_EMAIL')? 'true' : 'false';
            } elseif (env('SITE_EMAIL') === null) {
                $old = 'null';
            } else {
                $old = env('SITE_EMAIL');
            }

            if (isset($input['site_email'])) {
                file_put_contents(
                    $envPath,
                    str_replace(
                        "SITE_EMAIL=".$old,
                        "SITE_EMAIL=". $input['site_email'],
                        file_get_contents($envPath)
                    )
                );
            }

            // --
            // Mail from address
            if (is_bool(env('MAIL_FROM'))) {
                $old = env('MAIL_FROM')? 'true' : 'false';
            } elseif (env('MAIL_FROM') === null) {
                $old = 'null';
            } else {
                $old = env('MAIL_FROM');
            }

            if (isset($input['email'])) {
                file_put_contents(
                    $envPath,
                    str_replace(
                        "MAIL_FROM=".$old,
                        "MAIL_FROM=". $input['email'],
                        file_get_contents($envPath)
                    )
                );
            }

            // --
            // Mail from name
            if (is_bool(env('MAIL_FROM_NAME'))) {
                $old = env('MAIL_FROM_NAME')? 'true' : 'false';
            } elseif (env('MAIL_FROM_NAME') === null) {
                $old = 'null';
            } else {
                $old = env('MAIL_FROM_NAME');
            }

            if (isset($input['name'])) {
                file_put_contents(
                    $envPath,
                    str_replace(
                        "MAIL_FROM_NAME=".$old,
                        "MAIL_FROM_NAME=". $input['name'],
                        file_get_contents($envPath)
                    )
                );
            }
        }
        return true;
    }
}

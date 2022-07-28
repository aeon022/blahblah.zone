<?php

namespace Modules\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;
use Modules\Installer\Helpers\EnvironmentManager;
use Validator;

/**
 * Class EnvironmentController
 *
 * Environment functionality.
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
class EnvironmentController extends Controller
{
    protected $EnvironmentManager;

    /**
     * Instantiate a new controller instance.
     *
     * @param EnvironmentManager $environmentManager [Object]
     *
     * @return void
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the Environment page.
     *
     * @return View
     */
    public function environmentWizard()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();
        $bug_error = '';
        \View::addLocation(base_path().'/Modules/Installer/Views');
        \View::addNamespace('theme', base_path().'/Modules/Installer/Views');
        return \View::make(
            'theme::environment-wizard',
            compact('envConfig', 'bug_error')
        );
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param Request    $request  [Request for save wizard]
     * @param Redirector $redirect [Redirector object]
     *
     * @return RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {
        $configs = getConfigs();
        $bug_error = '';
        $messages = [
            'environment_custom.required_if' => trans(
                'installer_messages.environment.wizard.form.name_required'
            ),
        ];

        $validator = Validator::make(
            $request->all(),
            $configs['environment']['form']['rules'],
            $messages
        );

        if ($validator->fails()) {
            $errors = $validator->errors();
            $request = $request->input();

            \View::addLocation(base_path().'/Modules/Installer/Views');
            \View::addNamespace('theme', base_path().'/Modules/Installer/Views');

            return \View::make(
                'theme::environment-wizard',
                compact('errors', 'envConfig', 'bug_error','request')
            );
        }
        
        // --
        // Check datatbase connections
        $link = @mysqli_connect(
            $request->database_hostname,
            $request->database_username,
            $request->database_password,
            $request->database_name
        );
        if (!$link) {
            $bug_error .= "Error: Unable to connect to MySQL." . PHP_EOL;
            $bug_error .= "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            $bug_error .= "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            \View::addLocation(base_path().'/Modules/Installer/Views');
            \View::addNamespace('theme', base_path().'/Modules/Installer/Views');
            return \View::make('theme::environment-wizard', compact('bug_error'));
        }
        $results = $this->EnvironmentManager->saveFileWizard($request);

        // --
        // Replace front URL
        $file_name = 'main.3e70567d8b8ccf5feaf0.js';
        $filepath = base_path().'/public/vendor/'.$file_name;
        $old_string = 'http://chetsapp.de:8896';
        $new_string = $request->app_url;
        if (\File::exists($filepath)) {
            $fileContents = file_get_contents(base_path().'/public/vendor/'.$file_name);
            $fileContents = str_replace("$old_string", "$new_string", $fileContents);
            file_put_contents($filepath, $fileContents);
        }

        return $redirect->route('LaravelInstaller::database')
            ->with(['results' => $results]);
    }
}

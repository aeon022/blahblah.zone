<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Setting\Repositories\SettingRepository;
use Modules\Setting\Http\Requests\CreateUpdateSettingRequest;
use Modules\Helper\Helpers\AdminHelper;

/**
 * Class SettingController
 *
 * Setting create, update and view.
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
class SettingController extends Controller
{
    protected $settingRepo;

    /**
     * Instantiate a new controller instance.
     *
     * @param SettingRepository $setting [Object]
     *
     * @return void
     */
    public function __construct(SettingRepository $setting)
    {
        $this->settingRepo = $setting;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->settingRepo->findAll();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUpdateSettingRequest $request [Request for create setting]
     *
     * @return Response
     */
    public function store(CreateUpdateSettingRequest $request)
    {
        // --
        // Check role/permission
        $isAlllow = false;
        if (AdminHelper::can_action(17, 'created')  
            || AdminHelper::can_action(17, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(18, 'created')  
            || AdminHelper::can_action(18, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(20, 'created')  
            || AdminHelper::can_action(20, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(21, 'created')  
            || AdminHelper::can_action(21, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(23, 'created')  
            || AdminHelper::can_action(23, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(26, 'created')  
            || AdminHelper::can_action(26, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(27, 'created')  
            || AdminHelper::can_action(27, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(28, 'created')  
            || AdminHelper::can_action(28, 'edited')
        ) {
            $isAlllow = true;
        }
        if (AdminHelper::can_action(29, 'created')  
            || AdminHelper::can_action(29, 'edited')
        ) {
            $isAlllow = true;
        }

        if ($isAlllow) {
            if ($this->settingRepo->create($request)) {
                return response()->json('success', 200);
            } else {
                return response()->json(
                    ['error'],
                    400
                );
            }
        } else {
            return response()->json("Access denied", 403);
        }

    }
}

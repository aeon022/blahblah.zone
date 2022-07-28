<?php

namespace Modules\Helper\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Helper\Repositories\PmHelperRepository;
use Modules\Projects\Models\Project;
use Modules\User\Models\User\User;

/**
 * Class PmHelperController
 *
 * PM Helper functions
 *
 * PHP version 7.1.3
 *
 * @category  Helper
 * @package   Modules\Helper
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class PmHelperController extends Controller
{
    protected $pmHelperRepo;

    /**
     * Instantiate a new controller instance.
     *
     * @param HelperRepository $helper [Object]
     *
     * @return void
     */
    public function __construct(PmHelperRepository $helper)
    {
        $this->pmHelperRepo = $helper;
    }

    /**
     * Get project,task,defect,incident and client total count.
     *
     * @return Response
     */
    public function getCountForDashboard()
    {
        return $this->pmHelperRepo->getCountForDashboard();
    }

    /**
     * Get user project/task/meeting timecard.
     *
     * @param Int  $user_id [User id]
     * @param Date $date    [Timecard report month]
     *
     * @return Response
     */
    public function getTimeCardReports($user_id, $date)
    {
        return $this->pmHelperRepo->getTimeCardReports($user_id, $date);
    }

    /**
     * Get user project/task/meeting count.
     *
     * @param Int $user_id [User id]
     *
     * @return Response
     */
    public function getUserWiseCountForUserDetail($user_id)
    {
        return $this->pmHelperRepo->getUserWiseCountForUserDetail($user_id);
    }

    /**
     * Check user permission.
     *
     * @param Int    $id     [Project Id]
     * @param String $action [Action Name]
     *
     * @return void
     */
    public static function canUserAction($id, $action)
    {
        $data = Project::checkPermission($id, Auth::user()->id, $action)
            ->where('id', $id)
            ->firstOrFail();

        if ($data->users->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }
}

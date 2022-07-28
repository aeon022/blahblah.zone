<?php

namespace Modules\EmailGroup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\EmailGroup\Repositories\EmailGroupRepository;
use Modules\EmailGroup\Http\Requests\CreateEmailGroupRequest;
use Modules\EmailGroup\Http\Requests\UpdateEmailGroupRequest;
use Modules\Helper\Helpers\AdminHelper;

/**
 * Class EmailGroupController
 *
 * EmailGroup create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\EmailGroup
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class EmailGroupController extends Controller
{
    protected $emailgroupRepo;

    /**
     * Instantiate a new controller instance.
     *
     * @param EmailGroupRepository $emailgroup [Object]
     *
     * @return void
     */
    public function __construct(EmailGroupRepository $emailgroup)
    {
        $this->emailgroupRepo = $emailgroup;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->emailgroupRepo->findAll();
    }

    /**
     * Show all emailgroup name from helper function.
     *
     * @return Array
     */
    public function getGroup()
    {
        return $this->emailgroupRepo->getEmailGroup();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateEmailGroupRequest $request [Request for create email group]
     *
     * @return Response
     */
    public function store(CreateEmailGroupRequest $request)
    {
        // --
        // Check role/permission
        if (!AdminHelper::can_action(8, 'created')) {
            return response()->json("Access denied", 403);
        }

        if ($this->emailgroupRepo->create($request)) {
            return response()->json('success');
        } else {
            return response()->json(
                ['error' => 'Email group has not been created.'],
                400
            );
        }
    }

    /**
     * Show the specified resource.
     *
     * @param int $id [Row id]
     *
     * @return Response
     */
    public function show($id)
    {
        // --
        // Check role/permission
        if (!AdminHelper::can_action(8, 'view')) {
            return response()->json("Access denied", 403);
        }

        return $this->emailgroupRepo->getById($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmailGroupRequest $request [Request for update email group]
     * @param int                     $id      [Row id]
     *
     * @return Response
     */
    public function update(UpdateEmailGroupRequest $request, $id)
    {
        // --
        // Check role/permission
        if (!AdminHelper::can_action(8, 'edited')) {
            return response()->json("Access denied", 403);
        }

        if ($this->emailgroupRepo->update($request, $id)) {
            return response()->json("success");
        } else {
            return response()->json(
                ['error' => 'Email group has not been found.'],
                400
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for destroy email group]
     * @param int     $id      [Row id]
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // --
        // Check role/permission
        if (!AdminHelper::can_action(8, 'deleted')) {
            return response()->json("Access denied", 403);
        }

        if ($this->emailgroupRepo->delete($request, $id)) {
            return response()->json('success');
        } else {
            return response()->json(
                ['error' => 'Email group has not been found.'],
                400
            );
        }
    }
}

<?php

namespace Modules\EmailGroup\Repositories;

use Modules\EmailGroup\Models\EmailGroup;

/**
 * Class EmailGroupRepository
 *
 * Email Group insert, update, delete and view.
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
class EmailGroupRepository
{
    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        return EmailGroup::with('templates')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function getEmailGroup()
    {
        return EmailGroup::pluck('email_group_name', 'id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create email group]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $emailgroup = new EmailGroup;
        $emailgroup->fill($input);
        if ($emailgroup->save()) {
            //--
            // Add activities
            createUserActivity(
                EmailGroup::MODULE_NAME,
                $emailgroup->id,
                $request->method(),
                $emailgroup->email_group_name,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update email group]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function update($request, $id)
    {
        $input = $request->all();
        $emailgroup = EmailGroup::findOrFail($id);
        if ($emailgroup->fill($input)->save()) {
            //--
            // Add activities
            createUserActivity(
                EmailGroup::MODULE_NAME,
                $emailgroup->id,
                $request->method(),
                $emailgroup->email_group_name,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for delete email group]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $emailgroup = EmailGroup::findOrFail($id);
        if ($emailgroup->delete()) {
            //--
            // Add activities
            createUserActivity(
                EmailGroup::MODULE_NAME,
                $emailgroup->id,
                $request->method(),
                $emailgroup->email_group_name,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Get the specified resource from storage
     *
     * @param Int $id [Row id]
     *
     * @return Object
     */
    public function getById($id)
    {
        return EmailGroup::findOrFail($id);
    }
}

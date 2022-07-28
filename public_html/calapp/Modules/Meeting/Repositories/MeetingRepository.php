<?php

namespace Modules\Meeting\Repositories;

use Modules\Meeting\Models\Meeting;
use Modules\Helper\Helpers\EmailsHelper;

use Auth;

/**
 * Class MeetingController
 *
 * Meeting create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Meeting
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @author    Another Author <another@example.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class MeetingRepository
{
    protected $emailsHelper;

    /**
     * Instantiate a new reposiroty instance.
     *
     * @param EmailsHelper $emailsHelper [Object]
     *
     * @return void
     */
    public function __construct(EmailsHelper $emailsHelper)
    {
        $this->emailsHelper = $emailsHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        return Meeting::with('user', 'members')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Request for get meeting]
     *
     * @return Array
     */
    public function getAllMeetings($request)
    {
        $users_table = config('core.acl.users_table');
        $meetings_table = config('core.acl.meetings');
        
        $totalData = Meeting::with('members')->count();

        $columns = array(
            0 =>'title',
            1 =>'firstname',
            3 =>'start_date',
            4=> 'end_date'
        );
        
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $meetings = Meeting::with(
            ['members' => function ($query) {
                $query->select(
                    config('core.acl.users_table').'.id as member_id',
                    config('core.acl.users_table').'.firstname',
                    config('core.acl.users_table').'.lastname',
                    config('core.acl.users_table').'.avatar'
                );
            }]
        )
        ->join($users_table.' as user', 'user.id', '=', $meetings_table.'.organizer_id')
        ->select(
            config('core.acl.meetings').'.*',
            'user.firstname',
            'user.lastname',
            'user.avatar'
        );

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            
            $meetings = $meetings->where('title', 'LIKE', "%{$search}%")
                ->orWhere('firstname', 'LIKE', "%{$search}%")
                ->orWhere('lastname', 'LIKE', "%{$search}%")
                ->orWhere('start_date', 'LIKE', "%{$search}%")
                ->orWhere('end_date', 'LIKE', "%{$search}%");

            $totalFiltered = $meetings->count();
        }

        $meetings = $meetings->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $meetings
        );

        return $json_data;
    }

    /**
     * Display the specified resource in storage.
     *
     * @param Int $id [Row id]
     *
     * @return Object
     */
    public function findById($id)
    {
        return Meeting::with('user', 'members')->where('id', $id)->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create meeting]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $logginUser = Auth::user();
        $meeting = new Meeting;
        $meeting->fill($input);
        $meeting->organizer_id = $logginUser->id;

        if ($meeting->save()) {
            $meeting->members()->sync($input['members']);

            // --
            // Send mail
            if (isset($input['members'])) {
                array_push($input['members'], $logginUser->id);

                try {
                    $this->emailsHelper->sendMeetingEmails(
                        $input['members'],
                        $logginUser,
                        $meeting
                    );
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
            // --
            // Add Activity
            createUserActivity(
                Meeting::MODULE_NAME,
                $meeting->id,
                $request->method(),
                $meeting->title,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update meeting]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function update($request, $id)
    {
        $logginUser = Auth::user();
        $input = $request->all();
        $meeting = Meeting::find($id);
        $meeting->fill($input);

        if ($meeting->save()) {
            $meeting->members()->sync($input['members']);

            // --
            // Send mail
            if (isset($input['members'])) {
                array_push($input['members'], $logginUser->id);

                try {
                    $this->emailsHelper->sendMeetingEmails(
                        $input['members'],
                        $logginUser,
                        $meeting
                    );
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
            // --
            // Add Activity
            createUserActivity(
                Meeting::MODULE_NAME,
                $meeting->id,
                $request->method(),
                $meeting->title,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for delete meeting]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        if (!empty($meeting)) {
            $meeting->members()->sync(array());
            $meeting->delete();

            // --
            // Add activity
            createUserActivity(
                Meeting::MODULE_NAME,
                $meeting->id,
                $request->method(),
                $meeting->title,
                $request->ip()
            );
            return true;
        }

        return false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Params for list meeting]
     *
     * @return Object
     */
    public function getDashboardMeeting($request)
    {
        $matchThese = [['start_date','>=',date('Y-m-d')]];
        $user = Auth::user();
        $meetings = $user->meetings()->where($matchThese);
        if ($request->has('length')) {
            $meetings = $meetings->take($request->get('length'));
        }
        return $meetings->get();
    }
}

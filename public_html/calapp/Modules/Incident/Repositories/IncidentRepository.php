<?php

namespace Modules\Incident\Repositories;

use Modules\User\Models\User\User;
use Modules\Team\Models\Team;
use Modules\Projects\Models\Project;
use Modules\Incident\Models\Incident;
use Modules\Incident\Models\IncidentUser;
use Modules\Incident\Models\IncidentHistory;
use Modules\CustomField\Models\CustomField;
use Modules\Helper\Repositories\HelperRepository;
use Modules\Helper\Helpers\EmailsHelper;

use Auth;
use DB;

/**
 * Class IncidentRepository
 *
 * Incident create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Incident
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class IncidentRepository
{
    protected $helperRepo;
    protected $emailsHelper;

    /**
     * Instantiate a new reposiroty instance.
     *
     * @param HelperRepository $helperRepo   [Object]
     * @param EmailsHelper     $emailsHelper [Object]
     *
     * @return void
     */
    public function __construct(
        HelperRepository $helperRepo,
        EmailsHelper $emailsHelper
    ) {
        $this->helperRepo = $helperRepo;
        $this->emailsHelper = $emailsHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        $user = Auth::user();
        return $user->incidents()
            ->with(
                ['users' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                }]
            )
            ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Request for get incidents]
     *
     * @return Object
     */
    public function getAllIncidents($request)
    {
        $incident_table = config('core.acl.incidents_table');
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $client_table = config('core.acl.clients_table');
        $user = Auth::user();

        if ($request->get('isUserProfile') && $request->has('user_id')) {
            $incident = Incident::where($incident_table.'.assign_to', $request->get('user_id'));
            $statusCount = [];
        } elseif($request->has('filter') && $request->get('filter') === "selected") {
            $incident = Incident::where($incident_table.'.assign_to', $user->id);
            $statusCount = $this->_getAllIncidentCount(true);
        } else {
            $incident =  $user->incidents();
            $statusCount = $this->_getAllIncidentCount();
        }

        $columns = array(
            0 => $incident_table.'.incident_name',
            1 => $incident_table.'.generated_id',
            2 => $incident_table.'.priority',
            3 => 'incident_created.firstname',
            4 => 'incident_assigned.firstname',
            5 => $incident_table.'.status'
        );

        $status = $request->get('status');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $incident =  $incident->select(
            $incident_table.'.*',
            $project_table.'.project_name',
            $client_table.'.company_name',
            $user_table.'.firstname as client_firstname',
            $user_table.'.lastname as client_lastname',
            'incident_created.firstname as created_firstname',
            'incident_created.lastname as created_lastname',
            'incident_assigned.firstname as assigned_firstname',
            'incident_assigned.lastname as assigned_lastname',
            DB::raw("CONCAT(incident_assigned.firstname,' ',incident_assigned.lastname) as assign_name")
        )
            ->leftjoin($project_table, $project_table.'.id', '=', $incident_table.'.project_id')
            ->leftjoin($user_table, $user_table.'.id', '=', $project_table.'.client_id')
            ->leftjoin($client_table, $client_table.'.user_id', '=', $user_table.'.id')
            ->join($user_table.' as incident_created', 'incident_created.id', '=', $incident_table.'.create_user_id')
            ->leftjoin($user_table.' as incident_assigned', 'incident_assigned.id', '=', $incident_table.'.assign_to');

        if ($status) {
            $incident = $incident->where($incident_table.'.status', $status);
        }

        $totalData = $incident->count();
        $totalFiltered = $totalData;

        // --
        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $incident = $incident->where(
                function ($query) use ($search, $incident_table, $user_table, $client_table, $project_table) {
                    $query->where($incident_table.'.incident_name', 'LIKE', "%{$search}%")
                        ->orWhere($incident_table.'.generated_id', 'LIKE', "%{$search}%")
                        ->orWhere($incident_table.'.priority', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('concat(incident_created.firstname," ",incident_created.lastname)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('concat(incident_assigned.firstname," ",incident_assigned.lastname)'), 'LIKE', "%{$search}%")
                        ->orWhere($incident_table.'.status', 'LIKE', "%{$search}%");
                }
            );

            $totalFiltered = $incident->count();
        }

        $data = $incident->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "statusCount"     => $statusCount,
            "data"            => $data
        );

        return $json_data;
    }

    /**
     * Get all incident count.
     *
     * @param Boolean  $isMy [For all incident or user incident]
     *
     * @return Array
     */
    private function _getAllIncidentCount($isMy = false)
    {
        $user = Auth::user();
        $result['all'] =  $this->_getStatusWiseCount([1,2,3,4,5,6,7], $user, $isMy);
        if ($result['all'] > 0) {
            $result['open'] = $this->_getStatusWiseCount([1], $user, $isMy);
            $result['assigned'] = $this->_getStatusWiseCount([2], $user, $isMy);
            $result['in_progress'] = $this->_getStatusWiseCount([3], $user, $isMy);
            $result['solved'] = $this->_getStatusWiseCount([4], $user, $isMy);
            $result['deferred'] = $this->_getStatusWiseCount([5], $user, $isMy);
            $result['re_open'] = $this->_getStatusWiseCount([6], $user, $isMy);
            $result['closed'] = $this->_getStatusWiseCount([7], $user, $isMy);
        }
        return $result;
    }

    /**
     * Incident status wise counting.
     *
     * @param Int    $status [Status id]
     * @param object $user   [User object]
     * @param Boolean $isMy  [For all incident or user incident]
     *
     * @return Count
     */
    private function _getStatusWiseCount($status, $user, $isMy)
    {
        if ($isMy) {
            return Incident::where('assign_to', $user->id)
                ->whereIn('status', $status)
                ->count();
        } else {
            return $user->incidents()
                ->whereIn('status', $status)
                ->count();
        }
    }

    /**
     * Display the specified resource in storage.
     *
     * @param Int $id [Row id]
     *
     * @return Object
     */
    public function findByIdWithHistory($id)
    {
        $incident = Incident::with(
            [
                'project' => function ($query) {
                    $query->select('id', 'project_name', 'project_version');
                },
                'comments.user' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                } ,
                'createdUser' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                },
                'assignUser' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                },
                'attachments',
                'history',
                'users' => function ($query) {
                    $query->select('id', 'firstname', 'lastname', 'email');
                }]
        )
            ->where('id', $id)
            ->first();
        
        if ($incident) {
            $incident['custom_fields'] = CustomField::getViewFields(4)->get();
            return $incident;
        }
        return false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create incidents]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $project = Project::find($input['project_id']);
        $incident = new Incident;
        $user = Auth::user();
        $input['create_user_id'] = $user->id;
        if ($input['assign_to'] == null) {
            $input['assign_to'] = 'Unassign';
        }
        if ($incident->fill($input)->save()) {
            $userIds = [];
            $teamData = Team::with(
                ['members' => function ($query) {
                    $query->select(config('core.acl.users_table').'.id');
                }]
            )
            ->where('id', $input['assigned_group_id'])
            ->first();
            foreach ($teamData->members as $value) {
                $userIds[] = $value->id;
            }

            $super_admin_ids = User::where('is_super_admin', 1)
                ->pluck('id')
                ->toArray();
            $userIds = array_merge($userIds, $super_admin_ids);
            array_push($userIds, $user->id); // login user
            if($project){
                array_push($userIds, $project->client_id); // client
            }
            $uniqueUserId = array_unique($userIds);

            $incident->users()->sync($uniqueUserId);
            // --
            // Save custom field.
            if (isset($input['custom_fields'])) {
                $this->helperRepo->saveCustomField(
                    4,
                    $incident->id,
                    $input['custom_fields']
                );
            }
            // --
            // Create user activity.
            createUserActivity(
                Incident::MODULE_NAME,
                $incident->id,
                $request->method(),
                $input['incident_name'],
                $request->ip()
            );
            // --
            // Create incident history.
            $history = $this->_createHistory(
                $input['incident_name'],
                $user->id,
                'create',
                $user->id,
                $input['status']
            );
            $incident->history()->save($history);
            // --
            // Send mail.
            if ($project) {
                $uniqueUserId = array_diff( $uniqueUserId, [$project->client_id] ); // Unset client
            }
            $this->_sendMailEveryone(
                $uniqueUserId,
                $user->firstname.' '.$user->lastname,
                $incident->id,
                $input['incident_name'],
                'create',
                $input['incident_type']
            );

            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update incidents]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function update($request, $id)
    {
        $input = $request->all();
        $incident = Incident::find($id);
        $project = Project::find($input['project_id']);
        $user = Auth::user();
        if ($input['assign_to'] == null) {
            $input['assign_to'] = 'Unassign';
        }
        if ($incident->fill($input)->save()) {
            $userIds = [];
            $teamData = Team::with(
                ['members' => function ($query) {
                    $query->select(config('core.acl.users_table').'.id');
                }]
            )
            ->where('id', $input['assigned_group_id'])
            ->first();
            foreach ($teamData->members as $value) {
                $userIds[] = $value->id;
            }

            $super_admin_ids = User::where('is_super_admin', 1)
                ->pluck('id')
                ->toArray();
            $userIds = array_merge($userIds, $super_admin_ids);
            array_push($userIds, $user->id); // login user
            if($project){
                array_push($userIds, $project->client_id); // client
            }
            $uniqueUserId = array_unique($userIds);
            // --
            // Sync user.
            $incident->users()->sync($uniqueUserId);
            // --
            // Save custom field
            if (isset($input['custom_fields'])) {
                $this->helperRepo->saveCustomField(4, $id, $input['custom_fields']);
            }
            // --
            // Create history.
            $history = $this->_createHistory(
                $input['incident_name'],
                $user->id,
                'edit',
                $incident->create_user_id,
                $incident->status
            );
            $incident->history()->save($history);
            // --
            // Create user activity.
            createUserActivity(
                Incident::MODULE_NAME,
                $id,
                $request->method(),
                $input['incident_name'],
                $request->ip()
            );
            // --
            // Send email.            
            if ($project) {
                $uniqueUserId = array_diff( $uniqueUserId, [$project->client_id] ); // Unset client
            }

            $this->_sendMailEveryone(
                $uniqueUserId,
                $user->firstname.' '.$user->lastname,
                $id,
                $input['incident_name'],
                'edit',
                $input['incident_type']
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for delete incidents]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $incident = Incident::findOrFail($id);
        if ($incident) {
            $incident->users()->detach();
            if ($incident->delete()) {
                // --
                // Create user activity.
                createUserActivity(
                    Incident::MODULE_NAME,
                    $incident->id,
                    $request->method(),
                    $incident->incident_name,
                    $request->ip()
                );
                return true;
            }
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update incidents notes]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function incidentNotesUpdate($request, $id)
    {
        $input = $request->all();
        $incident = Incident::findOrFail($id);

        if ($incident->fill($input)->save()) {
            // --
            // Add activities
            createUserActivity(
                Incident::MODULE_NAME,
                $incident->id,
                $request->method(),
                $incident->incident_name,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Incident send mail.
     *
     * @param Array  $uniqueUserId  [User array]
     * @param String $loginUserName [Login user name]
     * @param Int    $incidentId    [Incident id]
     * @param String $incidentName  [Incident name]
     * @param String $methods       [Request method]
     * @param String $incidentType  [Incident type]
     *
     * @return Boolean
     */
    private function _sendMailEveryone(
        $uniqueUserId,
        $loginUserName,
        $incidentId,
        $incidentName,
        $methods,
        $incidentType
    ) {
        $url = config('app.front_url').'#/incidents/detail/'.$incidentId;

        if ($incidentType == 1) {
            $subSubject = 'Service Request';
        } else {
            $subSubject = 'Info Request';
        }

        if ($methods == 'create') {
            $subjects = $subSubject.' Create - '.$incidentName;
        } else {
            $subjects = $subSubject.' Edit - '.$incidentName;
        }
        
        $userData = User::select('email')
            ->whereIn('id', $uniqueUserId)
            ->where('is_active', 1)
            ->get();

        if (!$userData->isEmpty()) {
            // --
            // Send email
            $this->emailsHelper->sendIncidentAssignMails(
                $userData,
                $loginUserName,
                $subjects,
                $url,
                $incidentName
            );
            return true;
        }
        return false;
    }

    /**
     * Changed Incident status.
     *
     * @param Request $request [Request for change status]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function changeStatus($request, $id)
    {
        $statusId = $request->get('status');
        $incident = Incident::findOrFail($id);
        $user = Auth::user();
        if ($incident) {
            $input['status'] = $statusId;
            $incident->fill($input)->save();
            // --
            // Create history.
            $history = $this->_createHistory(
                $incident->incident_name,
                $user->id,
                'status',
                $incident->create_user_id,
                $input['status']
            );
            $incident->history()->save($history);
            // --
            // Create user activity.
            createUserActivity(
                Incident::MODULE_NAME,
                $incident->id,
                $request->method(),
                $incident->incident_name,
                $request->ip(),
                true
            );
            return true;
        }
        return false;
    }

    /**
     * Changed incident severity.
     *
     * @param Request $request [Request for change incident severity]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function changeIncidentSeverity($request, $id)
    {
        $input['priority'] = $request->get('priority');
        $incident = Incident::findOrFail($id);
        $user = Auth::user();
        if ($incident) {
            $incident->fill($input)->save();
            // --
            // Create history.
            $history = $this->_createHistory(
                $incident->incident_name,
                $user->id,
                'priority',
                $incident->create_user_id,
                $input['priority']
            );
            $incident->history()->save($history);

            // --
            // Create user activity.
            createUserActivity(
                Incident::MODULE_NAME,
                $incident->id,
                $request->method(),
                $incident->incident_name,
                $request->ip(),
                true
            );
            return true;
        }
        return false;
    }

    /**
     * Store incident history.
     *
     * @param String $incidentName [Incident name]
     * @param Int    $loginUserId  [Login user id]
     * @param String $method       [Request method]
     * @param Int    $createUserId [Created user id]
     * @param String $statusType   [Incident status type]
     *
     * @return Object
     */
    private function _createHistory(
        $incidentName,
        $loginUserId,
        $method,
        $createUserId,
        $statusType = 0
    ) {
        $incidentHistory = new IncidentHistory;
        $incidentHistory->created_by_id = $createUserId;
        if ($method == 'create') {
            $incidentHistory->description = "Incident information added : "
            . '<b>'.$incidentName.'</b>';
        } elseif ($method == 'edit') {
            $incidentHistory->description = "Incident information updated : "
            . '<b>'.$incidentName.'</b>';
            $incidentHistory->updated_by_id = $loginUserId;
        } elseif ($method == 'status') {
            $incidentHistory->description = "Incident status Changed : "
            . '<b>'.$incidentName.'</b>';
            $incidentHistory->updated_by_id = $loginUserId;
        } else {
            $incidentHistory->description = "Incident information deleted : "
            . '<b>'.$incidentName.'</b>';
            $incidentHistory->updated_by_id = $loginUserId;
        }

        if ($statusType == 7) {
            $incidentHistory->closed_by_id = $loginUserId;
        }
        if ($statusType == 4) {
            $incidentHistory->solved_by_id = $loginUserId;
        }
        return $incidentHistory;
    }

    /**
     * Retrive last insert id
     *
     * @return id
     */
    public function getLastId()
    {
        $incident = Incident::withTrashed()
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->first();

        if ($incident) {
            return $incident->id;
        }
        return 0;
    }

    /**
     * Get incident for report.
     *
     * @param Request $request [Request for get incident report]
     *
     * @return Response
     */
    public function getIncidentForReport($request)
    {
        $incident_table = config('core.acl.incidents_table');
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $client_detail_table = config('core.acl.clients_table');
        $team_table = config('core.acl.teams');
        $user = Auth::user();

        $columns = array(
            0 => $incident_table.'.incident_name',
            1 => $incident_table.'.generated_id',
            2 => $incident_table.'.incident_type',
            3 => $incident_table.'.priority',
            4 => $project_table.'.project_name',
            5 =>  DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname)"),
            6 => $client_detail_table.'.company_name',
            7 => DB::raw("CONCAT(incident_created.firstname,' ',incident_created.lastname)"),
            8 => $team_table.'.team_name',
            9 =>  DB::raw("CONCAT(incident_assigned.firstname,' ',incident_assigned.lastname)"),
            10 => $incident_table.'.status'
        );

        $input = $request->input();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $columns_search = $request->input('columns');
        
        $incident = $user->incidents()->leftjoin($project_table, $project_table.'.id', '=', $incident_table.'.project_id')
            ->leftjoin($user_table, $user_table.'.id', '=', $project_table.'.client_id')
            ->leftjoin($client_detail_table, $client_detail_table.'.user_id', '=', $user_table.'.id')
            ->join($user_table.' as incident_created', 'incident_created.id', '=', $incident_table.'.create_user_id')
            ->leftjoin($user_table.' as incident_assigned', 'incident_assigned.id', '=', $incident_table.'.assign_to')
            ->join($team_table, $team_table.'.id', '=', $incident_table.'.assigned_group_id')
            ->select(
                $incident_table.'.*',
                $project_table.'.project_name',
                $user_table.'.firstname as client_firstname',
                $user_table.'.lastname as client_lastname',
                $client_detail_table.'.company_name as client_company_name',
                'incident_created.id as created_id',
                'incident_created.firstname as created_firstname',
                'incident_created.lastname as created_lastname',
                'incident_assigned.id as assigned_id',
                'incident_assigned.firstname as assigned_firstname',
                'incident_assigned.lastname as assigned_lastname',
                DB::raw("CONCAT(incident_assigned.firstname,' ',incident_assigned.lastname) as assign_name"),
                $team_table.'.team_name'
            );
            
        $matchThese = [];
        foreach ((array)$columns_search as $key => $value) {
            if (!empty($value['search']['value'])) {
                array_push(
                    $matchThese,
                    [$columns[$key],'LIKE',"%{$value['search']['value']}%"]
                );
            }
        }

        $totalData = $incident->count();
        $totalFiltered = $totalData;

        if (!empty($matchThese)) {
            $incident = $incident->where($matchThese);

            $totalFiltered = $incident->count();
        }

        $data = $incident->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        return array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
    }

    /**
     * Get incidents for dashboard list.
     *
     * @param Request $request [Request for get incidents]
     *
     * @return Response
     */
    public function getIncidentForDashboard($request)
    {
        $user = Auth::user();
        $incidents = Incident::with(
            ['assignUser' => function ($query) {
                $query->select('id', 'firstname', 'lastname');
            }]
        )
        ->where(function ($query) use ($user){
            $query->where('assign_to', $user->id)
                ->orWhere('create_user_id', $user->id);
        })
        ->whereNotIn('status', [4,7])
        ->orderBy('created_at', 'DESC');

        if ($request->has('length')) {
            $incidents = $incidents->take($request->get('length'));
        }
        
        return $incidents->get();
    }

    /**
     * Check user permission.
     *
     * @param Int $incident_id [Incident Id]
     *
     * @return Boolean
     */
    public function checkPermission($incident_id)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->is_super_admin) {
            return true;
        }

        $incidentUser = Incident::where('id', $incident_id)
            ->where(
                function ($q) {
                    $q->where('assign_to', Auth::user()->id)
                        ->orWhere('create_user_id', Auth::user()->id);
                }
            )
            ->first();

        if ($incidentUser) {
            return true;
        }
        return false;
    }
}

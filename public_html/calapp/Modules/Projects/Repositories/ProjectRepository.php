<?php

namespace Modules\Projects\Repositories;

use Auth;
use Carbon\Carbon;
use DB;
use Modules\Helper\Helpers\EmailsHelper;
use Modules\Helper\Repositories\HelperRepository;
use Modules\Projects\Models\Project;
use Modules\Projects\Models\ProjectUser;
use Modules\Task\Models\Task;
use Modules\Team\Models\Team;
use Modules\User\Models\Department\Department;
use Modules\User\Models\User\User;
use Modules\CustomField\Models\CustomField;
use Storage;

/**
 * Class ProjectRepository
 *
 * Projects CRUD functionality
 *
 * Projects create, update, delete and view
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Projects
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class ProjectRepository
{
    protected $helperRepo;
    protected $emailRepo;

    /**
     * Instantiate a new repository instance.
     *
     * @param HelperRepository $helperRepo [Object]
     * @param EmailsHelper     $emailRepo  [Object]
     *
     * @return void
     */
    public function __construct(
        HelperRepository $helperRepo,
        EmailsHelper $emailRepo
    ) {
        $this->helperRepo = $helperRepo;
        $this->emailRepo = $emailRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        return Project::All();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function getProjectsForList()
    {
        return Project::select('id', 'project_name', 'project_version', 'estimated_hours')
            ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Request for get projects]
     *
     * @return Object
     */
    public function getAllProjects($request)
    {
        if ($request->get('isUserProfile') && $request->has('user_id')) {
            $user = User::findOrFail($request->get('user_id'));
            $projects = $user->projects(true);
        } else {
            $user = Auth::user();
            $projects = $user->projects();
        }
        
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $team_table = config('core.acl.teams');

        $columns = array(
            0 => $project_table.'.id',
            1 => $project_table.'.project_name',
            2 => $project_table.'.start_date',
            3 => $project_table.'.progress',
            4 => 'project_created.firstname',
            5 => $project_table.'.id',
            6 => $project_table.'.status'
        );

        $input = $request->input();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $projects = $projects->with(
            ['users' => function ($query) {
                $query->select('id', 'firstname', 'lastname');
            }]
        )
        ->select(
            $project_table.'.*',
            $user_table.'.firstname as client_firstname',
            $user_table.'.lastname as client_lastname',
            'project_created.firstname as created_firstname',
            'project_created.lastname as created_lastname',
            $team_table.'.team_name'
        )
        ->join($user_table, $user_table.'.id', '=', $project_table.'.client_id')
        ->join($user_table.' as project_created', 'project_created.id', '=', $project_table.'.user_id')
        ->join($team_table, $team_table.'.id', '=', $project_table.'.assign_to');

        if (isset($input['statusId']) && $input['statusId']) {
            $projects = $projects->where($project_table.'.status', $input['statusId']);
        }

        $totalData = $projects->count();
        $totalFiltered = $totalData;

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $projects = $projects->where(
                function ($query) use ($search, $project_table, $user_table, $team_table) {
                    $query->where($project_table.'.project_name', 'LIKE', "%{$search}%")
                        ->orWhere($project_table.'.start_date', 'LIKE', "%{$search}%")
                        ->orWhere($project_table.'.end_date', 'LIKE', "%{$search}%")
                        ->orWhere($project_table.'.progress', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('concat(project_created.firstname," ",project_created.lastname)'), 'LIKE', "%{$search}%")
                        // ->orWhere(DB::raw("(DATE_FORMAT(capl_projects.created_at,'%Y-%m-%d'))"), 'LIKE', "%{$search}%")
                        ;
                }
            );

            $totalFiltered = $projects->count();
        }

        $data = $projects->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $statusCount = $this->getAllProjectCount();

        return array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "statusCount"     => $statusCount,
            "data"            => $data
        );
    }

    /**
     * Get all project status wise count.
     *
     * @return json
     */
    public function getAllProjectCount()
    {
        $user = Auth::user();
        $result['all'] = $user->projects()
            ->whereIn('status', [1,2,3,4,5])
            ->count();

        if ($result['all'] > 0) {
            $result['open'] = $this->_getStatusWiseCount(1, $user);
            $result['inProgress'] = $this->_getStatusWiseCount(2, $user);
            $result['onHold'] = $this->_getStatusWiseCount(3, $user);
            $result['cancel'] = $this->_getStatusWiseCount(4, $user);
            $result['completed'] = $this->_getStatusWiseCount(5, $user);
        }
        return $result;
    }

    /**
     * Project status wise counting.
     *
     * @param Int    $status [Status id]
     * @param Object $user   [User object]
     *
     * @return count
     */
    private function _getStatusWiseCount($status, $user)
    {
        return $user->projects()
            ->where('status', $status)
            ->count();
    }

    /**
     * Get project members and version.
     *
     * @return Object
     */
    public function getProjectMembers()
    {
        $user = Auth::user();
        $project_table = config('core.acl.projects_table');
        $projects =  $user->projects()
            ->with(
                [ 'users' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                }]
            )
            ->select('id', 'project_name', 'project_version', 'assign_members', 'estimated_hours', 'start_date', 'end_date')
            ->whereIn($project_table.'.status', [1,2,3])
            ->where($project_table.'.end_date', '>=', Carbon::today()->format('Y-m-d'))
            ->get();
        return $projects;
    }

    /**
     * Display a project of the resource.
     *
     * @param Int $id [Row id]
     *
     * @return Object
     */
    public function findById($id)
    {   
        $user = Auth::user();
        $project = Project::with(
            [
                'clients' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'createdUser' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'sprints',
                'tasks',
                'defects.users' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'incidents.users' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'comments.user' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'attachments',
                'users' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname',
                        'email'
                    );
                }
            ]
        )
        ->where('id', $id)
        ->first();
        
        if ($project) {
            $result = $project;
            $result['current_user_id'] = $user->id;
            $assign_members = explode(",", $project['assign_members']);
            $result['assign_members'] = User::select('id', 'firstname', 'lastname')
                ->whereIn('id', $assign_members)
                // ->where('is_active', '=', 1)
                ->get();

            $result['custom_fields'] = CustomField::getViewFields(1)->get();

            return $result;
        }
        return false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create project]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $project = new Project;
        $input = $request->all();
        $user = Auth::user();
        $input['user_id'] = $user->id;
        $input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
        $input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
        $assignMembers = [];

        if (isset($input['assign_members'])
            && is_array($input['assign_members'])
            && count($input['assign_members']) > 0
        ) {
            foreach ($input['assign_members'] as $value) {
                $assignMembers[] = $value;
            }
            $input['assign_members'] = implode(",", $assignMembers);
        } else {
            $input['assign_members'] = 'Unassign';
        }

        $userIds = [];
        if (!empty($input['users'])) {
            foreach ($input['users'] as $value) {
                $userIds[] = $value;
            }
        }

        $super_admin_ids = User::where('is_super_admin', 1)
            ->pluck('id')
            ->toArray();

        $userIds = array_merge($userIds, $super_admin_ids);
        array_push($userIds, $user->id); // login user
        array_push($userIds, $input['client_id']); // client
        $userIds = array_unique($userIds);

        $assignMembers = array_merge($assignMembers, $super_admin_ids);
        array_push($assignMembers, $user->id);
        $assignMembers = array_unique($assignMembers);

        if (!empty($input['project_logo'])) {
            $input['project_logo'] = $this->_uploadImage($input['project_logo']);
        }
        
        $projects = $project->create($input);
        if ($projects) {
            // --
            // Save custom field
            if (!empty($projects) && isset($input['custom_fields'])) {
                $this->helperRepo->saveCustomField(
                    1, //Form id.
                    $projects['id'],
                    $input['custom_fields']
                );
            }

            // --
            // Add activities
            createUserActivity(
                Project::MODULE_NAME,
                $projects['id'],
                $request->method(),
                $input['project_name'],
                $request->ip()
            );
            
            if ($projects->users()->sync($userIds)) {
                $projects->users()->updateExistingPivot(
                    $assignMembers,
                    ['edit' => true,'delete' => true]
                );
                $userIds = array_diff( $userIds, [$input['client_id']] ); // Unset client
                $userData = User::select('email')
                    ->whereIn('id', $userIds)
                    ->where('is_active', '=', 1)
                    ->get();
                
                if (!empty($userData)) {
                    $this->_sendMailEveryone(
                        $userData,
                        $user->firstname.' '.$user->lastname,
                        $projects['id'],
                        $input['project_name'],
                        'create'
                    );
                }

                // --
                // Send client mail
                $this->emailRepo->sendCreateProjectNotiClientMail(
                    $input['project_name'],
                    $input['client_id'],
                    $projects['id']
                );

                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update project]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function update($request, $id)
    {
        $project = Project::findOrFail($id);
        $assignMembers = [];

        $old_version = $project->project_version;
        $current_version = explode(',', $old_version);
        $current_version = end($current_version);

        $input = $request->all();
        $user = Auth::user();
        $input['user_id'] = $user->id;
        $input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
        $input['end_date'] = date('Y-m-d', strtotime($input['end_date']));

        // --
        //Set all task completed when status of project is completed.
        if ($input['status'] == 5) {
            $this->_updateProjectTaskStatus($id);
        }

        if (!empty($input['project_logo'])
            && $project->project_logo != $input['project_logo']
        ) {
            $input['project_logo'] = $this->_uploadImage($input['project_logo']);
            if ($project->project_logo) {
                $this->_deleteFile($project->project_logo);
            }
        }

        if ($input['project_version'] != $current_version) {
            $input['project_version'] = $old_version.','.$input['project_version'];
        } else {
            $input['project_version'] = $old_version;
        }

        if (is_array($input['assign_members'])
            && count($input['assign_members']) > 0
        ) {
            foreach ($input['assign_members'] as $value) {
                $assignMembers[] = $value['id'];
                // $syncUser[$value['id']] = ['edit' => true];
            }
            $input['assign_members'] = implode(",", $assignMembers);
        } elseif ($input['assign_members'] == null) {
            $input['assign_members'] = 'Unassign';
        }

        $super_admin_ids = User::where('is_super_admin', 1)
            ->pluck('id')
            ->toArray();

        $userIds = [];
        if (!empty($input['users'])) {
            foreach ($input['users'] as $value) {
                $userIds[] = $value['id'];
            }
        }
        $userIds = array_merge($userIds, $super_admin_ids);
        array_push($userIds, $user->id); // login user
        array_push($userIds, $input['client_id']); // client
        $userIds = array_unique($userIds);

        $assignMembers = array_merge($assignMembers, $super_admin_ids);
        array_push($assignMembers, $user->id);
        $assignMembers = array_unique($assignMembers);

        $project->fill($input)->save();
        // --
        // Save custom field
        if (!empty($project) && isset($input['custom_fields'])) {
            $this->helperRepo->saveCustomField(1, $id, $input['custom_fields']);
        }
        // --
        // Add activities
        createUserActivity(
            Project::MODULE_NAME,
            $project->id,
            $request->method(),
            $project->project_name,
            $request->ip()
        );

        foreach ($userIds as $key => $value) {
            if (in_array($value, $assignMembers)) {
                $syncUser[$value] = ['edit' => true, 'delete' => true];
            } else {
                $syncUser[$value] = ['edit' => false, 'delete' => false];
            }
        }
        // $user->roles()->sync([1 => ['edit' => true], 2, 3]);
        if ($project->users()->sync($syncUser)) {
            $userIds = array_diff( $userIds, [$input['client_id']] ); // Unset client
            $userData = User::select('email')
                ->whereIn('id', $userIds)
                ->where('is_active', '=', 1)
                ->get();
            if (!empty($userData)) {
                $this->_sendMailEveryone(
                    $userData,
                    $user->firstname.' '.$user->lastname,
                    $id,
                    $input['project_name'],
                    'edit'
                );
            }
            return true;
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update project notes]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function projectNotesUpdate($request, $id)
    {
        $input = $request->all();
        $project = Project::findOrFail($id);

        if ($project->fill($input)->save()) {
            // --
            // Add activities
            createUserActivity(
                Project::MODULE_NAME,
                $project->id,
                $request->method(),
                $project->project_name,
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
     * @param Request $request [Request for delete project]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $project = Project::findOrFail($id);
        if ($project->project_logo) {
            $this->_deleteFile($project->project_logo);
        }
        if ($project->users()->detach()) {
            if ($project->delete()) {
                // --
                // Add activities
                createUserActivity(
                    Project::MODULE_NAME,
                    $project->id,
                    $request->method(),
                    $project->project_name,
                    $request->ip()
                );
                return true;
            }
        }
        return false;
    }

    /**
     * Change project status.
     *
     * @param Request $request [Request for change project status]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function changeProjectStatus($request, $id)
    {
        $statusId = $request->get('status');
        $project = Project::findOrFail($id);
        if ($project) {
            $input['status'] = $statusId;
            if ($statusId == 5) {
                $this->_updateProjectTaskStatus($id);
            }

            if ($project->fill($input)->save()) {
                if ($statusId == 5) {
                    $this->emailRepo->sendProjectCompletedNotificationClientMail(
                        $project,
                        $project->id
                    );
                }
                // --
                // Add activities
                createUserActivity(
                    Project::MODULE_NAME,
                    $project->id,
                    $request->method(),
                    $project->project_name,
                    $request->ip(),
                    true
                );
            }
            
            return true;
        }
        return false;
    }

    /**
     * Store a newly imported resource in storage.
     *
     * @param Request $request [Request for import project]
     * @param Array   $csvData [Project Data]
     *
     * @return Boolean
     */
    public function createImportProject($request, $csvData)
    {
        $success = 0;
        $unsuccess = 0;
        $result = [];
        $status = [
            1 => 'open',
            2 => 'in progress',
            3 => 'on hold',
            4 => 'cancel',
            5 => 'completed',
        ];
        unset($csvData[0]); //Unset header.
        foreach ($csvData as $key => $value) {
            $value[8] = strtolower(trim($value[8])); // status
            $team = Team::with(
                ['members' => function ($query) {
                    $query->select(
                        config('core.acl.users_table').'.id',
                        config('core.acl.users_table').'.email'
                    );
                }]
            )
            ->where('team_name', trim($value[6]))
            ->first();

            $client = User::whereEmail(trim($value[14]))
                ->where('is_client', 1)
                ->first();

            $login_user = User::select('id', 'firstname', 'lastname')
                ->whereEmail(trim($value[15]))
                ->first();

            $value["start_date"] = date("Y-m-d", strtotime(trim($value[2])));;
            $value["end_date"] = date("Y-m-d", strtotime(trim($value[3])));;

            $validationError[$key] = $this->_validateProject(
                $value,
                $team,
                $client,
                $login_user,
                $status
            );
            if (empty($validationError[$key])) {
                $input['project_name'] = trim($value[0]);
                $input['project_version'] = empty($value[1]) ? '1.0' : trim($value[1]);
                $input['start_date'] = $value['start_date'];
                $input['end_date'] = $value['end_date'];
                $input['progress'] = empty($value[4]) ? 0 : trim($value[4]);
                $input['demo_url'] = trim($value[5]);
                $input['assign_to'] = $team->id;

                $assignMembers = [];
                if (!empty($value[7])) {
                    $assignMembers = User::whereIn('email', explode("-", trim($value[7])))
                        ->pluck('id')
                        ->toArray();
                    $input['assign_members'] = implode(",", $assignMembers);
                } else {
                    $input['assign_members'] = 'Unassign';
                }

                $input['status'] = array_search($value[8], $status);
                
                $billing_type = strtolower(trim($value[9]));
                if ($billing_type == 'fixed rate') {
                    $input['billing_type'] = 1;
                    $input['price_rate'] = trim($value[10]);
                }
                if ($billing_type == 'hourly rate') {
                    $input['billing_type'] = 2;
                    $input['price_rate'] = trim($value[11]);
                }

                $input['estimated_hours'] = empty($value[12]) ? '' : trim($value[12]);
                $input['description'] = trim($value[13]);
                $input['client_id'] = $client->id;
                $input['user_id'] = $login_user->id;

                // --
                // Save project
                $project = new Project;
                $project = $project->create($input);
                if ($project) {
                    // --
                    // Add activities
                    createUserActivity(
                        Project::MODULE_NAME,
                        $project['id'],
                        $request->method(),
                        $input['project_name'],
                        $request->ip()
                    );

                    foreach ($team->members as $member) {
                        $userIds[] = $member->id;
                    }

                    $super_admin_ids = User::where('is_super_admin', 1)
                        ->pluck('id')
                        ->toArray();
                    $userIds = array_merge($userIds, $super_admin_ids);
                    array_push($userIds, $login_user->id); // login user
                    array_push($userIds, $client->id); // client
                    $userIds = array_unique($userIds);

                    if ($project->users()->sync($userIds)) {
                        $project->users()->updateExistingPivot(
                            $assignMembers,
                            ['edit' => true,'delete' => true]
                        );

                        $userIds = array_diff( $userIds, [$client->id] ); // Unset client
                        $userData = User::select('email')
                            ->whereIn('id', $userIds)
                            ->where('is_active', '=', 1)
                            ->get();
                            
                        if (!empty($userData)) {
                            $this->_sendMailEveryone(
                                $userData,
                                $login_user->firstname.' '.$login_user->lastname,
                                $project['id'],
                                $input['project_name'],
                                'create'
                            );
                        }
                        // --
                        // Send client mail
                        $this->emailRepo->sendCreateProjectNotiClientMail(
                            $input['project_name'],
                            $input['client_id'],
                            $project['id']
                        );
                    }
                }

                $result[$key] = 'success';
                $success++;
            } else {
                $result[$key] = $validationError[$key];
                $unsuccess++;
            }
        }

        return array(
            "result"    => $result,
            "success"   => intval($success),
            "unsuccess" => intval($unsuccess),
            "total" => intval($unsuccess) + intval($success)
        );
    }

    /**
     * Validate imported project.
     *
     * @param Array  $data       [Row data]
     * @param Object $team       [Team object]
     * @param Object $client     [Client object]
     * @param Object $login_user [User object]
     * @param Object $status     [Status array]
     *
     * @return Array
     */
    private function _validateProject($data, $team, $client, $login_user, $status)
    {
        $error = array();

        if (empty($data[0])) {
            $error[] = 'Project name is required.';
        } elseif (strlen($data[0]) > config('core.max_length')) {
            $error[] = 'The project name may not be greater than '.config('core.max_length').' characters.';
        } else {
            $project = Project::where('project_name', trim($data[0]))
            ->exists(); // Allowed softdeleted.
            if ($project) {
                $error[] = 'The project name has already been taken.';
            }
        }

        if (empty($data[2])) {
            $error[] = 'Start date is required.';
        } elseif (false === strtotime($data[2])) {
            $error[] = 'Please enter valid start date.';
        }

        if (empty($data[3])) {
            $error[] = 'End date is required.';
        } elseif (false === strtotime($data[3])) {
            $error[] = 'Please enter valid end date.';
        } elseif ($data['end_date'] < $data['start_date']) {
            $error[] = 'The project end date must be a date after or equal to project start date.';
        }
        
        if (empty($data[6])) {
            $error[] = 'Assign group is required.';
        } else {
            if (!$team) {
                $error[] = 'Assign group is not exists';
            } elseif (!empty($data[7])) {
                foreach ($team->members as $key => $value) {
                    $teamMem[] = $value->email;
                }
                $members = explode("-", trim($data[7]));
                foreach ($members as $key => $value) {
                    if (!in_array($value, $teamMem)) {
                        $error[] = $value . ' is not a team member';
                    }
                }
            }
        }

        if (empty($data[8])) {
            $error[]  = 'Status is required.';
        } elseif (!in_array($data[8], $status)) {
            $error[]  = 'Please enter valid status.';
        }

        if(!empty($data[12])){
            if(preg_match('/^[0-9]+\:[0-5][0-9]$/', $data[12]) !== 1){
                $error[]  = 'The estimated hours allow only digits, 2 digits after colon(less than 60) without any special characters.';
            }
        }

        if (empty($data[14])) {
            $error[] = 'Client is required.';
        } elseif (!$client) {
            $error[] = 'Client is not exists.';
        }
        
        if (empty($data[15])) {
            $error[] = 'Created by is required.';
        } elseif (!$login_user) {
            $error[] = 'Created by is not exists';
        }
        
        return $error;
    }

    /**
     * Update project task status.
     *
     * @param Int $id [Row id]
     *
     * @return void
     */
    private function _updateProjectTaskStatus($id)
    {
        $input['progress'] = 100;
        $tasks = Task::where('project_id', '=', $id)
            ->count();
        
        if ($tasks) {
            Task::where('project_id', '=', $id)
            ->update(
                [
                'progress'  =>  100,
                'status' => 6
                ]
            );
        }
    }

    /**
     * Image upload.
     *
     * @param String $imageData [Image base64]
     *
     * @return String
     */
    private function _uploadImage($imageData)
    {
        $image_parts = explode(";base64,", $imageData);
        if (count($image_parts) >= 2) {
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid() . '.'. $image_type_aux[1];
            if (!Storage::disk('project_uploads')->put($filename, $image_base64)) {
                return response()->json('File has not been uploaded', 401);
            }
            return $filename;
        }
        return response()->json('File has not been uploaded', 401);
    }

    /**
     * Delete Image.
     *
     * @param String $fileName [File name]
     *
     * @return Boolean
     */
    private function _deleteFile($fileName)
    {
        $file = public_path() .'/uploads/project/'.$fileName;
        if (file_exists($file)) {
            unlink($file);
        }
        return true;
    }

    /**
     * Project send mail.
     *
     * @param Array  $allUser       [Users array]
     * @param String $loginUserName [Login user name]
     * @param Int    $projectId     [Project id]
     * @param String $projectName   [Project name]
     * @param String $methods       [Request method]
     *
     * @return Boolean
     */
    private function _sendMailEveryone(
        $allUser,
        $loginUserName,
        $projectId,
        $projectName,
        $methods
    ) {
        if ($methods == 'create') {
            $subjects = 'Project Create - '.$projectName;
        } else {
            $subjects = 'Project Edit - '.$projectName;
        }
        
        // --
        // Send project assign user email
        try {
            $this->emailRepo->sendProjectAssignUserEmail(
                $allUser,
                $projectId,
                $subjects,
                $projectName,
                $loginUserName
            );
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return true;
    }

    /**
     *  Get project planner project, sprint, tasks
     *
     * @return Response
     */
    public function getProjectSprintTasks()
    {
        $users = Auth::user();
        $user_table = config('core.acl.users_table');
        return $users->projects()
            ->with(
                [
                    'users' => function ($query) use ($user_table) {
                        $query->select(
                            $user_table.'.id',
                            $user_table.'.firstname',
                            $user_table.'.lastname'
                        );
                    },
                    'sprints.sprintMembers' => function ($query) use ($user_table) {
                        $query->select(
                            $user_table.'.id',
                            $user_table.'.firstname',
                            $user_table.'.lastname'
                        );
                    },
                    'sprints.tasks.taskMembers' => function ($query) use ($user_table) {
                        $query->select(
                            $user_table.'.id',
                            $user_table.'.firstname',
                            $user_table.'.lastname'
                        );
                    }
                ]
            )
            ->get();
    }

    /**
     * Retrieve all project with its task and defect for release planner.
     *
     * @return Array
     */
    public function getProjectTaskDefect()
    {
        $user = Auth::user();
        return $user->projects()->with(
            [
                'tasks.assignUser' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                },
                'defects.assignUser' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                },
                'users' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                }
            ]
        )
        ->get();
    }

    /**
     * Display Project all data.
     *
     * @param Request $request [Request for get project report]
     *
     * @return Array
     */
    public function getProjectForReport($request)
    {
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $user = Auth::user();
        
        $columns = array(
            0 => $project_table.'.project_name',
            1 => $project_table.'.project_version',
            2 => $project_table.'.start_date',
            3 => $project_table.'.end_date',
            4 => $project_table.'.progress',
            5 => DB::raw('concat(project_created.firstname," ",project_created.lastname)'),
            6 => $project_table.'.assign_members',
            7 => $project_table.'.status',
            8 => $project_table.'.billing_type',
            9 => $project_table.'.price_rate',
            10 => $project_table.'.estimated_hours',
            11 => DB::raw("concat($user_table.firstname,' ',$user_table.lastname)"),
        );

        $input = $request->input();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $columns_search = $request->input('columns');

        $projects = $user->projects()->with(
            [
                'users' => function ($query) {
                    $query->select('id', 'firstname', 'lastname');
                }
            ]
        )
        ->select(
            $project_table.'.*',
            'project_created.id as created_id',
            'project_created.firstname as created_firstname',
            'project_created.lastname as created_lastname',
            $user_table.'.firstname as client_firstname',
            $user_table.'.lastname as client_lastname'
        )
        ->join($user_table.' as project_created', 'project_created.id', '=', $project_table.'.user_id')
        ->join($user_table, $user_table.'.id', '=', $project_table.'.client_id');

        $matchThese = [];
        foreach ((array)$columns_search as $key => $value) {
            if (!empty($value['search']['value'])) {
                array_push(
                    $matchThese,
                    [$columns[$key],'LIKE',"%{$value['search']['value']}%"]
                );
            }
        }

        $totalData = $projects->count();
        $totalFiltered = $totalData;

        if (!empty($matchThese)) {
            $projects = $projects->where($matchThese);

            $totalFiltered = $projects->count();
        }

        $data = $projects->offset($start)
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
     * Get projects for dashboard list.
     *
     * @param Request $request [Request for get projects]
     *
     * @return Response
     */
    public function getProjectForDashboard($request)
    {
        $user = Auth::user();
        if($user->is_client){
            $projects = $user->projects();
        }else{
            $projects = $user->projects(true);
        }
        $projects = $projects->whereNotIn('status', [4, 5])
            ->orderBy('created_at', 'DESC');

        if ($request->has('length')) {
            $projects = $projects->take($request->get('length'));
        }
        
        return $projects->get();
    }

    /**
     * Get project task count by task status for dashboard chart.
     *
     * @param Request $request [Request for get projects]
     *
     * @return Response
     */
    public function getProjectTaskCountByStatus($request)
    {
        $user = Auth::user();
        $projects = $user->projects()
            ->where('status', 2)
            ->select('id', 'project_name')
            ->orderBy('end_date', 'DESC');
            
        if ($request->has('length')) {
            $projects = $projects->take($request->get('length'));
        }

        $projects = $projects->get();

        if (!empty($projects)) {
            foreach ($projects as $project) {
                $project->tasks_count = Task::select('status', DB::raw('count(*) as total'))
                    ->where('project_id', $project->id)
                    ->groupBy('status')
                    ->get();
            }
        }

        return $projects;
    }

    /**
     * Check user permission.
     *
     * @param Int    $project_id [Project Id]
     * @param String $action     [Action For]
     *
     * @return Boolean
     */
    public function checkPermission($project_id, $action)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->is_super_admin) {
            return true;
        }

        $projectUser = ProjectUser::where('project_id', $project_id)
            ->where('user_id', Auth::user()->id)
            ->where($action, true)
            ->first();

        if ($projectUser) {
            return true;
        }
        return false;
    }
}

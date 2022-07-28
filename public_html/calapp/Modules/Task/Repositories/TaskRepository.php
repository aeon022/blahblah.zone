<?php
namespace Modules\Task\Repositories;

use Auth;
use Carbon\carbon;
use DB;
use Modules\Helper\Helpers\EmailsHelper;
use Modules\Helper\Repositories\HelperRepository;
use Modules\Projects\Models\Project;
use Modules\Task\Models\Task;
use Modules\Team\Models\Team;
use Modules\User\Models\User\User;
use Modules\CustomField\Models\CustomField;

/**
 * Class TaskRepository
 *
 * Task create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Task
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class TaskRepository
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
     * @param Request $request [Request for get task]
     *
     * @return Array
     */
    public function getAllTask($request)
    {
        $task_table = config('core.acl.task_table');
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $user = Auth::user();

        if ($request->get('isUserProfile') && $request->has('user_id')) {
            $task = Task::where($task_table.'.assign_to', $request->get('user_id'));
            $statusCount = [];
        } elseif($request->has('filter') && $request->get('filter') === "selected") {
            $task = Task::where($task_table.'.assign_to', $user->id);
            $statusCount = $this->_getTaskCount(true);
        } else {
            $task = $user->tasks();
            $statusCount = $this->_getTaskCount();
        }

        $columns = array(
            0 => $task_table.'.generated_id',
            1 => $task_table.'.name',
            2 => $task_table.'.task_end_date',
            3 => $task_table.'.estimated_hours',
            4 => $task_table.'.status',
            5 => $task_table.'.priority',
            6 => 'project_created.firstname',
            7 => $user_table.'.firstname'
        );

        $statusId = $request->get('status');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $task = $task->join($project_table, $project_table.'.id', '=', $task_table.'.project_id')
            ->join($user_table.' as project_created', 'project_created.id', '=', $task_table.'.created_by')
            ->join($user_table, $user_table.'.id', '=', $task_table.'.assign_to')
            ->select(
                $task_table.'.*',
                DB::raw("DATE_FORMAT($task_table.task_end_date, '%Y-%m-%d') as for_task_end_date"),
                $project_table.'.project_name',
                'project_created.firstname as created_firstname',
                'project_created.lastname as created_lastname',
                $user_table.'.firstname as assign_firstname',
                $user_table.'.lastname as assign_lastname',
                DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname) as assign_name")
            );

        // --
        // Status
        // 1=Open,2=InProgress,3=OnHold,4=Waiting,5=Cancel,6=Completed
        if ($statusId != 0) {
            if ($statusId == 2) {
                $task->whereIn($task_table.'.status', [2, 3, 4]);
            }
            if ($statusId == 6) {
                $task->whereIn($task_table.'.status', [5, 6]);
            }
            if ($statusId == 1) {
                $task->whereIn($task_table.'.status', [1]);
            }
        }

        $totalData = $task->count();
        $totalFiltered = $totalData;

        // --
        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $task = $task->where(
                function ($query) use ($search, $task_table, $user_table, $project_table) {
                    $query->where($task_table.'.name', 'LIKE', "%{$search}%")
                        ->orWhere($task_table.'.generated_id', 'LIKE', "%{$search}%")
                        ->orWhere($task_table.'.task_end_date', 'LIKE', "%{$search}%")
                        ->orWhere($task_table.'.estimated_hours', 'LIKE', "%{$search}%")
                        ->orWhere($task_table.'.status', 'LIKE', "%{$search}%")
                        ->orWhere($task_table.'.priority', 'LIKE', "%{$search}%")
                        ->orWhere(
                            DB::raw('concat(project_created.firstname," ",project_created.lastname)'),
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            DB::raw('concat('.$user_table.'.firstname," ",'.$user_table.'.lastname)'),
                            'LIKE',
                            "%{$search}%"
                        );
                }
            );

            $totalFiltered = $task->count();
        }
    
        $data = $task->offset($start)
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
     * Get all status type count.
     *
     * @param Boolean     $isMy      [For all task or user task]
     *
     * @return Array
     */
    private function _getTaskCount($isMy = false)
    {
        $user = Auth::user();
        $result['all'] = $this->_getStatusWiseCount([1,2,3,4,5,6], $user, $isMy);
        if ($result['all'] > 0) {
            $result['open'] = $this->_getStatusWiseCount([1], $user, $isMy);
            $result['in_progress'] = $this->_getStatusWiseCount([2,3,4], $user, $isMy);
            $result['completed'] = $this->_getStatusWiseCount([5,6], $user, $isMy);
        }
        return $result;
    }

    /**
     * Task status wise counting.
     *
     * @param Array  $status [Task status id]
     * @param Object $user   [User object]
     * @param Boolean $isMy  [For all task or user task]
     *
     * @return Count
     */
    private function _getStatusWiseCount($status, $user, $isMy)
    {
        if ($isMy) {
            return Task::where('assign_to', $user->id)
                ->whereIn('status', $status)
                ->count();
        } else {
            return $user->tasks()
                ->whereIn('status', $status)
                ->count();
        }
        
    }

    /**
     * Retrive particular resource
     *
     * @param Int $id [Row id]
     *
     * @return Array
     */
    public function getById($id)
    {
        $task = Task::with(
            [
            'project1.users' => function ($query) {
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
            'users' => function ($query) {
                $query->select(
                    'id',
                    'firstname',
                    'lastname'
                );
            },
            'attachments'
            ]
        )
        ->leftjoin(
            config('core.acl.task_table') . ' as parent',
            'parent.id',
            '=',
            config('core.acl.task_table').'.parent_task_id'
        )
        ->leftjoin(
            config('core.acl.users_table'),
            config('core.acl.task_table').'.created_by',
            '=',
            config('core.acl.users_table').'.id'
        )
        ->select(
            config('core.acl.task_table').'.*',
            config('core.acl.users_table').'.firstname as created_firstname',
            config('core.acl.users_table').'.lastname as created_lastname',
            config('core.acl.users_table').'.avatar',
            'parent.name as parentTaskName'
        )
        ->where(config('core.acl.task_table').'.id', '=', $id)
        ->first();

        if($task){
            $task['custom_fields'] = CustomField::getViewFields(2)->get();
        }
            
        return $task;
    }

    /**
     * Retrieve parent task of particular subtask.
     *
     * @param Int $parent_task_id [Parent task row id]
     *
     * @return Array
     */
    public function getParentTask($parent_task_id)
    {
        return Task::find($parent_task_id);
    }

    /**
     * Store a newly created resource in the storage.
     *
     * @param Request $request [Request for create task]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $project = Project::findOrFail($input['project_id']);

        // --
        // Add subtask hours to parent task hours.
        if (isset($input['parent_task_id']) && $input['parent_task_id']) {
            if (isset($input['estimated_hours'])) {
                $parentTask = Task::findOrFail($input['parent_task_id']);
                $parentTask->estimated_hours = (
                    (double)$parentTask->estimated_hours + (double)$input['estimated_hours']
                );
                $parentTask->save();
            }
        }

        $user = Auth::user();
        $task = new Task;
        $input['created_by'] = $user->id;
        $input['planned_start_date'] = date('Y-m-d', strtotime($input['planned_start_date']));
        $input['planned_end_date'] = date('Y-m-d', strtotime($input['planned_end_date']));
        $input['task_start_date'] = date('Y-m-d H:i:s', strtotime($input['task_start_date']));
        $input['task_end_date'] = date('Y-m-d H:i:s', strtotime($input['task_end_date']));
        $input['order'] = Task::count() + 1;
        if ($input['status'] == 6) {
            $input['progress'] = 100;
        }
        $tasks = $task->create($input);

        if ($tasks) {
            // --
            // Save custom field.
            if (isset($input['custom_fields'])) {
                $this->helperRepo->saveCustomField(
                    2,
                    $tasks['id'],
                    $input['custom_fields']
                );
            }
            // --
            // Add activities
            createUserActivity(
                Task::MODULE_NAME,
                $tasks->id,
                $request->method(),
                $tasks->name,
                $request->ip()
            );

            $super_admin_ids = User::where('is_super_admin', 1)
                ->pluck('id')
                ->toArray();

            $userIds = [];
            if (isset($input['users']) 
                && is_array($input['users']) 
                && $input['users'] > 0
            ) {
                foreach ($input['users'] as $value) {
                    $userIds[] = $value;
                }
            }

            $userIds = array_merge($userIds, $super_admin_ids);
            array_push($userIds, $user->id); // login user
            array_push($userIds, $project->client_id); // client
            $userIds = array_unique($userIds);

            if ($tasks->users()->sync($userIds)) {
                // --
                // Send mail.
                $mailUserId = $super_admin_ids;
                array_push($mailUserId, $input['assign_to']);
                $mailUserId = array_unique($mailUserId);
                $mailUser = User::select('email')
                    ->whereIn('id', $mailUserId)
                    ->where('is_active', 1)
                    ->get();
                if (!empty($mailUser)) {
                    $this->_sendMailEveryone(
                        $mailUser,
                        $user->firstname.' '.$user->lastname,
                        $tasks['id'],
                        $input['name'],
                        'create'
                    );
                }
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Update the specified resource
     *
     * @param Request $request [Request for update task]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function update($request, $id)
    {
        $task = Task::find($id);
        $project = Project::findOrFail($task->project_id);
        $input = $request->all();
        $user = Auth::user();
        $parentTask = [];
        // --
        // update subtask hours to parent task hours.
        if (isset($input['parent_task_id']) && $input['parent_task_id']) {
            if (isset($input['estimated_hours'])) {
                $parentTask = Task::findOrFail($input['parent_task_id']);
                $parentTask->estimated_hours = (((double)$parentTask->estimated_hours - (double)$task->estimated_hours) + (double)$input['estimated_hours']);
                $parentTask->save();
            }
        }
        
        $input['created_by'] = $user->id;
        $input['planned_start_date'] = date('Y-m-d', strtotime($input['planned_start_date']));
        $input['planned_end_date'] = date('Y-m-d', strtotime($input['planned_end_date']));
        $input['task_start_date'] = date('Y-m-d H:i:s', strtotime($input['task_start_date']));
        $input['task_end_date'] = date('Y-m-d H:i:s', strtotime($input['task_end_date']));
        if ($input['status'] == 6) {
            $input['progress'] = 100;
        }

        //  Edit task user activity
        if ($task->fill($input)->save()) {
            if (isset($input['parent_task_id'])
                && $input['parent_task_id'] == 0
                && $input['status'] == 6
            ) {
                $isProject = Project::where('id', $task->project_id)
                    ->where('project_hours', 1)
                    ->count();
                if ($isProject == 1) {
                    $totalTask = Task::where('project_id', $task->project_id)
                        ->count();
                    $completedTask = Task::where('project_id', $task->project_id)
                        ->where('status', 6)
                        ->count();
                    $progressInTask = $totalTask/$completedTask;
                    $finalProgress = ceil(100/$progressInTask);

                    if ($project) {
                        $data['progress'] = $finalProgress;
                        $project->fill($data)->save();
                    }
                }
            }
            // --
            // Save custom field.
            if (isset($input['custom_fields'])) {
                $this->helperRepo->saveCustomField(2, $id, $input['custom_fields']);
            }
            // --
            // Add activities
            createUserActivity(
                Task::MODULE_NAME,
                $id,
                $request->method(),
                $input['name'],
                $request->ip()
            );
            // --
            // Sync user.
            $super_admin_ids = User::where('is_super_admin', 1)
                ->pluck('id')
                ->toArray();

            $userIds = [];
            if (isset($input['users']) 
                && is_array($input['users']) 
                && $input['users'] > 0
            ) {
                foreach ($input['users'] as $value) {
                    $userIds[] = $value['id'];
                }
            }

            $userIds = array_merge($userIds, $super_admin_ids);
            array_push($userIds, $user->id); // login user
            array_push($userIds, $project->client_id); // client
            $userIds = array_unique($userIds);

            if ($task->users()->sync($userIds)) {
                // --
                // Send mail.
                $mailUserId = $super_admin_ids;
                array_push($mailUserId, $input['assign_to']);
                $mailUserId = array_unique($mailUserId);
                $mailUser = User::select('email')
                    ->whereIn('id', $mailUserId)
                    ->where('is_active', 1)
                    ->get();
                if (!empty($mailUser)) {
                    $this->_sendMailEveryone(
                        $mailUser,
                        $user->firstname.' '.$user->lastname,
                        $task['id'],
                        $input['name'],
                        'edit'
                    );
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request [Request for update task notes]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function taskNotesUpdate($request, $id)
    {
        $input = $request->all();
        $task = Task::findOrFail($id);

        if ($task->fill($input)->save()) {
            // --
            // Add activities
            createUserActivity(
                Task::MODULE_NAME,
                $task->id,
                $request->method(),
                $task->name,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the specified resource
     *
     * @param Request $request [Request for delete task]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $task = Task::findOrFail($id);
        // --
        // Delete subtask hours from parent task hours.
        if ($task->parent_task_id != 0) {
            $parentTask = Task::find($task->parent_task_id);
            $parentTask->estimated_hours = (
                ((double)$parentTask->estimated_hours - (double)$task->estimated_hours)
            );
            $parentTask->save();
        }
        $task->users()->detach();
        if ($task) {
            $task->delete();
            // --
            // Add activities
            createUserActivity(
                Task::MODULE_NAME,
                $task->id,
                $request->method(),
                $task->name,
                $request->ip()
            );
        }
        // --
        // Remove subtask if parent task is deleted also detach users.
        $subTask = Task::where('parent_task_id', $id);
        foreach ($subTask->pluck('id') as $id) {
            $task = Task::find($id);
            $task->users()->detach();
        }
        
        if ($subTask->delete()) {
            return true;
        }
        return true;
    }
    
    /**
     * Update status for specified resource.
     *
     * @param Request $request [Request for change task status]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function changeStatus($request, $id)
    {
        $input = $request->all();
        $task = Task::findOrFail($id);
        $task->status = $input['status'];
        if ($input['status'] == 6) {
            $task->progress = 100;
        }
        if ($task->save()) {
            if ($input['status'] == 6) {
                $isProject = Project::where('id', $task->project_id)
                    ->where('project_hours', 1)
                    ->count();
                if ($isProject == 1) {
                    $totalTask = Task::where('project_id', $task->project_id)
                        ->count();
                    $completedTask = Task::where('project_id', $task->project_id)
                        ->where('status', 6)
                        ->count();
                    $progressInTask = $totalTask/$completedTask;
                    $finalProgress = ceil(100/$progressInTask);

                    $project = Project::findOrFail($task->project_id);
                    if ($project) {
                        $data['progress'] = $finalProgress;
                        $project->fill($data)->save();
                        return true;
                    }
                }
            }
            // --
            // Add activities
            createUserActivity(
                Task::MODULE_NAME,
                $task->id,
                $request->method(),
                $task->name,
                $request->ip(),
                true
            );
            return true;
        }
        return false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Request for get task by project]
     *
     * @return Array
     */
    public function getTaskForTaskBoard($request)
    {
        if ($request->has('project_id')) {
            $user = Auth::user();
            $user_table = config('core.acl.users_table');
            $task_table = config('core.acl.task_table');
            return $user->tasks()->with([
                'project1.users' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname'
                    );
                },
                'users' => function ($query) {
                    $query->select(
                        'id',
                        'firstname',
                        'lastname',
                        'avatar'
                    );
                }
            ])
            ->leftjoin(
                $user_table,
                config('core.acl.task_table').'.created_by',
                '=',
                $user_table.'.id'
            )
            ->leftjoin(
                $user_table.' as task_assign',
                $task_table.'.assign_to',
                '=',
                'task_assign.id'
            )
            ->select(
                config('core.acl.task_table').'.*',
                $user_table.'.firstname as created_firstname',
                $user_table.'.lastname as created_lastname',
                'task_assign.firstname as assign_firstname',
                'task_assign.lastname as assign_lastname',
                config('core.acl.task_user_table').'.*'
            )
            ->where($task_table.'.project_id', $request->input('project_id'))
            ->orderBy($task_table.'.status', 'asc')
            ->get();
        }else{
            return [];
        }
    }

    /**
     * Update the specified resource
     *
     * @param Request $request [Request for update task status]
     *
     * @return Boolean
     */
    public function updateStatusList($request)
    {
        $order = 1;
        $input = $request->all();

        if (!empty($input)) {
            foreach ($input as $key => $value) {
                if (isset($value['list'])) {
                    foreach ($value['list'] as $Key1 => $Value1) {
                        $task = Task::findOrFail($Value1);
                        $task->status = $value['status'];
                        $task->order = $order;
                        $task->save();
                        $order++;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Store a newly imported resource in storage.
     *
     * @param Request $request [Request for import task]
     * @param Array   $csvData [Task Data]
     *
     * @return Boolean
     */
    public function createImportTask($request, $csvData)
    {
        $success = 0;
        $unsuccess = 0;
        $skip = 0;
        $result = [];
        $status = [
            1 => 'open' ,
            2 => 'in progress',
            3 => 'on hold',
            4 => 'waiting',
            5 => 'cancel',
            6 => 'completed',
        ];
        $priority = [
            1 => 'urgent',
            2 => 'very high',
            3 => 'high',
            4 => 'medium',
            5 => 'low',
        ];
        unset($csvData[0]); //Unset header.
        foreach ($csvData as $key => $value) {
            $date['plannedStartDate'] = date("Y-m-d", strtotime(trim(@$value[3])));
            $date['plannedEndDate'] = date("Y-m-d", strtotime(trim(@$value[4])));
            $date['taskStartDate'] = date("Y-m-d", strtotime(trim(@$value[5])));
            $date['taskEndDate'] = date("Y-m-d", strtotime(trim(@$value[6])));

            $value[8] = strtolower(trim(@$value[8]));
            $value[11] = strtolower(trim(@$value[11]));

            $project = Project::with(
                ['users' => function ($query) {
                    $query->select('id', 'email');
                }]
            )->where('project_name', trim(@$value[1]))->first();

            $validationError[$key] = $this->_validateTask(
                $value,
                $date,
                $status,
                $priority,
                $project
            );
            if (empty($validationError[$key])) {
                $task = Task::where('name', trim($value[0]))
                    ->where('project_id', $project->id)
                    ->first();

                if (!empty($task)) {
                    $skip++;
                    continue;
                }

                $loggin_user = User::select('id', 'firstname', 'lastname')
                    ->where('email', trim($value[10]))
                    ->first();

                $task = new Task;
                // --
                // Get task generated ID
                $generatedId = $this->getTaskLastId();
                $lastId = $generatedId + 1;
                $numberOfDigits = strlen((string)$lastId);
                if ($numberOfDigits == 1) {
                    $user_generated_id = 'T000'.$lastId;
                } elseif ($numberOfDigits == 2) {
                    $user_generated_id = 'T00'.$lastId;
                } elseif ($numberOfDigits == 3) {
                    $user_generated_id = 'T0'.$lastId;
                } else {
                    $user_generated_id = 'T'.$lastId;
                }
                $task->generated_id = $user_generated_id;

                $task->name = trim($value[0]);
                $task->project_id = $project->id;
                $task->project_version = trim($value[2]);
                $task->planned_start_date = $date['plannedStartDate'];
                $task->planned_end_date = $date['plannedEndDate'];
                $task->task_start_date = $date['taskStartDate'];
                $task->task_end_date = $date['taskEndDate'];
                $task->estimated_hours = empty(trim($value[7])) ? '' : trim($value[7]);
                $task->status = array_search($value[8], $status);
                $task->assign_to = User::where('email', trim($value[9]))
                    ->first()
                    ->id;
                $task->created_by = $loggin_user->id;
                $task->priority = array_search($value[11], $priority);
                $task->progress = empty(trim($value[12])) ? 0 : trim($value[12]);
                $task->description = trim($value[13]);
                $task->order = Task::count() + 1;

                // --
                // Create task
                if ($task->save()) {
                    $team = Team::with(
                        ['members' => function ($query) {
                            $query->select(config('core.acl.users_table').'.id');
                        }]
                    )->findOrFail($project->assign_to);
                    foreach ($team->members as $key2 => $value) {
                        $userIds[] = $value->id;
                    }

                    $super_admin_ids = User::where('is_super_admin', 1)
                        ->pluck('id')
                        ->toArray();
                    $userIds = array_merge($userIds, $super_admin_ids);
                    array_push($userIds, $task->created_by);
                    array_push($userIds, $project->client_id); // client
                    $userIds = array_unique($userIds);

                    if ($task->users()->sync($userIds)) {
                        // --
                        // Send mail.
                        $mailUserId = $super_admin_ids;
                        array_push($mailUserId, $task->assign_to);
                        $mailUserId = array_unique($mailUserId);

                        $mailUser = User::select('email')
                            ->whereIn('id', $mailUserId)
                            ->where('is_active', 1)
                            ->get();
                        if (!empty($mailUser)) {
                            $this->_sendMailEveryone(
                                $mailUser,
                                $loggin_user->firstname.' '.$loggin_user->lastname,
                                $task['id'],
                                $task['name'],
                                'create'
                            );
                        }
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
            "result"          => $result,
            "success"    => intval($success),
            "unsuccess" => intval($unsuccess),
            "skip" => intval($skip),
            "total" => intval($unsuccess) + intval($success) + intval($skip)
        );
    }

    /**
     * Validate imported task.
     *
     * @param Array  $data     [Row data]
     * @param Array  $date     [Date array]
     * @param Array  $status   [Status array]
     * @param Array  $priority [Priority array]
     * @param Object $project  [Project object]
     *
     * @return Array
     */
    private function _validateTask($data, $date, $status, $priority, $project)
    {
        $error = array();

        if (empty($data[0])) {
            $error[]  = 'Task name is required.';
        } elseif (strlen($data[0]) > config('core.max_length')) {
            $error[] = 'The task name may not be greater than '.config('core.max_length').' characters.';
        }

        if (empty($data[1])) {
            $error[]  = 'Project name is required.';
        } else {
            if (!$project) {
                $error[]  = 'Project is not exists.';
            } else {
                // --
                // Unique project task name.
                $task = Task::where('name',trim($data[0]))
                    ->where('project_id',$project->id)
                    ->exists();
                if($task){
                    $error[]  = 'The task name has already been taken.';
                }
                // --
                // Project version
                if (empty($data[2])) {
                    $error[]  = 'Project version is required.';
                } else {
                    $project_version = explode(',', $project->project_version);
                    if (!in_array(trim($data[2]), $project_version)) {
                        $error[]  = 'Please enter valid project version.';
                    }
                }

                // --
                // Assign to
                if (empty($data[9])) {
                    $error[]  = 'Assigned to is required.';
                } elseif (!filter_var(trim($data[9]), FILTER_VALIDATE_EMAIL)) {
                    $error[] = "Assigned to is not a valid email address";
                } else {
                    foreach ($project->users as $key => $value) {
                        $users[] = $value->email;
                    }
                    if (!in_array(trim($data[9]), $users)) {
                        $error[]  = 'Assigned to is not a project team member.';
                    }
                }
            }
        }

        if (empty($data[3])) {
            $error[]  = 'Planned start date is required.';
        } elseif (false === strtotime($data[3])) {
            $error[] = 'Please enter valid planned start date.';
        } elseif ($project) {
            if ($date['plannedStartDate'] < $project->start_date) {
                $error[] = 'The planned start date must be a date after or equal to project start date';
            } elseif ($date['plannedStartDate'] > $project->end_date) {
                $error[] = 'The planned start date must be a date before or equal to project end date.';
            }
        } 

        if (empty($data[4])) {
            $error[]  = 'Planned end date is required.';
        } elseif (false === strtotime($data[4])) {
            $error[] = 'Please enter valid planned end date.';
        } elseif ($date['plannedEndDate'] < $date['plannedStartDate']) {
            $error[] = 'The planned end date must be a date after or equal to planned start date.';
        } elseif ($project && $date['plannedEndDate'] > $project->end_date) {
            $error[] = 'The planned end date must be a date before or equal to project end date.';
        }

        if (empty($data[5])) {
            $error[]  = 'Task start date is required.';
        } elseif (false === strtotime($data[5])) {
            $error[] = 'Please enter valid task start date.';
        } elseif (!($date['taskStartDate'] >= $date['plannedStartDate'] 
            && $date['taskStartDate'] <= $date['plannedEndDate']            )
        ) {
            $error[] = 'The task start date must be in between planned start date and planned end date.';
        }

        if (empty($data[6])) {
            $error[]  = 'Task end date is required.';
        } elseif (false === strtotime($data[6])) {
            $error[] = 'Please enter valid task end date.';
        } elseif (!($date['taskEndDate'] >= $date['taskStartDate'] 
            && $date['taskEndDate'] <= $date['plannedEndDate']            )
        ) {
            $error[] = 'The task end date must be in between task start date and planned end date.';
        }

        if(!empty($data[7])){
            if(preg_match('/^[0-9]+\:[0-5][0-9]$/', $data[7]) !== 1){
                $error[]  = 'The estimated hours allow only digits, 2 digits after colon(less than 60) without any special characters.';
            }
        }

        if (empty($data[8])) {
            $error[]  = 'Status is required.';
        } elseif (!in_array($data[8], $status)) {
            $error[]  = 'Please enter valid status.';
        }

        if (empty($data[10])) {
            $error[]  = 'Created By is required.';
        } elseif (!filter_var(trim($data[10]), FILTER_VALIDATE_EMAIL)) {
            $error[] = "Created by is not a valid email address";
        } else {
            $created_user = User::whereEmail(trim($data[10]))->exists();
            if (!$created_user) {
                $error[]  = 'Please enter valid created by user email.';
            }
        }

        if (empty($data[11])) {
            $error[]  = 'Priority is required.';
        } elseif (!in_array($data[11], $priority)) {
            $error[]  = 'Please enter valid priority.';
        }

        return $error;
    }

    /**
     * Retrive last insert id
     *
     * @return id
     */
    public function getTaskLastId()
    {
        $task =  Task::where('parent_task_id', 0)
            ->withTrashed()
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->first();
            
        if ($task) {
            return $task->id;
        }
        return 0;
    }

    /**
     * Retrive count of subtask by parent task id.
     *
     * @param Int $parent_task_id [Parent task row id]
     *
     * @return count
     */
    public function getSubTaskCountByParent($parent_task_id)
    {
        return Task::where('parent_task_id', $parent_task_id)
            ->withTrashed()
            ->count();
    }

    /**
     * Get tasks for reports
     *
     * @param Request $request [Request for get task]
     *
     * @return Response
     */
    public function getTaskForReport($request)
    {
        $task_table = config('core.acl.task_table');
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $user = Auth::user();

        $columns = array(
            0 => $task_table.'.name',
            1 => $task_table.'.generated_id',
            2 => DB::raw('concat(project_created.firstname," ",project_created.lastname)'),
            3 => $project_table.'.project_name',
            4 => $task_table.'.project_version',
            5 => $task_table.'.planned_start_date',
            6 => $task_table.'.planned_end_date',
            7 => $task_table.'.task_start_date',
            8 => $task_table.'.task_end_date',
            9 => $task_table.'.estimated_hours',
            10 => $task_table.'.progress',
            11 => DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname)"),
            12 => $task_table.'.priority',
            13 => $task_table.'.status'
        );

        $input = $request->input();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $columns_search = $request->input('columns');

        $task = $user->tasks()->select(
            $task_table.'.*',
            DB::raw("DATE_FORMAT($task_table.task_start_date, '%Y-%m-%d') as for_task_start_date"),
            DB::raw("DATE_FORMAT($task_table.task_end_date, '%Y-%m-%d') as for_task_end_date"),
            $project_table.'.project_name',
            'project_created.id as created_id',
            'project_created.firstname as created_firstname',
            'project_created.lastname as created_lastname',
            $user_table.'.id as assign_id',
            $user_table.'.firstname as assign_firstname',
            $user_table.'.lastname as assign_lastname',
            DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname) as assign_name")
        )
            ->join($project_table, $project_table.'.id', '=', $task_table.'.project_id')
            ->join($user_table.' as project_created', 'project_created.id', '=', $task_table.'.created_by')
            ->join($user_table, $user_table.'.id', '=', $task_table.'.assign_to');

        $matchThese = [];
        foreach ((array)$columns_search as $key => $value) {
            if (!empty($value['search']['value'])) {
                array_push(
                    $matchThese,
                    [$columns[$key],'LIKE',"%{$value['search']['value']}%"]
                );
            }
        }

        $totalData = $task->count();
        $totalFiltered = $totalData;

        if (!empty($matchThese)) {
            $task = $task->where($matchThese);

            $totalFiltered = $task->count();
        }

        $data = $task->offset($start)
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
     * Task send mail.
     *
     * @param Array  $AllUser       [User array]
     * @param String $loginUserName [Login user name]
     * @param Int    $taskId        [Task id]
     * @param String $taskName      [Task name]
     * @param String $methods       [Request method]
     *
     * @return Boolean
     */
    private function _sendMailEveryone(
        $AllUser,
        $loginUserName,
        $taskId,
        $taskName,
        $methods
    ) {
        if ($methods == 'create') {
            $subjects = 'Task Create '. $taskName;
        } else {
            $subjects = 'Task Edit '. $taskName;
        }
        try {
            $this->emailRepo->sendTaskAssignUserEmail(
                $AllUser,
                $taskId,
                $subjects,
                $taskName,
                $loginUserName
            );
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return true;
    }

    /**
     * Get tasks for dashboard list.
     *
     * @param Request $request [Request for get tasks]
     *
     * @return Response
     */
    public function getTaskForDashboard($request)
    {
        $user = Auth::user();
        $tasks = Task::with(
            ['assignUser' => function ($query) {
                $query->select('id', 'firstname', 'lastname');
            }]
        )
        ->where(function ($query) use ($user){
            $query->where('assign_to', $user->id)
                ->orWhere('created_by', $user->id);
        })
        ->whereNotIn('status', [5,6])
        ->orderBy('created_at', 'DESC');
            
        if ($request->has('length')) {
            $tasks = $tasks->take($request->get('length'));
        }
        return $tasks->get();
    }

    /**
     * Get tasks count by status for dashboard chart.
     *
     * @param Request $request [Request for get tasks]
     *
     * @return Response
     */
    public function getTaskCountByStatus($request)
    {
        $user = Auth::user();
        $tasks = Task::select('status', DB::raw('count(*) as total'))
            ->where('assign_to', $user->id)
            ->groupBy('status')
            ->get();

        return $tasks;
    }

    /**
     * Check user permission.
     *
     * @param Int $task_id [Task Id]
     *
     * @return Boolean
     */
    public function checkPermission($task_id)
    {
        $user =Auth::user();
        if ($user->hasRole('admin') || $user->is_super_admin) {
            return true;
        }

        $taskUser = Task::where('id', $task_id)
            ->where(
                function ($q) {
                    $q->where('assign_to', Auth::user()->id)
                        ->orWhere('created_by', Auth::user()->id);
                }
            )
            ->first();

        if ($taskUser) {
            return true;
        }
        return false;
    }
}

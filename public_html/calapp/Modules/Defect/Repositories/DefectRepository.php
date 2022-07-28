<?php
namespace Modules\Defect\Repositories;

use Modules\User\Models\User\User;
use Modules\Team\Models\Team;
use Modules\Projects\Models\Project;
use Modules\Defect\Models\Defect;
use Modules\Defect\Models\DefectHistory;
use Modules\CustomField\Models\CustomField;
use Modules\Helper\Repositories\HelperRepository;
use Modules\Helper\Helpers\EmailsHelper;
use Modules\Helper\Helpers\UploadFileHelper;

use Auth;
use DB;

/**
 * Class DefectRepository
 *
 * Defect create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\Defect
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class DefectRepository
{
	protected $helperRepo;
	protected $emailsHelper;
	protected $uploadHelper;

	/**
	 * Instantiate a new repository instance.
	 *
	 * @param HelperRepository $helperRepo   [Object]
	 * @param EmailsHelper     $emailsHelper [Object]
	 * @param EmailsHelper     $uploadHelper [Object]
	 *
	 * @return void
	 */
	public function __construct(
		HelperRepository $helperRepo,
		EmailsHelper $emailsHelper,
		UploadFileHelper $uploadHelper
	) {
		$this->helperRepo = $helperRepo;
		$this->emailsHelper = $emailsHelper;
		$this->uploadHelper = $uploadHelper;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Object
	 */
	public function findAll()
	{
		$user = Auth::user();
		return $user->defects()
			->select(
				'id',
				'defect_name',
				'create_user_id',
				'generated_id',
				'defect_type',
				'severity',
				'status',
				'assign_member',
				'project_id',
				'project_version',
				'created_at'
			)->with(
				['users' => function ($query) {
					$query->select('id', 'firstname', 'lastname');
				}]
			)
			->get();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request [Request for get defect]
	 *
	 * @return Object
	 */
	public function getAllDefects($request)
	{
		$defects_table = config('core.acl.defects_table');
		$project_table = config('core.acl.projects_table');
		$user_table = config('core.acl.users_table');
		$user = Auth::user();

		if ($request->get('isUserProfile') && $request->has('user_id')) {
			$defect = Defect::where($defects_table.'.assign_member', $request->get('user_id'));
			$statusCount = [];
		} elseif($request->has('filter') && $request->get('filter') === "selected") {
            $defect = Defect::where($defects_table.'.assign_member', $user->id);
            $statusCount = $this->_getDefectCount(true);
        } else {
			$defect =  $user->defects();
			$statusCount = $this->_getDefectCount();
		}
		
		$columns = array(
			0 => $defects_table.'.defect_name',
			1 => $defects_table.'.generated_id',
			2 => $defects_table.'.severity',
			3 => $user_table.'.firstname',
			4 => 'defect_assigned.firstname',
			5 => $defects_table.'.status'
		);

		$status = $request->get('status');
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$defect =  $defect->select(
			$defects_table.'.*',
			DB::raw(
				'CASE WHEN defect_type = 1 THEN "Defect" ELSE "Enhancement" 
					END AS defect_types'
			),
			$project_table.'.project_name',
			$project_table.'.project_version',
			$user_table.'.firstname as created_firstname',
			$user_table.'.lastname as created_lastname',
			'defect_assigned.firstname as assigned_firstname',
			'defect_assigned.lastname as assigned_lastname',
			DB::raw(
				"CONCAT(defect_assigned.firstname,' ',defect_assigned.lastname) 
					as assign_name"
			)
		)->with(
			['users' => function ($query) {
				$query->select('id', 'firstname', 'lastname');
			}]
		)
		->join($project_table, $project_table.'.id', '=', $defects_table.'.project_id')
		->join($user_table, $user_table.'.id', '=', $defects_table.'.create_user_id')
		->leftjoin(
			$user_table.' as defect_assigned',
			'defect_assigned.id',
			'=',
			$defects_table.'.assign_member'
		);

		if ($status) {
			$defect = $defect->where($defects_table.'.status', $status);
		}

		$totalData = $defect->count();
		$totalFiltered = $totalData;

		// --
		// Search
		if (!empty($request->input('search.value'))) {
			$search = $request->input('search.value');
			$defect = $defect->where(
				function ($query) use ($search, $defects_table, $project_table, $user_table) {
					$query->where($defects_table.'.defect_name', 'LIKE', "%{$search}%")
						->orWhere($defects_table.'.generated_id', 'LIKE', "%{$search}%")
						->orWhere($defects_table.'.severity', 'LIKE', "%{$search}%")
						->orWhere(
							DB::raw('concat('.$user_table.'.firstname," ",'.$user_table.'.lastname)'),
							'LIKE',
							"%{$search}%"
						)
						->orWhere(
							DB::raw('concat(defect_assigned.firstname," ",defect_assigned.lastname)'),
							'LIKE',
							"%{$search}%"
						)
						->orWhere($defects_table.'.status', 'LIKE', "%{$search}%");
				}
			);

			$totalFiltered = $defect->count();
		}

		$data = $defect->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

		return array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"statusCount"     => $statusCount,
			"data"            => $data
		);
	}

	/**
	 * Get status type count.
	 *
	 ** @param Boolean     $isMy      [For all defect or user defect]
	 *
	 * @return array
	 */
	private function _getDefectCount($isMy = false)
	{
		$user = Auth::user();
		$result['all'] =  $this->_getStatusWiseCount([1,2,3,4,5,6,7], $user, $isMy);
		if ($result['all'] > 0) {
			$result['assigned'] = $this->_getStatusWiseCount([1], $user, $isMy);
			$result['closed'] = $this->_getStatusWiseCount([2], $user, $isMy);
			$result['in_progress'] = $this->_getStatusWiseCount([3], $user, $isMy);
			$result['open'] = $this->_getStatusWiseCount([4], $user, $isMy);
			$result['solved'] = $this->_getStatusWiseCount([5], $user, $isMy);
			$result['re_open'] = $this->_getStatusWiseCount([6], $user, $isMy);
			$result['deferred'] = $this->_getStatusWiseCount([7], $user, $isMy);
		}
		return $result;
	}

	/**
	 * Task status wise counting.
	 *
	 * @param Int    $status [Status id]
	 * @param Object $user   [User object]
	 * @param Boolean $isMy  [For all defect or user defect]
	 *
	 * @return Count
	 */
	private function _getStatusWiseCount($status, $user, $isMy)
	{
		if ($isMy) {
            return Defect::where('assign_member', $user->id)
            	->whereIn('status', $status)
				->count();
        } else {
            return $user->defects()
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
		$defect = Defect::with(
			[
				'project' => function ($query) {
					$query->select('id', 'project_name', 'project_version');
				},
				'createdUser' => function ($query) {
					$query->select('id', 'firstname', 'lastname');
				},
				'assignUser' => function ($query) {
					$query->select('id', 'firstname', 'lastname');
				},
				'comments.user' => function ($query) {
					$query->select('id', 'firstname', 'lastname');
				},
				'attachments',
				'users' => function ($query) {
					$query->select('id', 'firstname', 'lastname');
				}]
		)
			->with('history')
			->where('id', $id)
			->first();
		
		if ($defect) {
			$defect['custom_fields'] = CustomField::getViewFields(3)->get();
			return $defect;
		}
		return false;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request [Request for create defect]
	 *
	 * @return Boolean
	 */
	public function create($request)
	{
		$input = $request->all();
		$defect = new Defect;
		$project = Project::findOrFail($input['project_id']);
		$user = Auth::user();
		$input['create_user_id'] = $user->id;
		$userIds = [];

		$teamData = Team::with(
			['members' => function ($query) {
				$query->select(config('core.acl.users_table').'.id');
			}]
		)->where('id', $input['assigned_group_id'])
		->first();
		foreach ($teamData->members as $value) {
			$userIds[] = $value->id;
		}

		$super_admin_ids = User::where('is_super_admin', 1)
			->pluck('id')
			->toArray();
		$userIds = array_merge($userIds, $super_admin_ids);
		array_push($userIds, $user->id); // login user
		array_push($userIds, $project->client_id); // client
		$uniqueUserId = array_unique($userIds);

		
		if (!isset($input['assign_member']) || $input['assign_member'] == null) {
			$input['assign_member'] = 'Unassign';
		}

		// --
		// Upload attachment
		if (isset($input["file"]) && $input["file"]) {
			$defect->attachment_hash = $this->uploadHelper->uploadAttachment(
				$input,
				'defect'
			);
		}

		$defect->fill($input);
		if ($defect->save()) {
			$defect->users()->sync($uniqueUserId);
			// --
			// Save custom field
			if (isset($input['custom_fields'])) {
				$this->helperRepo->saveCustomField(
					3,
					$defect->id,
					$input['custom_fields']
				);
			}
			// --
			// Add activity
			createUserActivity(
				Defect::MODULE_NAME,
				$defect->id,
				$request->method(),
				$input['defect_name'],
				$request->ip()
			);
			// --
			// Add defect history
			$history = $this->_createHistory(
				$input['defect_name'],
				$user->id,
				'create',
				$user->id,
				$input['status']
			);
			$defect->history()->save($history);
			// --
			// Send email
			$uniqueUserId = array_diff( $uniqueUserId, [$project->client_id] ); // Unset client
			$this->_sendMailEveryone(
				$uniqueUserId,
				$user->firstname.' '.$user->lastname,
				$defect->id,
				$input['defect_name'],
				'create',
				$input['defect_type']
			);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request [Request for update defect]
	 * @param Int     $id      [Row id]
	 *
	 * @return Object
	 */
	public function update($request, $id)
	{
		$input = $request->all();
		$defect = Defect::find($id);
		$project = Project::findOrFail($defect->project_id);
		$user = Auth::user();

		// --
		// Upload attachment
		if (isset($input["file"]) && $input["file"]) {
			//
			// Delete old attachment.
			if ($defect->attachment_hash) {
				$this->_deleteFile($defect->attachment_hash);
			}
			$defect->attachment_hash = $this->uploadHelper->uploadAttachment(
				$input,
				'defect'
			);
		}

		$userIds = [];
		$teamData = Team::with(
			['members' => function ($query) {
				$query->select(config('core.acl.users_table').'.id');
			}]
		)->where('id', $input['assigned_group_id'])
		->first();
		foreach ($teamData->members as $value) {
			$userIds[] = $value->id;
		}

		$super_admin_ids = User::where('is_super_admin', 1)
			->pluck('id')
			->toArray();
		$userIds = array_merge($userIds, $super_admin_ids);
		array_push($userIds, $user->id); // login user
		array_push($userIds, $project->client_id); // client
		$uniqueUserId = array_unique($userIds);
		
		if (!isset($input['assign_member']) ||$input['assign_member'] == null) {
			$input['assign_member'] = 'Unassign';
		}

		if ($defect->fill($input)->save()) {
			$defect->users()->sync($uniqueUserId);
			// --
			// Save custom field
			if (isset($input['custom_fields'])) {
				$this->helperRepo->saveCustomField(3, $id, $input['custom_fields']);
			}
			// --
			// Add activity
			createUserActivity(
				Defect::MODULE_NAME,
				$defect->id,
				$request->method(),
				$input['defect_name'],
				$request->ip()
			);
			// --
			// Add defect history
			$history = $this->_createHistory(
				$input['defect_name'],
				$user->id,
				'edit',
				$defect->create_user_id,
				$defect->status
			);
			$defect->history()->save($history);
			// --
			// Send email
			$uniqueUserId = array_diff( $uniqueUserId, [$project->client_id] ); // Unset client
			$this->_sendMailEveryone(
				$uniqueUserId,
				$user->firstname.' '.$user->lastname,
				$id,
				$input['defect_name'],
				'edit',
				$input['defect_type']
			);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request [Request for update defect notes]
	 * @param Int     $id      [Row id]
	 *
	 * @return Boolean
	 */
	public function defectNotesUpdate($request, $id)
	{
		$input = $request->all();
		$defect = Defect::findOrFail($id);

		if ($defect->fill($input)->save()) {
			// --
			// Add activities
			createUserActivity(
				Defect::MODULE_NAME,
				$defect->id,
				$request->method(),
				$defect->defect_name,
				$request->ip()
			);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Changed defect status.
	 *
	 * @param Request $request [Request for change defect status]
	 * @param Int     $id      [Row id]
	 *
	 * @return Object
	 */
	public function changeDefectStatus($request, $id)
	{
		$input['status'] = $request->get('status');
		$defect = Defect::findOrFail($id);
		$user = Auth::user();
		if ($defect) {
			$defect->fill($input)->save();
			// --
			// Add defect history
			$history = $this->_createHistory(
				$defect->defect_name,
				$user->id,
				'status',
				$defect->create_user_id,
				$input['status']
			);
			$defect->history()->save($history);
			// --
			// Add activity
			createUserActivity(
				Defect::MODULE_NAME,
				$defect->id,
				$request->method(),
				$defect->defect_name,
				$request->ip(),
				true
			);
			// --
			// Send email
			$this->emailsHelper->sendDefectStatusChangeMails(
				$user,
				$defect
			);
			return true;
		}
		return false;
	}

	/**
	 * Changed defect severity.
	 *
	 * @param Request $request [Request for change defect severity]
	 * @param Int     $id      [Row id]
	 *
	 * @return Object
	 */
	public function changeDefectSeverity($request, $id)
	{
		$input['severity'] = $request->get('severity');
		$defect = Defect::findOrFail($id);
		$user = Auth::user();
		if ($defect) {
			$defect->fill($input)->save();
			// --
			// Add defect history
			$history = $this->_createHistory(
				$defect->defect_name,
				$user->id,
				'severity',
				$defect->create_user_id,
				$input['severity']
			);
			$defect->history()->save($history);

			// --
			// Add activity
			createUserActivity(
				Defect::MODULE_NAME,
				$defect->id,
				$request->method(),
				$defect->defect_name,
				$request->ip(),
				true
			);
			return true;
		}
		return false;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request [Request for delete defect]
	 * @param Int     $id      [Row id]
	 *
	 * @return Boolean
	 */
	public function delete($request, $id)
	{
		$defect = Defect::findOrFail($id);
		if ($defect) {
			$defect->users()->detach();
			if ($defect->attachment_hash) {
				$this->_deleteFile($defect->attachment_hash);
			}
			if ($defect->delete()) {
				// --
				// Add activity
				createUserActivity(
					Defect::MODULE_NAME,
					$defect->id,
					$request->method(),
					$defect->defect_name,
					$request->ip()
				);
				return true;
			}
		}
		return false;
	}

	/**
	 * Delete attachment file.
	 *
	 * @param String $fileName [File name]
	 *
	 * @return Boolean
	 */
	private function _deleteFile($fileName)
	{
		$file = public_path() .'/uploads/defect/'.$fileName;
		if (file_exists($file)) {
			unlink($file);
		}
		return true;
	}

	/**
	 * Defect send mail.
	 *
	 * @param Array  $uniqueUserId  [User id]
	 * @param String $loginUserName [Login user name]
	 * @param Int    $defectId      [Defect id]
	 * @param String $defectName    [Defect name]
	 * @param String $methods       [Request method]
	 * @param String $defectType    [Defect type]
	 *
	 * @return Boolean
	 */
	private function _sendMailEveryone(
		$uniqueUserId,
		$loginUserName,
		$defectId,
		$defectName,
		$methods,
		$defectType
	) {
		$subjects = '';
		$subSubject = '';
		if ($defectType == 1) {
			$subSubject = 'Defect';
		} else {
			$subSubject = 'Enhancement';
		}
		if ($methods == 'create') {
			$subjects = $subSubject.' Create - '.$defectName;
		} else {
			$subjects = $subSubject.' Edit - '.$defectName;
		}
		
		$userData = User::select('email')
			->whereIn('id', $uniqueUserId)
			->where('is_active', 1)
			->get();

		if (!empty($userData)) {
			// --
			// Send email
			$this->emailsHelper->sendDefectAssignMails(
				$userData,
				$loginUserName,
				$subjects,
				$defectId,
				$defectName
			);
			return true;
		}
		return false;
	}

	/**
	 * Defect history save.
	 *
	 * @param String $defectName   [Defect name]
	 * @param Int    $loginUserId  [Login user id]
	 * @param String $method       [Request method]
	 * @param Int    $createUserId [Created user id]
	 * @param String $statusType   [Status type]
	 *
	 * @return object
	 */
	private function _createHistory(
		$defectName,
		$loginUserId,
		$method,
		$createUserId,
		$statusType = 0
	) {
		$defectHistory = new DefectHistory;
		$defectHistory->created_by_id = $createUserId;
		if ($method == 'create') {
			$defectHistory->description = "Defect information added : " .
			'<b>'.$defectName.'</b>';
		} elseif ($method == 'edit') {
			$defectHistory->description = "Defect information updated : " .
			'<b>'.$defectName.'</b>';
			$defectHistory->updated_by_id = $loginUserId;
		} elseif ($method == 'status') {
			$defectHistory->description = "Defect status Changed : " .
			'<b>'.$defectName.'</b>';
			$defectHistory->updated_by_id = $loginUserId;
		} else {
			$defectHistory->description = "Defect information deleted : " .
			'<b>'.$defectName.'</b>';
			$defectHistory->updated_by_id = $loginUserId;
		}

		if ($statusType == 2) {
			$defectHistory->closed_by_id = $loginUserId;
		}
		if ($statusType == 5) {
			$defectHistory->solved_by_id = $loginUserId;
		}
		return $defectHistory;
	}

	/**
	 * Retrive last insert id
	 *
	 * @return id
	 */
	public function getLastId()
	{
		$defect = Defect::withTrashed()
			->orderBy('id', 'DESC')
			->limit(1)
			->first();

		if ($defect) {
			return $defect->id;
		}
		return 0;
	}

	/**
	 * Display defect all data.
	 *
	 * @param Request $request [Request for get defect report]
	 *
	 * @return Response
	 */
	public function getDefectForReport($request)
	{
		$defects_table = config('core.acl.defects_table');
		$project_table = config('core.acl.projects_table');
		$user_table = config('core.acl.users_table');
		$team_table = config('core.acl.teams');
		$user = Auth::user();

		$columns = array(
			0 => $defects_table.'.defect_name',
			1 => $defects_table.'.generated_id',
			2 => $defects_table.'.defect_type',
			3 => $defects_table.'.severity',
			4 => $defects_table.'.status',
			5 => $project_table.'.project_name',
			6 => $defects_table.'.project_version',
			7 => DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname)"),
			8 => $team_table.'.team_name',
			9 =>  DB::raw("CONCAT(defect_assigned.firstname,' ',defect_assigned.lastname)")
		);

		$input = $request->input();
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');
		$columns_search = $request->input('columns');

		$defect =  $user->defects()->select(
			$defects_table.'.*',
			DB::raw(
				'CASE WHEN defect_type = 1 THEN "Defect" ELSE "Enhancement" 
					END AS defect_types'
			),
			$project_table.'.project_name',
			$user_table.'.id as created_id',
			$user_table.'.firstname as created_firstname',
			$user_table.'.lastname as created_lastname',
			'defect_assigned.id as assigned_id',
			'defect_assigned.firstname as assigned_firstname',
			'defect_assigned.lastname as assigned_lastname',
			DB::raw("CONCAT(defect_assigned.firstname,' ',defect_assigned.lastname) as assign_name"),
			$team_table.'.team_name'
		)
			->join($project_table, $project_table.'.id', '=', $defects_table.'.project_id')
			->join($user_table, $user_table.'.id', '=', $defects_table.'.create_user_id')
			->leftjoin(
				$user_table.' as defect_assigned',
				'defect_assigned.id',
				'=',
				$defects_table.'.assign_member'
			)
			->join($team_table, $team_table.'.id', '=', $defects_table.'.assigned_group_id');

		$matchThese = [];
		foreach ((array)$columns_search as $key => $value) {
			if (!empty($value['search']['value'])) {
				array_push(
					$matchThese,
					[$columns[$key],'LIKE',"%{$value['search']['value']}%"]
				);
			}
		}

		$totalData = $defect->count();
		$totalFiltered = $totalData;

		if (!empty($matchThese)) {
			$defect = $defect->where($matchThese);

			$totalFiltered = $defect->count();
		}

		$data = $defect->offset($start)
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
	 * Get defects for dashboard list.
	 *
	 * @param Request $request [Request for get defects]
	 *
	 * @return Response
	 */
	public function getDefectForDashboard($request)
	{
		$user = Auth::user();
		$defects = Defect::with(
			['assignUser' => function ($query) {
				$query->select('id', 'firstname', 'lastname');
			}]
		)
		->where(function ($query) use ($user){
			$query->where('assign_member', $user->id)
				->orWhere('create_user_id', $user->id);
		})
		->whereNotIn('status', [2,5])
		->orderBy('created_at', 'DESC');

		if ($request->has('length')) {
			$defects = $defects->take($request->get('length'));
		}
		 
		return $defects->get();
	}

	/**
	 * Check user permission.
	 *
	 * @param Int $defect_id [Defect Id]
	 *
	 * @return Boolean
	 */
	public function checkPermission($defect_id)
	{
		$user =Auth::user();
		if ($user->hasRole('admin') || $user->is_super_admin) {
			return true;
		}

		$defectUser = Defect::where('id', $defect_id)
			->where(
				function ($q) {
					$q->where('assign_member', Auth::user()->id)
						->orWhere('create_user_id', Auth::user()->id);
				}
			)
			->first();

		if ($defectUser) {
			return true;
		}
		return false;
	}
}

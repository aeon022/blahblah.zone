<?php

namespace Modules\Announcement\Repositories;

use Modules\Announcement\Models\Announcement;
use Modules\User\Models\User\User;
use Modules\Helper\Helpers\EmailsHelper;
use Modules\Setting\Models\Setting;

use Auth;

/**
 * Class AnnouncementRepository
 *
 * Announcement create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Announcement
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @author    Another Author <another@example.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class AnnouncementRepository
{
	protected $emailsHelper;

	/**
	 * Instantiate a new repository instance.
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
		return Announcement::with(
			['user' => function ($query) {
				$query->select('id', 'firstname', 'lastname');
			}]
		)->get();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request [Request for get announcement]
	 *
	 * @return Array
	 */
	public function getAllAnnouncements($request)
	{
		$users_table = config('core.acl.users_table');
		$announcements_table = config('core.acl.announcements_table');
		
		$Announcement = Announcement::join(
			$users_table,
			$announcements_table.'.user_id',
			'=',
			$users_table.'.id'
		);

		$totalData = $Announcement->count();

		$columns = array(
			0 =>'id',
			1 =>'title',
			2 =>'firstname',
			3 =>'start_date',
			4=> 'end_date',
			5=> 'status'
		);
		
		$totalFiltered = $totalData;
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if (!empty($request->input('search.value'))) {
			$search = $request->input('search.value');

			$Announcement = $Announcement->where('title', 'LIKE', "%{$search}%")
				->orWhere('firstname', 'LIKE', "%{$search}%")
				->orWhere('start_date', 'LIKE', "%{$search}%")
				->orWhere('end_date', 'LIKE', "%{$search}%");

			$totalFiltered = $Announcement->count();
		}

		$Announcement = $Announcement->select(
			$announcements_table.'.*',
			$users_table.'.firstname',
			$users_table.'.lastname'
		)
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

		$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $Announcement
		);

		return $json_data;
	}

	/**
	 * Display a announcement of the resource.
	 *
	 * @param Int $id [Row id]
	 *
	 * @return Object
	 */
	public function findById($id)
	{
		return Announcement::find($id);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request [Request for create announcement]
	 *
	 * @return Boolean
	 */
	public function create($request)
	{
		$announcement = new Announcement;
		return $this->_createAnnouncement($announcement, $request);
	}

	/**
	 * Update announcement
	 *
	 * @param Request $request [Request for update announcement]
	 * @param Int     $id      [Row id]
	 *
	 * @return Boolean
	 */
	public function update($request, $id)
	{
		$announcement = $this->findById($id);
		if ($announcement) {
			return $this->_updateAnnouncement($id, $request);
		}
		return false;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request [Request for delete announcement]
	 * @param Int     $id      [Row id]
	 *
	 * @return Boolean
	 */
	public function delete($request, $id)
	{
		$Announcement = $this->findById($id);
		$title = $Announcement->title;
		if ($Announcement) {
			$Announcement->delete();
			// --
			// Add activity
			createUserActivity(
				Announcement::MODULE_NAME,
				$id,
				$request->method(),
				$title,
				$request->ip()
			);
			return true;
		}
		return false;
	}

	/**
	 * Create announcement stub.
	 *
	 * @param Object  $announcement [Announcement object]
	 * @param Request $request      [Create announcement]
	 *
	 * @return Boolean
	 */
	private function _createAnnouncement($announcement, $request)
	{
		$input = $request->all();
		$input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
		$input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
		
		$setting = Setting::first();
		$user = Auth::user();
		$announcement->fill($input);
		if ($announcement = $user->announcement()->save($announcement)) {
			if ($input['status']) {
				if (isset($input['all_client']) && $input['all_client']) {
					$userlist = User::select('id', 'email', 'firstname', 'lastname')
						->where('is_active', '=', 1)
						->get();
				} else {
					$userlist = User::select('id', 'email', 'firstname', 'lastname')
						->where('is_active', '=', 1)
						->where('is_client', 0)
						->get();
				}
				
				if (!empty($userlist) && $setting->is_announcement_notification) {
					try {
						$this->emailsHelper->sendAnnouncementEmails(
							$userlist,
							$announcement
						);
					} catch (\Exception $e) {
						return response()->json(['error' => $e->getMessage()], 500);
					}
				}
			}
			//
			// Add activity
			createUserActivity(
				Announcement::MODULE_NAME,
				$announcement->id,
				$request->method(),
				$input['title'],
				$request->ip()
			);
			return true;
		}
		return false;
	}

	/**
	 * Update announcement stub.
	 *
	 * @param Object  $announcement [Announcement object]
	 * @param Request $request      [Update announcement]
	 *
	 * @return Boolean
	 */
	private function _updateAnnouncement($id, $request)
	{
		$setting = Setting::first();
		$input = $request->all();
		$input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
		$input['end_date'] = date('Y-m-d', strtotime($input['end_date']));

		$announcement = Announcement::find($id);
		$announcement->fill($input);
		if ($announcements = $announcement->save()) {
			if ($input['status']) {
				if (isset($input['all_client']) && $input['all_client']) {
					$userlist = User::select('id', 'email', 'firstname', 'lastname')
						->where('is_active', '=', 1)
						->get();
				} else {
					$userlist = User::select('id', 'email', 'firstname', 'lastname')
						->where('is_active', '=', 1)
						->where('is_client', 0)
						->get();
				}
				
				if (!empty($userlist) && $setting->is_announcement_notification) {
					try {
						$this->emailsHelper->sendAnnouncementEmails(
							$userlist,
							$announcement
						);
					} catch (\Exception $e) {
						return response()->json(['error' => $e->getMessage()], 500);
					}
				}
			}
			//
			// Add activity
			createUserActivity(
				Announcement::MODULE_NAME,
				$announcement->id,
				$request->method(),
				$input['title'],
				$request->ip()
			);
			return true;
		}
		return false;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request [Params for list announcement]
	 *
	 * @return object
	 */
	public function getAnnouncementsForDashboard($request)
	{
		$matchThese = [['status','!=', 2]];
		$announcement = Announcement::where($matchThese)
			->orderBy('start_date', 'asc');
		if ($request->has('length')) {
			$announcement = $announcement->take($request->get('length'));
		}
		return $announcement->get();
	}
}

<?php

namespace Modules\Helper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Helper\Repositories\HelperRepository;
use Modules\Setting\Models\Setting;

/**
 * Class HelperController
 *
 * Helper functionas.
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
class HelperController extends Controller
{
	protected $helperRepo;

	/**
	 * Instantiate a new controller instance.
	 *
	 * @param HelperRepository $helper [Object]
	 *
	 * @return void
	 */
	public function __construct(HelperRepository $helper)
	{
		$this->helperRepo = $helper;
	}

	/**
	 * Get countries.
	 *
	 * @return Response
	 */
	public function getCountries()
	{
		return $this->helperRepo->getCountries();
	}

	/**
	 * Get locals.
	 *
	 * @return Response
	 */
	public function getLocales()
	{
		return $this->helperRepo->getLocales();
	}

	/**
	 * Get languages.
	 *
	 * @return Response
	 */
	public function getLanguages()
	{
		return $this->helperRepo->getLanguages();
	}

	/**
	 * Get time zones.
	 *
	 * @return Response
	 */
	public function getTimezones()
	{
		return $this->helperRepo->getTimezones();
	}

	/**
	 * Get days.
	 *
	 * @return Response
	 */
	public function getDays()
	{
		return $this->helperRepo->getDays();
	}

	/**
	 * Get user by email.
	 *
	 * @return Response
	 */
	public function getUserByEmail()
	{
		return $this->helperRepo->getUserByEmail();
	}

	/**
	 * Get currencies.
	 *
	 * @return Response
	 */
	public function getCurrencies()
	{
		return $this->helperRepo->getCurrencies();
	}

	/**
	 * Cronjob for task report, backupdatabase and annoucement overdue.
	 *
	 * @return Response
	 */
	public function cronjob()
	{
		$setting = Setting::first();
		if (!empty($setting)) {
			$isAllowed = false;
			if (isset($setting->active_cronjob)) {
				if (!isset($setting->last_cronjob_run)) {
					$isAllowed = true;
				}

				if (isset($setting->last_cronjob_run)
					&& time() > ($setting->last_cronjob_run + 300)
				) {
					$isAllowed = true;
				}
			}

			if ($isAllowed) {
				// --
				// For daily evening task mail
				$this->helperRepo->SendDailyMailTaskStatus();

				// --
				// Backup database
				if (isset($setting->automatic_backup)
					&& time() > ($setting->last_autobackup + 7 * 24 * 60 * 60)
				) {
					if ($this->helperRepo->backupDatabase()) {
						$setting->last_autobackup = time();
					}
				}

				// --
				// Annoucement overdue mail
				$this->helperRepo->removeOverdueAnnoucement();

				// --
				// Save settings
				$setting->last_cronjob_run = time();
				$setting->save();
			}
			return response()->json('Cronjob has been run successfully.');
		}
		return response()->json('There are no any setting found for cronjob.', 422);
	}
}

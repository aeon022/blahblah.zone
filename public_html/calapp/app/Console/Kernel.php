<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Setting\Models\Setting;
use Modules\Announcement\Models\Announcement;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\WordOfTheDay::class
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// --
		// Send mails in queue
		$schedule->command('queue:work')
			->everyMinute()
			->withoutOverlapping();

		// --
		// Daily send task report mail
		$schedule->command('work:day')
			->daily();

		// --
		// Automatically backup databases
		$schedule->call(function () {
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
					$currnetDate = date("Y-m-d");
					$matchThese = [['end_date','<',$currnetDate],['status','!=','2']];
					$Announcement = Announcement::where($matchThese)->update(['status' => 2]);

					// --
					// Save settings
					$setting->last_cronjob_run = time();
					$setting->save();
				}
			}
		})->hourly();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__.'/Commands');

		require base_path('routes/console.php');
	}
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Task\Models\Task;
use Modules\User\Models\User\User;

class WordOfTheDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a Daily email to users with daily tasks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::with(['roles' => function ($q) {
                $q->where(config('core.acl.roles_table').'.slug', '=', 'admin');
            }])
            ->where('is_active', true)
            ->get(array('id','firstname', 'lastname', 'email'));

        $tasks = Task::with(['project1','assignUser'])
            ->whereDate(\DB::raw("(DATE_FORMAT(updated_at,'%Y-%m-%d'))"), date('Y-m-d'))
            ->get();

        if(!empty($users) && !$tasks->isEmpty()) {
            foreach ($users as $key => $value) {
                if(!$value->roles->isEmpty()){
                    $mailsConfig = [
                        'email'=> $value->email,
                        'name'=> $value->firstname.' '.$value->lastname,
                        'from'=> 'no@replay.com',
                        'subject'=> 'Task Status Report'
                    ];
                    $this->emailsHelper->sendmail('emails.tasks_report_template', ['tasks' => $tasks], $mailsConfig);
                    exit;
                }
            }
        }
    }
}

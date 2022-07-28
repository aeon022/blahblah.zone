<?php

namespace Modules\Helper\Repositories;

use Auth;
use Illuminate\Support\Facades\DB;
use Modules\Defect\Models\Defect;
use Modules\Incident\Models\Incident;
use Modules\KnowledgeBaseArticle\Models\KnowledgeBaseArticle;
use Modules\Projects\Models\Project;
use Modules\Task\Models\Task;
use Modules\Team\Models\Team;
use Modules\UserActivity\Models\UserActivity;
use Modules\User\Models\User\User;

/**
 * Class PmHelperRepository
 *
 * PM Helper functions
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
class PmHelperRepository
{
    /**
     * Get project,task,defect,incident and client total count.
     *
     * @return Response
     */
    public function getCountForDashboard()
    {
        $user = Auth::user();

        if($user->is_client){
            $data['total_projects'] = $user->projects()->whereNotIn('status', [4,5])->count();
        }else{
            $data['total_projects'] = $user->projects(true)->whereNotIn('status', [4,5])->count();
        }

        $data['pending_task'] = Task::where(function ($query) use ($user){
                $query->where('assign_to', $user->id)->orWhere('created_by', $user->id);
            })->whereNotIn('status', [5,6])->count();
        $data['pending_defect'] = Defect::where(function ($query) use ($user){
                $query->where('assign_member', $user->id)->orWhere('create_user_id', $user->id);
            })->whereNotIn('status', [2,5])->count();
        $data['pending_incident'] = Incident::where(function ($query) use ($user){
                $query->where('assign_to', $user->id)->orWhere('create_user_id', $user->id);
            })->whereNotIn('status', [4,7])->count();
        $data['knowledge_base_article'] = KnowledgeBaseArticle::count();
        $data['total_client'] = User::where('is_client', true)->count();
        $data['total_team'] = Team::count();
        $data['total_user'] = User::where('is_client', false)->count();
        
        return $data;
    }

    /**
     * Get user project/task/meeting timecard.
     *
     * @param Int  $user_id [User id]
     * @param Date $date    [Timecard report month]
     *
     * @return Response
     */
    public function getTimeCardReports($user_id, $date)
    {
        $date = date('Y-m', strtotime($date));
        $user = User::findOrFail($user_id);

        // --
        // Projects
        $timecard['projects'] = $user->projects(true)
            ->select(
                'id',
                'project_name',
                'estimated_hours',
                'start_date',
                'end_date',
                'created_at'
            )
            // ->where(DB::raw("(DATE_FORMAT(start_date,'%Y-%m'))"), $date)
            ->where(DB::raw("(DATE_FORMAT(end_date,'%Y-%m'))"), $date)
            ->get();

        // --
        // Tasks
        $timecard['tasks'] = Task::select(
            'id',
            'name',
            'estimated_hours',
            'task_start_date',
            'task_end_date',
            'created_at'
        )
            // ->where(DB::raw("(DATE_FORMAT(task_start_date,'%Y-%m'))"), $date)
            ->where(DB::raw("(DATE_FORMAT(task_end_date,'%Y-%m'))"), $date)
            ->where('assign_to', $user_id)
            ->get();

        // --
        // Metting
        $timecard['meeting'] = $user->meetings()
            // ->where(\DB::raw("(DATE_FORMAT(start_date,'%Y-%m'))"), $date)
            ->where(DB::raw("(DATE_FORMAT(end_date,'%Y-%m'))"), $date)
            ->get();

        return $timecard;
    }

    /**
     * Get user project/task/meeting count.
     *
     * @param Int $user_id [User id]
     *
     * @return Response
     */
    public function getUserWiseCountForUserDetail($user_id)
    {
        $user_task_hours = Task::where('assign_to', $user_id)
            ->selectRaw('sum(TIME_TO_SEC( estimated_hours )) as total')
            ->first()
            ->total;
        $user_task_hours = $user_task_hours / 3600;
        // pr($user_task_hours,1);

        $user = User::findOrFail($user_id);
        $data['user_project_count'] = $user->projects(true)->count();
        $data['user_task_count'] = Task::where('assign_to', $user_id)->count();
        $data['user_defect_count'] = Defect::where('assign_member', $user_id)
            ->count();
        $data['user_incident_count'] = Incident::where('assign_to', $user_id)
            ->count();
        $data['user_activity_count'] = UserActivity::where('user_id', $user_id)
            ->count();
        // $data['user_task_hours'] = Task::where('assign_to', $user_id)
        //     ->sum('estimated_hours');
        $data['user_task_hours'] = $user_task_hours;
        return $data;
    }
}

<?php
namespace Modules\ProjectPlannerSprint\Repositories;

use Modules\Projects\Models\Project;
use Modules\ProjectPlannerSprint\Models\ProjectPlannerSprint;

use Auth;

/**
 * Class ProjectPlannerSprintRepository
 *
 * Project Planner Sprint create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\ProjectPlannerSprint
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class ProjectPlannerSprintRepository
{

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        return ProjectPlannerSprint::with(
            ['sprintMembers' => function ($query) {
                $query->select(
                    config('core.acl.users_table').'.id',
                    'firstname',
                    'lastname'
                );
            }]
        )->get();
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
        return ProjectPlannerSprint::with(
            [
                'tasks',
                'sprintMembers' => function ($query) {
                    $query->select(
                        config('core.acl.users_table').'.id',
                        'firstname',
                        'lastname'
                    );
                }
            ]
        )->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create PP sprint]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $projectPlannerSprint = new ProjectPlannerSprint;
        $input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
        $input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
        $projectPlannerSprint->fill($input);

        if ($projectPlannerSprint->save()) {
            // --
            // Save members
            $projectPlannerSprint->sprintMembers()->sync($input['sprint_members']);
            // --
            // Add activities
            createUserActivity(
                ProjectPlannerSprint::MODULE_NAME,
                $projectPlannerSprint->id,
                $request->method(),
                $projectPlannerSprint->sprint_name,
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
     * @param Request $request [Request for update PP sprint]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function update($request, $id)
    {
        $input = $request->all();
        $projectPlannerSprint = ProjectPlannerSprint::find($id);
        $input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
        $input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
        $projectPlannerSprint->fill($input);

        if ($projectPlannerSprint->save()) {
            if ($input['status'] == 2) {
                $project = Project::find($input['project_id']);
                $data['status'] = 2;
                $project->fill($data)->save();
            }
            // --
            // Save members
            $sprint_members = [];
            if (count($input['sprint_members']) > 0) {
                foreach ($input['sprint_members'] as $value) {
                    $sprint_members[] = $value['id'];
                }
            }
            $projectPlannerSprint->sprintMembers()->sync($sprint_members);
            // --
            // Add activities
            createUserActivity(
                ProjectPlannerSprint::MODULE_NAME,
                $projectPlannerSprint->id,
                $request->method(),
                $projectPlannerSprint->sprint_name,
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
     * @param Request $request [Request for delete PP sprint]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function delete($request, $id)
    {
        $projectPlannerSprint = ProjectPlannerSprint::findOrFail($id);
        
        if ($projectPlannerSprint->delete()) {
            // --
            // Remove members
            $projectPlannerSprint->sprintMembers()->sync([]);
            // --
            // Add activities
            createUserActivity(
                ProjectPlannerSprint::MODULE_NAME,
                $projectPlannerSprint->id,
                $request->method(),
                $projectPlannerSprint->sprint_name,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Change sprint status.
     *
     * @param Request $request [Request for change sprint status]
     * @param Int     $id      [Row id]
     *
     * @return Object
     */
    public function changeStatus($request, $id)
    {
        $status = $request->get('status');
        $projectPlannerSprint = ProjectPlannerSprint::findOrFail($id);
        if ($projectPlannerSprint) {
            $input['status'] = $status;

            if ($projectPlannerSprint->fill($input)->save()) {
                if ($status == 2) {
                    $project = Project::find($projectPlannerSprint->project_id);
                    $project->fill($input)->save();
                }
                // --
                // Add activities
                createUserActivity(
                    ProjectPlannerSprint::MODULE_NAME,
                    $projectPlannerSprint->id,
                    $request->method(),
                    $projectPlannerSprint->sprint_name,
                    $request->ip(),
                    true
                );
            }
            
            return true;
        }
        return false;
    }
}

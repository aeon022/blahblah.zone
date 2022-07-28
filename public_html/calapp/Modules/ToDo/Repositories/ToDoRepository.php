<?php
namespace Modules\ToDo\Repositories;

use Modules\User\Models\User\User;
use Modules\ToDo\Models\ToDo;

use Auth;

/**
 * Class ToDoRepository
 *
 * ToDo create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\ToDo
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @author    Another Author <another@example.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class ToDoRepository
{

    /**
     * Display a listing of the resource.
     *
     * @return Object
     */
    public function findAll()
    {
        $user = Auth::user();
        return $user->toDos()->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request [Request for get todo]
     *
     * @return array
     */
    public function getAllTodos($request)
    {
        $user = Auth::user();

        if($user->hasRole('admin') || $user->is_super_admin) {
            $todo = ToDo::with('assigned');
        } else {
            $todo = $user->todos()->with('assigned:id');
        }

        $totalData = $todo->count();
        $columns = array(
            0 =>'title',
            1 =>'status',
            2 =>'due_date'
        );
        
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $todo = $todo->where(
                function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%")
                        ->orWhere('due_date', 'LIKE', "%{$search}%");
                }
            );

            $totalFiltered = $todo->count();
        }

        $todo = $todo->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $todo
        );

        return $json_data;
    }

    /**
     * Get the specified resource from storage
     *
     * @param Int $id [Row id]
     *
     * @return Object
     */
    public function findById($id)
    {
        $user = Auth::user();
        return $user->toDos()
            ->with('users')
            ->where(config('core.acl.user_todos_table').'.id', '=', $id)
            ->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create todo]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $user = Auth::user();
        $superAdmins = User::select('id', 'email')
            ->where('is_super_admin', 1)
            ->pluck('id')
            ->toArray();

        $todo = new ToDo;
        $todo->fill($input);

        if (!empty($superAdmins)) {
            array_merge($input['assigned'], $superAdmins);
        }

        array_push($input['assigned'], $user->id);
        $input['assigned'] = array_unique($input['assigned']);
        if ($user->toDo()->save($todo)) {
            if ($todo->assigned()->sync($input['assigned'])) {
                // --
                // Add activities
                createUserActivity(
                    Todo::MODULE_NAME,
                    $todo->id,
                    $request->method(),
                    $todo->title,
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
     * @param Request $request [Request for update todo]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function update($request, $id)
    {
        $input = $request->all();
        $user = Auth::user();
        $superAdmins = User::select('id', 'email')
            ->where('is_super_admin', 1)
            ->pluck('id')
            ->toArray();

        $todo = ToDo::findOrFail($id);
        $todo->fill($input);

        if (!isset($input['isList'])) {
            if (!empty($superAdmins)) {
                array_merge($input['assigned'], $superAdmins);
            }

            array_push($input['assigned'], $user->id);
            $input['assigned'] = array_unique($input['assigned']);
        }

        if ($todo->save()) {
            if (!isset($input['isList'])) {
                $todo->assigned()->sync($input['assigned']);
            }

            // --
            // Add activities
            createUserActivity(
                Todo::MODULE_NAME,
                $todo->id,
                $request->method(),
                $todo->title,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for delete todo]
     * @param Int     $id      [Row id]
     *
     * @return Boolean
     */
    public function delete($request, $id)
    {
        $todo = ToDo::findOrFail($id);
        if ($todo->delete()) {
            // --
            // Add activities
            createUserActivity(
                Todo::MODULE_NAME,
                $todo->id,
                $request->method(),
                $todo->title,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Display a listing of the resource for dashboard.
     *
     * @param Request $request [Params for list todos]
     *
     * @return Object
     */
    public function getTodoForDashboard($request)
    {
        $user = Auth::user();

        if($user->hasRole('admin') || $user->is_super_admin) {
            $todos = ToDo::with('assigned')->whereNotIn('status', [3]);
        } else {
            $todos = $user->toDos()
                ->whereNotIn('status', [3])->with('assigned:id');
        }

        if ($request->has('length')) {
            $todos = $todos->take($request->get('length'));
        }
        return $todos->get();
    }
}

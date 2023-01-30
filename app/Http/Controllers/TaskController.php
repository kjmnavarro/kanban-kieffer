<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        $tasks = Task::all()->where('user_id', $user_id)->where('deleted_at', '=', NULL);
        return TaskResource::collection($tasks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_id)
    {

        $task = new Task;
        $task->user_id = $user_id;
        $task->board_id = $request->input('board_id');
        $task->name = $request->input('name');
        $task->description = $request->input('description');

        if ($task->save()) 
        {
            return new TaskResource($task);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($id, $user_id)
    {

        $task = Task::where('user_id', $user_id)->FindOrFail($id);
        return new TaskResource($task);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {

        $task = Task::where('user_id', $user_id)->FindOrFail($request->task_id);
        $task->user_id = $user_id;
        $task->board_id = $request->input('board_id');
        $task->name = $request->input('name');
        $task->description = $request->input('description');

        if ($task->save()) 
        {
            return new TaskResource($task);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $user_id)
    {

        $task = Task::where('user_id', $user_id)->where('id', $id)->update(['status' => 0, 'deleted_at' => date('Y-m-d')]);

        if ($task) 
        {
            return new TaskResource($task);
        }
    }

    public function listcards(Request $request)
    {
        $access_token = $request->input('access_token');
        $date = $request->input('date');
        $status = $request->input('status');

        $fetchAPIKey = $this->getAPIKey();

        if ($access_token == $fetchAPIKey) 
        {
            if (empty($status) || empty($date)) 
            {
               $task = Task::all();
            }
            else
            {
               $task = Task::where('status', $status)->where('created_at', 'like', $date.'%')->get();
            }
        }

        return response()->json($task);
    }

    private function getAPIKey()
    {
        return 'bmvxircjz7q4nk1a0938';
    }
}

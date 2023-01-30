<?php

namespace App\Http\Controllers;

use App\Board;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BoardResource;
use Spatie\DbDumper\Databases\MySql;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        $boards = Board::all()->where('user_id', $user_id)->where('deleted_at', '=', NULL);
        return BoardResource::collection($boards);
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
        $board = new Board;
        $board->user_id = $user_id;
        $board->name = $request->input('name');

        if ($board->save()) 
        {
            return new BoardResource($board);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show($id, $user_id)
    {
        $board = Board::where('user_id', $user_id)->FindOrFail($id);
        return new BoardResource($board);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $board = Board::where('user_id', $user_id)->FindOrFail($request->board_id);
        $board->user_id = $user_id;
        $board->name = $request->input('name');

        if ($board->save()) 
        {
            return new BoardResource($board);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $user_id)
    {
        $board = Board::where('user_id', $user_id)->FindOrFail($id);

        if ($board) 
        {   
            $task = Task::where('board_id', $board->id)->update(['status' => 0, 'deleted_at' => date('Y-m-d')]);
            $board->update(['deleted_at' => date('Y-m-d')]);
            return new BoardResource($board);
        }
    }

    public function dumpboards()
    {
        $databaseInfo = $this->fetchDBInfo();
        $dump = MySql::create()
                ->setDbName($databaseInfo['databaseName'])
                ->setUserName($databaseInfo['userName'])
                ->setPassword($databaseInfo['password'])
                ->excludeTables('boards')
                ->dumpToFile('dump.sql');
    }

    private function fetchDBInfo()
    {
        $databaseInfo = [];

        $databaseInfo['databaseName'] = 'db_kanban';
        $databaseInfo['userName'] = 'root';
        $databaseInfo['password'] = '';

        return $databaseInfo;
    }

}

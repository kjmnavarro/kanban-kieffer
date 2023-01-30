<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Task;
use App\Board;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        User::create([
            'name' => 'Kieffer',
            'email' => 'kip@kip.com',
            'password' => Hash::make('password'),
        ]);

        Board::create([
            'user_id' => 1,
            'name' => 'Column1',
            'created_at' => '2021-01-31 00:00:00',
        ]);

        Board::create([
            'user_id' => 1,
            'name' => 'Column2',
            'created_at' => '2022-01-31 00:00:00',
        ]);

        Board::create([
            'user_id' => 1,
            'name' => 'Column3',
            'created_at' => Carbon::today(),
        ]);

        Task::create([
            'user_id' => 1,
            'board_id' => 1,
            'name' => 'Task1',
            'description' => 'Task1 of Column1',
            'created_at' => '2021-01-31 00:00:00',
        ]);

        Task::create([
            'user_id' => 1,
            'board_id' => 2,
            'name' => 'Task1',
            'description' => 'Task1 of Column2',
            'created_at' => '2022-01-31 00:00:00',
        ]);

        Task::create([
            'user_id' => 1,
            'board_id' => 3,
            'name' => 'Task1',
            'description' => 'Task1 of Column3',
            'created_at' => Carbon::today(),
        ]);

        Task::create([
            'user_id' => 1,
            'board_id' => 3,
            'name' => 'Task2',
            'description' => 'Task2 of Column3',
            'created_at' => Carbon::today(),
        ]);
    }
}

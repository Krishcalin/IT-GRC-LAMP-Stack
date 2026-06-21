<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        if (Task::count() > 0) {
            return;
        }

        $userId = User::orderBy('created_at')->value('id');
        $today = Carbon::today();

        $tasks = json_decode(file_get_contents(database_path('seeders/data/tasks.json')), true);
        $i = 1;
        foreach ($tasks as $t) {
            $offset = $t['due_offset_days'] ?? null;
            unset($t['due_offset_days']);
            Task::create([
                'ref_id' => sprintf('TASK-%03d', $i++),
                'assignee_id' => $userId,
                'created_by_id' => $userId,
                'due_date' => $offset !== null ? $today->copy()->addDays($offset) : null,
            ] + $t);
        }
    }
}
